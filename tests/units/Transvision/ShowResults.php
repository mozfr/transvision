<?php
namespace tests\units\Transvision;

use atoum;
use Transvision\ShowResults as _ShowResults;

require_once __DIR__ . '/../bootstrap.php';

class ShowResults extends atoum\test
{
    public function getTMXResultsDP()
    {
        include TMX . 'en-US/cache_en-US_central.php';
        $source = $tmx;
        include TMX . 'fr/cache_fr_central.php';
        $target = $tmx;
        $data = [$source, $target];

        return [
            [
                ['browser/chrome/browser/downloads/downloads.dtd:cmd.showMac.label'],
                $data,
                ['browser/chrome/browser/downloads/downloads.dtd:cmd.showMac.label' =>
                    ['Find in Finder', 'Ouvrir dans le Finder'],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getTMXResultsDP
     */
    public function testGetTMXResults($a, $b, $c)
    {
        $obj = new _ShowResults();
        $this
            ->array($obj->getTMXResults($a, $b))
                ->isEqualTo($c);
    }

    public function getTranslationMemoryResultsDP()
    {
        include TMX . 'en-US/cache_en-US_central.php';
        $source = $tmx;
        include TMX . 'fr/cache_fr_central.php';
        $target = $tmx;

        foreach ($source as $key => $value) {
            if (isset($target[$key])) {
                $strings[] = [$value, $target[$key]];
            }
        }

        $results = [
            [
              'source'  => 'Bookmark',
              'target'  => 'Marquer cette page',
              'quality' => 100,
            ],

            [
              'source'  => 'Bookmark',
              'target'  => 'Marque-page',
              'quality' => 100,
            ],

            [
              'source'  => 'Bookmarks',
              'target'  => 'Marque-pages',
              'quality' => 88.89,
            ],

            [
              'source'  => 'New Bookmarks',
              'target'  => 'Nouveaux marque-pages',
              'quality' => 61.54,
            ],
        ];

        return [
            [
                $strings,
                'Bookmark',
                $results,
            ],
        ];
    }

    /**
     * @dataProvider getTranslationMemoryResultsDP
     */
    public function testGetTranslationMemoryResults($a, $b, $c)
    {
        $obj = new _ShowResults();
        $this
            ->array($obj->getTranslationMemoryResults($a, $b, 4))
                ->isEqualTo($c);
    }

    public function formatEntityDP()
    {
        return [
            ['browser/chrome/browser/browser.dtd:historyHomeCmd.label', false],
        ];
    }

    /**
     * @dataProvider formatEntityDP
     */
    public function testFormatEntity($a, $b)
    {
        $obj = new _ShowResults();
        $this
            ->string($obj->formatEntity($a, $b))
                ->isEqualTo('<span class="green">browser</span><span class="superset">&nbsp;&bull;&nbsp;</span>chrome<span class="superset">&nbsp;&bull;&nbsp;</span>browser<span class="superset">&nbsp;&bull;&nbsp;</span>browser.dtd<br><span class="red">historyHomeCmd.label</span>');
    }

    public function getStringFromEntityDP()
    {
        return [
            [
                'browser/pdfviewer/viewer.properties:last_page.label',
                ['browser/pdfviewer/viewer.properties:last_page.label' => 'Aller à la dernière page'],
                'Aller à la dernière page',
            ],
            [
                'browser/pdfviewer/viewer.properties:last_page.label',
                ['entity.is.not.found' => 'Aller à la dernière page'],
                '@@missing@@',
            ],
            [
                'browser/pdfviewer/viewer.properties:last_page.label',
                ['browser/pdfviewer/viewer.properties:last_page.label' => ''],
                '',
            ],
        ];
    }

    /**
     * @dataProvider getStringFromEntityDP
     */
    public function testGetStringFromEntity($a, $b, $c)
    {
        $obj = new _ShowResults();
        $this
            ->variable($obj->getStringFromEntity($a, $b))
                ->isIdenticalTo($c);
    }

    public function getRepositorySearchResultsDP()
    {
        return [
            // Simple search
            [
                ['apps/system/system.properties:softwareHomeButton.ariaLabel', 'apps/settings/settings.properties:homescreens'],
                [
                    0 => ['apps/system/system.properties:softwareHomeButton.ariaLabel' => 'Home'],
                    1 => ['apps/system/system.properties:softwareHomeButton.ariaLabel' => 'Accueil'],
                ],
                ['apps/system/system.properties:softwareHomeButton.ariaLabel' => ['Home' => 'Accueil']],
            ],

            // Check escaping
            [
                ['browser/chrome/browser/safebrowsing/phishing-afterload-warning-message.dtd:safeb.blocked.malwarePage.longDesc'],
                [
                    0 => [
                        'apps/system/system.properties:softwareHomeButton.ariaLabel' => 'Home',
                        'browser/chrome/browser/safebrowsing/phishing-afterload-warning-message.dtd:safeb.blocked.malwarePage.longDesc'
                            => '&#60;p&#62;Attack pages try to install programs that steal private information, use your computer to '
                            . 'attack others, or damage your system.&#60;/p&#62;&#60;p&#62;Some attack pages intentionally distribute '
                            . 'harmful software, but many are compromised without the knowledge or permission of their owners.&#60;/p&#62;',
                    ],
                    1 => [
                        'apps/system/system.properties:softwareHomeButton.ariaLabel' => 'Home',
                        'browser/chrome/browser/safebrowsing/phishing-afterload-warning-message.dtd:safeb.blocked.malwarePage.longDesc'
                            => '&#60;p&#62;Les pages malveillantes essaient d&#39;installer des programmes qui volent des informations '
                            . ' personnelles, qui utilisent votre ordinateur pour en attaquer d&#39;autres ou qui endommagent votre '
                            . 'système.&#60;/p&#62;&#60;p&#62;Certaines pages distribuent intentionnellement des logiciels malfaisants, '
                            . 'mais beaucoup sont compromises sans la permission de leurs propriétaires ou sans qu&#39;ils en aient '
                            . 'connaissance.&#60;/p&#62;',
                    ],
                    2 => ['apps/system/system.properties:softwareHomeButton.ariaLabel' => 'Accueil'],
                ],
                ['browser/chrome/browser/safebrowsing/phishing-afterload-warning-message.dtd:safeb.blocked.malwarePage.longDesc'
                => [
                        '<p>Attack pages try to install programs that steal private information, use your'
                      . ' computer to attack others, or damage your system.</p><p>Some attack pages'
                      . ' intentionally distribute harmful software, but many are compromised without'
                      . ' the knowledge or permission of their owners.</p>'
                     => '<p>Les pages malveillantes essaient d\'installer des programmes qui volent '
                      . 'des informations  personnelles, qui utilisent votre ordinateur pour en attaquer '
                      . 'd\'autres ou qui endommagent votre système.</p><p>Certaines pages distribuent intentionnellement '
                      . 'des logiciels malfaisants, mais beaucoup sont compromises sans la permission de leurs '
                      . 'propriétaires ou sans qu\'ils en aient connaissance.</p>',
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getRepositorySearchResultsDP
     */
    public function testGeRepositorySearchResults($a, $b, $c)
    {
        $obj = new _ShowResults();
        $this
            ->array($obj->getRepositorySearchResults($a, $b))
                ->isIdenticalTo($c);
    }

    public function searchEntitiesDP()
    {
        return [
            [
                [
                    'browser/chrome/browser/migration/migration.properties:sourceNameIE'  => 'test',
                    'browser/chrome/browser/migration/migration.properties:sourceNameIE1' => 'test2',
                ],
                '~^browser/chrome/browser/migration/migration.properties:sourceNameIE$~i',
                [
                    'browser/chrome/browser/migration/migration.properties:sourceNameIE',
                ],
            ],
            [
                [
                    'browser/chrome/browser/migration/migration.properties:sourceNameIE'  => 'test',
                    'browser/chrome/browser/migration/migration.properties:sourceNameIE1' => 'test2',
                ],
                '~sourceNameIE~i',
                [
                    'browser/chrome/browser/migration/migration.properties:sourceNameIE',
                    'browser/chrome/browser/migration/migration.properties:sourceNameIE1',
                ],
            ],
            [
                [
                    'browser/chrome/browser/migration/migration.properties:sourceNameIE'  => 'test',
                    'browser/chrome/browser/migration/migration.properties:sourceNameIE1' => 'test2',
                ],
                '~^sourceNameIE$~i',
                [
                    'browser/chrome/browser/migration/migration.properties:sourceNameIE',
                ],
            ],
            [
                [
                    'browser/chrome/browser/migration/migration.properties:sourceNameIE' => 'test',
                ],
                '~foobar~',
                [],
            ],
            [
                [],
                '~foobar~',
                [],
            ],
        ];
    }

    /**
     * @dataProvider searchEntitiesDP
     */
    public function testSearchEntities($a, $b, $c)
    {
        $obj = new _ShowResults();
        $this
            ->array($obj->searchEntities($a, $b))
                ->isEqualTo($c);
    }

    public function testGetSuggestionsResults()
    {
        $obj = new _ShowResults();
        include TMX . 'en-US/cache_en-US_central.php';
        $source = $tmx;
        include TMX . 'fr/cache_fr_central.php';
        $target = $tmx;
        $this
            ->array($obj->getSuggestionsResults($source, $target, 'Bookmark'))
                ->isEqualTo(['Bookmark', 'Bookmarks', 'New Bookmarks',
                             'Bookmark This Page', 'Find in Finder',
                             'Nouveaux marque-pages', 'Marque-page', 'Marque-pages',
                             'Marquer cette page', 'Ouvrir dans le Finder', ])
            ->array($obj->getSuggestionsResults($source, $target, 'Bookmark', 10))
                ->isEqualTo(['Bookmark', 'Bookmarks', 'New Bookmarks',
                             'Bookmark This Page', 'Find in Finder',
                             'Nouveaux marque-pages', 'Marque-page', 'Marque-pages',
                             'Marquer cette page', 'Ouvrir dans le Finder', ])
            ->array($obj->getSuggestionsResults($source, $target, 'Bookmark', 3))
                ->isEqualTo(['Bookmark', 'Nouveaux marque-pages']);
    }

    public function buildErrorStringDP()
    {
        return [
            [
                'Le système de marque-pages et.',
                'Le système de marque-pages et',
                '<em class="error">No final dot?</em> ',
            ],
            [
                'Le système de marque-pages et',
                'Le système de marque-pages et',
                '',
            ],
            [
                'Le système de marque-pages et.',
                'Le système de marque-pages et.',
                '',
            ],
            [
                'The bookmarks and history system will not be functional because one of files is in use by another application. Some security software can cause this problem.',
                'Le système de marque-pages et',
                '<em class="error">No final dot?</em> <em class="error">Small string?</em> ',
            ],
            [
                'The bookmarks and history system will not be functional because one of files is in use by another application. Some security software can cause this problem.',
                'Le système de marque-pages et.',
                '<em class="error">Small string?</em> ',
            ],
            [
                'The bookmarks and history system will not be functional because one of files is in use by another application. Some security software can cause this problem.',
                'Le système de marque-pages et dhistorique ne sera pas opérationnel car lun des fichiers de %S est en cours dutilisation par une autre application. Certains logiciels de sécurité peuvent causer ce problème. Le système de marque-pages et dhistorique ne sera pas opérationnel car lun des fichiers de %S est en cours dutilisation par une autre application. Certains logiciels de sécurité peuvent causer ce problème',
                '<em class="error">No final dot?</em> <em class="error">Large string?</em> ',
            ],
            [
                'Le système de marque-pages et',
                'Le système de marque-pages et dhistorique ne sera pas opérationnel car lun des fichiers de %S est en cours dutilisation par une autre application. Certains logiciels de sécurité peuvent causer ce problème. Le système de marque-pages et dhistorique ne sera pas opérationnel car lun des fichiers de %S est en cours dutilisation par une autre application. Certains logiciels de sécurité peuvent causer ce problème.',
                '<em class="error">Large string?</em> ',
            ],
        ];
    }

    /**
     * @dataProvider buildErrorStringDP
     */
    public function testBuildErrorString($a, $b, $c)
    {
        $obj = new _ShowResults();
        $this
            ->string($obj->buildErrorString($a, $b))
                ->isEqualTo($c);
    }

    public function getEditLinkDP()
    {
        return [
            // Pontoon links
            [
                'central',
                'browser/chrome/browser/browser.properties:webextPerms.hostDescription.allUrls',
                'test',
                'fr',
                "&nbsp;<a class='edit_link' target='_blank' href='https://pontoon.mozilla.org/fr/firefox/browser/chrome/browser/browser.properties?search=webextPerms.hostDescription.allUrls'>&lt;edit in Pontoon&gt;</a>",
            ],
            [
                'central',
                'calendar/chrome/calendar/calendar.dtd:calendar.calendar.label',
                'test',
                'fr',
                "&nbsp;<a class='edit_link' target='_blank' href='https://pontoon.mozilla.org/fr/lightning/calendar/chrome/calendar/calendar.dtd?search=calendar.calendar.label'>&lt;edit in Pontoon&gt;</a>",
            ],
            [
                'central',
                'chat/commands.properties:dnd',
                'test',
                'fr',
                "&nbsp;<a class='edit_link' target='_blank' href='https://pontoon.mozilla.org/fr/thunderbird/chat/commands.properties?search=dnd'>&lt;edit in Pontoon&gt;</a>",
            ],
            [
                'central',
                'editor/ui/chrome/composer/editingOverlay.dtd:fileRecentMenu.label',
                'test',
                'fr',
                "&nbsp;<a class='edit_link' target='_blank' href='https://pontoon.mozilla.org/fr/thunderbird/editor/ui/chrome/composer/editingOverlay.dtd?search=fileRecentMenu.label'>&lt;edit in Pontoon&gt;</a>",
            ],
            [
                'central',
                'extensions/irc/chrome/about.dtd:chatzilla.label',
                'test',
                '',
                '',
            ],
            [
                'central',
                'mail/chrome/messenger/addressbook/abContactsPanel.dtd:ccButton.label',
                'test',
                'de',
                "&nbsp;<a class='edit_link' target='_blank' href='https://pontoon.mozilla.org/de/thunderbird/mail/chrome/messenger/addressbook/abContactsPanel.dtd?search=ccButton.label'>&lt;edit in Pontoon&gt;</a>",
            ],
            [
                'central',
                'mobile/android/base/android_strings.dtd:activity_stream_highlights',
                'test',
                'bg',
                "&nbsp;<a class='edit_link' target='_blank' href='https://pontoon.mozilla.org/bg/firefox-for-android/mobile/android/base/android_strings.dtd?search=activity_stream_highlights'>&lt;edit in Pontoon&gt;</a>",
            ],
            [
                'central',
                'suite/chrome/browser/taskbar.properties:taskbar.tasks.composeMessage.description',
                'test',
                'it',
                "&nbsp;<a class='edit_link' target='_blank' href='https://pontoon.mozilla.org/it/seamonkey/suite/chrome/browser/taskbar.properties?search=taskbar.tasks.composeMessage.description'>&lt;edit in Pontoon&gt;</a>",
            ],
            [
                'beta',
                'suite/chrome/browser/taskbar.properties:taskbar.tasks.composeMessage.description',
                'test',
                'it',
                '',
            ],
            [
                'release',
                'suite/chrome/browser/taskbar.properties:taskbar.tasks.composeMessage.description',
                'test',
                'it',
                '',
            ],
            [
                'mozilla_org',
                'mozilla_org/firefox/android/index.lang:4e0bc9d4',
                'test',
                'it',
                "&nbsp;<a class='edit_link' target='_blank' href='https://pontoon.mozilla.org/it/mozillaorg/firefox/android/index.lang?search=test'>&lt;edit in Pontoon&gt;</a>",
            ],
            [
                'firefox_ios',
                'mozilla_org/firefox/android/index.lang:4e0bc9d4',
                'test',
                'it',
                "&nbsp;<a class='edit_link' target='_blank' href='https://pontoon.mozilla.org/it/firefox-for-ios/firefox-ios.xliff?search=test'>&lt;edit in Pontoon&gt;</a>",
            ],
            [
                'firefox_ios',
                'mozilla_org/firefox/android/index.lang:4e0bc9d4',
                '@@missing@@',
                'it',
                '',
            ],
            [
                'firefox_ios',
                'mozilla_org/firefox/android/index.lang:4e0bc9d4',
                '<em class="error">Warning: Missing string</em>',
                'it',
                '',
            ],
            // Test URLencode
            [
                'firefox_ios',
                'mozilla_org/firefox/android/index.lang:4e0bc9d4',
                '%(test)',
                'it',
                "&nbsp;<a class='edit_link' target='_blank' href='https://pontoon.mozilla.org/it/firefox-for-ios/firefox-ios.xliff?search=%25%28test%29'>&lt;edit in Pontoon&gt;</a>",
            ],
        ];
    }

    /**
     * @dataProvider getEditLinkDP
     */
    public function testGetEditLink($a, $b, $c, $d, $e)
    {
        $obj = new _ShowResults();
        $this
            ->string($obj->getEditLink($a, $b, $c, $d))
                ->isEqualTo($e);
    }
}
