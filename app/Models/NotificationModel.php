<?php

namespace App\Models;

use App\Models\BaseModel;

class NotificationModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'_notif';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->judul; });
    }
}