<?php

namespace Modules\Pengasuhan\Models;

use App\Models\BaseModel;

class NilaiPengasuhanModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();

        $this->table = PREFIX_TABLE.'peng_nilai';
        $this->relations = [
          'id_santri' => [
            'foreign_key' => 'id_santri',
            'model' => 'DataSantriModel',
            // 'pass_key' => ['id_kamar'],
            'selects' => ['nama', 'nisn','id_kelas','status','kamar','nama_wamar','nama_wamar_arab'],
          ],
        ];
    }

    public function getOptions($where = [])
    {
      return $this->getOptionsData($where, function($d) { return $d->id; });
    }

    public function checkKamar()
    {
      $query = "UPDATE ".PREFIX_TABLE."peng_nilai p
        JOIN ".PREFIX_TABLE."_santri_kamar sk ON p.id_santri=sk.id_santri AND p.id_semester=2
        SET p.id_kamar=sk.id_kamar
        WHERE sk.id_kamar != p.id_kamar;";

      return $this->db->query($query);
    }
}