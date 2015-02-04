<?php if(isset($filter_block)) : ?>

<div id="filters">
    <h4>Filter by folder:</h4>
    <a href="#showall" id="showall" class="filter">Show all results</a>
    <?=$filter_block;?>
</div>

<?php
endif;

// Promote API view
include VIEWS . 'templates/api_promotion.php';

foreach($output as $results_table) {
    print $results_table;
}
