<?php
namespace Transvision\tests\units;

use atoum;

require_once __DIR__ . '/../bootstrap.php';

class Project extends atoum\test
{
    public function testGetRepositories()
    {
        $obj = new \Transvision\Project();
        $repos = ['release', 'beta', 'aurora', 'central', 'gaia', 'gaia_1_2',
                  'gaia_1_3', 'gaia_1_4', 'mozilla_org'];
        $this
            ->array($obj->getRepositories())
                ->isEqualTo($repos);
    }

    public function testGetRepositoriesNames()
    {
        $obj = new \Transvision\Project();
        $repos = [
                'release'     => 'Release',
                'beta'        => 'Beta',
                'aurora'      => 'Aurora',
                'central'     => 'Central',
                'gaia'        => 'Gaia master',
                'gaia_1_2'    => 'Gaia 1.2',
                'gaia_1_3'    => 'Gaia 1.3',
                'gaia_1_4'    => 'Gaia 1.4',
                'mozilla_org' => 'mozilla.org',
            ];
        $this
            ->array($obj->getRepositoriesNames())
                ->isEqualTo($repos);
    }

    public function testGetGaiaRepositories()
    {
        $obj = new \Transvision\Project();
        $repos = ['gaia', 'gaia_1_4', 'gaia_1_3', 'gaia_1_2'];
        $this
            ->array($obj->getGaiaRepositories())
                ->isEqualTo($repos);
    }

    public function testGetDesktopRepositories()
    {
        $obj = new \Transvision\Project();
        $repos = ['release', 'beta', 'aurora', 'central'];
        $this
            ->array($obj->getDesktopRepositories())
                ->isEqualTo($repos);
    }

    public function testGetRepositoryLocales()
    {
        $obj = new \Transvision\Project();
        $this
            ->array($obj->getRepositoryLocales('central'))
                ->isEqualTo(['en-US', 'fr']);
    }

    public function testGetReferenceLocale()
    {
        $obj = new \Transvision\Project();
        $this
            ->string($obj->getReferenceLocale('central'))
                ->isEqualTo('en-US');
        $this
            ->string($obj->getReferenceLocale('mozilla_org'))
                ->isEqualTo('en-GB');
    }

    public function testIsValidRepository()
    {
        $obj = new \Transvision\Project();
        $this
            ->boolean($obj->isValidRepository('central'))
                ->isEqualTo(true);
        $this
            ->boolean($obj->isValidRepository('foo'))
                ->isEqualTo(false);
    }

    public function testGetLocaleInContextDP()
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
     * @dataProvider testGetLocaleInContextDP
     */
    public function testGetLocaleInContext($a, $b, $c)
    {
        $obj = new \Transvision\Project();
        $this
            ->string($obj->getLocaleInContext($a, $b))
                ->isEqualTo($c)
        ;
    }
}
