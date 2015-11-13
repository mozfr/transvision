<?php
namespace tests\units\Transvision;

use atoum;
use Cache\Cache as _Cache;
use DateTime;
use Transvision\Utils as _Utils;

require_once __DIR__ . '/../bootstrap.php';

class Utils extends atoum\test
{
    public function uniqueWordsDP()
    {
        return ['achat des couteaux suisses'];
    }

    /**
     * @dataProvider uniqueWordsDP
     */
    public function testUniqueWords($a)
    {
        $obj = new _Utils();
        $this
            ->array($obj->uniqueWords($a))
                ->isEqualTo(
                    [
                        'couteaux',
                        'suisses',
                        'achat',
                        'des',
                    ]
                );
    }

    public function checkboxDefaultOption1DP()
    {
        return [
            ['es-ES', 'es-ES', ' checked="checked"'],
        ];
    }

    /**
     * @dataProvider checkboxDefaultOption1DP
     */
    public function testCheckboxDefaultOption1($a, $b, $c)
    {
        $obj = new _Utils();
        $this
            ->string($obj->checkboxDefaultOption($a, $b))
                ->isEqualTo($c);
    }

    public function checkboxDefaultOption2DP()
    {
        return [
            ['es-ES', 'en-US', false],
        ];
    }

    /**
     * @dataProvider checkboxDefaultOption2DP
     */
    public function testCheckboxDefaultOption2($a, $b, $c)
    {
        $obj = new _Utils();
        $this
            ->boolean($obj->checkboxDefaultOption($a, $b))
                ->isEqualTo($c);
    }

    public function checkBoxState1DP()
    {
        $_GET['t2t'] = 'somedata';

        return [
            [$_GET['t2t'], ''],
        ];
    }

    /**
     * @dataProvider checkBoxState1DP
     */
    public function testCheckboxState1($a, $b)
    {
        $obj = new _Utils();
        $this
            ->string($obj->checkboxState($a, $b))
                ->isEqualTo(' disabled="disabled"');
    }

    public function checkBoxState2DP()
    {
        $_GET['t2t'] = 'somedata';

        return [
            [$_GET['t2t'], 't2t'],
        ];
    }

    /**
     * @dataProvider checkBoxState2DP
     */
    public function testCheckboxState2($a, $b)
    {
        $obj = new _Utils();
        $this
            ->string($obj->checkboxState($a, $b))
                ->isEqualTo(' checked="checked"');
    }

    public function checkBoxState3DP()
    {
        return [
            ['some string', null],
        ];
    }

    /**
     * @dataProvider checkBoxState3DP
     */
    public function testCheckboxState3($a, $b)
    {
        $obj = new _Utils();
        $this
            ->string($obj->checkboxState($a, $b))
                ->isEqualTo(' checked="checked"');
    }

    public function markStringDP()
    {
        return [
            ['cronologia', 'cronologia di navigazione', '←cronologia→ di navigazione'],
            ['cronologia', 'Cronologia di navigazione', '←Cronologia→ di navigazione'],
            ['test', 'Cronologia di navigazione', 'Cronologia di navigazione'],
            ['Überdeckende', 'Überdeckende Popups öffnen', '←Überdeckende→ Popups öffnen'],
            ['überdeckende', 'Überdeckende Popups öffnen', '←Überdeckende→ Popups öffnen'],
            ['Überdeckende', 'überdeckende Popups öffnen', '←überdeckende→ Popups öffnen'],
        ];
    }

    /**
     * @dataProvider markStringDP
     */
    public function testMarkString($a, $b, $c)
    {
        $obj = new _Utils();
        $this
            ->string($obj->markString($a, $b, $c))
                ->isEqualTo($c);
    }

    public function highlightStringDP()
    {
        return [
            ['←cronologia→ di navigazione', '<span class=\'highlight\'>cronologia</span> di navigazione'],
            ['←Cronologia→ di navigazione', '<span class=\'highlight\'>Cronologia</span> di navigazione'],
            ['←servi←ce→→', '<span class=\'highlight\'>service</span>'],
            ['Cronologia di navigazione', 'Cronologia di navigazione'],
        ];
    }

