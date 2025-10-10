<?php

// app/Libraries/Fields.php

namespace App\Libraries;

use App\Models\KolomModel;

class Fields
{
    public $model;

    public function __construct()
    {
        $this->model = new KolomModel;
    }
    
    public function getFields($table, $input = TRUE, $output = FALSE)
    {

      $datas = $this->model->getAll($table, $input, $output);

      $results = [];
      // echo json_encode($datas);exit;
      foreach ($datas as $key => $data) {
        $options = [];
        if (!empty($data->pilihan)) {
          try {
            $options = unserialize($data->pilihan);
          } catch (\Throwable $th) {
            $options = FALSE;
          }
          if (!$options) {
              $pilihan = explode(',',$data->pilihan);
              // var_dump($pilihan);
              if (count($pilihan) > 1) {
                  foreach ($pilihan as $opt) {
                      $options[] = (object)[
                          'value'   => $opt,
                          'label'   => ucwords($opt),
                      ];
                  }
              } else {
                  $methods = explode('::',$data->pilihan);
                  $model = $methods[0];
                  $method = $methods[1] ?? 'getOptions';
                  // var_dump($model);
                  $model = model($model);
                  $options = $model?->$method() ?? [];
              }
          }
          $data->options = $options;
          $data->allow_add = $data->allow_add == '1';
          $data->allow_create = $data->allow_create == '1';
          $data->customInput = false;
          if ($data->allow_add){
              // echo json_encode($data);exit;
              $data->isAdding = false;
              $data->optionName = '';
              $data->optionSave = '';
              $data->addHref = $data->add_href;
              $data->addReset = $data->add_reset;
              $data->addFields = empty($model) ? [] : $this->getFields($model->getTableName(), TRUE, FALSE);
              // echo json_encode($data);exit;
          }
          if (str_contains($data->input,'select-double')) {
            $data->parentSelect = '';
            $labels = explode(' dan ', $data->label);
            $data->label1 = $labels[0] ?? $data->label;
            $data->label2 = $labels[1] ?? $data->label;
            $prepends = explode(' dan ', $data->prepend);
            $data->prepend1 = $prepends[0] ?? $data->prepend;
            $data->prepend2 = $prepends[1] ?? $data->prepend;
          }
        }
        
        $data->change = true;
        if ($data->sortable == '1') {
          $data->sort = '';
        }

        if ($data->tipe == 'table') {
          $data->fields = $this->preparation($data->nama_kolom, TRUE);
        }

        $results[$data->nama_kolom] = $data;
      }


      return $results;
    }

    public function groupingData($results)
    {
      // var_dump($results);
      $new_results = [];
      foreach ($results as $nama_kolom => $result) {
        if (empty($new_results[$result->group])) {
          $data = (object)[
            'label' => "Data ".ucwords($result->group),
            'group' => $result->group,
            'group_icon' => $result->group_icon,
            'children' => [],
          ];
          $new_results[$result->group] = $data;
        } 
        $new_results[$result->group]->children[$nama_kolom] = $result;
      }

      return $new_results;
    }

    public function getModelFromTable($tableName)
    {
        // Convert table name to model name
        // Example: 'users' → 'UserModel'
        $tableName = preg_replace('/^[^_]+_/', '', $tableName);
        $tableName = str_replace('_', ' ', $tableName);
        $tableName = ucwords($tableName);
        $tableName = str_replace(' ', '', $tableName);
        $modelName = ucfirst($tableName) . 'Model';

        // Check if the model class exists
        return model($modelName);
    }

    public function getKolomDetail($table, $nama_kolom){
      $data = $this->model->getKolom($table, $nama_kolom);
      // var_dump($table, $nama_kolom, $data);
      return $data;
    }
}
