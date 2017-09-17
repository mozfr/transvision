<?php if (isset($filter_block)) : ?>

<div id="filters">
    <h4>Filter by folder:</h4>
    <a href="#showall" id="showall" class="filter">Show all results</a>
    <?=$filter_block;?>
</div>

<?php
endif;

if ($search->getSearchTerms() != trim($search->getSearchTerms())) {
    parse_str($_SERVER['QUERY_STRING'], $args);
    $args['recherche'] = trim($search->getSearchTerms());
    $query = urldecode(http_build_query($args));
    echo '<p id="search_warning"><strong>Warning:</strong> the current search includes leading or trailing whitespaces.<br/> <a href="/?'
        . $query . '">Click here</a> to perform the same search without whitespaces.</p>';
}

foreach ($output as $results_table) {
    print $results_table;
}

// Promote API view
include VIEWS . 'templates/api_promotion.php';
