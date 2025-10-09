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
        $this->model = model('MapelNilaiModel');
        $this->santriModel = model('DataSantriModel');
        $this->mapelPembagianModel = model('MapelPembagianModel');
    }

    public function index()
    {
        $id_pembagian_mapel = $this->request->getGetPost('id_pembagian_mapel') ?? -1;
        $order = $this->request->getGetPost('order') ?? ['nama asc'];
        $order = implode(',', $order);
        $pembagian = $this->mapelPembagianModel->find($id_pembagian_mapel) ?? NULL;
        $saved_nilai = $this->model->getAll(whereAnd: ['id_pembagian_mapel' => $id_pembagian_mapel]);
        // var_dump($pembagian);
        $id_kelas = $pembagian->id_kelas ?? -1;

        $santris = $this->santriModel->getAll(whereAnd: ['id_kelas' => $id_kelas, 'status' => '0'], order: $order);
        $result = [];
        foreach ($saved_nilai as $key => $value) {
            $result[$value->id_santri] = $value;
        }
        
        // var_dump($santris, $result);
        array_walk($santris, function($a) use ($id_pembagian_mapel, $result) {
            $a->id_santri = $id_santri = $a->id;
            // var_dump($id_santri, $result[$id_santri]);
            $a->id = $result[$id_santri]->id ?? -1;
            $a->id_pembagian_mapel = $id_pembagian_mapel;
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

        return $this->respondCreated($santris);
    }

    public function rekapitulasi($return_data = FALSE)
    {
        $id_semester = $this->request->getGetPost('id_semester');
        $id_kelas = $this->request->getGetPost('id_kelas');
        if (empty($id_semester)) $id_semester = -1;
        if (empty($id_kelas)) $id_kelas = -1;
        $ujian = $this->request->getGetPost('ujian') ?? 'uts';

        $order = $this->request->getGetPost('order') ?? ['nama asc'];
        $order = implode(',', $order);

        // var_dump($id_kelas, $id_semester);
        $mapel = $this->mapelPembagianModel->getAll(whereAnd: ['id_kelas' => $id_kelas, 'id_semester' => $id_semester], order: 'nama_mapel asc');
        $mapels = [];
        $mapel_ids = [];
        // $mapel = array_splice($mapel, 0, 15);
        foreach ($mapel as $key => $value) {
            $mapels[$key] = (object) [
                'id_pembagian_mapel' => $value->id,
                'nama_mapel' => $value->nama_mapel,
                'uts' => 0,
                'uas' => 0,
                'nilai_rapor' => 0,
                'katrol1' => 0,
            ];
            $mapel_ids[$value->id] = $key;
        }
        // var_dump($mapels);exit;
        $santris = $this->santriModel->getAll(whereAnd: ['id_kelas' => $id_kelas, 'status' => '0'], order: $order);
        // var_dump($this->santriModel->getLastQuery());
        $result = [];
        foreach ($santris as $key => $santri) {
            $result[$santri->id] = (object) [
                'id_santri' => $santri->id,
                'nama' => $santri->nama,
                'mapel' => unserialize(serialize($mapels)),
            ];
        }

        $saved_nilai = $this->model->getAll(whereAnd: ['{n}id_kelas' => $id_kelas]);
        foreach ($saved_nilai as $key => $nilai) {
            $id_santri = $nilai->id_santri;
            $ind_mapel = $mapel_ids[$nilai->id_pembagian_mapel];
            // var_dump($id_santri, $id_pembagian_mapel, $result[$id_santri]->nama, $nilai->uts);
            if(isset($result[$id_santri]) && isset($result[$id_santri]->mapel[$ind_mapel])){
                $result[$id_santri]->mapel[$ind_mapel]->id = $nilai->id;
                $result[$id_santri]->mapel[$ind_mapel]->uts = $nilai->uts;
                $result[$id_santri]->mapel[$ind_mapel]->uas = $nilai->uas;
                $result[$id_santri]->mapel[$ind_mapel]->nilai_rapor = $nilai->nilai_rapor;
                $result[$id_santri]->mapel[$ind_mapel]->katrol1 = $nilai->katrol1;
            }
        }

        $count_mapel = count($mapels);
        foreach ($result as $key => $santri) {
            $total_uts = 0;
            $total_uas = 0;
            $total_nilai_rapor = 0;
            foreach ($santri->mapel as $key => $map) {
                $total_uts += $map->uts;
                $total_uas += $map->uas;
                $total_nilai_rapor += $map->nilai_rapor;
            }
            $santri->total_uts = $total_uts;
            $santri->total_uas = $total_uas;    
            $santri->total_nilai_rapor = $total_nilai_rapor;
            $santri->rata_uts = $count_mapel > 0 ? round($total_uts / $count_mapel, 2) : 0;
            $santri->rata_uas = $count_mapel > 0 ? round($total_uas / $count_mapel, 2) : 0;
            $santri->rata_nilai_rapor = $count_mapel > 0 ? round($total_nilai_rapor / $count_mapel, 2) : 0;
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

    public function download_ledger()
    {
        $id_semester = $this->request->getGetPost('id_semester');
        $id_kelas = $this->request->getGetPost('id_kelas');
        $semester = model('DataSemesterModel')->getData($id_semester);
        $kelas = model('DataKelasModel')->getData($id_kelas);

        $result = array_values($this->rekapitulasi(TRUE));
        
        $filename = APPPATH . '../templates/ledger-mid.xlsx';
        /**  Create a new Reader of the type that has been identified  **/
        $spreadsheet = IOFactory::load($filename);
        $sheet = $spreadsheet->setActiveSheetIndex(0);

        $images = backupImageInfo($spreadsheet);
        // var_dump($images);exit;
        $cols = excelColumnRange('D','AAA');
        $mapels = array_values(reset($result)->mapel);
        $count_mapel = count($mapels);
        // var_dump($cols, $count_mapel);
        for ($i=0; $i < $count_mapel; $i++) { 
            $next = $i + 1;
            // var_dump($cols[$i], $cols[$next]);
            $sheet->setCellValue($cols[$i] ."15", $mapels[$i]->nama_mapel ?? '');
            if ($next < $count_mapel)
                $sheet->insertNewColumnBefore($cols[$next]);
            // duplicateColumn($spreadsheet, $cols[$i], $cols[$i+1]);
        }
        $sheet->mergeCells("D14:".$cols[$count_mapel - 1]."14");
        $sheet->setCellValue("A10", "DAFTAR NILAI UJIAN TENGAH SEMESTER ".strtoupper($semester->semester ?? ''));
        $sheet->setCellValue("A11", "KMI PONDOK PESANTREN MUHAMMADIYAH DARUL ARQAM PATEAN ");
        $sheet->setCellValue("A12", "TAHUN AJARAN ".strtoupper($semester->tahun_ajaran ?? ''));
        reApplyImageInfo($spreadsheet, $images);

        $row = 16;
        // var_dump($kelas);exit;
        foreach ($result as $key => $santri) {
            duplicateRow($spreadsheet, $row, $row + 1);
            $sheet->setCellValue("A$row", $key + 1);
            $sheet->setCellValue("B$row", $santri->nama);
            $sheet->setCellValue("C$row", $kelas->kelas ?? '');
            
            $santri->mapel = array_values($santri->mapel);
            foreach ($santri->mapel as $key => $mapel) {
               $sheet->setCellValue($cols[$key] . $row, $mapel->uts);
            }
            $sheet->setCellValue($cols[$count_mapel] . $row, $santri->total_uts);
            $sheet->setCellValue($cols[$count_mapel + 1] . $row, $santri->rata_uts);
            $sheet->setCellValue($cols[$count_mapel + 2] . $row, $santri->ranking);
            // $sheet->insertNewRowBefore($row + 1);
            $row++;
        }
        for ($i=0; $i < $count_mapel; $i++) { 
            $col = $cols[$i];
            // $sheet->getColumnDimension($col)->setAutoSize(true);
            $sheet->getColumnDimension($col)->setWidth(3.2);
        }
        
        $filename = "LEDGER MID SEMESTER";
        // exit;
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        // ob_end_clean();
        $writer->save('php://output');
    }

    public function download_raport()
    {
        $id_semester = $this->request->getGetPost('id_semester');
        $id_kelas = $this->request->getGetPost('id_kelas');
        $semester = model('DataSemesterModel')->getData($id_semester);
        $kelas = model('DataKelasModel')->getData($id_kelas);

        $result = array_values($this->rekapitulasi(TRUE));
        $result = array_splice($result, 0, 4);
        
        $templatePath = APPPATH . '../templates/raport-mid.docx';
        $templateProcessor = new TemplateProcessor($templatePath);

        $santri = reset($result);
        // return $this->respondCreated($santri);
        $templateProcessor->setValue("semester", strtoupper($semester->semester));
        $templateProcessor->setValue("tahun_ajaran", $semester->tahun_ajaran);
        $templateProcessor->setValue("nama", $santri->nama);
        $templateProcessor->setValue("stb", $santri->stb ?? '');
        $templateProcessor->setValue("semester_small", ucfirst($semester->semester));
        $templateProcessor->setValue("kelas", $kelas->kelas);
        $templateProcessor->cloneRow('no', count($santri->mapel));

        foreach ($santri->mapel as $index => $mapel) {
            $i = $index + 1;
            $templateProcessor->setValue("no#{$i}", $i);
            $templateProcessor->setValue("mapel#{$i}", $mapel->nama_mapel);
            $templateProcessor->setValue("uts#{$i}", $mapel->uts);
            $templateProcessor->setValue("uts_bilangan#{$i}", $mapel->uts);
            $templateProcessor->setValue("no_arab#{$i}", $i);
            $templateProcessor->setValue("mapel_arab#{$i}", $mapel->nama_mapel);
            $templateProcessor->setValue("uts_arab#{$i}", $mapel->uts);
            $templateProcessor->setValue("uts_bilangan_arab#{$i}", $mapel->uts);
        }
        $templateProcessor->setValue("total_uts", $santri->total_uts);
        $templateProcessor->setValue("rata_uts", $santri->rata_uts);
        $templateProcessor->setValue("total_uts_arab", $santri->total_uts);
        $templateProcessor->setValue("rata_uts_arab", $santri->rata_uts);
        $templateProcessor->setValue("tanggal", dateIndo(date('Y-m-d')));
        $templateProcessor->setValue("tanggal_arab", dateIndo(date('Y-m-d')));

        // Set HTTP headers to force download
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="Raport-MID.docx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');

        // Save output to PHP output stream (browser)
        $templateProcessor->saveAs('php://output');
        exit;  // Always exit after output to avoid extra output

    }
}