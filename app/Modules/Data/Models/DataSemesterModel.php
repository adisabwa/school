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
      return $this->getOptionsData($where, function($d) { return "Semester ".ucfirst($d->semester)." $d->tahun_ajaran"; });
    }

    public function get_semester_now()
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