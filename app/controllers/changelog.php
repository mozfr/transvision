<?php
namespace Transvision;

include MODELS . 'changelog.php';

include VIEWS . 'changelog' . ($page == 'rss' ? '_rss' : '') . '.php';
