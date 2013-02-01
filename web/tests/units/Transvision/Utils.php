<?php
namespace Transvision\tests\units;

require_once __DIR__ . '/../../../vendor/autoload.php';

use atoum;

class Utils extends atoum\test
{
    public function dataProviderUniqueWords()
    {
        return array('Le système le style du couteau du suisse');
    }

    /**
     * @dataProvider dataProviderUniqueWords
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
                )
        ;
    }

    public function dataProvider_mtrim()
    {
        return array('Le cheval  blanc ');
    }

    /**
     * @dataProvider dataProvider_mtrim
     */
    public function test_mtrim($a)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->mtrim($a))
                ->isEqualTo('Le cheval blanc')
        ;
    }

    public function dataProvider1_checkBoxState()
    {
        $_GET['t2t'] = "somedata";
        return array(
            array($_GET['t2t'], '')
        );
    }

    /**
     * @dataProvider dataProvider1_checkBoxState
     */

    public function testCheckboxState1($a, $b)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->checkboxState($a, $b))
                ->isEqualTo(' disabled="disabled"')
        ;
    }

    public function dataProvider2_checkBoxState()
    {
        $_GET['t2t'] = "somedata";
        return array(
            array($_GET['t2t'], 't2t')
        );
    }

    /**
     * @dataProvider dataProvider2_checkBoxState
     */

    public function testCheckboxState2($a, $b)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->checkboxState($a, $b))
                ->isEqualTo(' checked="checked"')
        ;
    }

    public function dataProvider3_checkBoxState()
    {
        return array(
            array('some string', null)
        );
    }

    /**
     * @dataProvider dataProvider3_checkBoxState
     */

    public function testCheckboxState3($a, $b)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->checkboxState($a, $b))
                ->isEqualTo(' checked="checked"')
        ;
    }

    public function dataProvider_formatEntity()
    {
        return array(
            array('browser/chrome/browser/browser.dtd:historyHomeCmd.label', false)
        );
    }

    /**
     * @dataProvider dataProvider_formatEntity
     */
    public function testFormatEntity($a, $b)
    {
        $obj = new \Transvision\Utils();
        $this
            ->string($obj->formatEntity($a, $b))
                ->isEqualTo('<span class="green">browser</span><span class="superset">&nbsp;&sup;&nbsp;</span>chrome<span class="superset">&nbsp;&sup;&nbsp;</span>browser<span class="superset">&nbsp;&sup;&nbsp;</span>browser.dtd<br><span class="red">historyHomeCmd.label</span>')
        ;
    }

}
