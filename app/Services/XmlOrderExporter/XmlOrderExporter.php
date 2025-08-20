<?php

namespace App\Services\XmlOrderExporter;

use App\Models\Order;
use Illuminate\Support\Collection;

class XmlOrderExporter
{
    public function exportMany(Collection $orders): string
    {
/*        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="ISO-8859-1"?><tBestellungen/>');*/

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="ISO-8859-1"?><tBestellungen/>');

        foreach ($orders as $order) {
            $this->appendBestellung($xml, $order);
        }

        return $xml->asXML();
    }

    private function appendBestellung(\SimpleXMLElement $root, Order $order): void
    {
        $b = $root->addChild('tBestellung');


        $add = function(\SimpleXMLElement $parent, string $name, $value = null) {
            $text = $value === null ? '' : (string)$value;
            $parent->addChild($name, htmlspecialchars($text, ENT_QUOTES, 'UTF-8'));
        };

//        $add = function(\SimpleXMLElement $parent, string $name, $value = null) {
//            $text = $value === null ? '' : (string)$value;
//            // UTF-8 (DB) -> ISO-8859-1 (Export)
//            $text = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
//            $parent->addChild($name, htmlspecialchars($text, ENT_QUOTES));
//        };

        // Kopf
        $add($b, 'cSprache',     $order->language ?? 'ger');
        $add($b, 'cWaehrung',    $order->currency ?? 'EUR');
        $add($b, 'fGuthaben',    $this->dec($order->credit ?? 0));
        $add($b, 'fGesamtsumme', $this->dec($order->total ?? 0));
        $add($b, 'cBestellNr',   $order->order_number ?? '');
        $add($b, 'cExterneBestellNr', $order->commercial_id ?? '');
        //TODO: Versandart mapping
        $add($b, 'cVersandartName',   $order->shipping_method_name ?? 'GLS Deutschland');
        $add($b, 'cVersandInfo',      $order->shipping_info);
        $add($b, 'dVersandDatum',     optional($order->shipped_at)?->format('Y-m-d'));
        $add($b, 'cTracking',         $order->tracking_code ?? '');
        $add($b, 'dLieferDatum',      optional($order->delivered_at)?->format('Y-m-d'));
        $add($b, 'cKommentar',        $order->comment ?? '');
        $add($b, 'cBemerkung',        $order->remark ?? '');
        $add($b, 'cZahlungsartName',  'Fressnapf');
        $add($b, 'dBezahltDatum',     optional($order->paid_at)?->format('Y-m-d'));
        $add($b, 'fBezahlt',          $this->dec($order->paid_amount ?? 0));

        // Positionen
        foreach ($order->lines as $item) {
            $p = $b->addChild('twarenkorbpos');
            $add($p, 'cName',   $item->product_shop_sku);
            $add($p, 'cArtNr',  $item->product_shop_sku);
            $add($p, 'cBarcode', $item->barcode ?? '');
            $add($p, 'cSeriennummer', $item->serial_number ?? '');
            $add($p, 'cEinheit', $item->unit ?? '');
            $add($p, 'fPreisEinzelNetto', $this->dec($item->price_unit, 2)); // wie in deinem Beispiel
            $add($p, 'fPreis',            $this->dec($item->price_gross ?? 0, 2));
            $add($p, 'fMwSt',             19);
            $add($p, 'fAnzahl',           $this->dec($item->quantity, 2));
            $add($p, 'cPosTyp',           $item->type ?? 'standard');
            $add($p, 'fRabatt',           $this->dec($item->discount ?? 0));
        }

        // Kunde
        if ($order->billingAddress) {
            $k = $b->addChild('tkunde');
            $add($k, 'cKundenNr',   $order->billingAddress->number ?? '');
            $add($k, 'cAnrede',     $order->billingAddress->salutation ?? '');
            $add($k, 'cTitel',      $order->billingAddress->title ?? '');
            $add($k, 'cVorname',    $order->billingAddress->firstname);
            $add($k, 'cNachname',   $order->billingAddress->lastname);
            $add($k, 'cFirma',      $order->billingAddress->company);
            $add($k, 'cStrasse',    $order->billingAddress->street_1);
            $add($k, 'cAdressZusatz',$order->billingAddress->street_2);
            $add($k, 'cPLZ',        $order->billingAddress->zip_code);
            $add($k, 'cOrt',        $order->billingAddress->city);
            $add($k, 'cBundesland', $order->billingAddress->state ?? '');
            $add($k, 'cLand',       'Deutschland');
            $add($k, 'cTel',        $order->billingAddress->phone ?? '');
            $add($k, 'cMobil',      $order->billingAddress->mobile ?? '');
            $add($k, 'cFax',        $order->billingAddress->fax ?? '');
            $add($k, 'cMail',       $order->billingAddress->email ?? '');
            $add($k, 'cUSTID',      $order->billingAddress->vat_id ?? '');
            $add($k, 'cWWW',        $order->billingAddress->website ?? '');
            $add($k, 'fRabatt',     0);
            $add($k, 'cHerkunft',   'Otto');
        }

        // Lieferadresse
        if ($order->shippingAddress) {
            $a = $b->addChild('tlieferadresse');
            $add($a, 'cAnrede',     $order->shippingAddress->salutation ?? '');
            $add($a, 'cVorname',    $order->shippingAddress->firstname);
            $add($a, 'cNachname',   $order->shippingAddress->lastname);
            $add($a, 'cTitel',      $order->shippingAddress->title ?? '');
            $add($a, 'cFirma',      $order->shippingAddress->company);
            $add($a, 'cStrasse',    $order->shippingAddress->street_1);
            $add($a, 'cAdressZusatz',$order->shippingAddress->street_2);
            $add($a, 'cPLZ',        $order->shippingAddress->zip_code);
            $add($a, 'cOrt',        $order->shippingAddress->city);
            $add($a, 'cBundesland', $order->shippingAddress->state ?? '');
            $add($a, 'cLand',       'Deutschland');
            $add($a, 'cTel',        $order->shippingAddress->phone ?? '');
            $add($a, 'cMobil',      $order->shippingAddress->mobile ?? '');
            $add($a, 'cFax',        $order->shippingAddress->fax ?? '');
            $add($a, 'cMail',       $order->shippingAddress->email ?? '');
        }

//        // Zahlungsinfo
//        if ($order->paymentInfo) {
//            $z = $b->addChild('tzahlungsinfo');
//            $add($z, 'cBankName', $order->paymentInfo->bank_name);
//            $add($z, 'cBLZ',      $order->paymentInfo->blz);
//            $add($z, 'cKontoNr',  $order->paymentInfo->account_number);
//            $add($z, 'cKartenNr', $order->paymentInfo->card_number);
//            $add($z, 'dGueltigkeit', optional($order->paymentInfo->valid_until)?->format('Y-m'));
//            $add($z, 'cCVV',      $order->paymentInfo->cvv);
//            $add($z, 'cKartenTyp',$order->paymentInfo->card_type);
//            $add($z, 'cInhaber',  $order->paymentInfo->holder);
//            $add($z, 'cIBAN',     $order->paymentInfo->iban);
//            $add($z, 'cBIC',      $order->paymentInfo->bic);
//        }
    }

    private function dec($number, int $scale = 2): string
    {
        return number_format((float)$number, $scale, '.', '');
    }
}
