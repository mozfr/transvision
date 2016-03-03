<?php
namespace tests\units\Transvision;

use atoum;
use Transvision\Consistency as _Consistency;

require_once __DIR__ . '/../bootstrap.php';

class Consistency extends atoum\test
{
    public function findDuplicates_DP()
    {
        return [
            [
                [
                    'browser/pdfviewer/viewer.properties:last_page.label'                               => 'Aller à la dernière page',
                    'browser/pdfviewer/viewer.properties:last_page.title'                               => 'Aller à la dernière page',
                    'browser/chrome/browser/migration/migration.properties:1_safari'                    => 'Préférences',
                    'devtools/shared/gclicommands.properties:cookieListDesc'                            => 'Afficher les cookies',
                    'browser/chrome/browser/browser.properties:popupWarningButtonUnix'                  => 'Préférences',
                    'browser/chrome/browser/aboutPrivateBrowsing.dtd:aboutPrivateBrowsing.info.cookies' => 'Les cookies',
                ],
                [
                    'browser/pdfviewer/viewer.properties:last_page.label'              => 'Aller à la dernière page',
                    'browser/pdfviewer/viewer.properties:last_page.title'              => 'Aller à la dernière page',
                    'browser/chrome/browser/browser.properties:popupWarningButtonUnix' => 'Préférences',
                    'browser/chrome/browser/migration/migration.properties:1_safari'   => 'Préférences',
                ],
            ],
        ];
    }

    /**
     * @dataProvider findDuplicates_DP
     */
    public function testFindDuplicatesID($a, $b)
    {
        $obj = new _Consistency();
        $this
            ->array($obj->findDuplicates($a))
                ->isEqualTo($b);
    }

    public function filterStrings_DP()
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
                'aurora',
                [
                    'browser/pdfviewer/viewer.properties:last_page.label'                               => 'Aller à la dernière page',
                    'browser/chrome/browser/aboutPrivateBrowsing.dtd:aboutPrivateBrowsing.info.cookies' => 'Les cookies',
                    'shared/date/date.properties:days-until-long[many]'                                 => 'dans {{value}} jours',
                    'dom/chrome/accessibility/win/accessible.properties:press'                          => 'Appuyer',
                    'apps/system/accessibility.properties:accessibility-listItemsCount[two]'            => '{{count}} éléments',
                ],
            ],
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
                'gaia',
                [
                    'browser/pdfviewer/viewer.properties:last_page.label'                               => 'Aller à la dernière page',
                    'browser/chrome/browser/aboutPrivateBrowsing.dtd:aboutPrivateBrowsing.info.cookies' => 'Les cookies',
                    'browser/chrome/browser-region/region.properties:browser.search.order.1'            => 'Google',
                    'browser/defines.inc:MOZ_LANGPACK_CREATOR'                                          => 'L\'équipe FrenchMozilla',
                    'dom/chrome/accessibility/win/accessible.properties:press'                          => 'Appuyer',
                    'dom/chrome/accessibility/AccessFu.properties:notation-phasorangle'                 => 'angle de phaseur',
                ],
            ],
        ];
    }

    /**
     * @dataProvider filterStrings_DP
     */
    public function testFilterStrings($a, $b, $c)
    {
        $obj = new _Consistency();
        $this
            ->array($obj->filterStrings($a, $b))
                ->isEqualTo($c);
    }
}
