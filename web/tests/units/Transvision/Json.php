<?php
namespace Transvision\tests\units;
require_once __DIR__ . '/../../../vendor/autoload.php';

use atoum;

class Json extends atoum\test
{
    public function fetchJsonDP()
    {
        $local = __DIR__ . '/data/';
        return array(
            array($local . '/empty.json', array()),
            array($local . '/test1.json', array('user_name' => 'Pascal'))
        );
    }

    /**
     * @dataProvider fetchJsonDP
     */
    public function testFetchJson($a, $b)
    {
        $obj = new \Transvision\Json();
        $this
            ->array($obj->fetchJson($a))
                ->isEqualTo($b)
        ;
    }


}
