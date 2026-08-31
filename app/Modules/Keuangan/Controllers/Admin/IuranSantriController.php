<?php

namespace Modules\Keuangan\Controllers\Admin;

use App\Controllers\BaseDataController;
use App\Libraries\PdfBuilder;
use PhpOffice\PhpSpreadsheet\IOFactory as IOFactorySpreadsheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory as IOFactoryWord;

class IuranSantriController extends BaseDataController
{
    public $modelIuaran;
    public $modelSantri;

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('Modules\Keuangan\Models\IuranSantriModel');
        $this->modelIuran = model('Modules\Keuangan\Models\Data\IuranModel');
        $this->modelTransaksi = model('Modules\Keuangan\Models\TransaksiModel');
        $this->modelSantri = model('DataSantriKelasModel');

    }

    public function generate()
    {
        $id_semester = $this->request->getGetPost('id_semester');
        $semester = model('DataSemesterModel')->getData($id_semester);
        $dateNow = date('Y-m-d');
        $bulans = [];
        $tmp = $semester->tanggal_mulai;
        for ($i=0; $i < 6; $i++) { 
            $bulans[] = $tmp;
            $tmp = date('Y-m-01', strtotime($tmp." +1 month"));
        }
        $bulans = array_filter($bulans, function($a) use ($dateNow) { return $a <= $dateNow;} );
        $iuran = $this->modelIuran->getAll(whereAnd:[
            '{f}id_semester' => $semester->id,
        ],whereOr:[
            // "{f}tipe" => 'rutin',
            // "({f}tipe='non-rutin' AND {f}tanggal_mulai >= '$dateNow')" => '',
        ]);
        // var_dump($this->modelIuran->getLastQuery(), $iuran);
        $santris = $this->modelSantri->getAll(whereAnd:['tahun_ajaran' => $semester->tahun_ajaran]);
        // var_dump($santris);
        $tagihan = [];
        foreach ($iuran as $key => $value) {
            if ($value->tipe == 'rutin') $value->periode = $bulans;
            else $value->periode = [NULL];
            switch ($value->tipe) {
                case 'unit':
                    $value->santris = array_filter($santris, function($e) use ($value) { return $e->id_unit == $value->id_unit; });
                    break;
                case 'angkatan':
                    $value->santris = array_filter($santris, function($e) use ($value) { return $e->tingkat == $value->angkatan; });
                    break;
                case 'kelas':
                    $value->santris = array_filter($santris, function($e) use ($value) { return $e->id_kelas == $value->id_kelas; });
                    break;
                case 'pribadi':
                    $value->santris = array_filter($santris, function($e) use ($value) { return $e->id_santri == $value->id_santri; });
                    break;
                default:
                    $value->santris = $santris;
                    break;
           }
        }
        $savedTagihan = $this->model->getAll([
            'id_semester' => $id_semester,
            // 'id_santri'   => '1143',
        ]);
        $dataSavedTagihan = [];
        foreach ($savedTagihan as $key => $value) {
            $dataSavedTagihan["$value->id_semester-$value->id_santri-$value->id_iuran-$value->tipe-$value->periode"] = $value;
        }
        // var_dump($dataSavedTagihan);
        // return $this->failServerError();

        $tagihans = [];
        foreach ($iuran as $key => $t) {
            foreach ($t->periode as $key => $value) {
                foreach ($t->santris as $key => $s) {
                    $ind = "$id_semester-$s->id_santri-$t->id-$t->tipe-$value";
                    // var_dump($ind, isset($dataSavedTagihan[$ind]));
                    if (isset($dataSavedTagihan[$ind]))
                        continue;
                    $tagihans[] = (object)[
                        'id_semester' => $id_semester,
                        'id_santri' => $s->id_santri,
                        'id_iuran' => $t->id,
                        'periode' => $value,
                        'nominal' => $t->nominal,
                    ];
                }
            }
        }
        
        // Start the transaction
        // var_dump($tagihans);
        // $db = \Config\Database::connect();

        // try {
        //     // Aktifkan exception ketat agar tidak gagal secara diam-diam
        //     $db->transException(true)->transStart();
            
        //     $this->model->insertBatch($tagihans);
            
        //     $db->transComplete();
        // } catch (\Exception $e) {
        //     // Ini akan menampilkan pesan error internal PHP / PDO Driver sesungguhnya
        //     echo "<h3>Penyebab Gagal Eksekusi:</h3>";
        //     echo "<p style='color:red'>" . $e->getMessage() . "</p>";
        //     die();
        // }

        $this->model->transBegin();
        if (!empty($tagihans))
            $this->model->insertBatch($tagihans);

        if ($this->model->transStatus() === false) {
            // var_dump($this->model->getLastQuery(), $this->model->error());
            $this->model->transRollback();
            return $this->failServerError();
        } else {
            $this->model->transCommit();
            return $this->respondCreated($tagihans);
        }
    }

    public function get_all_grouping()
    {
        $order = $this->request->getGetPost('order') ?? [];
        $where = $this->request->getGetPost('where') ?? [];
        $having = $this->request->getGetPost('having') ?? [];
        $order = implode(',' , $order);
        $this->model->selects = [
            "SUM(IF({f}.status='1',{f}.nominal,0)) jumlah_lunas",
            "SUM(IF({f}.status='0',{f}.nominal,0)) jumlah_tunggakan",
            "( SELECT ( COALESCE(SUM(CASE WHEN jenis_mutasi = 'in' THEN nominal ELSE 0 END), 0) - 
                COALESCE(SUM(CASE WHEN jenis_mutasi = 'out' THEN nominal ELSE 0 END), 0) ) AS saldo
                FROM sch_keu_saldo_iuran 
                WHERE sch_keu_saldo_iuran.id_santri = {f}.id_santri) saldo",
        ];
        $data = $this->model->getAll(
            whereAnd: $where,
            order: $order, 
            groupBy:['id_semester','id_santri'],
            havingAnd: $having, 
        );

        return $this->respondCreated($data);
    }

}