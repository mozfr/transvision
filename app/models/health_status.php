<?php
namespace Transvision;

use Cache\Cache;
use VCS\Git;
use VCS\Mercurial;
use VCS\Subversion;

$projects = [];
foreach (Project::getRepositories() as $repo) {

    // Get the right locale for this repo
    $locale = Project::getLocaleInContext($page_locale, $repo);

    // We don't care about en-US
    if ($locale == 'en-US') {
        continue;
    }

    if (in_array($locale, Project::getRepositoryLocales($repo))) {
        $ref_locale = Project::getReferenceLocale($repo);

        // Get VCS data
        $repo_vcs = VersionControl::VCSRepoName($repo);

        $repo_path = $repo_vcs . '/' . $locale;

        switch (VersionControl::getVCS($repo)) {
            case 'hg':
                $vcs = new Mercurial(HG . $repo_path);
                break;
            case 'svn':
                $vcs = new Subversion(SVN . $repo_path);
                break;
            case 'git':
                $vcs = new Git(GIT . $repo_path);
                break;
        }

        // Extract, cache and store VCS data
        $cache_id = $repo_vcs . $locale . 'healthstatus2';
        if (! $stats = Cache::getKey($cache_id)) {
            /* generate data */
            $commits = $vcs->getCommits();
            $stats['commit']     = $commits[0];
            $stats['commit_sum'] = count($commits);
            $stats['repo']       = $repo;
            unset($commits);

            /* cache the data */
            Cache::setKey($cache_id, $stats);
        }

        // Utility closure to create a cache file of filtered strings
        $cache_filtered_strings = function ($lang, $cache_id) use ($repo) {
            // Get all the strings (English and locale), ignore empty entities
            $filter_empty = function ($arr) {
                // return $arr;
                return array_filter($arr, 'strlen');
            };

            if ($cache = Cache::getKey($cache_id)) {
                return $cache;
            } else {
                $filtered_strings = $filter_empty(Utils::getRepoStrings($lang, $repo));
                Cache::setKey(
                    $cache_id,
                    $filtered_strings
                );

                return $filtered_strings;
            }
        };

        $strings[$locale][$repo] = $cache_filtered_strings($locale, $locale . $repo . 'filteredstrings');
        $strings[$ref_locale][$repo] = $cache_filtered_strings($ref_locale, $ref_locale . $repo . 'filteredstrings');

        // If Desktop, parse the strings to get components
        if (in_array($repo, Project::getDesktopRepositories())) {
            foreach (Project::getComponents($strings[$locale][$repo]) as $component) {
                $filter_pattern = function ($locale_code) use ($component, $repo, $strings) {
                    return array_filter(
                        preg_grep(
                            '#^' . $component . '/.*#',
                            array_keys($strings[$locale_code][$repo])
                        ),
                        'strlen');
                };

                $locale_entities  = $filter_pattern($locale);
                $english_entities = $filter_pattern($ref_locale);

                // Skip some special cases (mostly optional strings)
                $path = [];
                switch ($component) {
                    case 'browser':
                        $path[] = $component . '/branding';
                        $path[] = $component . '/chrome/browser/devtools/styleeditor.dtd:noStyleSheet-tip';
                        $path[] = $component . '/chrome/browser-region/region.properties';
                        break;
                    case 'extensions':
                        $path[] = $component . '/irc/chrome/chatzilla.properties:pref.bugKeyword';
                        break;
                    case 'mail':
                        $path[] = $component . '/branding';
                        $path[] = $component . '/test/';
                        break;
                    case 'mobile':
                        $path[] = $component . '/android/branding';
                        $path[] = $component . '/android/defines.inc';
                        $path[] = $component . '/chrome/region.properties';
                        break;
                    case 'suite':
                        $path[] = $component . '/chrome/browser/region.properties';
                        $path[] = $component . '/chrome/common/region.properties';
                        break;
                    case 'toolkit':
                        $path[] = $component . '/content/tests/';
                        break;
                }

                $english_entities = array_filter(
                    $english_entities,
                    function ($entity) use ($path) {
                        return ! Strings::startsWith($entity, $path);
                    }
                );

                // Map the values
                foreach ($english_entities as $entity) {
                    $english_strings[$entity] = $strings[$ref_locale][$repo][$entity];

                    if (isset($strings[$locale][$repo][$entity])) {
                        $locale_strings[$entity] = $strings[$locale][$repo][$entity];
                    }
                }

                // Get pretty name for component or fallback to folder name
                $name = in_array($component, array_keys(Project::$components_names))
                        ? Project::$components_names[$component]
                        : $component;

                // Store stats and status data for current component and repo.
                $projects[$repo]['stats'] = $stats;
                $projects[$repo]['repos'][$component] = Health::getStatus(
                    $name,
                    $english_strings,
                    $locale_strings
                );

                unset($locale_entities, $english_entities, $english_strings, $locale_strings);
            }
        } else {
            // Define if grouped repos in the view then store the data in the same place
            $grouped_repos = in_array($repo, Project::getGaiaRepositories()) ? 'gaia' : 'others';

            $projects[$grouped_repos][$repo] = Health::getStatus(
                Project::getRepositoriesNames()[$repo],
                $strings[$ref_locale][$repo],
                $strings[$locale][$repo]
            );

            $projects[$grouped_repos][$repo]['stats'] = $stats;
        }

        unset($strings);
    }
}

