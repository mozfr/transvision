<?php
namespace Transvision;

$title = '<a href="/" id="transvision-title">Transvision glossary</a>
          <a href="/news/#v' . VERSION . '">' . VERSION . '</a>';

require_once WEBROOT . 'inc/l10n-init.php';

// let's add en-US to check their errors too
$allLocales[] = 'en-US';

$repo  = 'central';

if (isset($_GET['channel']) && in_array($_GET['channel'], $desktop_repos)) {
    $repo = $_GET['channel'];
}

if (isset($_GET['locale']) && in_array($_GET['locale'], $allLocales)) {
    $locale = $_GET['locale'];
}

$strings[$repo]        = Utils::getRepoStrings($locale, $repo);
$stringsEnglish[$repo] = Utils::getRepoStrings('en-US', $repo);

$channel_selector = Utils::getHtmlSelectOptions($desktop_repos, $repo);

// Get the locale list
$loc_list = Utils::getFilenamesInFolder(TMX . $repo . '/');

// Gaia hack
$spanish  = array_search('es', $loc_list);
if ($spanish) {
    $loc_list[$spanish] = 'es-ES';
}

// build the target locale switcher
$target_locales_list = Utils::getHtmlSelectOptions($loc_list, $locale);

$akeys = array_filter(
    array_keys($strings[$repo]),
    function ($entity) {
        return substr($entity, -9) == 'accesskey';
    }
);

$ak_labels  = array('.label', '.title', '.title2');
$ak_results = array();

foreach ($akeys as $akey) {

    $entity     = substr($akey, 0, -10);
    $akey_value = $strings[$repo][$akey];

    foreach ($ak_labels as $ak_label) {
        if ( isset($strings[$repo][$entity . $ak_label])
                    && $strings[$repo][$entity . $ak_label] != ''
                    && $stringsEnglish[$repo][$akey] != '') {
            if ($akey_value == '') {
                $ak_results[$akey] = $entity . $ak_label;
            } elseif (mb_stripos($strings[$repo][$entity . $ak_label], $akey_value) === false) {
                $ak_results[$akey] = $entity . $ak_label;
            } else {
                break;
            }
        }
    }
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
echo '<h2>' . count($ak_results) . ' potential accesskey errors</h2>';
Utils::printSimpleTable(
    $ak_results,
    $strings[$repo],
    array('Label entity', 'Label value', 'Access&nbsp;key', 'Access key entity')
);
