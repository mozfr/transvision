<?php
/* Webhook to update a repo for each push on GitHub. */

date_default_timezone_set('Europe/Paris');

// app variables
$app_root = realpath(__DIR__ . '/../');
$composer = $app_root . '/composer.phar';

// git variables
$branch = 'master';
$header = 'HTTP_X_HUB_SIGNATURE';
$secret = parse_ini_file($app_root . '/app/config/config.ini')['github_key'];

// Logging function to output content to /github_log.txt
function logHookResult($message, $success = false)
{
    $log_headers = "$message\n";
    if (! $success) {
        foreach ($_SERVER as $header => $value) {
            $log_headers .= "$header: $value \n";
        }
    }
    file_put_contents(__DIR__ . '/github_log.txt', $log_headers);
}

// CHECK: Download composer in the app root if it is not already there
if (! file_exists($composer)) {
    file_put_contents(
        $composer,
        file_get_contents('https://getcomposer.org/composer.phar')
    );
}

if (isset($_SERVER[$header])) {
    $validation = hash_hmac(
        'sha1',
        file_get_contents("php://input"),
        $secret
    );

    if ($validation == explode('=', $_SERVER[$header])[1]) {
        // Pull latest changes
        exec("git checkout $branch ; git pull origin $branch");

        // Install or update dependencies
        if (file_exists($composer)) {
            chdir($app_root);

            // www-data does not have a HOME or COMPOSER_HOME, create one
            if (! is_dir("{$app_root}/cache/.composer")) {
                mkdir("{$app_root}/cache/.composer");
            }

            putenv("COMPOSER_HOME={$app_root}/cache/.composer");

            if (file_exists($app_root . '/vendor')) {
                exec("php {$composer} update > /dev/null 2>&1");
            } else {
                exec("php {$composer} install > /dev/null 2>&1");
            }
        }

        // Delete cache
        exec("rm {$app_root}/cache/*.cache > /dev/null 2>&1");

        // Execute setup.sh to update potential project structure changes
        exec("{$app_root}/app/scripts/setup.sh > /dev/null 2>&1");

        // Generate API documentation
        chdir($app_root);
        exec('php vendor/bin/phpdoc -d "./app/classes/Transvision/" -t "web/docs/" --template="clean" --title="Transvision classes documentation" > /dev/null 2>&1');

        logHookResult('Last update: ' . date('d-m-Y H:i:s'), true);
    } else {
        logHookResult('Invalid GitHub secret');
    }
} else {
    logHookResult("{$header} header missing, define a secret key for your project in GitHub");
}
