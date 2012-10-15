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

// We can now initialize the application and dispatch urls
require_once $_SERVER['DOCUMENT_ROOT'] . '/inc/init.php';