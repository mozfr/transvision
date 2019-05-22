<?php
namespace tests\units\Transvision;

use atoum;
use Transvision\VersionControl as _VersionControl;

require_once __DIR__ . '/../bootstrap.php';

class VersionControl extends atoum\test
{
    public function getVCSDP()
    {
        return [
            [
                'mozilla_org', 'git',
            ],
            [
                'gecko_strings', 'hg',
            ],
            [
                'firefox_ios', 'git',
            ],
            [
                'focus_ios', 'git',
            ],
        ];
    }

    /**
     * @dataProvider getVCSDP
     */
    public function testGetVCS($a, $b)
    {
        $obj = new _VersionControl();
        $this
            ->string($obj->getVCS($a))
                ->isEqualTo($b);
    }

    public function VCSRepoNameDP()
    {
        return [
            [
                'gecko_strings', 'gecko_strings',
            ],
            [
                'mozilla_org', 'mozilla_org',
            ],
            [
                'foobar', 'foobar',
            ],
        ];
    }

    /**
     * @dataProvider VCSRepoNameDP
     */
    public function testVCSRepoName($a, $b)
    {
        $obj = new _VersionControl();
        $this
            ->string($obj->VCSRepoName($a))
                ->isEqualTo($b);
    }

    public function hgPathDP()
    {
        return [
            [
                'fr',
                'gecko_strings',
                'browser/updater/updater.ini:TitleText',
                'https://hg.mozilla.org/l10n-central/fr/file/default/browser/updater/updater.ini',
            ],
            [
                'en-US',
                'gecko_strings',
                'extensions/irc/chrome/chatzilla.properties:msg.save.files.folder',
                'https://hg.mozilla.org/chatzilla/file/default/locales/en-US/chrome/chatzilla.properties',
            ],
        ];
    }

    /**
     * @dataProvider hgPathDP
     */
    public function testHgPath($a, $b, $c, $d)
    {
        $obj = new _VersionControl();
        $this
            ->string($obj->hgPath($a, $b, $c))
                ->isEqualTo($d);
    }

    public function gitPathDP()
    {
        return [
            [
                'it',
                'firefox_ios',
                'firefox_ios/firefox-ios.xliff:0f4d892c',
                'https://github.com/mozilla-l10n/firefoxios-l10n/blob/master/it/firefox-ios.xliff',
            ],
            [
                'fr',
                'focus_ios',
                'focus_ios/focus-ios.xliff:0f4d892c',
                'https://github.com/mozilla-l10n/focusios-l10n/blob/master/fr/focus-ios.xliff',
            ],
            [
                'fr',
                'focus_android',
                'focus_android/app.po:0f4d892c',
                'https://github.com/mozilla-l10n/focus-android-l10n/blob/master/fr/locales/app.po',
            ],
            [
                'sr',
                'mozilla_org',
                'mozilla_org/download_button.lang:ab34ff81',
                'https://github.com/mozilla-l10n/www.mozilla.org/blob/master/sr/download_button.lang',
            ],
            [
                'es-ES',
                'mozilla_org',
                'mozilla_org/firefox/os/faq.lang:c71a7a50',
                'https://github.com/mozilla-l10n/www.mozilla.org/blob/master/es-ES/firefox/os/faq.lang',
            ],
            [
                'fr',
                'unknown',
                'test/file.properties',
                'https://github.com/mozilla-l10n/unknown/blob/master/fr/test/file.properties',
            ],
            [
                'en-US',
                'android_l10n',
                'android_l10n/test/values/strings.xml',
                'https://github.com/mozilla-l10n/android-l10n/blob/master/test/values/strings.xml',
            ],
            [
                'es-ES',
                'android_l10n',
                'android_l10n/test/values/strings.xml',
                'https://github.com/mozilla-l10n/android-l10n/blob/master/test/values-es-rES/strings.xml',
            ],
            [
                'de',
                'android_l10n',
                'android_l10n/test/values/strings.xml',
                'https://github.com/mozilla-l10n/android-l10n/blob/master/test/values-de/strings.xml',
            ],
        ];
    }

    /**
     * @dataProvider gitPathDP
     */
    public function testGitPath($a, $b, $c, $d)
    {
        $obj = new _VersionControl();
        $this
            ->string($obj->gitPath($a, $b, $c))
                ->isEqualTo($d);
    }

    public function getPathDP()
    {
        return [
            [
                'fr',
                'gecko_strings',
                'browser/updater/updater.ini:TitleText',
                'https://hg.mozilla.org/l10n-central/fr/file/default/browser/updater/updater.ini',
            ],
            [
                'it',
                'firefox_ios',
                'firefox_ios/firefox-ios.xliff:0f4d892c',
                'https://github.com/mozilla-l10n/firefoxios-l10n/blob/master/it/firefox-ios.xliff',
            ],
        ];
    }

    /**
     * @dataProvider getPathDP
     */
    public function testGetPath($a, $b, $c, $d)
    {
        $obj = new _VersionControl();
        $this
            ->string($obj->getPath($a, $b, $c))
                ->isEqualTo($d);
    }
}
