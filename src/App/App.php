<?php
namespace App;

use App\Managers\FileManager;
use App\Managers\CsvManager;
use App\Core\DB;
use App\Core\Schema;
use App\Core\Logger;

/**
 * App
 *
 * PHP Version 5.6
 *
 * @category  Infinito
 * @package   App
 * @author    AD3 <adebalogoon@gmail.com>
 * @copyright 2017 AD3
 * @license   New BSD License
 * @link      https://github.com/harleybalo/infinito.git
 */
class App 
{
    /**
     * @var string
     */
    const UPLOADED_PATH = '/uploaded/';

    /**
     * @var string
     */
    const PROCESSED_PATH = '/processed/';

    /**
     * @var string
     */
    const SCHEMA_PATH = '/schema/';

    /**
     * @var string
     */
    const TABLE = 'transactions';

    /**
     * @var string
     */
    public static $dbName;


    /**
     * @var array
     */
    public static $schema = array();

    /**
     * @var array
     */
    public static $config = array();

    /**
     * Initiate application
     *  - If an instance is already running the process if terminated
     *  - The Database is created for the first time
     *  - The table is created for the first time
     * 
     * @return void
     */
    public static function init()
    {
        self::startJob();
        $db             = DB::getInstance();
        $config         = self::getConfig();
        $dbName         = $config['database_name'];
        self::$dbName   = $dbName;
        Logger::log("[App][init] Creating DB if not exist");
        $db->createDatabase(self::$dbName);

        $transactionTable = $config['database_txn_table'];
        $dbAndTableName = "`$dbName`.`$transactionTable`";
        
        if (!$db->tableExists($dbAndTableName)) {
            Logger::log("[App][init] Creating the TABLE for the first time");
            $tableSchema = self::getSchema();
            $query = Schema::prepareTable($tableSchema, $dbName);
            $db->exec($query);
        }
    }

    /**
     * Depending on the environment the job is started using a directory to determine progress
     *  NOTE: Port (Solo Program) should be used instead of this logic - this will prevent the issue
     * of system crash
     * 
     * @return void
     * @throw Execption
     */
    public static function startJob()
    {
        $jobPath = self::getBaseDir() . '/tmp/active'; 
        if (file_exists($jobPath)) {
            $error = "Job In Progress";
            Logger::log($error);
            throw new \Exception($error, 1);
        } else {
            mkdir($jobPath);
        }
    }

    /**
     * Depending on the environment the job is started using a directory to determine progress
     *  NOTE: Port (Solo Program) should be used instead of this logic - this will prevent the issue
     * of system crash
     * 
     * @return void
     */
    public static function endJob() {
        Logger::log("[App][endjob] Ending cron job");
        $jobPath = self::getBaseDir() . '/tmp/active'; 
        if (file_exists($jobPath)) {
            rmdir($jobPath);
        }
    }

    /**
     * Transaction table name is retrieved from  parameters.ini
     * 
     * @return string
     */
    public static function getTxnTable()
    {
        $config         = self::getConfig();
        $dbName         = $config['database_name'];
        $transactionTable = $config['database_txn_table'];
        return  "`$dbName`.`$transactionTable`";
    }

    /**
     * Application Handler
     * 
     * @var void
     */
    public static function handler() 
    {
        $base   = self::getBaseDir();
        $dir    = trim($base . self::UPLOADED_PATH);
        Logger::log("[App][handler] Reading directory for file");
        $files  = FileManager::readFolderCsvFiles($dir);
        if ($files) {
            $total = count($files);
            Logger::log("[App][init] $total total files found");
            $db = DB::getInstance();
            $transactionTable = self::getTxnTable();
            $csvManager = new CsvManager($transactionTable, $db);
            foreach ($files as $key => $file) {
                $filePath = $dir . $file;
                $csvManager->processCsv($filePath);
            }
        }
        self::endJob();
    }

    /**
     * Returns application base directory
     * 
     * @var string
     */
    public static function getBaseDir() 
    {
        $dir = dirname(dirname(__FILE__));
        return dirname($dir);
    } 
    
    /**
     * Load Transaction schema
     * 
     * @return array
     */
    public static function loadSchema()
    {
        if (self::$schema) {
            return self::$schema;
        }
        $base   = self::getBaseDir();
        $schema = $base . self::SCHEMA_PATH . self::TABLE . '.php';
        if (file_exists($schema)) {
            $schema = include_once($schema);
            return self::$schema = $schema;
        }
    }

    /**
     * Return schema
     * 
     * @var array
     */
    public static function getSchema() 
    {
        $schema = self::loadSchema();     
        $error  = "Schema not provided";

        if ($schema) {
            if (isset($schema['table']) && isset($schema['fields'])) {
                return $schema;
            }
            $error = "Invalid Schema Structure";
        }
        Logger::log($error);
        throw new \Exception($error, 1);
    }

    /**
     * Load config file from parameter.ini
     *      
     * 
     * @return array
     */
    public static function getConfig()
    {
        if (self::$config) {
            return self::$config;
        }

        $configPath = self::getBaseDir() . "/config/parameters.ini";
        return self::$config =  parse_ini_file($configPath);
    }
}
