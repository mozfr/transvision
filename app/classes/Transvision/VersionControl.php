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
            'git' => ['mozilla_org'],
            'hg'  => [],
            'svn' => ['firefox_ios'],
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

        // remove entity from path and store it in a variable
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
                ['apps', 'shared', 'showcase_apps',
                      'test_apps', 'test_external_apps', ]
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

        $en_US_Folder_Mess = [
            'b2g/branding/official/',
            'b2g/branding/unofficial/',
            'b2g/',
            'netwerk/',
            'embedding/android/',
            'testing/extensions/community/chrome/',
            'intl/',
            'extensions/spellcheck/',
            'services/sync/',
            'mobile/android/branding/aurora/',
            'mobile/android/branding/official/',
            'mobile/android/branding/nightly/',
            'mobile/android/branding/unofficial/',
            'mobile/android/branding/beta/',
            'mobile/android/base/',
            'mobile/android/',
            'mobile/',
            'security/manager/',
            'toolkit/content/tests/fennec-tile-testapp/chrome/',
            'toolkit/',
            'browser/branding/aurora/',
            'browser/branding/official/',
            'browser/branding/nightly/',
            'browser/branding/unofficial/',
            'browser/',
            'devtools/client/',
            'devtools/shared/',
            'layout/tools/layout-debug/ui/',
            'dom/',
            'webapprt/',
            'chat/',
            'suite/',
            'other-licenses/branding/thunderbird/',
            'mail/branding/aurora/',
            'mail/branding/nightly/',
            'mail/',
            'mail/test/resources/mozmill/mozmill/extension/',
            'editor/ui/',
            'calendar/',
        ];

        // Destop repos
        if ($locale != 'en-US') {
            if ($repo == 'central') {
                $url .= '/l10n-central/' . $locale . '/file/default/';
            } else {
                $url .= '/releases/l10n/mozilla-' . $repo . '/' . $locale . '/file/default/';
            }
        } else {
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
                if (in_array(implode('/', $exploded_path) . '/', $en_US_Folder_Mess)) {
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
     * Generate a path to the subversion repo for the file
     *
     * @param  string $locale locale code
     * @param  string $repo   repository name
     * @param  string $path   Entity name representing the local file
     * @return string Path to the file in remote subversion repository
     */
    public static function svnPath($locale, $repo, $path)
    {
        if ($repo == 'firefox_ios') {
            $file_path = "projects/l10n-misc/trunk/firefox-ios/{$locale}/firefox-ios.xliff";
        } else {
            $file_path = '';
        }

        return 'https://viewvc.svn.mozilla.org/vc/'
               . $file_path . '?view=markup';
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
            case 'mozilla_org':
            default:
                $repo = 'www.mozilla.org';
                break;
        }

        return 'https://github.com/mozilla-l10n/'
               . $repo . '/blob/master/'
               . $locale . '/' . self::extractFilePath($path);
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
