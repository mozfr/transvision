<?php
// Safety checks to make sure we have valid Piwik values in config.ini
if (! isset($server_config['piwik_url'])
    || ! isset($server_config['piwik_id'])
    || ! filter_var($server_config['piwik_url'], FILTER_VALIDATE_URL)
    || ! filter_var($server_config['piwik_id'], FILTER_VALIDATE_INT)) {
    return;
}
?>

<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="<?=$server_config['piwik_url']?>";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', <?=$server_config['piwik_id']?>]);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<noscript><p><img src="<?=$server_config['piwik_url']?>piwik.php?idsite=<?=$server_config['piwik_id']?>" style="border:0;" alt="" /></p></noscript>
<!-- End Piwik Code -->
