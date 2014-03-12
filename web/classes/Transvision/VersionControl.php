<?php

namespace Transvision;

class VersionControl
{
    /*
     * get the path in our hg repository for a string
     *
     */

    public static function hgPath($locale, $repo, $path)
    {

        $url = 'http://hg.mozilla.org';

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
                array('apps', 'shared', 'showcase_apps',
                      'test_apps', 'test_external_apps')
            )
        ) {
            $locale = ($locale == 'es-ES') ? 'es' : $locale;

            if ($repo == 'gaia_1_1') {
                $url .= '/releases/gaia-l10n/v1_1/' . $locale . '/file/default/';
            } elseif ($repo == 'gaia_1_2') {
                $url .= '/releases/gaia-l10n/v1_2/' . $locale . '/file/default/';
            } elseif ($repo == 'gaia_1_3') {
                $url .= '/releases/gaia-l10n/v1_3/' . $locale . '/file/default/';
            } else {
                $url .= '/gaia-l10n/' . $locale . '/file/default/';
            }

            return $url . $path . '/' . $entity_file;
        }

        $en_US_Folder_Mess = array(
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
        );

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
                array('calendar', 'chat', 'editor', 'ldap', 'mail', 'mailnews', 'suite')
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
     * Generate a path to the svn repo for the file
     *
     * @param string $locale locale code
     * @param string $repo repository name
     * @param string $path Entity name representing the local file
     * @return string Path to the file in remote subversion repository
     */
    public static function svnPath($locale, $repo, $path)
    {
        $url = 'http://viewvc.svn.mozilla.org/vc/';

        // remove entity and project name from path
        $path          = explode(':', $path);
        $path          = $path[0];
        $path          = explode('/', $path);
        array_shift($path);
        $path          = implode('/', $path);

        if ($repo == 'mozilla_org') {
            $url .= 'projects/mozilla.com/trunk/locales/' . $locale . '/' . $path;
        }

        return $url . '?view=markup';
    }
}
