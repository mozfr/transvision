<?php

namespace Transvision;

class Utils
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
     * @param $var string or array of strings
     * @param $tablo optional parameter to indicate that $var = true
     * @return string or array, depending on the input
     */

    public static function secureText($var, $tablo = true)
    {
        if (!is_array($var)) {
            $var   = array($var);
            $tablo = false;
        }

        foreach ($var as $item => $value) {
            // CRLF XSS
            $value = str_replace('%0D', '', $value);
            $value = str_replace('%0A', '', $value);

            // Remove html tags and ASCII characters below 32
            $value = filter_var(
                $value,
                FILTER_SANITIZE_STRING,
                FILTER_FLAG_STRIP_LOW
            );

            // Repopulate value
            $var[$item] = $value;
        }

        return ($tablo == true) ? $var : $var[0];
    }

    /*
     * Helper function to set checkboxes value for the default option in source locale, target locale and repository
     *
     * @param string $option
     * @param string $cookie
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

    public static function results($entities, $locale1_strings, $locale2_strings)
    {

        $search_results = array();

        foreach ($entities as $entity) {
            $search_results[$entity] = array($locale1_strings[$entity],
                                             $locale2_strings[$entity]);
        }

        return $search_results;
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



    /*
     * make an entity look nice in tables
     *
     */

    public static function formatEntity($entity, $highlight = false)
    {
        // let's analyse the entity for the search string
        $chunk  = explode(':', $entity);

        if ($highlight) {
            $entity = array_pop($chunk);
            $highlight = preg_quote($highlight, '/');
            $entity = preg_replace("/($highlight)/i", '<span class="highlight">$1</span>', $entity);
            $entity = '<span class="red">' . $entity . '</span>';
        } else {
            $entity = '<span class="red">' . array_pop($chunk) . '</span>';
        }
        // let's analyse the entity for the search string
        $chunk  = explode('/', $chunk[0]);
        $repo   = '<span class="green">' . array_shift($chunk) . '</span>';

        $path = implode('<span class="superset">&nbsp;&sup;&nbsp;</span>', $chunk);

        return $repo . '<span class="superset">&nbsp;&sup;&nbsp;</span>' . $path . '<br>' .$entity;
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
        function showhide(foobar) {
            if(foobar.innerHTML == "hide") {
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

        $content = str_replace('[', '<span class="dump">[', $content);
        $content = str_replace(']', ']</span>', $content);
        $content = str_replace(' => ', '<span class="dump-arrow">=></span>', $content);
        echo $content;
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
                echo '<td>' . Utils::formatEntity($val) . '</td>';
                echo '<td>' . $arr2[$val] . '</td>';
                echo '<td>' . str_replace(' ', '<span class="highlight-red"> </span>', $arr2[$key]) . '</td>';
                echo '<td>' . Utils::formatEntity($key) . '</td>';
            } else {
                echo '<td>' . $val . '</td>';
                echo '<td>' . $key . '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }

    /*
     * get the path in our hg repository for a string
     *
     */

    public static function pathFileInRepo($locale, $repo, $path)
    {

        $url = 'http://hg.mozilla.org';

        // remove entity from path and store it in a variable
        $path          = explode(':', $path);
        $path          = $path[0];
        $path          = explode('/', $path);
        $entity_file   = array_pop($path);
        $path          = implode('/', $path);
        $exploded_path = explode('/', $path);
        $base_folder   = $exploded_path[0];

        if ($repo == 'gaia' || $base_folder == 'apps') {
            $locale = ($locale == 'es-ES') ? 'es' : $locale;
            $url   .= '/gaia-l10n/' . $locale . '/file/default/';
            return $url . $path . '/' . $entity_file;
        }

        $en_US_Folder_Mess = array(
            'b2g/branding/official/',
            'b2g/branding/unofficial/',
            'b2g/',
            'netwerk/',
            'embedding/android/',
            'testing/extensions/community/chrome/',
            'intl/',
            'extensions/spellcheck/',
            'services/sync/',
            'mobile/android/branding/aurora/',
            'mobile/android/branding/official/',
            'mobile/android/branding/nightly/',
            'mobile/android/branding/unofficial/',
            'mobile/android/branding/beta/',
            'mobile/android/base/',
            'mobile/android/',
            'mobile/',
            'security/manager/',
            'toolkit/content/tests/fennec-tile-testapp/chrome/',
            'toolkit/',
            'browser/branding/aurora/',
            'browser/branding/official/',
            'browser/branding/nightly/',
            'browser/branding/unofficial/',
            'browser/',
            'layout/tools/layout-debug/ui/',
            'dom/',
            'webapprt/',
            'chat/',
            'suite/',
            'other-licenses/branding/sunbird/',
            'other-licenses/branding/thunderbird/',
            'mail/branding/aurora/',
            'mail/branding/nightly/',
            'mail/',
            'mail/test/resources/mozmill/mozmill/extension/',
            'editor/ui/',
            'calendar/sunbird/branding/nightly/',
            'calendar/',
        );


        // Destop repos
        if ($locale != 'en-US') {

            if ($repo == 'central') {
                $url .= '/l10n-central/' . $locale . '/file/default/';
            } else {
                $url .= '/releases/l10n/mozilla-aurora/' . $locale . '/file/default/';
            }

        } else {

            if (in_array(
                $base_folder,
                array('calendar', 'chat', 'editor', 'ldap', 'mail', 'mailnews', 'suite')
            )) {
                $repo_base = 'comm';
            } else {
                $repo_base = 'mozilla';
            }

            if ($repo == 'central') {
                $url .= "/${repo_base}-central/file/default/";
            } else {
                $url .= "/releases/${repo_base}-${repo}/file/default/";
            }


            $loop = true;
            while ($loop && count($exploded_path) > 0) {
                if (in_array(implode('/', $exploded_path) . '/', $en_US_Folder_Mess)) {
                    $path_part1 = implode('/', $exploded_path) . '/locales/en-US';
                    $pattern = preg_quote(implode('/', $exploded_path), '/');
                    $path = preg_replace('/' . $pattern . '/', $path_part1, $path, 1, $count);
                    $loop = false;
                    break;
                } else {
                    array_pop($exploded_path);
                }
            }

            if ($loop == true) {
                $path = explode('/', $path);
                $categorie = array_shift($path);
                $path = $categorie . '/locales/en-US/' . implode('/', $path);
            }
        }

        return $url . $path . '/' . $entity_file;
    }

    /*
     * Split a sentence in words from longest to shortest
     *
     * @param string $sentence
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
                return strlen($b) - strlen($a);
            }
        );
        return $words;
    }


    /*
     * Generate a list of <option> html tags from an array and mark one as selected
     *
     * @param       array   $options list of <option>
     * @param       string  $selected the option which should have the 'selected' html attribute
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
     * @param  string  $folder the directory we want to access
     * @param  array   $exclude files to exclude from results, by default . and ..
     * @return array
     */
    public static function getFilenamesInFolder($folder, $exclude = array('.', '..'))
    {
        // Get the locale list
        $file_list = scandir($folder);
        $file_list = array_diff($file_list, $exclude);
        return $file_list;
    }

    /*
     * Return a json/jsonp representation of data and exits;
     *
     * @param  array  data in json field
     * @param  string jsonp function name, default to false
     * @return json feed
     */
    public static function jsonOutput(array $data, $jsonp = false)
    {
        $json = json_encode($data);
        $mime = 'application/json';

        if ($jsonp && is_string($jsonp)) {
            $mime = 'application/javascript';
            $json = $jsonp . '(' . $json . ')';
        }

        ob_start();
        header("access-control-allow-origin: *");
        header("Content-type: {$mime}; charset=UTF-8");
        echo $json;
        $json = ob_get_contents();
        ob_end_clean();
        return $json;
    }

    /*
     * Return an array of strings from our repos
     */
    public static function getRepoStrings($locale, $repository)
    {
        $tmx = array();
        global $spanishes;

        if ($repository != 'gaia') {

            if ($locale == 'en-US') {
                // English
                include TMX . "{$repository}/{$locale}/cache_en-US.php";
            } else {
                // Localised, for a locale to locale comparison
                include TMX . "{$repository}/${locale}/cache_${locale}.php";
            }
        }

        // We have only one Spanish for Gaia
        if (in_array($locale, $spanishes)) {
            $gaialocale = 'es';
        } else {
            $gaialocale = $locale;
        }

        $file = TMX . 'gaia/' . $gaialocale . '/cache_' . $gaialocale . '.php';

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
        $str = Utils::mtrim($str);
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
        $origin_length = strlen(strip_tags($origin));
        $translated_length = strlen(strip_tags($translated));

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
     * Check if we have a cache file with the components (languages) and the cache file is not one week old. If not Connect to Bugzilla API and get components list
     *
     * @return $components_list
     */
    public static function getBugzillaComponents()
    {

        if (!file_exists('bugzilla_components.txt') || filemtime('bugzilla_components.txt')+ (7 * 24 * 60 * 60) < time() ) {
            $json_url = 'https://bugzilla.mozilla.org/jsonrpc.cgi?method=Product.get&params=[%20{%20%22names%22:%20[%22Mozilla%20Localizations%22]}%20]';
            file_put_contents('bugzilla_components.txt', file_get_contents($json_url));
        }

        $data = json_decode(file_get_contents('bugzilla_components.txt'), TRUE);
        $components_list = $data['result']['products'][0]['components'];
        
        return $components_list;
    }
    
    /*
     * Collect the correct language component for bugzilla URL
     *
     * @param $actual_lng string
     * @param $components_array array
     * @return $component_string
     */
    public static function collectLanguageComponent($actual_lng, $components_array)
    {
        $component_string = 'Other';
        foreach ($components_array as $component) {
            if (strpos($component['name'], $actual_lng) !== false) {
                $component_string = $component['name'];
                break;
            }
        }
        return $component_string;
    }

}
