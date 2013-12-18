<?php
namespace Transvision;

class ShowResults
{
    /*
     * Create an array for search results with this format:
     * 'entity' => ['locale 1', 'locale 2']
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

    /*
     * Returns the string from its entity or false
     * @param $entity string
     * @param $strings array
     * @return string or false
     */
    public static function getStringFromEntity($entity, $strings)
    {
        return isset($strings[$entity]) && $strings[$entity] != ''
               ? $strings[$entity]
               : false;
    }

    /*
     * make an entity look nice in tables
     *
     */
    public static function formatEntity($entity, $highlight = false)
    {
        // let's analyse the entity for the search string
        $chunk  = explode(':', $entity);

        if ($highlight) {
            $entity = array_pop($chunk);
            $highlight = preg_quote($highlight, '/');
            $entity = preg_replace("/($highlight)/i", '<span class="highlight">$1</span>', $entity);
            $entity = '<span class="red">' . $entity . '</span>';
        } else {
            $entity = '<span class="red">' . array_pop($chunk) . '</span>';
        }
        // let's analyse the entity for the search string
        $chunk = explode('/', $chunk[0]);
        $repo  = '<span class="green">' . array_shift($chunk) . '</span>';

        $path = implode('<span class="superset">&nbsp;&sup;&nbsp;</span>', $chunk);

        return $repo . '<span class="superset">&nbsp;&sup;&nbsp;</span>' . $path . '<br>' .$entity;
    }

    /*
     * format string for French cases
     *
     */
    public static function highlight($string, $locale = 'fr')
    {
        $replacements = array(
            ' ' => '<span class="highlight-gray"> </span>',
            '…' => '<span class="highlight-gray">…</span>',
        );

        switch ($locale) {
            case 'fr':
            default:
                $replacements['&hellip;'] = '<span class="highlight-gray">…</span>'; // right ellipsis highlight
                break;
        }

        return Strings::multipleStringReplace($replacements, $string);
    }

