<?php
// Page title
$title = 'Transvision glossary <a href="/news/#v' . VERSION . '">' . VERSION . '</a>';
require_once WEBROOT .'classes/ShowResults.class.php';
require_once WEBROOT .'inc/l10n-init.php';

function getRepoStrings($locale, $repo) {
    $tmx = array();
    include TMX . $repo . '/' . $locale . '/cache_' . $locale . '.php';
    return $tmx;
}

// let's add en-US to check their errors too
$allLocales[] = 'en-US';

$repos = array('central', 'aurora', 'beta', 'release');
$repo = 'central';

if (isset($_GET['channel']) && in_array($_GET['channel'], $repos)) {
    $repo = $_GET['channel'];
}

if (isset($_GET['locale']) && in_array($_GET['locale'], $allLocales)) {
    $locale = $_GET['locale'];
}

$strings[$repo] = getRepoStrings($locale, $repo);
$stringsEnglish[$repo] = getRepoStrings('en-US', $repo);

$channel_selector = '';

foreach ($repos as $val) {
    $ch = ($val == $repo) ? ' selected' : '';
    $channel_selector .= "\t<option" . $ch . " value=" . $val . ">" . $val . "</option>\n";
}

// Get the locale list
$loc_list = scandir(TMX . $repo . '/');
$loc_list = array_diff($loc_list, array('.', '..'));
$spanish  = array_search('es', $loc_list);

if ($spanish) {
    $loc_list[$spanish] = 'es-ES';
}

// build the target locale switcher
$target_locales_list = '';

foreach ($loc_list as $loc) {
    $ch = ($loc == $locale) ? ' selected' : '';
    $target_locales_list .= "\t<option" . $ch . " value=" . $loc . ">" . $loc . "</option>\n";
}

?>

<form name="searchform" method="get" action="">
    <fieldset id="main">
        <fieldset>
            <legend>Locale</legend>
            <select name='locale'>
            <?=$target_locales_list?>
            </select>
        </fieldset>
        <fieldset>
            <legend>Channel</legend>
            <select name='channel'>
            <?=$channel_selector?>
            </select>
        </fieldset>
        <input type="submit" value="Go" alt="Go" />
    </fieldset>
</form>

<?php

$akeys = array_filter(
            array_keys($strings[$repo]),
            function ($entity) {
                return substr($entity, -9) == 'accesskey';
            }
);

$ak_labels  = array('.label', '.title', '.title2');
$ak_results = array();

foreach($akeys as $akey) {

    $entity         = substr($akey, 0, -10);
    $akey_value     = $strings[$repo][$akey];

    foreach ($ak_labels as $ak_label) {
        if ( isset($strings[$repo][$entity . $ak_label])
                    && $strings[$repo][$entity . $ak_label] != ''
                    && $stringsEnglish[$repo][$akey] != '') {
            if(stripos($strings[$repo][$entity . $ak_label], $akey_value) === false) {
                $ak_results[$akey] = $entity . $ak_label;
            } else {
                break;
            }
        }
    }
}


function spit2ColTable($arr, $arr2 = false) {
    echo '<table>
          <tr>
          <th>Column1</th><th>Column2</th>';

    if($arr2) {
        echo '<th>Column3</th><th>Column4</th>';
    }

    echo '</tr>';

    foreach ($arr as $key => $val) {
        echo '<tr>';
        if($arr2) {
            echo '<td>' . $val . '</td>';
            echo '<td>' . $arr2[$val] . '</td>';
            echo '<td>' . $arr2[$key] . '</td>';
            echo '<td>' . $key . '</td>';
        } else {
            echo '<td>' . $val . '</td>';
            echo '<td>' . $key . '</td>';
        }
        echo '</tr>';
    }
    echo '</table>';
}


//~ dump($ak_results);
echo '<h2>' . count($ak_results) . ' potential errors</h2>';
spit2ColTable($ak_results, $strings[$repo]);
