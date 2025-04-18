<?php
namespace tests\Transvision;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Transvision\Consistency;

require_once __DIR__ . '/../bootstrap.php';

class ConsistencyTest extends TestCase
{
    public static function findDuplicates_DP()
    {
        return [
            [
                [
                    'browser/pdfviewer/viewer.properties:last_page.label'                               => 'Aller à la dernière page',
                    'browser/pdfviewer/viewer.properties:last_page.title'                               => 'Aller à la Dernière page',
                    'browser/chrome/browser/migration/migration.properties:1_safari'                    => 'Préférences',
                    'devtools/shared/gclicommands.properties:cookieListDesc'                            => 'Afficher les cookies',
                    'browser/chrome/browser/browser.properties:popupWarningButtonUnix'                  => 'Préférences',
                    'browser/chrome/browser/aboutPrivateBrowsing.dtd:aboutPrivateBrowsing.info.cookies' => 'Les cookies',
                ],
                [
                    'browser/pdfviewer/viewer.properties:last_page.title'              => 'Aller à la Dernière page',
                    'browser/pdfviewer/viewer.properties:last_page.label'              => 'Aller à la dernière page',
                    'browser/chrome/browser/migration/migration.properties:1_safari'   => 'Préférences',
                    'browser/chrome/browser/browser.properties:popupWarningButtonUnix' => 'Préférences',
                ],
            ],
        ];
    }

    #[DataProvider('findDuplicates_DP')]
    public function testFindDuplicatesID($a, $b)
    {
        $obj = new Consistency();
        $this
            ->assertEqualsCanonicalizing($obj->findDuplicates($a), $b);
    }

    public static function findDuplicatesSensitive_DP()
    {
        return [
            [
                [
                    'browser/pdfviewer/viewer.properties:last_page.title'                               => 'Aller à la Dernière page',
                    'browser/pdfviewer/viewer.properties:last_page.label'                               => 'Aller à la dernière page',
                    'browser/chrome/browser/migration/migration.properties:1_safari'                    => 'Préférences',
                    'devtools/shared/gclicommands.properties:cookieListDesc'                            => 'Afficher les cookies',
                    'browser/chrome/browser/browser.properties:popupWarningButtonUnix'                  => 'Préférences',
                    'browser/chrome/browser/aboutPrivateBrowsing.dtd:aboutPrivateBrowsing.info.cookies' => 'Les cookies',
                ],
                [
                    'browser/chrome/browser/migration/migration.properties:1_safari'   => 'Préférences',
                    'browser/chrome/browser/browser.properties:popupWarningButtonUnix' => 'Préférences',
                ],
            ],
        ];
    }

    #[DataProvider('findDuplicatesSensitive_DP')]
    public function testFindDuplicatesSensitiveID($a, $b)
    {
        $obj = new Consistency();
        $this
            ->assertEqualsCanonicalizing($obj->findDuplicates($a, False), $b);
    }

    public static function filterStrings_DP()
    {
        return [
            [
                [
                    'browser/pdfviewer/viewer.properties:last_page.label'                               => 'Aller à la dernière page',
                    'browser/branding/nightly/brand.dtd:trademarkInfo.part1'                            => '',
                    'browser/chrome/browser/aboutPrivateBrowsing.dtd:aboutPrivateBrowsing.info.cookies' => 'Les cookies',
                    'browser/chrome/browser/browser.properties:slowStartup.helpButton.accesskey'        => 'D',
                    'browser/chrome/browser/preferences/applicationManager.dtd:appManager.style2'       => 'width: 20ch;',
                    'browser/chrome/browser/preferences/applicationManager.dtd:appManager.style'        => 'width: 30em; min-height: 20em;',
                    'extensions/irc/chrome/ceip.dtd:window.size'                                        => 'width: 42em;',
                    'mail/chrome/messenger/importDialog.dtd:window.macWidth'                            => '45em',
                    'browser/chrome/browser-region/region.properties:browser.search.order.1'            => 'Google',
                    'browser/defines.inc:MOZ_LANGPACK_CREATOR'                                          => 'L\'équipe FrenchMozilla',
                    'dom/chrome/accessibility/win/accessible.properties:press'                          => 'Appuyer',
                    'shared/date/date.properties:days-until-long[many]'                                 => 'dans {{value}} jours',
                    'dom/chrome/accessibility/AccessFu.properties:notation-phasorangle'                 => 'angle de phaseur',
                    'apps/system/accessibility.properties:accessibility-listItemsCount[two]'            => '{{count}} éléments',
                ],
                'gecko_strings',
                [
                    'browser/pdfviewer/viewer.properties:last_page.label'                               => 'Aller à la dernière page',
                    'browser/chrome/browser/aboutPrivateBrowsing.dtd:aboutPrivateBrowsing.info.cookies' => 'Les cookies',
                    'dom/chrome/accessibility/win/accessible.properties:press'                          => 'Appuyer',
                    'shared/date/date.properties:days-until-long[many]'                                 => 'dans {{value}} jours',
                    'apps/system/accessibility.properties:accessibility-listItemsCount[two]'            => '{{count}} éléments',
                ],
            ],
        ];
    }

