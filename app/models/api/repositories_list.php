<?php
namespace Transvision;

// We have a query for the repositories supported by a locale
if (isset($request->parameters[2])) {
    return $json = Project::getLocaleRepositories($request->parameters[2]);
}

// Default to list all existing repositories
return $json = Project::getRepositories();
