<?php
namespace App\Core;

/**
 * Validator
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
class Validator 
{

    /**
     * Validates Columns/Field
     * 
     * @param array $columns
     * @param array $fields
     * @return bool
     */
    public static function validateColumns($columns, $fields) 
    {
        foreach ($fields as $key => $field) {
            if (!isset($columns[$key])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Validates values
     * 
     * @param array $data
     * @param array $rules
     * @return bool
     */
    public static function validateValues($data, $rules)
    {
        $errors = false;
        $insert = [];
        foreach ($rules as $key => $rule) {
            $rule   = isset($rule['rule']) ?  $rule['rule'] : false;
            $value  = isset($data[$key]) ? $data[$key] : null;
            if (!$rule || self::validator($rule, $value, $data)) {
                if ($rule == 'datetime') {
                    $value = date("Y-m-d H:i:s", strtotime($value));
                }
                $insert[$key] = $value; 
            } else {
                $errors[$key] = $key . ' field is required or not valid VAL:' . $value;
            }
        }
        return [
            'errors'    => $errors,
            'insert'    => $insert,
        ];
    }


    /**
     * Validates each rule
     * 
     * @param array $rule
     * @param mixed $value
     * @param array $data
     * @return bool
     */
    public static function validator($rule, $value, $data) 
    {
        $ruleArray  = explode('|', $rule);
        $passed     = true;
        foreach($ruleArray as $conditions) {
            $options        = explode(':', $conditions);
            $condition      = $options[0];
            $optionValue    = isset($options[1]) ? $options[1] : false;
            switch (true) {
                case ($condition == 'required'):
                    if (!$value) {
                        $passed = false;
                    }
                    break;
                case (strpos($condition, 'length:') !== false) :
                    if ($optionValue !== false && strlen($value) != $optionValue) {
                        $passed = false;
                    }
                case ($condition == 'datetime'):
                    if (!self::verifyDate($value)) {
                        $passed = false;
                    }
                case (strpos($condition, 'required_if:') !== false) :
                    $expression = $condition;
                    if (!self::validateExpression($data, $expression, $value)) {
                        $passed = false;
                    }
                default:
                    break;
            }
        }

        return $passed;
    }

    /**
     * Verify a valid date time
     * 
     * @param string
     * @return bool
     */
    public static function verifyDate($date)
    {
        if ($date) {
            $date = date("Y-m-d H:i:s", strtotime($date));
        }
        $dateTime   = \DateTime::createFromFormat('Y-m-d H:i:s', $date);
        $errors     = \DateTime::getLastErrors();
        if (!empty($errors['warning_count'])) {
            return false;
        }
        return $dateTime !== false;
    }

    /**
     * Validates expression/operator
     * 
     * @param array $data
     * @param array $expression
     * @param mixed $value
     * @return bool
     */
    public static function validateExpression($data, $expression, $value)
    {
        $options = explode(':', $expression);
        if (count($options) > 3) {
            $col        = $options[1];
            $operator   = $options[2];
            $colValue   = $options[3];
            if (!isset($data[$col])) {
                return false;
            }
            $dataValue  = $data[$col];
            switch ($operator) {
                case 'isEqual':
                    if ($colValue == $dataValue) {
                        return false;
                    }
                    break;
                case 'isNot': 
                    if ($colValue != $dataValue) {
                        return false;
                    }
                    break; 
                default:
                    # code...
                    break;
            }
        }
        return true;
    }

    /**
     * Get table rules from schema
     * 
     * @param array $fields
     * @return array
     */
    public static function getRulesFromSchema($fields) 
    {
        $rules = [];
        foreach ($fields as $key => $field) {
            $required = $field['rule'];
            $rules[$key] = [
                'rule' => $required,
            ];
        }
        return $rules;
    }
}