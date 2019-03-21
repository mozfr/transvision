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
     *
     * @return string cleaned up string with entities converted
     */
    public static function cleanUpEntities($string)
    {
        $replace = [
            '&#037;'   => '%',
            '&amp;'    => '&',
            '&apos;'   => "'",
            '&percnt;' => '%',
        ];
        $string = str_replace(array_keys($replace), array_values($replace), $string);
        $string = html_entity_decode($string, ENT_QUOTES);

        return $string;
    }

    /**
     * Search for strings with variables differences
     *
     * @param array  $tmx_source      TMX file as reference
     * @param array  $tmx_target      TMX file for the locale to compare
     * @param string $repo            Name of the repo to determine the right set of regexps
     * @param array  $ignored_strings Optional list of ignored strings, default empty
     *
     * @return array List of entity names not matching source
     */
    public static function differences($tmx_source, $tmx_target, $repo, $ignored_strings = [])
    {
        $pattern_mismatch = [];

        $patterns = [
            // &foobar;
            'dtd'        => '/&([A-Za-z0-9\.]+);/',
            // { $foo }, { foo }, { -foo }, { -foo[bar] }, { -foo(attr: "value") } Used in FTL files
            'ftl'        => '/(?<!\{)\{\s*([\$|-]?[A-Za-z0-9_-]+)(?:[\[(]?[A-Za-z0-9_\- :"]+[\])])*\s*\}/u',
            // %@, but also %1$@, %2$@, etc.
            'ios'        => '/(%(?:[0-9]+\$){0,1}@)/i',
            // {{foobar2}} Used in Loop and PDFViewer
            'l10njs'     => '/\{\{\s*([A-Za-z0-9_]+)\s*\}\}/u',
            // %1$S or %S. %1$0.S and %0.S are valid too
            'printf'     => '/(%(?:[0-9]+\$){0,1}(?:[0-9].){0,1}([sS]))/',
            // $BrandShortName, but not "My%1$SFeeds-%2$S.opml" or "{ $brandShortName }"
            'properties' => '/(?<!%[0-9]|\{\s)(\$[A-Za-z0-9\.]+)\b/',
        ];
        $repo_patterns = Project::$repos_info[$repo]['variable_patterns'];

        $patterns = array_filter($patterns, function($k) use ($repo_patterns) {
            return in_array($k, $repo_patterns);
        }, ARRAY_FILTER_USE_KEY);

        foreach ($patterns as $pattern_name => $pattern) {
            foreach ($tmx_source as $key => $source) {
                if (isset($tmx_target[$key])
                    && $tmx_target[$key] != ''
                    && ! in_array($key, $ignored_strings)) {
                    /*
                        Check variables only if the translation exists and
                        the string is not in the list of strings to ignore.
                    */
                    $translation = $tmx_target[$key];
                    $wrong_variable = false;

                    preg_match_all($pattern, $source, $matches_source);
                    preg_match_all($pattern, $translation, $matches_translation);

                    if (count($matches_source[1]) > 0) {
                        foreach ($matches_source[1] as $var_source) {
                            if (! in_array($var_source, $matches_translation[1])) {
                                $wrong_variable = true;
                            }

                            // For l10n.js {{n}} == {{ n }}
                            if ($pattern_name == 'l10njs' && $wrong_variable) {
                                // Trim whitespaces and sort variables alphabetically
                                $trimmed_source_vars = array_map('trim', $matches_source[1]);
                                sort($trimmed_source_vars);
                                $trimmed_source_trans = array_map('trim', $matches_translation[1]);
                                sort($trimmed_source_trans);

                                if ($trimmed_source_vars === $trimmed_source_trans) {
                                    $wrong_variable = false;
                                }
                            }
                        }
                    }

                    if ($pattern_name == 'printf') {
                        /*
                            Check ordered vs unordered variables. The pattern
                            regular expression returns "S" or "s" as second group
                            for each variable, I can use it to check for case
                            differences ("s" vs "S") which are not allowed.
                        */
                        preg_match_all('/%(?:[0-9]+\$){1}(?:[0-9].){0,1}S/i', $translation, $matches_ordered_trans);
                        preg_match_all('/%(?:0.){0,1}S/i', $translation, $matches_unordered_trans);

                        if (count($matches_ordered_trans[0]) > 0 && count($matches_unordered_trans[0]) > 0) {
                            // Strings can't mix ordered and unordered variables
                            $wrong_variable = true;
                        } else {
                            if (count($matches_translation[0]) == count($matches_source[0]) &&
                                $matches_translation[2] === $matches_source[2]) {
                                // Same number of variables and case, I assume the string is OK
                                $wrong_variable = false;
                            }
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
