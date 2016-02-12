<?php
    if ($controller == 'mainsearch') {
        ?>
<p class="api_link">
    <span>API</span>These results are also available as an API request for <a href="<?=\Transvision\Utils::redirectToAPI()?>"><?=$requested_sourcelocale?></a> or <a href="<?=\Transvision\Utils::redirectToAPI(true)?>"><?=$requested_locale?></a>.<br>
    <a href="https://github.com/mozfr/transvision/wiki/JSON-API">Learn more about the Transvision API</a>.
</p>
<?php

    } else {
        ?>
<p class="api_link">
    <span>API</span>These results are also available as an <a href="<?=\Transvision\Utils::redirectToAPI()?>">API request</a>.<br>
    <a href="https://github.com/mozfr/transvision/wiki/JSON-API">Learn more about the Transvision API</a>.
</p>
<?php

    }
?>
