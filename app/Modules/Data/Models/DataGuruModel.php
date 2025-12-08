<?php

namespace Modules\Data\Models;

use App\Models\BaseModel;

class DataGuruModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = 'sch__guru';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return trim(str_replace("-","","$d->prefix $d->nama $d->suffix")); });
    }
}