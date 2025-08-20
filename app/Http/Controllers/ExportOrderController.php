<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\XmlOrderExporter\XmlOrderExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExportOrderController extends Controller
{
    public function exportOrder(XmlOrderExporter $exporter)
    {
        $orders = Order::with(['lines', 'billingAddress', 'shippingAddress'])
            ->whereNull('exported_at')
            ->orderBy('id')
            ->get();

        if ($orders->isEmpty()) {
            return response('Keine offenen Bestellungen zum Export.', 200);
        }

        // XML generieren
        $xml = $exporter->exportMany($orders);

        // Dateiname & Speichern (z.B. storage/app/exports/…)
        $filename = 'exports/orders_' . now()->format('Ymd_His') . '.xml';
        Storage::disk('local')->put($filename, $xml); // Inhalt ist bereits ISO-8859-1

        // Wenn Speichern ok war, exported_at für genau diese IDs setzen
        $ids = $orders->pluck('id');

        DB::transaction(function () use ($ids) {
            Order::whereIn('id', $ids)->update(['exported_at' => now()]);
        });

        // Optional: Direkt zum Download anbieten
        return response()->download(storage_path('app/private/' . $filename), basename($filename), [
            'Content-Type' => 'application/xml; charset=ISO-8859-1',
        ])->deleteFileAfterSend(false); // Datei behalten? -> false
    }
}
