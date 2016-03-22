<?php
namespace Transvision;

$source_locale  = isset($source_locale) ? $source_locale : 'en-US';
$locale         = isset($locale) ? $locale : 'fr';
$initial_search = isset($initial_search) ? $initial_search : 'Bookmarks';
$base_js        = ['/js/base.js'];
$base_css       = ['transvision.css'];
$cache_bust     = '?v=' . VERSION;

if (isset($javascript_include)) {
    $javascript_include = array_merge($base_js, $javascript_include);
} else {
    $javascript_include = $base_js;
}

if (isset($css_include)) {
    $css_include = array_merge($base_css, $css_include);
} else {
    $css_include = $base_css;
}

/*
    This is a closure to build a list item containing the
    link to the page. The page we are on gets a CSS class
    of selected_view added.
*/
$li_link = function ($page, $title, $text) use ($url, $urls) {
    $link = array_search($page, $urls);

    $css_class = $url['path'] == $link ? 'class="selected_view" ' : '';
    if ($link != '/') {
        $link = "/{$link}/";
    }

    return "<li><a {$css_class} href=\"{$link}\" title=\"{$title}\">{$text}</a></li>";
};

/*
    The t2t page is a legacy page without an entry in the $urls array.
*/
$li_t2t = '<li><a ' . (isset($_GET['t2t']) ? 'class="selected_view" ' : '')
       . 'href="/?sourcelocale=' . $source_locale . '&locale=' . $locale
       . '&repo=' . $search->getRepository() . '&t2t=t2t&recherche='
       . Utils::secureText($initial_search)
       . '" title="Search in the Glossary">Glossary</a></li>';

$links = <<<EOT
<div class="linkscolumn">
  <h3>Main Views</h3>
  <ul>
    {$li_link('root', 'Main search', 'Home')}
    {$li_link('3locales', 'Search with 3 locales', '3 locales')}
    {$li_t2t}
    {$li_link('downloads', 'Download TMX files', 'TMX Download')}
  </ul>
</div>
<div class="linkscolumn" id="qa_column">
  <h3>QA Views</h3>
  <ul>
    {$li_link('keys', 'Check your access keys', 'Access Keys')}
    {$li_link('checkvariables', 'Check what variable differences there are from English', 'Check Variables')}
    {$li_link('consistency', 'Translation Consistency', 'Translation Consistency')}
    {$li_link('unchangedstrings', 'Display all strings identical to English', 'Unchanged Strings')}
    {$li_link('unlocalized', 'Display common words remaining in English', 'Unlocalized Words')}
  </ul>
  <ul>
    {$li_link('channelcomp', 'Compare strings betwen channels', 'Channel Comparison')}
    {$li_link('gaia', 'Compare strings across Gaia channels', 'Gaia Comparison')}
    {$li_link('showrepos', 'Check the health status of locales', 'Health Status Overview')}
    {$li_link('productization', 'Show productization aspects', 'Productization')}
  </ul>
</div>
<div class="linkscolumn">
  <h3>About Transvision</h3>
  <ul>
    {$li_link('credits', 'Transvision Credits page', 'Credits')}
    {$li_link('changelog', 'Release Notes', 'Release Notes')}
    {$li_link('stats', 'Light usage statistics', 'Statistics')}
  </ul>
</div>
EOT;

$title_productname = BETA_VERSION ? 'Transvision Beta' : 'Transvision';

if (file_exists(CACHE_PATH . 'lastdataupdate.txt')) {
    $last_update = "<p>Data last updated: " .
                 date('F d, Y \a\t H:i (e)', filemtime(CACHE_PATH . 'lastdataupdate.txt')) .
                 ".</p>\n";
} else {
    $last_update = "<p>Data last updated: not available.</p>\n";
}
?>
<!doctype html>

<html lang="en" dir="ltr">
  <head>
    <title><?php if ($show_title == true) {
    print $page_title . ' | ';
} ?><?= $title_productname ?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php foreach ($css_include as $css_file):?>
    <link rel="stylesheet" href="/style/<?= $css_file . $cache_bust ?>" type="text/css" media="all" />
<?php endforeach?>
    <link rel="shortcut icon" type="image/png" href="/img/logo/Icon_16x16.png" />
    <link rel="shortcut icon" type="image/svg+xml" href="/img/logo/Icon.svg" />
    <link rel="alternate" type="application/rss+xml" title="Changelog RSS" href="/rss" />
  </head>
<body id="<?= $page ?>" class="nojs">
  <script>
    document.getElementsByTagName('body')[0].className = 'jsEnabled';
  </script>
  <div id="links-top" class="links">
    <div class="container">
      <?= $links ?>
    </div>
  </div>
  <div id="links-top-button-container">
    <a href="" class="menu-button" id="links-top-button" title="Display Transvision Menu"><span>menu</span></a>
  </div>
  <?php
  if (BETA_VERSION) {
      print "<div id='beta-badge'><span>BETA VERSION</span></div>\n";
  }
  ?>
  <h1><a href="/"><img src="/img/logo/Logo_Full.svg" alt="Transvision"></a></h1>
  <?php if ($experimental == true): ?>
  <h2 id="experimental" class="alert">Experimental View</h2>
  <?php endif; ?>

  <?php if ($show_title == true): ?>
  <h2 id="page_title"><?= $page_title ?></h2>
  <h3 id="page_descrition"><?= $page_descr ?></h3>
  <?php endif; ?>

  <div id="pagecontent">
    <?= $extra ?>
    <?= $content ?>
  </div>

  <div id="noscript-warning">
    Please enable JavaScript. Some features won't be available without it.
  </div>

  <div id="footer">
    <p>Transvision is a tool provided by the French Mozilla community, <a href="https://www.mozfr.org" title="Home of MozFR, the French Mozilla Community" hreflang="fr">MozFR</a>.</p>
    <?= $last_update ?>
  </div>

  <script src="/assets/jquery/jquery.min.js?v=<?= VERSION ?>"></script>
  <script src="/assets/clipboard.js/clipboard.js-built.js?v=<?= VERSION ?>"></script>
<?php foreach ($javascript_include as $js_file):?>
  <script src="<?= $js_file . $cache_bust ?>"></script>
<?php endforeach?>

  <script>
    var supported_locales = [];
<?php
    /*
        Building array of supported locales for JavaScript functions.
        This is inline because it shouldn't be cached by the browser.
        Note: encoding array_values() instead of the array makes sure
        that json_encode returns an array and not an object.
    */
    foreach (Project::getSupportedRepositories() as $repo_id => $repo_name) {
        print "      supported_locales['{$repo_id}'] = " .
             json_encode(array_values(Project::getRepositoryLocales($repo_id))) .
             ";\n";
    }
?>
  </script>

<?php
// Piwik Optional integration in production mode
if (! LOCAL_DEV) {
    include VIEWS . 'templates/piwik.php';
}
?>
</body>
</html>
