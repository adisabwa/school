<?php

namespace Modules\Pengasuhan\Controllers;

use App\Controllers\BaseDataController;
use App\Libraries\PdfBuilder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpWord\TemplateProcessor;

class NilaiPengasuhanController extends BaseDataController
{
    public $santriModel;
    public $santriKamarModel;
    public $mapelPembagianModel;

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('NilaiPengasuhanModel');
        $this->santriModel = model('DataSantriModel');
        $this->santriKamarModel = model('DataSantriKamarModel');
        $this->mapelPembagianModel = model('MapelPembagianModel');
    }

    public function index($return_data = FALSE)
    {
        $id_semester = $this->request->getGetPost('id_semester');
        if (empty($id_semester)) $id_semester = -1;
        $id_kamar = $this->request->getGetPost('id_kamar');
        if (empty($id_kamar)) $id_kamar = -1;
        $order = $this->request->getGetPost('order') ?? ['nama asc'];
        $order = implode(',', $order);
        $kategori = unserialize(NILAI_PENGASUHAN_KATEGORI);
        $count_kategori = count($kategori);

        $santris = $this->santriKamarModel->getAll(
            whereAnd: ['id_kamar' => $id_kamar], 
            // orWhereIn: ['id_kamar' => $id_kamars],
            order: $order
        );
        
        // var_dump($id_kamar, $this->santriKamarModel->getLastQuery());

        $saved_nilai = $this->model->getAll(
            whereAnd: ['id_semester' => $id_semester, 'id_kamar' => $id_kamar],
            // orWhereIn: ['id_pembagian_mapel' => $id_pembagian_mapels],
        );
        $result = [];
        foreach ($saved_nilai as $key => $value) {
            $result[$value->id_santri] = $value;
        }
        
        // var_dump($santris, $result);
        array_walk($santris, function($a) use ($count_kategori, $kategori, $result) {
            $id_santri = $a->id_santri;
            // var_dump($id_santri, $result[$id_santri]);
            $a->id = $result[$id_santri]->id ?? -1;
            $a->nilai = [];
            for ($i=0; $i < $count_kategori; $i++) { 
                $cat = $kategori[$i];
                $key = "nilai_" . ($i + 1);
                $a->nilai[$i] = (object)[
                    'kategori' => $cat,
                    'col' => $key,
                    'nilai' => $result[$id_santri]->$key ?? null,
                ];
            }
            $a->nilai = array_merge($a->nilai,[
                (object)[
                    'kategori' => 'Izin Sakit',
                    'col' => 'sakit',
                    'type' => 'number',
                    'nilai' => $result[$id_santri]->sakit ?? 0,
                ],
                (object)[
                    'kategori' => 'Izin karena Kepentingan',
                    'col' => 'izin',
                    'type' => 'number',
                    'nilai' => $result[$id_santri]->izin ?? 0,
                ],
                (object)[
                    'kategori' => 'Tanpa Keterangan',
                    'col' => 'alfa',
                    'type' => 'number',
                    'nilai' => $result[$id_santri]->alfa ?? 0,
                ],
            ]);
            return $a;
        });

        return $this->respondCreated($santris);
    }
}