<?php if (!valid($valid)) return; ?>

<!doctype html>

<html lang="fr" dir="ltr">
  <head>
    <title>Transvision/glossaire</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/style/glossary.css" type="text/css" media="all" />
    <link rel="shortcut icon" type="image/x-icon" href="http://www.mozfr.org/favicon.ico" />
  </head>
<body id="<?=$page?>">
  <h1><?=$title?></h1>
    <?=$content?>

  <div id="links">
    <ul>
      <li><a href="./" title="Search in the Glossary">Glossary</a></li>
<!--       <li><a href="alignement.php" title="Search for similarities">Alignment</a></li>
      <li><a href="doublons.php" title="Search for Duplicates">Duplicates</a></li>
      <li><a href="entite.php" title="Search for entities">Entities</a></li>-->
      <li><a href="http://www.mozfr.org" title="Home of MozFR, the French Mozilla Community" hreflang="fr">MozFR</a></li>
    </ul>
  </div>
</body>
</html>
<?php
// insert page load time in debug mode, the $time_start variable is set in inc/init.php

if ($debug) {
    $time_end = getmicrotime();
    $time     = $time_end - $time_start;
    echo '<p><strong>Page generated in ' . $time . ' seconds</strong></p>';
    echo '<p><strong>Memory Peak: ' . number_format(memory_get_peak_usage(true), 0, '.', ' ') . '</strong></p>';
}
