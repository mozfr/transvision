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
    public static function outputJson(array $data, $jsonp = false)
    {
        $json = json_encode($data);
        $mime = 'application/json';

        if ($jsonp && is_string($jsonp)) {
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
    public static function fetchJson($uri)
    {
        $json = json_decode(file_get_contents($uri), true);

        return $json;
    }
}
