<?php
namespace Transvision\tests\units;
use atoum;

require_once __DIR__ . '/../bootstrap.php';

class ShowResults extends atoum\test
{
    public function formatEntityDP()
    {
        return array(
            array('browser/chrome/browser/browser.dtd:historyHomeCmd.label', false)
        );
    }

    /**
     * @dataProvider formatEntityDP
     */
    public function testFormatEntity($a, $b)
    {
        $obj = new \Transvision\ShowResults();
        $this
            ->string($obj->formatEntity($a, $b))
                ->isEqualTo('<span class="green">browser</span><span class="superset">&nbsp;&sup;&nbsp;</span>chrome<span class="superset">&nbsp;&sup;&nbsp;</span>browser<span class="superset">&nbsp;&sup;&nbsp;</span>browser.dtd<br><span class="red">historyHomeCmd.label</span>');
    }

    public function getStringFromEntityDP()
    {
        return array(
            array(
                'browser/pdfviewer/viewer.properties:last_page.label',
                ['browser/pdfviewer/viewer.properties:last_page.label' => 'Aller à la dernière page'],
                'Aller à la dernière page'
                ),
            array(
                'browser/pdfviewer/viewer.properties:last_page.label',
                ['entity.is.not.found' => 'Aller à la dernière page'],
                false
                ),
            array(
                'browser/pdfviewer/viewer.properties:last_page.label',
                ['browser/pdfviewer/viewer.properties:last_page.label' => ''],
                false
                ),
        );
    }

    /**
     * @dataProvider getStringFromEntityDP
     */
    public function testGetStringFromEntity($a, $b, $c)
    {
        $obj = new \Transvision\ShowResults();
        $this
            ->variable($obj->getStringFromEntity($a, $b))
                ->isIdenticalTo($c);
    }

    public function getRepositorySearchResultsDP()
    {
        return array(
            // simple search
            array(
                ['apps/system/system.properties:softwareHomeButton.ariaLabel', 'apps/settings/settings.properties:homescreens'],
                [
                    0 => ['apps/system/system.properties:softwareHomeButton.ariaLabel' => 'Home'],
                    1 => ['apps/system/system.properties:softwareHomeButton.ariaLabel' => 'Accueil'],
                ],
                ['apps/system/system.properties:softwareHomeButton.ariaLabel' => ['Home' => 'Accueil']]
            ),

            // Check escaping
            array(
                ['browser/chrome/browser/safebrowsing/phishing-afterload-warning-message.dtd:safeb.blocked.malwarePage.longDesc'],
                [
                    0 => [
                        'apps/system/system.properties:softwareHomeButton.ariaLabel' => 'Home',
                        'browser/chrome/browser/safebrowsing/phishing-afterload-warning-message.dtd:safeb.blocked.malwarePage.longDesc'
                            => "&#60;p&#62;Attack pages try to install programs that steal private information, use your computer to "
                            . "attack others, or damage your system.&#60;/p&#62;&#60;p&#62;Some attack pages intentionally distribute "
                            . "harmful software, but many are compromised without the knowledge or permission of their owners.&#60;/p&#62;"
                    ],
                    1 => [
                        'apps/system/system.properties:softwareHomeButton.ariaLabel' => 'Home',
                        'browser/chrome/browser/safebrowsing/phishing-afterload-warning-message.dtd:safeb.blocked.malwarePage.longDesc'
                            => "&#60;p&#62;Les pages malveillantes essaient d&#39;installer des programmes qui volent des informations "
                            . " personnelles, qui utilisent votre ordinateur pour en attaquer d&#39;autres ou qui endommagent votre "
                            . "système.&#60;/p&#62;&#60;p&#62;Certaines pages distribuent intentionnellement des logiciels malfaisants, "
                            . "mais beaucoup sont compromises sans la permission de leurs propriétaires ou sans qu&#39;ils en aient "
                            . "connaissance.&#60;/p&#62;"
                    ],
                    2 => ['apps/system/system.properties:softwareHomeButton.ariaLabel' => 'Accueil'],
                ],
                ['browser/chrome/browser/safebrowsing/phishing-afterload-warning-message.dtd:safeb.blocked.malwarePage.longDesc'
                =>  [
                        "<p>Attack pages try to install programs that steal private information, use your"
                      . " computer to attack others, or damage your system.</p><p>Some attack pages"
                      . " intentionally distribute harmful software, but many are compromised without"
                      . " the knowledge or permission of their owners.</p>"
                     => "<p>Les pages malveillantes essaient d'installer des programmes qui volent "
                      . "des informations  personnelles, qui utilisent votre ordinateur pour en attaquer "
                      . "d'autres ou qui endommagent votre système.</p><p>Certaines pages distribuent intentionnellement "
                      . "des logiciels malfaisants, mais beaucoup sont compromises sans la permission de leurs "
                      . "propriétaires ou sans qu'ils en aient connaissance.</p>"
                    ]
                ],
            )
        );
    }

    /**
     * @dataProvider getRepositorySearchResultsDP
     */
    public function testGeRepositorySearchResults($a, $b, $c)
    {
        $obj = new \Transvision\ShowResults();
        $this
            ->array($obj->getRepositorySearchResults($a, $b))
                ->isIdenticalTo($c);
    }
}
