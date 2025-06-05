<?php
namespace Transvision;

use Json\Json;

/**
 * Project class
 *
 * This is data used across the project to remove them from global scope and
 * make this data accessible from other classes.
 *
 * @package Transvision
 */
class Project
{
    /*
     * This array contains information about supported repositories.
     *
     * files: list of files to analyze during extraction
     *
     * git_repository: name of remote Git repository
     *
     * git_subfolder: if localizations are in a subdirectory, e.g. if they're
     * subfolders in /locales, value will be simply "locales" (no ending or
     * starting /)
     *
     * locale_mapping: if locale codes need to be mapped (Mozilla code -> Repo code)
     *
     * underscore_locale: set to true if using underscores as separator (en_CA)
     *                    instead of hyphens (en-CA)
     *
     * pontoon_project: name of the project in Pontoon
     *
     * source_type: source type used by the project (xliff, gettext, etc.)
     *
     * variable_patterns: list of patterns used to check for errors in variables.
     * Actual patterns (regex) are defined in the AnalyseStrings class
     *
     * @var array
     *
     */
    public static $repos_info = [
        'all_projects' => [
            /*
             *  Only one project should be set as meta, as it's used to track
             *  the repository ID that includes all other projects.
             */
            'meta' => true,
        ],
        'firefox_ios'  => [
            'git_repository'    => 'mozilla-l10n/firefoxios-l10n',
            'locale_mapping'    => [
                'bn-IN' => 'bn',
                'bn-BD' => 'bn',
                'es-ES' => 'es',
                'son'   => 'ses',
            ],
            'pontoon_project'   => 'firefox-for-ios',
            'source_type'       => 'xliff',
            'variable_patterns' => ['ios'],
        ],
        'gecko_strings'=> [
            'git_repository'    => 'mozilla-l10n/firefox-l10n',
            'git_branch'        => 'main',
            'source_type'       => 'mixed',
            'variable_patterns' => ['dtd', 'ftl', 'l10njs', 'printf', 'properties'],
        ],
        'seamonkey'    => [
            'git_repository'    => 'seamonkey-project/seamonkey-central-l10n',
            'source_type'       => 'mixed',
            'variable_patterns' => ['dtd', 'ftl', 'printf', 'properties'],
        ],
        'thunderbird'  => [
            'git_repository'    => 'thunderbird/thunderbird-l10n',
            'source_type'       => 'mixed',
            'variable_patterns' => ['dtd', 'ftl', 'printf', 'properties'],
        ],
        'mozilla_org'  => [
            'git_repository'    => 'mozilla-l10n/www-l10n',
            'git_branch'        => 'master',
            'pontoon_project'   => 'mozillaorg',
            'reference_locale'  => 'en',
            'source_type'       => 'mixed',
            'variable_patterns' => ['ftl'],
        ],
        'firefox_com'  => [
            'git_repository'    => 'mozilla-l10n/www-firefox-l10n',
            'git_branch'        => 'main',
            'pontoon_project'   => 'firefoxcom',
            'reference_locale'  => 'en',
            'source_type'       => 'mixed',
            'variable_patterns' => ['ftl'],
        ],
        'android_l10n' => [
            'git_repository'    => 'mozilla-l10n/android-l10n',
            'git_branch'        => 'master',
            'locale_mapping'    => [], // To avoid using Bugzilla
            'pontoon_project'   => 'android-l10n',
            'source_type'       => 'xml',
            'variable_patterns' => ['xml_android'],
        ],
        'vpn_client'   => [
            'git_repository'     => 'mozilla-l10n/mozilla-vpn-client-l10n',
            'underscore_locales' => true,
            'pontoon_project'    => 'mozilla-vpn-client',
            'reference_locale'   => 'en',
            'source_type'        => 'xliff',
            'variable_patterns'  => ['xliff-qt'],
        ],
    ];

