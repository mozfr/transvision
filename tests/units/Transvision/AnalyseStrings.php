<?php
namespace tests\units\Transvision;

use atoum\atoum;
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
                ['browser:foobar1' => '&brandShortName; will:'],
                ['browser:foobar1' => 'Règles de conservation :'],
                'gecko_strings',
                [],
                ['browser:foobar1'],
            ],
            [
                ['browser:foobar2' => '{{name}}, +{{n}} more'],
                ['browser:foobar2' => '{{name}} et un autre'],
                'gecko_strings',
                [],
                ['browser:foobar2'],
            ],
            [
                ['browser:foobar2' => '$BrandShortName is already running.\n\nPlease close $BrandShortName prior to launching the version you have just installed.'],
                ['browser:foobar2' => 'El $BrandFullName ja s\'està executant.\n\nTanqueu el $BrandFullName abans d\'executar la versió que acabeu d\'instal·lar.'],
                'gecko_strings',
                [],
                ['browser:foobar2'],
            ],
            [
                // Variable format %S
                ['browser:foobar3' => 'A username and password are being requested by %S.'],
                ['browser:foobar3' => 'Le site %S demande un nom d\'utilisateur et un mot de passe.'],
                'gecko_strings',
                [],
                [],
            ],
            [
                // Variable format %S, different case
                ['browser:foobar4' => 'A username and password are being requested by %S.'],
                ['browser:foobar4' => 'Le site %s demande un nom d\'utilisateur et un mot de passe.'],
                'gecko_strings',
                [],
                ['browser:foobar4'],
            ],
            [
                // Variable format %1$s
                ['browser:foobar5' => 'A username and password are being requested by %2$s. The site says: "%1$s"'],
                ['browser:foobar5' => 'Le site %2$s demande un nom d\'utilisateur et un mot de passe. Le site indique : « %1$s »'],
                'gecko_strings',
                [],
                [],
            ],
            [
                // Variable format %1$s, different case
                ['browser:foobar6' => 'Invalid section name (%1$s) at line %2$s.'],
                ['browser:foobar6' => 'Nom de section (%1$S) incorrect à la ligne %2$S.'],
                'gecko_strings',
                [],
                ['browser:foobar6'],
            ],
            [
                // Using ordered variables %1$S instead of %S (not an error)
                ['browser:foobar7' => 'Invalid section name (%1$S) at line %2$S.'],
                ['browser:foobar7' => 'Nom de section (%S) incorrect à la ligne %S.'],
                'gecko_strings',
                [],
                [],
            ],
            [
                // Using %0.S instead of %S (not an error)
                ['browser:foobar8' => 'Do you want %S to save your tabs for the next time it starts?'],
                ['browser:foobar8' => 'Salvare le schede aperte per il prossimo avvio?%0.S'],
                'gecko_strings',
                [],
                [],
            ],
            [
                // Using %1$0.S instead of %1$S (not an error)
                ['browser:foobar9' => '%1$S is unable to connect with %2$S right now.'],
                ['browser:foobar9' => 'Impossibile connettersi a %2$S in questo momento.%1$0.S'],
                'gecko_strings',
                [],
                [],
            ],
            [
                // Mixed ordered variables %1$S and %S (error)
                ['browser:foobar10' => 'A username and password are being requested by %S. The site says: "%S"'],
                ['browser:foobar10' => 'Le site %S demande un nom d\'utilisateur et un mot de passe. Le site indique : « %1$S »'],
                'gecko_strings',
                [],
                ['browser:foobar10'],
            ],
            [
                // Not matching test
                ['foobar11' => 'A username and password are being requested by %2$S. The site says: "%1$S"'],
                ['foobar11' => 'Le site %2$S demande un nom d\'utilisateur et un mot de passe. Le site indique : « %1$S »'],
                'gecko_strings',
                [],
                [],
            ],
            [
                // String should be ignored for false positives
                ['browser:foobar12' => 'A username and password are being requested by %S. The site says: "%S"'],
                ['browser:foobar12' => 'Le site %S demande un nom d\'utilisateur et un mot de passe. Le site indique : « %1$S »'],
                'gecko_strings',
                ['browser:foobar12'],
                [],
            ],
            [
                // {{n}} vs {{ n }} (not an error)
                ['browser:foobar13' => '{{name}}, +{{n}} more'],
                ['browser:foobar13' => '{{ name }} et {{ n }} autre'],
                'gecko_strings',
                [],
                [],
            ],
            [
                // {{n}} vs {{ n }}, changed order (not an error)
                ['browser:foobar14' => '{{n}} more and {{ name }}'],
                ['browser:foobar14' => '{{name}} et {{n}} autre'],
                'gecko_strings',
                [],
                [],
            ],
            [
                // Non-breaking space in {{ n }} (not an error)
                ['browser:foobar15' => '{{ appName }} installed'],
                ['browser:foobar15' => '{{ appName }} ইনস্টল রয়েছে'],
                'gecko_strings',
                [],
                [],
            ],
            [
                // Missing variable for FTL
                ['browser:foobar16' => '{ $brandname } installed'],
                ['browser:foobar16' => 'installato'],
                'gecko_strings',
                [],
                ['browser:foobar16'],
            ],
            [
                // Difference spacing in variable for FTL (not an error)
                ['browser:foobar16a1' => '{ $brandname } installed'],
                ['browser:foobar16a1' => '{$brandname} installato'],
                'gecko_strings',
                [],
                [],
            ],
            [
                // Difference spacing in variable for FTL (not an error)
                ['browser:foobar16a2' => '{$brandname} installed'],
                ['browser:foobar16a2' => '{ $brandname } installato'],
                'gecko_strings',
                [],
                [],
            ],
            [
                // Missing message reference for FTL
                ['browser:foobar16b' => '{ other-message } installed'],
                ['browser:foobar16b' => 'installato'],
                'gecko_strings',
                [],
                ['browser:foobar16b'],
            ],
            [
                // Missing message reference for FTL
                ['browser:foobar16b1' => '{ other-message.attribute } installed'],
                ['browser:foobar16b1' => 'installato'],
                'gecko_strings',
                [],
                ['browser:foobar16b1'],
            ],
            [
                // Missing term reference for FTL
                ['browser:foobar16c' => '{ -brand-name } installed'],
                ['browser:foobar16c' => 'installato'],
                'gecko_strings',
                [],
                ['browser:foobar16c'],
            ],
            [
                // Missing term reference for FTL (no spaces)
                ['browser:foobar16d' => '{-brand-name} installed'],
                ['browser:foobar16d' => 'installato'],
                'gecko_strings',
                [],
                ['browser:foobar16d'],
            ],
            [
                // Term reference with variant (not an error)
                ['browser:foobar16da' => '{-brand-name} installed'],
                ['browser:foobar16da' => '{-brand-name[acc]} installato'],
                'gecko_strings',
                [],
                [],
            ],
            [
                // Term reference with variant and different spacing (not an error)
                ['browser:foobar16db' => '{-brand-name} installed'],
                ['browser:foobar16db' => '{ -brand-name[acc]} installato'],
                'gecko_strings',
                [],
                [],
            ],
            [
                // Different format for term reference for FTL (no errors)
                ['browser:foobar16e' => '{-brand-name} installed'],
                ['browser:foobar16e' => '{ -brand-name } installato'],
                'gecko_strings',
                [],
                [],
            ],
            [
                // Parameterized term (not an error)
                ['browser:foobar16f' => '{-brand-name} installed'],
                ['browser:foobar16f' => '{ -brand-name(case: "upper") } installato'],
                'gecko_strings',
                [],
                [],
            ],
            [
                // Parameterized term with multiple parameters (not an error)
                ['browser:foobar16f2' => '{-brand-name} installed'],
                ['browser:foobar16f2' => '{ -brand-name(case: "gen", capitalization: "lower") } installato'],
                'gecko_strings',
                [],
                [],
            ],
            [
                // Mispelled variable
                ['ios:foobar1' => 'Introductory slide %1$@ of %2$@'],
                ['ios:foobar1' => 'Introduzione (passaggio %1$@ di %$@)'],
                'firefox_ios',
                [],
                ['ios:foobar1'],
            ],
            [
                // Missing variable
                ['ios:foobar2' => 'Do you want to save the password on %@?'],
                ['ios:foobar2' => 'Salvare la password?'],
                'firefox_ios',
                [],
                ['ios:foobar2'],
            ],
            [
                // Changed order, not an error
                ['ios:foobar3' => 'Introductory slide %1$@ of %2$@'],
                ['ios:foobar3' => 'Introduzione (passaggio %2$@ di %1$@)'],
                'firefox_ios',
                [],
                [],
            ],
        ];
    }

    /**
     * @dataProvider differencesDP
     */
    public function testDifferences($a, $b, $c, $d, $e)
    {
        $obj = new _AnalyseStrings();
        $this
            ->array($obj->differences($a, $b, $c, $d))
                ->isEqualTo($e);
    }
}
