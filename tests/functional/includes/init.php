<?php

define('INSTALL_ROOT', realpath(__DIR__ . '/../../../') . '/');

// We always work with UTF8 encoding
mb_internal_encoding('UTF-8');

// Make sure we have a timezone set
date_default_timezone_set('Europe/Paris');

require __DIR__ . '/../../../vendor/autoload.php';

// Set an environment variable so that the instance will use content from test files
putenv('AUTOMATED_TESTS=true');

// Launch PHP dev server in the background
chdir(INSTALL_ROOT);
exec('./start.sh -tests-server > /dev/null 2>&1 & echo $!', $output);

// We will need the pid to kill it, beware, this is the pid of the bash process started with start.sh
$pid = $output[0];

// Pause to let time for the dev server to launch in the background
sleep(3);
