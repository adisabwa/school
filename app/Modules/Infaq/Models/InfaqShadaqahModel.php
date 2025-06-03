<?php

namespace Modules\Infaq\Models;

use CodeIgniter\Model;

class InfaqShadaqahModel extends Model
{
    protected $table         = 'mu_infaq_shadaqah';
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

    public function getAll($whereAnd = [], $whereOr = [], $order = '', $limit = 0, $offset = 0, $groupBy = ['id'], $whereIn = [])
    {
        $whereAnd = empty($whereAnd) ? '1=1' : $whereAnd;
        $whereOr = empty($whereOr) ? '1=1' : $whereOr;

        $builder = $this->db->table('mu_infaq_shadaqah f')
                    ->select('f.*, f.jumlah data_chart, s.nama, s.nbm, 1 as count')
                    ->join('mu_anggota'.' s','s.id=f.id_anggota')
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

    public function get_last($id_anggota)
    {
        return $this->db->table('mu_infaq_shadaqah f')
                    ->select('f.*, f.jumlah data_chart, s.nama, s.nbm')
                    ->join('mu_anggota'.' s','s.id=f.id_anggota')
                    ->where('f.id_anggota', $id_anggota)
                    ->orderBy('tanggal desc')
                    ->get()
                    ->getRow();
    }

    public function get_best($id_anggota)
    {
        return $this->db->table('mu_infaq_shadaqah f')
                    ->select('f.*, f.jumlah data_chart, s.nama, s.nbm')
                    ->join('mu_anggota'.' s','s.id=f.id_anggota')
                    ->where('f.id_anggota', $id_anggota)
                    ->orderBy('total_score desc')
                    ->get()
                    ->getRow();
    }

    public function update_total_score($id)
    {
        return $this->db->query("UPDATE $this->table
                        SET total_score = (
                            IF(shubuh IS NULL, 0, shubuh) +
                            IF(dhuhur IS NULL, 0, dhuhur) +
                            IF(asar IS NULL, 0, asar) +
                            IF(maghrib IS NULL, 0, maghrib) +
                            IF(isya IS NULL, 0, isya)
                        )
                        WHERE id = $id");
    }
}