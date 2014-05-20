<?php

// Global variable used across the project
$repos               = ['release', 'beta', 'aurora', 'central', 'gaia', 'gaia_1_1', 'gaia_1_2', 'gaia_1_3', 'gaia_1_4', 'mozilla_org'];
$repos_nice_names    = [
    'release'     => 'Release',
    'beta'        => 'Beta',
    'aurora'      => 'Aurora',
    'central'     => 'Central',
    'gaia'        => 'Gaia master',
    'gaia_1_1'    => 'Gaia 1.1',
    'gaia_1_2'    => 'Gaia 1.2',
    'gaia_1_3'    => 'Gaia 1.3',
    'gaia_1_4'    => 'Gaia 1.4',
    'mozilla_org' => 'www.mozilla.org',
];

// Repos must be sorted in this array (latest master -> older branch, etc)
$gaia_repos          = ['gaia', 'gaia_1_4', 'gaia_1_3', 'gaia_1_2', 'gaia_1_1'];
$desktop_repos       = array_diff($gaia_repos, array_diff($repos, ['mozilla_org']));
$spanishes           = ['es-AR', 'es-CL', 'es-ES', 'es-MX'];
$form_search_options = ['case_sensitive', 'wild', 'whole_word', 'perfect_match', 't2t', 'repo', 'search_type'];
$form_checkboxes     = array_diff($form_search_options, ['repo', 'search_type']);
