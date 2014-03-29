<?php
namespace Transvision;

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
     * @param string $string The string we want to sanitize
     * @return string Sanitized string for security
     */
    public static function secureText($string)
    {
        $sanitize = function($v) {
            // CRLF XSS
            $v = str_replace(['%0D', '%0A'], '', $v);
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
     * @param string $cookie Out cookie
     * @param string $option The checkbox
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
     * @param string $str Usually the value of a GET/POST parameter setting a box
     * @param string $extra Optional. Defaults to empty string.
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
     * This method surrounds a searched term by ←→ so as to nbe used together
     * with highlightString() and replace those by spans.
     *
     * @param string $needle The term we when to find and mark for highlighting
     * @param string $haystack The string we search in
     * @return string The original string with the searched term surronded by arrows
     */
    public static function markString($needle, $haystack)
    {
        $str = str_replace($needle, '←' . $needle . '→', $haystack);
        $str = str_replace(ucwords($needle), '←' . ucwords($needle) . '→', $str);
        $str = str_replace(strtolower($needle), '←' . strtolower($needle) . '→', $str);

        return $str;
    }


    /**
     * Highlight searched terms in a string that were marked by markString()
     *
     * @param string $str String with marked items to highlight
     * @return string html with searched terms in <span class="hightlight">
     */
    public static function highlightString($str)
    {
        $str = preg_replace(
            '/←←←(.*)→→→/isU',
            "<span class='highlight'>$1</span>",
            $str
        );

        $str = preg_replace(
            '/←←(.*)→→/isU',
            "<span class='highlight'>$1</span>",
            $str
        );

        $str = preg_replace(
            '/←(.*)→/isU',
            "<span class='highlight'>$1</span>",
            $str
        );

        // remove last ones
        $str = str_replace(['←', '→'], '', $str);

        return $str;
    }

    /**
     * Print a simple table, used in the accesskeys view, needs rework
     *
     * @param array $arr First column of data
     * @param array $arr2 Optional. A second column of data
     * @param array $titles Column titles, by default 4 columns
     * @param string $cssclass optional css class to apply to the table
     * @return string and html table
     */
    public static function printSimpleTable(
        $arr,
        $arr2 = false,
        $titles = array('Column1', 'Column2', 'Column3', 'Column4'),
        $cssclass = ''
    ) {
        if ($cssclass != '') {
            echo "<table class='{$cssclass}'>";
        } else {
            echo '<table>';
        }
        echo '<tr>' .
             "<th>{$titles[0]}</th><th>{$titles[1]}</th>";

        if ($arr2) {
            echo "<th>{$titles[2]}</th><th>{$titles[3]}</th>";
        }

        echo '</tr>';

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
        echo '</table>';
    }

    /**
     * Split a sentence in words from longest to shortest
     *
     * @param string $sentence
     * @return array all the words in the sentence sorted by length
     */
    public static function uniqueWords($sentence)
    {
        $words = explode(' ', $sentence);
        $words = array_filter($words); // filter out extra spaces
        $words = array_unique($words); // remove duplicate words
        // sort words from longest to shortest
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
     * @param array $options All the values we want in <option> tags
     * @param string $selected put selected tag on a specific <option>
     * @param boolean $nice_labels Optional. Defaults to False.
     *                             Use nice labels for the option.
     *                             Indicates if $options is an associative
     *                             array with the array value as the text
     *                             inside the <option> tag
     * @return string html <option> tags
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
     * @param string $locale Locale we want to have strings for
     * @param string $repository string repository such as gaia_1_3, central...
     * @return array Localized strings or empty array if no match
     */
    public static function getRepoStrings($locale, $repository)
    {

        if (Strings::startsWith($repository, 'gaia')) {
            $locale = Strings::startsWith($locale, 'es-') ? 'es' : $locale;
        }

        $file = TMX . "{$repository}/{$locale}/cache_{$locale}.php";

        if (is_file($file)) {
            include $file;
        }

        return isset($tmx) ? $tmx : array();
    }

    /**
     * Sanitize and clean up for "noise" a string
     *
     * @param string $str the string to clean up
     * @return string the cleaned up string
     */
    public static function cleanSearch($str)
    {
        if (!is_string($str)) {
            return '';
        }

        $str = self::secureText($str);
        $str = stripslashes($str);
        // Filter out double spaces
        $str = Strings::mtrim($str);

        return $str;
    }

    /**
     * Compare original and translated strings to check abnormal length.
     * This is used in search views to warn of strings that look much wider
     * or much shorter than English
     *
     * @param string $origin The source string
     * @param string $translated  The string we want to compare to
     * @return string 'large' or 'small' or false if it doesn't look abnormal
     */
    public static function checkAbnormalStringLength($origin, $translated)
    {
        $origin_length = Strings::getLength($origin);
        $translated_length = Strings::getLength($translated);

        if ($origin_length != 0 && $translated_length != 0) {
            $difference = ( $translated_length / $origin_length ) * 100;
            $difference = round($difference);

            if ($origin_length > 100 && $difference > 150) {
                //large translation for a large origin
                $abnormal_length =  'large';
            } elseif ($origin_length > 100 && $difference < 50) {
                //small translation for a large origin
                $abnormal_length =  'small';
            } elseif ($origin_length < 100 && $difference > 200 && $translated_length > 100) {
                //large translation for a small origin
                $abnormal_length =  'large';
            } elseif ($origin_length < 100 && $difference < 25) {
                //small translation for a small origin
                $abnormal_length =  'small';
            } else {
                //no problems detected
                $abnormal_length =  false;
            }
        } else {
            //missing origin or translated string
            $abnormal_length =  false;
        }

        return $abnormal_length;
    }

    /**
     * Generate a table with TMX Download links for the download page
     *
     * @param array $locales All the locales we want to show
     * @return string Html table with all the download links
     */
    public static function tmxDownloadTable($locales)
    {
        $output = '<table id="DownloadsTable"><tr><th></th><th colspan="4">Desktop Software</th><th colspan="4">Firefox OS</th></tr><tr><th></th><th>Central</th><th>Aurora</th><th>Beta</th><th>Release</th><th>Gaia central</th><th>Gaia 1.1</th><th>Gaia 1.2</th><th>Gaia 1.3</th></tr>';

        foreach ($locales as $locale) {
            $cell = function ($repo) use ($locale) {
                $file = $repo . '/'. $locale . '/memoire_en-US_' . $locale . '.tmx';
                $str = file_exists(TMX . $file)
                        ? '<a href="/TMX/' . $file . '">Download</a>'
                        : '<span class="red">TMX Not Available</span>';

                return '<td>' . $str . '</td>';
            };

            $output .=
                '<tr><th>' . $locale . '</th>'
                . $cell('central')
                . $cell('aurora')
                . $cell('beta')
                . $cell('release')
                . $cell('gaia')
                . $cell('gaia_1_1')
                . $cell('gaia_1_2')
                . $cell('gaia_1_3')
                . '</tr>';
        }

        $output .= '</table>';

        return $output;
    }
}
