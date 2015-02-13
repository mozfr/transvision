<?php
/**
 * Utility function to get symfony dump() function output to the CLI
 * http://symfony.com/doc/current/components/var_dumper/
 */
function cli_dump()
{
    $cloner = new Symfony\Component\VarDumper\Cloner\VarCloner();
    $dumper = new Symfony\Component\VarDumper\Dumper\CliDumper();
    foreach (func_get_args() as $arg) {
        $dumper->dump($cloner->cloneVar($arg));
    }
}
