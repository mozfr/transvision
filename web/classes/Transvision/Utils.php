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

    public static function  valid($var)
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

    public static function  secureText($var, $tablo = true)
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
     *  helper function to set checkboxes value
     */

    public static function  checkboxState($str, $extra = '')
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

    public static function  results($entities, $locale1_strings, $locale2_strings)
    {

        $search_results = array();

        foreach ($entities as $entity) {
            $search_results[$entity] = array($locale1_strings[$entity],
                                             $locale2_strings[$entity]);
        }

        return $search_results;
    }

    /*
     * Search results in a table
     */

    public static function  resultsTable($search_results, $recherche, $locale1, $locale2, $search_options)
    {
        $direction1 = RTLSupport::getDirection($locale1);
        $direction2 = RTLSupport::getDirection($locale2);

        $table  = "<table>
                      <tr>
                        <th>Entity</th>
                        <th>${locale1}</th>
                        <th>${locale2}</th>
                      </tr>";

        if (!$search_options['whole_word'] && !$search_options['perfect_match']) {
            $recherche = Utils::uniqueWords($recherche);
        } else {
            $recherche = array($recherche);
        }

        foreach ($search_results as $key => $strings) {

            $source_string = $strings[0];
            $target_string = $strings[1];
            foreach ($recherche as $val) {
                $source_string = Utils::markString($val, $source_string);
                $target_string = Utils::markString($val, $target_string);
            }

            $source_string = Utils::highlightString($source_string);
            $target_string = Utils::highlightString($target_string);

            // nbsp highlight
            $target_string = str_replace(
                ' ',
                '<span class="highlight-gray" title="Non breakable space"> </span>',
                $target_string
            );
            // thin space highlight
            $target_string = str_replace(
                ' ',
                '<span class="highlight-red" title="Thin space"> </span>',
                $target_string
            );

            // right ellipsis highlight
            $target_string = str_replace(
                '…',
                '<span class="highlight-gray">…</span>',
                $target_string
            );

            // right ellipsis highlight
            $target_string = str_replace(
                '&hellip;',
                '<span class="highlight-gray">…</span>',
                $target_string
            );

            $temp = explode('-', $locale1);
            $short_locale1 = $temp[0];

            $temp = explode('-', $locale2);
            $short_locale2 = $temp[0];

            $path_locale1 = Utils::pathFileInRepo($locale1, $search_options['repo'], $key);
            $path_locale2 = Utils::pathFileInRepo($locale2, $search_options['repo'], $key);

            $table .= "
                <tr>
                  <td>" . Utils::formatEntity($key, $recherche[0]) . "</td>

                  <td dir='${direction1}'>
                    <div class='string'>
                      <a href='http://translate.google.com/#${short_locale1}/${short_locale2}/"
                      . urlencode(strip_tags($source_string))
                      . "'>${source_string}</a>
                     </div>
                     <div class='sourcelink'><a href='${path_locale1}'><em>&lt;source&gt;</em></a></div>
                   </td>

                   <td dir='${direction2}'>
                      <div class='string'>${target_string} </div>
                      <div class='sourcelink'><a href='${path_locale2}'><em>&lt;source&gt;</em></a></div>
                   </td>
                </tr>";
        }

        $table .= "  </table>";
        return $table;
    }

    public static function  markString($needle, $haystack)
    {
        $str = str_replace($needle, '←' . $needle . '→', $haystack);
        $str = str_replace($needle, '←' . $needle . '→', $haystack);
        $str = str_replace(ucwords($needle), '←' . ucwords($needle) . '→', $str);
        $str = str_replace(strtolower($needle), '←' . strtolower($needle) . '→', $str);
        return $str;
    }

    public static function  highlightString($str)
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
        return $str;
    }



    /*
     * make an entity look nice in tables
     *
     */

    public static function formatEntity($entity, $highlight=false)
    {
        // let's analyse the entity for the search string
        $chunk  = explode(':', $entity);

        if($highlight) {
            $entity = array_pop($chunk);
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
        list($usec, $sec) = explode (' ', microtime());
        return ((float) $usec + (float) $sec);
    }

    /**
     * nicer var_dump()
     */
    public static function  dump($var)
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


    public static function printSimpleTable($arr, $arr2 = false, $titles=array('Column1', 'Column2', 'Column3', 'Column4') )
    {
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
                echo '<td>' . formatEntity($val) . '</td>';
                echo '<td>' . $arr2[$val] . '</td>';
                echo '<td>' . str_replace(' ', '<span class="highlight-red"> </span>', $arr2[$key]) . '</td>';
                echo '<td>' . formatEntity($key) . '</td>';
            } else {
                echo '<td>' . $val . '</td>';
                echo '<td>' . $key . '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }


    public static function getRepoStrings($locale, $repo)
    {
        $tmx = array();
        include TMX . $repo . '/' . $locale . '/cache_' . $locale . '.php';
        return $tmx;
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
            'mobile/xul/branding/aurora/',
            'mobile/xul/branding/official/',
            'mobile/xul/branding/nightly/',
            'mobile/xul/branding/unofficial/',
            'mobile/xul/branding/beta/',
            'mobile/xul/',
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

            if (in_array($base_folder,
                array('calendar', 'chat', 'editor', 'ldap',
                        'mail', 'mailnews', 'suite'))) {
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
     * Split a sentence in words
     *
     * @param string $sentence
     * @return array
     */
    public static function uniqueWords($sentence) {
        $words = explode(' ', $sentence);
        $words = array_filter($words); // filter out extra spaces
        $words = array_unique($words); // remove duplicate words
        return $words;
    }
}
