<?php

namespace Transvision;

class Strings
{
    /*
     * Replace contiguous spaces in a string by a single space
     *
     * @param $string
     * @return string
     */
    public static function mtrim($string)
    {
        $string = explode(' ', $string);
        $string = array_filter($string);
        $string = implode(' ', $string);

        return $string;
    }

    /*
     * Check if $haystack starts with the $needle string
     *
     * @param $haystack string
     * @param $needle string
     * @return boolean
     */
    public static function startsWith($haystack, $needle)
    {
        return !strncmp($haystack, $needle, mb_strlen($needle));
    }

    /*
     * Check if $haystack ends with the $needle string
     *
     * @param $haystack string
     * @param $needle string
     * @return boolean
     */
    public static function endsWith($haystack, $needle)
    {
        if mb_strlen($needle) == 0 {
            return true;
        }

        return mb_substr($haystack, -mb_strlen($needle)) === $needle;
    }

    /*
     * Check if $needle is in $haystack string string
     *
     * @param $haystack string
     * @param $needle string
     * @return boolean
     */
    public static function inString($haystack, $needle)
    {
        return mb_strpos($haystack, $needle, $offset = 0, 'UTF-8') !== false ? true : false;
    }

    /*
     * return a string after replacing all the items provided in an array
     *
     * @param $needle string
     * @param $haystack array
     * @return string
     */
    public static function multipleStringReplace($haystack, $needle)
    {
        return str_replace(array_keys($haystack), $haystack, $needle);
    }

    /*
     * get multibyte UTF-8 string length, html tags stripped
     *
     * @param $str a multibyte string
     * @return $number integer
     */
    public static function getLength($str)
    {
        return mb_strlen(strip_tags($str), 'UTF-8');
    }
}
