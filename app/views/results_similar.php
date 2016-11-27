<?php
namespace Transvision;

$proposed_search = $_GET;
// Reset advanced search options (no need to check in advance if they're set)
unset($proposed_search['case_sensitive'], $proposed_search['entire_string'], $proposed_search['each_word'], $proposed_search['entire_words']);

$list_items = '';
foreach ($best_matches as $match) {
    $proposed_search['recherche'] = $match;
    $query = '?' . http_build_query($proposed_search, '', '&amp;');
    $list_items .= '<li><a href="' . $query . '">' . Utils::secureText($match) . '</a></li>';
}
?>

<div class="resultsbox">
    <h3>There were no results for the string
    <span class="searchedTerm"><?=htmlentities($my_search)?></span>.
    <br>Possibly related searches:
    </h3>

    <ol class="listbox">
    <?=$list_items;?>
    </ol>
</div>
