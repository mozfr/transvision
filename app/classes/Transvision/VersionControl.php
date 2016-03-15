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
     * @param  string $repo repository name
     * @return string Name of the VCS or false if the repo does not exist
     */
    public static function getVCS($repo)
    {
        $vcs = [
            'git' => ['firefox_ios', 'mozilla_org'],
            'hg'  => [],
            'svn' => [],
        ];
        $vcs['hg'] = array_merge(
            Project::getDesktopRepositories(),
            Project::getGaiaRepositories(),
            $vcs['hg']
        );
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
     * @param  string $repo repository name
     * @return string Name of the VCS or unchanged $repo by default
     */
    public static function VCSRepoName($repo)
    {
        // Desktop
        if (in_array($repo, Project::getDesktopRepositories())) {
            $repo = strtoupper($repo == 'central' ? 'trunk' : $repo) . '_L10N';
        }

        // Gaia
        if (substr($repo, 0, 4) == 'gaia') {
            $repo = strtoupper($repo);
        }

        return $repo;
    }

    /**
     * Generate a path to the mercurial repo for the file
     *
     * @param  string $locale locale code
     * @param  string $repo   repository name
     * @param  string $path   Entity name representing the local file
     * @return string Path to the file in remote mercurial repository
     */
    public static function hgPath($locale, $repo, $path)
    {
        $url = 'https://hg.mozilla.org';

        // Remove entity from path and store it in a variable
        $path          = explode(':', $path);
        $path          = $path[0];
        $path          = explode('/', $path);
        $entity_file   = array_pop($path);
        $path          = implode('/', $path);
        $exploded_path = explode('/', $path);
        $base_folder   = $exploded_path[0];

        if (Strings::startsWith($repo, 'gaia')
            || in_array(
                $base_folder,
                [
                    'apps', 'shared', 'showcase_apps',
                    'test_apps', 'test_external_apps',
                ]
            )
        ) {
            $locale = Project::getLocaleInContext($locale, $repo);

            if ($repo == 'gaia') {
                $url .= '/gaia-l10n/' . $locale . '/file/default/';
            } else {
                $version = str_replace('gaia_', '', $repo);
                $url .= '/releases/gaia-l10n/v' . $version . '/' . $locale . '/file/default/';
            }

            return $url . $path . '/' . $entity_file;
        }

        $en_US_folder_mess = [
            'b2g/',
            'b2g/branding/official/',
            'b2g/branding/unofficial/',
            'browser/',
            'browser/branding/aurora/',
            'browser/branding/nightly/',
            'browser/branding/official/',
            'browser/branding/unofficial/',
            'browser/extensions/pocket/',
            'calendar/',
            'chat/',
            'devtools/client/',
            'devtools/shared/',
            'dom/',
            'editor/ui/',
            'embedding/android/',
            'extensions/spellcheck/',
            'intl/',
            'layout/tools/layout-debug/ui/',
            'mail/',
            'mail/branding/aurora/',
            'mail/branding/nightly/',
            'mail/test/resources/mozmill/mozmill/extension/',
            'mobile/',
            'mobile/android/',
            'mobile/android/base/',
            'mobile/android/branding/aurora/',
            'mobile/android/branding/beta/',
            'mobile/android/branding/nightly/',
            'mobile/android/branding/official/',
            'mobile/android/branding/unofficial/',
            'netwerk/',
            'other-licenses/branding/thunderbird/',
            'security/manager/',
            'services/sync/',
            'suite/',
            'testing/extensions/community/chrome/',
            'toolkit/',
            'toolkit/content/tests/fennec-tile-testapp/chrome/',
            'webapprt/',
        ];

        // Desktop repos
        if ($locale != 'en-US') {
            if ($repo == 'central') {
                $url .= '/l10n-central/' . $locale . '/file/default/';
            } else {
                $url .= '/releases/l10n/mozilla-' . $repo . '/' . $locale . '/file/default/';
            }
        } else {
            // Chatzilla and Venkman are in separate repositories
            if ($base_folder == 'extensions') {
                switch ($exploded_path[1]) {
                    case 'irc':
                        $url .= '/chatzilla';
                        $found_extension = true;
                        break;
                    case 'venkman':
                        $url .= '/venkman';
                        $found_extension = true;
                        break;
                    default:
                        $found_extension = false;
                        break;
                }

                if ($found_extension) {
                    return "{$url}/file/default/locales/en-US/chrome/{$entity_file}";
                }
            }

            // comm-central folders
            if (in_array(
                $base_folder,
                ['calendar', 'chat', 'editor', 'ldap', 'mail', 'mailnews', 'suite']
            )) {
                $repo_base = 'comm';
            } else {
                $repo_base = 'mozilla';
            }

            if ($repo == 'central') {
                $url .= "/${repo_base}-central/file/default/";
            } else {
                $url .= "/releases/${repo_base}-${repo}/file/default/";
            }

            $loop = true;

            while ($loop && count($exploded_path) > 0) {
                if (in_array(implode('/', $exploded_path) . '/', $en_US_folder_mess)) {
                    $path_part1 = implode('/', $exploded_path) . '/locales/en-US';
                    $pattern = preg_quote(implode('/', $exploded_path), '/');
                    $path = preg_replace('/' . $pattern . '/', $path_part1, $path, 1);
                    $loop = false;
                    break;
                } else {
                    array_pop($exploded_path);
                }
            }

            if ($loop == true) {
                $path = explode('/', $path);
                $category = array_shift($path);
                $path = $category . '/locales/en-US/' . implode('/', $path);
            }
        }

        return $url . $path . '/' . $entity_file;
    }

    /**
     * Generate a path to the GitHub repo for the file.
     * Only mozilla.org is supported for now.
     *
     * @param  string $locale locale code
     * @param  string $repo   repository name
     * @param  string $path   Entity name representing the local file
     * @return string Path to the file in remote GitHub repository
     */
    public static function gitPath($locale, $repo, $path)
    {
        switch ($repo) {
            case 'firefox_ios':
                $repo = 'firefoxios-l10n';
                $file_path = 'firefox-ios.xliff';
                break;
            case 'mozilla_org':
                $repo = 'www.mozilla.org';
                $file_path = self::extractFilePath($path);
                break;
            default:
                $file_path = $path;
                break;
        }

        return "https://github.com/mozilla-l10n/{$repo}/blob/master/{$locale}/$file_path";
    }

    /**
     * Remove entity and project name from path
     *
     * @param  string $path A Transvision file path
     * @return string The same path without the entity
     *                     and internal project name
     */
    private static function extractFilePath($path)
    {
        $path = explode(':', $path);
        $path = explode('/', $path[0]);
        array_shift($path);

        return implode('/', $path);
    }
}
