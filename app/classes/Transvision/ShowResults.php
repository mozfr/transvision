<?php
namespace Transvision;

/**
 * ShowResults class
 *
 * This class is used to get search results and display them
 *
 * @package Transvision
 */
class ShowResults
{
    /**
     * Create an array for search results with this format:
     * 'entity' => ['string_locale 1', 'string_locale 2']
     * @param  array $entities      Haystack of entities to search in
     * @param  array $array_strings The strings to look into [locale1 strings, locale2 strings]
     * @return array Results with entities as keys and translations as values
     */
    public static function getTMXResults($entities, $array_strings)
    {
        $search_results = [];

        foreach ($entities as $entity) {
            $temp = [];

            foreach ($array_strings as $strings) {
                $temp[] = self::getStringFromEntity($entity, $strings);
            }

            $search_results[$entity] = $temp;
        }

        return $search_results;
    }

    /**
     * Return an array of suggestions from our Translation Memory API. Unlike
     * other services, we return values from both source and target languages.
     *
     * @param array  $source_strings Matches from source strings
     * @param array  $target_strings Matches from target strings
     * @param string $search         The string to search for
     * @param int    $max_results    Optional, default to 10, the max number of results we return
     *
     * @return array An array of strings
     */
    public static function getSuggestionsResults($source_strings, $target_strings, $search, $max_results = 10)
    {
        // Only interested in unique strings (values, not IDs).
        $data = [
            'source' => array_unique(array_values($source_strings)),
            'target' => array_unique(array_values($target_strings)),
        ];
        $output = [
            'source' => [],
            'target' => [],
        ];
        $flat_output = [];

        // Reset $max_results as 10 if it's currently set to 0 (default value when the parameter is not specified in the API request).
        $max_results = $max_results > 0 ? $max_results : 10;

        // Assign quality to each string in each group (source, target)
        foreach ($data as $group => $group_strings) {
            foreach ($group_strings as $single_string) {
                $quality = round(Strings::levenshteinQuality($search, $single_string), 2);
                $output[$group][$single_string] = $quality;
            }
        }

        // Determine how many suggestions we should display
        $limits = [
            'source' => $max_results / 2,
            'target' => $max_results / 2,
        ];
        if (count($output['source']) < $limits['source']) {
            $limits['target'] = $max_results - count($output['source']);
        }
        if (count($output['target']) < $limits['target']) {
            $limits['source'] = $max_results - count($output['target']);
        }

        // Sort them by quality, display higher quality results first
        foreach ($output as $group => $group_strings) {
            natsort($group_strings);
            $suggestions = array_keys(array_reverse($group_strings));
            array_splice($suggestions, $limits[$group]);
            $flat_output = array_merge($flat_output, $suggestions);
        }

        return $flat_output;
    }

    /**
     * Return an array of search results from our Translation Memory API
     * service with a quality index based on the levenshtein distance.
     *
     * @param  array  $strings     The source and target strings to look into
     * @param  string $search      The string to search for
     * @param  int    $max_results Optional, default to 200, the max number of results we return
     * @param  int    $min_quality Optional, default to 0, The minimal quality index to filter result
     * @return array  An array of strings as [source => string, target => string, quality=> Levenshtein index]
     */
    public static function getTranslationMemoryResults($strings, $search, $max_results = 200, $min_quality = 0)
    {
        if (empty($strings)) {
            return [];
        }

        /*
            Here we prepare an output array with source and target strings plus
            a quality index.
            $set[0] is the source string (usually English) on which we
            calculate a quality index based on the Levenshtein algorithm.
            $set[1] is the target string, that is the language we want
            translations from.
        */
        $output = [];
        foreach ($strings as $set) {
            $quality = round(Strings::levenshteinQuality($search, $set[0]), 2);

            if ($quality >= $min_quality) {
                $output[] = [
                    'source'  => $set[0],
                    'target'  => $set[1],
                    'quality' => $quality,
                ];
            }
        }

        // Remove duplicate results
        $output = array_unique($output, SORT_REGULAR);

        // We sort by quality to get the best results first
        usort($output, function ($a, $b) {
           return $a['quality'] < $b['quality'];
        });

        if ($max_results > 0) {
            array_splice($output, $max_results);
        }

        return $output;
    }