    /**
     * @dataProvider highlightStringDP
     */
    public function testHighlightString($a, $b)
    {
        $obj = new _Utils();
        $this
            ->string($obj->highlightString($a))
                ->isEqualTo($b);
    }

    public function getHtmlSelectOptionsDP()
    {
        return [
            [
                ['strings' => 'Strings', 'entities' => 'Entities', 'strings_entities' => 'Strings & Entities'],
                'strings_entities',
                true,
            ],
        ];
    }

    /**
     * @dataProvider getHtmlSelectOptionsDP
     */
    public function testGetHtmlSelectOptions($a, $b, $c)
    {
        $obj = new _Utils();
        $this
            ->string($obj->getHtmlSelectOptions($a, $b, $c))
                ->isEqualTo('<option value=strings>Strings</option><option value=entities>Entities</option><option selected value=strings_entities>Strings & Entities</option>');
    }

    public function cleanStringDP()
    {
        return [
            [
                'pages web   fréquemment visitées (en cliquant sur « Recharger »,',
                'pages web fréquemment visitées (en cliquant sur « Recharger »,',
            ],
            [
                'toto ',
                'toto',
            ],
            [
                'don\'t escape',
                "don't escape",
            ],
        ];
    }

    /**
     * @dataProvider cleanStringDP
     */
    public function testCleanString($a, $b)
    {
        $obj = new _Utils();
        $this
            ->string($obj->cleanString($a))
                ->isEqualTo($b);
    }

    public function checkAbnormalStringLengthDP()
    {
        return [
            [
                'The bookmarks and history system will not be functional because one of files is in use by another application. Some security software can cause this problem.',
                'Le système de marque-pages et dhistorique ne sera pas opérationnel car lun des fichiers de %S est en cours dutilisation par une autre application. Certains logiciels de sécurité peuvent causer ce problème. Le système de marque-pages et dhistorique ne sera pas opérationnel car lun des fichiers de %S est en cours dutilisation par une autre application. Certains logiciels de sécurité peuvent causer ce problème.',
                'large',
            ],
            [
                'The bookmarks and history system will not be functional because one of files is in use by another application. Some security software can cause this problem.',
                'Le système de marque-pages et',
                'small',
            ],
            [
                'Le système de marque-pages et',
                'The bookmarks and history system will not be functional because one of files is in use by another application. Some security software can cause this problem.',
                'large',
            ],
            [
                'pages web',
                'pa',
                'small',
            ],
        ];
    }

    /**
     * @dataProvider checkAbnormalStringLengthDP
     */
    public function testCheckAbnormalStringLength($a, $b, $c)
    {
        $obj = new _Utils();
        $this
            ->string($obj->checkAbnormalStringLength($a, $b))
                ->isEqualTo($c);
    }

    public function checkAbnormalStringLength2DP()
    {
        return [
            ['Add Bookmarks', 'Ajouter des marque-pages', false],
            ['Add Bookmarks', '', false],
        ];
    }

    /**
     * @dataProvider checkAbnormalStringLength2DP
     */
    public function testCheckAbnormalStringLength2($a, $b, $c)
    {
        $obj = new _Utils();
        $this
            ->boolean($obj->checkAbnormalStringLength($a, $b))
                ->isEqualTo($c);
    }

    public function getRepoStringsDP()
    {
        return [
            ['fr', 'central', 'Ouvrir dans le Finder'],
            ['es-ES', 'gaia', 'Hogar'], // test fallback to es locale for gaia
        ];
    }

    /**
     * @dataProvider getRepoStringsDP
     */
    public function testGetRepoStrings($a, $b, $c)
    {
        if (! getenv('TRAVIS')) {
            $obj = new _Utils();
            $this
                ->array($obj->getRepoStrings($a, $b))
                    ->contains($c);
        }
    }

