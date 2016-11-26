<?php
namespace Transvision;

if ($controller == 'mainsearch'):
?>
<p class="api_link">
    <span>API</span>These results are also available as an API request to search in
    <a href="<?=Utils::APIPromotion($source_locale, $locale)?>"><?=$source_locale?></a> or
    <a href="<?=Utils::APIPromotion($locale, $source_locale)?>"><?=$locale?></a>.
    <br>
    <a href="https://github.com/mozfr/transvision/wiki/JSON-API">Learn more about the Transvision API</a>.
</p>
<?php else: ?>

<p class="api_link">
    <span>API</span>These results are also available as an <a href="<?=Utils::redirectToAPI()?>">API request</a>.
    <br>
    <a href="https://github.com/mozfr/transvision/wiki/JSON-API">Learn more about the Transvision API</a>.
</p>
<?php endif; ?>
