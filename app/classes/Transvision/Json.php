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
     * @param boolean $convert_data Determine if file needs conversion (default option) or is already in json/jsonp format
     * @param boolean $full_output Generate complete output with headers, default to false
     * @return string Json data
     */
    public static function output($data, $jsonp = false, $pretty_print = false, $convert_data = true, $full_output = false)
    {
        if ($convert_data) {
            // Convert $data in json/jsonp format
            $json = $pretty_print ? json_encode($data, JSON_PRETTY_PRINT) : json_encode($data);
            if ($jsonp) {
                $json = $jsonp . '(' . $json . ')';
            }
        } else {
            // I don't need any conversion, $data is already in json/jsonp format
            $json = $data;
        }


        if ($full_output) {
            // I need to generate a full output with headers
            $mime = 'application/json';
            if ($jsonp) {
                $mime = 'application/javascript';
            }
            ob_start();
            header("access-control-allow-origin: *");
            header("Content-type: {$mime}; charset=UTF-8");
            echo $json;
            $json = ob_get_contents();
            ob_end_clean();
        }

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
