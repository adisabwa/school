<?php

namespace App\Models;

use CodeIgniter\Model;

class FinanceModel extends Model
{
    protected $table         = TB_FINANCE;
    protected $primaryKey = 'id';

    protected $protectFields = false;
    protected $useAutoIncrement = true;
    // protected $returnType    = \App\Entities\Prodi::class;
    protected $returnType    = 'array';

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'created_at';

    protected function initialize()
    {

    }

    public function getAll($id_santri, $kelas, $start, $end)
    {
        $where1 = empty($id_santri) ? '1=1' : "id_santri='$id_santri'";
        $where4 = empty($kelas) ? '1=1' : "s.kelas='$kelas'";
        $where2 = empty($start) ? '1=1' : "tanggal >= '$start'";
        $where3 = empty($end) ? '1=1' : "tanggal <= '$end'";

        return $this->db->table(TB_FINANCE.' f')
                    ->select('f.*, s.nama, s.tingkat, s.kelas')
                    ->join(TB_SANTRI.' s','s.id=f.id_santri')
                    ->where($where1)
                    ->where($where2)
                    ->where($where3)
                    ->where($where4)
                    ->orderBy('tanggal desc, s.kelas, s.nama')
                    ->get()
                    ->getResult();
    }


    public function getCount($start, $end)
    {
        $where2 = empty($start) ? '1=1' : "tanggal >= '$start'";
        $where3 = empty($end) ? '1=1' : "tanggal <= '$end'";

        return $this->db->table(TB_SANTRI.' s')
                    ->select("f.id_santri, f.tanggal, s.nama, s.tingkat, s.kelas, f.jenis, SUM(f.nominal) jumlah" )
                    ->join(TB_FINANCE.' f',"s.id=f.id_santri")
                    ->where($where2)
                    ->where($where3)
                    ->groupBy('s.id, f.jenis, f.tanggal')
                    ->orderBy('tingkat, kelas, nama')
                    ->get()
                    ->getResult();
    }


    public function getSaldo($end)
    {
        $where3 = empty($end) ? '1=1' : "tanggal < '$end'";

        return $this->db->table(TB_SANTRI.' s')
                    ->select("f.id_santri, f.tanggal, s.nama, s.tingkat, s.kelas, f.jenis, SUM(f.nominal) jumlah" )
                    ->join(TB_FINANCE.' f',"s.id=f.id_santri")
                    ->where($where3)
                    ->groupBy('s.id, f.jenis')
                    ->orderBy('tingkat, kelas, nama')
                    ->having("jumlah > 0")
                    ->get()
                    ->getResult();
    }
}