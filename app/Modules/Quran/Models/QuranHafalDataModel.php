<?php

namespace Modules\Quran\Models;

use CodeIgniter\Model;

class QuranHafalDataModel extends Model
{
    protected $table         = 'mu_quran_hafal_data';
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
    
    public function get_juz($id_anggota)
    {
      $data= $this->db->table('mu_quran_hafal_data qb0')
                    ->select("qb.*, s.nama,
                    qb3.id juz_mulai, qb2.id juz_selesai,
                    sq.nama_latin nama_surat_mulai, sq2.nama_latin nama_surat_selesai")
                    ->join('(SELECT *, (surat_mulai * 1000 + ayat_mulai) number_mulai, (surat_selesai * 1000 + ayat_selesai) number_selesai FROM mu_quran_hafal_data) qb',"qb.id=qb0.id")
                    ->join('(SELECT *, (surat_mulai * 1000 + ayat_mulai) number_mulai, (surat_selesai * 1000 + ayat_selesai) number_selesai FROM mu__juz_quran) qb2',"qb.number_selesai <= qb2.number_selesai AND qb.number_selesai >= qb2.number_mulai")
                    ->join('(SELECT *, (surat_mulai * 1000 + ayat_mulai) number_mulai, (surat_selesai * 1000 + ayat_selesai) number_selesai FROM mu__juz_quran) qb3',"qb.number_mulai <= qb3.number_selesai AND qb.number_mulai >= qb3.number_mulai")
                    ->join('mu_anggota s','qb.id_anggota=s.id')
                    ->join('mu__surat_quran sq','qb.surat_mulai=sq.id')
                    ->join('mu__surat_quran sq2','qb.surat_selesai=sq2.id')
                    ->where('qb.id_anggota', $id_anggota)
                    ->get()
                    ->getResultObject();
        // var_dump($data);
        return $data;
    }
    
    public function get_last_data($id_anggota, $surat_mulai, $ayat_mulai)
    {
      $prev_surat = $surat_mulai-1;
      $prev_ayat = $ayat_mulai-1;

      $data = $this->db->table('mu_quran_hafal_data qb')
              ->select("qb.*")
              // ->join('mu__surat_quran sq','qb.surat_mulai=sq.id')
              ->join('mu__surat_quran sq2','qb.surat_selesai=sq2.id')
              ->where("qb.id_anggota", $id_anggota)
              ->groupStart()
                ->where("(surat_selesai = $surat_mulai AND ayat_selesai = $prev_ayat)")
                ->orWhere("(surat_selesai = $prev_surat AND $ayat_mulai = 1 AND ayat_selesai=sq2.jumlah_ayat)")
              ->groupEnd()
              ->get()
              ->getRowObject();

      return $data;
    }

    public function get_merge($id_anggota)
    {

      $data = 
        // $this->db->table('( SELECT d.*, (surat_mulai * 1000 + ayat_mulai) number_mulai, (surat_selesai * 1000 + ayat_selesai) number_selesai FROM mu_quran_hafal_data d ) qb')
        $this->db->table('mu_quran_hafal_data qb0')
              ->select("qb.*, qb2.id id_2, 
                qb2.surat_mulai surat_mulai_2, qb2.ayat_mulai ayat_mulai_2,
                qb.number_mulai, qb.number_selesai, 
                qb2.number_mulai number_mulai_2, qb2.number_selesai number_selesai_2")
              ->join('(SELECT *, (surat_mulai * 1000 + ayat_mulai) number_mulai, (surat_selesai * 1000 + ayat_selesai) number_selesai FROM mu_quran_hafal_data) qb',"qb.id=qb0.id")
              ->join('(SELECT *, (surat_mulai * 1000 + ayat_mulai) number_mulai, (surat_selesai * 1000 + ayat_selesai) number_selesai FROM mu_quran_hafal_data) qb2',"qb2.number_selesai <= qb.number_selesai AND qb2.number_selesai >= qb.number_mulai")
              ->join('mu__surat_quran sq','qb.surat_selesai=sq.id')
              // ->join('mu__surat_quran sq2','qb2.surat_selesai=sq2.id')
              ->where("qb.id_anggota", $id_anggota)
              ->where("qb2.number_mulai <= qb.number_mulai")
              ->where("qb.id != qb2.id")
              ->groupBy("qb0.id") 
              ->orderBy("qb0.id")
              ->get()
              ->getResultObject();
              
      return $data;
    }
}