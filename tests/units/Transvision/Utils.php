<?php
namespace tests\units\Transvision;

use atoum;
use Transvision\Utils as _Utils;
use Cache\Cache as _Cache;

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
            ['es-ES', 'es-ES', ' checked="checked"']
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
            ['es-ES', 'en-US', false]
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
            [$_GET['t2t'], '']
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
            [$_GET['t2t'], 't2t']
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
            ['some string', null]
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

    public function getHtmlSelectOptionsDP()
    {
        return [
            [
                ['strings' => 'Strings', 'entities'=> 'Entities', 'strings_entities' => 'Strings & Entities'],
                'strings_entities',
                true
            ]
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
                'pages web fréquemment visitées (en cliquant sur « Recharger »,'
            ],
            [
                'toto ',
                'toto'
            ],
            [
                'don\'t escape',
                "don't escape"
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
                'large'
            ],
            [
                'The bookmarks and history system will not be functional because one of files is in use by another application. Some security software can cause this problem.',
                'Le système de marque-pages et',
                'small'
            ],
            [
                'Le système de marque-pages et',
                'The bookmarks and history system will not be functional because one of files is in use by another application. Some security software can cause this problem.',
                'large'
            ],
            [
                'pages web',
                'pa',
                'small'
            ]
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
            ['Add Bookmarks', '', false]
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
            ['es-ES','gaia', 'Hogar'], // test fallback to es locale for gaia
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
        switch ($method)
        {
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
        return [
            [
                (new \DateTime('now'))->modify('-1 day'),
                '1 day ago'
            ],
            [
                (new \DateTime('now'))->modify('-2 seconds'),
                '2 seconds ago'
            ],
            [
                (new \DateTime('now'))->modify('+2 months'),
                '2 months'
            ],
            [
                (new \DateTime('now'))->modify('+1 year'),
                '1 year'
            ],
            [
                (new \DateTime('now'))->modify('-10 hours'),
                '10 hours ago'
            ],
        ];
    }

    /**
     * @dataProvider agoDP
     */
    public function testAgo($a, $b)
    {
        $obj = new _Utils();
        $this
            ->string($obj->ago($a))
                ->contains($b);
    }
}
