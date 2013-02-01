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

}
