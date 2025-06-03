<?php

namespace Modules\Sholat\Models;

use CodeIgniter\Model;

class SholatSunnahModel extends Model
{
    protected $table         = 'mu_sholat_sunnah';
    protected $primaryKey = 'id';

    protected $protectFields = false;
    protected $useAutoIncrement = true;
    protected $returnType    = 'object';

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected function initialize()
    {

    }

    public function getAll($whereAnd = [], $whereOr = [], $order = '', $limit = 0, $offset = 0, $groupBy = 'id', $whereIn = [])
    {
        $whereAnd = empty($whereAnd) ? '1=1' : $whereAnd;
        $whereOr = empty($whereOr) ? '1=1' : $whereOr;

        $builder = $this->db->table('mu_sholat_sunnah f')
                    ->select("f.*, s.nama, s.nbm,
                        su.nama_sholat,
                        SUM(f.rakaat) total_rakaat, 
                        SUM(f.rakaat) data_chart,
                        GROUP_CONCAT(CONCAT(su.nama_sholat,'-',f.rakaat) SEPARATOR '/') daftar_sholat")
                    ->join('mu_anggota'.' s','s.id=f.id_anggota')
                    ->join('mu__sholat_sunnah su','su.id=f.id_sholat')
                    ->where($whereAnd)
                    ->where($whereAnd);

        foreach($whereIn as $key => $in) {
            $builder->whereIn($key, $in);
        }

        return $builder->groupStart()
                            ->orWhere($whereOr)
                        ->groupEnd()
                        ->orderBy($order)
                        ->groupBy($groupBy)
                        ->limit($limit, $offset)
                        ->get()
                        ->getResult();
    }

    
    public function getWhere($whereAnd = [], $whereOr = [], $order = '', $groupBy = 's.id, f.tanggal')
    {
        $whereAnd = empty($whereAnd) ? '1=1' : $whereAnd;
        $whereOr = empty($whereOr) ? '1=1' : $whereOr;

        return $this->db->table('mu_sholat_sunnah f')
                    ->select("f.*, s.nama, s.nbm,
                        su.nama_sholat,
                        SUM(f.rakaat) total_rakaat, 
                        SUM(f.rakaat) data_chart,
                        GROUP_CONCAT(CONCAT(su.nama_sholat,'-',f.rakaat) SEPARATOR '/') daftar_sholat")
                    ->join('mu_anggota'.' s','s.id=f.id_anggota')
                    ->join('mu__sholat_sunnah su','su.id=f.id_sholat')
                    ->where($whereAnd)
                    ->groupStart()
                        ->orWhere($whereOr)
                    ->groupEnd()
                    ->orderBy($order)
                    ->groupBy($groupBy)
                    ->get()
                    ->getRowObject();
    }
    
}