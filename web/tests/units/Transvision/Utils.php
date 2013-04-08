<?php
namespace Transvision\tests\units;

require_once __DIR__ . '/../../../vendor/autoload.php';

use atoum;

class Utils extends atoum\test
{
    public function uniqueWordsDataProvider()
    {
        return array('Le système le style du couteau du suisse');
    }

    /**
     * @dataProvider uniqueWordsDataProvider
     */
    public function testUniqueWords($a)
    {
        $obj = new \Transvision\Utils();
        $this
            ->array($obj->uniqueWords($a))
                ->isEqualTo(
                    array(
                        'système',
                        'couteau',
                        'suisse',
                        'style',
                        'le',
                        'Le',
                        'du'
                    )
                );
    }

    public function mtrimDataProvider()
    {
        return array('Le cheval  blanc ');
    }

    /**
     * @dataProvider mtrimDataProvider
     */
    public function testMtrim($a)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->mtrim($a))
                ->isEqualTo('Le cheval blanc');
    }

    public function checkboxDefaultOptionDataProvider1()
    {
        return array(
            array(
                'es-ES',
                'es-ES',
                ' checked="checked"'
                )
        );
    }

    /**
     * @dataProvider checkboxDefaultOptionDataProvider1
     */

    public function testCheckboxDefaultOption1($a, $b, $c)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->checkboxDefaultOption($a, $b))
                ->isEqualTo($c);
    }

    public function checkboxDefaultOptionDataProvider2()
    {
        return array(
            array(
                'es-ES',
                'en-US',
                false
                )
        );
    }

    /**
     * @dataProvider checkboxDefaultOptionDataProvider2
     */

    public function testCheckboxDefaultOption2($a, $b, $c)
    {
        $obj = new \Transvision\Utils();
        $this
            ->boolean($obj->checkboxDefaultOption($a, $b))
                ->isEqualTo($c);
    }

    public function checkBoxStateDataProvider1()
    {
        $_GET['t2t'] = "somedata";
        return array(
            array($_GET['t2t'], '')
        );
    }

    /**
     * @dataProvider checkBoxStateDataProvider1
     */

    public function testCheckboxState1($a, $b)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->checkboxState($a, $b))
                ->isEqualTo(' disabled="disabled"');
    }

    public function checkBoxStateDataProvider2()
    {
        $_GET['t2t'] = "somedata";
        return array(
            array($_GET['t2t'], 't2t')
        );
    }

    /**
     * @dataProvider checkBoxStateDataProvider2
     */

    public function testCheckboxState2($a, $b)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->checkboxState($a, $b))
                ->isEqualTo(' checked="checked"');
    }

    public function checkBoxStateDataProvider3()
    {
        return array(
            array('some string', null)
        );
    }

    /**
     * @dataProvider checkBoxStateDataProvider3
     */

    public function testCheckboxState3($a, $b)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->checkboxState($a, $b))
                ->isEqualTo(' checked="checked"');
    }

    public function formatEntityDataProvider()
    {
        return array(
            array('browser/chrome/browser/browser.dtd:historyHomeCmd.label', false)
        );
    }

    /**
     * @dataProvider formatEntityDataProvider
     */
    public function testFormatEntity($a, $b)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->formatEntity($a, $b))
                ->isEqualTo('<span class="green">browser</span><span class="superset">&nbsp;&sup;&nbsp;</span>chrome<span class="superset">&nbsp;&sup;&nbsp;</span>browser<span class="superset">&nbsp;&sup;&nbsp;</span>browser.dtd<br><span class="red">historyHomeCmd.label</span>')
        ;
    }


    public function getHtmlSelectOptionsDataProvider()
    {
        return array(
            array(
                    array('strings' => 'Strings', 'entities'=> 'Entities', 'strings_entities' => 'Strings & Entities'),
                    'strings_entities',
                    true
            )
        );
    }

    /**
     * @dataProvider getHtmlSelectOptionsDataProvider
     */
    public function testGetHtmlSelectOptions($a, $b, $c)
    {

        $obj = new \Transvision\Utils();
        $this
            ->string($obj->getHtmlSelectOptions($a, $b, $c))
                ->isEqualTo('<option value=strings>Strings</option><option value=entities>Entities</option><option selected value=strings_entities>Strings & Entities</option>')
        ;
    }

    public function cleanSearchDataProvider()
    {
        return array(
            array(
                'pages web   fréquemment visitées (en cliquant sur « Recharger »,',
                'pages web fréquemment visitées (en cliquant sur « Recharger »,'
                ),
            array(
                'toto ',
                'toto'
            )
        );
    }

    /**
     * @dataProvider cleanSearchDataProvider
     */
    public function testCleanSearch($a, $b)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->cleanSearch($a))
                ->isEqualTo($b)
        ;
    }

    public function checkAbnormalStringLengthDataProvider()
    {
        return array(
            array(
                'The bookmarks and history system will not be functional because one of files is in use by another application. Some security software can cause this problem.',
                'Le système de marque-pages et dhistorique ne sera pas opérationnel car lun des fichiers de %S est en cours dutilisation par une autre application. Certains logiciels de sécurité peuvent causer ce problème. Le système de marque-pages et dhistorique ne sera pas opérationnel car lun des fichiers de %S est en cours dutilisation par une autre application. Certains logiciels de sécurité peuvent causer ce problème.',
                'large'
                ),
            array(
                'The bookmarks and history system will not be functional because one of files is in use by another application. Some security software can cause this problem.',
                'Le système de marque-pages et',
                'small'
                ),
            array(
                'Le système de marque-pages et',
                'The bookmarks and history system will not be functional because one of files is in use by another application. Some security software can cause this problem.',
                'large'
                ),
            array(
                'pages web',
                'pa',
                'small'
                )
        );
    }
    /**
     * @dataProvider checkAbnormalStringLengthDataProvider
     */
    public function testCheckAbnormalStringLength($a, $b, $c)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->checkAbnormalStringLength($a,$b))
                ->isEqualTo($c)
        ;
    }

    public function checkAbnormalStringLengthDataProvider2()
    {
        return array(
            array(
                'Add Bookmarks',
                'Ajouter des marque-pages',
                false
                ),
            array(
                'Add Bookmarks',
                '',
                false
            )
        );
    }
    /**
     * @dataProvider checkAbnormalStringLengthDataProvider2
     */
    public function testCheckAbnormalStringLength2($a, $b, $c)
    {
        $obj = new \Transvision\Utils();
        $this
            ->boolean($obj->checkAbnormalStringLength($a,$b))
                ->isEqualTo($c)
        ;
    }

    public function collectLanguageComponentDataProvider()
    {
        $obj = new \Transvision\Utils();
        $components_list = $obj->getBugzillaComponents();
        return array(
            array(
                'en-GB',
                $components_list,
                'en-GB / English (United Kingdom)'
                ),
            array(
                'fr',
                $components_list,
                'fr / French'
                ),
            array(
                'unknow_LANG',
                $components_list,
                'Other'
            )
        );
    }
    /**
     * @dataProvider collectLanguageComponentDataProvider
     */
    public function testCollectLanguageComponent($a, $b, $c)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->collectLanguageComponent($a,$b))
                ->isEqualTo($c)
        ;
    }


    public function startsWithDataProvider()
    {
        return array(
            array(
                'it is raining',
                'it',
                true
                ),
            array(
                ' foobar starts with a nasty space',
                'foobar',
                false
            )
        );
    }
    /**
     * @dataProvider startsWithDataProvider
     */
    public function testStartsWith($a, $b, $c)
    {
        $obj = new \Transvision\Utils();
        $this
            ->boolean($obj->startsWith($a,$b))
                ->isEqualTo($c)
        ;
    }


    public function endsWithDataProvider()
    {
        return array(
            array(
                'it is raining',
                'ing',
                true
                ),
            array(
                'foobar ends with a nasty space ',
                'space',
                false
            )
        );
    }
    /**
     * @dataProvider endsWithDataProvider
     */
    public function testEndsWith($a, $b, $c)
    {
        $obj = new \Transvision\Utils();
        $this
            ->boolean($obj->endsWith($a,$b))
                ->isEqualTo($c)
        ;
    }
}
