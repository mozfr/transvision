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
     * @param  string $string The string to analyze
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
     * Check if $haystack starts with a string in $needles.
     * $needles can be a string or an array of strings.
     *
     * @param  string  $haystack String to analyse
     * @param  array   $needles  The string to look for
     * @return boolean True if the $haystack string starts with a string in $needles
     */
    public static function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $prefix) {
            if (! strncmp($haystack, $prefix, mb_strlen($prefix))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if $haystack ends with a string in $needles.
     * $needles can be a string or an array of strings.
     *
     * @param  string  $haystack String to analyse
     * @param  array   $needles  The strings to look for
     * @return boolean True if the $haystack string ends with a string in $needles
     */
    public static function endsWith($haystack, $needles)
    {
        foreach ((array) $needles as $suffix) {
            if (mb_substr($haystack, -mb_strlen($suffix)) === $suffix) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if $needle is in $haystack
     *
     * @param  string  $haystack String to analyse
     * @param  string  $needle   The string to look for
     * @return boolean True if the $haystack string contains $needle
     */
    public static function inString($haystack, $needle)
    {
        return mb_strpos($haystack, $needle, $offset = 0, 'UTF-8') !== false ? true : false;
    }

    /**
     * Returns a string after replacing all the items provided in an array
     *
     * @param  array  $replacements List of replacements to do as :
     *                              ['before1' => 'after1', 'before2' => 'after2']
     * @param  string $string       The string to process
     * @return string Processed string
     */
    public static function multipleStringReplace($replacements, $string)
    {
        return str_replace(array_keys($replacements), $replacements, $string);
    }

    /**
     * Get multibyte UTF-8 string length, html tags stripped
     *
     * @param  string $str A multibyte string
     * @return int    The length of the string after removing all html
     */
    public static function getLength($str)
    {
        return mb_strlen(strip_tags($str), 'UTF-8');
    }

    /**
     * Search for similar strings in an array
     *
     * @param  string $needle   string to search for
     * @param  array  $haystack array of strings to search into
     * @param  int    $number   optional, number of results we want, defaults to 1
     * @return array  Closest strings to $needle in $haystack or empty array if no match
     */
    public static function getSimilar($needle, $haystack, $number = 1)
    {
        $similarity = 0;
        $matches = [];

        foreach ($haystack as $string) {
            similar_text($needle, $string, $percent);

            if ($percent >= $similarity && ! in_array($string, $matches)) {
                $similarity = $percent;

                if (count($matches) < $number) {
                    $matches[] = $string;
                } else {
                    array_shift($matches);
                    $matches[] = $string;
                }
            } elseif (count($matches) < $number) {
                // We don't want to return less strings than $number
                $matches[] = $string;
            }
        }

        // We reverse the array to get the best results first
        return array_reverse($matches);
    }
    /**
     * Levenshtein implementation that works with unicode strings
     * It calculates the number of insertions/deletions/Additions
     * needed to change string 1 into string 2
     *
     * Fallback to native levenshtein() if strings are ascii
     *
     * @param  string $string1 First string to compare
     * @param  string $string2 Second string to compare
     * @return int    The Levenshtein distance
     */
    public static function levenshteinUTF8($string1, $string2)
    {
        $length1 = mb_strlen($string1, 'UTF-8');
        $length2 = mb_strlen($string2, 'UTF-8');

        // No multibyte characters, use native C Levenshtein function
        if (strlen($string1) == $length1 && strlen($string2) == $length2) {
            // Native Levenshtein function is limited to 255 characters
            if (strlen($string1) < 255  && strlen($string2) < 255) {
                return levenshtein($string1, $string2);
            }
        }

        // Always compare the longest string to the shortest
        if ($length1 < $length2) {
            return self::levenshteinUTF8($string2, $string1);
        }

        // If length is 0, then we only have as many insertions as letters in string2
        if ($length1 == 0) {
            return $length2;
        }

        // Strings are the same
        if ($string1 === $string2) {
            return 0;
        }

        $previous_row = range(0, $length2);

        // This is a matrix
        for ($i = 0; $i < $length1; $i++) {
            $current_row    = [];
            $current_row[0] = $i + 1;
            $c1 = mb_substr($string1, $i, 1, 'UTF-8');

            for ($j = 0; $j < $length2; $j++) {
                $c2            = mb_substr($string2, $j, 1, 'UTF-8');
                $insertions    = $previous_row[$j + 1] + 1;
                $deletions     = $current_row[$j] + 1;
                $substitutions = $previous_row[$j] + (($c1 != $c2) ? 1 : 0);
                $current_row[] = min($insertions, $deletions, $substitutions);
            }

            $previous_row = $current_row;
        }

        return (int) $previous_row[$length2];
    }

    /**
     * Get a quality index (%) for two strings compared with Levenshtein distance
     *
     * @param  string $string1 First string to compare
     * @param  string $string2 Second string to compare
     * @return float  String similarity as a percent, higher is better
     */
    public static function levenshteinQuality($string1, $string2)
    {
        $length = max([
            mb_strlen($string1, 'UTF-8'),
            mb_strlen($string2, 'UTF-8'),
        ]);

        return (float) (1 - self::levenshteinUTF8($string1, $string2) / $length) * 100;
    }
}
