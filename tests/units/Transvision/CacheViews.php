<?php
namespace Transvision\tests\units;
use atoum;

require_once __DIR__ . '/../bootstrap.php';

class CacheViews extends atoum\test
{

    public function cacheStructureDataProvider()
    {
        return array(
            array(
                'id1234',
                'good-request',
                'file content'
                )
        );
    }

     /**
     * @dataProvider cacheStructureDataProvider
     */
    public function testCacheStructure($cache_id, $request, $cache_content)
    {
        // Both db and cache file exist to avoid failures on first run in
        // other methods. In this method I delete and recreate them.

        $db_filename = CACHE . 'cache.sqlite';
        // Delete database if existing
        if (file_exists($db_filename)) {
            unlink($db_filename);
        }
        // Create cache entry if it exists. Calling constructor with false to avoid garbage collection
        $obj = new \Transvision\CacheViews(false);
        $obj->createCacheEntry($cache_id, $request);

        // Delete cache file if it exists
        $cache_filename = CACHE . 'views/'. $cache_id . '.gz';
        if (file_exists($cache_filename)) {
            unlink($cache_filename);
        }
        // Create cache file
        $obj->writeCacheFile($cache_id, $cache_content);

        // Check that both the db and file entry exist, record exists in the db
        $test_result = file_exists($db_filename) &&
                       file_exists($cache_filename) &&
                       $obj->searchCacheEntry($request);
        $this->boolean($test_result)->isTrue();
    }

    public function readCacheDataProvider()
    {
        return array(
            array(
                'id1234',
                'good-request',
                'file content'
                )
        );
    }

    /**
     * @dataProvider readCacheDataProvider
     */
    public function testReadCache($cache_id, $request, $cache_content)
    {
        ob_start();
        readgzfile(CACHE . 'views/'. $cache_id . '.gz');
        $file_content = ob_get_contents();
        ob_end_clean();
        $this->string($file_content)->isEqualTo($cache_content);
    }

    public function searchCacheDataProvider()
    {
        return array(
            array(
                'bad-request',
                false
                ),
            array(
                'good-request',
                'id1234'
                )
        );
    }

    /**
     * @dataProvider searchCacheDataProvider
     */
    public function testSearchCache($request, $cache_id)
    {
        // Calling constructor with false to avoid garbage collection
        $cacheView = new \Transvision\CacheViews(false);

        if (!$cache_id) {
            $this->boolean($cacheView->searchCacheEntry($request))->isFalse();
        } else {
            $this->string($cacheView->searchCacheEntry($request))->isEqualTo($cache_id);
        }

        unset($cacheView);
    }

}
