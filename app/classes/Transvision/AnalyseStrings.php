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
     * @param  string $string the string to process
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
     * @param  array  $tmx_source TMX file as reference
     * @param  array  $tmx_target TMX file for the locale to compare
     * @param  string $repo       Name of the repo to determine the right set of regexps
     * @return array  List of entity names not matching source
     */
    public static function differences($tmx_source, $tmx_target, $repo)
    {
        $pattern_mismatch = [];

        if (Strings::startsWith($repo, 'gaia')) {
            $patterns = [
                'l10njs'     => '/\{\{([\s]*[a-z0-9]+[\s]*)\}\}/i', // {{foobar2}}
            ];
        } else {
            $patterns = [
                'dtd'        => '/&([a-z0-9\.]+);/i',                // &foobar;
                'printf'     => '/%([0-9]+\$){0,1}([0-9].){0,1}S/i', // %1$S or %S. %1$0.S and %0.S are valid too
                'properties' => '/(?<!%[0-9])\$[a-z0-9\.]+\b/i',      // $BrandShortName, but not "My%1$SFeeds-%2$S.opml"
            ];
        }

        foreach ($patterns as $pattern_name => $pattern) {
            foreach ($tmx_source as $key => $value) {
                if (isset($tmx_target[$key])
                    && $tmx_target[$key] != '') {
                    //Check variables only if the translation exist
                    $translation = $tmx_target[$key];
                    $wrong_variable = false;
                    preg_match_all($pattern, $value, $matches_source);
                    if (count($matches_source[0]) > 0) {
                        foreach ($matches_source[0] as $val) {
                            if ($pattern_name == 'printf') {
                                // Variables are in format %S or %1$S, case is not relevant
                                if (stripos($translation, $val) === false) {
                                    $wrong_variable = true;
                                }
                            } else {
                                if (strpos($translation, $val) === false) {
                                    $wrong_variable = true;
                                }
                            }
                        }
                    }

                    if ($pattern_name == 'printf') {
                        preg_match_all('/%([0-9]+\$){1}([0-9].){0,1}S/i', $translation, $matches_ordered);
                        preg_match_all('/%(0.){0,1}S/i', $translation, $matches_unordered);

                        if ((count($matches_ordered[0]) > 0) &&
                            (count($matches_unordered[0]) > 0)) {
                            // A string can't have both ordered and unordered variables
                            $wrong_variable = true;
                        } elseif (count($matches_ordered[0]) + count($matches_unordered[0]) == count($matches_source[0])) {
                            /* I have the same number of variables in source and translation,
                             * I consider the string valid.
                             */
                            $wrong_variable = false;
                        }
                    }

                    if ($wrong_variable) {
                        $pattern_mismatch[] = $key;
                    }
                }
            }
        }

        return $pattern_mismatch;
    }
}