    /*
        Since Project is used statically, not as an object, it would be too
        expensive to generate the list of repos dinamically from $repos_info.
    */
    public static $repos_lists = [
        // Desktop products
        'desktop'     => [
            'gecko_strings',
            'seamonkey',
            'thunderbird',
        ],
        // Products using Git
        'git'         => [
            'android_l10n', 'firefox_ios', 'gecko_strings', 'firefox_com',
            'mozilla_org', 'seamonkey', 'thunderbird', 'vpn_client',
        ],
        // Products using free text search on Pontoon
        'text_search' => [
            'firefox_ios',
            'vpn_client',
        ],
    ];

    /**
     * Create a list of all supported repositories.
     *
     * @param boolean $exclude_meta Exclude meta project
     *
     * @return array List of supported repositories, key is the repo, value is
     *               the nice name for the repo for display purposes.
     */
    public static function getSupportedRepositories($exclude_meta = false)
    {
        // Read list of repositories from supported_repositories.json
        $file_name = APP_SOURCES . 'supported_repositories.json';
        if (file_exists($file_name)) {
            $json_repositories = (new Json($file_name))->fetchContent();
        } else {
            die('ERROR: run app/scripts/setup.sh or app/scripts/dev-setup.sh to generate sources.');
        }

        $repositories = [];
        foreach ($json_repositories as $repository) {
            if ($exclude_meta && self::isMetaRepository($repository['id'])) {
                continue;
            }
            $repositories[$repository['id']] = $repository['name'];
        }

        return $repositories;
    }

    /**
     * Get the list of repositories.
     *
     * @param boolean $exclude_meta Exclude meta projects
     *
     * @return array list of local repositories values
     */
    public static function getRepositories($exclude_meta = false)
    {
        $repositories = array_keys(self::getSupportedRepositories($exclude_meta));
        sort($repositories);

        return $repositories;
    }

    /**
     * Get the list of repositories with their Display name.
     * The array has repo folder names as keys and Display names as value:
     * ex: ['firefox_ios' => 'Firefox for iOS', 'mozilla_org' => 'mozilla.org']
     *
     * @param boolean $exclude_meta Exclude meta project
     *
     * @return array list of local repositories and their Display names
     */
    public static function getRepositoriesNames($exclude_meta = false)
    {
        return self::getSupportedRepositories($exclude_meta);
    }

    /**
     * Get the list of repositories for desktop applications
     *
     * @return array list of local repositories folder names
     */
    public static function getDesktopRepositories()
    {
        return self::$repos_lists['desktop'];
    }

    /**
     * Check if the repository belongs to a desktop application
     *
     * @param string $repository ID of the repository
     *
     * @return boolean True if repository is used for a desktop application
     */
    public static function isDesktopRepository($repo)
    {
        return in_array($repo, self::getDesktopRepositories());
    }

