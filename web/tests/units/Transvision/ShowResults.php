<?php
namespace Transvision\tests\units;
require __DIR__ . '/../../classes/Transvision/ShowResults.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Transvision;
use mageekguy\atoum;

class ShowResults extends atoum\test
{
    public function test_isRTL()
    {
        $obj = new Transvision\ShowResults();
        $this->assert
            ->boolean($obj->isRTL('fr'))
                ->isFalse()
            ->boolean($obj->isRTL('ar'))
                ->isTrue();
    }

}
