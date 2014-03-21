<?php
namespace Transvision;

/**
  * CacheViews class
  *
  * Manage cache entries and associated sqlite database
  *
  * @package Transvision
  */
class CacheViews
{
    /**
    * @var object Database handler
    */
    protected $db_handler;

    /**
     * Constructor: create db handler
     *
     * @param boolean $gc Determine if we need garbage collection, default true
     */
    function __construct($gc = true) {
        // Create a db_handler
        $this->db_handler = new \SQLite3(CACHE . 'cache.sqlite');
        $this->db_handler->busyTimeout(300);
        $this->checkCacheStructure();
        if ($gc) {
            $this->cacheGarbageCollector();
        }
    }

    /**
     * Destructor: close db connection
    */
    function __destruct() {
        if ($this->db_handler) {
            $this->db_handler->close();
        }
    }

    /**
     * Check if there's a table called "entries" with the right structure
     */
    private function checkCacheStructure() {
        // If cache.sqlite is missing, file is created (empty) by "new SQLite3"
        // Check it a table called "entries" exists
        try {
            $sql = 'SELECT name FROM sqlite_master WHERE type="table" AND name="entries"';
            $results = $this->db_handler->querySingle($sql);
            if (!$results) {
                // Table "entries" is missing
                $sql = 'CREATE TABLE entries (uid varchar(23) primary key, request text)';
                $this->db_handler->exec($sql);
            }
        }
        catch (Exception $ex) {
            echo "\n<!-- Error checking structure of sqlite cache database -->\n";
        }

        // Create cache/views if missing
        if (!is_dir(CACHE . 'views')) {
            mkdir(CACHE . 'views');
        }
    }

    /**
     * Search a cache entry in the DB. If there's a cache entry but the file is missing,
     * delete the record.
	 *
     * @param string $request Parameters used in request
     * @return string/boolean ID of the cache entry, or false if there's no entry or the associated file is missing
     */
    public function searchCacheEntry($request) {
        try {
            // Search for a cache entry with these parameters
            $sql = 'SELECT * FROM entries WHERE request = :request';
            $query = $this->db_handler->prepare($sql);
            $query->bindValue(':request', $request, SQLITE3_TEXT);
            $results = $query->execute();
            $row = $results->fetchArray();

	        if ($row) {
		        // There's a cache entry, I need to be sure that the file really exists
		        $filename = CACHE . 'views/' . $row[0] . '.gz';

		        if (file_exists($filename)) {
		        	return $row[0];
		        } else {
		        	// There's an item in the DB but not the file. Remove entry
		        	// from DB and consider the file not cached
		        	$sql = "DELETE FROM entries WHERE uid = :uid";
		            $query = $this->db_handler->prepare($sql);
		            $query->bindValue(':uid', $row[0], SQLITE3_TEXT);
		            $results = $query->execute();
		        }
	        }
    	}
    	catch (Exception $ex) {
    		echo "\n<!-- Error connecting to sqlite cache database -->\n";
    	}
        // If a cache entry exists, function has already returned its cache ID
        return false;
    }

    /**
     * Create a cache entry in the DB.
	 *
	 * @param string $uid (unique id)
     * @param array $request (=$_REQUEST)
     */
    public function createCacheEntry($uid, $request) {
    	try {
    		$sql = "INSERT INTO entries(uid, request) VALUES (:uid, :request)";
            $query = $this->db_handler->prepare($sql);
            $query->bindValue(':uid', $uid, SQLITE3_TEXT);
            $query->bindValue(':request', $request, SQLITE3_TEXT);
            $results = $query->execute();
    	}
    	catch (Exception $ex) {
    		echo "\n<!-- Error connecting to sqlite cache database -->\n";
    	}
    }

    /**
     * Read a cache file from filesystem and output it to STDOUT.
     *
     * @param string $uid Unique id of the cache entry
     * @param string $type Type of the request used to set headers
	 */
    public static function readCacheFile($uid, $type = 'html') {
    	$filename = CACHE . 'views/' . $uid . '.gz';
    	ob_start();
        if ($type == 'json') {
            // It's a json file
            header("access-control-allow-origin: *");
            header("Content-type: application/json; charset=UTF-8");
        } elseif ($type == 'jsonp') {
            // It's a jsonp file
            header("access-control-allow-origin: *");
            header("Content-type: application/javascript; charset=UTF-8");
        }
        readgzfile($filename);
        ob_end_flush();
    }

    /**
     * Write a cache file
     *
     * @param string $uid Unique id of the cache entry
     * @param string $html_output Content of the cache entry to be saved
	 */
    public static function writeCacheFile($uid, $html_output) {
        try {
            $gz_file = gzopen(CACHE . 'views/' . $uid . '.gz', 'w9');
            gzwrite($gz_file, $html_output);
            gzclose($gz_file);
        }
    	catch (Exception $ex) {
            echo "\n<!-- Error writing cache entry -->\n";
        }
    }

    /**
     * Check if the cache is obsolete and clean it if necessary
     */
    private function cacheGarbageCollector() {
        $clean_needed = true;

        // cache/lastupdate.txt is generated by glossaire.sh. If it's missing,
        // I don't recreate it to avoid permissions problems, I just consider
        // the cache as non valid

        if (file_exists(CACHE . 'lastcachegc.txt') && file_exists(CACHE . 'lastupdate.txt')) {
            $last_garbagecollection = intval(file_get_contents(CACHE . 'lastcachegc.txt'));
            $last_update = intval(file_get_contents(CACHE . 'lastupdate.txt'));
            if ($last_update < $last_garbagecollection) {
                // Cache content is still valid
                $clean_needed = false;
            }
        }

        if ($clean_needed) {
            // Remove cache files
            $files = glob(CACHE . 'views/*.gz');
            foreach($files as $file) {
                if(is_file($file)) {
                    unlink($file);
                }
            }

            // Empty database
            try {
                $sql = 'DELETE FROM entries';
                $this->db_handler->exec($sql);
            }
            catch (Exception $ex) {
                echo "\n<!-- Error clearing sqlite cache database -->\n";
            }

            // Write timestamp to lastcachegc.txt
            file_put_contents(CACHE . 'lastcachegc.txt', time());
        }
    }
}
