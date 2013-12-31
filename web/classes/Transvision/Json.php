<?php
namespace Transvision;

class Json
{
    /*
     * Return a json/jsonp representation of data and exits;
     *
     * @param  array  data in json field
     * @param  string jsonp function name, default to false
     * @return json feed
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

    /*
     * Return a array from a local or remote file json file
     *
     * @param  string  uri of the resource
     * @return array
     */
    public static function fetch($uri)
    {
        return json_decode(file_get_contents($uri), true);
    }
}
