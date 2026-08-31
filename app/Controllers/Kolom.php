<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Fields;
use stdClass;

class Kolom extends BaseController
{
    protected $kolomModel;
    protected $fieldsLibrary;

    public function __construct()
    {      
      $this->kolomModel = model('KolomModel');
      $this->fieldsLibrary = new Fields;
    }

    public function preparation($table = NULL, $return_data = FALSE, $grouping = NULL)
    {
        $input = empty($this->request) ? '1' : $this->request->getGet('input');
        $output = empty($this->request) ? '0' : $this->request->getGet('output');
        $input = ($input ?? '1') == '1';
        $output = ($output ?? '0') == '1';
        $table = $table ?? $this->request->getGet('table');
        $grouping = ($grouping ?? $this->request->getGet('grouping')) != '0';
        
        $results = $this->fieldsLibrary->getFields($table, $input, $output);
      
        if ($grouping)
          $results = $this->fieldsLibrary->groupingData($results);

        if ($return_data) return $results;

        return $this->respondCreated($results);
    }

}