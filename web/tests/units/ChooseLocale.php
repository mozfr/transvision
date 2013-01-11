<?php
// Vos classes de tests se situent dans un namespace dédié qui découle du namespace de la clase à tester
namespace tinyl10n\tests\units;

//Inclusion de la classe à tester
// require __DIR__ . '/../../classes/ChooseLocale.class.php';

//Inclusion de atoum dans toutes les classes de tests
require_once __DIR__ . '/../../vendor/autoload.php';

use tinyl10n;
use atoum;

class ChooseLocale extends atoum\test
{
    // toutes les méthodes doivent commencer par test
    public function test_getDefaultLocale()
    {
        $obj = new tinyl10n\ChooseLocale();
        $this->assert
                    ->string($obj->getDefaultLocale())
                    ->isEqualTo('en-US');
    }

}
