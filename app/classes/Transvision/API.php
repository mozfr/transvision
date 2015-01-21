<?php
namespace Transvision;

use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * API class
 *
 * This class manages all of our calls to the API.
 * This class does not return data, it only allows validating in the controller
 * that the API query is technically correct
 *
 * Calls are like this:
 * api/<version>/<service>/<repository>/<search type>/<source locale>/<target locale>/<url escaped search>/?optional_parameter1=foo&optional_parameter2=bar
 * Example for an entity search containing bookmark:
 * http://transvison.mozfr.org/api/v1/tm/release/entity/en-US/fr/bookmark/?case_sensitive=1
 * (tm = translation memory service)
 *
 * Example for the list of locales supported for a repo:
 * http://transvison.mozfr.org/api/v1/locales/gaia/
 *
 * List of services:
 * entity:        Return translations for all locales of a Mozilla entity
 * locales:       Return all the locales supported for a repository
 * repositories : Return the list of repositories supported by Transvision that can be used in the API
 * search:        Return translations + entity as key for a locale matching a search query
 * tm:            Return translations for a locale with a quality index
 * versions:      Return the list of API versions supported with their status (stable, beta, deprecated)
 *
 * This API is versioned, currently we are at v1.
 * If we make api changes that change the results or remove services, we need to
 * create a v2 api.
 *
 * @package Transvision
 */
class API
{
    public $url;
    public $parameters;
    public $extra_parameters;
    public $api_versions = ['v1' => 'stable'];
    public $services = ['entity', 'locales', 'repositories', 'search', 'tm', 'versions'];
    public $error;
    public $logging = true;
    public $logger;
    public $valid_repositories;
    /**
     * The constructor analyzes the URL to extract its parameters
     *
     * @param array $url parsed url
     */
    public function __construct($url)
    {
        $this->url = $url;

        // We use the Monolog library to log our events
        $this->logger = new Logger('API');

        if ($this->logging) {
            $this->logger->pushHandler(new StreamHandler(INSTALL_ROOT . 'logs/api-errors.log'));
        }

        // Also log to error console in Debug mode
        if (DEBUG) {
            $this->logger->pushHandler(new ErrorLogHandler());
        }

        $this->parameters = $this->getParameters($url['path']);
        $this->extra_parameters = isset($url['query'])
            ? $this->getExtraParameters($url['query'])
            : [];
    }

    /**
     * Get the list of parameters for an API call.
     *
     * @param  string $parameters The list of parameters from the URI
     * @return array  All the main parameters for the query
     */
    public function getParameters($parameters)
    {
        $parameters = explode('/', $parameters);
        // Remove empty values
        $parameters = array_filter($parameters);
        // Remove 'api' as all API calls start with it
        array_shift($parameters);
        // Reorder keys
        $parameters = array_values($parameters);

        return array_map(
            function ($item) {
                return trim(urldecode($item));
            },
            $parameters
        );
    }

    /**
     * Get the list of extra parameters for an API call.
     *
     * @param  string $parameters The $_GET list of parameters
     * @return array  All the extra parameters as [key => value]
     */
    public function getExtraParameters($parameters)
    {
        foreach (explode('&', $parameters) as $item) {
            if (strstr($item, '=')) {
                list($key, $value) = explode('=', $item);
                $extra[$key] = $value;
            } else {
                /* Deal with empty queries such as:
                 query/?foo=
                 query/?foo
                 query/?foo&bar=toto
                */
                $extra[$item] = '';
            }
        }

        return $extra;
    }

    /**
     * Get the name of the service queried
     *
     * @return string Name of the service
     */
    public function getService()
    {
        if ($this->parameters[0] == 'versions') {
            return 'versions';
        }

        return $this->isValidService() ? $this->parameters[1] : 'Invalid service';
    }

    /**
     * Check if an API request is valid
     *
     * @return boolean True if valid request, False if invalid request
     */
    public function isValidRequest()
    {
        // No parameters passed
        if (! count($this->parameters)) {
            $this->log('No service requested');

            return false;
        }

        // The 'versions' service is special as its URL is not versioned
        if ($this->parameters[0] == 'versions') {
            return true;
        }

        // Check that we have enough parameters for a query
        if (! $this->verifyEnoughParameters(1)) {
            return false;
        }

        // Check if we (still) support this API version
        if (! in_array($this->parameters[0], array_keys($this->api_versions))) {
            $this->log("Incorrect version of API ({$this->parameters[0]})");

            return false;
        }

        // Check if the service requested exists
        if (! $this->isValidService()) {
            return false;
        }

        // Check if the call to the service is technically valid
        if (! $this->isValidServiceCall($this->parameters[1])) {
            return false;
        }

        return true;
    }

