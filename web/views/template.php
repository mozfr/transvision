<?php if (!valid($valid)) return; ?>

<!doctype html>

<html lang="fr" dir="ltr">
  <head>
    <title>Transvision/glossaire</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style/glossary.css" type="text/css" media="all" />
  </head>
<body>
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

