<?php
$check['repo'] = isset($check['repo']) ? $check['repo'] : 'central';
$sourceLocale = isset($sourceLocale) ? $sourceLocale : 'en-US';
$locale = isset($locale) ? $locale : 'fr';
$initial_search = isset($initial_search) ? $initial_search : 'Bookmarks';
$initial_search = isset($initial_search) ? $initial_search : 'Bookmarks';

$links = '
<ul>
    <li><a href="/" title="Main search">Home</a></li>
    <li><a ' . (isset($_GET['t2t']) ? 'class="select" ' : '') . 'href="/?sourcelocale=' . $sourceLocale . '&locale=' . $locale . '&repo=' . $check['repo'] . '&t2t=t2t&recherche=' . $initial_search . '" title="Search in the Glossary">Glossary</a></li>
    <li><a ' . ($url['path'] == 'accesskeys' ? 'class="select" ' : '') . 'href="/accesskeys/" title="Check your access keys">Access Keys</a></li>
    <li><a ' . ($url['path'] == 'channelcomparison' ? 'class="select" ' : '') . 'href="/channelcomparison/" title="Compare strings from channel to channel">Channel Comparison</a></li>
    <li><a ' . ($url['path'] == 'gaia' ? 'class="select" ' : '') . 'href="/gaia/" title="Compare strings across Gaia channels">Gaia Comparison</a></li>
    <li><a ' . ($url['path'] == 'downloads' ? 'class="select" ' : '') . 'href="/downloads/" title="Download TMX files">TMX Download</a></li>
    <li><a ' . ($url['path'] == 'stats' ? 'class="select" ' : '') . 'href="/stats/" title="Light usage statistics">Statistics</a></li>
    <li><a ' . ($url['path'] == 'showrepos' ? 'class="select" ' : '') . 'href="/showrepos/" title="Repository status overview">Status Overview</a></li>
    <li><a ' . ($url['path'] == 'showrepos' ? 'class="select" ' : '') . 'href="/variables/" title="Check what variable differences there are from English">Check Variables</a></li>
    <li><a ' . ($url['path'] == 'credits' ? 'class="select" ' : '') . 'href="/credits/" title="Transvision Credits page">Credits</a></li>
</ul>
';

if (strpos(VERSION, 'dev') !== false) {
  $beta_version = true;
  $title_productname = 'Transvision Beta';
} else {
  $beta_version = false;
  $title_productname = 'Transvision';
}

?>
<!doctype html>

<html lang="<?=$locale?>" dir="ltr">
  <head>
    <title><?php if($show_title == true){ echo $page_title . ' | '; } ?><?=$title_productname?></title>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="/style/new_glossary.css" type="text/css" media="all" />
    <link rel="shortcut icon" type="image/x-icon" href="http://www.mozfr.org/favicon.ico" />
  </head>
<body id="<?=$page?>">
  <?php
  if ($beta_version) {
    echo "<div id='beta-badge'><span>BETA VERSION</span></div>\n";
  }
  ?>
  <div id="links-top" class="links"><?=$links?></div>
  <h1><?=$title?></h1>
  <?php if($experimental == true){ ?>
  <h2 id="experimental" class="alert">experimental View</h2>
  <?php } ?>

  <?php if($show_title == true){ ?>
  <h2 id="title-page"><?=$page_title?></h2>
  <h3 id="descr-page"><?=$page_descr?></h3>
  <?php } ?>

  <?=$extra?>
  <?=$content?>

  <div id="links-bottom" class="links"><?=$links?></div>
  <footer>Transvision is a tool provided by the French Mozilla community, <a href="http://www.mozfr.org" title="Home of MozFR, the French Mozilla Community" hreflang="fr">MozFR</a>.</footer>

</body>
</html>
