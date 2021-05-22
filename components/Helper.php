<?php

namespace app\components;

class Helper
{
    static function parsePregMatch($str, $pattern, $what = '')
    {
        $index = 0;

        if ('url' === $what) {
            $index = 2;
        } else if ('user_agent' === $what) {
            $index = 1;
        }

        preg_match_all($pattern, $str, $match);

        if ($match[$index][0]) {
            return $match[$index][0];
        } else {
            return false;
        }
    }

    static function parseBrowser($str)
    {
        $browser = '';

        if (strpos($str, 'Trident') !== false && strpos($str, 'like Gecko') !== false) {
            $browser = 'IE11';
        } else if (strpos($str, 'Chrome') !== false && strpos($str, 'OPR') !== false) {
            $browser = 'Opera';
        } else if (strpos($str, 'Firefox') !== false) {
            $browser = 'Firefox';
        } else if (strpos($str, '(KHTML, like Gecko) Chrome') !== false) {
            $browser = 'Chrome';
        } else if (strpos($str, 'Version') !== false && strpos($str, 'Safari') !== false) {
            $browser = 'Safari';
        }

        return $browser;
    }

    static function parseOperation($str)
    {
        $operation = '';

        if (strpos($str, 'Linux') !== false) {
            $operation = 'Linux';
        } else if (strpos($str, 'Windows') !== false) {
            $operation = 'Windows';
        } else if (strpos($str, 'Mac Os') !== false) {
            $operation = 'Mac Os';
        }

        return $operation;
    }

    static function parseArchitecture($str)
    {
        $architecture = '';

        if (strpos($str, 'x64') !== false) {
            $architecture = 'x64';
        } else {
            $architecture = 'x86';
        }

        return $architecture;
    }

    static function parseDateTime($date)
    {
        $patterns = ['/\//', '/(\d{4}):/'];
        $replace = [' ', '$1 '];
        $new_date = preg_replace($patterns, $replace, $date);
        return $new_date;
    }

    static function parseForTimeStamp($date)
    {
        $d_time_parse = date_parse($date);
        $timestamp = mktime($d_time_parse['hour'], $d_time_parse['minute'], $d_time_parse['second'], $d_time_parse['month'], $d_time_parse['day'], $d_time_parse['year']);
        return $timestamp;
    }
}