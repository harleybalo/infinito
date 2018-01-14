<?php
namespace App\Core;

/**
 * Schema
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
class Schema
{

	/**
	 * Prepare Schema for Query
	 * 
	 * @param array $schema
	 * @param string $dbName
	 * @return string
	 */
	public static function prepareTable($schema, $dbName)
	{
		$table 	= $schema['table'];
		$fields = $schema['fields'];
		$query 	= "CREATE table `$dbName`.`$table` (`id` INT(11) AUTO_INCREMENT PRIMARY KEY, ";

		$ignoreQuotes =  ['TIMESTAMP', 'DATETIME', 'DATE', 'TIME', 'YEAR'];

		foreach ($fields as $key => $field) {
			if (isset($field['name']) && isset($field['type'])) {
				$type 		= strtoupper($field['type']);
				$name 		= " `" . $field['name'] . "` ";
				$nullable	= isset($field['nullable']) ? $field['nullable'] : false;
				$default 	= isset($field['default']) ? $field['default'] : null;
				$comment 	= isset($field['comment']) ? $field['comment'] : false;
				$length 	= isset($field['length']) ?  $field['length'] : false;
				if ($default !== false) {
					$default = in_array($type, $ignoreQuotes) ? " DEFAULT $default " : " DEFAULT '$default' ";
				}

				$length 	= $length ? "($length) " : " ";
				$nullable 	= $nullable ? " NULL " : " NOT NULL";
				$comment 	= $comment ? " COMMENT '$comment' " : "";

				$query .= $name . $type . $length . $nullable . $default  . $comment . ", ";
			}
		}

		return trim($query, ', ') . ');';
	}
}