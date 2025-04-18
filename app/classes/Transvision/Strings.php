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
     * Replace contiguous spaces in a string by a single space.
     * Leading and trailing spaces are preserved but collapsed
     * to a single space.
     *
     * @param string $string The string to analyze
     *
     * @return string Cleaned up string with extra spaces merged
     */
    public static function mtrim($string)
    {
        return preg_replace('/\s+/', ' ', $string);
    }

    /**
     * Check if $haystack starts with a string in $needles.
     * $needles can be a string or an array of strings.
     *
     * @param string $haystack String to analyse
     * @param array  $needles  The string to look for
     *
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
     * @param string $haystack String to analyse
     * @param array  $needles  The strings to look for
     *
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
     * Check if $needles are in $haystack
     *
     * @param string  $haystack  String to analyze
     * @param mixed   $needles   The string (or array of strings) to look for
     * @param boolean $match_all True if we need to match all $needles, false
     *                           if it's enough to match one. Default: false
     *
     * @return boolean True if the $haystack string contains any/all $needles
     */
    public static function inString($haystack, $needles, $match_all = false)
    {
        $matches = 0;
        $needles = (array) $needles;
        foreach ($needles as $needle) {
            if (mb_strpos($haystack, $needle, $offset = 0, 'UTF-8') !== false) {
                // If I need to match any needle, I can stop at the first match
                if (! $match_all) {
                    return true;
                }
                $matches++;
            }
        }

        return $matches == count($needles);
    }

    /**
     * Returns a string after replacing all the items provided in an array
     *
     * @param array  $replacements List of replacements to do as :
     *                             ['before1' => 'after1', 'before2' => 'after2']
     * @param string $string       The string to process
     *
     * @return string Processed string
     */
    public static function multipleStringReplace($replacements, $string)
    {
        return str_replace(array_keys($replacements), $replacements, $string);
    }

    /**
     * Highlight special characters in the string
     *
     * @param string  $string              Source text
     * @param boolean $exclude_whitespaces Optional param to specify if we need
     *                                     to highlight white spaces. White
     *                                     spaces are not highlighted by default.
     *
     * @return string Same string with specific sub-strings in <span>
     *                elements for styling with CSS
     */
    public static function highlightSpecial($string, $exclude_whitespaces = true)
    {
        $replacements = [
            ' '        => '<span class="highlight-special highlight-space" title="White space"> </span>',
            ' '        => '<span class="highlight-special highlight-gray" title="Non breakable space"> </span>',
            ' '        => '<span class="highlight-special highlight-red" title="Narrow no-break space"> </span>',
            '…'        => '<span class="highlight-special highlight-gray" title="Real ellipsis">…</span>',
            '&hellip;' => '<span class="highlight-special highlight-red" title="HTML ellipsis">…</span>',
        ];

        if ($exclude_whitespaces) {
            unset($replacements[' ']);
        }

        return self::multipleStringReplace($replacements, $string);
    }

    /**
     * Highlight searched terms in a string previously marked by markString()
     *
     * @param string $str String with marked items to highlight
     *
     * @return string HTML with searched terms in <span class="hightlight">.
     *                Returns the original string if it's missing.
     */
    public static function highlightString($str)
    {
        if ($str == '@@missing@@') {
            return $str;
        }

        /*
            [^←→]: matches any character but the marker characters.
            [^←→]|(?R): matches any character but the marker characters,
                        or recursively the entire pattern.

            This is wrapped in a non capturing group (?:), since we're
            only interested in the content of the more external markers.
            For example, for “←←A→dd→” we're only interested in “←A→dd”,
            capturing “←A→” is not useful.
            Internal extra markers are later removed by str_replace.

            See also http://stackoverflow.com/a/14952740
        */
        $str = preg_replace(
            '/←((?:[^←→]|(?R))*)→/iu',
            "<span class='highlight'>$1</span>",
            $str
        );

        // Remove remaining marking characters
        $str = str_replace(['←', '→'], '', $str);

        return $str;
    }

    /**
     * This method surrounds a searched term with ←→ It's used together
     * with highlightString() to replace these with spans.
     *
     * @param string $needle   The term we when to find and mark for
     *                         highlighting
     * @param string $haystack The string we search in
     *
     * @return string The original string with the searched term
     *                surronded by arrows. Returns the original string
     *                if it's missing.
     */
    public static function markString($needle, $haystack)
    {
        if ($haystack == '@@missing@@') {
            return $haystack;
        }

        // Search for the original $needle
        $original_needle = $needle;
        $str = str_replace($needle, '←' . $needle . '→', $haystack);

        // Search for $needle converted to Title Case
        $needle = mb_convert_case($original_needle, MB_CASE_TITLE);
        if ($original_needle != $needle) {
            $str = str_replace($needle, '←' . $needle . '→', $str);
        }

        // Search for $needle converted to lower case
        $needle = mb_strtolower($original_needle);
        if ($original_needle != $needle) {
            $str = str_replace($needle, '←' . $needle . '→', $str);
        }

        return $str;
    }

    /**
     * Get multibyte UTF-8 string length, html tags stripped
     *
     * @param string $str A multibyte string
     *
     * @return int The length of the string after removing all html
     */
    public static function getLength($str)
    {
        return mb_strlen(strip_tags($str), 'UTF-8');
    }

    /**
     * Search for similar strings in an array
     *
     * @param string $needle   string to search for
     * @param array  $haystack array of strings to search into
     * @param int    $number   optional, number of results we want, defaults to 1
     *
     * @return array Closest strings to $needle in $haystack or empty array if no match
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
     * @param string $string1 First string to compare
     * @param string $string2 Second string to compare
     *
     * @return int The Levenshtein distance
     */
    public static function levenshteinUTF8($string1, $string2)
    {
        $length1 = mb_strlen($string1, 'UTF-8');
        $length2 = mb_strlen($string2, 'UTF-8');

        // No multibyte characters, use native C Levenshtein function
        if (strlen($string1) == $length1 && strlen($string2) == $length2) {
            // Native Levenshtein function is limited to 255 characters
            if (strlen($string1) < 255 && strlen($string2) < 255) {
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
            $current_row = [];
            $current_row[0] = $i + 1;
            $c1 = mb_substr($string1, $i, 1, 'UTF-8');

            for ($j = 0; $j < $length2; $j++) {
                $c2 = mb_substr($string2, $j, 1, 'UTF-8');
                $insertions = $previous_row[$j + 1] + 1;
                $deletions = $current_row[$j] + 1;
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
     * @param string $string1 First string to compare
     * @param string $string2 Second string to compare
     *
     * @return float String similarity as a percent, higher is better
     */
    public static function levenshteinQuality($string1, $string2)
    {
        $length = max([
            mb_strlen($string1, 'UTF-8'),
            mb_strlen($string2, 'UTF-8'),
        ]);

        return (float) round((1 - self::levenshteinUTF8($string1, $string2) / $length) * 100, 5);
    }
}
