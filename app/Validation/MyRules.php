<?php
namespace App\Validation;

class MyRules
{
    public function __construct()
    {
        helper('function');
    }

    public function double_greater_and_equal_to(string $str, string $field, array $data): bool
    {
        $compareField = $data[$field] ?? null;
        // var_dump($str, $compareField);
        
        $str = explode('-', $str);
        $compareField = explode('-', $compareField);
        
        if (count($str) != 2 || count($compareField) != 2) {
            return false;
        }

        if (!is_numeric($str[0]) || !is_numeric($str[1]) || !is_numeric($compareField[0]) || !is_numeric($compareField[1])) {
            return false;
        }
        
        if ($str[0] > $compareField[0])
            return true;
        else if ($str[0] == $compareField[0])
            return $str[1] >= $compareField[1];
        else
            return false;
    }

    public function unique_combination(string $str, string $params, array $data): bool
    {
        // var_dump($str, $data, $params);
        // Format: table.column1-column2,ignore-field,value-field
        [$tableColumns, $ignoreField, $ignoreValue, $fieldPath] = explode(',', $params);
        [$table, $columns] = explode('.', $tableColumns);
        [$col1, $col2] = explode('-', $columns);
        [$table, $ind, $first_column] =  explode('.', $fieldPath);
        // var_dump($data, $tableColumns, $ignoreField, $ignoreValue, $fieldPath);
        // return TRUE;

        $db = \Config\Database::connect();
        $builder = $db->table($table);

        $ignore = empty($ignoreField) ? '1=1' :
            ($ignoreValue > 0 ? "$ignoreField!='$ignoreValue'" : '1=1');
        // Check for existing record with both values
        $exists = $builder->where($col1, $data[$table][$ind][$col1])
                          ->where($col2, $data[$table][$ind][$col2])
                          ->where($ignore)
                          ->get()
                          ->getRow();
        // var_dump($exists, $data[$table][$ind], $ignore);
        return $exists === null;
    }

    
    public function unique_input(string $str, string $params, array $data): bool
    {
        // var_dump($str, $data, $params);
        // Format: table.column1-column2,ignore-field,value-field
        $comparedKeys = explode(',', $params);
        $fieldPath = array_pop($comparedKeys);
        [$table, $ind, $first_column] =  explode('.', $fieldPath);

        $base = $data[$table][$ind];
        // var_dump($table, $ind, $base);
        $filter = array_filter($data[$table], function($arr) use ($comparedKeys, $base, $ind) {
            $same = TRUE;
            foreach ($comparedKeys as $key) {
                if ($base[$key] !== $arr[$key])
                    $same = FALSE;
            }
            return $same;
        });
        // var_dump($filter);
        return count($filter) <= 1;
    }
}