    #[DataProvider('filterStrings_DP')]
    public function testFilterStrings($a, $b, $c)
    {
        $obj = new Consistency();
        $this
            ->assertEqualsCanonicalizing($obj->filterStrings($a, $b), $c);
    }

    public static function filterComponents_DP()
    {
        return [
            [
                [
                    'browser/chrome/browser/preferences/applicationManager.dtd:appManager.style' => 'width: 30em; min-height: 20em;',
                    'extensions/irc/chrome/ceip.dtd:window.size'                                 => 'width: 42em;',
                    'mail/chrome/messenger/importDialog.dtd:window.macWidth'                     => '45em',
                    'browser/defines.inc:MOZ_LANGPACK_CREATOR'                                   => 'L\'équipe FrenchMozilla',
                    'dom/chrome/accessibility/win/accessible.properties:press'                   => 'Appuyer',
                    'shared/date/date.properties:days-until-long[many]'                          => 'dans {{value}} jours',
                    'dom/chrome/accessibility/AccessFu.properties:notation-phasorangle'          => 'angle de phaseur',
                    'apps/system/accessibility.properties:accessibility-listItemsCount[two]'     => '{{count}} éléments',
                ],
                ['browser', 'dom'],
                [
                    'extensions/irc/chrome/ceip.dtd:window.size'                             => 'width: 42em;',
                    'mail/chrome/messenger/importDialog.dtd:window.macWidth'                 => '45em',
                    'shared/date/date.properties:days-until-long[many]'                      => 'dans {{value}} jours',
                    'apps/system/accessibility.properties:accessibility-listItemsCount[two]' => '{{count}} éléments',
                ],
            ],
            [
                [
                    'browser/pdfviewer/viewer.properties:last_page.label'                    => 'Aller à la dernière page',
                    'extensions/irc/chrome/ceip.dtd:window.size'                             => 'width: 42em;',
                    'mail/chrome/messenger/importDialog.dtd:window.macWidth'                 => '45em',
                    'dom/chrome/accessibility/win/accessible.properties:press'               => 'Appuyer',
                    'shared/date/date.properties:days-until-long[many]'                      => 'dans {{value}} jours',
                    'apps/system/accessibility.properties:accessibility-listItemsCount[two]' => '{{count}} éléments',
                ],
                [],
                [
                    'browser/pdfviewer/viewer.properties:last_page.label'                    => 'Aller à la dernière page',
                    'extensions/irc/chrome/ceip.dtd:window.size'                             => 'width: 42em;',
                    'mail/chrome/messenger/importDialog.dtd:window.macWidth'                 => '45em',
                    'dom/chrome/accessibility/win/accessible.properties:press'               => 'Appuyer',
                    'shared/date/date.properties:days-until-long[many]'                      => 'dans {{value}} jours',
                    'apps/system/accessibility.properties:accessibility-listItemsCount[two]' => '{{count}} éléments',
                ],
            ],
        ];
    }

    #[DataProvider('filterComponents_DP')]
    public function testFilterComponents($a, $b, $c)
    {
        $obj = new Consistency();
        $this
            ->assertEqualsCanonicalizing($obj->filterComponents($a, $b), $c);
    }
}
