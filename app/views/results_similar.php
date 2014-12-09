<?php
namespace Transvision;

$proposed_search = $_GET;

$list_items = '';
foreach ($best_matches as $match) {
    $proposed_search['recherche'] = $match;
    $query = '?' .http_build_query($proposed_search, '', '&amp;');
    $list_items .= '<li><a href="' . $query . '">' . $match . '</a></li>';
}
?>

<div class="resultsbox">
    <h3>There were no results for the string
    <span class="searchedTerm"><?=$initial_search_decoded?></span>.
    <br>Possibly related searches:
    </h3>

    <ol class="listbox">
    <?=$list_items;?>
    </ol>
</div>
