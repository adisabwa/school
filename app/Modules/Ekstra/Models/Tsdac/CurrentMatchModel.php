<?php

namespace Modules\Ekstra\Models\Tsdac;

use App\Models\BaseModel;

class CurrentMatchModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'ts_tsdac_current_match';
        $this->relations = [
          'biru' => [
            'foreign_key' => 'biru',
            'model' => 'Modules\Ekstra\Models\Tsdac\PesertaModel',
            'alias' => 'tbiru',
            'selects' => ['nama nama_biru', 'kelas kelas_biru'],
          ],
          'kuning' => [
            'foreign_key' => 'kuning',
            'model' => 'Modules\Ekstra\Models\Tsdac\PesertaModel',
            'alias' => 'tkuning',
            'selects' => ['nama nama_kuning', 'kelas kelas_kuning'],
          ],
        ];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->id; });
    }
}