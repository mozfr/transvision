<?php
namespace tests\units\Transvision;

use atoum;
use Transvision\Gaia as _Gaia;

require_once __DIR__ . '/../bootstrap.php';

class Gaia extends atoum\test
{
    public function testGetGaiaRepositories()
    {
        $obj = new _Gaia();
        $repos = ['gaia', 'gaia_2_0', 'gaia_1_4', 'gaia_1_3'];
        $this
            ->array($obj->getRepositories())
                ->isEqualTo($repos);
    }
}
