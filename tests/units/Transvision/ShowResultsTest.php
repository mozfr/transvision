<?php
namespace tests\Transvision;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Transvision\ShowResults;

require_once __DIR__ . '/../bootstrap.php';

class ShowResultsTest extends TestCase
{
    public static function getTMXResultsDP()
    {
        include TMX . 'en-US/cache_en-US_gecko_strings.php';
        $source = $tmx;
        include TMX . 'fr/cache_fr_gecko_strings.php';
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

    #[DataProvider('getTMXResultsDP')]
    public function testGetTMXResults($a, $b, $c)
    {
        $obj = new ShowResults();
        $this
            ->assertSame($obj->getTMXResults($a, $b), $c);
    }

    public static function getTMXResultsReposDP()
    {
        include TMX . 'en-US/cache_en-US_gecko_strings.php';
        $source['test_repo'] = $tmx;
        include TMX . 'fr/cache_fr_gecko_strings.php';
        $target['test_repo'] = $tmx;
        $data = [$source, $target];

        return [
            [
                [
                    ['test_repo', 'browser/chrome/browser/downloads/downloads.dtd:cmd.showMac.label', 'Find in Finder'],
                ],
                $data,
                [
                    'browser/chrome/browser/downloads/downloads.dtd:cmd.showMac.label' => [
                       'test_repo', 'Find in Finder', 'Ouvrir dans le Finder',
                    ],
                ],
            ],
        ];
    }

    #[DataProvider('getTMXResultsReposDP')]
    public function testGetTMXResultsRepos($a, $b, $c)
    {
        $obj = new ShowResults();
        $this
            ->assertEqualsCanonicalizing($obj->getTMXResultsRepos($a, $b), $c);
    }

    public static function getTranslationMemoryResultsDP()
    {
        include TMX . 'en-US/cache_en-US_gecko_strings.php';
        $source = $tmx;
        include TMX . 'fr/cache_fr_gecko_strings.php';
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

    #[DataProvider('getTranslationMemoryResultsDP')]
    public function testGetTranslationMemoryResults($a, $b, $c)
    {
        $obj = new ShowResults();
        $this
            ->assertEqualsCanonicalizing($obj->getTranslationMemoryResults($a, $b, 4), $c);
    }

    public static function formatEntityDP()
    {
        return [
            ['browser/chrome/browser/browser.dtd:historyHomeCmd.label', false],
        ];
    }

    #[DataProvider('formatEntityDP')]
    public function testFormatEntity($a, $b)
    {
        $obj = new ShowResults();
        $this
            ->assertSame($obj->formatEntity($a, $b), '<span class="green">browser</span><span class="superset">&nbsp;&bull;&nbsp;</span>chrome<span class="superset">&nbsp;&bull;&nbsp;</span>browser<span class="superset">&nbsp;&bull;&nbsp;</span>browser.dtd<br><span class="red">historyHomeCmd.label</span>');
    }

    public static function getStringFromEntityDP()
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

    #[DataProvider('getStringFromEntityDP')]
    public function testGetStringFromEntity($a, $b, $c)
    {
        $obj = new ShowResults();
        $this
            ->assertEqualsCanonicalizing($obj->getStringFromEntity($a, $b), $c);
    }

    public static function getStringFromEntityReposDP()
    {
        return [
            [
                'browser/pdfviewer/viewer.properties:last_page.label',
                'test_repo',
                [
                    'test_repo' => [
                        'browser/pdfviewer/viewer.properties:last_page.label' => 'Aller à la dernière page',
                    ],
                ],
                'Aller à la dernière page',
            ],
            [
                'browser/pdfviewer/viewer.properties:last_page.label',
                'test_repo',
                [
                    'test_repo' => [
                        'entity.is.not.found' => 'Aller à la dernière page',
                    ],
                ],
                '@@missing@@',
            ],
            [
                'browser/pdfviewer/viewer.properties:last_page.label',
                'test_repo',
                [
                    'test_repo' => [
                        'browser/pdfviewer/viewer.properties:last_page.label' => '',
                    ],
                ],
                '',
            ],
        ];
    }

    #[DataProvider('getStringFromEntityReposDP')]
    public function testGetStringFromEntityRepos($a, $b, $c, $d)
    {
        $obj = new ShowResults();
        $this
            ->assertEqualsCanonicalizing($obj->getStringFromEntityRepos($a, $b, $c), $d);
    }

    public static function getRepositorySearchResultsDP()
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

    #[DataProvider('getRepositorySearchResultsDP')]
    public function testGeRepositorySearchResults($a, $b, $c)
    {
        $obj = new ShowResults();
        $this
            ->assertEqualsCanonicalizing($obj->getRepositorySearchResults($a, $b), $c);
    }

    public static function searchEntitiesDP()
    {
        return [
            [
                [
                    'test_repo' => [
                        'browser/chrome/browser/migration/migration.properties:sourceNameIE'  => 'test',
                        'browser/chrome/browser/migration/migration.properties:sourceNameIE1' => 'test2',
                    ],
                ],
                '~^browser/chrome/browser/migration/migration.properties:sourceNameIE$~i',
                false,
                [
                    [
                        'test_repo', 'browser/chrome/browser/migration/migration.properties:sourceNameIE',
                    ],
                ],
            ],
            [
                [
                    'test_repo' => [
                        'browser/chrome/browser/migration/migration.properties:sourceNameIE'  => 'test',
                        'browser/chrome/browser/migration/migration.properties:sourceNameIE1' => 'test2',
                    ],
                ],
                '~sourceNameIE~i',
                false,
                [
                    ['test_repo', 'browser/chrome/browser/migration/migration.properties:sourceNameIE'],
                    ['test_repo', 'browser/chrome/browser/migration/migration.properties:sourceNameIE1'],
                ],
            ],
            [
                [
                    'browser/chrome/browser/migration/migration.properties:sourceNameIE'  => 'test',
                    'browser/chrome/browser/migration/migration.properties:sourceNameIE1' => 'test2',
                ],
                '~sourceNameIE~i',
                true,
                [
                    'browser/chrome/browser/migration/migration.properties:sourceNameIE',
                    'browser/chrome/browser/migration/migration.properties:sourceNameIE1',
                ],
            ],
            [
                ['test_repo' => [
                    'browser/chrome/browser/migration/migration.properties:sourceNameIE'  => 'test',
                    'browser/chrome/browser/migration/migration.properties:sourceNameIE1' => 'test2',
                ]],
                '~^sourceNameIE$~i',
                false,
                [
                    ['test_repo', 'browser/chrome/browser/migration/migration.properties:sourceNameIE'],
                ],
            ],
            [
                ['test_repo' => [
                    'browser/chrome/browser/migration/migration.properties:sourceNameIE' => 'test',
                ]],
                '~foobar~',
                false,
                [],
            ],
            [
                [],
                '~foobar~',
                false,
                [],
            ],
        ];
    }

    #[DataProvider('searchEntitiesDP')]
    public function testSearchEntities($a, $b, $c, $d)
    {
        $obj = new ShowResults();
        $this
            ->assertEqualsCanonicalizing($obj->searchEntities($a, $b, $c), $d);
    }

    public function testGetSuggestionsResults()
    {
        $obj = new ShowResults();
        include TMX . 'en-US/cache_en-US_gecko_strings.php';
        $source = $tmx;
        include TMX . 'fr/cache_fr_gecko_strings.php';
        $target = $tmx;
        $this
            ->assertEqualsCanonicalizing($obj->getSuggestionsResults($source, $target, 'Bookmark'),
                            ['Bookmark', 'Bookmarks', 'New Bookmarks',
                             'Bookmark This Page', 'Find in Finder',
                             'Nouveaux marque-pages', 'Marque-page', 'Marque-pages',
                             'Marquer cette page', 'Ouvrir dans le Finder', ]);
        $this
            ->assertEqualsCanonicalizing($obj->getSuggestionsResults($source, $target, 'Bookmark', 10),
                            ['Bookmark', 'Bookmarks', 'New Bookmarks',
                             'Bookmark This Page', 'Find in Finder',
                             'Nouveaux marque-pages', 'Marque-page', 'Marque-pages',
                             'Marquer cette page', 'Ouvrir dans le Finder', ]);
        $this
            ->assertEqualsCanonicalizing($obj->getSuggestionsResults($source, $target, 'Bookmark', 3), ['Bookmark', 'Nouveaux marque-pages']);
    }

    public static function buildErrorStringDP()
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

    #[DataProvider('buildErrorStringDP')]
    public function testBuildErrorString($a, $b, $c)
    {
        $obj = new ShowResults();
        $this
            ->assertEqualsCanonicalizing($obj->buildErrorString($a, $b), $c);
    }

    public static function getEditLinkDP()
    {
        return [
            // Pontoon links
            [
                'pontoon',
                'gecko_strings',
                'browser/chrome/browser/browser.properties:webextPerms.hostDescription.allUrls',
                'test',
                'fr',
                "&nbsp;<a class='edit_link' target='_blank' href='https://pontoon.mozilla.org/fr/firefox/browser/chrome/browser/browser.properties?search_identifiers=true&search_exclude_source_strings=true&search=webextPerms.hostDescription.allUrls'>&lt;edit in Pontoon&gt;</a>",
            ],
            [
                'pontoon',
                'comm_l10n',
                'calendar/chrome/calendar/calendar.dtd:calendar.calendar.label',
                'test',
                'fr',
                "&nbsp;<a class='edit_link' target='_blank' href='https://pontoon.mozilla.org/fr/thunderbird/calendar/chrome/calendar/calendar.dtd?search_identifiers=true&search_exclude_source_strings=true&search=calendar.calendar.label'>&lt;edit in Pontoon&gt;</a>",
            ],
            [
                'pontoon',
                'comm_l10n',
                'chat/commands.properties:dnd',
                'test',
                'fr',
                "&nbsp;<a class='edit_link' target='_blank' href='https://pontoon.mozilla.org/fr/thunderbird/chat/commands.properties?search_identifiers=true&search_exclude_source_strings=true&search=dnd'>&lt;edit in Pontoon&gt;</a>",
            ],
            [
                'pontoon',
                'gecko_strings',
                'browser/browser/preferences/main.ftl:default-content-process-count.label',
                'test',
                'fr',
                "&nbsp;<a class='edit_link' target='_blank' href='https://pontoon.mozilla.org/fr/firefox/browser/browser/preferences/main.ftl?search_identifiers=true&search_exclude_source_strings=true&search=default-content-process-count'>&lt;edit in Pontoon&gt;</a>",
            ],
            [
                'pontoon',
                'gecko_strings',
                'extensions/irc/chrome/about.dtd:chatzilla.label',
                'test',
                '',
                '',
            ],
            [
                'pontoon',
                'gecko_strings',
                'mail/chrome/messenger/addressbook/abContactsPanel.dtd:ccButton.label',
                'test',
                'de',
                "&nbsp;<a class='edit_link' target='_blank' href='https://pontoon.mozilla.org/de/thunderbird/mail/chrome/messenger/addressbook/abContactsPanel.dtd?search_identifiers=true&search_exclude_source_strings=true&search=ccButton.label'>&lt;edit in Pontoon&gt;</a>",
            ],
            [
                'pontoon',
                'comm_l10n',
                'suite/chrome/browser/taskbar.properties:taskbar.tasks.composeMessage.description',
                'test',
                'it',
                "&nbsp;<a class='edit_link' target='_blank' href='https://pontoon.mozilla.org/it/seamonkey/suite/chrome/browser/taskbar.properties?search_identifiers=true&search_exclude_source_strings=true&search=taskbar.tasks.composeMessage.description'>&lt;edit in Pontoon&gt;</a>",
            ],
            [
                'pontoon',
                'mozilla_org',
                'mozilla_org/en/banners/firefox-mobile.ftl:banner-firefox-mobile-get-android-title',
                'test',
                'it',
                "&nbsp;<a class='edit_link' target='_blank' href='https://pontoon.mozilla.org/it/mozillaorg/en/banners/firefox-mobile.ftl?search_identifiers=true&search_exclude_source_strings=true&search=banner-firefox-mobile-get-android-title'>&lt;edit in Pontoon&gt;</a>",
            ],
            [
                'pontoon',
                'android_l10n',
                'android_l10n/mozilla-mobile/fenix/app/src/main/res/values/strings.xml:preference_experiments',
                'test',
                'it',
                "&nbsp;<a class='edit_link' target='_blank' href='https://pontoon.mozilla.org/it/firefox-for-android/mozilla-mobile/fenix/app/src/main/res/values/strings.xml?search_identifiers=true&search_exclude_source_strings=true&search=preference_experiments'>&lt;edit in Pontoon&gt;</a>",
            ],
            [
                'pontoon',
                'firefox_ios',
                'firefox_ios/firefox-ios.xliff:4e0bc9d4',
                'test',
                'it',
                "&nbsp;<a class='edit_link' target='_blank' href='https://pontoon.mozilla.org/it/firefox-for-ios/firefox-ios.xliff?search_identifiers=true&search_exclude_source_strings=true&search=test'>&lt;edit in Pontoon&gt;</a>",
            ],
            [
                'pontoon',
                'firefox_ios',
                'firefox_ios/firefox-ios.xliff:4e0bc9d4',
                '@@missing@@',
                'it',
                '',
            ],
            [
                'pontoon',
                'firefox_ios',
                'firefox_ios/firefox-ios.xliff:4e0bc9d4',
                '<em class="error">Warning: Missing string</em>',
                'it',
                '',
            ],
            // Test URLencode
            [
                'pontoon',
                'firefox_ios',
                'firefox_ios/firefox-ios.xliff:4e0bc9d4',
                '%(test)',
                'it',
                "&nbsp;<a class='edit_link' target='_blank' href='https://pontoon.mozilla.org/it/firefox-for-ios/firefox-ios.xliff?search_identifiers=true&search_exclude_source_strings=true&search=%25%28test%29'>&lt;edit in Pontoon&gt;</a>",
            ],
            // Test unknown tool
            [
                'pontoon.test',
                'firefox_ios',
                'firefox_ios/firefox-ios.xliff:4e0bc9d4',
                '%(test)',
                'it',
                '',
            ],
        ];
    }

    #[DataProvider('getEditLinkDP')]
    public function testGetEditLink($a, $b, $c, $d, $e, $f)
    {
        $obj = new ShowResults();
        $this
            ->assertEqualsCanonicalizing($obj->getEditLink($a, $b, $c, $d, $e), $f);
    }

    public static function buildComponentsFilterDP()
    {
        return [
            [
                ['browser', 'mail'],
                "<div id='filters'>
                <h4>Filter by folder:</h4>
                <a href='#showall' id='showall' class='filter'>Show all results</a>
                 <a href='#browser' id='browser' class='filter'>browser</a> <a href='#mail' id='mail' class='filter'>mail</a>
            </div>\n",
            ],
            [
                [],
                '',
            ],
        ];
    }

    #[DataProvider('buildComponentsFilterDP')]
    public function testBuildComponentsFilter($a, $b)
    {
        $obj = new ShowResults();
        $this
            ->assertEqualsCanonicalizing($obj->buildComponentsFilter($a), $b);
    }
}
