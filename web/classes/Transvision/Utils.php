<?php

namespace Transvision;

class Utils
{
    /*
     * Check if a variable exists and is not set to false
     * Useful to check variables in $_GET for example
     *
     * @param $var variable to process
     * @return boolean
     */

    public static function valid($var)
    {
        if (isset($var) && $var != false) {
            return true;
        } else {
            return false;
        }
    }

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
     * @return string $defaultChecked -> ' checked="checked"' or false
     */

    public static function checkboxDefaultOption($option, $cookie)
    {
        if ($cookie == $option) {
            $defaultChecked = ' checked="checked"';
        } else {
            $defaultChecked = false;
        }

        return $defaultChecked;
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

        return ($str) ? ' checked="checked"' : '';
    }

    /*
     * Create an array for search results with this format:
     * 'entity' => ['locale 1', 'locale 2']
     */

    public static function results($entities, $locale1Strings, $locale2Strings)
    {

        $searchResults = array();

        foreach ($entities as $entity) {
            $searchResults[$entity] = array($locale1Strings[$entity],
                                             $locale2Strings[$entity]);
        }

        return $searchResults;
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
        $str = str_replace(array('←', '→'), '', $str);

        return $str;
    }

    /**
     * get the current microtime for perf measurements
     *
     */

    public static function getmicrotime()
    {
        list($usec, $sec) = explode(' ', microtime());

        return ((float) $usec + (float) $sec);
    }


    public static function printSimpleTable(
        $arr,
        $arr2 = false,
        $titles = array('Column1', 'Column2', 'Column3', 'Column4')
    ) {
        echo "<table>
              <tr>
              <th>{$titles[0]}</th><th>{$titles[1]}</th>";

        if ($arr2) {
            echo "<th>{$titles[2]}</th><th>{$titles[3]}</th>";
        }

        echo '</tr>';

        foreach ($arr as $key => $val) {
            echo '<tr>';
            if ($arr2) {
                echo '<td>' . ShowResults::formatEntity($val) . '</td>';
                echo '<td>' . $arr2[$val] . '</td>';
                echo '<td>' . str_replace(' ', '<span class="highlight-red"> </span>', $arr2[$key]) . '</td>';
                echo '<td>' . ShowResults::formatEntity($key) . '</td>';
            } else {
                echo '<td>' . $val . '</td>';
                echo '<td>' . $key . '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
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
     * @nicelabels  boolean $nicelabels, indicates if $options is an associative array
     *                      with the array value as the text inside the <option> tag
     * @return string
     */
    public static function getHtmlSelectOptions($options, $selected, $nicelabels = false)
    {
        $html = '';
        foreach ($options as $key => $option) {
            $value = ($nicelabels) ? $key : $option;
            $ch = ($value == $selected) ? ' selected' : '';
            $html.= "<option" . $ch . " value=" . $value . ">" . $option . "</option>";
        }

        return $html;
    }

    /*
     * Return the list of files in a folder as an array
     *
     * @param  string $folder  the directory we want to access
     * @param  array  $exclude files to exclude from results, by default . and ..
     * @return array
     */
    public static function getFilenamesInFolder($folder, $excludedFiles = array('.', '..', '.htaccess'))
    {
        // Get the locale list
        $files = scandir($folder);
        $files = array_diff($files, $excludedFiles);

        return $files;
    }

    /*
     * Return an array of strings from our repos
     */
    public static function getRepoStrings($locale, $repository)
    {
        $tmx = array();
        global $spanishes;

        if (Strings::startsWith($repository, 'gaia') == false) {
            if ($locale == 'en-US') {
                // English
                include TMX . "{$repository}/{$locale}/cache_en-US.php";
            } else {
                // Localised, for a locale to locale comparison
                // HACK: check if file exist to avoid PHP errors with coockie default value
                $file = TMX . "{$repository}/${locale}/cache_${locale}.php";
                if (file_exists($file)) {
                    include $file;
                }
            }
        }

        // We have only one Spanish for Gaia
        if (in_array($locale, $spanishes)) {
            $gaialocale = 'es';
        } else {
            $gaialocale = $locale;
        }

        $file = TMX . $repository . '/' . $gaialocale . '/cache_' . $gaialocale . '.php';

        if (file_exists($file)) {
            include $file;
        }

        return $tmx;
    }

    /*
     * cleanSearch
     * @param $str string to search
     * @return $str cleaned up string for security and noise
     */
    public static function cleanSearch($str)
    {
        $str = is_string($str) ? $str : '';
        $str = Utils::secureText($str);
        $str = stripslashes($str);
        // Filter out double spaces
        $str = Strings::mtrim($str);

        return $str;
    }

    /*
     * Compare original and translated strings to check anormal length
     *
     * @param $origin en-US string
     * @param $translated locale string
     * @return $anormal_length
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
                $anormal_length =  'large';
            } elseif ($origin_length > 100 && $difference < 50) {
                //small translation for a large origin
                $anormal_length =  'small';
            } elseif ($origin_length < 100 && $difference > 200 && $translated_length > 100) {
                //large translation for a small origin
                $anormal_length =  'large';
            } elseif ($origin_length < 100 && $difference < 25) {
                //small translation for a small origin
                $anormal_length =  'small';
            } else {
                //no problems detected
                $anormal_length =  false;
            }
        } else {
            //missing origin or translated string
            $anormal_length =  false;
        }

        return $anormal_length;
    }

    /*
     * Generate a table with TMX Download links
     *
     * @param $locales array with locales
     * @return $output table with download links
     */
    public static function tmxDownloadTable($locales)
    {
        $output = '<table id="DownloadsTable"><tr><th></th><th colspan="4">Desktop Software</th><th colspan="3">Firefox OS</th></tr><tr><th></th><th>Central</th><th>Aurora</th><th>Beta</th><th>Release</th><th>Gaia central</th><th>Gaia 1.1</th><th>Gaia 1.2</th></tr>';

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
                . '</tr>';
        }

        $output .= '</table>';

        return $output;
    }
}
