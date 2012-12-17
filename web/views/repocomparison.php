<?php
$locale = 'fr';
$repo = 'release';
// Page title
$title = 'Transvision glossary <a href="./news/#v' . VERSION . '">' . VERSION . '</a>';

include TMX . $repo . '/en-US/cache_en-US.php';
$tmx_source = $tmx;
unset($tmx);


echo "Repo: $repo<br>";
echo count($tmx_source) . " strings in en-US<br>";

$locales = array(
'ach', 'af', 'ak', 'an', 'ar', 'as', 'ast', 'be', 'bg', 'bn-IN', 'bn-BD', 'br', 'bs', 'ca', 'cs', 'csb', 'cy', 'da', 'de', 'el', 'eo', 'es-AR', 'es-ES', 'es-CL', 'es-MX', 'et', 'eu', 'fa', 'ff', 'fi', 'fr', 'fy-NL', 'ga-IE', 'gd', 'gl', 'gu-IN', 'he', 'hi-IN', 'hr', 'hu', 'hy-AM', 'id', 'is', 'it', 'ja', 'ka', 'kk', 'kn', 'km', 'ko', 'ku', 'lg', 'lij', 'lt', 'lv', 'mai', 'mk', 'ml', 'mn', 'mr', 'ms', 'my', 'nb-NO', 'nl', 'nn-NO', 'nso', 'oc', 'or', 'pa-IN', 'pl', 'pt-BR', 'pt-PT', 'rm', 'ro', 'ru', 'si', 'sk', 'sl', 'son', 'sq', 'sr', 'sv-SE', 'sw', 'ta', 'ta-LK', 'te', 'th', 'tr', 'uk', 'ur', 'vi', 'wo', 'zh-CN', 'zh-TW', 'zu',
);

foreach ($locales as $locale) {
    include TMX . $repo . '/' . $locale . '/cache_' . $locale . '.php'; // localised
    $tmx_target = $tmx;
    unset($tmx);
    $result = count(array_diff_key($tmx_source, $tmx_target));
    echo count($tmx_target) . " strings in $locale, $result missing<br>";
}
//~ $result = count(array_diff_key($tmx_target, $tmx_source));
//~ print_r($result);
//~ echo '<pre>';
//~ var_dump(array_diff_key($tmx_source, $tmx_target));
