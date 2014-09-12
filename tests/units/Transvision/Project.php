<?php
namespace tests\units\Transvision;

use atoum;
use Transvision\Project as _Project;

require_once __DIR__ . '/../bootstrap.php';

class Project extends atoum\test
{
    public function testGetRepositories()
    {
        $obj = new _Project();
        $repos = ['central', 'aurora', 'beta', 'release',
                  'gaia', 'gaia_2_0', 'gaia_1_4', 'gaia_1_3',   'mozilla_org'];
        $this
            ->array($obj->getRepositories())
                ->isEqualTo($repos);
    }

    public function testGetRepositoriesNames()
    {
        $obj = new _Project();
        $repos = [
            'central'     => 'Central',
            'aurora'      => 'Aurora',
            'beta'        => 'Beta',
            'release'     => 'Release',
            'gaia'        => 'Gaia master',
            'gaia_2_0'    => 'Gaia 2.0',
            'gaia_1_4'    => 'Gaia 1.4',
            'gaia_1_3'    => 'Gaia 1.3',
            'mozilla_org' => 'mozilla.org',
        ];
        $this
            ->array($obj->getRepositoriesNames())
                ->isEqualTo($repos);
    }

    public function testGetGaiaRepositories()
    {
        $obj = new _Project();
        $repos = ['gaia', 'gaia_2_0', 'gaia_1_4', 'gaia_1_3'];
        $this
            ->array($obj->getGaiaRepositories())
                ->isEqualTo($repos);
    }

    public function testGetDesktopRepositories()
    {
        $obj = new _Project();
        $repos = ['central', 'aurora', 'beta', 'release'];
        $this
            ->array($obj->getDesktopRepositories())
                ->isEqualTo($repos);
    }

    public function testGetRepositoryLocales()
    {
        $obj = new _Project();
        $this
            ->array($obj->getRepositoryLocales('central'))
                ->isEqualTo(['en-US', 'fr']);
    }

    public function testGetReferenceLocale()
    {
        $obj = new _Project();
        $this
            ->string($obj->getReferenceLocale('central'))
                ->isEqualTo('en-US');
        $this
            ->string($obj->getReferenceLocale('mozilla_org'))
                ->isEqualTo('en-GB');
    }

    public function testIsValidRepository()
    {
        $obj = new _Project();
        $this
            ->boolean($obj->isValidRepository('central'))
                ->isEqualTo(true);
        $this
            ->boolean($obj->isValidRepository('foo'))
                ->isEqualTo(false);
    }

    public function getLocaleInContextDP()
    {
        return [
            ['fr', 'bugzilla', 'fr'],
            ['es', 'bugzilla', 'es-ES'],
            ['pa', 'bugzilla', 'pa-IN'],
            ['gu', 'bugzilla', 'gu-IN'],
            ['sr', 'bugzilla', 'sr'],
            ['sr-Cyrl', 'bugzilla', 'sr'],
            ['sr-Latn', 'bugzilla', 'sr'],
            ['sr-Latn', 'gaia', 'sr-Latn'],
            ['sr', 'gaia', 'sr-Cyrl'],
            ['es', 'gaia', 'es'],
            ['es-CL', 'gaia', 'es'],
            ['es-ES', 'gaia_1_4', 'es'],
            ['es', 'mozilla_org', 'es-ES'],
            ['es-AR', 'mozilla_org', 'es-AR'],
            ['sr-Cyrl', 'mozilla_org', 'sr'],
            ['es-ES', 'foobar', 'es-ES'],
            ['fr', 'foobar', 'fr'],
        ];
    }

    /**
     * @dataProvider getLocaleInContextDP
     */
    public function testGetLocaleInContext($a, $b, $c)
    {
        $obj = new _Project();
        $this
            ->string($obj->getLocaleInContext($a, $b))
                ->isEqualTo($c);
    }
}