    /**
     * Check if we call a service that we do support and check that
     * the request is technically correct
     *
     * @param  string  $service The name of the service
     * @return boolean Returns True if we have a valid service call, False otherwise
     */
    private function isValidServiceCall($service)
    {
        switch ($service) {
            case 'entity':
            // ex: /api/v1/entity/mozilla_org/?id=mozilla_org/mozorg/home.lang:d9d4307d
                if (! $this->verifyRepositoryExists($this->parameters[2])) {
                    return false;
                }

                if (! isset($this->extra_parameters['id'])) {
                    $this->log("You haven't provided the entity id to search.");

                    return false;
                }

                break;
            case 'locales':
            // ex: /api/v1/locales/release/
                if (! $this->verifyEnoughParameters(3)) {
                    return false;
                }

                if (! $this->verifyRepositoryExists($this->parameters[2])) {
                    return false;
                }

                break;
            case 'search':
            // ex: /api/v1/search/string/central/en-US/fr/Bookmark/?case_sensitive=1
                if (! $this->verifyEnoughParameters(7)) {
                    return false;
                }

                if (! in_array($this->parameters[2], ['strings', 'entities', 'all'])) {
                    $this->log("'{$this->parameters[2]}' is not a type of search "
                                . "you can perform, 'strings' and 'entities' are.");

                    return false;
                }

                if (! $this->verifyRepositoryExists($this->parameters[3], true)) {
                    return false;
                }

                if (! $this->verifyLocaleExists($this->parameters[4], $this->parameters[3])) {
                    return false;
                }

                if (! $this->verifyLocaleExists($this->parameters[5], $this->parameters[3])) {
                    return false;
                }

                break;
            case 'tm':
            // ex: /api/v1/tm/release/en-US/fr/string/Home%20page/?max_results=3&min_quality=80
                if (! $this->verifyEnoughParameters(6)) {
                    return false;
                }

                if (! $this->verifyRepositoryExists($this->parameters[2], true)) {
                    return false;
                }

                if (! $this->verifyLocaleExists($this->parameters[3], $this->parameters[2])) {
                    return false;
                }

                if (! $this->verifyLocaleExists($this->parameters[4], $this->parameters[2])) {
                    return false;
                }

                break;
            case 'repositories':
                // ex: api/repositories/
                // ex: api/repositories/fr/
                // Generated from Project class
                // There is one optional parameter, a locale code
                if (isset($this->parameters[2])) {
                    $match = false;

                    foreach (Project::getRepositories() as $repository) {
                        if ($this->verifyLocaleExists($this->parameters[2], $repository)) {
                            $match = true;
                            break;
                        }
                    }

                    if (! $match) {
                        $this->log("The locale queried ({$this->parameters[2]}) is not supported");

                        return false;
                    }
                }

                break;
            case 'versions':
                // ex: api/versions/
                // No user-defined variables = nothing to check
                break;
            default:
                return false;
        }

        return true;
    }

    public function invalidAPICall()
    {
        http_response_code(400);

        return ['error' => $this->error];
    }

    /**
     * Check that we have enough parameters in the URL to satisfy the request
     *
     * @param  int     $number number of compulsory parameters
     * @return boolean True if we can satisfy the request, False if we can't
     */
    private function verifyEnoughParameters($number)
    {
        if (count($this->parameters) < $number) {
            $this->log('Not enough parameters for this query.');

            return false;
        }

        return true;
    }

    /**
     * Check that the repository asked for is one we support
     *
     * @param  string  $repository Name of the repository
     * @param  boolean $alias      Do we allow aliases for repository names,
     *                             ex: 'global', to query all repositories. Default to False
     * @return boolean True if we support this repository, False if we don't
     */
    private function verifyRepositoryExists($repository, $alias = false)
    {
        $this->valid_repositories = $alias
            ? array_merge(Project::getRepositories(), ['global'])
            : Project::getRepositories();

        if (! in_array($repository, $this->valid_repositories)) {
            $this->log("The repo queried ({$repository}) doesn't exist.");

            return false;
        }

        return true;
    }

    /**
     * Check that a locale is available for a repository
     *
     * @param  string  $locale     Locale code we want to check
     * @param  string  $repository Repository name we want to check the locale for
     * @return boolean True if we support the locale, False if we don't
     */
    private function verifyLocaleExists($locale, $repository)
    {
        if ($repository == 'global') {
            $locale_repositories = Project::getLocaleRepositories($locale);
            if (! empty($locale_repositories)) {
                return true;
            }
        }

        if (! in_array($locale, Project::getRepositoryLocales($repository))) {
            $this->log("The locale queried ({$locale}) is not "
                       . "available for the repository ({$repository}).");

            return false;
        }

        return true;
    }

    /**
     * Check if the service called is valid
     *
     * @return boolean True if the service called is valid, False otherwise
     */
    private function isValidService()
    {
        if ($this->parameters[0] == 'versions') {
            return true;
        }

        if (! $this->verifyEnoughParameters(2)) {
            return false;
        }

        if (! in_array($this->parameters[1], $this->services)) {
            $this->log("The service requested ({$this->parameters[1]}) doesn't exist");

            return false;
        }

        return true;
    }

    /**
     * Utility function to log API call errors.
     *
     * @param  string  $message
     * @return boolean True if we logged, False if we didn't log
     */
    private function log($message)
    {
        $this->error = $message;

        return $this->logging
            ? $this->logger->addWarning($message, [$this->url['path']])
            : false;
    }
}
