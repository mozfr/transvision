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
     * @return string $default_checked -> ' checked="checked"' or false
     */

    public static function checkboxDefaultOption($option, $cookie)
    {
        if ($cookie == $option) {
            $default_checked = ' checked="checked"';
        } else {
            $default_checked = false;
        }

        return $default_checked;
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

    /**
     * nicer var_dump()
     */
    public static function dump($var)
    {
        ob_start();
        print_r($var);
        $content = ob_get_contents();
        ob_end_clean();
        echo '
        <style>
            span.dump {
                display:inline-block;
                min-width:4em;
                color:orange;
            }

            span.dump-arrow {
                display:inline;
                min-width:auto;
                color:lightgray;
                padding:0 0.5em;
            }

            pre.dump {
                background-color:black;
                color: lightblue;
                display:table;
                font-family:monospace;
                padding: 0.5em;
            }

        </style>
        <script>
        function showhide(foobar)
        {
            if (foobar.innerHTML == "hide") {
                foobar.parentNode.style.display = "inline-block";
                foobar.parentNode.style.width = "100px";
                foobar.parentNode.style.height = "0.9em";
                foobar.parentNode.style.overflow = "hidden";
                foobar.innerHTML = "show";
            } else {
                foobar.parentNode.style.display = "auto";
                foobar.parentNode.style.width = "auto";
                foobar.parentNode.style.height = "auto";
                foobar.parentNode.style.overflow = "auto";
                foobar.innerHTML = "hide";
            }
        }
        </script>
        ';

        echo '<pre class="dump">';
        echo '<span class="hide" onclick="showhide(this);">hide</span>';
        echo "<br>(" . count($var) . " elements)<br>";
        echo '<code>';

        $replacements = array(
            '['     => '<span class="dump">[',
            ']'     => ']</span>',
            ' => '  => '<span class="dump-arrow">=></span>'
        );

        echo Strings::mutipleStringReplace($replacements, $content);
        echo '</code></pre>';
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
     * @nice_labels  boolean $nice_labels, indicates if $options is an associative array
     *                      with the array value as the text inside the <option> tag
     * @return string
     */
    public static function getHtmlSelectOptions($options, $selected, $nice_labels = false)
    {
        $html = '';
        foreach ($options as $key => $option) {
            $value = ($nice_labels) ? $key : $option;
            $ch = ($value == $selected) ? ' selected' : '';
            $html.= "<option" . $ch . " value=" . $value . ">" . $option . "</option>";
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
    public static function getFilenamesInFolder($folder, $excluded_files = array('.', '..', '.htaccess'))
    {
        // Get the locale list
        $files = scandir($folder);
        $files = array_diff($files, $excluded_files);

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
            $gaia_locale = 'es';
        } else {
            $gaia_locale = $locale;
        }

        $file = TMX . $repository . '/' . $gaia_locale . '/cache_' . $gaia_locale . '.php';

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
