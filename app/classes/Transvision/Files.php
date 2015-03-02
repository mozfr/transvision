<?php
namespace Transvision;

/**
 * Files class
 *
 * Utility functions for IO
 *
 * @package Transvision
 */
class Files
{
    /**
     * This is the equivalent of file_put_contents() but in addition
     * it will create the folders and subfolders to the target if
     * they don't exist
     *
     * @param  string    $dir      path to the file we want to create
     * @param  string    $contents the string we want to write
     * @return int/false value in bytes if success, false otherwise
     */
    public static function fileForceContents($dir, $contents)
    {
        $parts = explode('/', $dir);
        $file  = array_pop($parts);
        $dir   = '';

        foreach ($parts as $part) {
            if (! is_dir($dir .= "/{$part}")) {
                mkdir($dir);
            }
        }

        return file_put_contents("{$dir}/{$file}", $contents);
    }

    /**
     * Return the list of files in a folder as an array.
     * Hidden files starting with a dot (.svn, .htaccess...) are ignored.
     *
     * @param  string $folder         the directory we want to scan
     * @param  array  $excluded_files Files to exclude from results
     * @return array  Files in the folder
     */
    public static function getFilenamesInFolder($folder, $excluded_files = [])
    {
        /*
            Here we exclude by default hidden files starting with a dot and
            the . and .. symbols for directories
        */
        $files = array_filter(
            scandir($folder),
            function ($item) {
                return !Strings::startsWith($item, '.');
            }
        );

        return array_diff($files, $excluded_files);
    }
}
