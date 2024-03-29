<?php
namespace Transvision;

$source_locale = isset($source_locale) ? $source_locale : 'en-US';
$locale = isset($locale) ? $locale : 'fr';
$cache_bust = '?v=' . VERSION;

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

$links = <<<EOT
<div class="linkscolumn">
  <h3>Main Views</h3>
  <ul>
    {$li_link('root', 'Main search', 'Home')}
    {$li_link('3locales', 'Search with 3 locales', '3 locales')}
    {$li_link('downloads', 'Download TMX files', 'TMX Download')}
  </ul>
</div>
<div class="linkscolumn" id="qa_column">
  <h3>QA Views</h3>
  <ul>
    {$li_link('accesskeys', 'Check your access keys', 'Access Keys')}
    {$li_link('commandkeys', 'Check your keyboard shortcuts', 'Keyboard Shortcuts')}
    {$li_link('checkvariables', 'Check what variable differences there are from English', 'Check Variables')}
    {$li_link('empty_strings', 'Display empty strings in English or locale', 'Empty Strings')}
  </ul>
  <ul>
    {$li_link('unchangedstrings', 'Display all strings identical to English', 'Unchanged Strings')}
    {$li_link('unlocalized', 'Display common words remaining in English', 'Unlocalized Words')}
    {$li_link('consistency', 'Translation Consistency', 'Translation Consistency')}
  </ul>
</div>
<div class="linkscolumn">
  <h3>About Transvision</h3>
  <ul>
    {$li_link('credits', 'Transvision Credits page', 'Credits')}
    {$li_link('changelog', 'Release Notes', 'Release Notes')}
  </ul>
</div>
EOT;

$title_productname = BETA_VERSION ? 'Transvision Beta' : 'Transvision';

if (file_exists(CACHE_PATH . 'lastdataupdate.txt')) {
    $last_update = '<p>Data last updated: ' .
                 date('F d, Y \a\t H:i (e)', filemtime(CACHE_PATH . 'lastdataupdate.txt')) .
                 ".</p>\n";
} else {
    $last_update = "<p>Data last updated: not available.</p>\n";
}

$version_number = '';
if (file_exists(CACHE_PATH . 'tag.txt')) {
    $version_number = $title_productname . ' ' . file_get_contents(CACHE_PATH . 'tag.txt');
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
    <meta name="description" content="Tranvision is a tool used by the Mozilla Community to search translations extracted from products and websites. It also provides API access to translation memory, and specific QA features">
<?php foreach ($css_files as $css_file) : ?>
    <link rel="stylesheet" href="/style/<?= $css_file . $cache_bust ?>" type="text/css" media="all" />
<?php endforeach?>
    <link rel="shortcut icon" type="image/png" href="/img/logo/favicon16.png" />
    <link rel="shortcut icon" type="image/svg+xml" href="/img/logo/favicon.svg" />
    <link rel="alternate" type="application/rss+xml" title="Changelog RSS" href="/rss" />
  </head>
<body id="<?= $page ?>" class="nojs">
  <script>
    document.getElementsByTagName('body')[0].className = 'jsEnabled';
  </script>
  <div id="main-wrapper">
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
        print "<div id='beta-badge'><span>BETA</span></div>\n";
    }
    ?>
    <h1 id="logo"><a href="/"><img src="/img/logo/transvision.svg<?= $cache_bust ?>" alt="Transvision"></a></h1>
    <?php if ($experimental == true) : ?>
    <h2 id="experimental" class="alert">Experimental View</h2>
    <?php endif; ?>

    <?php if ($show_title == true) : ?>
    <h2 id="page_title"><?= $page_title ?></h2>
    <p class="page_description"><?= $page_descr ?></p>
    <?php endif; ?>

    <div id="pagecontent">
      <?= $extra ?>
      <?= $content ?>
    </div>

    <div id="noscript-warning">
      Please enable JavaScript. Some features won't be available without it.
    </div>
  </div>

  <div id="footer">
    <p>Transvision is a tool provided by the French Mozilla community, <a href="https://www.mozfr.org" title="Home of MozFR, the French Mozilla Community" hreflang="fr">MozFR</a>.</p>
    <?= $last_update ?>
    <?= $version_number ?>
  </div>

  <script src="/js/libs/jquery.min.js?v=<?= VERSION ?>"></script>
  <script src="/js/libs/clipboard.min.js?v=<?= VERSION ?>"></script>
<?php foreach ($js_files as $js_file) : ?>
  <script src="<?= $js_file . $cache_bust ?>"></script>
<?php endforeach?>

  <script>
    var supportedLocales = [];
<?php
    /*
        Building array of supported locales for JavaScript functions.
        This is inline because it shouldn't be cached by the browser.
        Note: encoding array_values() instead of the array makes sure
        that json_encode returns an array and not an object.
    */
    foreach (Project::getSupportedRepositories() as $repo_id => $repo_name) {
        print "      supportedLocales['{$repo_id}'] = " .
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