    /**
     * Return search results in a repository on strings/entities for the API
     *
     * @param  array $entities      An array of all the entities we want to return
     * @param  array $array_strings The strings to look into [locale1 strings, locale2 strings]
     * @return array An array of strings with the entity as key [entity => [English => French]]
     */
    public static function getRepositorySearchResults($entities, $array_strings)
    {
        $search_results = self::getTMXResults($entities, $array_strings);
        $output = [];

        $clean_string = function ($string) {
            return trim(htmlspecialchars_decode($string, ENT_QUOTES));
        };

        foreach ($search_results as $entity => $set) {
            // We only want results for which we have a translation
            if ($set[1]) {
                $output[$entity] = [
                    $clean_string($set[0]) => $clean_string($set[1]),
                ];
            }
        }

        return $output;
    }

    /**
     * Returns the string from its entity or false
     *
     * @param  string $entity  Entity we are looking for
     * @param  array  $strings Haystack of strings to search in
     * @return string The string for the entity or false if no matching result
     */
    public static function getStringFromEntity($entity, $strings)
    {
        return isset($strings[$entity]) && $strings[$entity] != ''
               ? $strings[$entity]
               : false;
    }

    /**
     * Nicely format entities for tables by splitting them in subpaths and styling them
     *
     * @param  string  $entity
     * @param  boolean $highlight Optional. Default to false. Use a highlighting style
     * @return string  Entity reformated with html markup and css classes for styling
     */
    public static function formatEntity($entity, $highlight = false)
    {
        // Let's analyse the entity for the search string
        $chunk  = explode(':', $entity);

        if ($highlight) {
            $entity = array_pop($chunk);
            $highlight = preg_quote($highlight, '/');
            $entity = preg_replace("/($highlight)/i", '<span class="highlight">$1</span>', $entity);
            $entity = '<span class="red">' . $entity . '</span>';
        } else {
            $entity = '<span class="red">' . array_pop($chunk) . '</span>';
        }
        // Let's analyse the entity for the search string
        $chunk = explode('/', $chunk[0]);
        $repo  = '<span class="green">' . array_shift($chunk) . '</span>';

        $path = implode('<span class="superset">&nbsp;&sup;&nbsp;</span>', $chunk);

        return $repo . '<span class="superset">&nbsp;&sup;&nbsp;</span>' . $path . '<br>' . $entity;
    }

    /**
     * Highlight specific elements in the string.
     *
     * @param  string $string Source text
     * @return string Same string with specific sub-strings in <span>
     *                       elements for styling with CSS
     */
    public static function highlight($string)
    {
        $replacements = [
            ' '        => '<span class="highlight-space" title="White space"> </span>',
            ' '        => '<span class="highlight-red" title="Unicode non-breaking space"> </span>',
            '…'        => '<span class="highlight-gray" title="Real ellipsis">…</span>',
            '&hellip;' => '<span class="highlight-red" title="HTML ellipsis">…</span>',
        ];

        return Strings::multipleStringReplace($replacements, $string);
    }

