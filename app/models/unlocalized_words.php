<?php
namespace Transvision;

// Build arrays for the search form.
$channel_selector = Utils::getHtmlSelectOptions(
    $repos_nice_names,
    $repo,
    true
);
$target_locales_list = Utils::getHtmlSelectOptions(
    Project::getRepositoryLocales($repo),
    $locale
);

$ref_locale = Project::getReferenceLocale($repo);
$unlocalized_words = [];
$skip_pspell = true;

/*
    pspell helps getting rid of false positive results by keeping only valid
    English words. The downside is that it’s filtering out 'jargon' words that
    can be used in devtools or Mozilla-specific words.
*/
if (extension_loaded('pspell')) {
    $pspell_link = \pspell_new('en_US', '', '', '', PSPELL_FAST);
    $skip_pspell = false;
} else {
    $logger->error('Please install libpspell-dev, php5-pspell and aspell-en ' .
    'packages and make sure pspell module is enabled in PHP config.');
}

// Load reference strings.
$strings_reference = array_map('strtolower', Utils::getRepoStrings(
    $ref_locale,
    $repo
));

// Load locale strings.
$strings_locale = array_map('strtolower', Utils::getRepoStrings($locale, $repo));

/*
    Go through all strings in $strings_reference, extract valid English words
    then check if any of them is present in the localized string from
    $strings_locale.
*/
foreach ($strings_reference as $string_ref_id => $ref_words) {
    /*
        If the string is missing in the locale or has been copy pasted from
        source (e.g. not translated), skip it.
    */
    if (! isset($strings_locale[$string_ref_id])
        || ($ref_words == $strings_locale[$string_ref_id] && $locale != $ref_locale)) {
        continue;
    }

    $english_words = [];
    $locale_words = $strings_locale[$string_ref_id];

    /*
        Remove punctuation characters from the strings then explode them into
        words.
    */
    $ref_words = explode(
        ' ',
        preg_replace('/\p{P}/u', '', $ref_words)
    );
    $locale_words = explode(
        ' ',
        preg_replace('/\p{P}/u', '', $locale_words)
    );

    /*
        Only keep valid English words with more than 1 character in the current
        string.
    */
    foreach ($ref_words as $word) {
        if (strlen($word) > 1 && ! in_array($word, $english_words)) {
            // Skip pspell when extension is not loaded
            if ($skip_pspell) {
                $english_words[] = $word;
                continue;
            }

            if (pspell_check($pspell_link, $word)) {
                $english_words[] = $word;
            }
        }
    }

    /*
        Check if there is any English word in the current translated string and
        count matches.
    */
    foreach ($locale_words as $word) {
        if (in_array($word, $english_words)) {
            if (! isset($unlocalized_words[$word])) {
                $unlocalized_words[$word] = 1;
            } else {
                $unlocalized_words[$word]++;
            }
        }
    }
}
unset($strings_reference);
unset($strings_locale);

// Filtering out stop words from results at the end for performance reasons.
include INC . 'stop_word_list.php';

foreach ($unlocalized_words as $key => $value) {
    if (in_array($key, $stopwords)) {
        unset($unlocalized_words[$key]);
    }
}
unset($stopwords);

asort($unlocalized_words);
