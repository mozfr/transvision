<?php
namespace tests\units\Transvision;

use atoum\atoum;
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
                'comm_l10n',
                'mail/branding/thunderbird/brand.dtd:brandFullName',
                'https://hg.mozilla.org/projects/comm-l10n/file/default/en-US/mail/branding/thunderbird/brand.dtd',
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
                'https://github.com/mozilla-l10n/firefoxios-l10n/blob/main/it/firefox-ios.xliff',
            ],
            [
                'sr',
                'mozilla_org',
                'mozilla_org/en/brands.ftl:-brand-name-firefox-accounts',
                'https://github.com/mozilla-l10n/www-l10n/blob/master/sr/brands.ftl',
            ],
            [
                'pt-BR',
                'mozilla_org',
                'mozilla_org/en/brands.ftl:-brand-name-firefox-esr',
                'https://github.com/mozilla-l10n/www-l10n/blob/master/pt-BR/brands.ftl',
            ],
            [
                'fr',
                'unknown',
                'test/file.properties',
                'https://github.com/mozilla-l10n/unknown/blob/main/fr/test/file.properties',
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
                'https://github.com/mozilla-l10n/firefoxios-l10n/blob/main/it/firefox-ios.xliff',
            ],
            [
                'sv-SE',
                'vpn_client',
                'vpn_client/mozillavpn.xliff.xliff:0f4d892c',
                'https://github.com/mozilla-l10n/mozilla-vpn-client-l10n/blob/main/sv_SE/mozillavpn.xliff.xliff',
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
