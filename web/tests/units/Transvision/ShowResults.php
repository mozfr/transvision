<?php
namespace Transvision\tests\units;
require_once __DIR__ . '/../../../vendor/autoload.php';

use atoum;

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
                ->isEqualTo('<span class="green">browser</span><span class="superset">&nbsp;&sup;&nbsp;</span>chrome<span class="superset">&nbsp;&sup;&nbsp;</span>browser<span class="superset">&nbsp;&sup;&nbsp;</span>browser.dtd<br><span class="red">historyHomeCmd.label</span>')
        ;
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
                ->isIdenticalTo($c)
        ;
    }


}