    public function getOrSetDP()
    {
        return [
            [
                ['en-US', 'ar', 'fr'],
                'fr',
                'en-US',
                'fr',
                ],
            [
                ['fa', 'es-ES', 'fr'],
                'it',
                'en-US',
                'en-US',
                ],
            [
                ['fa', 'es-ES', 'FR'],
                'fr',
                'en-US',
                'en-US',
                ],
            [
                ['fa', 'es-ES', 'FR'],
                'es',
                'en-US',
                'en-US',
                ],
        ];
    }

    /**
     * @dataProvider getOrSetDP
     */
    public function testGetOrSet($a, $b, $c, $d)
    {
        $obj = new _Utils();
        $this
            ->string($obj->getOrSet($a, $b, $c))
                ->isEqualTo($d);
    }

    public function afterTestMethod($method)
    {
        switch ($method) {
            case 'testGetRepoStrings':
                // Destroy cached files created by getRepoStrings()
                $obj = new _Cache();
                $obj->flush();
            break;
        }
    }

    public function redYellowGreenDP()
    {
        return [
            ['99.92', '10,255,0'],
        ];
    }

    /**
     * @dataProvider redYellowGreenDP
     */
    public function testRedYellowGreen($a, $b)
    {
        $obj = new _Utils();
        $this
            ->string($obj->redYellowGreen($a))
                ->contains($b);
    }

    public function pluralizeDP()
    {
        return [
            ['42', 'test', '42 tests'],
            ['1', 'test', '1 test'],
        ];
    }

    /**
     * @dataProvider pluralizeDP
     */
    public function testPluralize($a, $b, $c)
    {
        $obj = new _Utils();
        $this
            ->string($obj->pluralize($a, $b))
                ->contains($c);
    }

    public function agoDP()
    {
        $data = [
            [
                (new DateTime())->modify('-2 seconds'),
                (new DateTime()),
                '2 seconds ago',
            ],
            [
                (new DateTime())->modify('-10 hours'),
                (new DateTime()),
                '10 hours ago',
            ],
            [
                (new DateTime())->modify('-1 day'),
                (new DateTime()),
                '1 day ago',
            ],
            [
                (new DateTime())->modify('+2 months'),
                (new DateTime()),
                '2 months',
            ],
            [
                (new DateTime())->modify('+1 year'),
                (new DateTime()),
                '1 year',
            ],
        ];

        // If running tests locally, check also the behavior without providing
        // a reference date
        if (! getenv('TRAVIS')) {
            $data = array_merge(
                $data,
                [
                    [
                        (new DateTime())->modify('-2 seconds'),
                        '',
                        '2 seconds ago',
                    ],
                    [
                        (new DateTime())->modify('-1 hour'),
                        '',
                        '1 hour ago',
                    ],
                ]
            );
        }

        return $data;
    }

    /**
     * @dataProvider agoDP
     */
    public function testAgo($a, $b, $c)
    {
        $obj = new _Utils();
        $this
            ->string($obj->ago($a, $b))
                ->contains($c);
    }

    public function redirectToAPIDP()
    {
        return [
            [
                'http://transvision.mozfr.org/string/?entity=browser/chrome/browser/downloads/downloads.properties:stateStarting&repo=central',
                'http://transvision.mozfr.org/string/?entity=browser/chrome/browser/downloads/downloads.properties:stateStarting&repo=central&json',
            ],
            [
                'http://transvision.mozfr.org/api/v1/versions/',
                'http://transvision.mozfr.org/api/v1/versions/?json',
            ],
        ];
    }

    /**
     * @dataProvider redirectToAPIDP
     */
    public function testRedirectToAPI($a, $b)
    {
        $obj = new _Utils();
        $_SERVER["REQUEST_URI"] = $a;
        $_SERVER['QUERY_STRING'] = isset(parse_url($a)['query'])
            ? parse_url($a)['query']
            : null;
        $this
            ->string($obj->redirectToAPI())
                ->isEqualTo($b);
    }
}
