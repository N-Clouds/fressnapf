<?php

namespace App\Console\Commands;

use App\Models\Offer;
use App\Services\Mirakl\Mirakl;
use DB;
use Illuminate\Console\Command;

class ImportOffers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-offers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private Mirakl $client;
    private string $baseUri;
    private string $token;

    public function __construct(Mirakl $client)
    {
        parent::__construct();

        $this->client  = $client;
        $this->baseUri = config('fressnapf.host');
        $this->token   = config('fressnapf.token');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $_offers = $this->client->offers()->import();


        $h = fopen('php://temp', 'r+');
        fwrite($h, $_offers);
        rewind($h);

        // Header einlesen (beachtet ; als Delimiter und " als Enclosure, mehrzeilige Felder ok)
        $header = fgetcsv($h, 0, ';', '"');
        if (!$header) {
            fclose($h);
            throw new \RuntimeException('CSV-Header fehlt oder ist leer.');
        }

        // Header-Namen vereinheitlichen
        $header = array_map(fn($v) => mb_strtolower(trim((string)$v)), $header);
        $idx = array_flip($header);

        foreach (['shop-sku', 'price', 'quantity'] as $col) {
            if (!array_key_exists($col, $idx)) {
                fclose($h);
                throw new \RuntimeException("Pflichtspalte fehlt: {$col}");
            }
        }

        $now = now();

        // Map nach SKU, damit es nur einen Datensatz je SKU gibt (letzter gewinnt)
        $bySku = [];

        while (($row = fgetcsv($h, 0, ';', '"')) !== false) {
            // Leere Zeilen überspringen
            if ($row === null || $row === [null] || (count($row) === 1 && trim((string)$row[0]) === '')) {
                continue;
            }

            $sku = trim((string)($row[$idx['shop-sku']] ?? ''));
            if ($sku === '') {
                continue; // ohne SKU kein Upsert
            }

            // Preis als Dezimal (Punkt) – falls mal Komma kommt, umwandeln
            $priceStr = trim((string)($row[$idx['price']] ?? ''));
            $priceStr = str_replace(',', '.', $priceStr);
            $price    = $priceStr === '' ? '0.00' : number_format((float)$priceStr, 2, '.', '');

            // Menge als Integer, leere Felder -> 0
            $qtyStr   = trim((string)($row[$idx['quantity']] ?? ''));
            $quantity = is_numeric($qtyStr) ? (int)$qtyStr : 0;

            $bySku[$sku] = [
                'sku'        => $sku,
                'price'      => $price,
                'quantity'   => $quantity,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        fclose($h);

        $rows = array_values($bySku);
        if (!$rows) {
            return 0;
        }

        // Ein einziges Upsert – Konfliktspalte: sku; zu aktualisieren: price, quantity, updated_at
        DB::table('offers')->upsert(
            $rows,
            ['sku'],
            ['price', 'quantity', 'updated_at']
        );

        return self::SUCCESS;

        foreach ($_offers['offers'] as $offer) {

            Offer::updateOrCreate([
                'sku' => $offer['shop_sku'],
            ],[
                'sku' => $offer['shop_sku'],
                'price' => $offer['price'],
                'quantity' => $offer['quantity']
            ]);
        }
    }

    protected function flush(array $rows): void
    {
        // upsert anhand sku; aktualisiert price, quantity, updated_at
        DB::table('offers')->upsert(
            $rows,
            ['sku'],
            ['price', 'quantity', 'updated_at']
        );
    }
}
