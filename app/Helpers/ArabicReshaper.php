<?php

namespace App\Helpers;

/**
 * A lightweight Arabic Reshaper for PHP.
 * Solves character isolation and mirroring issues in DomPDF.
 */
class ArabicReshaper
{
    private static $map = [
        // Char => [Isolated, End, Middle, Beginning]
        0x0621 => [0xFE80, 0xFE80, 0xFE80, 0xFE80], // HAMZA
        0x0622 => [0xFE81, 0xFE82, 0xFE82, 0xFE81], // ALEF WITH MADDA ABOVE
        0x0623 => [0xFE83, 0xFE84, 0xFE84, 0xFE83], // ALEF WITH HAMZA ABOVE
        0x0624 => [0xFE85, 0xFE86, 0xFE86, 0xFE85], // WAW WITH HAMZA ABOVE
        0x0625 => [0xFE87, 0xFE88, 0xFE88, 0xFE87], // ALEF WITH HAMZA BELOW
        0x0626 => [0xFE89, 0xFE8A, 0xFE8B, 0xFE8C], // YEH WITH HAMZA ABOVE
        0x0627 => [0xFE8D, 0xFE8E, 0xFE8E, 0xFE8D], // ALEF
        0x0628 => [0xFE8F, 0xFE90, 0xFE91, 0xFE92], // BEH
        0x0629 => [0xFE93, 0xFE94, 0xFE94, 0xFE93], // TEH MARBUTA
        0x062A => [0xFE95, 0xFE96, 0xFE97, 0xFE98], // TEH
        0x062B => [0xFE99, 0xFE9A, 0xFE9B, 0xFE9C], // THEH
        0x062C => [0xFE9D, 0xFE9E, 0xFE9F, 0xFEA0], // JEEM
        0x062D => [0xFEA1, 0xFEA2, 0xFEA3, 0xFEA4], // HAH
        0x062E => [0xFEA5, 0xFEA6, 0xFEA7, 0xFEA8], // KHAH
        0x062F => [0xFEA9, 0xFEAA, 0xFEAA, 0xFEA9], // DAL
        0x0630 => [0xFEAB, 0xFEAC, 0xFEAC, 0xFEAB], // THAL
        0x0631 => [0xFEAD, 0xFEAE, 0xFEAE, 0xFEAD], // REH
        0x0632 => [0xFEAF, 0xFEB0, 0xFEB0, 0xFEAF], // ZAIN
        0x0633 => [0xFEB1, 0xFEB2, 0xFEB3, 0xFEB4], // SEEN
        0x0634 => [0xFEB5, 0xFEB6, 0xFEB7, 0xFEB8], // SHEEN
        0x0635 => [0xFEB9, 0xFEBA, 0xFEBB, 0xFEBC], // SAD
        0x0636 => [0xFEBD, 0xFEBE, 0xFEBF, 0xFEC0], // DAD
        0x0637 => [0xFEC1, 0xFEC2, 0xFEC3, 0xFEC4], // TAH
        0x0638 => [0xFEC5, 0xFEC6, 0xFEC7, 0xFEC8], // ZAH
        0x0639 => [0xFEC9, 0xFECA, 0xFECB, 0xFECC], // AIN
        0x063A => [0xFECD, 0xFECE, 0xFECF, 0xFED0], // GHAIN
        0x0641 => [0xFED1, 0xFED2, 0xFED3, 0xFED4], // FEH
        0x0642 => [0xFED5, 0xFED6, 0xFED7, 0xFED8], // QAF
        0x0643 => [0xFED9, 0xFEDA, 0xFEDB, 0xFEDC], // KAF
        0x0644 => [0xFEDD, 0xFEDE, 0xFEDF, 0xFEE0], // LAM
        0x0645 => [0xFEE1, 0xFEE2, 0xFEE3, 0xFEE4], // MEEM
        0x0646 => [0xFEE5, 0xFEE6, 0xFEE7, 0xFEE8], // NOON
        0x0647 => [0xFEE9, 0xFEEA, 0xFEEB, 0xFEEC], // HEH
        0x0648 => [0xFEED, 0xFEEE, 0xFEEE, 0xFEED], // WAW
        0x0649 => [0xFEEF, 0xFEF0, 0xFEF0, 0xFEEF], // ALEF MAKSURA
        0x064A => [0xFEF1, 0xFEF2, 0xFEF3, 0xFEF4], // YEH
    ];

