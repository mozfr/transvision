<?php

/**
 * Function sanitizing a string or an array of strings.
 * Returns a string or an array, depending on the input
 *
 */

function secureText($var, $tablo = true)
{
    if (!is_array($var)) {
        $var   = array($var);
        $tablo = false;
    }

    foreach ($var as $item => $value) {
        // CRLF XSS
        $value = str_replace('%0D', '', $value);
        $value = str_replace('%0A', '', $value);

        // Remove html tags and ASCII characters below 32
        $value = filter_var(
            $value,
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_STRIP_LOW
        );

        // Repopulate value
        $var[$item] = $value;
    }

    return ($tablo == true) ? $var : $var[0];
}
?>
