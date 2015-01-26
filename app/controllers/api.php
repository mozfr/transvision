<?php
namespace Transvision;

// Create our API object
$request = new API($url);

// Check if valid API call
if (! $request->isValidRequest()) {
    $json = $request->invalidAPICall();
    include VIEWS . 'json.php';
}

// the array stores each function so we can access it
// in log time instead of linear time, just performance
// tweak

$getServiceCB = array(
    'entity' => function() {
        $repo = $request->parameters[2];
        $entity = $request->extra_parameters['id'];
        
        include MODELS . 'api/entity.php';
        
        if (empty($json)) {
            $request->error = 'Entity not available';
            $json = $request->invalidAPICall();
        }
    } ,

    'locales' => function() {
        include MODELS . 'api/repository_locales.php';
    } ,

    'search' => function () {
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
    } ,
    
    'tm' => function () {
        include MODELS . 'api/translation_memory.php';
    } ,

    'repositories' => function () {
        include MODELS . 'api/repositories_list.php';
    } ,

    'versions' => function () {
        include MODELS . 'api/versions.php';
    }
 );

// here comes the magic
call_user_func($getServiceCB[$request->getService()]);

include VIEWS . 'json.php';
