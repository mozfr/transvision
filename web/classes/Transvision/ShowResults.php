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

    public function foo()
    {
        return 'bar';
    }
}
