<?php
namespace Transvision;

/* Global variables used across the project */

// Repositories
$repos            = Project::getRepositories();
$repos_nice_names = Project::getRepositoriesNames();
$gaia_repos       = Project::getGaiaRepositories();
$desktop_repos    = Project::getDesktopRepositories();

// Search forms
$form_search_options = ['case_sensitive', 'wild', 'whole_word',
                        'perfect_match', 't2t', 'repo', 'search_type', ];
$form_checkboxes = array_diff($form_search_options, ['repo', 'search_type']);
