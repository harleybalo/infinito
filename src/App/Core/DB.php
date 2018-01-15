<?php
namespace App\Core;

use App\App;
use PDO;

/**
 * DB
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
class DB 
{
    /**
     * @var \App\Core\DB
     */
    public $db;

    /**
     * @var \App\Core\DB
     */
    private static $instance = null;

    /**
     * @var string
     */
    private static $dbName = null;
    
    /**
     * Creates DB Class
     *      This stops the class from being instantiated more than once
     */
    private function __construct()
    {
        $config = App::getConfig();
        try {
            $this->db = new PDO(
                "mysql:host=" . $config['database_host'], 
                $config['database_user'], 
                $config['database_password']
            );

        } catch(\Exception $e) {
            die($e->getMessage());
        }
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Get instance of the DB Class 
     *  - An instance is created to allow the instance to only be loaded once
     * 
     * @param string $dbName
     * @return \App\Core\DB
     */
    public static function getInstance($dbName = false) 
    {
        if (!isset(self::$instance) || !self::$instance) {
            if ($dbName) {
                self::$dbName = $dbName;
            }
            self::$instance = new DB();
        }
        return self::$instance;
    }


    /**
     * Create a new database if not exist
     * 
     * @param string
     * @return void
     */
    public function createDatabase($dbName)
	{
		$dbName = "`".str_replace("`", "``", $dbName)."`";
		$this->db->query("CREATE DATABASE IF NOT EXISTS $dbName");
		$this->db->query("use $dbName");
    }
    
    /**
	 * Check if a table exists in the current database.
	 *
	 * @param string $dbAndTableName Table to search for.
	 * @return bool true if table exists, false if no table found.
	 */
	public function tableExists($dbAndTableName) 
	{
	    try {
	        $result = $this->db->query("SELECT 1 FROM $dbAndTableName LIMIT 1");
	    } catch (\Exception $e) {
	        return false;
	    }
	    return $result !== false;
	}

    /**
     * Execute Query
     * 
     * @param string $sql
     * @return PDO
     */
    public function exec($sql)
    {
        return $this->db->exec($sql);
    }


    /**
     * Insert into database
     * 
     * @param string    $table
     * @param array     $data
     * @return bool
     */
    public function insert($table, $data) 
    {
        try {
            $keys       = array_keys($data);
            $columns    = implode(',', $keys);
            $cols       = implode(',', array_fill(0, count($keys), '?'));
            $values     = array_values($data);
            $stmt = $this->db->prepare("INSERT INTO $table ($columns) VALUES ($cols)");
            $stmt->execute($values);
	    } catch (\Exception $e) {
	        return false;
	    }
	    return true;
    }   
}
