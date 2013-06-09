<?php
namespace Transvision;

class ShowResults
{
    /*
     * Create an array for search results with this format:
     * 'entity' => ['locale 1', 'locale 2']
     */
    public function getTMXResults($entities, $locale1Strings, $locale2Strings)
    {
        $searchResults = array();

        foreach ($entities as $entity) {
            $locale1Strings[$entity] = (isset($locale1Strings[$entity]) && $locale1Strings[$entity] !='') ?
                $locale1Strings[$entity] : false;
            $locale2Strings[$entity] = (isset($locale2Strings[$entity]) && $locale2Strings[$entity] !='') ?
                $locale2Strings[$entity]: false;
            $searchResults[$entity] = array($locale1Strings[$entity], $locale2Strings[$entity]);
        }
        return $searchResults;
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
        $chunk  = explode('/', $chunk[0]);
        $repo   = '<span class="green">' . array_shift($chunk) . '</span>';

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

        switch($locale) {
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
    public static function resultsTable($searchResults, $recherche, $locale1, $locale2, $searchOptions)
    {
        $direction1 = RTLSupport::getDirection($locale1);
        $direction2 = RTLSupport::getDirection($locale2);

        // Get cached bugzilla components (languages list) or connect to Bugzilla API to retrieve them
        $bzComponent = rawurlencode(
                            Utils::collectLanguageComponent(
                                $locale2,
                                Utils::getBugzillaComponents()
        ));

        $bzLink = 'https://bugzilla.mozilla.org/enter_bug.cgi?format=__default__&component='
                   . $bzComponent
                   . '&product=Mozilla%20Localizations&status_whiteboard=%5Btransvision-feedback%5D';

        $table  = "<table>
                      <tr>
                        <th>Entity</th>
                        <th>$locale1</th>
                        <th>$locale2</th>
                      </tr>";

        if (!$searchOptions['whole_word'] && !$searchOptions['perfect_match']) {
            $recherche = Utils::uniqueWords($recherche);
        } else {
            $recherche = array($recherche);
        }

        foreach ($searchResults as $key => $strings) {

            // Don't highlight search matchs in entities when searching strings
            if ($searchOptions['search_type'] == 'strings') {
                $resultEntity = ShowResults::formatEntity($key);
            } else {
                $resultEntity = ShowResults::formatEntity($key, $recherche[0]);
            }

            $sourceString = trim($strings[0]);
            $targetString = trim($strings[1]);

            // Link to entity
            $entityLink = "?sourcelocale={$locale1}&locale={$locale2}&repo={$searchOptions['repo']}&search_type=entities&recherche={$key}</a>";

            // Bugzilla GET data
            $bugSummary = rawurlencode("Translation update proposed for ${key}");
            $bugMessage = rawurlencode(html_entity_decode(
                "The string:\n{$sourceString}\n\nIs translated as:\n{$targetString}\n\nAnd should be:\n\n\n\nFeedback via Transvision:\nhttp://transvision.mozfr.org/{$entityLink}"));

            foreach ($recherche as $val) {
                $sourceString = Utils::markString($val, $sourceString);
                $targetString = Utils::markString($val, $targetString);
            }

            $sourceString = Utils::highlightString($sourceString);
            $targetString = Utils::highlightString($targetString);

            $replacements = array(
                ' '        => '<span class="highlight-gray" title="Non breakable space"> </span>', // nbsp highlight
                ' '        => '<span class="highlight-red" title="Thin space"> </span>', // thin space highlight
                '…'        => '<span class="highlight-gray">…</span>', // right ellipsis highlight
                '&hellip;' => '<span class="highlight-gray">…</span>', // right ellipsis highlight
            );

            $targetString = Strings::multipleStringReplace($replacements, $targetString);

            $temp = explode('-', $locale1);
            $locale1ShortCode = $temp[0];

            $temp = explode('-', $locale2);
            $locale2ShortCode = $temp[0];

            $locale1Path = Utils::pathFileInRepo($locale1, $searchOptions['repo'], $key);
            $locale2Path = Utils::pathFileInRepo($locale2, $searchOptions['repo'], $key);

            // errors
            $errorMessage = '';

            // check for final dot
            if (substr(strip_tags($sourceString), -1) == '.'
                && substr(strip_tags($targetString), -1) != '.') {
                $errorMessage = '<em class="error"> No final dot?</em>';
            }

            // check abnormal string length
            $lengthDiff = Utils::checkAbnormalStringLength($sourceString, $targetString);
            if ($lengthDiff) {
                switch ($lengthDiff) {
                    case 'small':
                        $errorMessage = $errorMessage . '<em class="error"> Small string?</em>';
                        break;
                    case 'large':
                        $errorMessage = $errorMessage . '<em class="error"> Large String?</em>';
                        break;
                }
            }

            // Missing string error
            if (!$sourceString) {
                $sourceString = '<em class="error">warning: missing string</em>';
                $errorMessage = '';
            }
            if (!$targetString) {
                $targetString = '<em class="error">warning: missing string</em>';
                $errorMessage = '';
            }

            $table .= "
                <tr>
                  <td><a href=\"/{$entityLink}\">{$resultEntity}</a></td>

                  <td dir='{$direction1}'>
                    <div class='string'>
                      {$sourceString}
                    </div>
                    <div dir='ltr' class='infos'>
                      <a class='source_link' href='{$locale1Path}'>
                        &lt;source&gt;
                      </a>
                      <span>Translate with:</span>
                      <a href='http://translate.google.com/#{$locale1ShortCode}/{$locale2ShortCode}/"
                      . urlencode(strip_tags($sourceString))
                      . "' target='_blank'>Google</a>
                      <a href='http://www.bing.com/translator/?from={$locale1ShortCode}&to={$locale2ShortCode}&text="
                      . urlencode(strip_tags($sourceString))
                      . "' target='_blank'>BING</a>
                    </div>
                  </td>

                  <td dir='{$direction2}'>
                    <div class='string'>{$targetString}</div>
                    <div dir='ltr' class='infos'>
                      <a class='source_link' href='{$locale2Path}'>
                        &lt;source&gt;
                      </a>
                      &nbsp;
                      <a class='bug_link' target='_blank' href='{$bzLink}&short_desc={$bugSummary}&comment={$bugMessage}'>
                        &lt;report a bug&gt;
                      </a>
                      {$errorMessage}
                    </div>
                  </td>
                </tr>";
        }

        $table .= "  </table>";
        return $table;
    }
}
