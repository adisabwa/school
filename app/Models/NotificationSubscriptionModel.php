<?php

namespace App\Models;

use App\Models\BaseModel;

class NotificationSubscriptionModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'_notif_subscription';
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->id; });
    }
}