<?php
namespace Transvision;

use Cache\Cache;
use DateTime;

/**
 * Utils class
 *
 * Various static methods that don't belong yet to a specialized class
 *
 * @package Transvision
 */
class Utils
{
    /**
     * Sanitize a string or an array of strings for security before template use.
     *
     * @param  string $string The string we want to sanitize
     * @return string Sanitized string for security
     */
    public static function secureText($string)
    {
        $sanitize = function ($v) {
            // CRLF XSS
            $v = str_replace(['%0D', '%0A'], '', $v);
            // We want to convert line breaks into spaces
            $v = str_replace("\n", ' ', $v);
            // Escape HTML tags and remove ASCII characters below 32
            $v = filter_var(
                $v,
                FILTER_SANITIZE_SPECIAL_CHARS,
                FILTER_FLAG_STRIP_LOW
            );

            return $v;
        };

        return is_array($string) ? array_map($sanitize, $string) : $sanitize($string);
    }

    /**
     * Helper function to set checkboxes value for the default
     * option in source locale, target locale and repository
     * depending on the cookie
     *
     * @param  string $cookie Out cookie
     * @param  string $option The checkbox
     * @return string Checked html attribute if cookie matches $option or false
     */
    public static function checkboxDefaultOption($option, $cookie)
    {
        return $cookie == $option ? ' checked="checked"' : false;
    }

