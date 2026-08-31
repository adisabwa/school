<?php

namespace Modules\Ekstra\Models\Tsdac;

use App\Models\BaseModel;

class NilaiModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'ts_tsdac_penilaian';
        $this->relations = [
          'id_peserta' => [
            'foreign_key' => 'id_peserta',
            'model' => 'Modules\Ekstra\Models\Tsdac\PesertaModel',
            'alias' => 'peserta',
            'selects' => ['nama', 'kelas'],
          ],
          'vs_peserta' => [
            'foreign_key' => 'vs_peserta',
            'model' => 'Modules\Ekstra\Models\Tsdac\PesertaModel',
            'alias' => 'vs_peserta',
            'selects' => ['nama nama_lawan', 'kelas kelas_lawan'],
          ],
        ];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->id; });
    }
}