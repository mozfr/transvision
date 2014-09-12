<?php
namespace Transvision;

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

    /**
     * This array maps different subfolders name for Desktop products
     * with their display name
     */
    public static $components_names = [
        'browser'  => 'Firefox Desktop',
        'mobile'   => 'Firefox for Android',
        'mail'     => 'Thunderbird',
        'suite'    => 'SeaMonkey',
        'calendar' => 'Lightning'
    ];

    /**
     * This array stores all the repositories we support in Transvision
     */
    public static $repositories = [
        'central'     => 'Central',
        'aurora'      => 'Aurora',
        'beta'        => 'Beta',
        'release'     => 'Release',
        'gaia'        => 'Gaia master',
        'gaia_2_0'    => 'Gaia 2.0',
        'gaia_1_4'    => 'Gaia 1.4',
        'gaia_1_3'    => 'Gaia 1.3',
        'mozilla_org' => 'mozilla.org',
    ];

    /**
     * Get the list of repositories.
     *
     * @return array list of local repositories
     */
    public static function getRepositories()
    {
        return array_keys(self::$repositories);
    }

    /**
     * Get the list of repositories with their Display name.
     * The array has repo folder names as keys and Display names as value:
     * ex: ['gaia_1_4' => 'Gaia 1.4', 'mozilla_org' => 'mozilla.org']
     *
     * @return array list of local repositories and their Display names
     */
    public static function getRepositoriesNames()
    {
        return self::$repositories;
    }

    /**
     * Get the list of repositories for Gaia.
     * The list is sorted by age (latest master -> older branch)
     *
     * @return array list of local repositories for Gaia
     */
    public static function getGaiaRepositories()
    {
        $gaia_repos = array_filter(
            self::getRepositories(),
            function($value) {
                if (Strings::startsWith($value, 'gaia_')) {
                    return $value;
                }
            }
        );

        // Sort repos from latest branch to oldest branch
        rsort($gaia_repos);
        // gaia repo is the latest master branch, always first
        array_unshift($gaia_repos, 'gaia');

        return $gaia_repos;
    }

    /**
     * Get the list of repositories for Desktop Applications
     *
     * @return array list of local repositories folder names
     */
    public static function getDesktopRepositories()
    {
        return array_diff(
            array_diff(self::getRepositories(), ['mozilla_org']),
            self::getGaiaRepositories()
        );
    }

    /**
     * Get the list of locales available for a repository
     *
     * @param  string $repository Name of the folder for the repository
     * @return array  A sorted list of locales
     */
    public static function getRepositoryLocales($repository)
    {
        $locales = Files::getFilenamesInFolder(TMX . $repository . '/', ['ab-CD']);
        return array_values($locales);
    }

    /**
     * Return the reference locale for a repository
     *
     * @param  string $repository Name of the folder for the repository
     * @return string Name of the reference locale
     */
    public static function getReferenceLocale($repository)
    {
        return $repository == 'mozilla_org' ? 'en-GB' : 'en-US';
    }

    /**
     * Check if the specified repository is supported
     *
     * @param  string $repository Name of the folder for the repository
     * @return boolean True if supported repository, False if unknown
     */
    public static function isValidRepository($repository)
    {
        return in_array($repository, self::getRepositories());
    }

    /**
     * Return the correct locale code based on context
     * For example: given "es", returns "es-ES" for Bugzilla,
     * "es" for Gaia, "es-ES" for other repos.
     *
     * @param  string $locale Name of the current locale
     * @param  string $context The context we need to use this locale in
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

        // Gaia locales
        $locale_mappings['gaia'] = [
            'es-AR' => 'es',
            'es-CL' => 'es',
            'es-ES' => 'es',
            'es-MX' => 'es',
            'gu-in' => 'gu',
            'pa-in' => 'pa',
            'sr'    => 'sr-Cyrl',
        ];

        // Other contexts. At the moment, identical to Bugzilla list
        $locale_mappings['other'] = $locale_mappings['bugzilla'];

        // Use Gaia mapping for all Gaia repositories
        if (Strings::startsWith($context, 'gaia')) {
            $context = 'gaia';
        }

        // Fallback to 'other' if context doesn't exist in $locale_mappings
        $context = array_key_exists($context, $locale_mappings)
                   ? $context
                   : 'other';

        $locale = array_key_exists($locale, $locale_mappings[$context])
                  ? $locale_mappings[$context][$locale]
                  : $locale;

        return $locale;
    }

    /**
     * Return the list of components by parsing a set of entities.
     * Components are folders at the root of desktop repos ("desktop", "mobile", etc.)
     *
     * @param array containing entities associated with strings,
     * like "path/to/properties:entity" => "a string".
     * @return array List of components
     */
    public static function getComponents($strings) {
        $reference_components = array_keys($strings);
        $reference_components = array_map(
            function($row) {
                return explode('/', $row)[0];
            },
            $reference_components
        );

        return array_unique($reference_components);
    }
}
