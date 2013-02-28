<?php

// Global variable used across the project
$repos               = array('release', 'beta', 'aurora', 'central', 'gaia');
$desktop_repos       = array_diff($repos, array('gaia'));
$spanishes           = array('es-AR', 'es-CL', 'es-ES', 'es-MX');
$tmx                 = array();
$form_search_options = array('case_sensitive', 'wild', 'whole_word', 'perfect_match', 't2t', 'repo', 'search_type');
$form_checkboxes     = array_diff($form_search_options, array('repo', 'search_type'));
