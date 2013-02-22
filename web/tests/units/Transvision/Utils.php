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
}
