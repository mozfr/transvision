<?php
namespace Transvision;

use Cache\Cache;

// Filtering out stop words from results.
$stopwords = ['318419', '9999', '8601', '6667', '2000ms', '2000', '1990', '1024', '500', '360', '200', '140', '120', '100', '45em', '30em', '26em', '22em', '6ch', '005', '128px', 'adobe', 'android', 'ansi', 'ascii', 'aurora', 'doctype', 'e10s', 'ftp', 'gecko', 'gif', 'https', 'jpg', 'nntp', 'rgb', 'txt', 'unicode', 'usascii', 'vcard', 'wwwexamplecom', 'b-163', 'k-163', 'nist', 'secg', 'sect113r1', 'sect113r2', 'sect131r1', 'sect131r2', 'sect163k1', 'sect163r1', 'sect163r2', 'sect193r1', 'sect193r2', 'secp112r1', 'secp112r2', 'secp128r1', 'secp128r2', 'secp160k1', 'secp160r1', 'secp160r2', 'secp192k1', 'secp224k1', 'secp224r1', 'secp256k1', 'secp384r1', 'secp521r1','javascript', 'prime256v1', 'c2tnb191v2', 'sect239k1', 'c2onb239v4', 'c2onb191v5', 'c2pnb163v2', 'c2tnb191v1', 'c2pnb163v3', 'c2pnb208w1', 'c2tnb431r1', 'c2tnb239v1', 'c2tnb239v2', 'c2tnb239v3', 'sect409r1', 'c2tnb359v1', 'c2tnb191v3', 'c2pnb272w1', 'c2onb191v4', 'c2pnb368w1', 'c2onb239v5', 'c2pnb163v1', 'c2pnb176v1', 'sect233k1', 'sect409k1', 'c2pnb304w1', 'iii', 'sect233r1', 'sect283r1', 'sect283k1', 'sect571r1', 'sect571k1', 'iframe', 'enctype', 'charset', 'chrome', 'pprint', 'mozcmd', 'prime239v3', 'prime239v1', 'prime192v2', 'prime239v2', 'prime192v3', 'prime192v1', 'srcdir', 'newsrc',
];

// Build arrays for the search form.
$channel_selector = Utils::getHtmlSelectOptions(
    $repos_nice_names,
    $repo,
    true
);

// Load reference strings.
$ref_locale = Project::getReferenceLocale($repo);
$strings_reference = Utils::getRepoStrings($ref_locale, $repo);

function filter_strings($locale, $repo, $strings_reference)
{
    $strings = Utils::getRepoStrings($locale, $repo);
    foreach ($strings as $k => &$n) {
        if (! isset($strings_reference[$k])) {
            unset($strings[$k]);
            continue;
        }

        if ($strings[$k] == $strings_reference[$k]) {
            unset($strings[$k]);
            continue;
        }

        $n = strip_tags($n);
        $n = strtolower($n);
        $n = preg_replace('/\p{P}/u', '', $n);
        $n = trim($n);

        if (is_null($n)) {
            unset($strings[$k]);
            continue;
        }

        if (mb_strlen($n) < 2) {
            unset($strings[$k]);
        }
    }

    return $strings;
}

$all_locales = array_diff($all_locales, ['en-US', 'en-ZA', 'en-GB', 'ja-JP-mac', 'ltg']);

/*
    For unlocalized and unlocalized-json, the cache for $unlocalized_words
    should be the same as the cache file used for a single locale on
    unlocalized-all.
*/
switch ($page) {
    case 'unlocalized-all':
        $cache_id = $repo . $page . 'unlocalized_words';
        break;
    default:
        $cache_id = $repo . $page . $locale . 'unlocalized_words';
}

if (! $unlocalized_words = Cache::getKey($cache_id)) {
    $unlocalized_words = [];
    foreach ($all_locales as $lang) {
        // Load locale strings.
        $cache_id2 = $repo . $page . $lang . 'unlocalized_words';
        if (! $strings = Cache::getKey($cache_id2)) {
            $strings = filter_strings($lang, $repo, $strings_reference);
            Cache::setKey($cache_id2, $strings);
        }

        foreach ($strings as $id => $locale_words) {
            /*
                Check if there is any English word in the current translated string and
                count matches.
            */
            $suspicious_words = array_intersect(
                explode(' ', $locale_words),
                explode(' ', $strings_reference[$id])
            );

            foreach ($suspicious_words as $word) {
                if (mb_strlen($word) <= 2) {
                    continue;
                }

                if (in_array($word, $stopwords)) {
                    continue;
                }

                if (! isset($unlocalized_words[$word][$lang])) {
                    $unlocalized_words[$word][$lang] = 1;
                } else {
                    $unlocalized_words[$word][$lang]++;
                }
            }
        }
    }
    Cache::setKey($cache_id, $unlocalized_words);
}

unset($strings_reference, $strings, $stopwords);
arsort($unlocalized_words);
