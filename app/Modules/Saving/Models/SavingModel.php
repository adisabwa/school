<?php

namespace Modules\Saving\Models;

use CodeIgniter\Model;

class SavingModel extends Model
{
    protected $table         = 'sch_sav_tabungan';
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

    public function getAll(array $whereAnd = [], array $whereOr = [], array $whereIn = [], array $orWhereIn = [], string $order = '', int $limit = 0, int $offset = 0)
    {
        $whereAnd = empty($whereAnd) ? '1=1' : $whereAnd;
        $whereOr = empty($whereOr) ? '1=1' : $whereOr;

        $data = $this->db->table('sch_sav_tabungan'.' f')
                    ->select('f.*, s.nama nama_santri, s.id_kelas, k.nama nama_kas, c.kelas')
                    ->join('sch__santri'.' s','s.id=f.id_santri','left')
                    ->join('sch_sav_kas k','k.id=f.id_kas','left')
                    ->join('sch__kelas c','c.id=s.id_kelas','left')
                    ->having($whereAnd)
                    ->havingGroupStart()
                        ->orHaving($whereOr)
                    ->havingGroupEnd();
                    
        foreach ($whereIn as $key => $value) {
            $data->havingIn($key, $value);
        }
        
        foreach ($orWhereIn as $key => $value) {
            $data->orHavingIn($key, $value);
        }

        $data = $data->orderBy($order)
                    ->limit($limit, $offset)
                    ->get()
                    ->getResult();
        // var_dump($this->db->getLastQuery());
        return $data;
    }

    public function getCount($start, $end)
    {
        $where2 = empty($start) ? '1=1' : "tanggal >= '$start'";
        $where3 = empty($end) ? '1=1' : "tanggal <= '$end'";

        return $this->db->table('sch_sav_tabungan'.' f')
                    ->select("f.id_santri, f.id_kas, f.tanggal, f.sumber, s.nama nama_santri, s.id_kelas, k.nama nama_kas, c.kelas, f.jenis, SUM(f.nominal) jumlah" )
                    ->join('sch__santri'.' s','s.id=f.id_santri','left')
                    ->join('sch_sav_kas k','k.id=f.id_kas','left')
                    ->join('sch__kelas c','c.id=s.id_kelas','left')
                    ->where($where2)
                    ->where($where3)
                    ->groupBy('s.id, f.jenis, f.tanggal')
                    ->orderBy('tingkat, kelas, s.nama, k.nama')
                    ->get()
                    ->getResult();
    }


    public function getSaldo($end)
    {
        $where3 = empty($end) ? '1=1' : "tanggal < '$end'";

        return $this->db->table('sch_sav_tabungan'.' f')
                    ->select("f.id_santri, f.id_kas, f.tanggal, f.sumber, s.nama nama_santri, s.id_kelas, k.nama nama_kas, c.kelas, f.jenis, SUM(f.nominal) jumlah" )
                    ->join('sch__santri'.' s','s.id=f.id_santri','left')
                    ->join('sch_sav_kas k','k.id=f.id_kas','left')
                    ->join('sch__kelas c','c.id=s.id_kelas','left')
                    ->where($where3)
                    ->groupBy('s.id, f.jenis')
                    ->orderBy('tingkat, kelas, s.nama, k.nama')
                    ->having("jumlah > 0")
                    ->get()
                    ->getResult();
    }
}