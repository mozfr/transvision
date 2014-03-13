<?php
namespace Transvision;

/**
 * Strings class
 *
 * This class is for all the methods we need to manipulate strings
 *
 * @package Transvision
 */
class Strings
{
    /**
     * Replace contiguous spaces in a string by a single space
     *
     * @param string $string The string to analyze
     * @return string Cleaned up string with extra spaces merged
     */
    public static function mtrim($string)
    {
        $string = explode(' ', $string);
        $string = array_filter($string);
        $string = implode(' ', $string);

        return $string;
    }

    /**
     * Check if $haystack starts with the $needle string
     *
     * @param string $haystack String to analyse
     * @param string $needle The string to look for
     * @return boolean True if the strings starts with $needle
     */
    public static function startsWith($haystack, $needle)
    {
        return !strncmp($haystack, $needle, mb_strlen($needle));
    }

    /**
     * Check if $haystack ends with the $needle string
     *
     * @param string $haystack String to analyse
     * @param string $needle The string to look for
     * @return boolean True if the strings ends with $needle
     */
    public static function endsWith($haystack, $needle)
    {
        if (mb_strlen($needle) == 0) {
            return true;
        }

        return mb_substr($haystack, -mb_strlen($needle)) === $needle;
    }

    /**
     * Check if $needle is in $haystack
     *
     * @param string $haystack String to analyse
     * @param string $needle The string to look for
     * @return boolean True is the $haystack string contains $needle
     */
    public static function inString($haystack, $needle)
    {
        return mb_strpos($haystack, $needle, $offset = 0, 'UTF-8') !== false ? true : false;
    }

    /**
     * Returns a string after replacing all the items provided in an array
     *
     * @param array $replacements List of replacements to do as :
     *                            ['before1' => 'after1', 'before2' => 'after2']
     * @param string $string The string to process
     * @return string Processed string
     */
    public static function multipleStringReplace($replacements, $string)
    {
        return str_replace(array_keys($replacements), $replacements, $string);
    }

    /**
     * Get multibyte UTF-8 string length, html tags stripped
     *
     * @param $str a multibyte string
     * @return integer The length of the string after removing all html
     */
    public static function getLength($str)
    {
        return mb_strlen(strip_tags($str), 'UTF-8');
    }
}
