<?php
namespace Transvision;

/**
 * Health class
 *
 * Helper functions for the Health view
 *
 * @package Transvision
 */
class Health
{
    /**
     * This array stores all the different columns we display in the table for
     * products health with the associated display name
     */
    protected static $columns = [
        'name'       => 'Repository name',
        'total'      => 'Total',
        'translated' => 'Translated',
        'missing'    => 'Missing',
        'identical'  => 'Identical',
    ];

    /**
     * Get the list of id for columns used in products health table
     *
     * @return array list of column ids
     */
    public static function getColumnsKeys()
    {
        return array_keys(self::$columns);
    }

    /**
     * Get the list of name for columns used in products health table
     *
     * @return array list of column names
     */
    public static function getColumnsNames()
    {
        return array_values(self::$columns);
    }

    /**
     * Get an array with the completion status by analyzing the localized strings
     * array vs. the reference strings array.
     *
     * @param  string $name        Real name of the repository that will be displayed
     *                             in the UI.
     * @param  array  $ref_strings Array containing all the reference strings of
     *                             the repo ('entity_name' => 'Content.')
     * @param  array  $loc_strings Array containing all the strings of the repo
     *                             for a given locale ('entity_name' => 'Localized content.')
     * @return array  Status for a repository of a locale
     */
    public static function getStatus($name, $ref_strings, $loc_strings)
    {
        return [
            'name'       => $name,
            'total'      => count($ref_strings),
            'translated' => count($loc_strings),
            'missing'    => count(array_diff_key($ref_strings, $loc_strings)),
            'identical'  => count(array_intersect_assoc($ref_strings, $loc_strings)),
        ];
    }

    /**
     * Get an array with the total of available strings and total of translated
     * strings for all the projects of the current locale
     *
     * @param array with statuses from all projects
     * @return array Total number of translated strings and reference strings
     */
    public static function getStats($projects)
    {
        $ref_total = $loc_translated = 0;
        foreach ($projects as $project => $repos) {
            foreach ($repos as $repo => $status) {
                // Continue if last commit data
                if (isset($status['commit'])) {
                    continue;
                }

                if (isset($status['name'])) {
                    $ref_total += $status['total'];
                    $loc_translated += $status['translated'];
                } else {
                    // Project with multiples statuses
                    foreach ($status as $component => $status_component) {
                        if (! isset($status_component['total'])) {
                            continue;
                        }
                        $ref_total += $status_component['total'];
                        $loc_translated += $status_component['translated'];
                    }
                }
            }
        }

        return [
            'total'      => $ref_total,
            'translated' => $loc_translated,
        ];
    }

    /**
     * Create a tab-link element that can be added to a list of tabs.
     *
     * @param  string  $title  Text displayed in the tab
     * @param  string  $anchor HTML anchor used to activate the associated
     *                         tab-content. Should be the same as the $id argument in Health::addTab.
     * @param  boolean $active Should this tab be the active one?
     * @return string  HTML element to add between <ul class="tab-links"> and </ul>
     */
    public static function addLink($title, $anchor, $active)
    {
        $link = '<li';
        if ($active) {
            $link .= ' class="active"';
        }
        $link .= "><a href='#{$anchor}'>{$title}</a></li>\n";

        return $link;
    }

    /**
     * Create a tab-content element that can be added to a list of tab-content.
     *
     * @param  string  $id     ID that will be used in combination with an HTML anchor
     *                         in a tab-link to show this tab-content. Should match the $anchor argument
     *                         in Health::addLink.
     * @param  boolean $active Should this tab be the active one?
     * @return string  HTML element to add between <div class="tab-content"> and
     *                        </div>.
     */
    public static function addTab($id, $active)
    {
        $tab = '<div class="';
        if ($active) {
            $tab .= 'active ';
        }
        $tab .= 'tab" id="' . $id . '">';

        return $tab;
    }

    /**
     * Create a row to be added to a table, extracting values for all the columns.
     *
     * @param  array  $col    Array containing the id of each column
     * @param  array  $status Array containing the value for each column
     * @return string HTML element to add to a table
     */
    public static function addRow($col, $status)
    {
        $row = '<tr>';
        foreach ($col as $id) {
            $row .= '<td>' . $status[$id] . '</td>';
        }

        return $row .= "</tr>\n";
    }

    /**
     * Create a tab containing the repo info
     *
     * @param  array   $data   Array containing structured commit data
     * @param  string  $id     ID that will be used in combination with an HTML anchor
     *                         in a tab-link to show this tab-content. Should match the id in $data
     *                         argument used in Health::getStatsPane.
     * @param  boolean $active Should this tab be the active one?
     * @return string  HTML element to add between <div class="tab-content"> and
     *                        </div>, or false if data are missing.
     */
    public static function getStatsTab($data, $id, $active)
    {
        // Create the tab only if we get data
        if (isset($data['commit'])) {
            $date = $data['commit']['date'];
            $tab = '';

            // Set CSS classes
            // If the latest commit is 1 year old or older
            $commit_class = $date->diff(new \DateTime('now'))->format('%y') >= 1
                            ? ' old' : '';

            $active_class = $active ? ' active' : '';

            // Build HTML
            $commit = '<b>' . Utils::ago($date) . '</b> ('
                    . $date->format('F d, Y \a\t H:i \G\M\T e')
                    . ') by ' . $data['commit']['author'];

            $tab .= '<div id="' . $id . '" class="metrics tab' . $active_class . '">'
                 . '<h4>Repo metrics:</h4><ul>'
                 . '<li class="metric' . $commit_class . '">Last commit: ' . $commit . '</li>'
                 . '<li class="metric">Number of commits: ' . Utils::pluralize($data['commit_sum'], 'commit') . '</li>'
                 . '</ul></div>';

            return $tab;
        } else {
            return false;
        }
    }

    /**
     * Create a panel containing tabs with info for all the repos of this group
     * For instance, a panel with tabs for each gaia repo.
     *
     * @param  array  $data Array containing all the data for each repo
     * @return string HTML element to add in the repo tab
     */
    public static function getStatsPane($data)
    {
        if (! isset($data['stats'])) {
            $first_tab = true;
            $links = $tabs = '';

            foreach ($data as $stats) {
                $repo_pretty_name = Project::getRepositoriesNames()[$stats['stats']['repo']];
                $links .= self::addLink(
                                    $repo_pretty_name,
                                    'sub-' . $stats['stats']['repo'],
                                    $first_tab
                                );

                $tabs .= self::getStatsTab(
                                    $stats['stats'],
                                    'sub-' . $stats['stats']['repo'],
                                    $first_tab
                                );

                $first_tab = false;
            }

            return '
            <div class="stats-panel">
                <div class="tabs">
                    <ul class="tab-links">
                        ' . $links . '
                    </ul>
                    <div class="tab-content">
                        ' . $tabs . '
                    </div>
                </div>
            </div>';
        } else {
            return '<div class="stats-panel">'
                    . self::getStatsTab(
                                $data['stats'],
                                'sub-' . $data['stats']['repo'],
                                true
                            )
                    . '</div>';
        }
    }
}
