<?php

namespace x13allegro\Api\Model\Offers\ProductSet;

use x13allegro\Component\Enum;

final class SafetyInformationType extends Enum
{
    const ATTACHMENTS = 'ATTACHMENTS';
    const TEXT = 'TEXT';

    /**
     * @return string[]
     */
    public static function translateValues()
    {
        return [
            self::ATTACHMENTS => 'Dodaj informacje o bezpieczeństwie produktu w postaci załączników',
            self::TEXT => 'Dodaj informacje o bezpieczeństwie produktu w postaci opisu tekstowego'
        ];
    }
}
