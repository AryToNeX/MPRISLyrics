<?php

namespace AryToNeX\MPRISLyrics;

class Utils{

    const UTF32_BIG_ENDIAN_BOM = 0x00 . 0x00 . 0xFE . 0xFF;
    const UTF32_LITTLE_ENDIAN_BOM = 0xFF . 0xFE . 0x00 . 0x00;
    const UTF16_BIG_ENDIAN_BOM = 0xFE . 0xFF;
    const UTF16_LITTLE_ENDIAN_BOM = 0xFF . 0xFE;
    const UTF8_BOM = 0xEF . 0xBB . 0xBF;

    public static function detect_utf_encoding(string $text): ?string{
        $first2 = substr($text, 0, 2);
        $first3 = substr($text, 0, 3);
        $first4 = substr($text, 0, 4);
   
        if ($first3 == self::UTF8_BOM) return 'UTF-8';
        elseif ($first4 == self::UTF32_BIG_ENDIAN_BOM) return 'UTF-32BE';
        elseif ($first4 == self::UTF32_LITTLE_ENDIAN_BOM) return 'UTF-32LE';
        elseif ($first2 == self::UTF16_BIG_ENDIAN_BOM) return 'UTF-16BE';
        elseif ($first2 == self::UTF16_LITTLE_ENDIAN_BOM) return 'UTF-16LE';
        
        return null;
    }
    
    public static function ellipsis(string $text, int $maxdimens): string{
        $str = substr($text, 0, $maxdimens-1);
        return $str . (strlen($str) < strlen($text) ? "…" : "");
    }

}
