<?php
$time_start = getmicrotime();

$locales = array('ach', 'af', 'ak', 'ar', 'as', 'ast', 'be', 'bg', 'bn-BD', 'bn-IN', 'br', 'bs', 'ca', 'cs', 'csb', 'cy', 'da', 'de', 'el', 'en-GB', 'en-US', 'en-ZA', 'eo', 'es-AR', 'es-CL', 'es-ES', 'es-MX', 'et', 'eu', 'fa', 'ff', 'fi', 'fr', 'fy-NL', 'ga-IE', 'gd', 'gl', 'gu-IN', 'he', 'hi-IN', 'hr', 'hu', 'hy-AM', 'id', 'is', 'it', 'ja', 'ka', 'kk', 'km', 'kn', 'ko', 'ku', 'lg', 'lij', 'lt', 'lv', 'mai', 'mk', 'ml', 'mr', 'ms', 'my', 'nb-NO', 'ne-NP', 'nl', 'nn-NO', 'nr', 'nso', 'or', 'pa-IN', 'pl', 'pt-BR', 'pt-PT', 'rm', 'ro', 'ru', 'rw', 'si', 'sk', 'sl', 'son', 'sq', 'sr', 'ss', 'st', 'sv-SE', 'sw', 'ta', 'ta-LK', 'te', 'th', 'tn', 'tr', 'ts', 'uk', 've', 'vi', 'wo', 'xh', 'zh-CN', 'zh-TW', 'zu');
//~ $latin_locales = array('ast', 'ca', 'fr',  'es-AR', 'es-CL', 'es-ES', 'es-MX', 'it', 'lij', 'pt-BR', 'pt-PT', 'rm', 'ro',);

//~ $viking_locales = array('da', 'is', 'nb-NO', 'nn-NO', 'sv-SE');
//~ $viking_locales = array('fr');

//~ $locales = $latin_locales;
//~ $locales = $viking_locales;
$ratio = array();

include TMX . $check['repo'] . '/en-US/cache_en-US.php';
$tmx_source = $tmx;

unset($tmx);

$total_source = 0;

foreach($tmx_source as $string) {
    $total_source += mb_strlen($string, 'UTF-8');
}
echo '<table>';
foreach($locales as $locale) {

    include TMX . $check['repo'] . '/' . $locale . '/cache_en-US.php'; // localised, for a locale to locale comparizon

    $tmx_source = $tmx;

    unset($tmx);

    $total_source = 0;

    foreach($tmx_source as $key => $string) {
        $total_source += mb_strlen($string, 'UTF-8');
        $tmp = explode(':', $key);
        $entity['en-US'][$tmp[0]]['count'] += 1;
        $entity['en-US'][$tmp[0]]['total_length'] += mb_strlen($string, 'UTF-8');
    }

    $target = TMX . $check['repo'] . '/' . $locale . '/cache_' . $locale . '.php';

    if(!is_file($target)) {
        echo $target . '<br>';
        continue;
    }

    include $target; // localised
    $tmx_target = $tmx;
    unset($tmx);

    $total_target = 0;

    foreach($tmx_target as $key => $string) {
        $total_target += mb_strlen($string, 'UTF-8');
        $tmp = explode(':', $key);
        $entity[$locale][$tmp[0]]['count'] += 1;
        $entity[$locale][$tmp[0]]['total_length'] += mb_strlen($string, 'UTF-8');
    }

    $ratio[$locale] = $total_target/$total_source;
    asort($ratio);

    $total = number_format($ratio[$locale], 2) * 100 . '%';

    //~ echo '<tr><td></td><th>Total</th>';
    //~ foreach ($entity['en-US'] as $key => $val) {
        //~ echo "<th>$key</th>";
    //~ }
    echo '</tr>';

    echo '<tr>';
    echo '<th>' . $locale . '</th><td>' . $total . '</td>';
    //~ foreach ($entity['en-US'] as $key => $val) {
        //~ $v = number_format($entity[$locale][$key]['total_length']/$val['total_length'], 2) * 100 . '%';
        //~ echo "<td>$v</td>";
    //~ }
    echo '</tr>';

    unset($entity);
}
echo '</table>';
