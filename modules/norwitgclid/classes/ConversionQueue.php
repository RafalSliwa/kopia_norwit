<?php
/**
 * Conversion Queue - kolejka konwersji do wysłania do Google Ads
 * Obsługuje gclid, wbraid, gbraid
 */

class ConversionQueue
{
    /**
     * Dodaj konwersję do kolejki
     */
    public static function add(
        int $gclidRecordId,
        ?string $gclid,
        ?string $wbraid,
        ?string $gbraid,
        string $conversionAction,
        float $value,
        string $callDateTime,
        int $duration
    ): bool {
        return Db::getInstance()->insert('norwit_conversion_queue', [
            'gclid_record_id' => (int)$gclidRecordId,
            'gclid' => $gclid ? pSQL($gclid) : null,
            'wbraid' => $wbraid ? pSQL($wbraid) : null,
            'gbraid' => $gbraid ? pSQL($gbraid) : null,
            'conversion_action' => pSQL($conversionAction),
            'conversion_value' => (float)$value,
            'call_datetime' => pSQL($callDateTime),
            'call_duration' => (int)$duration,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Pobierz pending konwersje
     */
    public static function getPending(int $limit = 50): array
    {
        return Db::getInstance()->executeS(
            "SELECT * FROM `" . _DB_PREFIX_ . "norwit_conversion_queue`
             WHERE status = 'pending'
             ORDER BY created_at ASC
             LIMIT " . (int)$limit
        ) ?: [];
    }

    /**
     * Oznacz jako wysłane
     */
    public static function markSent(int $id): bool
    {
        return Db::getInstance()->update('norwit_conversion_queue', [
            'status' => 'sent',
            'sent_at' => date('Y-m-d H:i:s'),
        ], "id = " . (int)$id);
    }

    /**
     * Oznacz błąd
     */
    public static function markError(int $id, string $error): bool
    {
        return Db::getInstance()->update('norwit_conversion_queue', [
            'status' => 'error',
            'error_message' => pSQL(substr($error, 0, 500)),
            'retry_count' => 'retry_count + 1',
        ], "id = " . (int)$id);
    }
}
