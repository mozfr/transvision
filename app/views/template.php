<?php
ob_start();

$check['repo'] = isset($check['repo']) ? $check['repo'] : 'central';
$source_locale = isset($source_locale) ? $source_locale : 'en-US';
$locale = isset($locale) ? $locale : 'fr';
$initial_search = isset($initial_search) ? $initial_search : 'Bookmarks';
$javascript_include = isset($javascript_include) ? $javascript_include : [];
$css_include = isset($css_include) ? $css_include : [];

$links = '
<div class="linkscolumn">
  <h3>Main Views</h3>
  <ul>
    <li><a href="/" title="Main search">Home</a></li>
    <li><a ' . (isset($_GET['t2t']) ? 'class="selected_view" ' : '') . 'href="/?sourcelocale=' . $source_locale . '&locale=' . $locale . '&repo=' . $check['repo'] . '&t2t=t2t&recherche=' . $initial_search . '" title="Search in the Glossary">Glossary</a></li>
    <li><a ' . ($url['path'] == '3locales' ? 'class="selected_view" ' : '') . 'href="/3locales/" title="Search with 3 locales">3 locales</a></li>
    <li><a ' . ($url['path'] == 'string' ? 'class="selected_view" ' : '') . 'href="/string/?entity=apps/sms/sms.properties:home&repo=gaia" title="Get all translations available for an entity">Translate String</a></li>
    <li><a ' . ($url['path'] == 'downloads' ? 'class="selected_view" ' : '') . 'href="/downloads/" title="Download TMX files">TMX Download</a></li>
  </ul>
</div>
<div class="linkscolumn">
  <h3>QA Views</h3>
  <ul>
    <li><a ' . ($url['path'] == 'showrepos' ? 'class="selected_view" ' : '') . 'href="/showrepos/" title="Check the health status of locales">Health Status Overview</a></li>
    <li><a ' . ($url['path'] == 'productization' ? 'class="selected_view" ' : '') . 'href="/productization/" title="Show productization aspects">Productization</a></li>
    <li><a ' . ($url['path'] == 'accesskeys' ? 'class="selected_view" ' : '') . 'href="/accesskeys/" title="Check your access keys">Access Keys</a></li>
    <li><a ' . ($url['path'] == 'channelcomparison' ? 'class="selected_view" ' : '') . 'href="/channelcomparison/" title="Compare strings from channel to channel">Channel Comparison</a></li>
    <li><a ' . ($url['path'] == 'gaia' ? 'class="selected_view" ' : '') . 'href="/gaia/" title="Compare strings across Gaia channels">Gaia Comparison</a></li>
    <li><a ' . ($url['path'] == 'unchanged_strings' ? 'class="selected_view" ' : '') . 'href="/unchanged/" title="Display all strings identical to English">Unchanged strings</a></li>
    <li><a ' . ($url['path'] == 'variables' ? 'class="selected_view" ' : '') . 'href="/variables/" title="Check what variable differences there are from English">Check Variables</a></li>
  </ul>
</div>
<div class="linkscolumn">
  <h3>About Transvision</h3>
  <ul>
    <li><a ' . ($url['path'] == 'credits' ? 'class="selected_view" ' : '') . 'href="/credits/" title="Transvision Credits page">Credits</a></li>
    <li><a ' . ($url['path'] == 'news' ? 'class="selected_view" ' : '') . 'href="/news/" title="Changelog">Release Notes</a></li>
    <li><a ' . ($url['path'] == 'stats' ? 'class="selected_view" ' : '') . 'href="/stats/" title="Light usage statistics">Statistics</a></li>
  </ul>
</div>
';

if (strpos(VERSION, 'dev') !== false) {
  $beta_version = true;
  $title_productname = 'Transvision Beta';
} else {
  $beta_version = false;
  $title_productname = 'Transvision';
}

if (file_exists(CACHE_PATH . 'lastdataupdate.txt')) {
  $last_update = "<p>Data last updated: " .
                 date ('F d, Y \a\t H:i (e)', filemtime(CACHE_PATH . 'lastdataupdate.txt')) .
                 ".</p>\n";
} else {
  $last_update = "<p>Data last updated: not available.</p>\n";
}

?>
<!doctype html>

<html lang="en" dir="ltr">
  <head>
    <title><?php if($show_title == true){ echo $page_title . ' | '; } ?><?=$title_productname?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/style/transvision.css?<?php echo VERSION; ?>" type="text/css" media="all" />
    <?php
    foreach ($css_include as $css_file) {
        echo "<link rel=\"stylesheet\" href=\"/style/{$css_file}?" . VERSION . "\" type=\"text/css\" media=\"all\" />\n";
    }
    ?>
    <link rel="shortcut icon" type="image/x-icon" href="http://www.mozfr.org/favicon.ico" />
    <script src="/assets/jquery/jquery.min.js"></script>
    <script src="/js/base.js"></script>
    <?php
    foreach ($javascript_include as $js_file) {
        echo "    <script src=\"/js/{$js_file}\"></script>\n";
    }
    ?>
  </head>
<body id="<?=$page?>">
  <div id="links-top" class="links"><div class="container"><?=$links?></div></div>
  <div id="links-top-button-container">
    <a href="" class="menu-button" id="links-top-button" title="Display Transvision Menu"><span>menu</span></a>
  </div>
  <?php
  if ($beta_version) {
    echo "<div id='beta-badge'><span>BETA VERSION</span></div>\n";
  }
  ?>
  <h1><?=$title?></h1>
  <?php if($experimental == true): ?>
  <h2 id="experimental" class="alert">Experimental View</h2>
  <?php endif; ?>

  <?php if($show_title == true): ?>
  <h2 id="page_title"><?=$page_title?></h2>
  <h3 id="page_descrition"><?=$page_descr?></h3>
  <?php endif; ?>

  <div id="pagecontent">
    <?=$extra?>
    <?=$content?>
  </div>

  <div id="footer">
    <p>Transvision is a tool provided by the French Mozilla community, <a href="http://www.mozfr.org" title="Home of MozFR, the French Mozilla Community" hreflang="fr">MozFR</a>.</p>
    <?php echo $last_update; ?>
  </div>

</body>
</html>

<?php

$content = ob_get_contents();

ob_end_clean();

print $content;