    /*
     * Search results in a table
     */
    public static function resultsTable($search_results, $recherche, $locale1, $locale2, $search_options)
    {
        $direction1 = RTLSupport::getDirection($locale1);
        $direction2 = RTLSupport::getDirection($locale2);

        if (isset($search_options['extra_locale'])) {
            $locale3    = $search_options['extra_locale'];
            $direction3 = RTLSupport::getDirection($locale3);
            $extra_column_header = "<th>{$locale3}</th>";
        } else {
            $extra_column_header = '';
        }

        // Get cached bugzilla components (languages list) or connect to Bugzilla API to retrieve them

        $bz_link = function($locale) {
            $bz_component = rawurlencode(
               Bugzilla::collectLanguageComponent(
                    $locale,
                    Bugzilla::getBugzillaComponents()
                )
            );

            return 'https://bugzilla.mozilla.org/enter_bug.cgi?format=__default__&component='
                   . $bz_component
                   . '&product=Mozilla%20Localizations&status_whiteboard=%5Btransvision-feedback%5D';
        };


        $table  = "<table>
                      <tr>
                        <th>Entity</th>
                        <th>{$locale1}</th>
                        <th>{$locale2}</th>
                        {$extra_column_header}
                      </tr>";


        if (!$search_options['whole_word'] && !$search_options['perfect_match']) {
            $recherche = Utils::uniqueWords($recherche);
        } else {
            $recherche = array($recherche);
        }

        foreach ($search_results as $key => $strings) {

            // Don't highlight search matches in entities when searching strings
            if ($search_options['search_type'] == 'strings') {
                $result_entity = ShowResults::formatEntity($key);
            } else {
                $result_entity = ShowResults::formatEntity($key, $recherche[0]);
            }

            $source_string = trim($strings[0]);
            $target_string = trim($strings[1]);

            if (isset($search_options["extra_locale"])) {
                $target_string2 = trim($strings[2]);
            } else {
                $target_string2 = '';
            }

            // Link to entity
            $entity_link = "?sourcelocale={$locale1}"
                        . "&locale={$locale2}"
                        . "&repo={$search_options['repo']}"
                        . "&search_type=entities&recherche={$key}";

            // Bugzilla GET data
            $bug_summary = rawurlencode("Translation update proposed for ${key}");

            $bug_message = function($extra_locale)
                use ($source_string, $target_string,
                    $target_string2, $entity_link) {
                    $target_string = $extra_locale ? $target_string2 : $target_string;

                    return rawurlencode(html_entity_decode(
                            "The string:\n{$source_string}\n\n"
                            . "Is translated as:\n{$target_string}\n\n"
                            . "And should be:\n\n\n\n"
                            . "Feedback via Transvision:\n"
                            . "http://transvision.mozfr.org/{$entity_link}"
                        ));
            };

            foreach ($recherche as $val) {
                $source_string = Utils::markString($val, $source_string);
                $target_string = Utils::markString($val, $target_string);
                if (isset($search_options["extra_locale"])) {
                    $target_string2 = Utils::markString($val, $target_string2);
                }
            }

            $source_string = Utils::highlightString($source_string);
            $target_string = Utils::highlightString($target_string);

            if (isset($search_options["extra_locale"])) {
                $target_string2 = Utils::highlightString($target_string2);
            }

            $replacements = array(
                ' '        => '<span class="highlight-gray" title="Non breakable space"> </span>', // nbsp highlight
                ' '        => '<span class="highlight-red" title="Thin space"> </span>', // thin space highlight
                '…'        => '<span class="highlight-gray">…</span>', // right ellipsis highlight
                '&hellip;' => '<span class="highlight-gray">…</span>', // right ellipsis highlight
            );

            $target_string = Strings::multipleStringReplace($replacements, $target_string);

            $temp = explode('-', $locale1);
            $locale1_short_code = $temp[0];

            $temp = explode('-', $locale2);
            $locale2_short_code = $temp[0];

            $locale1_path = VersionControl::filePath($locale1, $search_options['repo'], $key);
            $locale2_path = VersionControl::filePath($locale2, $search_options['repo'], $key);

            // errors
            $error_message = '';

            // check for final dot
            if (substr(strip_tags($source_string), -1) == '.'
                && substr(strip_tags($target_string), -1) != '.') {
                $error_message = '<em class="error"> No final dot?</em>';
            }

            // check abnormal string length
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
            if (!$source_string) {
                $source_string = '<em class="error">warning: missing string</em>';
                $error_message = '';
            }
            if (!$target_string) {
                $target_string = '<em class="error">warning: missing string</em>';
                $error_message = '';
            }
            if (!$target_string2) {
                $target_string2 = '<em class="error">warning: missing string</em>';
                $error_message = '';
            }

            // Replace / and : in the key name and use it as an anchor name
            $anchor_name = str_replace(array('/', ':'), '_', $key);

            // 3locales view
            if (isset($search_options["extra_locale"])) {
                $locale3_path = VersionControl::filePath($locale3, $search_options['repo'], $key);

                $extra_column_rows = "
                <td dir='{$direction3}'>
                    <div class='string'>{$target_string2}</div>
                    <div dir='ltr' class='infos'>
                      <a class='source_link' href='{$locale3_path}'>
                        &lt;source&gt;
                      </a>
                      &nbsp;
                      <a class='bug_link' target='_blank' href='{$bz_link($locale3)}&short_desc={$bug_summary}&comment={$bug_message(true)}'>
                        &lt;report a bug&gt;
                      </a>
                    </div>
                  </td>
                </tr>";

            } else {
                $extra_column_rows = '';
            }

            $table .= "
                <tr>
                  <td>
                    <a class='resultpermalink tag' id='{$anchor_name}' href='#{$anchor_name}' title='Permalink to this result'>link</a>
                    <a class='l10n tag' href='/string/?entity={$key}&amp;repo={$search_options['repo']}' title='List all translations for this entity'>l10n</a>
                    <a class='linktoentity' href=\"/{$entity_link}\">{$result_entity}</a>
                  </td>
                  <td dir='{$direction1}'>
                    <div class='string'>
                      {$source_string}
                    </div>
                    <div dir='ltr' class='infos'>
                      <a class='source_link' href='{$locale1_path}'>
                        &lt;source&gt;
                      </a>
                      <span>Translate with:</span>
                      <a href='http://translate.google.com/#{$locale1_short_code}/{$locale2_short_code}/"
                      . urlencode(strip_tags($source_string))
                      . "' target='_blank'>Google</a>
                      <a href='http://www.bing.com/translator/?from={$locale1_short_code}&to={$locale2_short_code}&text="
                      . urlencode(strip_tags($source_string))
                      . "' target='_blank'>BING</a>
                    </div>
                  </td>

                  <td dir='{$direction2}'>
                    <div class='string'>{$target_string}</div>
                    <div dir='ltr' class='infos'>
                      <a class='source_link' href='{$locale2_path}'>
                        &lt;source&gt;
                      </a>
                      &nbsp;
                      <a class='bug_link' target='_blank' href='{$bz_link($locale2)}&short_desc={$bug_summary}&comment={$bug_message(false)}'>
                        &lt;report a bug&gt;
                      </a>
                      {$error_message}
                    </div>
                  </td>
                {$extra_column_rows}
                </tr>";
        }

        $table .= "  </table>";

        return $table;
    }
}
