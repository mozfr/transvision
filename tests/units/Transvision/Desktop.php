<?php
namespace tests\units\Transvision;

use atoum;
use Transvision\Desktop as _Desktop;

require_once __DIR__ . '/../bootstrap.php';

class Desktop extends atoum\test
{
    public function testGetRepositories()
    {
        $obj = new _Desktop();
        $repos = ['central', 'aurora', 'beta', 'release'];
        $this
            ->array($obj->getRepositories())
                ->isEqualTo($repos);
    }
}
