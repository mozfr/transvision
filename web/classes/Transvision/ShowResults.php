<?php
namespace Transvision;

class ShowResults {

    /*
     * Create an array for search results with this format:
     * 'entity' => ['locale 1', 'locale 2']
     */
    public function getTMXResults($entities, $locale1_strings, $locale2_strings)
    {
        $search_results = array();

        foreach ($entities as $entity) {
            $search_results[$entity] = array($locale1_strings[$entity],
                                             $locale2_strings[$entity]);
        }
        return $search_results;
    }

    // XXX: this method is a work in progress (migration from a functional area)
    public function resultsTable($search_results, $recherche, $locale1,
                                    $locale2, $l10n_repo, $search_options)
    {
        // rtl support
        $direction1 = RTL::getDirection($locale1);
        $direction2 = RTL::getDirection($locale2);

        // mxr support
        $prefix = ($search_options['repo'] == 'central') ?
                        $search_options['repo']
                        : 'mozilla-' . $search_options['repo'];

        $base_url = "http://mxr.mozilla.org/";
        
        if ($l10n_repo) {
            $mxr_url = "{$base_url}l10n-{$prefix}/search?find={$locale2}/";
            $mxr_field_limit = 28 - mb_strwidth("{$locale2}/");
        } else {
            $mxr_url = "{$base_url}comm-${search_options['repo']}/search?find=";
            $mxr_field_limit = 27;
        }

        $table  = "\n\n
                   <table>\n\n
                     <tr>\n
                       <th>Entity</th>\n
                       <th>{$locale1}</th>\n
                       <th>{$locale2}</th>\n
                     </tr>\n\n";

        foreach ($search_results as $key => $strings) {
            // let's analyse the entity for the search string
            $search = explode('/', $key);

            // we chop search strings with mb_strimwidth()
            // because  of field length limits in mxr)
            $search = mb_strimwidth($search[0] . '.*' . $search[1], 0,
                                    $mxr_field_limit) . '&amp;string=' .
                                    mb_strimwidth($search[2], 0, 29);

            $mxr_link = "<a href=\"{$mxr_url}{$search}\">" . $this->formatEntity2($key) . '</a>';

            $source_string = str_replace($recherche, '<span class="red">'  . $recherche . '</span>', $strings[0]);
            $source_string = str_replace(ucwords($recherche), '<span class="red">'  . ucwords($recherche) . '</span>', $source_string);
            $source_string = str_replace(strtolower($recherche), '<span class="red">'  . strtolower($recherche) . '</span>', $source_string);

            $target_string = str_replace($recherche, '<span class="red">'  . $recherche . '</span>', $strings[1]);
            $target_string = str_replace(ucwords($recherche), '<span class="red">'  . ucwords($recherche) . '</span>', $target_string);
            $target_string = str_replace(strtolower($recherche), '<span class="red">'  . strtolower($recherche) . '</span>', $target_string);

            $target_string = str_replace(' ', '<span class="highlight-gray"> </span>', $target_string); // nbsp highlight

            $target_string = str_replace('…', '<span class="highlight-gray">…</span>', $target_string); // right ellipsis highlight
            $target_string = str_replace('&hellip;', '<span class="highlight-gray">…</span>', $target_string); // right ellipsis highlight

            $temp = explode('-', $locale1);
            $short_locale1 = $temp[0];

            $temp = explode('-', $locale2);
            $short_locale2 = $temp[0];

            $table .= "    <tr>\n";
            $table .= "      <td>" . $mxr_link . "</td>\n";
            $table .= "      <td dir='" . $direction1. "'><a href='http://translate.google.com/#$short_locale1/$short_locale2/" . urlencode(strip_tags($source_string)) ."'>". $source_string . "</a></td>\n";
            $table .= "      <td dir='" . $direction2. "'>" . $target_string . "</td>\n";
            $table .= "    </tr>\n\n";
        }

        $table .= "  </table>\n\n";
        return $table;
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
     * make an entity look nice in tables, alternate
     *
     */

    public static function formatEntity2($entity)
    {
        // let's analyse the entity for the search string
        $chunk = explode('/', $entity);
        // let's format the entity key to look better
        $chunk[0] = '<span class="green">' . $chunk[0] . '</span>';
        $chunk[1] = '<span class="blue">' .  $chunk[1] . '</span>';
        $chunk[2] = '<br><span class="green">' .   $chunk[2] . '</span>';
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

    public function foo()
    {
        return 'bar';
    }

}
