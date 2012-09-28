<?php
// Vos classes de tests se situent dans un namespace dédié qui découle du namespace de la clase à tester
namespace tinyL10n\tests\units;

//Inclusion de la classe à tester
require __DIR__ . '/../../classes/ChooseLocale.class.php';

//Inclusion de atoum dans toutes les classes de tests
require_once __DIR__ . '/../../vendor/autoload.php';

use tinyL10n;
use mageekguy\atoum;

class ChooseLocale extends atoum\test
{
    // toutes les méthodes doivent commencer par test
    public function test_getDefaultLocale()
    {
        $obj = new tinyL10n\ChooseLocale();
        $this->assert
                    ->string($obj->getDefaultLocale())
                    ->isEqualTo('en-US');
    }

}
