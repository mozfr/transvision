<?php
namespace Transvision;

/**
 * AnalyseStrings class
 *
 * This class is for all the methods we use to analyse strings
 *
 * @package Transvision
 */
class AnalyseStrings
{
    /**
     * Replace common and uncommon html entities by real letters
     *
     * @param string $string the string to process
     * @return string cleaned up string with entities converted
     */
    public static function cleanUpEntities($string)
    {
        $replace = [
            '&#037;'    => '%',
            '&amp;'     => '&',
            '&apos;'    => "'",
            '&percnt;'  => '%',
        ];
        $string = str_replace(array_keys($replace), array_values($replace), $string);
        $string = html_entity_decode($string, ENT_QUOTES);

        return $string;
    }

    /**
     * Search for strings with variables differences
     * @param array $tmx_source TMX file as reference
     * @param array $tmx_target TMX file for the locale to compare
     * @param array  $patterns  list of regex patterns for the search
     *               Pattern examples:
     *               '/&([a-z0-9\.]+);/i'      -> &brandShortName;
     *               '/\{\{([a-z0-9]+)\}\}/i'  -> {{brandShortName}}
     * @return array List of entity names not matching source
     */
    public static function differences($tmx_source, $tmx_target, $patterns)
    {
        $pattern_mismatch = [];

        if (!is_array($patterns)) {
            $patterns = (array) $patterns;
        }

        foreach ($patterns as $pattern) {
            foreach ($tmx_source as $key => $value) {
                preg_match_all($pattern, $value, $matches);
                if (count($matches[0]) > 0) {
                    foreach ($matches[0] as $val) {
                        if (isset($tmx_target[$key])
                            && $tmx_target[$key] != ''
                            && strpos(str_replace(' ' , '', $tmx_target[$key]),
                                      str_replace(' ' , '', $val)) === false )
                        {
                            $pattern_mismatch[] = $key;
                        }
                    }
                }
            }
        }

        return $pattern_mismatch;
    }
}
