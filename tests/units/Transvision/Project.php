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
        $repos = ['release', 'beta', 'central',
                  'mozilla_org', ];
        $this
            ->array($obj->getRepositories())
                ->isEqualTo($repos);
    }

    public function testGetRepositoriesNames()
    {
        $obj = new _Project();
        $repos = [
            'release'     => 'Release',
            'beta'        => 'Beta',
            'central'     => 'Central',
            'mozilla_org' => 'mozilla.org',
        ];
        $this
            ->array($obj->getRepositoriesNames())
                ->isEqualTo($repos);
    }

    public function testGetDesktopRepositories()
    {
        $obj = new _Project();
        $repos = ['release', 'beta', 'central'];
        $this
            ->array($obj->getDesktopRepositories())
                ->isEqualTo($repos);
    }

    public function isDesktopRepositoryDP()
    {
        return [
            ['central', true],
            ['release', true],
            ['firefox_ios', false],
            ['mozilla_org', false],
            ['randomrepo', false],
        ];
    }

    /**
     * @dataProvider isDesktopRepositoryDP
     */
    public function testIsDesktopRepository($a, $b)
    {
        $obj = new _Project();
        $this
            ->boolean($obj->isDesktopRepository($a))
                ->isEqualTo($b);
    }

    public function getRepositoryLocalesDP()
    {
        return [
            ['central', ['en-US', 'fr']],
            ['release', ['en-US']],
        ];
    }

    /**
     * @dataProvider getRepositoryLocalesDP
     */
    public function testGetRepositoryLocales($a, $b)
    {
        $obj = new _Project();
        $this
            ->array($obj->getRepositoryLocales($a))
                ->isEqualTo($b);
    }

    public function getLocaleRepositoriesDP()
    {
        return [
            ['fr', ['central', 'mozilla_org']],
            ['foobar', []],
        ];
    }

    /**
     * @dataProvider getLocaleRepositoriesDP
     */
    public function testGetLocaleRepositories($a, $b)
    {
        $obj = new _Project();
        $this
            ->array($obj->getLocaleRepositories($a))
                ->isEqualTo($b);
    }

    public function testGetReferenceLocale()
    {
        $obj = new _Project();
        $this
            ->string($obj->getReferenceLocale('central'))
                ->isEqualTo('en-US');
        $this
            ->string($obj->getReferenceLocale('mozilla_org'))
                ->isEqualTo('en-US');
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
            ['es', 'mozilla_org', 'es-ES'],
            ['es-AR', 'mozilla_org', 'es-AR'],
            ['sr-Cyrl', 'mozilla_org', 'sr'],
            ['es-ES', 'foobar', 'es-ES'],
            ['fr', 'foobar', 'fr'],
            ['es-ES', 'firefox_ios', 'es'],
            ['es', 'firefox_ios', 'es'],
            ['son', 'firefox_ios', 'ses'],
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

    public function getLocaleToolDP()
    {
        return [
            ['ar', 'locamotion'],
            ['fr', ''],
            ['sr', 'pontoon'],
            ['te', 'pontoon'],
        ];
    }

    /**
     * @dataProvider getLocaleToolDP
     */
    public function testGetLocaleTool($a, $b)
    {
        $obj = new _Project();
        $this
            ->string($obj->getLocaleTool($a))
                ->isEqualTo($b);
    }
}
