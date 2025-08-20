<?php

namespace App\Services\Mirakl\Utils;

class Response
{
    /**
     * Prüft, ob die Antwort erfolgreich ist (enthält UUID).
     */
    public static function isSuccess(array $response): bool
    {
        return isset($response['uuid']) && is_string($response['uuid']) && !empty($response['uuid']);
    }

    /**
     * Gibt die UUID zurück (falls vorhanden).
     */
    public static function getUuid(array $response): ?string
    {
        return self::isSuccess($response) ? $response['uuid'] : null;
    }

    /**
     * Prüft, ob es ein Fehler-Response ist.
     */
    public static function hasErrors(array $response): bool
    {
        return isset($response['messages']) && is_array($response['messages']);
    }

    /**
     * Gibt die Fehler-Messages zurück (falls vorhanden).
     */
    public static function getErrors(array $response)
    {
        if (!self::hasErrors($response)) {
            return '';
        }

        return collect($response['messages'])
            ->map(function ($message, $field) {
                return (is_array($message) ? $message[0] : $message);
            })
            ->implode("-");
//        return self::hasErrors($response) ? $response['messages'] : [];
    }
}
