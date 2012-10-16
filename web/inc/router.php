<?php
$url  = parse_url($_SERVER['REQUEST_URI']);
$file = pathinfo($url['path']);

// Forbid direct access to router file
if ($url['path'] == '/inc/router.php') {
    return false;
}

// Real files and folders don't get pre-processed
if (file_exists($_SERVER['DOCUMENT_ROOT'] . $url['path']) && $url['path'] != '/') {
    return false;
}

// Don't process non-PHP files, even if they don't exist on the server
if ((isset($file['extension']) && $file['extension'] != 'php')) {
    return false;
}

// check if we process this url or not
require_once __DIR__ . '/urls.php';
if ($url['path'] != '/') {
    // we clean up the path to normalize it before comparing the string to the valid paths
    $url['path'] = explode('/', $url['path']);
    $url['path'] = array_filter($url['path']);
    $url['path'] = implode('/', $url['path']);
}

if (!in_array($url['path'], $urls)) {
    return false;
}

// We can now initialize the application and dispatch urls
require_once __DIR__ . '/init.php';
