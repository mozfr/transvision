<?php
namespace Transvision;

$searches = [
    $source_locale => $locale1_strings,
    $locale => $locale2_strings
];

$data = [$tmx_source, $tmx_target];

// 3locales view
if ($url['path'] == '3locales') {
    $check['extra_locale'] = $locale2;
    $searches[$locale2] = $locale3_strings;
    $data[] = $tmx_target2;
}

$search_yields_results = false;

// This will hold the components names for the search filters
$components = [];

foreach ($searches as $key => $value) {
    $search_results = ShowResults::getTMXResults(array_keys($value), $data);

    $current_components = array_keys($search_results);
    $current_components = array_map(
        function($row) {
            return explode('/', $row)[0];
        },
        $current_components
    );

    $components = array_merge($components, $current_components);
    $components = array_unique($components);

    if (count($value) > 0) {
        // We have results, we won't display search suggestions but search results
        $search_yields_results = true;
        $output[$key]  = '<h2>Matching results for the string <span class="searchedTerm">'
                         . $initial_search . '</span> in ' . $key . ':</h2>';
        $output[$key] .=  ShowResults::resultsTable($search_results, $initial_search,
                                                    $source_locale, $locale, $check);
    } else {
        $output[$key]  =  "<h2>No matching results for the string "
                        . "<span class=\"searchedTerm\">{$initial_search}</span>"
                        . " for the locale {$key}</h2>";
    }
}

// Display a search hint for the closest string we have if we have no search results
if (! $search_yields_results) {
    $merged_strings = [];

    foreach ($data as $key => $values) {
        $merged_strings = array_merge($merged_strings, array_values($values));
    }

    $best_matches = Strings::getSimilar($initial_search, $merged_strings, 3);

    include VIEWS . 'results_similar.php';
    return;
} else {
    if (in_array($check['repo'], $desktop_repos)) {
        // We build the filters logic with JQuery that we will inject in template
        $javascript = <<<JS

        $('.filter').click(function(e) {
            e.preventDefault();
            $('#filters a').removeClass('selected');
            if (e.target.id == 'showall') {
                $('tr').show();
                $('#showall').addClass('selected');
            } else {
                $('tr').hide();
                $('.' + e.target.id).show();
                $('#' + e.target.id).addClass('selected');
            }
        });

        // We want URL anchors to also work as filters
        var anchor = location.hash.substring(1);
        if (anchor != '') {
            $('#' + anchor).click();
        } else {
            $('#filters a').removeClass('selected');
            $('#showall').addClass('selected');
        }

JS;
        $filter_block = '';
        foreach ($components as $value) {
            $filter_block .= " <a href='#{$value}' id='{$value}' class='filter'>{$value}</a>";
        }
    }
}
