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
                'gaia_2_1', 'hg',
            ],
            [
                'central', 'hg',
            ],
            [
                'firefox_ios', 'svn',
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
                'gaia', 'GAIA',
            ],
            [
                'gaia_42_0', 'GAIA_42_0',
            ],
            [
                'central', 'TRUNK_L10N',
            ],
            [
                'release', 'RELEASE_L10N',
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

    public function hgFileDP()
    {
        return [
            [
                'fr',
                'beta',
                'browser/updater/updater.ini:TitleText',
                'https://hg.mozilla.org/releases/l10n/mozilla-beta/fr/file/default/browser/updater/updater.ini',
            ],
            [
                'es-ES',
                'gaia',
                'apps/settings/settings.properties:usb-tethering',
                'https://hg.mozilla.org/gaia-l10n/es/file/default/apps/settings/settings.properties',
            ],
            [
                'de',
                'gaia',
                'shared/date/date.properties:month-7-long',
                'https://hg.mozilla.org/gaia-l10n/de/file/default/shared/date/date.properties',
            ],
            [
                'sr-Cyrl',
                'gaia_2_0',
                'shared/date/date.properties:month-7-long',
                'https://hg.mozilla.org/releases/gaia-l10n/v2_0/sr-Cyrl/file/default/shared/date/date.properties',
            ],
            [
                'en-US',
                'beta',
                'browser/chrome/browser/aboutPrivateBrowsing.dtd:aboutPrivateBrowsing.info.cookies',
                'https://hg.mozilla.org/releases/mozilla-beta/file/default/browser/locales/en-US/chrome/browser/aboutPrivateBrowsing.dtd',
            ],
            [
                'en-US',
                'central',
                'devtools/shared/gclicommands.properties:appCacheViewEntryManual',
                'https://hg.mozilla.org/mozilla-central/file/default/devtools/shared/locales/en-US/gclicommands.properties',
            ],
            [
                'en-US',
                'aurora',
                'devtools/client/memory.properties:heapview.field.totalcount',
                'https://hg.mozilla.org/releases/mozilla-aurora/file/default/devtools/client/locales/en-US/memory.properties',
            ],
        ];
    }

    /**
     * @dataProvider hgFileDP
     */
    public function testHgFile($a, $b, $c, $d)
    {
        $obj = new _VersionControl();
        $this
            ->string($obj->hgPath($a, $b, $c))
                ->isEqualTo($d);
    }

    public function svnFileDP()
    {
        return [
            [
                'es-ES',
                'random_repo',
                'mozilla_org/firefox/os/faq.lang:c71a7a50',
                'https://viewvc.svn.mozilla.org/vc/?view=markup',
            ],
            [
                'it',
                'firefox_ios',
                'firefox_ios/Client/ClearPrivateData.strings:0f4d892c',
                'https://viewvc.svn.mozilla.org/vc/projects/l10n-misc/trunk/firefox-ios/it/firefox-ios.xliff?view=markup',
            ],
        ];
    }

    /**
     * @dataProvider svnFileDP
     */
    public function testSvnFile($a, $b, $c, $d)
    {
        $obj = new _VersionControl();
        $this
            ->string($obj->svnPath($a, $b, $c))
                ->isEqualTo($d);
    }

    public function gitFileDP()
    {
        return [
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
        ];
    }

    /**
     * @dataProvider gitFileDP
     */
    public function testGitFile($a, $b, $c, $d)
    {
        $obj = new _VersionControl();
        $this
            ->string($obj->gitPath($a, $b, $c))
                ->isEqualTo($d);
    }
}
