<?php

// Include the common simple search form
include __DIR__ . '/simplesearchform.php';
?>

<h3>Health Status for <?=$page_locale?></h3>
<div id="wrapper">
<?php
    if (empty($active_projects)):
?>
    <div class="metrics">
        <p>The requested locale doesn't have any supported project.</p>
    </div>
<?php
    else:
?>
    <div class="metrics">
        <h4>Active projects:</h4>
        <ul>
            <?=$active_projects?>
        </ul>
        <h4>General metrics:</h4>
        <ul>
            <li class="metric">Global completion: <span class="completion" style="background-color:rgb(<?=$color?>)">~<?=$completion?>%</span></li>
        </ul>
    </div>

    <div class="tabs">
        <ul class="tab-links">
            <?=$links?>
        </ul>

        <div class="tab-content">
            <?=$content?>
        </div>
    </div>
<?php
    endif;
?>
</div>
