<?php

/*
 * Check if a variable exists and is not set to false
 * Useful to check variables in $_GET for example
 * returns true/false
 */

function valid($var)
{
    if (isset($var) && $var != false) {
        return true;
    } else {
        return false;
    }
}

/*
 * Function sanitizing a string or an array of strings.
 * Returns a string or an array, depending on the input
 */

function secureText($var, $tablo = true)
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

function checkboxState($str, $disabled='')
{
    if ($str == 't2t') {
        return ($str) ? ' checked="checked"' : '';
    }

    if (isset($_GET['t2t'])) {
        return ' disabled="disabled"';
    } else {
        return ($str) ? ' checked="checked"' : '';
    }
}

/*
 * Create an array for search results with this format:
 * 'entity' => ['locale 1', 'locale 2']
 */

function results($entities, $locale1_strings, $locale2_strings)
{

    $search_results = array();

    foreach ($entities as $entity) {
        $search_results[$entity] = array($locale1_strings[$entity], $locale2_strings[$entity]);
    }

    return $search_results;
}

/*
 * Search results in a table
 */

function resultsTable($search_results, $recherche, $locale1, $locale2, $l10n_repo, $search_options)
{

    // rtl support
    $direction1 = tinyl10n\RTL::getDirection($locale1);
    $direction2 = tinyl10n\RTL::getDirection($locale2);

    // mxr support
    $prefix = ($search_options['repo'] == 'central') ? $search_options['repo'] : 'mozilla-' . $search_options['repo'];
    if ($l10n_repo) {
        $mxr_url = "http://mxr.mozilla.org/l10n-$prefix/search?find=$locale2/";
        $mxr_field_limit = 28 - mb_strwidth("$locale2/");
    } else {
        $mxr_url  = "http://mxr.mozilla.org/comm-${search_options['repo']}/search?find=";
        $mxr_field_limit = 27;
    }

    $table  = "\n\n  <table>\n\n";
    $table .= "    <tr>\n";
    $table .= "      <th>Entity</th>\n";
    $table .= "      <th>" . $locale1 . "</th>\n";
    $table .= "      <th>" . $locale2 . "</th>\n";
    $table .= "    </tr>\n\n";

    foreach ($search_results as $key => $strings) {
        // let's analyse the entity for the search string
        $search = explode(':', $key);

        if ($search[0] == 'apps') {
            $mxr_link = formatEntity($key);
        } else {
            // we chop search strings with mb_strimwidth() because  of field length limits in mxr)
            $search = mb_strimwidth($search[0] . '.*' . $search[1], 0, $mxr_field_limit) . '&amp;string=' . mb_strimwidth($search[2], 0, 29);
            $mxr_link = '<a href="' . $mxr_url . $search . '">' . formatEntity($key) . '</a>';
        }

        $source_string = str_replace($recherche, '<span class="red">'  . $recherche . '</span>', $strings[0]);
        $source_string = str_replace(ucwords($recherche), '<span class="red">'  . ucwords($recherche) . '</span>', $source_string);
        $source_string = str_replace(strtolower($recherche), '<span class="red">'  . strtolower($recherche) . '</span>', $source_string);

        $target_string = str_replace($recherche, '<span class="red">'  . $recherche . '</span>', $strings[1]);
        $target_string = str_replace(ucwords($recherche), '<span class="red">'  . ucwords($recherche) . '</span>', $target_string);
        $target_string = str_replace(strtolower($recherche), '<span class="red">'  . strtolower($recherche) . '</span>', $target_string);

        $target_string = str_replace(' ', '<span class="highlight-gray"  title="Non breakable space"> </span>', $target_string); // nbsp highlight
        $target_string = str_replace(' ', '<span class="highlight-red" title="Thin space"> </span>', $target_string); // thin space highlight

        $target_string = str_replace('…', '<span class="highlight-gray">…</span>', $target_string); // right ellipsis highlight
        $target_string = str_replace('&hellip;', '<span class="highlight-gray">…</span>', $target_string); // right ellipsis highlight

        $temp = explode('-', $locale1);
        $short_locale1 = $temp[0];

        $temp = explode('-', $locale2);
        $short_locale2 = $temp[0];

        $table .= "    <tr>\n";
        $table .= "      <td>" . $mxr_link . "</a></td>\n";
        $table .= "      <td dir='" . $direction1. "'><a href='http://translate.google.com/#$short_locale1/$short_locale2/" . urlencode(strip_tags($source_string)) ."'>". $source_string . "</a></td>\n";
        $table .= "      <td dir='" . $direction2. "'>" . $target_string . "</td>\n";
        $table .= "    </tr>\n\n";
    }

    $table .= "  </table>\n\n";
    return $table;
}

/*
 * make an entity look nice in tables
 *
 */

function formatEntity($entity)
{
    // let's analyse the entity for the search string
    $chunk = explode(':', $entity);
    // let's format the entity key to look better
    $chunk[0] = '<span class="green">' . $chunk[0] . '</span>';
    $chunk[1] = '<span class="blue">' .  $chunk[1] . '</span>';
    $chunk[2] = '<span class="red">' .   $chunk[2] . '</span>';
    $entity = implode('<span class="superset">&nbsp;&sup;&nbsp;</span>', $chunk);
    return $entity;
}

/**
 * get the current microtime for perf measurements
 *
 */

function getmicrotime()
{
    list($usec, $sec) = explode (' ', microtime());
    return ((float)$usec + (float)$sec);
}

/**
 * nicer var_dump()
 */
function dump($var)
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


function printSimpleTable($arr, $arr2 = false, $titles=array('Column1', 'Column2', 'Column3', 'Column4') ) {
    echo "<table>
          <tr>
          <th>{$titles[0]}</th><th>{$titles[1]}</th>";

    if($arr2) {
        echo "<th>{$titles[2]}</th><th>{$titles[3]}</th>";
    }

    echo '</tr>';

    foreach ($arr as $key => $val) {
        echo '<tr>';
        if($arr2) {
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


function getRepoStrings($locale, $repo) {
    $tmx = array();
    include TMX . $repo . '/' . $locale . '/cache_' . $locale . '.php';
    return $tmx;
}