    /**
     * Html table of search results used by the main view (needs a lot of refactoring)
     *
     * @param object $search_object  The Search object that contains all the options for the query
     * @param array  $search_results List of rows
     *
     * @return string html table to insert in the view
     */
    public static function resultsTable($search_object, $search_results)
    {
        $locale1    = $search_object->getLocales()[0];
        $locale2    = $search_object->getLocales()[1];
        $direction1 = RTLSupport::getDirection($locale1);
        $direction2 = RTLSupport::getDirection($locale2);

        $extra_column_header = '';

        if (isset($search_object->getLocales()[2])) {
            $locale3    = $search_object->getLocales()[2];
            $direction3 = RTLSupport::getDirection($locale3);
            $extra_column_header = "<th>{$locale3}</th>";
        }

        $table  = "<table class='collapsable results_table'>
                     <thead>
                       <tr class='column_headers'>
                         <th>Entity</th>
                         <th>{$locale1}</th>
                         <th>{$locale2}</th>
                         {$extra_column_header}
                       </tr>
                     </thead>
                     <tbody>\n";

        if (! $search_object->isWholeWords() && ! $search_object->isPerfectMatch()) {
            $recherche = Utils::uniqueWords($search_object->getSearchTerms());
        } else {
            $recherche = [$search_object->getSearchTerms()];
        }

        $current_repo = $search_object->getRepository();

        foreach ($search_results as $key => $strings) {

            // Don't highlight search matches in entities when searching strings
            if ($search_object->getSearchType() == 'strings') {
                $result_entity = self::formatEntity($key);
            } else {
                $result_entity = self::formatEntity($key, $recherche[0]);
            }

            $component = explode('/', $key)[0];
            $source_string = trim($strings[0]);
            $target_string = trim($strings[1]);

            $entity_link = "?sourcelocale={$locale1}"
            . "&locale={$locale2}"
            . "&repo={$current_repo}"
            . "&search_type=entities&recherche={$key}";

            $bz_link = [Bugzilla::reportErrorLink(
                $locale2, $key, $source_string, $target_string, $current_repo, $entity_link
            )];

            if (isset($search_object->getLocales()[2])) {
                $target_string2 = trim($strings[2]);
                $entity_link = "?sourcelocale={$locale1}"
                                . "&locale={$search_object->getLocales()[2]}"
                                . "&repo={$current_repo}"
                                . "&search_type=entities&recherche={$key}";
                $bz_link[] = Bugzilla::reportErrorLink(
                    $search_object->getLocales()[2], $key, $source_string, $target_string2, $current_repo, $entity_link
                );
            } else {
                $target_string2 = '';
            }

            foreach ($recherche as $val) {
                $source_string = Utils::markString($val, $source_string);
                $target_string = Utils::markString($val, $target_string);
                if (isset($search_object->getLocales()[2])) {
                    $target_string2 = Utils::markString($val, $target_string2);
                }
            }

            // Escape HTML before highlighing search terms
            $source_string = htmlspecialchars($source_string);
            $target_string = htmlspecialchars($target_string);
            $source_string = Utils::highlightString($source_string);
            $target_string = Utils::highlightString($target_string);

            if (isset($search_object->getLocales()[2])) {
                $target_string2 = htmlspecialchars($target_string2);
                $target_string2 = Utils::highlightString($target_string2);
            }

            $replacements = [
                ' '            => '<span class="highlight-gray" title="Non breakable space"> </span>', // Nbsp highlight
                ' '            => '<span class="highlight-red" title="Thin space"> </span>', // Thin space highlight
                '…'            => '<span class="highlight-gray">…</span>', // Right ellipsis highlight
                '&hellip;'     => '<span class="highlight-gray">…</span>', // Right ellipsis highlight
            ];

            $target_string = Strings::multipleStringReplace($replacements, $target_string);

            $temp = explode('-', $locale1);
            $locale1_short_code = $temp[0];

            $temp = explode('-', $locale2);
            $locale2_short_code = $temp[0];

            if (in_array($current_repo, ['firefox_ios', 'mozilla_org'])) {
                $locale1_path = VersionControl::gitPath($locale1, $current_repo, $key);
                $locale2_path = VersionControl::gitPath($locale2, $current_repo, $key);
            } else {
                $locale1_path = VersionControl::hgPath($locale1, $current_repo, $key);
                $locale2_path = VersionControl::hgPath($locale2, $current_repo, $key);
            }

            // Errors
            $error_message = '';

            // Check for final dot
            if (substr(strip_tags($source_string), -1) == '.'
                && substr(strip_tags($target_string), -1) != '.') {
                $error_message = '<em class="error"> No final dot?</em>';
            }

            // Check abnormal string length
            $length_diff = Utils::checkAbnormalStringLength($source_string, $target_string);
            if ($length_diff) {
                switch ($length_diff) {
                    case 'small':
                        $error_message = $error_message . '<em class="error"> Small string?</em>';
                        break;
                    case 'large':
                        $error_message = $error_message . '<em class="error"> Large String?</em>';
                        break;
                }
            }

            // Missing string error
            if (! $source_string) {
                $source_string = '<em class="error">warning: missing string</em>';
                $error_message = '';
            }

            if (! $target_string) {
                $target_string = '<em class="error">warning: missing string</em>';
                $error_message = '';
            }

            if (! $target_string2) {
                $target_string2 = '<em class="error">warning: missing string</em>';
                $error_message = '';
            }

            // Replace / and : in the key name and use it as an anchor name
            $anchor_name = str_replace(['/', ':'], '_', $key);

            $clipboard_target_string  = 'clip_' . md5($target_string);

            // 3locales view
            if (isset($search_object->getLocales()[2])) {
                if (in_array($current_repo, ['firefox_ios', 'mozilla_org'])) {
                    $locale3_path = VersionControl::gitPath($locale3, $current_repo, $key);
                } else {
                    $locale3_path = VersionControl::hgPath($locale3, $current_repo, $key);
                }

                $clipboard_target_string2 = 'clip_' . md5($target_string2);

                $extra_column_rows = "
                <td dir='{$direction3}' lang='{$locale3}'>
                    <span class='celltitle'>{$locale3}</span>
                    <div class='string' id='{$clipboard_target_string2}'>{$target_string2}</div>
                    <div dir='ltr' class='result_meta_link'>
                      <a class='source_link' href='{$locale3_path}'>
                        &lt;source&gt;
                      </a>
                      &nbsp;
                      <a class='bug_link' target='_blank' href='{$bz_link[1]}'>
                        &lt;report a bug&gt;
                      </a>
                      <span class='clipboard' data-clipboard-target='#{$clipboard_target_string2}' alt='Copy to clipboard'><img src='/img/copy_icon_black_18x18.png'></span>
                    </div>
                  </td>";
            } else {
                $extra_column_rows = '';
            }
            $table .= "
                <tr class='{$component}'>
                  <td>
                    <span class='celltitle'>Entity</span>
                    <a class='resultpermalink tag' id='{$anchor_name}' href='#{$anchor_name}' title='Permalink to this result'>link</a>
                    <a class='l10n tag' href='/string/?entity={$key}&amp;repo={$current_repo}' title='List all translations for this entity'>l10n</a>
                    <a class='link_to_entity' href=\"/{$entity_link}\">{$result_entity}</a>
                  </td>
                  <td dir='{$direction1}' lang='{$locale1}'>
                    <span class='celltitle'>{$locale1}</span>
                    <div class='string'>
                      {$source_string}
                    </div>
                    <div dir='ltr' class='result_meta_link'>
                      <a class='source_link' href='{$locale1_path}'>
                        &lt;source&gt;
                      </a>
                      <span>Translate with:</span>
                      <a href='http://translate.google.com/#{$locale1_short_code}/{$locale2_short_code}/"
                      // We use html_entity_decode twice because we can have strings as &amp;amp; stored
                      . urlencode(strip_tags(html_entity_decode(html_entity_decode($source_string))))
                      . "' target='_blank'>Google</a>
                      <a href='http://www.bing.com/translator/?from={$locale1_short_code}&to={$locale2_short_code}&text="
                      . urlencode(strip_tags(html_entity_decode(html_entity_decode($source_string))))
                      . "' target='_blank'>BING</a>
                    </div>
                  </td>

                  <td dir='{$direction2}' lang='{$locale2}'>
                    <span class='celltitle'>{$locale2}</span>
                    <div class='string' id='{$clipboard_target_string}'>{$target_string}</div>
                    <div dir='ltr' class='result_meta_link'>
                      <a class='source_link' href='{$locale2_path}'>
                        &lt;source&gt;
                      </a>
                      &nbsp;
                      <a class='bug_link' target='_blank' href='{$bz_link[0]}'>
                        &lt;report a bug&gt;
                      </a>
                      <span class='clipboard' data-clipboard-target='#{$clipboard_target_string}' alt='Copy to clipboard'><img src='/img/copy_icon_black_18x18.png'></span>
                      {$error_message}
                    </div>
                  </td>
                {$extra_column_rows}
                </tr>";
        }

        $table .= "  </tbody>\n</table>\n";

        return $table;
    }

    /**
     * Search entity names: search full entity IDs (including path and filename),
     * then search entity names (without the full path) if there are no results.
     *
     * @param array  $source_strings Array of source strings
     * @param string $regex          Regular expression to search entity names
     *
     * @return array List of matching entity names
     */
    public static function searchEntities($source_strings, $regex)
    {
        // Search through the full entity ID
        $entities = preg_grep($regex, array_keys($source_strings));

        /*
            If there are no results, search also through the entity names.
            This is needed for "perfect match" when only the entity name is
            provided.
        */
        if (empty($entities)) {
            $entity_names = [];
            foreach ($source_strings as $entity => $translation) {
                $entity_names[$entity] = explode(':', $entity)[1];
            }
            $entities = preg_grep($regex, $entity_names);
            $entities = array_keys($entities);
        }

        return $entities;
    }
}
