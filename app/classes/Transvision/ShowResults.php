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
     *
     * @param array $entities      Haystack of entities to search in
     * @param array $array_strings The strings to look into [locale1 strings, locale2 strings]
     *
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
     * @param array  $strings     The source and target strings to look into
     * @param string $search      The string to search for
     * @param int    $max_results Optional, default to 200, the max number of results we return
     * @param int    $min_quality Optional, default to 0, The minimal quality index to filter result
     *
     * @return array An array of strings as [source => string, target => string, quality=> Levenshtein index]
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
     * Use the API to return a transliterated string
     *
     * @param string $string The string to be transliterated
     * @param string $locale The target locale
     *
     * @return string The string transliterated into the target locale
     */
    public static function getTransliteratedString($string, $locale)
    {
        $request = new API(parse_url(API_ROOT . "transliterate/$locale/$string"));
        $json = include MODELS . 'api/transliterate.php';

        return isset($json[0]) ? $json[0] : 'Function not available';
    }

    /**
     * Return search results in a repository on strings/entities for the API
     *
     * @param array $entities      An array of all the entities we want to return
     * @param array $array_strings The strings to look into [locale1 strings, locale2 strings]
     *
     * @return array An array of strings with the entity as key [entity => [English => French]]
     */
    public static function getRepositorySearchResults($entities, $array_strings)
    {
        $search_results = self::getTMXResults($entities, $array_strings);
        $output = [];

        $clean_string = function ($string) {
            return htmlspecialchars_decode($string, ENT_QUOTES);
        };

        foreach ($search_results as $entity => $set) {
            // We only want results for which we have a translation
            if ($set[1] != '@@missing@@') {
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
     * @param string $entity  Entity we are looking for
     * @param array  $strings Haystack of strings to search in
     *
     * @return string The string for the entity or false if no matching result
     */
    public static function getStringFromEntity($entity, $strings)
    {
        return isset($strings[$entity])
               ? $strings[$entity]
               : '@@missing@@';
    }

    /**
     * Nicely format entities for tables by splitting them in subpaths and styling them
     *
     * @param string  $entity
     * @param boolean $highlight Optional. Default to false. Use a highlighting style
     *
     * @return string Entity reformated with html markup and css classes for styling
     */
    public static function formatEntity($entity, $highlight = false)
    {
        // Let's analyse the entity for the search string
        $chunk = explode(':', $entity);

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
        $repo = '<span class="green">' . array_shift($chunk) . '</span>';

        $path = implode('<span class="superset">&nbsp;&bull;&nbsp;</span>', $chunk);

        return $repo . '<span class="superset">&nbsp;&bull;&nbsp;</span>' . $path . '<br>' . $entity;
    }

    /**
     * Return link to edit a message on external tool used by requested locale
     *
     * @param string $repo   Repository
     * @param string $key    Key of the current string
     * @param string $text   Text of the current strings
     * @param string $locale Current locale
     *
     * @return string HTML link to edit the string inside the tool used by this locale
     */
    public static function getEditLink($repo, $key, $text, $locale)
    {
        $component = explode('/', $key)[0];
        $fileAndRawString = explode(':', $key);

        // Ignore files in /extensions
        if ($component == 'extensions') {
            return '';
        }

        // Ignore Beta and Release
        if (in_array($repo, ['beta', 'release'])) {
            return '';
        }

        // We only support Pontoon
        $tool_name = 'Pontoon';

        if ($repo == 'mozilla_org') {
            $project_name = 'mozillaorg';
            $resource_path = ltrim($fileAndRawString[0], 'mozilla_org/');
            $search_key = $text;
        } elseif ($repo == 'firefox_ios') {
            $project_name = 'firefox-for-ios';
            $resource_path = 'firefox-ios.xliff';
            $search_key = $text;
        } else {
            $resource_path = $fileAndRawString[0];
            $search_key = $fileAndRawString[1];
            switch ($component) {
                case 'calendar':
                    $project_name = 'lightning';
                    break;
                case 'chat':
                case 'editor':
                case 'mail':
                    $project_name = 'thunderbird';
                    break;
                case 'mobile':
                    $project_name = 'firefox-for-android';
                    break;
                case 'suite':
                    $project_name = 'seamonkey';
                    break;
                default:
                    $project_name = 'firefox';
            }
        }

        $edit_link = "https://pontoon.mozilla.org/{$locale}/{$project_name}/{$resource_path}?search={$search_key}";

        return "&nbsp;<a class='edit_link' target='_blank' href='{$edit_link}'>&lt;edit in {$tool_name}&gt;</a>";
    }

    /**
     * Html table of search results used by the main view (needs a lot of refactoring)
     *
     * @param object $search_object  The Search object that contains all the options for the query
     * @param array  $search_results List of rows
     * @param string $page           The page we are generating, used to output results for 2 or 3 locales
     *
     * @return string html table to insert in the view
     */
    public static function resultsTable($search_object, $search_results, $page)
    {
        $locale1 = $search_object->getLocale('source');
        $locale2 = $search_object->getLocale('target');
        $direction1 = RTLSupport::getDirection($locale1);
        $direction2 = RTLSupport::getDirection($locale2);
        $extra_locale = ($page == '3locales');

        $extra_column_header = '';

        // Get the tool used to edit strings for the target locale
        $toolUsedByTargetLocale = Project::getLocaleTool($locale2);

        if ($extra_locale) {
            $locale3 = $search_object->getLocale('extra');
            $direction3 = RTLSupport::getDirection($locale3);
            $extra_column_header = "<th>{$locale3}</th>";
        }

        $table = "<table class='collapsable results_table sortable'>
                     <thead>
                       <tr class='column_headers'>
                         <th>Entity</th>
                         <th>{$locale1}</th>
                         <th>{$locale2}</th>
                         {$extra_column_header}
                       </tr>
                     </thead>
                     <tbody>\n";

        if ($search_object->isEachWord()) {
            $search_terms = Utils::uniqueWords($search_object->getSearchTerms());
        } else {
            $search_terms = [$search_object->getSearchTerms()];
        }

        $current_repo = $search_object->getRepository();

        foreach ($search_results as $key => $strings) {
            // Don't highlight search matches in entities when searching strings
            if ($search_object->getSearchType() == 'strings') {
                $result_entity = self::formatEntity($key);
            } else {
                $result_entity = self::formatEntity($key, $search_terms[0]);
            }

            $component = explode('/', $key)[0];
            $source_string = $strings[0];
            $target_string = $strings[1];

            $entity_link = "?sourcelocale={$locale1}"
            . "&locale={$locale2}"
            . "&repo={$current_repo}"
            . "&search_type=entities&recherche={$key}"
            . '&entire_string=entire_string';

            $bz_link = [Bugzilla::reportErrorLink(
                $locale2, $key, $source_string, $target_string, $current_repo, $entity_link
            )];

            if ($extra_locale) {
                $target_string2 = $strings[2];
                $entity_link = "?sourcelocale={$locale1}"
                                . "&locale={$search_object->getLocale('extra')}"
                                . "&repo={$current_repo}"
                                . "&search_type=entities&recherche={$key}"
                                . '&entire_string=entire_string';
                $bz_link[] = Bugzilla::reportErrorLink(
                    $search_object->getLocale('extra'), $key, $source_string, $target_string2, $current_repo, $entity_link
                );
            } else {
                $target_string2 = '';
            }

            $string_id = md5($key . mt_rand());
            $regular_string_id = 'string_' . $string_id;

            /*
                Find if we need to transliterate the string.
                The string gets transliterated if the target local is Serbian,
                if we aren't in the 3locales view and if we have a $target_string
            */
            $transliterate = $locale2 == 'sr' && ! $extra_locale && $target_string && $target_string != '@@missing@@';

            $edit_link = $toolUsedByTargetLocale != ''
                ? self::getEditLink($current_repo, $key, $target_string, $locale2)
                : '';

            if ($transliterate) {
                $transliterated_string = self::getTransliteratedString(urlencode($target_string), 'sr-Cyrl');
                $transliterate_string_id = 'transliterate_' . $string_id;
            }

            foreach ($search_terms as $search_term) {
                $source_string = Strings::markString($search_term, $source_string);
                $target_string = Strings::markString($search_term, $target_string);
                if ($extra_locale) {
                    $target_string2 = Strings::markString($search_term, $target_string2);
                }
                if ($transliterate) {
                    $transliterated_string = Strings::markString($search_term, $transliterated_string);
                }
            }

            // Escape HTML before highlighing search terms
            $source_string = htmlspecialchars($source_string);
            $target_string = htmlspecialchars($target_string);

            $source_string = Strings::highlightString($source_string);
            $target_string = Strings::highlightString($target_string);
            $source_string = Strings::highlightSpecial($source_string);
            $target_string = Strings::highlightSpecial($target_string);

            if ($transliterate) {
                $transliterated_string = htmlspecialchars($transliterated_string);
                $transliterated_string = Strings::highlightString($transliterated_string);
                $transliterated_string = Strings::highlightSpecial($transliterated_string);
            }

            if ($extra_locale) {
                $target_string2 = htmlspecialchars($target_string2);
                $target_string2 = Strings::highlightString($target_string2);
                $target_string2 = Strings::highlightSpecial($target_string2);
            }

            $clipboard_target_string = 'clip_' . md5($target_string);
            $clipboard_target_string2 = 'clip_' . md5($target_string2);

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

            // Get the potential errors for $target_string (final dot, long/small string)
            $error_message = ShowResults::buildErrorString($source_string, $target_string);

            // Don't show meta links by default
            $meta_source = $meta_target = $meta_target2 = '';

            // If there is no source_string, display an error, otherwise display the string + meta links
            if ($source_string == '@@missing@@') {
                $source_string = '<em class="error">Warning: Source string is missing</em>';
            } elseif (! $source_string) {
                $source_string = '<em class="error">Warning: Source string is empty</em>';
            } else {
                $meta_source = "
                  <span>Translate with:</span>
                  <a href='https://translate.google.com/#{$locale1_short_code}/{$locale2_short_code}/"
                  // We use html_entity_decode twice because we can have strings as &amp;amp; stored
                  . urlencode(strip_tags(html_entity_decode(html_entity_decode($source_string))))
                  . "' target='_blank'>Google</a>
                  <a href='https://www.bing.com/translator/?from={$locale1_short_code}&to={$locale2_short_code}&text="
                  . urlencode(strip_tags(html_entity_decode(html_entity_decode($source_string))))
                  . "' target='_blank'>BING</a>";
            }

            // If there is no target_string, display an error, otherwise display the string + meta links
            if ($target_string == '@@missing@@') {
                $target_string = '<em class="error">Warning: Missing string</em>';
            } elseif (! $target_string) {
                $target_string = '<em class="error">Warning: Empty string</em>';
            } else {
                $meta_target = "<span class='clipboard' data-clipboard-target='#{$regular_string_id}' alt='Copy to clipboard'></span>";
                if ($transliterate) {
                    $meta_target .= "<input type='button' value='To Latin' data-transliterated-id='{$string_id}' class='transliterate_button button action'>";
                }
                $meta_target .= $error_message;
            }

            // If there is no target_string2, display an error, otherwise display the string + meta links
            if ($target_string2 == '@@missing@@') {
                $target_string2 = '<em class="error">Warning: Missing string</em>';
            } elseif (! $target_string2) {
                $target_string2 = '<em class="error">Warning: Empty string</em>';
            } else {
                $meta_target2 = "<span class='clipboard' data-clipboard-target='#{$clipboard_target_string2}' alt='Copy to clipboard'></span>";
            }

            // Replace / and : in the key name and use it as an anchor name
            $anchor_name = str_replace(['/', ':'], '_', $key);

            // 3locales view
            if ($extra_locale) {
                if (in_array($current_repo, ['firefox_ios', 'mozilla_org'])) {
                    $locale3_path = VersionControl::gitPath($locale3, $current_repo, $key);
                } else {
                    $locale3_path = VersionControl::hgPath($locale3, $current_repo, $key);
                }

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
                      {$meta_target2}
                    </div>
                </td>";
            } else {
                $extra_column_rows = '';
            }
            $table .= "
                <tr class='{$component}'>
                  <td>
                    <span class='celltitle'>Entity</span>
                    <a class='resultpermalink tag' id='{$anchor_name}' href='#{$anchor_name}' title='Permalink to this result'>#</a>
                    <a class='l10n tag' href='/string/?entity={$key}&amp;repo={$current_repo}' title='List all translations for this entity'>all locales</a>
                    <span class='link_to_entity'>
                      <a href=\"/{$entity_link}\">{$result_entity}</a>
                    </span>
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
                      {$meta_source}
                    </div>
                  </td>

                  <td dir='{$direction2}' lang='{$locale2}'>
                    <span class='celltitle'>{$locale2}</span>
                    <div class='string' id='{$regular_string_id}'>{$target_string}</div>";
            if ($transliterate) {
                $table .= "<div class='string toggle' id='{$transliterate_string_id}' style='display: none;'>{$transliterated_string}</div>";
            }
            $table .= "
                    <div dir='ltr' class='result_meta_link'>
                      <a class='source_link' href='{$locale2_path}'>
                        &lt;source&gt;
                      </a>
                      {$edit_link}
                      &nbsp;
                      <a class='bug_link' target='_blank' href='{$bz_link[0]}'>
                        &lt;report a bug&gt;
                      </a>
                      {$meta_target}
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
            This is needed for "entire string" when only the entity name is
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

    /**
     * Build the error message for target string. Can return a combination of
     * errors, including missing final dot, long string and short string.
     *
     * @param string $source_string String from the source locale
     * @param string $target_string String from the target locale
     *
     * @return string A concatenated string of errors or an empty string if
     *                there is no error.
     */
    public static function buildErrorString($source_string, $target_string)
    {
        $error_message = '';

        // Check for final dot
        if (substr(strip_tags($source_string), -1) == '.'
            && substr(strip_tags($target_string), -1) != '.') {
            $error_message = '<em class="error">No final dot?</em> ';
        }

        // Check abnormal string length
        $length_diff = Utils::checkAbnormalStringLength($source_string, $target_string);
        if ($length_diff) {
            switch ($length_diff) {
                case 'small':
                    $error_message = $error_message . '<em class="error">Small string?</em> ';
                    break;
                case 'large':
                    $error_message = $error_message . '<em class="error">Large string?</em> ';
                    break;
            }
        }

        // Missing string error
        if (! $source_string || ! $target_string) {
            $error_message = '';
        }

        return $error_message;
    }
}
