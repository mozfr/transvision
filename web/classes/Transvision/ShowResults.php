<?php
namespace Transvision;

class ShowResults
{
    /*
     * Create an array for search results with this format:
     * 'entity' => ['locale 1', 'locale 2']
     */
    public function getTMXResults($entities, $locale1_strings, $locale2_strings)
    {
        $search_results = array();

        foreach ($entities as $entity) {
            $locale1_strings[$entity] = (isset($locale1_strings[$entity]) && $locale1_strings[$entity] !='') ?
                $locale1_strings[$entity] : false;
            $locale2_strings[$entity] = (isset($locale2_strings[$entity]) && $locale2_strings[$entity] !='') ?
                $locale2_strings[$entity]: false;
            $search_results[$entity] = array($locale1_strings[$entity], $locale2_strings[$entity]);
        }
        return $search_results;
    }

    /*
     * make an entity look nice in tables
     *
     */
    public static function formatEntity($entity)
    {
        // let's analyse the entity for the search string
        $chunk = explode('/', $entity);
        // let's format the entity key to look better
        $chunk[0] = '<span class="green">' . $chunk[0] . '</span>';
        $chunk[1] = '<span class="blue">' .  $chunk[1] . '</span>';
        $chunk[2] = '<span class="red">' .   $chunk[2] . '</span>';
        $entity = implode('<span class="superset">&nbsp;&sup;&nbsp;</span>', $chunk);
        return $entity;
    }

    /*
     * format string for French cases
     *
     */
    public static function highlight($string, $locale = 'fr')
    {
        switch($locale) {
            case 'fr':
            default:
                $string = str_replace('&hellip;', '<span class="highlight-gray">…</span>', $string); // right ellipsis highlight
                break;
        }

        $string = str_replace(' ', '<span class="highlight-gray"> </span>', $string); // nbsp highlight
        $string = str_replace('…', '<span class="highlight-gray">…</span>', $string); // right ellipsis highlight
        return $string;
    }

    /*
     * Search results in a table
     */
    public static function resultsTable($search_results, $recherche, $locale1, $locale2, $search_options)
    {
        $direction1 = RTLSupport::getDirection($locale1);
        $direction2 = RTLSupport::getDirection($locale2);

        // Get cached bugzilla components (languages list) or connect to Bugzilla API to retrieve them
        $bz_component = rawurlencode(
                            Utils::collectLanguageComponent(
                                $locale2,
                                Utils::getBugzillaComponents()
        ));

        $bz_link = 'https://bugzilla.mozilla.org/enter_bug.cgi?format=__default__&component='
                   . $bz_component
                   . '&product=Mozilla%20Localizations&status_whiteboard=%5Btransvision-feedback%5D';

        $table  = "<style>
                    /* Label for responsive view */
                    @media only screen and (max-width: 850px)  {
                    td:nth-of-type(1):after { content: 'Entity'; }
                    td:nth-of-type(2):after { content: '$locale1'; }
                    td:nth-of-type(3):after { content: '$locale2'; }
                    }
                   </style>
                   <table>
                      <tr>
                        <th>Entity</th>
                        <th>$locale1</th>
                        <th>$locale2</th>
                      </tr>";

        if (!$search_options['whole_word'] && !$search_options['perfect_match']) {
            $recherche = Utils::uniqueWords($recherche);
        } else {
            $recherche = array($recherche);
        }

        foreach ($search_results as $key => $strings) {

            $source_string = trim($strings[0]);
            $target_string = trim($strings[1]);

            // Bugzilla GET data
            $bug_summary = rawurlencode("Translation update proposed for ${key}");
            // We don't rawurlencode() the strings otherwise they are unreadable in the bugzilla comment
            $bug_message = rawurlencode("The string:\n")
                           . $source_string
                           . rawurlencode("\n\nIs translated as:\n")
                           . $target_string
                           . rawurlencode("\n\nAnd should be:\n\n\n\nFeedback via Transvision:\nhttp://transvision.mozfr.org/?sourcelocale=${locale1}&locale=${locale2}&repo=${search_options['repo']}&search_type=entities&recherche=${key}");

            foreach ($recherche as $val) {
                $source_string = Utils::markString($val, $source_string);
                $target_string = Utils::markString($val, $target_string);
            }

            $source_string = Utils::highlightString($source_string);
            $target_string = Utils::highlightString($target_string);

            // nbsp highlight
            $target_string = str_replace(
                ' ',
                '<span class="highlight-gray" title="Non breakable space"> </span>',
                $target_string
            );
            // thin space highlight
            $target_string = str_replace(
                ' ',
                '<span class="highlight-red" title="Thin space"> </span>',
                $target_string
            );

            // right ellipsis highlight
            $target_string = str_replace(
                '…',
                '<span class="highlight-gray">…</span>',
                $target_string
            );

            // right ellipsis highlight
            $target_string = str_replace(
                '&hellip;',
                '<span class="highlight-gray">…</span>',
                $target_string
            );

            $temp = explode('-', $locale1);
            $short_locale1 = $temp[0];

            $temp = explode('-', $locale2);
            $short_locale2 = $temp[0];

            $path_locale1 = Utils::pathFileInRepo($locale1, $search_options['repo'], $key);
            $path_locale2 = Utils::pathFileInRepo($locale2, $search_options['repo'], $key);

            // errors
            $error_msg = '';

            // check for final dot
            if (substr(strip_tags($source_string), -1) == '.'
                && substr(strip_tags($target_string), -1) != '.') {
                $error_msg = '<em class="error"> No final dot?</em>';
            }

            // check abnormal string length
            $length_diff = Utils::checkAbnormalStringLength($source_string, $target_string);
            if ($length_diff) {
                switch ($length_diff) {
                    case 'small':
                        $error_msg = $error_msg . '<em class="error"> Small string?</em>';
                        break;
                    case 'large':
                        $error_msg = $error_msg . '<em class="error"> Large String?</em>';
                        break;
                }
            }

            // Missing string error
            if (!$source_string) {
                $source_string = '<em class="error">warning: missing string</em>';
                $error_msg = '';
            }
            if (!$target_string) {
                $target_string = '<em class="error">warning: missing string</em>';
                $error_msg = '';
            }

            $table .= "
                <tr>
                  <td>" . Utils::formatEntity($key, $recherche[0]) . "</td>

                  <td dir='${direction1}'>
                    <div class='string'>
                      <a href='http://translate.google.com/#${short_locale1}/${short_locale2}/"
                      . urlencode(strip_tags($source_string))
                      . "'>${source_string}</a>
                    </div>
                    <div dir='ltr' class='infos'>
                      <a class='source_link' href='${path_locale1}'>
                        &lt;source&gt;
                      </a>
                    </div>
                  </td>

                  <td dir='${direction2}'>
                    <div class='string'>${target_string}</div>
                    <div dir='ltr' class='infos'>
                      <a class='source_link' href='${path_locale2}'>
                        &lt;source&gt;
                      </a>
                      &nbsp;
                      <a class='bug_link' target='_blank' href='${bz_link}&short_desc=${bug_summary}&comment=${bug_message}'>
                        &lt;report a bug&gt;
                      </a>
                      ${error_msg}
                    </div>
                  </td>
                </tr>";
        }

        $table .= "  </table>";
        return $table;
    }
}
