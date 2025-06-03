<?php

namespace Modules\Psb\Models;

use CodeIgniter\Model;

class PsbModel extends Model
{
    protected $table      = 'mu_psb';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $protectFields = false;
    protected $returnType    = 'object';

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected function initialize()
    {

    }

    public function getData($whereAnd = [], $whereOr = [])
    {
        $whereAnd = empty($whereAnd) ? '1=1' : $whereAnd;
        $whereOr = empty($whereOr) ? '1=1' : $whereOr;
        $data = $this->db->table('mu_psb p')
                    ->select("p.*,
                    pe1.label ayah_peng_label, pe1.dari ayah_peng_dari, pe1.hingga ayah_peng_hingga,
                    pe2.label ibu_peng_label, pe2.dari ibu_peng_dari, pe2.hingga ibu_peng_hingga,
                    pe3.label wali_peng_label, pe3.dari wali_peng_dari, pe3.hingga wali_peng_hingga")
                    ->join('mu_penghasilan pe1','pe1.id=p.ayah_penghasilan','left')
                    ->join('mu_penghasilan pe2','pe2.id=p.ibu_penghasilan','left')
                    ->join('mu_penghasilan pe3','pe3.id=p.wali_penghasilan','left')
                    ->where($whereAnd)
                    ->groupStart()
                        ->orWhere($whereOr)
                    ->groupEnd()
                    ->get()
                    ->getResultObject();

        if (empty($data)) return $data;
        else $data = $data[0];

        $data->ayah_penghasilan_label = empty($data->ayah_penghasilan) ? "-" : "$data->ayah_peng_label ( Rp. ".number_format($data->ayah_peng_dari, 2, ',', '.')." - Rp. ".number_format($data->ayah_peng_hingga, 2, ',', '.')." )";
        $data->ibu_penghasilan_label = empty($data->ibu_penghasilan) ? "-" : "$data->ibu_peng_label ( Rp. ".number_format($data->ibu_peng_dari, 2, ',', '.')." - Rp. ".number_format($data->ibu_peng_hingga, 2, ',', '.')." )";
        $data->wali_penghasilan_label = empty($data->wali_penghasilan) ? "-" : "$data->wali_peng_label ( Rp. ".number_format($data->wali_peng_dari, 2, ',', '.')." - Rp. ".number_format($data->wali_peng_hingga, 2, ',', '.')." )";
        return $data;
    }

    
    public function getAll($whereAnd = [], $whereOr = [], $order)
    {
        $whereAnd = empty($whereAnd) ? '1=1' : $whereAnd;
        $whereOr = empty($whereOr) ? '1=1' : $whereOr;

        $data = $this->db->table('mu_psb p')
                    ->select("p.*,
                    pe1.label ayah_peng_label, pe1.dari ayah_peng_dari, pe1.hingga ayah_peng_hingga,
                    pe2.label ibu_peng_label, pe2.dari ibu_peng_dari, pe2.hingga ibu_peng_hingga,
                    pe3.label wali_peng_label, pe3.dari wali_peng_dari, pe3.hingga wali_peng_hingga")
                    ->join('mu_penghasilan pe1','pe1.id=p.ayah_penghasilan','left')
                    ->join('mu_penghasilan pe2','pe2.id=p.ibu_penghasilan','left')
                    ->join('mu_penghasilan pe3','pe3.id=p.wali_penghasilan','left')
                    ->where($whereAnd)
                    ->groupStart()
                        ->orWhere($whereOr)
                    ->groupEnd()
                    ->orderBy($order)
                    ->get()
                    ->getResultObject();

        return $data;
    }

    public function getSummary()
    {
        return $this->db->table('mu_psb')
                        ->select("status, count(id) jumlah")
                        ->groupBy('status')
                        ->get()
                        ->getResultObject();
    }
}