    /**
     *  Helper function to set checkboxes value in <input> on main search form
     * Example:
     * <input type="checkbox"
     *  id="case_sensitive"
     *  value="case_sensitive"
     *  <?=Utils::checkboxState($check['case_sensitive'])?>>
     *                    />
     * @param  string $str   Usually the value of a GET/POST parameter setting a box
     * @param  string $extra Optional. Defaults to empty string.
     * @return string Checked attribute or empty string.
     */
    public static function checkboxState($str, $extra = '')
    {
        if (isset($_GET['t2t']) && $extra != 't2t') {
            return ' disabled="disabled"';
        }

        if (isset($_GET['t2t']) && $extra == 't2t') {
            return ' checked="checked"';
        }

        return $str ? ' checked="checked"' : '';
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
     *                surronded by arrows
     */
    public static function markString($needle, $haystack)
    {
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
     * Highlight searched terms in a string previously marked by markString()
     *
     * @param string $str String with marked items to highlight
     *
     * @return string HTML with searched terms in <span class="hightlight">
     */
    public static function highlightString($str)
    {
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
     * Print a simple table, used in the accesskeys view, needs rework
     *
     * @param  array  $arr      First column of data
     * @param  array  $arr2     Optional. A second column of data
     * @param  array  $titles   Column titles, by default 4 columns
     * @param  string $cssclass optional css class to apply to the table
     * @return string and html table
     */
    public static function printSimpleTable(
        $arr,
        $arr2 = false,
        $titles = ['Column1', 'Column2', 'Column3', 'Column4'],
        $cssclass = ''
    ) {
        if ($cssclass != '') {
            echo "<table class='{$cssclass}'>";
        } else {
            echo '<table>';
        }
        echo "<thead>\n  <tr class='column_headers'>\n" .
             "    <th>{$titles[0]}</th><th>{$titles[1]}</th>\n";

        if ($arr2) {
            echo "    <th>{$titles[2]}</th><th>{$titles[3]}</th>\n";
        }

        echo "  </tr>\n</thead>\n<tbody>\n";

        foreach ($arr as $key => $val) {
            echo '<tr>';
            if ($arr2) {
                echo "<td><span class='celltitle'>{$titles[0]}</span><div class='string'>" . ShowResults::formatEntity($val) . '</div></td>';
                echo "<td><span class='celltitle'>{$titles[1]}</span><div class='string'>" . $arr2[$val] . '</div></td>';
                echo "<td><span class='celltitle'>{$titles[2]}</span><div class='string'>" . str_replace(' ', '<span class="highlight-red"> </span>', $arr2[$key]) . '</div></td>';
                echo "<td><span class='celltitle'>{$titles[3]}</span><div class='string'>" . ShowResults::formatEntity($key) . '</div></td>';
            } else {
                echo "<td>{$key}</td>";
                echo "<td>{$val}</td>";
            }
            echo '</tr>';
        }
        echo "</tbody>\n</table>\n";
    }

    /**
     * Split a sentence in words from longest to shortest
     *
     * @param  string $sentence
     * @return array  all the words in the sentence sorted by length
     */
    public static function uniqueWords($sentence)
    {
        $words = explode(' ', $sentence);
        $words = array_filter($words); // Filter out extra spaces
        $words = array_unique($words); // Remove duplicate words
        // Sort words from longest to shortest
        usort(
            $words,
            function ($a, $b) {
                return mb_strlen($b) - mb_strlen($a);
            }
        );

        return $words;
    }

    /**
     * Generate a list of <option> html tags from an array and mark one as selected
     *
     * @param  array   $options     All the values we want in <option> tags
     * @param  string  $selected    put selected tag on a specific <option>
     * @param  boolean $nice_labels Optional. Defaults to False.
     *                              Use nice labels for the option.
     *                              Indicates if $options is an associative
     *                              array with the array value as the text
     *                              inside the <option> tag
     * @return string  html <option> tags
     */
    public static function getHtmlSelectOptions($options, $selected, $nice_labels = false)
    {
        $html = '';

        foreach ($options as $key => $option) {
            $value = $nice_labels ? $key : $option;
            $ch    = ($value == $selected) ? ' selected' : '';
            $html .= "<option" . $ch . " value=" . $value . ">" . $option . "</option>";
        }

        return $html;
    }

    /**
     * Return an array of strings for a locale from a repository
     * @param  string $locale     Locale we want to have strings for
     * @param  string $repository string repository such as gaia_2_5, central...
     * @return array  Localized strings or empty array if no match
     */
    public static function getRepoStrings($locale, $repository)
    {
        $locale = Project::getLocaleInContext($locale, $repository);

        $file = TMX . "{$locale}/cache_{$locale}_{$repository}.php";
        if (! is_file($file)) {
            return [];
        }

        include $file;

        if (! isset($tmx)) {
            error_log('$tmx not set in file: ' . $file);
        }

        return $tmx;
    }

    /**
     * Return an array of entities for a locale from a repository
     * @param  string $locale     Locale we want to have entities for
     * @param  string $repository string repository such as gaia_2_5, central...
     * @return array  Entities or empty array if no match
     */
    public static function getRepoEntities($locale, $repository)
    {
        $key = $locale . $repository . 'entities';

        if (! $entities = Cache::getKey($key)) {
            if ($entities = array_keys(self::getRepoStrings($locale, $repository))) {
                Cache::setKey($key, $entities);
            }
        }

        return isset($entities) ? $entities : [];
    }

    /**
     * Clean up for "noise" a string
     *
     * @param  string $string the string to clean up
     * @return string The cleaned up string
     */
    public static function cleanString($string)
    {
        if (! is_string($string)) {
            return '';
        }

        // Filter out double spaces
        $string = Strings::mtrim($string);

        return $string;
    }

    /**
     * Compare original and translated strings to check abnormal length.
     * This is used in search views to warn of strings that look much wider
     * or much shorter than English
     *
     * @param  string $origin     The source string
     * @param  string $translated The string we want to compare to
     * @return string 'large' or 'small' or false if it doesn't look abnormal
     */
    public static function checkAbnormalStringLength($origin, $translated)
    {
        $origin_length = Strings::getLength($origin);
        $translated_length = Strings::getLength($translated);

        if ($origin_length != 0 && $translated_length != 0) {
            $difference = ($translated_length / $origin_length) * 100;
            $difference = round($difference);

            if ($origin_length > 100 && $difference > 150) {
                // Large translation for a large origin
                $abnormal_length =  'large';
            } elseif ($origin_length > 100 && $difference < 50) {
                // Small translation for a large origin
                $abnormal_length =  'small';
            } elseif ($origin_length < 100 && $difference > 200 && $translated_length > 100) {
                // Large translation for a small origin
                $abnormal_length =  'large';
            } elseif ($origin_length < 100 && $difference < 25) {
                // Small translation for a small origin
                $abnormal_length =  'small';
            } else {
                // No problems detected
                $abnormal_length =  false;
            }
        } else {
            // Missing origin or translated string
            $abnormal_length =  false;
        }

        return $abnormal_length;
    }

    /**
     * Check if a parameter exists, if not, return fallback value
     *
     * @param  array  $arr      Array in which we want to check $value
     * @param  string $value    Parameter we want to check
     * @param  string $fallback Default value
     * @return string $value if $value into $arr, $fallback otherwise
     */
    public static function getOrSet($arr, $value, $fallback)
    {
        return isset($value) && in_array($value, $arr)
                ? $value
                : $fallback;
    }

    /**
     * Utility function to return the memory used by a script
     * and the time needed to compute the data.
     *
     * @return array [Memory peak in bytes, Memory peak in MB, Computation time]
     */
    public static function getScriptPerformances()
    {
        $memory_peak_B    = memory_get_peak_usage(true);
        $memory_peak_MB   = round(($memory_peak_B / (1024 * 1024)), 2);
        $computation_time = round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']), 4);

        return [$memory_peak_B, $memory_peak_MB, $computation_time];
    }

    /**
     * Utility function to log to stderr the memory used by a script
     * and the time needed to generate the page.
     * This is used only when the constants DEBUG and PERF_CHECK are set to True
     * because we don't want to fill our logs with debug data on production.
     *
     * @return void
     */
    public static function logScriptPerformances()
    {
        list($memory_peak_B, $memory_peak_MB, $computation_time) = self::getScriptPerformances();

        if (DEBUG && PERF_CHECK) {
            error_log("Memory peak: {$memory_peak_B} ({$memory_peak_MB}MB)");
            error_log("Elapsed time (s): {$computation_time}");
        }
    }

    /**
     * Utility function to log the memory used by a script
     * and the time needed to generate the page as an HTTP header.
     *
     * @return void
     */
    public static function addPerformancesHTTPHeader()
    {
        list($memory_peak_B, $memory_peak_MB, $computation_time) = self::getScriptPerformances();
        header("Transvision-perf: Memory: {$memory_peak_B} ({$memory_peak_MB}MB); Time: {$computation_time}s");
    }

    /**
     * Generate a red to green color from a numeric value
     *
     * @return the RGB values separated by a comma
     */
    public static function redYellowGreen($number)
    {
        // Work with 0-99 values
        $number--;

        if ($number < 50) {
            // Red to yellow
            $r = 255;
            $g = floor(255 * ($number / 50));
        } else {
            // Yellow to red
            $r = floor(255 * ((50 - $number % 50) / 50));
            $g = 255;
        }
        $b = 0;

        return "$r,$g,$b";
    }

    /**
     * Lazy function to handle English plural form
     *
     * @param  int    $count The value to check
     * @param  string $text  The word to pluralize
     * @return the    value concatenated with the word properly pluralized
     */
    public static function pluralize($count, $text)
    {
        return $count . (($count == 1) ? (" {$text}") : (" ${text}s"));
    }

    /**
     * Get the elapsed/remaining time from a DateTime vs. now
     *
     * @param  DateTime $datetime The DateTime object to check against current time
     * @param  DateTime $ref_time Reference time to calculate the difference (optional)
     * @return string   String containing the value concatenated with the pluralized unit
     */
    public static function ago($datetime, $ref_time = '')
    {
        if (! $ref_time instanceof DateTime) {
            // Use current time as reference
            $ref_time = new DateTime();
        }
        $interval = $ref_time->diff($datetime);
        $suffix = $interval->invert ? ' ago' : '';
        if ($interval->y >= 1) {
            return self::pluralize($interval->y, 'year') . $suffix;
        }
        if ($interval->m >= 1) {
            return self::pluralize($interval->m, 'month') . $suffix;
        }
        if ($interval->d >= 1) {
            return self::pluralize($interval->d, 'day') . $suffix;
        }
        if ($interval->h >= 1) {
            return self::pluralize($interval->h, 'hour') . $suffix;
        }
        if ($interval->i >= 1) {
            return self::pluralize($interval->i, 'minute') . $suffix;
        }

        return self::pluralize($interval->s, 'second') . $suffix;
    }

    /**
     * Return the current URL with the json GET variable appended
     * This is used on views which also exist in our public API
     * https://github.com/mozfr/transvision/wiki/JSON-API
     *
     * @return string URL with 'json' appended as part of the query string
     */
    public static function redirectToAPI()
    {
        return $_SERVER["REQUEST_URI"] . (is_null($_SERVER['QUERY_STRING']) ? '?json' : '&json');
    }

    /**
     * Return the current URL with the json GET variable appended
     * and also adds locale codes if they are not already provided
     * by redirectToAPI()
     *
     * @param  string $source_locale Locale code for source
     * @param  string $target_locale Locale code for target
     * @return string URL with 'json' appended as part of the query string
     */
    public static function APIPromotion($source_locale, $target_locale)
    {
        // We are going to split and then rebuild QUERY_STRING
        parse_str(self::redirectToAPI(), $args);

        $args = array_map(['self', 'secureText'], $args);
        $args['locale']       = $source_locale;
        $args['sourcelocale'] = $target_locale;
        $args['json']         = 'true';

        // We don't want to encode slashes in searches for entity names
        $query = urldecode(http_build_query($args));

        return $query;
    }
}
