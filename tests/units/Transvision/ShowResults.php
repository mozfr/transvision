<?php
namespace Transvision\tests\units;
use atoum;

require_once __DIR__ . '/../bootstrap.php';

class ShowResults extends atoum\test
{

    public function getTMXResultsDP()
    {
        include TMX . 'central/en-US/cache_en-US.php';
        $source = $tmx;
        include TMX . 'central/fr/cache_fr.php';
        $target = $tmx;
        $data = [$source, $target];

        return array(
            array(
                ['browser/chrome/browser/downloads/downloads.dtd:cmd.showMac.label'],
                $data,
                ['browser/chrome/browser/downloads/downloads.dtd:cmd.showMac.label' =>
                    ['Find in Finder', 'Ouvrir dans le Finder']
                ]
            ),
        );
    }

    /**
     * @dataProvider getTMXResultsDP
     */
    public function testGetTMXResults($a, $b, $c)
    {
        $obj = new \Transvision\ShowResults();
        $this
            ->array($obj->getTMXResults($a, $b))
                ->isEqualTo($c);
    }

    public function getTranslationMemoryResultsDP()
    {
        include TMX . 'central/en-US/cache_en-US.php';
        $source = $tmx;
        include TMX . 'central/fr/cache_fr.php';
        $target = $tmx;
        $data = [$source, $target];
        $results = array(
            [
              'source' => 'Bookmark',
              'target' => 'Marquer cette page',
              'quality'=> (float) 100
            ],

            [
              'source'  => 'Bookmark',
              'target'  => 'Marque-page',
              'quality' => (float) 100
            ],

            [
              'source'  => 'Bookmarks',
              'target'  => 'Marque-pages',
              // 'quality' => (float) 88.888888888889
              'quality' => (float) 100/1.125
            ],

            [
              'source'  => 'New Bookmarks',
              'target'  => 'Nouveaux marque-pages',
              // 'quality' => (float) 61.538461538462
              'quality' => (float) 100/1.625
            ],
        );

        return array(
            array(
                array_keys($source),
                $data,
                'Bookmark',
                $results
            ),
        );
    }

    /**
     * @dataProvider getTranslationMemoryResultsDP
     */
    public function testGetTranslationMemoryResults($a, $b, $c, $d)
    {
        // $entities, $array_strings, $search, $max_results=200, $min_quality=0
        $obj = new \Transvision\ShowResults();
        $this
            ->array($obj->getTranslationMemoryResults($a, $b, $c, 4))
                ->isEqualTo($d);
    }

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
