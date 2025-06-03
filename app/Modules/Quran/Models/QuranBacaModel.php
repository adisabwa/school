<?php

namespace Modules\Quran\Models;

use CodeIgniter\Model;

class QuranBacaModel extends Model
{
    protected $table         = 'mu_quran_baca';
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

        $builder = $this->db->table('mu_quran_baca qb')
                    ->select("qb.*, qb.total_ayat data_chart, s.nama, 
                        sq.nama_latin nama_surat_mulai, sq2.nama_latin nama_surat_selesai")
                    ->join('mu_anggota s','qb.id_anggota=s.id')
                    ->join('mu__surat_quran sq','qb.surat_mulai=sq.id')
                    ->join('mu__surat_quran sq2','qb.surat_selesai=sq2.id')
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

        return $data;
    }

    public function get_last($id_anggota)
    {
        $data = $this->db->table('mu_quran_baca qb')
                    ->select("qb.*, qb.total_ayat data_chart, s.nama, 
                        sq.nama_latin nama_surat_mulai, sq2.nama_latin nama_surat_selesai")
                    ->join('mu_anggota s','qb.id_anggota=s.id')
                    ->join('mu__surat_quran sq','qb.surat_mulai=sq.id')
                    ->join('mu__surat_quran sq2','qb.surat_selesai=sq2.id')
                    ->orderBy('qb.tanggal desc,surat_selesai desc,surat_mulai desc')
                    ->where('id_anggota',$id_anggota)
                    ->get()
                    ->getRowObject();
        // var_dump($data);
        return $data;
    }

    
    public function getSummary($where = [])
    {
        return $this->db->table('daiq_list_iqab li')
                        ->select("q.tingkat_iqab, tanggal, count(li.id) jumlah")
                        ->join("daiq_iqab q","q.id=li.id_iqab")
                        ->where($where)
                        ->groupBy('LEFT(tanggal, 7), tingkat_iqab')
                        ->orderBy('tanggal, tingkat_iqab')
                        ->get()
                        ->getResultObject();
    }
}