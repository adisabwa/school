<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataSemesterModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch__semester';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData(where: $where, 
        order: 'tahun_ajaran DESC, tanggal_mulai DESC',
        concatFunc : function($d) { return "Semester ".ucfirst($d->semester)." $d->tahun_ajaran"; },
        addOptions : function($option, $data) { 
          $option->tanggal_mulai = $data->tanggal_mulai;
          $option->tanggal_selesai = $data->tanggal_selesai;
          return $option; 
        });
    }

    public function getSemesterNow()
    {
      $tanggal = date('Y-m-d');
      $data = $this->getAll(whereAnd: [
        'tanggal_mulai <= ' => $tanggal,
        'tanggal_selesai >= ' => $tanggal,
      ]);
      // var_dump($data, $this->getLastQuery());
      return $data[0] ?? [];
    }
}