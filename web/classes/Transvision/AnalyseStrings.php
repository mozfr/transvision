<?php

namespace Transvision;

class AnalyseStrings
{
    /*
     * Replace common and uncommon html entities by real characters
     *
     * @param $string
     *
     * @return string
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

    /*
     * Search for strings with differences of variables into it
     *
     * @param $source TMX file as reference
     * @param $target TMX file for the locales to compare
     * @param $patterns array with regex patterns for the search
     *
     * @return array List of entity names
     */
    public static function differences($source, $target, $patterns)
    {
        /*
         * Pattern examples:
         * '/&([a-z0-9\.]+);/i'      -> &brandShortName;
         * '/\{\{([a-z0-9]+)\}\}/i'  -> {{brandShortName}}
         */
        $pattern_mismatch = [];

        if (!is_array($patterns)) {
            $patterns = (array) $patterns;
        }

        foreach ($patterns as $pattern) {
            foreach ($source as $key => $value) {
                preg_match_all($pattern, $value, $matches);
                if (count($matches[0]) > 0) {
                    foreach ($matches[0] as $val) {
                        if (isset($target[$key])
                            && $target[$key] != ''
                            && strpos(str_replace(' ' , '', $target[$key]),
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
