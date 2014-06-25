<?php
namespace tests\units\Transvision;

use atoum;
use Transvision\AnalyseStrings as _AnalyseStrings;

require_once __DIR__ . '/../bootstrap.php';

class AnalyseStrings extends atoum\test
{
    public function cleanUpEntitiesDP()
    {
        return [
            ['&#037; &amp; &apos; &percnt; &lt; ', "% & ' % < "],
            ['6&percnt; is not&nbsp;much', '6% is not much'] // real nbsp here
        ];
    }

    /**
     * @dataProvider cleanUpEntitiesDP
     */
    public function testCleanUpEntities($a, $b)
    {
        $obj = new _AnalyseStrings();
        $this
            ->string($obj->cleanUpEntities($a))
                ->isEqualTo($b);
    }

    public function differencesDP()
    {
        return [
            [
                ['browser/chrome/browser/preferences/privacy.dtd:historyHeader.pre.label' => '&brandShortName; will:'],
                ['browser/chrome/browser/preferences/privacy.dtd:historyHeader.pre.label' => 'Règles de conservation :'],
                '/&([a-z0-9\.]+);/i',
                ['browser/chrome/browser/preferences/privacy.dtd:historyHeader.pre.label']
            ],
            [
                ['apps/settings/settings.properties:bt-status-paired[one]' => '{{name}}, +{{n}} more'],
                ['apps/settings/settings.properties:bt-status-paired[one]' => '{{name}} et un autre'],
                '/\{\{([a-z0-9]+)\}\}/i',
                ['apps/settings/settings.properties:bt-status-paired[one]']
            ],
            [
                ['browser/installer/custom.properties:WARN_MANUALLY_CLOSE_APP_LAUNCH' => '$BrandShortName is already running.\n\nPlease close $BrandShortName prior to launching the version you have just installed.'],
                ['browser/installer/custom.properties:WARN_MANUALLY_CLOSE_APP_LAUNCH' => 'El $BrandFullName ja s\'està executant.\n\nTanqueu el $BrandFullName abans d\'executar la versió que acabeu d\'instal·lar.'],
                ['/\$[a-z0-9\.]+\s/i', '/&([a-z0-9\.]+);/i'],
                ['browser/installer/custom.properties:WARN_MANUALLY_CLOSE_APP_LAUNCH','browser/installer/custom.properties:WARN_MANUALLY_CLOSE_APP_LAUNCH']
            ],
            [
                // not matching test
                ['foobar' => 'A username and password are being requested by %2$S. The site says: "%1$S"'],
                ['foobar' => 'Le site %2$S demande un nom d\'utilisateur et un mot de passe. Le site indique : « %1$S »'],
                ['/\s\$[a-z0-9\.]+\s/i'],
                []
            ],
        ];
    }

    /**
     * @dataProvider differencesDP
     */
    public function testDifferences($a, $b, $c, $d)
    {
        $obj = new _AnalyseStrings();
        $this
            ->array($obj->differences($a, $b, $c))
                ->isEqualTo($d);
    }
}
