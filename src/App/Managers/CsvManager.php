<?php
namespace App\Managers;

use App\Core\Logger;
use App\Core\DB;
use App\Core\Validator;
use App\App;
use App\Managers\FileManager;

/**
 * CsvManager
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

class CsvManager
{

    /**
     * @var array
     */
    protected $schema = []; 

    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @var string
     */
    protected $transactionTable;

    /**
     * @var \App\Core\DB
     */
    protected $db;

    /**
     * Create CsvManager
     * 
     * @param string $transactionTable
     * @param \App\Core\DB
     */
    public function __construct($transactionTable, \App\Core\DB $db) 
    {
        $this->db = $db;
        $this->schema = self::getFields();
        $this->transactionTable = $transactionTable;
    }

    /**
     * Process each file in the directory and move them to the processed folder
     * 
     * @return void
     */
    public function processCsv($filepath)
    {
        $pathInfo   = pathinfo($filepath);
        $fileName   = $pathInfo['filename'];
        $newPath    = App::getBaseDir() . '/processed/' . $fileName . '.csv';
        $i          = 0;
        $x          = 0;

        if (($handle = fopen($filepath, "r")) !== false) {
            $milliseconds = round(microtime(true));
            $error = false;
            while (($data = fgetcsv($handle)) !== false) {
                if (array(null) !== $data) {
                    if ($i == 0) {
                        $columns    = array_flip($data);
                        $validated  = $this->validateColumns($columns);
                        if (!$validated) {
                            $error = "[CSVManager][processCsv] Invalid Column structure FILE: " . $fileName;
                            Logger::log($error);
                            break;
                        }
                    } else {
                        $dataToValidate = $this->addKeysToData($columns, $data);
                        $dataToInsert   = $this->validateValues($dataToValidate);
                        if (!$dataToInsert['errors'] && $dataToInsert['insert']) {
                            $x++;
                            $this->db->insert($this->transactionTable, $dataToInsert['insert']);
                        } else {
                            $msg = "[CsvManager][processCsv] Filed(s) Error found on file $fileName " . var_export($dataToInsert['errors'], true);
                            $errorFile = 'stderr.log';
                            Logger::log($msg, $errorFile);
                        }
                    }
                    $i++; 
                }
            }
            fclose($handle);
            $milliseconds = round(microtime(true)) - $milliseconds;
            $msg = "[CSVManager][processCsv] %s -  Processed $x items  in %0.2f seconds";
            $log = sprintf($msg, $fileName, $milliseconds);
            Logger::log($log);
            FileManager::moveFile($filepath, $newPath);
        }
    }

    /**
     * Add keys to data
     * 
     * @param array $columns
     * @param array $data
     * @return array
     */
    public function addKeysToData($columns, $data)
    {
        $arrayKeys      = array_keys($columns);
        $arrayValues    = array_values($data);
        $res            = [];
        foreach ($arrayKeys as $key => $value) {
            if (isset($arrayValues[$key])) {
                $res[$value] =  $arrayValues[$key];
            }
        }
        return $res;
    }

    /**
     * Validate Columns data
     * 
     * @var array $columns
     * @return array
     */
    public function validateColumns($columns) 
    {
        $fields = $this->schema['fields'];
        unset($fields['id'], $fields['created_at']);
        return Validator::validateColumns($columns, $fields);
    }

    /**
     * Validate Data before insertion
     * 
     * @var array $columns
     * @return array
     */
    public function validateValues($data)
    {
        if (!$this->rules) {
            $fields = $this->schema['fields'];
            $this->rules = Validator::getRulesFromSchema($fields);
        }

        return Validator::validateValues($data, $this->rules);
    }

    /**
     * Get columns and structure from schema
     * 
     * @throw Exception
     * @return array
     */
    public static function getFields() 
    {
        return App::getSchema();
    }
}