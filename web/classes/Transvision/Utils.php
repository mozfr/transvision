<?php

namespace Transvision;

class Utils
{
    /*
     * Sanitize a string or an array of strings.
     *
     * @param $str string or array of strings
     * @return sanitized string or array of strings
     */

    public static function secureText($str)
    {
        $sanitize = function($v) {
            // CRLF XSS
            $v = str_replace(['%0D', '%0A'], '', $v);
            // Remove html tags and ASCII characters below 32
            $v = filter_var(
                $v,
                FILTER_SANITIZE_STRING,
                FILTER_FLAG_STRIP_LOW
            );
            return $v;
        };

        return is_array($str) ? array_map($sanitize, $str) : $sanitize($str);
    }

    /*
     * Helper function to set checkboxes value for the default option in source locale, target locale and repository
     *
     * @param  string $option
     * @param  string $cookie
     * @return string $default_checked -> ' checked="checked"' or false
     */

    public static function checkboxDefaultOption($option, $cookie)
    {
        return $cookie == $option ? ' checked="checked"' : false;
    }

    /*
     *  helper function to set checkboxes value
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

    public static function markString($needle, $haystack)
    {
        $str = str_replace($needle, '←' . $needle . '→', $haystack);
        $str = str_replace(ucwords($needle), '←' . ucwords($needle) . '→', $str);
        $str = str_replace(strtolower($needle), '←' . strtolower($needle) . '→', $str);

        return $str;
    }

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

    public static function printSimpleTable(
        $arr,
        $arr2 = false,
        $titles = array('Column1', 'Column2', 'Column3', 'Column4'),
        $cssclass = ''
    ) {
        if ($cssclass != '') {
            echo "<table class='{$cssclass}'>\n";
        } else {
            echo "<table>\n";
        }
        echo "  <tr>\n" .
             "    <th>{$titles[0]}</th><th>{$titles[1]}</th>\n";

        if ($arr2) {
            echo "    <th>{$titles[2]}</th><th>{$titles[3]}</th>\n";
        }

        echo "  </tr>\n";

        foreach ($arr as $key => $val) {
            echo "  <tr>\n";
            if ($arr2) {
                echo "    <td><span class='celltitle'>{$titles[0]}</span><div class='string'>" . ShowResults::formatEntity($val) . '</div></td>';
                echo "    <td><span class='celltitle'>{$titles[1]}</span><div class='string'>" . $arr2[$val] . '</div></td>';
                echo "    <td><span class='celltitle'>{$titles[2]}</span><div class='string'>" . str_replace(' ', '<span class="highlight-red"> </span>', $arr2[$key]) . '</div></td>';
                echo "    <td><span class='celltitle'>{$titles[3]}</span><div class='string'>" . ShowResults::formatEntity($key) . '<div></td>';
            } else {
                echo "    <td>{$val}</td>\n";
                echo "    <td>{$key}</td>\n";
            }
            echo "  </tr>\n";
        }
        echo "</table>";
    }

    /*
     * Split a sentence in words from longest to shortest
     *
     * @param  string $sentence
     * @return array
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

    /*
     * Generate a list of <option> html tags from an array and mark one as selected
     *
     * @param array  $options  list of <option>
     * @param string $selected the option which should have the 'selected' html attribute
     * @nice_labels  boolean $nice_labels, indicates if $options is an associative array
     *                      with the array value as the text inside the <option> tag
     * @return string
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

    /*
     * Return the list of files in a folder as an array
     *
     * @param  string $folder  the directory we want to access
     * @param  array  $excluded_files to exclude from results, by default . and ..
     * @return array
     */
    public static function getFilenamesInFolder($folder, $excluded_files = ['.', '..', '.htaccess'])
    {
        return array_diff(scandir($folder), $excluded_files);
    }


    /*
     * Return an array of strings from our repos
     * @param $locale locale code queried
     * @param $repository string repository such as gaia_1_3, central, aurora...
     * @return array of localized strings or empty array id no match
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


    /*
     * cleanSearch
     * @param $str string to search
     * @return $str cleaned up string for security and noise
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

    /*
     * Compare original and translated strings to check abnormal length
     *
     * @param $origin en-US string
     * @param $translated locale string
     * @return $abnormal_length
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

    /*
     * Generate a table with TMX Download links
     *
     * @param $locales array with locales
     * @return $output table with download links
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
