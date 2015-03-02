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
            $request->error = 'Entity not available';
            $json = $request->invalidAPICall();
        }
        break;
    case 'locales':
        include MODELS . 'api/repository_locales.php';
        break;
    case 'search':
        // We chain 2 queries to match both strings and entities
        if ($request->parameters[2] == 'all') {
            $request->parameters[2] = 'entities';
            include MODELS . 'api/repository_search.php';
            $entities_json = $json;

            $request->parameters[2] = 'strings';
            include MODELS . 'api/repository_search.php';
            $strings_json = $json;

            $json = array_merge($entities_json, $strings_json);
        } else {
            include MODELS . 'api/repository_search.php';
        }
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
