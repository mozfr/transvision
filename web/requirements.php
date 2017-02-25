<?php

function is_cli()
{
  return !isset($_SERVER['HTTP_HOST']);
}

/**
 * Checks a configuration.
 */
function check($boolean, $message, $help = '', $fatal = false)
{
  if($boolean) {
    echo "  OK        ";
  } else {
    echo sprintf("%s     ", $fatal ? ' ERROR ' : 'WARNING');
  }
  echo sprintf("$message%s\n", $boolean ? '' : ': FAILED');

  if ( ! $boolean) {
    echo "            *** $help ***\n";
    if ($fatal)
    {
      die("You must fix this problem before resuming the check.\n");
    }
  }
}

function is_empty_dir($dir)
{
    if (($files = @scandir($dir)) && count($files) <= 2) {
        return true;
    }
    return false;
}

if (!is_cli())
{
  echo '<html><body><pre>';
}

echo "************************************\n";
echo "*                                  *\n";
echo "*  transvision requirements check  *\n";
echo "*                                  *\n";
echo "************************************\n\n";

echo "Please run this script from web folder\n\n";

// check php version
check(version_compare(phpversion(), '5.4', '>='), sprintf('PHP version is at least 5.4 (%s)', phpversion()), 'Current version is '.phpversion(), true);

// check permissions on cache folder
clearstatcache(null, '../cache');
$cachePerms = substr( sprintf('%o', fileperms('../cache') ), -4 );
check($cachePerms == '0777', 'Cache folder is writable', sprintf('Cache folder should be writable (current permissions : (%s))', $cachePerms), false );

// check if logs folder exists
check(file_exists('../logs') && is_dir('../logs'), 'Logs folder exists', 'Logs folder should exists' );

// check if TMX folder exists
check(file_exists('./TMX') && is_dir('./TMX'), 'TMX folder exists', 'TMX folder should exists', true );
check(!is_empty_dir('./TMX'), 'TMX folder has content', 'TMX folder is empty', true);


if (!is_cli())
{
  echo '</pre></body></html>';
}
 
