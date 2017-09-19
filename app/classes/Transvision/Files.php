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
     * @param string $dir      path to the file we want to create
     * @param string $contents the string we want to write
     *
     * @return int/false value in bytes if success, false otherwise
     */
    public static function fileForceContents($dir, $contents)
    {
        $parts = explode('/', $dir);
        $file = array_pop($parts);
        $dir = '';

        foreach ($parts as $part) {
            if (! is_dir($dir .= "/{$part}")) {
                mkdir($dir);
            }
        }

        return file_put_contents("{$dir}/{$file}", $contents);
    }

    /**
     * Return the list of folders in a path as an array.
     * Hidden folders starting with a dot (.svn, .htaccess...) are ignored.
     *
     * @param string $path             The directory we want to scan
     * @param array  $excluded_folders Folders to exclude from results
     *
     * @return array Folders in path
     */
    public static function getFoldersInPath($path, $excluded_folders = [])
    {
        // We exclude by default hidden folders starting with a dot
        $folders = array_filter(
            scandir($path),
            function ($item) use ($path) {
                return is_dir("{$path}/{$item}") && ! Strings::startsWith($item, '.');
            }
        );

        return array_diff($folders, $excluded_folders);
    }
}
