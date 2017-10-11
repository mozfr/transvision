<?php
namespace Transvision;

/**
 * VersionControl class
 *
 * This class is for all the methods we need to relate to our VCS
 *
 * @package Transvision
 */
class VersionControl
{
    /**
     * Get the right VCS for a given repository
     *
     * @param string $repo repository name
     *
     * @return string Name of the VCS or false if the repo does not exist
     */
    public static function getVCS($repo)
    {
        $vcs = [
            'git' => Project::$repos_lists['git'],
            'hg'  => Project::getDesktopRepositories(),
            'svn' => [],
        ];
        foreach ($vcs as $system => $repos) {
            if (in_array($repo, $repos)) {
                return $system;
            }
        }

        return false;
    }

    /**
     * Get the repo name used for VCS from the folder name used in Transvision
     *
     * @param string $repo repository name
     *
     * @return string Name of the VCS or unchanged $repo by default
     */
    public static function VCSRepoName($repo)
    {
        $mappings = [];

        return isset($mappings[$repo]) ? $mappings[$repo] : $repo;
    }

    /**
     * Generate a path to the repo for the file, depending on the VCS
     * used by this repo
     *
     * @param string $locale Locale code
     * @param string $repo   Repository name
     * @param string $path   Entity name representing the local file
     *
     * @return string Path to the file in remote repository
     */
    public static function getPath($locale, $repo, $path)
    {
        $vcs = self::getVCS($repo);

        switch ($vcs) {
            case 'git':
                $path = self::gitPath($locale, $repo, $path);
                break;
            case 'hg':
                $path = self::hgPath($locale, $repo, $path);
                break;
            default:
                $path = '';
                break;
        }

        return $path;
    }

    /**
     * Generate a path to the mercurial repo for the file
     *
     * @param string $locale Locale code
     * @param string $repo   Repository name
     * @param string $path   Entity name representing the local file
     *
     * @return string Path to the file in remote mercurial repository
     */
    public static function hgPath($locale, $repo, $path)
    {
        // Remove entity from path and store it in a variable
        $path = explode(':', $path);
        $path = $path[0];
        $path = explode('/', $path);
        $entity_file = array_pop($path);
        $path = implode('/', $path);
        $exploded_path = explode('/', $path);
        $base_folder = $exploded_path[0];

        if ($locale != 'en-US') {
            $url = "https://hg.mozilla.org/l10n-central/{$locale}/file/default/";
        } else {
            // ChatZilla is in a separate repository
            if ($base_folder == 'extensions' && $exploded_path[1] == 'irc') {
                return "https://hg.mozilla.org/chatzilla/file/default/locales/en-US/chrome/{$entity_file}";
            }
            $url = 'https://hg.mozilla.org/l10n/gecko-strings/file/default/';
        }

        return $url . $path . '/' . $entity_file;
    }

    /**
     * Generate a path to the GitHub repo for the file.
     * Only mozilla.org is supported for now.
     *
     * @param string $locale Locale code
     * @param string $repo   Repository name
     * @param string $path   Entity name representing the local file
     *
     * @return string Path to the file in remote GitHub repository
     */
    public static function gitPath($locale, $repo, $path)
    {
        if (isset(Project::$repos_info[$repo]) && isset(Project::$repos_info[$repo]['git_repository'])) {
            $repo_data = Project::$repos_info[$repo];
            $repo = $repo_data['git_repository'];
            $file_path = self::extractFilePath($path);
            if (isset($repo_data['git_subfolder'])) {
                $file_path = "{$repo_data['git_subfolder']}/{$file_path}";
            }
        } else {
            $file_path = $path;
        }

        return "https://github.com/mozilla-l10n/{$repo}/blob/master/{$locale}/$file_path";
    }

    /**
     * Remove entity and project name from path (project/file:entity)
     *
     * @param string $path A Transvision file path
     *
     * @return string The same path without the entity
     *                and internal project name
     */
    public static function extractFilePath($path)
    {
        $path = explode(':', $path);
        $path = explode('/', $path[0]);
        array_shift($path);

        return implode('/', $path);
    }
}
