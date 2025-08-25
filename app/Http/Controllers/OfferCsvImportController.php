<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OfferCsvImportController extends Controller
{
    public function store(Request $request)
    {
        // Bearer prüfen
//        $token = $request->bearerToken();
//        abort_unless($token === config('services.csv_import.token'), 401, 'Unauthorized');

        // Datei validieren (ohne mimes – Windows/curl liefert oft octet-stream)
        $validated = $request->validate([
            'file' => ['required','file','max:51200'], // 50MB
        ]);

        // Datei ablegen (optional)
        $filename = now()->format('Ymd_His').'_'.str()->random(6).'.csv';
        $path = $validated['file']->storeAs('imports/offers', $filename, 'local');
        $full = Storage::path($path);

        @set_time_limit(600);

        [$updated, $skipped] = $this->importCsvQuantitiesSimple($full);

        return response()->json(compact('updated','skipped','filename'));
    }

    /**
     * Aller-simpleste Importlogik:
     * - erwartet Header mit mind. 'sku' und 'quantity'
     * - erkennt ; oder , als Trennzeichen
     * - entfernt UTF-8 BOM
     * - pro Zeile: UPDATE offers SET quantity=?, updated_at=? WHERE sku=?
     * - wenn WHERE 0 Zeilen trifft -> skipped++
     */
    protected function importCsvQuantitiesSimple(string $filepath): array
    {
        $h = fopen($filepath, 'r');
        if (!$h) abort(422, 'CSV nicht lesbar');

        // Delimiter erkennen
        $first = fgets($h);
        if ($first === false) { fclose($h); abort(422, 'Leere CSV'); }
        $delimiter = (substr_count($first, ';') > substr_count($first, ',')) ? ';' : ',';
        rewind($h);

        // Header lesen + normalisieren + BOM entfernen
        $header = fgetcsv($h, 0, $delimiter);
        if (!$header) { fclose($h); abort(422, 'Header-Zeile fehlt'); }
        if (isset($header[0])) { $header[0] = ltrim($header[0], "\xEF\xBB\xBF"); } // BOM
        $header = array_map(fn($c) => strtolower(trim($c)), $header);
        $idx = array_flip($header);

        foreach (['sku','quantity'] as $col) {
            if (!array_key_exists($col, $idx)) {
                fclose($h); abort(422, "Pflichtspalte fehlt: {$col}");
            }
        }

        $updated = 0;
        $skipped = 0;
        $now = now()->toDateTimeString();

        while (($row = fgetcsv($h, 0, $delimiter)) !== false) {
            if (!isset($row[$idx['sku']]) || !isset($row[$idx['quantity']])) { $skipped++; continue; }

            $sku = trim((string)$row[$idx['sku']]);
            $qtyRaw = trim((string)$row[$idx['quantity']]);

            // qty validieren
//            $qty = filter_var($qtyRaw, FILTER_VALIDATE_INT);
//            if ($sku === '' || $qty === false || $qty < 0) {
//                $skipped++; continue;
//            }

            // Einzelnes UPDATE – nur bestehende Datensätze werden getroffen
            $count = DB::table('offers')
                ->where('sku', $sku)
                ->update(['quantity' => (int)$qtyRaw, 'updated_at' => $now]);

            if ($count > 0) { $updated++; } else { $skipped++; }
        }

        fclose($h);
        return [$updated, $skipped];
    }
}