// Build content

// Titles
$table_header = '<table class="stats-table"><tr>';
foreach (Health::getColumnsNames() as $name) {
    $table_header .= '<th>' . $name . '</th>';
}
$table_header .= '</tr>';

$html = [];
$links = '';
$first_tab = true;
$new_tab = false;

// Rows
foreach ($projects as $project => $repos) {
    foreach ($repos as $repo => $status) {
        // Continue if last commit data
        if ($repo == 'stats') {
            continue;
        }

        if (! isset($html[$project]['repos'])) {
            $html[$project]['repos'] = '';
            $html[$project]['stats'] = '';
            $new_tab = true;
        }
        switch ($project) {
            case 'gaia':
                $name = 'Gaia';
                $single_stats_pane = false;
                break;
            case 'others':
                $name = 'Other repositories';
                $repo = $project;
                $single_stats_pane = false;
                break;
            default:
                $name = Project::getRepositoriesNames()[$project];
                $single_stats_pane = true;
                break;
        }

        // Create tab-link + content div
        if ($new_tab) {
            $links .= Health::addLink($name, $project, $first_tab);
            $html[$project]['repos'] = Health::addTab($project, $first_tab)
                                     . $table_header;
        }

        // If we have more than one repo for which we need to get status
        if (! isset($status['translated'])) {
            foreach ($status as $repo => $component) {
                $html[$project]['repos'] .= Health::addRow(
                                                    Health::getColumnsKeys(),
                                                    $component
                                                );
            }
        } else {
            $html[$project]['repos'] .= Health::addRow(
                                                    Health::getColumnsKeys(),
                                                    $status
                                                );
        }
        $new_tab = $first_tab = false;
    }
}

// Closing content div for each tab, concatenate all the html
$content = '';
foreach (array_keys($html) as $project) {
    if (! empty($html[$project]['repos'])) {
        $content .= $html[$project]['repos'] . '</table>'
                 . Health::getStatsPane($projects[$project]) . '</div>';
    }
}

$active_projects = '';

// Build locales select
$target_locales_list = '';
foreach ($locales_list as $loc) {
    if ($loc == 'en-US') {
        continue;
    }
    $ch = ($loc == $locale) ? ' selected' : '';
    $target_locales_list .= "\t<option{$ch} value={$loc}>{$loc}</option>\n";
}

// Get stats
if (! empty($projects)) {
    $stats = Health::getStats($projects);
    $translated = $stats['translated'];
    $reference = $stats['total'];
    $completion = round(($translated / $reference) * 100, 2);
    $completion = $completion > 100 ? 100 : $completion;

    // Get color from completion value
    $color = Utils::redYellowGreen($completion);

    // Get active projects
    if (isset($projects['release']['repos'])) {
        $active_projects .= '<li><b>Desktop:</b> ';
        $tmp_projects = [];
        foreach (array_keys($projects['release']['repos']) as $repo) {
            if (in_array($repo, array_keys(Project::$components_names))) {
                $tmp_projects[] = Project::$components_names[$repo];
            }
        }
        $active_projects .= implode(', ', $tmp_projects) . '</li>';
    }

    if (isset($projects['gaia'])) {
        $active_projects .= '<li><b>Gaia:</b> ';
        $tmp_projects = [];
        foreach (array_keys($projects['gaia']) as $repo) {
            $tmp_projects[] = Project::getRepositoriesNames()[$repo];
        }
        $active_projects .= implode(', ', $tmp_projects) . '</li>';
    }

    if (isset($projects['others'])) {
        $active_projects .= '<li><b>Others:</b> ';
        $tmp_projects = [];
        foreach (array_keys($projects['others']) as $repo) {
            $tmp_projects[] = Project::getRepositoriesNames()[$repo];
        }
        $active_projects .= implode(', ', $tmp_projects) . '</li>';
    }
}