    private static $connects_left = [
        0x0628, 0x062A, 0x062B, 0x062C, 0x062D, 0x062E, 0x0633, 0x0634, 0x0635, 0x0636, 0x0637, 0x0638,
        0x0639, 0x063A, 0x0641, 0x0642, 0x0643, 0x0644, 0x0645, 0x0646, 0x0647, 0x064A, 0x0626
    ];

    private static $connects_right = [
        0x0622, 0x0623, 0x0624, 0x0625, 0x0627, 0x062F, 0x0630, 0x0631, 0x0632, 0x0648, 0x0649,
        0x0628, 0x062A, 0x062B, 0x062C, 0x062D, 0x062E, 0x0633, 0x0634, 0x0635, 0x0636, 0x0637, 0x0638,
        0x0639, 0x063A, 0x0641, 0x0642, 0x0643, 0x0644, 0x0645, 0x0646, 0x0647, 0x064A, 0x0626, 0x0629
    ];

    public static function reshape($text)
    {
        $chars = self::utf8_to_codes($text);
        $reshaped = [];
        $count = count($chars);

        for ($i = 0; $i < $count; $i++) {
            $current = $chars[$i];
            if (!isset(self::$map[$current])) {
                $reshaped[] = $current;
                continue;
            }

            $prev = ($i > 0) ? $chars[$i - 1] : null;
            $next = ($i < $count - 1) ? $chars[$i + 1] : null;

            $connect_prev = ($prev && in_array($prev, self::$connects_left));
            $connect_next = ($next && in_array($next, self::$connects_right));

            if ($connect_prev && $connect_next) {
                $reshaped[] = self::$map[$current][2]; // Middle
            } elseif ($connect_prev) {
                $reshaped[] = self::$map[$current][1]; // End
            } elseif ($connect_next) {
                $reshaped[] = self::$map[$current][3]; // Beginning
            } else {
                $reshaped[] = self::$map[$current][0]; // Isolated
            }
        }

        // Lam-Alef Ligatures (Simplified)
        $ligatured = [];
        for ($i = 0; $i < count($reshaped); $i++) {
            if ($i < count($reshaped) - 1 && $chars[$i] == 0x0644) {
                $next_code = $chars[$i+1];
                $la = null;
                if ($next_code == 0x0622) $la = [0xFEF5, 0xFEF6];
                if ($next_code == 0x0623) $la = [0xFEF7, 0xFEF8];
                if ($next_code == 0x0625) $la = [0xFEF9, 0xFEFA];
                if ($next_code == 0x0627) $la = [0xFEFB, 0xFEFC];
                
                if ($la) {
                    $prev = ($i > 0) ? $chars[$i - 1] : null;
                    $connect_prev = ($prev && in_array($prev, self::$connects_left));
                    $ligatured[] = $connect_prev ? $la[1] : $la[0];
                    $i++;
                    continue;
                }
            }
            $ligatured[] = $reshaped[$i];
        }

        return self::codes_to_utf8(array_reverse($ligatured));
    }

    private static function utf8_to_codes($text)
    {
        $codes = [];
        $len = mb_strlen($text, 'UTF-8');
        for ($i = 0; $i < $len; $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');
            $codes[] = self::uniord($char);
        }
        return $codes;
    }

    private static function codes_to_utf8($codes)
    {
        $text = '';
        foreach ($codes as $code) {
            $text .= self::unichr($code);
        }
        return $text;
    }

    private static function uniord($c)
    {
        $ord0 = ord($c[0]);
        if ($ord0 >= 0 && $ord0 <= 127) return $ord0;
        $ord1 = ord($c[1]);
        if ($ord0 >= 192 && $ord0 <= 223) return ($ord0 - 192) * 64 + ($ord1 - 128);
        $ord2 = ord($c[2]);
        if ($ord0 >= 224 && $ord0 <= 239) return ($ord0 - 224) * 4096 + ($ord1 - 128) * 64 + ($ord2 - 128);
        return 0;
    }

    private static function unichr($u)
    {
        if ($u <= 0x7F) return chr($u);
        if ($u <= 0x7FF) return chr(0xC0 | ($u >> 6)) . chr(0x80 | ($u & 0x3F));
        if ($u <= 0xFFFF) return chr(0xE0 | ($u >> 12)) . chr(0x80 | (($u >> 6) & 0x3F)) . chr(0x80 | ($u & 0x3F));
        return '';
    }
}