    /**
     * Get the list of locales available for a repository, exclude a
     * subset if needed
     *
     * @param string $repository ID of the repository
     * @param array  $ignored    Array of excluded locales
     *
     * @return array A sorted list of locales
     */
    public static function getRepositoryLocales($repository, $ignored = [])
    {
        $file_name = APP_SOURCES . "{$repository}.txt";
        $supported_locales = [];
        if (file_exists($file_name)) {
            $supported_locales = file($file_name, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        }

        // Make sure that the reference locale is included
        $reference_locale = self::getReferenceLocale($repository);
        if (! in_array($reference_locale, $supported_locales)) {
            $supported_locales[] = $reference_locale;
        }
        sort($supported_locales);

        if (! empty($ignored)) {
            $supported_locales = array_diff($supported_locales, $ignored);
        }

        return $supported_locales;
    }

    /**
     * Get the list of all locales available by looking at the meta project
     *
     * @return array A sorted list of all supported locales
     */
    public static function getAllLocales()
    {
        $file_name = APP_SOURCES . 'all_projects.txt';
        $supported_locales = [];
        if (file_exists($file_name)) {
            $supported_locales = file($file_name, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        }
        sort($supported_locales);

        return $supported_locales;
    }

    /**
     * Get the list of repositories available for a locale
     *
     * @param string $locale Mozilla locale code
     *
     * @return array A sorted list of repositories available for the locale
     */
    public static function getLocaleRepositories($locale)
    {
        $matches = [];
        foreach (self::getRepositories(true) as $repository) {
            if (in_array($locale, self::getRepositoryLocales($repository))) {
                $matches[] = $repository;
            }
        }

        sort($matches);

        return $matches;
    }

    /**
     * Return the reference locale for a repository
     *
     * @param string $repository Name of the repository
     *
     * @return string Name of the reference locale
     */
    public static function getReferenceLocale($repository)
    {
        return isset(self::$repos_info[$repository]['reference_locale'])
            ? self::$repos_info[$repository]['reference_locale']
            : 'en-US';
    }

    /**
     * Return true if it's a meta repository
     *
     * @param string $repository Name of the repository
     *
     * @return boolean True if the repository is set as meta
     */
    public static function isMetaRepository($repository)
    {
        return isset(self::$repos_info[$repository]['meta'])
            ? self::$repos_info[$repository]['meta']
            : false;
    }

    /**
     * Return meta repository
     *
     * @return string ID of the meta repository
     */
    public static function getMetaRepository()
    {
        foreach (self::getRepositories() as $repository) {
            if (self::isMetaRepository($repository)) {
                return $repository;
            }
        }
    }

    /**
     * Return true if the locale is the reference locale for a repository
     *
     * @param string $locale     Locale code
     * @param string $repository Name of the repository
     *
     * @return boolean True is it's the reference locale
     */
    public static function isReferenceLocale($locale, $repository)
    {
        return self::getReferenceLocale($repository) == $locale;
    }

    /**
     * Check if the specified repository is supported
     *
     * @param string $repository Name of the repository
     *
     * @return boolean True if supported repository, False if unknown
     */
    public static function isValidRepository($repository)
    {
        return in_array($repository, self::getRepositories());
    }

    /**
     * Return the correct locale code based on context
     * For example: given "es", returns "es-ES" for Bugzilla.
     *
     * @param string $locale  Name of the current locale
     * @param string $context The context we need to use this locale in
     *
     * @return string Locale code to use in the requested context
     */
    public static function getLocaleInContext($locale, $context)
    {
        $locale_mappings = [];
        // Bugzilla locales
        $locale_mappings['bugzilla'] = [
            'es'      => 'es-ES',
            'gu'      => 'gu-IN',
            'pa'      => 'pa-IN',
            'sr-Cyrl' => 'sr',
            'sr-Latn' => 'sr',
        ];

        // Fall back to Bugzilla if there are no mappings for the requested context
        if (isset(self::$repos_info[$context]['locale_mapping'])) {
            $mapping = self::$repos_info[$context]['locale_mapping'];
        } elseif (isset($locale_mappings[$context])) {
            $mapping = $locale_mappings[$context];
        } else {
            $mapping = $locale_mappings['bugzilla'];
        }

        $locale = array_key_exists($locale, $mapping)
                  ? $mapping[$locale]
                  : $locale;

        return $locale;
    }

    /**
     * Return the list of components by parsing a set of entities.
     * Components are folders at the root of desktop repos ("desktop", "mobile", etc.)
     *
     * @param  array  Containing entities associated with strings,
     *                like "path/to/properties:entity" => "a string".
     *
     * @return array List of components
     */
    public static function getComponents($strings)
    {
        $reference_components = array_keys($strings);
        $reference_components = array_map(
            function ($row) {
                return explode('/', $row)[0];
            },
            $reference_components
        );

        return array_unique($reference_components);
    }

    /**
     * Return the name of the tool the requested locale is working in.
     *
     * @return string Name of the tool, empty if not available
     */
    public static function getLocaleTool($locale)
    {
        // Read list of tools and their supported locales from local sources
        $file_name = APP_SOURCES . 'tools.json';
        if (file_exists($file_name)) {
            $json_tools = (new Json($file_name))->fetchContent();
        } else {
            die('ERROR: run app/scripts/setup.sh or app/scripts/dev-setup.sh to generate sources.');
        }

        foreach ($json_tools as $tool => $supported_locales) {
            if (in_array($locale, $supported_locales)) {
                return $tool;
            }
        }

        return '';
    }
}
