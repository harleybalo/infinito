<?php

namespace App\Core;
use App\App;

/**
 * Logger
 *
 * PHP Version 5.6
 *
 * @category  Infinito
 * @package   App\Core
 * @author    AD3 <adebalogoon@gmail.com>
 * @copyright 2017 AD3
 * @license   New BSD License
 * @link      https://github.com/harleybalo/infinito.git
 */
class Logger
{
    const LOGFILE = 'stdout.log';

    /**
     * Log process data
     * 
     * @return void
     */
    public static function log($data, $file = false) 
    {
        $file   = $file ? $file : self::LOGFILE;
        $logFile = self::getLogDir() . '/' . $file;

        if (!file_exists($logFile)) {
            $handle = fopen($logFile, 'w');
            fclose($handle);
        }

        file_put_contents($logFile, date("Y-m-d H:i:s") . ' ', FILE_APPEND);
        file_put_contents($logFile, var_export($data, true)."\r\n", FILE_APPEND);
    }



    /**
     * Get Log Directory
     * 
     * @return string
     */
    public static function getLogDir()
    {
        return App::getBaseDir();
    }
}
