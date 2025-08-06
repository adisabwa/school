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


    //Check if value exist in a table, check with combination paramaters 
    public function unique_combination(string $str, string $params, array $data): bool
    {
        // var_dump($str, $data, $params);
        // Format: table.column1-column2-....,ignore-field,value-field,Array fieldKey for nested Value
        [$tableColumns, $ignoreField, $ignoreValue, $fieldPath] = explode(',', $params);
        [$table, $columns] = explode('.', $tableColumns);
        $cols = explode('-', $columns);
        $paths =  explode('.', $fieldPath);
        // var_dump($data, $tableColumns, $ignoreField, $ignoreValue, $fieldPath);
        // return TRUE;
        array_pop($paths);
        if ($paths)
            foreach ($paths as $key => $p) {
                $data = $data[$p];
            }

        $db = \Config\Database::connect();
        $builder = $db->table($table);

        $ignore = empty($ignoreField) ? '1=1' :
            ($ignoreValue > 0 ? "$ignoreField!='$ignoreValue'" : '1=1');
        // Check for existing record with both values
        foreach ($cols as $key => $col) {
            $builder->where($col, $data[$col]);
        }
        $exists = $builder->where($ignore)
                          ->get()
                          ->getRow();
        // var_dump($exists);
        return $exists === null;
    }

    
    //Check if value exist in a table, check with combination paramaters 
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