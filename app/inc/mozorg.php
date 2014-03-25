<?php
namespace Transvision;

// Script should not be called from the Web
if (php_sapi_name() != 'cli') {
    die('Nope');
}

include __DIR__ . '/init.php';

error_log('Mozilla.org: extraction of strings');

foreach (Files::getFilenamesInFolder( SVN . 'mozilla_org/') as $locale) {
    $path = SVN . "mozilla_org/{$locale}/";
    $mozilla_org_files = Dotlang::getLangFilesList($path);

    $mozilla_org_files = array_map(
        function($item) use ($path) {
            return str_replace($path, '', $item);
        },
        $mozilla_org_files);

    $out_english = $out_translation = '';
    $total_strings = 0;

    foreach ($mozilla_org_files as $file) {
        $strings = Dotlang::getStrings(SVN . "mozilla_org/{$locale}/{$file}");
        $total_strings += count($strings);

        foreach ($strings as $english => $translation) {
            $output = function($str1, $str2) use ($file, $english, $translation)
            {
                $array_line =
                    "'"
                   . Dotlang::generateStringID('mozilla_org/' . $file, $str1)
                   . "' => '"
                   . Utils::secureText($str2)
                   . "',"
                   . "\n";

                return $array_line;
            };

            $out_english     .= $output($english, $english);
            $out_translation .= $output($english, $translation);
        }
    }

    $out_english     = "<?php\n\$tmx = [\n" . $out_english .  "];\n";
    $out_translation = "<?php\n\$tmx = [\n" . $out_translation .  "];\n";

    Files::fileForceContents(TMX . "mozilla_org/{$locale}/cache_{$locale}.php", $out_translation);
    Files::fileForceContents(TMX . "mozilla_org/{$locale}/cache_en-GB.php", $out_english);
    error_log("{$locale}: {$total_strings} strings");
}

