<?php
namespace TransvisionResults\tests\units;
require __DIR__ . '/../../classes/ShowResults.class.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use TransvisionResults;
use mageekguy\atoum;

class ShowResults extends atoum\test
{
    public function test_isRTL()
    {
        $obj = new TransvisionResults\ShowResults();
        $this->assert
            ->boolean($obj->isRTL('fr'))
                ->isFalse()
            ->boolean($obj->isRTL('ar'))
                ->isTrue();
    }

}
