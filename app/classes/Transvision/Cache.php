<?php
namespace Transvision;

/**
 * Cache class
 *
 * A simple and fast file caching system.
 *
 * 3 global constants are used: CACHE_ENABLED, CACHE_PATH and CACHE_TIME
 * If those app constants are not available, the system temp folder
 * and the class constants CACHE_ENABLED and CACHE_TIME  are used.
 *
 * @package Transvision
 */
class Cache
{
    /** Fallback for activation of Cache */
    const CACHE_ENABLED = true;

    /** Duration of the cache */
    const CACHE_TIME = 3600;

    /**
     * Create a cache file with serialized data
     *
     * We use PHP serialization and not json for example as it allows
     * storing not only data but also data representations and
     * instantiated objects.
     *
     * @param string $id
     * @param object $data
     * @return boolean True if cache file is created. False if there was an error.
     */
    public static function setKey($id, $data)
    {
        if (! self::isActivated()) {
            return false;
        }

        return file_put_contents(self::getKeyPath($id), serialize($data)) ? true : false;
    }

    /**
     * Get the cached serialized data via its UID
     *
     * @param string $id UID of the cache
     * @param int $ttl Number of seconds for time to live. Default to 0.
     * @return object Unserialized cached data. Or False
     */
    public static function getKey($id, $ttl = 0)
    {
        if (! self::isActivated()) {
            return false;
        }

        if ($ttl == 0) {
            $ttl = defined('CACHE_TIME') ? CACHE_TIME : self::CACHE_TIME;
        }

        return self::isValidKey($id, $ttl)
               ? unserialize(file_get_contents(self::getKeyPath($id)))
               : false;
    }

    /**
     * Flush our cache
     *
     * @return boolean True if files in cache are deleted, False if some files were  not deleted
     */
    public static function flush()
    {
        $files = glob(self::getCachePath() . '*.cache');

        return ! in_array(false, array_map('unlink', $files));
    }

    /**
     * Is the caching system activated?
     * We look at the existence of a CACHE constant and if it's at True
     *
     * @return boolean True if activated, False if deactivated
     */
    private static function isActivated()
    {
        return defined('CACHE_ENABLED') ? CACHE_ENABLED : self::CACHE_ENABLED;
    }

    /**
     * Check if cached data for a key is usable
     *
     * @param string $id UID for the data
     * @param int $ttl Number of seconds for time to live
     * @return boolean if valid data, false if no usable cached data
     */
    private static function isValidKey($id, $ttl)
    {
        // No cache file
        if (! file_exists(self::getKeyPath($id))) {
            return false;
        }

        // Cache is obsolete and was deleted
        if (self::isObsoleteKey($id, $ttl)) {
            self::deleteKey($id);
            return false;
        }

        // All good, cache is valid
        return true;
    }

    /**
     * Delete a cache file thanks to its UID
     *
     * @param string $id UID of the cached data
     * @return boolean True if data was deleted, false if it doesn't exist
     */
    private static function deleteKey($id)
    {
        $file = self::getKeyPath($id);

        if (! file_exists($file)) {
            return false;
        }

        unlink($file);
        clearstatcache(true, $file);

        return true;
    }

    /**
     * Get the path to the cached file
     *
     * File is of the form a840d513be5240045ccc979208f739a168946332.cache
     *
     * @param string $id UID of the cached file
     * @return string path for the file
     */
    private static function getKeyPath($id)
    {
        return self::getCachePath() . sha1($id) . '.cache';
    }

    /**
     * Get the path to the cache folder
     *
     * If a CACHE_PATH global constant is defined, use it,
     * otherwise write to OS folder for temporary files.
     *
     * @return string path to Cache
     */
    private static function getCachePath()
    {
        return defined('CACHE_PATH') ? CACHE_PATH : sys_get_temp_dir() . '/';
    }

    /**
     * Check if the data has not expired
     * @param string $id UID of the cached file
     * @param int $ttl Number of seconds for time to live
     * @return boolean True if file is obsolete, False if it is still fresh
     */
    private static function isObsoleteKey($id, $ttl)
    {
        return filemtime(self::getKeyPath($id)) < (time() - $ttl);
    }
}
