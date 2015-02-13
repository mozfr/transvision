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
                'mozilla_org', 'svn',
            ],
            [
                'gaia_1_4', 'hg',
            ],
            [
                'central', 'hg',
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
                'http://hg.mozilla.org/releases/l10n/mozilla-beta/fr/file/default/browser/updater/updater.ini',
            ],
            [
                'es-ES',
                'gaia',
                'apps/settings/settings.properties:usb-tethering',
                'http://hg.mozilla.org/gaia-l10n/es/file/default/apps/settings/settings.properties',
            ],
            [
                'de',
                'gaia',
                'shared/date/date.properties:month-7-long',
                'http://hg.mozilla.org/gaia-l10n/de/file/default/shared/date/date.properties',
            ],
            [
                'sr-Cyrl',
                'gaia_2_0',
                'shared/date/date.properties:month-7-long',
                'http://hg.mozilla.org/releases/gaia-l10n/v2_0/sr-Cyrl/file/default/shared/date/date.properties',
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
                'sr',
                'mozilla_org',
                'mozilla_org/download_button.lang:ab34ff81',
                'http://viewvc.svn.mozilla.org/vc/projects/mozilla.com/trunk/locales/sr/download_button.lang?view=markup',
            ],
            [
                'es-ES',
                'mozilla_org',
                'mozilla_org/firefox/os/faq.lang:c71a7a50',
                'http://viewvc.svn.mozilla.org/vc/projects/mozilla.com/trunk/locales/es-ES/firefox/os/faq.lang?view=markup',
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
}
