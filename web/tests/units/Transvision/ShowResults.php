<?php
namespace Transvision\tests\units;
require_once __DIR__ . '/../../../vendor/autoload.php';

use atoum;

class ShowResults extends atoum\test
{
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
        $obj = new \Transvision\ShowResults();
        $this
            ->string($obj->formatEntity($a, $b))
                ->isEqualTo('<span class="green">browser</span><span class="superset">&nbsp;&sup;&nbsp;</span>chrome<span class="superset">&nbsp;&sup;&nbsp;</span>browser<span class="superset">&nbsp;&sup;&nbsp;</span>browser.dtd<br><span class="red">historyHomeCmd.label</span>')
        ;
    }


}
