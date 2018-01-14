<?php
namespace App\Managers;

use App\Core\Logger;

/**
 * FileManager
 *
 * PHP Version 5.6
 *
 * @category  Infinito
 * @package   App\Managers
 * @author    AD3 <adebalogoon@gmail.com>
 * @copyright 2017 AD3
 * @license   New BSD License
 * @link      https://github.com/harleybalo/infinito.git
 */
class FileManager 
{
    /**
     * Checks if directory is empty
     */
    public static function isDirectoryEmpty($dir) 
    {
        $handle = opendir($dir);
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                return false;
            }
        }
        return true;
    }

    /**
     * Read all files in the directory including non-csv
     * 
     * @param string $dir
     * @return array
     */
    public static function readFolderCsvFiles($dir)
    {
        $handle = opendir($dir);
        $files  = [];
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $path = pathinfo($entry);
                if (isset($path['extension']) && strtolower($path['extension']) === 'csv') {
                    $files[] = $entry;
                }
            }
        }
        return $files;
    }

    /**
     * Move file from one directory to another
     * 
     * @param string $filePath
     * @param string $newPath
     * @return void
     */
    public static function moveFile($filePath, $newPath) 
    {
        Logger::log("[FileManager][moveFile] moving file to processed directory");
        rename($filePath, $newPath);
    }
}