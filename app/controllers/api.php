<?php
namespace Transvision;

// Create our API object
$request = new API($url);

// Check if valid API call
if (! $request->isValidRequest()) {
    $json = $request->invalidAPICall();
    include VIEWS . 'json.php';
}

switch ($request->getService()) {
    case 'entity':
        $repo = $request->parameters[2];
        $entity = $request->extra_parameters['id'];
        include MODELS . 'api/entity.php';
        if (empty($json)) {
            $json = ['Invalid service'];
        }
        break;
    case 'locales':
        include MODELS . 'api/repository_locales.php';
        break;
    case 'search':
        include MODELS . 'api/repository_search.php';
        break;
    case 'tm':
        include MODELS . 'api/translation_memory.php';
        break;
    case 'repositories':
        include MODELS . 'api/repositories_list.php';
        break;
    case 'versions':
        include MODELS . 'api/versions.php';
        break;
    default:
        return false;
}

include VIEWS . 'json.php';
