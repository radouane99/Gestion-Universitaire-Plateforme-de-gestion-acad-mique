<?php

namespace App\Helpers;

use ArPHP\I18N\Arabic;

class ArabicHelper
{
    /**
     * Shape Arabic text for DomPDF rendering.
     *
     * @param string|null $text
     * @return string
     */
    public static function shape(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        try {
            // Instantiate Ar-PHP Arabic Glyphs shaper
            $arabic = new Arabic('Glyphs');
            // Use 500 chars to avoid premature wrapping, false for hindo digits, false to not force RTL
            return $arabic->utf8Glyphs($text, 500, false, false);
        } catch (\Exception $e) {
            return $text;
        }
    }
}
