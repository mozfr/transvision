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
            ['6&percnt; is not&nbsp;much', '6% is not much'], // real nbsp here
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
                'central',
                ['browser/chrome/browser/preferences/privacy.dtd:historyHeader.pre.label'],
            ],
            [
                ['apps/settings/settings.properties:bt-status-paired[one]' => '{{name}}, +{{n}} more'],
                ['apps/settings/settings.properties:bt-status-paired[one]' => '{{name}} et un autre'],
                'gaia',
                ['apps/settings/settings.properties:bt-status-paired[one]'],
            ],
            [
                ['browser/installer/custom.properties:WARN_MANUALLY_CLOSE_APP_LAUNCH' => '$BrandShortName is already running.\n\nPlease close $BrandShortName prior to launching the version you have just installed.'],
                ['browser/installer/custom.properties:WARN_MANUALLY_CLOSE_APP_LAUNCH' => 'El $BrandFullName ja s\'està executant.\n\nTanqueu el $BrandFullName abans d\'executar la versió que acabeu d\'instal·lar.'],
                'aurora',
                ['browser/installer/custom.properties:WARN_MANUALLY_CLOSE_APP_LAUNCH'],
            ],
            [
                // Variable format %S
                ['browser:foobar' => 'A username and password are being requested by %S.'],
                ['browser:foobar' => 'Le site %S demande un nom d\'utilisateur et un mot de passe.'],
                'release',
                [],
            ],
            [
                // Variable format %S, different case (not an error)
                ['browser:foobar' => 'A username and password are being requested by %S.'],
                ['browser:foobar' => 'Le site %s demande un nom d\'utilisateur et un mot de passe.'],
                'release',
                [],
            ],
            [
                // Variable format %1$s
                ['browser:foobar' => 'A username and password are being requested by %2$s. The site says: "%1$s"'],
                ['browser:foobar' => 'Le site %2$s demande un nom d\'utilisateur et un mot de passe. Le site indique : « %1$s »'],
                'release',
                [],
            ],
            [
                // Variable format %1$s, different case (not an error)
                ['browser:foobar' => 'Invalid section name (%1$s) at line %2$s.'],
                ['browser:foobar' => 'Nom de section (%1$S) incorrect à la ligne %1$S.'],
                'release',
                [],
            ],
            [
                // Using ordered variables %1$S instead of %S (not an error)
                ['browser:foobar' => 'Invalid section name (%1$S) at line %2$S.'],
                ['browser:foobar' => 'Nom de section (%S) incorrect à la ligne %S.'],
                'release',
                [],
            ],
            [
                // Using %0.S instead of %S (not an error)
                ['browser:foobar' => 'Do you want %S to save your tabs for the next time it starts?'],
                ['browser:foobar' => 'Salvare le schede aperte per il prossimo avvio?%0.S'],
                'release',
                [],
            ],
            [
                // Using %1$0.S instead of %1$S (not an error)
                ['browser:foobar' => '%1$S is unable to connect with %2$S right now.'],
                ['browser:foobar' => 'Impossibile connettersi a %2$S in questo momento.%1$0.S'],
                'release',
                [],
            ],
            [
                // Mixed ordered variables %1$S and %S (error)
                ['browser:foobar' => 'A username and password are being requested by %S. The site says: "%S"'],
                ['browser:foobar' => 'Le site %S demande un nom d\'utilisateur et un mot de passe. Le site indique : « %1$S »'],
                'release',
                ['browser:foobar'],
            ],
            [
                // not matching test
                ['foobar' => 'A username and password are being requested by %2$S. The site says: "%1$S"'],
                ['foobar' => 'Le site %2$S demande un nom d\'utilisateur et un mot de passe. Le site indique : « %1$S »'],
                'release',
                [],
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
