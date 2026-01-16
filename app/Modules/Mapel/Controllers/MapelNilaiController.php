<?php

namespace Modules\Mapel\Controllers;

use App\Controllers\BaseDataController;
use App\Libraries\PdfBuilder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpWord\TemplateProcessor;

class MapelNilaiController extends BaseDataController
{
    public $santriModel;
    public $mapelPembagianModel;

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('MapelNilaiModel');
        $this->santriModel = model('DataSantriModel');
        $this->mapelPembagianModel = model('MapelPembagianModel');
    }

    public function index($return_data = FALSE)
    {
        $id_semester = $this->request->getGetPost('id_semester') ?? -1;
        $id_kelas = $this->request->getGetPost('id_kelas') ?? -1;
        $id_mapel = $this->request->getGetPost('id_mapel') ?? -1;
        if (!is_array($id_kelas))
            $id_kelas = [$id_kelas];
        if (!is_array($id_mapel))
            $id_mapel = [$id_mapel];
        $order = $this->request->getGetPost('order') ?? ['no_presensi asc','nama asc'];
        $order = implode(',', $order);


        $result = [];
        foreach ($id_kelas as $key => $_id_kelas) {
            foreach ($id_mapel as $key => $_id_mapel) {
                // var_dump($this->mapelPembagianModel->getLastQuery());
                $saved_nilai = $this->model->getAll(whereAnd:[
                    'id_semester' => $id_semester,
                    'id_kelas' => $_id_kelas,
                    'id_mapel' => $_id_mapel,
                ]);
                
                $santris = $this->santriModel->getAll(whereAnd: [
                    'id_kelas' => $_id_kelas,
                ], order: $order );

                foreach ($saved_nilai as $key => $value) {
                    $result[$value->id_santri] = $value;
                }
                // var_dump($santris, $result);
                array_walk($santris, function($a) use ($id_semester, $_id_kelas, $_id_mapel, $result) {
                    $a->id_santri = $id_santri = $a->id;
                    // var_dump($id_santri, $result[$id_santri]);
                    $a->id = $result[$id_santri]->id ?? -1;
                    $a->id_semester = $id_semester;
                    $a->id_kelas = $_id_kelas;
                    $a->id_mapel = $_id_mapel;
                    $a->nilai = (object)[
                        'nilai_harian' => $result[$id_santri]->nilai_harian ?? 0,
                        'uts' => $result[$id_santri]->uts ?? 0,
                        'uas' => $result[$id_santri]->uas ?? 0,
                        'nilai_rapor' => $result[$id_santri]->nilai_rapor ?? 0,
                        'katrol1' => $result[$id_santri]->katrol1 ?? 0,
                        'katrol2' => $result[$id_santri]->katrol2 ?? 0,
                    ];
                    return $a;
                });
            }
        }

        if ($return_data)
            return $santris;

        return $this->respondCreated($santris);
    }

    public function download_template()
    {
        $data = $this->index(TRUE);
        // return $this->respondCreated($data);
        
        $filename = 'TEMPLATE-NILAI';
        // var_dump($filename);exit;
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
                    ->setCreator('Codev-App')
                    ->setTitle('Aplikasi Sekolah');
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setCellValue('A1', 'No');
        $activeWorksheet->setCellValue('B1', 'Nama');
        $activeWorksheet->setCellValue('C1', 'NH');
        $activeWorksheet->setCellValue('D1', 'UTS');
        $activeWorksheet->setCellValue('E1', 'UAS');

        $row = 2;
        foreach ($data as $key => $value) {
           $activeWorksheet->setCellValue('A'.$row, $key + 1);
           $activeWorksheet->setCellValue('B'.$row, $value->nama);
           $activeWorksheet->setCellValue('C'.$row, $value->nilai->nilai_harian ?? 0);
           $activeWorksheet->setCellValue('D'.$row, $value->nilai->uts ?? 0);
           $activeWorksheet->setCellValue('E'.$row, $value->nilai->uas ?? 0);
           $row++;
        }

        for ($i = 'A'; $i !=  $activeWorksheet->getHighestColumn(); $i++) {
            $activeWorksheet->getColumnDimension($i)->setAutoSize(TRUE);
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        // ob_end_clean();
        $writer->save('php://output');
    }

    public function rekapitulasi($return_data = FALSE)
    {
        $id_semester = $this->request->getGetPost('id_semester');
        $id_kelas = $this->request->getGetPost('id_kelas');
        $id_santri = $this->request->getGetPost('id_santri');
        $id_pembagian = $this->request->getGetPost('id_pembagian');
        if (empty($id_semester)) $id_semester = -1;
        if (empty($id_kelas)) $id_kelas = -1;
        $ujian = $this->request->getGetPost('ujian') ?? 'uts';

        $order = $this->request->getGetPost('order') ?? ['nama asc'];
        $order = implode(',', $order);

        // var_dump($id_pembagian);
        $whereIn = empty($id_pembagian) ? [] : ['id' => $id_pembagian];

        $mapel = $this->mapelPembagianModel->getAll(
            whereAnd: ['id_kelas' => $id_kelas, 'id_semester' => $id_semester],
            whereIn: $whereIn, 
            order: 'nama_mapel asc');

        $mapels = [];
        $mapel_ids = [];
        // $mapel = array_splice($mapel, 0, 15);
        foreach ($mapel as $key => $value) {
            $mapels[$key] = (object) [
                'id_pembagian_mapel' => $value->id,
                'nama_mapel' => $value->nama_mapel,
                'nama_guru' => $value->nama_guru,
                'nama_guru_arab' => $value->nama_guru_arab,
                'nama_mapel_arab' => $value->nama_mapel_arab ?? $value->nama_mapel,
                'nilai_harian' => 0,
                'uts' => 0,
                'uas' => 0,
                'nilai_rapor' => 0,
                'katrol1' => 0,
            ];
            $mapel_ids[$value->id] = $key;
        }
        // var_dump($mapels);exit;
        $santris = $this->santriModel->getAll(
            whereAnd: ['id_kelas' => $id_kelas, 'id' => $id_santri, 'status' => '0'], 
            order: $order);
        // var_dump($this->santriModel->getLastQuery());
        // var_dump($santris);
        // exit;
        $result = [];
        foreach ($santris as $key => $santri) {
            $result[$santri->id] = (object) [
                'id_santri' => $santri->id,
                'stb' => $santri->stb,
                'nama' => $santri->nama,
                'nama_arab' => $santri->nama_arab,
                'kelas' => $santri->kelas,
                'daerah' => $santri->daerah,
                'daerah_arab' => $santri->daerah_arab,
                'id_kelas' => $santri->id_kelas,
                'mapel' => unserialize(serialize($mapels)),
            ];
        }

        $whereIn = empty($id_pembagian) ? [] : ['id_pembagian_mapel' => $id_pembagian];
        $saved_nilai = $this->model->getAll(
            whereAnd: ['{n}id_kelas' => $id_kelas],
            whereIn: $whereIn, 
        );
        foreach ($saved_nilai as $key => $nilai) {
            $id_santri = $nilai->id_santri;
            $ind_mapel = $mapel_ids[$nilai->id_pembagian_mapel];
            // var_dump($id_santri, $id_pembagian_mapel, $result[$id_santri]->nama, $nilai->uts);
            if(isset($result[$id_santri]) && isset($result[$id_santri]->mapel[$ind_mapel])){
                $result[$id_santri]->mapel[$ind_mapel]->id = $nilai->id;
                $result[$id_santri]->mapel[$ind_mapel]->nilai_harian = $nilai->nilai_harian;
                $result[$id_santri]->mapel[$ind_mapel]->uts = $nilai->uts;
                $result[$id_santri]->mapel[$ind_mapel]->uas = $nilai->uas;
                $result[$id_santri]->mapel[$ind_mapel]->nilai_rapor = $nilai->nilai_rapor;
                $result[$id_santri]->mapel[$ind_mapel]->katrol1 = $nilai->katrol1;
            }
        }

        $count_mapel = count($mapels);
        foreach ($result as $key => $santri) {
            $total_nilai_harian = 0;
            $total_uts = 0;
            $total_uas = 0;
            $total_nilai_rapor = 0;
            foreach ($santri->mapel as $key => $map) {
                $total_nilai_harian += $map->nilai_harian;
                $total_uts += $map->uts;
                $total_uas += $map->uas;
                $total_nilai_rapor += $map->nilai_rapor;
            }
            $santri->total_nilai_harian = $total_nilai_harian;
            $santri->total_uts = $total_uts;
            $santri->total_uas = $total_uas;    
            $santri->total_nilai_rapor = round($total_nilai_rapor, 1);
            $santri->rata_nilai_harian = $count_mapel > 0 ? round($total_nilai_harian / $count_mapel, 1) : 0;
            $santri->rata_uts = $count_mapel > 0 ? round($total_uts / $count_mapel, 1) : 0;
            $santri->rata_uas = $count_mapel > 0 ? round($total_uas / $count_mapel, 1) : 0;
            $santri->rata_nilai_rapor = $count_mapel > 0 ? round($total_nilai_rapor / $count_mapel, 1) : 0;
        }

        $newArr = unserialize(serialize($result));
        usort($newArr, function($a, $b) use ($ujian){
            $compare = 0;
            if ($ujian) {
                $ind = "total_$ujian";
                $compare = $a->$ind <=> $b->$ind;
            }
            if ($compare === 0)
                return $a->nama <=> $b->nama;
            else
                return $compare * -1;
        });
        // return $this->respondCreated($newArr);
        foreach ($newArr as $key => $value) {
            $result[$value->id_santri]->ranking = $key + 1;
        }

        // var_dump($return_data);
        if ($return_data)
            return $result;

        return $this->respondCreated(array_values($result));
    }
    
    public function get_progres()
    {
        $id_semester = $this->request->getGetPost('id_semester');
        $id_kelas = $this->request->getGetPost('id_kelas');
        $id_guru = $this->request->getGetPost('id_guru');
        
        $summary = $this->model->get_progres($id_semester, $id_kelas, $id_guru);
        // var_dump($this->model->getLastQuery());
        return $this->respondCreated($summary);
    }
}