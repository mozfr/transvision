<?php
namespace Transvision;

/**
 * Json class
 *
 * All the methods we need to work with or generate Json data
 *
 * @package Transvision
 */
class Json
{
    /**
     * Transforms an array into JSON/JSONP
     *
     * @param array $data The data we want to convert to json
     * @param boolean $jsonp Optional, false by default, true to generate JSONP
     * @param boolean $pretty_print Optional. Output as readable JSON_PRETTY_PRINT
     * @return string Json data
     */
    public static function output(array $data, $jsonp = false, $pretty_print = false)
    {
        $json = $pretty_print ? json_encode($data, JSON_PRETTY_PRINT) : json_encode($data);
        $mime = 'application/json';

        if ($jsonp) {
            $mime = 'application/javascript';
            $json = $jsonp . '(' . $json . ')';
        }

        ob_start();
        header("access-control-allow-origin: *");
        header("Content-type: {$mime}; charset=UTF-8");
        echo $json;
        $json = ob_get_contents();
        ob_end_clean();

        return $json;
    }

    /**
     * Fetch a local or remote JSON source and returns as a PHP array
     *
     * @param string $uri Location of the json file, local or remote
     * @return array Data converted to an array
     */
    public static function fetch($uri)
    {
        return json_decode(file_get_contents($uri), true);
    }
}
