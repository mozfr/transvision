<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class ChooseLocaleTest extends TestCase
{
    public function test_getDefaultLocale()
    {
        $obj = new \tinyl10n\ChooseLocale();
        $this->assertSame($obj->getDefaultLocale(), 'en-US');
    }
}
