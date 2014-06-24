<?php
namespace Transvision;

/**
 * TMX class
 *
 * Methods to create and manage TMX files
 *
 * @package Transvision
 */
class TMX
{
    /**
     * Generate a TMX file from a data source
     *
     * @param array $strings All the strings the user picked
     * @param string $target_lang Locale picked from which we get translations
     * @param string $source_lang Source locale
     */
    public static function create($strings, $target_lang, $source_lang) {
        if ($strings[$target_lang] && $strings[$source_lang]) {
            $content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
                        . '<tmx version="1.4">' . "\n"
                        . '<header o-tmf="plain text" o-encoding="UTF8" adminlang="en"'
                        . ' creationdate="' . date('c') . '" creationtoolversion="0.1" creationtool="Transvision"'
                        . ' srclang="' . $source_lang . '" segtype="sentence" datatype="plaintext">' . "\n"
                        . '</header>' . "\n"
                        . '<body>' . "\n";

            foreach ($strings[$source_lang] as $entity => $string_source) {
                $string_target = isset($strings[$target_lang][$entity]) ? $strings[$target_lang][$entity] : '';

                $string_source = htmlentities($string_source, ENT_XML1);
                $string_target = htmlentities($string_target, ENT_XML1);

                $content .= "\t".'<tu tuid="' . $entity . '" srclang="' . $source_lang . '">' . "\n"
                            . "\t\t" . '<tuv xml:lang="' . $source_lang . '"><seg>' . $string_source . '</seg></tuv>' . "\n"
                            . "\t\t" . '<tuv xml:lang="' . $target_lang . '"><seg>' . $string_target . '</seg></tuv>' . "\n"
                            . "\t" . '</tu>' . "\n";
            }
            $content .= "</body>\n</tmx>\n";

            return $content;
        }

        return false;
    }
}
