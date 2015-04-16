<?php
namespace Transvision;

// Using Aurora as a reference for simplicity to create the list of locales
$repo = 'aurora';

require_once INC . 'l10n-init.php';

if (!file_exists(WEB_ROOT . 'p12n/searchplugins.json')) {
    echo "<p>Productization file does not exists. No value to display</p>\n";
} else {
    $json_file = file_get_contents(WEB_ROOT . 'p12n/searchplugins.json');
    $json_data = json_decode($json_file, true);

    $channels = [
        'trunk'   => 'Nightly',
        'aurora'  => 'Developer Edition',
        'beta'    => 'Beta',
        'release' => 'Release',
    ];
    $products = [
        'browser' => 'Firefox',
        'mobile'  => 'Firefox for Android',
        'suite'   => 'Seamonkey',
        'mail'    => 'Thunderbird',
    ];

    // Request parameters
    $product = !empty($_REQUEST['product']) ? $_REQUEST['product'] : 'browser';
    if (isset($_GET['locale']) && in_array($_GET['locale'], $all_locales)) {
        $locale = $_GET['locale'];
    }

    $html_output = "  <h2>Current locale: {$locale}</h2>\n";

    $target_locales_list = Utils::getHtmlSelectOptions(
        Project::getRepositoryLocales('aurora'),
        $locale
    );
    $product_selector = Utils::getHtmlSelectOptions($products, $product, true);

    $html_output .= '
   <form name="searchform" id="simplesearchform" method="get" action="">
     <fieldset id="main_search">
       <fieldset>
         <label>Locale</label>
           <select name="locale" id="locale_select">
             ' . $target_locales_list . '
           </select>
       </fieldset>
       <fieldset>
           <label>Repository</label>
           <select name="product" id="product_select">
             ' .  $product_selector . '
           </select>
       </fieldset>
       <input type="submit" value="Go" alt="Go" />
     </fieldset>
   </form>';

    $html_output .= "\n\n<div class='product'>\n" .
                    "<h3>{$products[$product]} Searchplugins</h3>\n";

    $json_locale = $json_data['locales'][$locale];

    if (isset($json_locale[$product])) {
        // This product exists for this locale
        foreach ($channels as $channel_id => $channel_name) {
            $html_output .= "<div class='channel'>\n" .
                            "  <h4>$channel_name</h4>\n";
            if (isset($json_locale[$product][$channel_id]['searchplugins'])) {
                foreach ($json_locale[$product][$channel_id]['searchplugins'] as $singlesp) {
                    $html_output .= "  <div class='searchplugin'>\n" .
                                    "    <div class='image'>\n";
                    foreach ($singlesp['images'] as $imageindex) {
                        $data_uri = str_replace('\'', '%27', $json_data['images'][$imageindex]);
                        $html_output .= "      <img src='{$data_uri}' alt='searchplugin icon' />\n";
                    }
                    $html_output .= "    </div>\n";

                    $html_output .= "    <div class='info'>\n";
                    if ($singlesp['name'] == 'not available') {
                        $html_output .= "      <p class='error'><strong>{$singlesp['name']}</strong><br/> ({$singlesp['file']})</p>\n";
                    } else {
                        $html_output .= "      <p><strong>{$singlesp['name']}</strong><br/> ({$singlesp['file']})</p>\n";
                    }

                    if (strpos($singlesp['description'], 'not available')) {
                        $html_output .= "      <p class='error'>{$singlesp['description']}</p>\n";
                    } else {
                        $html_output .= "      <p>{$singlesp['description']}</p>\n";
                    }

                    if ($singlesp['secure']) {
                        $html_output .= "      <p class='https' title='Connection over https'>URL: <a href='{$singlesp['url']}'>link</a></p>\n";
                    } else {
                        $html_output .= "      <p class='http' title='Connection over http'>URL: <a href='{$singlesp['url']}'>link</a></p>\n";
                    }
                    $html_output .= "    </div>\n" .
                                    "  </div>\n";
                }
            } else {
                // Product exists, but not on this channel
                $html_output .= "  <div class='searchplugin'>\n" .
                                "    <p class='emptysp'>Searchplugins not available for this update channel.</p>\n" .
                                "  </div>\n";
            }
            $html_output .= "</div>\n";
        }

        $html_output .= "\n<h3>Search order</h3>\n";

        if (isset($json_locale[$product])) {
            // This product exists for this locale
            foreach ($channels as $channel_id => $channel_name) {
                $html_output .= "<div class='channel'>\n" .
                                "  <h4>$channel_name</h4>\n";
                if (isset($json_locale[$product][$channel_id])) {
                    if (isset($json_locale[$product][$channel_id]['p12n'])) {
                        $p12n = $json_locale[$product][$channel_id]['p12n'];

                        $html_output .= "    <div class='searchorder'>\n" .
                                        "      <p><strong>Default:</strong> {$p12n['defaultenginename']}</p>\n" .
                                        "      <ol>\n";
                        // Search order starts from 1
                        for ($i = 1; $i <= count($p12n['searchorder']); $i++) {
                            $html_output .= "      <li>{$p12n['searchorder'][$i]}</li>\n";
                        }
                        $html_output .= "      </ol>\n" .
                                        "    </div>\n";
                    } else {
                        $html_output .= "    <div class='searchorder'>\n" .
                                        "      <p class='emptysp'>Productization data not available for this update channel.</p>\n" .
                                        "    </div>\n";
                    }
                } else {
                    // Product exists, but not on this channel
                    $html_output .= "  <div class='searchorder'>\n" .
                                    "    <p class='emptysp'>This product is not available for this update channel.</p>\n" .
                                    "  </div>\n";
                }
                $html_output .= "</div>\n";
            }
        }

        $html_output .= "\n<h3>Protocol handlers</h3>\n";
        if (isset($json_locale[$product])) {
            // This product exists for locale
            foreach ($channels as $channel_id => $channel_name) {
                $html_output .= "<div class='channel'>\n" .
                                "  <h4>$channel_name</h4>\n";
                if (isset($json_locale[$product][$channel_id])) {
                    if (isset($json_locale[$product][$channel_id]['p12n'])) {
                        $p12n = $json_locale[$product][$channel_id]['p12n'];
                        $html_output .= "  <div class='searchorder'>\n" .
                                        "    <p><strong>Feed readers:</strong></p>\n" .
                                        "    <ol>\n";
                        // Feed handlers start from 0
                        for ($i = 0; $i < count($p12n['feedhandlers']); $i++) {
                            $html_output .= "    <li><a href='{$p12n['feedhandlers'][$i]['uri']}'>{$p12n['feedhandlers'][$i]['title']}</a></li>\n";
                        }
                        $html_output .= "    </ol>\n";

                        $html_output .= "    <p><strong>Handlers version:</strong> {$p12n['handlerversion']}</p>\n";
                        foreach ($p12n['contenthandlers'] as $protocol => $handler) {
                            $html_output .= "    <p>{$protocol}</p>\n" .
                                            "    <ol>\n";
                            for ($i = 0; $i < count($handler); $i++) {
                                $html_output .= "      <li><a href='{$handler[$i]['uri']}'>{$handler[$i]['name']}</a></li>\n";
                            }
                            $html_output .= "    </ol>\n";
                        }
                        $html_output .= "  </div>\n";
                    } else {
                        $html_output .= "  <div class='searchorder'>\n" .
                                        "    <p class='emptysp'>Productization data not available for this update channel.</p>\n" .
                                        "  </div>\n";
                    }
                } else {
                    // Product exists, but not on this channel
                    $html_output .= "  <div class='searchorder'>\n" .
                                    "    <p class='emptysp'>This product is not available for this update channel.</p>\n" .
                                    "  </div>\n";
                }
                $html_output .= "</div>\n";
            }
        }
    } else {
        $html_output .= "  <p>This product is not available for this locale.</p>\n";
    }

    $html_output .= "</div>\n\n";

    echo $html_output;
}
