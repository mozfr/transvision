<?php
namespace Transvision;

require_once WEBROOT . 'inc/l10n-init.php';

if (!file_exists(WEBROOT . 'p12n/searchplugins.json')) {
    echo "<p>Productization file does not exists. No value to display</p>\n";
} else {
    $jsondata = file_get_contents(WEBROOT . 'p12n/searchplugins.json');
    $jsonarray = json_decode($jsondata, true);

    $product = !empty($_REQUEST['product']) ? $_REQUEST['product'] : 'browser';

    if (isset($_GET['locale']) && in_array($_GET['locale'], $all_locales)) {
       $locale = $_GET['locale'];
    }

    $channels = ['trunk', 'aurora', 'beta', 'release'];
    $products = ['browser', 'metro', 'mobile', 'suite', 'mail'];
    $productnames = ['Firefox Desktop', 'Firefox Metro', 'Firefox Mobile (Android)', 'Seamonkey', 'Thunderbird'];


    echo "  <h2>Current locale: $locale</h2>\n";

    // Using Aurora as a reference for simplicity as locale list
    $loc_list = Utils::getFilenamesInFolder(TMX . 'aurora/');
    $target_locales_list = Utils::getHtmlSelectOptions($loc_list, $locale);
    $product_selector = Utils::getHtmlSelectOptions($products, $product);

    echo '
   <form name="searchform" id="simplesearchform" method="get" action="">
     <fieldset id="main">
       <fieldset>
         <legend>Locale</legend>
           <select name="locale">
             ' . $target_locales_list .'
           </select>
       </fieldset>
       <fieldset>
           <legend>Repository</legend>
           <select name="product">
             ' .  $product_selector . '
           </select>
       </fieldset>
       <input type="submit" value="Go" alt="Go" />
     </fieldset>
   </form>';

    $i = array_search($product, $products);
    echo "\n\n   <div class='product'>\n" .
         "    <h3>{$productnames[$i]}<br/>Searchplugins</h3>\n";
    if (array_key_exists($product, $jsonarray[$locale])) {
        # This product exists for locale
        foreach ($channels as $channel) {
            echo "    <div class='channel'>\n" .
                 "      <h4>$channel</h4>\n";
            if (array_key_exists($channel, $jsonarray[$locale][$product])) {
                foreach ($jsonarray[$locale][$product][$channel] as $key => $singlesp) {
                    if ($key != 'p12n') {
                        echo "        <div class='searchplugin'>\n";
                        echo "          <div class='image'>\n";
                        foreach ($singlesp['images'] as $imageindex) {
                            echo "            <img src='" . $jsonarray['images'][$imageindex] . "' alt='searchplugin icon' />\n";
                        }
                        echo "          </div>\n";

                        echo "          <div class='info'>\n";
                        if ( $singlesp['name'] == 'not available') {
                            echo '            <p class="error"><strong>' . $singlesp['name'] . '</strong><br/> (' . $singlesp['file'] . ")</p>\n";
                        } else {
                            echo '            <p><strong>' . $singlesp['name'] . '</strong><br/> (' . $singlesp['file'] . ")</p>\n";
                        }

                        if ( strpos($singlesp['description'], 'not available')) {
                            echo '            <p class="error">' . $singlesp['description'] . "</p>\n";
                        } else {
                            echo '            <p>' . $singlesp['description'] . "</p>\n";
                        }

                        if ($singlesp['secure']) {
                            echo '            <p class="https" title="Connection over https">URL: <a href="' . $singlesp['url'] . '">link</a></p>' . "\n";
                        } else {
                            echo '            <p class="http" title="Connection over http">URL: <a href="' . $singlesp['url'] . '">link</a></p>' . "\n";
                        }
                        echo "          </div>\n";
                        echo "        </div>\n";                        }
                }
            } else {
                # Product exists, but not on this channel
                echo "      <div class='searchplugin'>\n";
                echo "        <p class='emptysp'>Searchplugins not available for this update channel.</p>\n";
                echo "      </div>\n";
            }
            echo "    </div>\n";
        }

        echo "\n    <h3>Search order</h3>\n";
        if (array_key_exists($product, $jsonarray[$locale])) {
            # This product exists for locale
            foreach ($channels as $channel) {
                echo "    <div class='channel'>\n" .
                     "      <h4>$channel</h4>\n";
                if (array_key_exists($channel, $jsonarray[$locale][$product])) {
                    if (array_key_exists('p12n', $jsonarray[$locale][$product][$channel])) {
                        $p12n = $jsonarray[$locale][$product][$channel]['p12n'];

                        echo "        <div class='searchorder'>\n";
                        echo "          <p><strong>Default:</strong> " . $p12n['defaultenginename'] . "</p>\n";
                        echo "          <ol>\n";
                        // Search order starts from 1
                        for ($i=1; $i<=count($p12n['searchorder']); $i++) {
                           echo "            <li>" . $p12n['searchorder'][$i] . "</li>\n";
                        }
                        echo "          </ol>\n";
                        echo "        </div>\n";
                    } else {
                        echo "        <div class='searchorder'>\n";
                        echo "          <p class='emptysp'>Productization data not available for this update channel.</p>\n";
                        echo "        </div>\n";
                    }
                } else {
                    # Product exists, but not on this channel
                    echo "        <div class='searchorder'>\n";
                    echo "          <p class='emptysp'>This product is not available for this update channel.</p>\n";
                    echo "        </div>\n";
                }
                echo "    </div>\n";
            }
        }

        echo "\n    <h3>Protocol handlers</h3>\n";
        if (array_key_exists($product, $jsonarray[$locale])) {
            # This product exists for locale
            foreach ($channels as $channel) {
                echo "    <div class='channel'>\n" .
                     "      <h4>$channel</h4>\n";
                if (array_key_exists($channel, $jsonarray[$locale][$product])) {
                    if (array_key_exists('p12n', $jsonarray[$locale][$product][$channel])) {
                        $p12n = $jsonarray[$locale][$product][$channel]['p12n'];
                        echo "        <div class='searchorder'>\n";
                        echo "          <p><strong>Feed readers:</strong></p>\n";
                        echo "          <ol>\n";
                        // Feed handlers start from 0
                        for ($i=0; $i<count($p12n['feedhandlers']); $i++) {
                           echo "            <li><a href='" . $p12n['feedhandlers'][$i]['uri'] . "'>" .
                                $p12n['feedhandlers'][$i]['title'] . "</a></li>\n";
                        }
                        echo "          </ol>\n";

                        echo "          <p><strong>Handlers version:</strong> " . $p12n['handlerversion'] . "</p>\n";
                        foreach ($p12n['contenthandlers'] as $protocol => $handler) {
                            echo "          <p>{$protocol}</p>\n";
                            echo "          <ol>\n";
                            for ($i=0; $i<count($handler); $i++) {
                               echo "            <li><a href='" . $handler[$i]['uri'] . "'>" .
                                    $handler[$i]['name'] . "</a></li>\n";
                            }
                            echo "          </ol>\n";
                        }
                        echo "        </div>\n";
                    } else {
                        echo "        <div class='searchorder'>\n";
                        echo "          <p class='emptysp'>Productization data not available for this update channel.</p>\n";
                        echo "        </div>\n";
                    }
                } else {
                    # Product exists, but not on this channel
                    echo "        <div class='searchorder'>\n";
                    echo "          <p class='emptysp'>This product is not available for this update channel.</p>\n";
                    echo "        </div>\n";
                }
                echo "    </div>\n";
            }
        }


    } else {
        echo "    <p>This product is not available for this locale.</p>\n";
    }

    echo "   </div>\n\n";
}
