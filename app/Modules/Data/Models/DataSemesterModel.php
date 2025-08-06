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
}