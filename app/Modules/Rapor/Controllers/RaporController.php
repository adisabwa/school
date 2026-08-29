<?php

namespace Modules\Rapor\Controllers;

use App\Controllers\BaseDataController;
use App\Libraries\PdfConverter;
use PhpOffice\PhpSpreadsheet\IOFactory as IOFactorySpreadsheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory as IOFactoryWord;

class RaporController extends BaseDataController
{
    public $santriModel;
    public $santriKamarModel;
    public $mapelPembagianModel;
    public $pengasuhanNilaiModel;

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('RaporModel', false);
        $this->nilaiModel = model('MapelNilaiModel');
        $this->santriModel = model('DataSantriKelasModel');
        $this->mapelPembagianModel = model('MapelPembagianModel');
        $this->pengasuhanNilaiModel = model('NilaiPengasuhanModel');
        $this->santriKamarModel = model('DataSantriKamarModel');

    }

    public function rekapitulasi($return_data = FALSE)
    {
        $id_semester = $this->request->getGetPost('id_semester');
        $id_kelas = $this->request->getGetPost('id_kelas');
        $id_santri = $this->request->getGetPost('id_santri');
        if (empty($id_semester) ||empty($id_kelas)) 
            return $this->respondCreated([]);
        
        $ujian = $this->request->getGetPost('ujian') ?? 'nilai_rapor';

        $order = $this->request->getGetPost('order') ?? ['no_presensi asc','nama asc'];
        $order = implode(',', $order);

        // var_dump($id_pembagian);
        $semester = model('DataSemesterModel')->getData($id_semester);
        $kelas = model('DataKelasAjarModel')->getDataWhere(whereAnd:[
            'id_kelas' => $id_kelas,
            'tahun_ajaran' => $semester->tahun_ajaran ?? ''
        ]);
        $santris = $this->santriModel->getAll(
            whereAnd: [
                'id_kelas' => $id_kelas, 
                'tahun_ajaran' => $semester->tahun_ajaran ?? ''
            ], 
            order: $order);
            
        $kelas->jumlah_santri = count($santris);
        
        $santriKamar = $this->santriKamarModel->getAll(
            whereAnd: ['{n}id_kelas' => $id_kelas, 
            // '{n}id_santri' => $id_santri, 
            '{n}status' => '0'], 
        );
        $sk = [];
        foreach ($santriKamar as $key => $value) {
            $sk[$value->id_santri] = $value;
        }
        // var_dump($kelas);exit;
        $mapel = $this->mapelPembagianModel->getAll(
            whereAnd: ['id_kelas' => $id_kelas, 'id_semester' => $id_semester],
            order: 'nama_mapel asc');

        $mapels = [];
        // $mapel = array_splice($mapel, 0, 15);
        foreach ($mapel as $key => $value) {
            $mapels[$value->id_mapel] = (object) [
                'id_mapel' => $value->id_mapel,
                'nama_mapel' => $value->nama_mapel,
                'is_kejuruan' => $value->is_kejuruan,
                'nama_mapel_arab' => $value->nama_mapel_arab ?? $value->nama_mapel,
                'nilai_harian' => 0,
                'uts' => 0,
                'uas' => 0,
                'um' => 0,
                'nilai_rapor' => 0,
                'katrol1' => 0,
                'katrol2' => 0,
            ];
        }

        
        $kategori = unserialize(NILAI_PENGASUHAN_KATEGORI);

        $pengasuhan = [];
        // $mapel = array_splice($mapel, 0, 15);
        foreach ($kategori as $key => $value) {
            $index = "nilai_".($key+1);
            $pengasuhan[$index] = (object) [
                'key' => $key,
                'index' => $index,
                'kategori' => $value,
                'nilai' => 0,
            ];
        }
        $pengasuhan = array_merge($pengasuhan,[
            'sakit' => (object)[
                'kategori' => 'Izin Sakit',
                'index' => 'sakit',
                'type' => 'number',
                'nilai' => 0,
            ],
            'izin' => (object)[
                'kategori' => 'Izin karena Kepentingan',
                'index' => 'izin',
                'type' => 'number',
                'nilai' => 0,
            ],
            'alfa' => (object)[
                'kategori' => 'Tanpa Keterangan',
                'index' => 'alfa',
                'type' => 'number',
                'nilai' => 0,
            ],
        ]);
        // var_dump($this->santriModel->getLastQuery());
        // var_dump($santris);
        // exit;
        $result = [];
        foreach ($santris as $key => $santri) {
            $_id_santri = $santri->id_santri;
            $kamar = $sk[$_id_santri] ?? null;
            $result[$_id_santri] = (object) [
                'id_santri' => $_id_santri,
                'stb' => $santri->stb,
                'nisn' => $santri->nisn,
                'induk_sekolah' => $santri->induk_sekolah,
                'nama' => $santri->nama,
                'nama_arab' => $santri->nama_arab,
                'kelas' => $santri->kelas,
                'daerah' => $santri->daerah,
                'daerah_arab' => $santri->daerah_arab,
                'id_kelas' => $santri->id_kelas,
                'tingkat' => $santri->tingkat,
                'id_jurusan' => $santri->id_jurusan,
                'rayon' => $kamar->rayon ?? '',
                'kamar' => $kamar->kamar ?? '',
                'kamar_arab' => ($kamar->rayon_arab ?? '')." - ".to_arabic_number($kamar->nomor ?? ''),
                'nama_walas' => $kelas->nama_walas ?? '',
                'nama_walas_lengkap' => $kelas->nama_walas_lengkap ?? '',
                'nama_walas_arab' => $kelas->nama_walas_arab ?? '',
                'nbm_walas' => $kelas->nbm_walas ?? '',
                'nama_wamar' => $kamar->nama_wamar ?? '',
                'nama_wamar_lengkap' => $kamar->nama_wamar_lengkap ?? '',
                'nama_wamar_arab' => $kamar->nama_wamar_arab ?? '',
                'wamar_signature' => $kamar->wamar_signature ?? '',
                'nomor' => $kamar->nomor ?? '',
                'catatan' => '',
                'korikuler' => '',
                'ranking_kelas' => '',
                'ranking_angkatan' => '',
                'naik_kelas' => '1',
                'mapel' => unserialize(serialize($mapels)),
                'pengasuhan' => unserialize(serialize($pengasuhan)),
                'total_nilai_pengasuhan' => 0,
                'rata_nilai_pengasuhan' => 0,
            ];
        }
        // var_dump($result);
        // Ambil Nilai Raport KMI
        $saved_nilai = $this->nilaiModel->getAll(
            whereAnd: ['{n}id_kelas' => $id_kelas, '{n}id_santri' => $id_santri, '{n}id_semester' => $id_semester],
        );
        // var_dump($this->nilaiModel->getLastQuery());
        // var_dump($saved_nilai);
        foreach ($saved_nilai as $key => $nilai) {
            $item_id_santri = $nilai->id_santri;
            $ind_mapel = $nilai->id_mapel;
            if(isset($result[$item_id_santri])){
                $result[$item_id_santri]->id_rapor = $nilai->id_rapor;
                $result[$item_id_santri]->catatan = $nilai->catatan;
                $result[$item_id_santri]->korikuler = $nilai->korikuler;
                $result[$item_id_santri]->ranking_kelas = $nilai->ranking_kelas;
                $result[$item_id_santri]->ranking_angkatan = $nilai->ranking_angkatan;
                $result[$item_id_santri]->naik_kelas = $nilai->naik_kelas;
                
                if (isset($result[$item_id_santri]->mapel[$ind_mapel])){
                    $result[$item_id_santri]->mapel[$ind_mapel]->id = $nilai->id;
                    $result[$item_id_santri]->mapel[$ind_mapel]->nilai_harian = round($nilai->nilai_harian);
                    $result[$item_id_santri]->mapel[$ind_mapel]->uts = round($nilai->uts);
                    $result[$item_id_santri]->mapel[$ind_mapel]->uas = round($nilai->uas);
                    $result[$item_id_santri]->mapel[$ind_mapel]->um = round($nilai->um);
                    $result[$item_id_santri]->mapel[$ind_mapel]->nilai_rapor = round($nilai->nilai_rapor);
                    $result[$item_id_santri]->mapel[$ind_mapel]->katrol1 = $nilai->katrol1;
                    $result[$item_id_santri]->mapel[$ind_mapel]->katrol2 = $nilai->katrol2;
                }
            }
        }
        // var_dump($result);
        $count_mapel = count($mapels);
        foreach ($result as $key => $santri) {
            $total_nilai_harian = 0;
            $total_uts = 0;
            $total_uas = 0;
            $total_um = 0;
            $total_nilai_rapor = 0;
            $total_katrol1 = 0;
            $total_katrol2 = 0;
            foreach ($santri->mapel as $key => $map) {
                $total_nilai_harian += $map->nilai_harian;
                $total_uts += $map->uts;
                $total_uas += $map->uas;
                $total_nilai_rapor += $map->nilai_rapor;
                $total_um += $map->um;
                $total_katrol1 += $map->katrol1;
                $total_katrol2 += $map->katrol2;
            }
            $santri->total_nilai_harian = $total_nilai_harian;
            $santri->total_uts = $total_uts;
            $santri->total_uas = $total_uas;    
            $santri->total_um = $total_um;
            $santri->total_nilai_rapor = round($total_nilai_rapor);
            $santri->total_katrol1 = $total_katrol1;
            $santri->total_katrol2 = $total_katrol2;
            $santri->rata_nilai_harian = $count_mapel > 0 ? round($total_nilai_harian / $count_mapel) : 0;
            $santri->rata_uts = $count_mapel > 0 ? round($total_uts / $count_mapel) : 0;
            $santri->rata_uas = $count_mapel > 0 ? round($total_uas / $count_mapel) : 0;
            $santri->rata_um = $count_mapel > 0 ? round($total_um / $count_mapel) : 0;
            $santri->rata_nilai_rapor = $count_mapel > 0 ? round($total_nilai_rapor / $count_mapel, 2) : 0;
            $santri->rata_kantrol1 = $count_mapel > 0 ? round($total_katrol1 / $count_mapel) : 0;
            $santri->rata_kantrol2 = $count_mapel > 0 ? round($total_katrol2 / $count_mapel) : 0;
            $santri->mapel = array_values($santri->mapel);
        }

        // Ambil Nilai Pengasuhan        
        $saved_nilai = $this->pengasuhanNilaiModel->getAll(
            whereAnd: ['{n}id_kelas' => $id_kelas],
        );
        // var_dump($this->nilaiModel->getLastQuery());

        foreach ($saved_nilai as $key => $nilai) {
            $id_santri = $nilai->id_santri;
            // var_dump($nilai);
            foreach ($kategori as $key => $value) {
                $index = "nilai_".($key+1);
                if (isset($result[$id_santri]) && isset($result[$id_santri]->pengasuhan[$index])){
                    $result[$id_santri]->pengasuhan[$index]->nilai = $nilai->$index;
                    $result[$id_santri]->total_nilai_pengasuhan += (int)$nilai->$index;
                }
            }
            
            foreach (['sakit','izin','alfa'] as $absen_type){
                if (isset($result[$id_santri]) && isset($result[$id_santri]->pengasuhan[$absen_type])){
                    $result[$id_santri]->pengasuhan[$absen_type]->nilai = $nilai->$absen_type;
                }
            }
        }

        $count_mapel = count($kategori);
        foreach ($result as $key => $santri) {
            // $santri->mapel = array_values($santri->mapel);
            // $santri->pengasuhan = array_values($santri->pengasuhan);
            $santri->rata_nilai_pengasuhan = $count_mapel > 0 ? round($santri->total_nilai_pengasuhan / $count_mapel, 1) : 0;
            // $santri->pengasuhan = array_values($santri->pengasuhan);
        }

        // $newArr = unserialize(serialize($result));
        // // var_dump($ujian);
        // usort($newArr, function($a, $b) use ($ujian){
        //     $compare = 0;
        //     if ($ujian) {
        //         $ind = "total_$ujian";
        //         $compare = $a->$ind <=> $b->$ind;
        //     }
        //     if ($compare === 0)
        //         return $a->nama <=> $b->nama;
        //     else
        //         return $compare * -1;
        // });
        // return $this->respondCreated($newArr);
        // foreach ($newArr as $key => $value) {
        //     $result[$value->id_santri]->ranking = $key + 1;
        // }

        // var_dump($result);exit;
        // var_dump($return_data);
        if ($return_data)
            return $result;

        return $this->respondCreated(array_values($result));
    }

    public function get_nilai_rdm($id_semester = NULL, $id_mapel = NULL, $tingkat = NULL)
    {
        $id_semester = $id_semester ?? $this->request->getGetPost('id_semester');
        $id_mapel = $id_mapel ?? $this->request->getGetPost('id_mapel');
        $tingkat = $tingkat ?? $this->request->getGetPost('tingkat');

        $nilai = $this->nilaiModel->getAll(
            whereAnd:['{n}id_semester' => $id_semester, '{n}tingkat' => $tingkat, '{n}id_mapel'=>$id_mapel],
        );
        $rapor_list = [];
        $um_list = [];
        foreach ($nilai as $key => $value) {
            if ($value->nilai_rapor > 0 ) $rapor_list[] = $value->nilai_rapor;
            if ($value->um > 0 ) $um_list[] = $value->um;
        }   
        if (empty($rapor_list)) $rapor_list = [0];
        if (empty($um_list)) $um_list = [0];
        var_dump($rapor_list, $um_list);
        $updates = [];
        if (!empty($rapor_list)) {
            // return $this->respondCreated($rapor_list);
            $min = min($rapor_list);
            $max = max($rapor_list);
            $min_um = min($um_list);
            $max_um = max($um_list);

            if ($max == $min) $max = $min + 1;
            $nilai_min = 78; //Nilai minimal baru
            if ($min > $nilai_min) $nilai_min = $min;
            //Nilai max baru
            if ($max <= 60) $nilai_max = 80;
            elseif ($max <= 70) $nilai_max = 85;
            elseif ($max <= 80) $nilai_max = 90;
            elseif ($max <= 90) $nilai_max = 95;
            else $nilai_max = 99;

            
            if ($max_um == $min_um) $max_um = $min_um + 1;
            $nilai_min_um = 78; //Nilai minimal baru
            if ($min_um > $nilai_min_um) $nilai_min_um = $min_um;
            //Nilai max baru
            if ($max_um <= 60) $nilai_max_um = 80;
            elseif ($max_um <= 70) $nilai_max_um = 85;
            elseif ($max_um <= 80) $nilai_max_um = 90;
            elseif ($max_um <= 90) $nilai_max_um = 95;
            else $nilai_max_um = 99;

            var_dump($min, $max, $nilai_min, $nilai_max);
            var_dump($min_um, $max_um, $nilai_min_um, $nilai_max_um);
            foreach ($nilai as $key => $value) {
                $katrol = $value->nilai_rapor <= 0 ? 0 : round($nilai_min + ( ( $value->nilai_rapor - $min ) / ( $max - $min ) * ( $nilai_max - $nilai_min ) ));
                $katrol2 = $value->um <= 0 ? 0 : round($nilai_min_um + ( ( $value->um - $min_um ) / ( $max_um - $min_um ) * ( $nilai_max_um - $nilai_min_um ) ));
                $updates[] = [
                    'id' => $value->id,
                    'katrol1' => $katrol,
                    'katrol2' => $katrol2,
                ];
            }
        }

        $this->nilaiModel->transBegin();

        if (!empty($updates))
            $this->nilaiModel->updateBatch($updates, 'id');

        if ($this->nilaiModel->transStatus() === false) {
            $this->nilaiModel->transRollback();
            return $this->failServerError();
        } else {
            $this->nilaiModel->transCommit();                             
            return $this->respondCreated($nilai);
        }

    }

    public function countRanking($id_semester = NULL, $id_jurusan = NULL, $tingkat = NULL, $id_kelas = NULL, $ujian = 'nilai_rapor')
    {
        $id_semester = $id_semester ?? $this->request->getGetPost('id_semester');
        $id_jurusan = $id_jurusan ?? $this->request->getGetPost('id_jurusan');
        $tingkat = $tingkat ?? $this->request->getGetPost('tingkat');
        $id_kelas = $id_kelas ?? $this->request->getGetPost('id_kelas');

        $this->model->relations['id_kelas']['on_condition'] = [
            $tingkat ? "tingkat = $tingkat" : NULL,
            $id_jurusan ? "id_jurusan = $id_jurusan" : NULL,
        ];
        $raportAngkatan = $this->model->getAll(
            whereAnd:['id_semester' => $id_semester],
            order: "total_$ujian desc, nama asc",
        );
        // var_dump($this->model->getLastQuery());
        // return $this->respondCreated($raportAngkatan);
        
        $updates = [];
        $ranking_kelas = 1;
        $ranking_angkatan = 1;
        foreach ($raportAngkatan as $key => $value) {
            $new_updates = [
                'id' => $value->id,
            ];
            if ($value->id_kelas == $id_kelas) {
                $new_updates['ranking_kelas'] = $ranking_kelas;
                $ranking_kelas++;
            }
            $new_updates['ranking_angkatan'] = $ranking_angkatan;
            $ranking_angkatan++;
            $updates[] = $new_updates;
        }

        $this->model->transBegin();
        if (!empty($updates)) {
            foreach ($updates as $key => $value) {
                $this->model->update($value['id'], $value);
            }
        }

        if ($this->model->transStatus() === false) {
            var_dump($this->model->error());
            $this->model->transRollback();
            return $this->failServerError();
        } else {
            $this->model->transCommit();                             
            return $this->respondCreated();
        }

    }

    public function get_nilai_rdm_all()
    {
        $id_semester = model('DataSemesterModel')->getSemesterNow()->id ?? -1;
        $kelas = ['1','2','3','4','5','6'];
        $mapel = model('MapelModel')->getAll();
        foreach ($kelas as $key => $k) {
            foreach ($mapel as $key => $m) {
                $this->get_nilai_rdm($id_semester, $m->id, $k);
            }
        }
    }

    public function download_ledger()
    {
        $id_semester = $this->request->getGetPost('id_semester');
        $id_kelas = $this->request->getGetPost('id_kelas');
        $semester = model('DataSemesterModel')->getData($id_semester);
        $kelas = model('DataKelasAjarModel')->getDataWhere(whereAnd:[
            'id_kelas' => $id_kelas,
            'tahun_ajaran' => $semester->tahun_ajaran ?? ''
        ]);
        $ujian = $this->request->getGetPost('ujian') ?? 'uts';

        $result = array_values($this->rekapitulasi(TRUE));
        
        $filename = APPPATH . '../templates/ledger-mid.xlsx';
        /**  Create a new Reader of the type that has been identified  **/
        $spreadsheet = IOFactorySpreadsheet::load($filename);
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
        switch ($ujian) {
            case 'uts':
                $title = "UJIAN TENGAH SEMESTER";
                break;
            case 'uas':
                $title = "UJIAN AKHIR SEMESTER";
                break;
            case 'nilai_rapor':
                $title = "RAPOR AKHIR SEMESTER";
                break;
            
            default:
                # code...
                break;
        }
        $sheet->setCellValue("A10", "DAFTAR NILAI $title ".strtoupper($semester->semester ?? ''));
        $sheet->setCellValue("A11", "KMI PONDOK PESANTREN MUHAMMADIYAH DARUL ARQAM PATEAN ");
        $sheet->setCellValue("A12", "TAHUN AJARAN ".strtoupper($semester->tahun_ajaran ?? ''));
        reApplyImageInfo($spreadsheet, $images);

        $row = 16;
        // var_dump($kelas);exit;
        $total = "total_$ujian";
        $rata = "rata_$ujian";
        foreach ($result as $key => $santri) {
            duplicateRow($spreadsheet, $row, $row + 1);
            $sheet->setCellValue("A$row", $key + 1);
            $sheet->setCellValue("B$row", $santri->nama);
            $sheet->setCellValue("C$row", $kelas->kelas ?? '');
            
            $santri->mapel = array_values($santri->mapel);
            foreach ($santri->mapel as $key => $mapel) {
               $sheet->setCellValue($cols[$key] . $row, $mapel->$ujian);
            }
            $sheet->setCellValue($cols[$count_mapel] . $row, $santri->$total);
            $sheet->setCellValue($cols[$count_mapel + 1] . $row, $santri->$rata);
            $sheet->setCellValue($cols[$count_mapel + 2] . $row, $santri->_kelas);
            // $sheet->insertNewRowBefore($row + 1);
            $row++;
        }
        for ($i=0; $i < $count_mapel; $i++) { 
            $col = $cols[$i];
            // $sheet->getColumnDimension($col)->setAutoSize(true);
            $sheet->getColumnDimension($col)->setWidth(3.2);
        }
        
        $filename = "LEDGER $title";
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

        $writer = IOFactorySpreadsheet::createWriter($spreadsheet, 'Xlsx');
        // ob_end_clean();
        $writer->save('php://output');
    }
    
    public function download_ledger_akhir()
    {
        helper('rapor');
        $id_semester = $this->request->getGetPost('id_semester');
        $id_kelas = $this->request->getGetPost('id_kelas');
        $semester = model('DataSemesterModel')->getData($id_semester);
        $kelas = model('DataKelasAjarModel')->getDataWhere(whereAnd:[
            'id_kelas' => $id_kelas,
            'tahun_ajaran' => $semester->tahun_ajaran ?? ''
        ]);
        $tingkat = $kelas->tingkat ?? '1';
        $id_jurusan = $kelas->id_jurusan ?? -1;
        if ($tingkat == '3' && $id_semester % 2 == 0)
          $ujians = ['nilai_harian', 'uas', 'nilai_rapor', 'katrol1', 'um', 'katrol2'];
        elseif ($tingkat == '6' && $id_semester % 2 == 0)
          $ujians = ['nilai_harian', 'uas', 'nilai_rapor', 'katrol1'];
        else
          $ujians = ['nilai_harian', 'uts', 'uas','nilai_rapor', 'katrol1'];

        $result = array_values($this->rekapitulasi(TRUE));
        // array_splice($result, 0, 4);
        // return $this->respondCreated($result);
        $filename = APPPATH . '../templates/ledger-akhir.xlsx';
        /**  Create a new Reader of the type that has been identified  **/
        $spreadsheet = IOFactorySpreadsheet::load($filename);
        $sheet = $spreadsheet->setActiveSheetIndex(0);

        $start = 'L';
        $cols = excelColumnRange($start,'AAA');
        $mapels = array_values(reset($result)->mapel);
        $count_mapel = count($mapels);
        $count_ujians = count($ujians);
        $ind_ujians = array_map(function ($key) { return $key; }, $ujians);
        // $count_mapel = 2;
        // var_dump($cols, $count_mapel);
        for ($i=0; $i < $count_mapel; $i++) { 
            $next = $i + 1;
            foreach ($ind_ujians as $key => $value) {
                $ind_ujians[$key] = ($i * $count_ujians) + $key;
                $sheet->setCellValue($cols[$ind_ujians[$key]] ."2", getLabelShort($ujians[$key]));
                $sheet->getColumnDimension($cols[$ind_ujians[$key]])->setWidth(4);
                $sheet->insertNewColumnBefore($cols[$ind_ujians[$key] + 1], 1);
            }
            // if ((($next + 1) < $count_mapel)) {
            //     $_nc = $ind_ujians[($count_ujians - 1)]+1;
            //     $sheet->insertNewColumnBefore($cols[$_nc], $count_ujians);
            // }
            // $sheet->mergeCells($cols[$_1] ."1:".$cols[$_4] ."1");
            $sheet->setCellValue($cols[$ind_ujians[0]] ."1", $mapels[$i]->nama_mapel ?? '');
            for ($j=1; $j <= 50 ; $j++) { 
                foreach ($ind_ujians as $key => $value) {
                    $sheet->getStyle($cols[$ind_ujians[$key]].$j)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                    if (in_array($ujians[$key], ['nilai_harian', 'uts', 'uas']))
                        $sheet->getStyle($cols[$ind_ujians[$key]])->getFill()->getStartColor()->setRGB('33CCCC'); // biru
                    else
                        $sheet->getStyle($cols[$ind_ujians[$key]].$j)->getFill()->getStartColor()->setRGB('AAD08E'); // biru
                }
            }
            $sheet->mergeCells($cols[$ind_ujians[0]] ."1:".$cols[$ind_ujians[($count_ujians - 1)]] ."1");
            // duplicateColumn($spreadsheet, $cols[$i], $cols[$i+1]);
        }

        $last_col = $count_mapel * $count_ujians;
        $sheet->removeColumn($cols[$last_col]);
        $sheet->removeColumn($cols[$last_col]);
        $row = 3;
        // var_dump($kelas);exit;
        foreach ($result as $key => $santri) {
            $sheet->setCellValue("A$row", $key + 1);
            $sheet->setCellValue("B$row", $santri->nama);
            $sheet->setCellValue("C$row", $santri->nama_arab);
            $sheet->setCellValue("D$row", $kelas->kelas ?? '');
            $sheet->setCellValue("E$row", $kelas->kelas_arab ?? '');
            $sheet->setCellValue("F$row", $santri->stb);
            $sheet->setCellValue("G$row", $santri->nama_walas_lengkap);
            $sheet->setCellValue("H$row", $santri->nama_walas_arab);
            $sheet->setCellValue("I$row", $santri->nbm_walas);
            $sheet->setCellValue("J$row", $santri->daerah);
            $sheet->setCellValue("K$row", $santri->daerah_arab);
            
            $santri->mapel = array_values($santri->mapel);
            foreach ($santri->mapel as $k1 => $mapel) {
                foreach ($ind_ujians as $k2 => $value) {
                    $ind_uj = ($k1 * $count_ujians) + $k2;
                    $ind_u = $ujians[$k2];
                    $sheet->setCellValue($cols[$ind_uj] . $row, 
                        $mapel->$ind_u);
                }
            }
            $sakit = $santri->pengasuhan['sakit']->nilai ?? 0;
            $izin = $santri->pengasuhan['izin']->nilai ?? 0;
            $alfa = $santri->pengasuhan['alfa']->nilai ?? 0;
            $sheet->setCellValue($cols[$last_col] . $row, $sakit);
            $sheet->setCellValue($cols[$last_col + 1] . $row, $izin);
            $sheet->setCellValue($cols[$last_col + 2] . $row, $alfa);

            $kelakuan = $santri->pengasuhan['nilai_7']->nilai ?? 0;
            $kebersihan = $santri->pengasuhan['nilai_11']->nilai ?? 0;
            $eskul = $santri->pengasuhan['nilai_15']->nilai ?? 0;
            $sheet->setCellValue($cols[$last_col + 3] . $row, get_sikap_short($kelakuan));
            $sheet->setCellValue($cols[$last_col + 4] . $row, get_sikap_short($kebersihan));
            $sheet->setCellValue($cols[$last_col + 5] . $row, get_sikap_short($kebersihan));
            $sheet->setCellValue($cols[$last_col + 6] . $row, get_sikap_short($eskul));
            $sheet->setCellValue($cols[$last_col + 7] . $row, get_sikap_short($eskul));
            $sheet->setCellValue($cols[$last_col + 8] . $row, get_sikap_short($eskul));
            $sheet->setCellValue($cols[$last_col + 9] . $row, $santri->total_nilai_rapor);
            $sheet->setCellValue($cols[$last_col + 10] . $row, $santri->rata_nilai_rapor);
            $sheet->setCellValue($cols[$last_col + 11] . $row, $santri->ranking_kelas);
            // $sheet->insertNewRowBefore($row + 1);
            $row++;
        }
        
        $filename = "LEDGER NILAI LENGKAP";
        // // exit;
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

        $writer = IOFactorySpreadsheet::createWriter($spreadsheet, 'Xlsx');
        // ob_end_clean();
        $writer->save('php://output');
    }

    public function download_raport()
    {
        $id_semester = $this->request->getGetPost('id_semester');
        $id_kelas = $this->request->getGetPost('id_kelas');
        $id_santri = $this->request->getGetPost('id_santri');
        $tanggal = $this->request->getGetPost('tanggal');
        $download = $this->request->getGetPost('download') == '1';
        $semester = model('DataSemesterModel')->getData($id_semester);
        $kelas = model('DataKelasAjarModel')->getDataWhere(whereAnd:[
            'id_kelas' => $id_kelas,
            'tahun_ajaran' => $semester->tahun_ajaran ?? ''
        ]);
        
        $result = array_values($this->rekapitulasi(TRUE));
        $ujian = $this->request->getGetPost('ujian') ?? 'uts';
        // $result = array_splice($result, 0, 4);

        $santri = [];
        // return $this->respondCreated($result);

        foreach ($result as $key => $value) {
            if ($value->id_santri == $id_santri)
                $santri = $value;
        }

        if (empty($santri))
            return $this->respond([
                'message' => 'Tidak ada data santri',
            ], 401);
        // return $this->respondCreated($santri);

        $templatePath = APPPATH . '../templates/';
        if ($ujian == 'uts'){
            $templatePath .= 'raport-mid.docx';
        } if ($id_semester % 2 == 0) {
            if ($santri->naik_kelas == '0')
                $templatePath .= 'raport-akhir-tidak-naik.docx';
            else
                $templatePath .= 'raport-akhir-kenaikan.docx';
        } else {
            $templatePath .= 'raport-akhir.docx';
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        $templateProcessor->setValue("tahun_ajaran", $semester->tahun_ajaran);
        $templateProcessor->setValue("nama", strtoupper($santri->nama));
        $templateProcessor->setValue("nama_arab", $santri->nama_arab);
        $templateProcessor->setValue("stb", $santri->stb ?? '');
        $templateProcessor->setValue("stb_arab", to_arabic_number($santri->stb) ?? '');
        $templateProcessor->setValue("daerah", $santri->daerah ?? '');
        $templateProcessor->setValue("daerah_arab", $santri->daerah_arab ?? '');
        $templateProcessor->setValue("semester_small", ucfirst($semester->semester));
        $templateProcessor->setValue("semester_small_arab", $semester->semester == 'gasal' ? 'اَلْفَصْلُ الدِّرَاسِيُّ الْأَوَّلُ' : 'اَلْفَصْلُ الدِّرَاسِيُّ الثَّانِي');
        $templateProcessor->setValue("kelas", $kelas->kelas);
        $kelas_part = str_split($kelas->kelas);
        $templateProcessor->setValue("kelas_huruf_arab", class_to_arabic($kelas_part[0]));
        $templateProcessor->setValue("kelas_arab", to_arabic_number($kelas_part[1] ?? ''));
        $templateProcessor->cloneRow('no', count($santri->mapel));

        foreach ($santri->mapel as $index => $mapel) {
            $i = $index + 1;
            $templateProcessor->setValue("no#{$i}", $i);
            $templateProcessor->setValue("mapel#{$i}", $mapel->nama_mapel);
            $templateProcessor->setValue("{$ujian}#{$i}", $mapel->{$ujian});
            $templateProcessor->setValue("{$ujian}_bilangan#{$i}", number_to_words($mapel->{$ujian}));
            $templateProcessor->setValue("no_arab#{$i}", to_arabic_number($i));
            $templateProcessor->setValue("mapel_arab#{$i}", $mapel->nama_mapel_arab ?? $mapel->nama_mapel);
            $templateProcessor->setValue("{$ujian}_arab#{$i}", to_arabic_number($mapel->{$ujian}));
            $templateProcessor->setValue("{$ujian}_bilangan_arab#{$i}", number_to_words_arabic($mapel->{$ujian},true));
        }
        $total_text = "total_{$ujian}";
        $rata_text = "rata_{$ujian}";
        $templateProcessor->setValue("$total_text", $santri->$total_text);
        $templateProcessor->setValue("$rata_text", $santri->$rata_text);
        $templateProcessor->setValue("{$total_text}_arab", to_arabic_number($santri->$total_text));
        $templateProcessor->setValue("{$rata_text}_arab", to_arabic_number($santri->$rata_text));
        $templateProcessor->setValue("tanggal", dateIndo($tanggal ?? date('Y-m-d')));
        $templateProcessor->setValue("tanggal_arab", dateIndoArabic($tanggal ?? date('Y-m-d')));
        $templateProcessor->setValue("predikat", strtoupper(get_predikat($santri->$rata_text)));
        $templateProcessor->setValue("predikat_arab", get_predikat_arab($santri->$rata_text));
        $templateProcessor->setValue("peringkat", $santri->ranking_kelas);
        $templateProcessor->setValue("peringkat_arab", to_arabic_number($santri->ranking_kelas));
        $templateProcessor->setValue("total_santri", count($result));
        $templateProcessor->setValue("total_santri_arab", to_arabic_number(count($result)));
        $naik_tingkat = $kelas->tingkat + 1;
        $templateProcessor->setValue("naik_tingkat", number_to_roman($naik_tingkat) . " (" . number_to_words($naik_tingkat) . ")");
        $templateProcessor->setValue("naik_tingkat_arab", class_to_arabic($naik_tingkat));

        //Kelakuan dan Kepribadian
        $kelakuan = $santri->pengasuhan['nilai_7']->nilai ?? 0;
        $kebersihan = $santri->pengasuhan['nilai_11']->nilai ?? 0;
        $eskul = $santri->pengasuhan['nilai_15']->nilai ?? 0;
        $templateProcessor->setValue("kelakuan", get_sikap($kelakuan));
        $templateProcessor->setValue("kelakuan_arab", get_sikap_arab($kelakuan));
        $templateProcessor->setValue("kebersihan", get_sikap($kebersihan));
        $templateProcessor->setValue("kebersihan_arab", get_sikap_arab($kebersihan));
        $templateProcessor->setValue("kerapihan", get_sikap($kebersihan));
        $templateProcessor->setValue("kerapihan_arab", get_sikap_arab($kebersihan));
        $templateProcessor->setValue("hw", get_sikap($eskul));
        $templateProcessor->setValue("muhadloroh", get_sikap($eskul));
        $templateProcessor->setValue("ts", get_sikap($eskul));
        $templateProcessor->setValue("hw_arab", get_sikap_arab($eskul));
        $templateProcessor->setValue("muhadloroh_arab", get_sikap_arab($eskul));
        $templateProcessor->setValue("ts_arab", get_sikap_arab($eskul));

        //Kehadiran
        $sakit = $santri->pengasuhan['sakit']->nilai ?? 0;
        $templateProcessor->setValue("sakit", $sakit);
        $templateProcessor->setValue("sakit_arab", to_arabic_number($sakit));
        $izin = $santri->pengasuhan['izin']->nilai ?? 0;
        $templateProcessor->setValue("izin", $izin);
        $templateProcessor->setValue("izin_arab", to_arabic_number($izin));
        $alfa = $santri->pengasuhan['alfa']->nilai ?? 0;
        $templateProcessor->setValue("alfa", $alfa);   
        $templateProcessor->setValue("alfa_arab", to_arabic_number($alfa));

        // Wali Kelas
        if (!empty($kelas->walas_signature))
            $templateProcessor->setImageValue('ttd', [
                'path' => $kelas->walas_signature,
                'height' => 80,
                'ratio' => true
            ]);
        else 
            $templateProcessor->setValue("ttd", "");

        $nama_walas = trim(shortenName(ucwords(strtolower($kelas->nama_walas)), 32));
        $nama_walas = trim($kelas->prefix_walas).$nama_walas.trim($kelas->suffix_walas);
        // var_dump($kelas->nama_walas_lengkap, $nama_walas);
        $templateProcessor->setValue("nbm_walas", $kelas->nbm_walas ?? '-');
        $templateProcessor->setValue("nama_walas", $nama_walas ?? '-');
        $templateProcessor->setValue("nama_walas_arab", $kelas->nama_walas_arab ?? '-');

        // Set HTTP headers to force download
        $fileName = "$santri->nama.docx";
        $savePath = WRITEPATH . 'documents/' . $fileName;

        // Make sure directory exists
        if (!is_dir(WRITEPATH . 'documents')) {
            mkdir(WRITEPATH . 'documents', 0777, true);
        }

        // Save the processed file
        $templateProcessor->saveAs($savePath);
        $pdfConverter = new PdfConverter();
        $pdfBinary = $pdfConverter->convertWordToPdf($savePath);
        unlink($savePath);

        if ($download) {
            // // 4. Send headers for the "Save As" dialog
            // header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            // header('Content-Disposition: attachment; filename='.$fileName);
            // header('Cache-Control: max-age=0');
            // header('Content-Length: ' . filesize($savePath));

            // // 5. Output the file content and delete the temporary file
            // readfile($savePath);
            // unlink($savePath); // Cleans up the server
            // exit;
            // 1. Clear output buffers to prevent corruption from whitespace or notices
            if (ob_get_level()) {
                ob_end_clean();
            }

            // 2. Set headers instructing the browser to render a PDF inline
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="document.pdf"');
            header('Content-Length: ' . strlen($pdfBinary));
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // 3. Output the binary raw data and stop execution
            echo $pdfBinary;
            exit;
        } else {    
            $fileName = "$santri->nama.pdf";
            $fileName =  preg_replace('/[^A-Za-z0-9_\-]/', '-',$fileName);
            $savePath = WRITEPATH . 'documents/' . $fileName;
            file_put_contents($savePath, $pdfBinary);
            return $this->respondCreated($savePath);
        }
        

    }
    
    public function download_raport_smk()
    {
        $id_semester = $this->request->getGetPost('id_semester');
        $id_kelas = $this->request->getGetPost('id_kelas');
        $id_santri = $this->request->getGetPost('id_santri');
        $tanggal = $this->request->getGetPost('tanggal');
        $semester = model('DataSemesterModel')->getData($id_semester);
        $kelas = model('DataKelasAjarModel')->getDataWhere(whereAnd:[
        'id_kelas' => $id_kelas,
        'tahun_ajaran' => $semester->tahun_ajaran ?? ''
    ]);
        $download = $this->request->getGetPost('download') == '1';
        
        $result = array_values($this->rekapitulasi(TRUE));
        $ujian = $this->request->getGetPost('ujian') ?? 'uts';
        // $result = array_splice($result, 0, 4);
        $templatePath = APPPATH . '../templates/raport-smk.docx';
        $templateProcessor = new TemplateProcessor($templatePath);

        $santri = [];
        foreach ($result as $key => $value) {
            if ($value->id_santri == $id_santri)
                $santri = $value;
        }

        if (empty($santri))
            return $this->respond([
                'message' => 'Tidak ada data santri',
            ], 401);
        // return $this->respondCreated($santri);
        $templateProcessor->setValue("tahun_ajaran", $semester->tahun_ajaran);
        $templateProcessor->setValue("nama", strtoupper($santri->nama));
        $templateProcessor->setValue("induk_sekolah", $santri->induk_sekolah ?? '');
        $templateProcessor->setValue("nisn", $santri->nisn ?? '');
        $templateProcessor->setValue("semester_small", ucfirst($semester->semester));
        $kelas_smk = model('DataKelasAjarModel')->getAll(whereAnd: [
            '{n}tingkat' => $kelas->tingkat,
            'id_unit' => $kelas->id_unit,
            'id_jurusan' => $kelas->id_jurusan,
            'tahun_ajaran' => $kelas->tahun_ajaran,
        ], order: 'kelas asc');
        $cols = excelColumnRange('A','Z');
        $text_kelas = '';
        foreach ($kelas_smk as $key => $value) {
            if ($value->id == $id_kelas) {
                $text_kelas = $cols[$key];;
                break;
            }
        }
        $jurusan = strlen($kelas->nama_jurusan) > 15 ? $kelas->kode_jurusan : $kelas->nama_jurusan;
        switch ($kelas->tingkat) {
            case '4': 
                $text_kelas = "X-$jurusan-$text_kelas";
                $fase = 'D'; 
                break;
            case '5': 
                $text_kelas = "XI-$jurusan-$text_kelas";
                $fase = 'E'; 
                break;
            case '6': 
                $text_kelas = "XII-$jurusan-$text_kelas";
                $fase = 'F'; 
                break;
            default: $fase = '-'; break;
        }
        $templateProcessor->setValue("kelas", $text_kelas);
        $templateProcessor->setValue("fase", $fase);

        $list_mapels = [];
        foreach ($santri->mapel as $index => $mapel) {
            $list_mapels[$mapel->id_mapel] = $mapel;
        }

        $list_mapel = unserialize(RAPOR_SMK_MAPEL_KET);
        $mapels = $list_mapel[$kelas->id_jurusan][$kelas->tingkat];

        // return $this->respondCreated($mapels);
        $templateProcessor->cloneRow('no', count($mapels['umum']));
        $i = 1;
        $total = 0;
        foreach ($mapels['umum'] as $index => $mapel) {
            $score = $list_mapels[$index] ?? (object)['katrol1' => 80];
            $templateProcessor->setValue("no#{$i}", $i);
            $templateProcessor->setValue("mapel#{$i}", $mapel['label']);
            $templateProcessor->setValue("kompetensi#{$i}", $mapel['kompetensi'] ?? '-');
            $templateProcessor->setValue("nilai_rapor#{$i}", round($score->katrol1));
            $total += $score->katrol1;
            $i++;
        }

        $templateProcessor->cloneRow('no_kejuruan', count($mapels['kejuruan']));
        $i = 1;
        foreach ($mapels['kejuruan'] as $index => $mapel) {
            $score = $list_mapels[$index] ?? (object)['katrol1' => 80];
            $templateProcessor->setValue("no_kejuruan#{$i}", $i);
            $templateProcessor->setValue("mapel_kejuruan#{$i}", $mapel['label']);
            $templateProcessor->setValue("kompetensi_kejuruan#{$i}", $mapel['kompetensi'] ?? '-');
            $templateProcessor->setValue("nilai_rapor_kejuruan#{$i}", round($score->katrol1));
            $total += $score->katrol1;
            $i++;
        }
        $templateProcessor->setValue("total_nilai_rapor", round($total));
        
        //Kelakuan dan Kepribadian
        $kelakuan = $santri->pengasuhan['nilai_7']->nilai ?? 0;
        $kebersihan = $santri->pengasuhan['nilai_11']->nilai ?? 0;
        $eskul = $santri->pengasuhan['nilai_15']->nilai ?? 0;
        $templateProcessor->setValue("kelakuan", get_sikap($kelakuan));
        $templateProcessor->setValue("kelakuan_arab", get_sikap_arab($kelakuan));
        $templateProcessor->setValue("kebersihan", get_sikap($kebersihan));
        $templateProcessor->setValue("hw", get_sikap($eskul));
        $templateProcessor->setValue("muhadloroh", get_sikap($eskul));
        $templateProcessor->setValue("ts", get_sikap($eskul));
        $templateProcessor->setValue("hw_aktif", get_sikap_aktif($eskul));
        $templateProcessor->setValue("muhadloroh_aktif", get_sikap_aktif($eskul));
        $templateProcessor->setValue("ts_aktif", get_sikap_aktif($eskul));
        //Kehadiran
        $sakit = $santri->pengasuhan['sakit']->nilai ?? 0;
        $sakit = $sakit > 3 ? 3 : $sakit;
        $templateProcessor->setValue("sakit", $sakit);
        $templateProcessor->setValue("sakit_arab", to_arabic_number($sakit));
        $izin = $santri->pengasuhan['izin']->nilai ?? 0;
        $izin = $izin > 3 ? 3 : $izin;
        $templateProcessor->setValue("izin", $izin);
        $templateProcessor->setValue("izin_arab", to_arabic_number($izin));
        $alfa = $santri->pengasuhan['alfa']->nilai ?? 0;
        $alfa = $alfa > 3 ? 3 : $alfa;
        $templateProcessor->setValue("alfa", $alfa);   
        $templateProcessor->setValue("alfa_arab", to_arabic_number($alfa));

        $templateProcessor->setValue("catatan", $santri->catatan);   
        $templateProcessor->setValue("kokurikuler", empty($santri->korikuler) ? 'Pada semester ini, ananda menunjukkan capaian yang cukup baik dalam penguatan profil lulusan, yang ditunjukkan melalui kegiatan kokurikuler Gerakan olah raga pagi dan sholat dhuha berjamaah. Pada dimensi keimanan dan ketaqwaan terhadap Allah SWT, ananda berkembang dalam subdimensi hubungan dengan Tuhan yang Maha Esa. Pada dimensi kemandirian, ananda berkembang dalam subdimensi bertanggung jawab. Pada dimensi kesehatan, ananda cakap dalam subdimensi kebugaran, kesehatan fisik, dan kesehatan mental.' : $santri->korikuler);   
        $templateProcessor->setValue("tanggal", dateIndo($tanggal ?? date('Y-m-d')));

        // Wali Kelas
        if (!empty($kelas->walas_signature))
            $templateProcessor->setImageValue('ttd', [
                'path' => $kelas->walas_signature,
                'height' => 80,
                'ratio' => true
            ]);
        else 
            $templateProcessor->setValue("ttd", "");

        $nama_walas = trim(shortenName(ucwords(strtolower($kelas->nama_walas)), 32));
        $nama_walas = trim($kelas->prefix_walas).$nama_walas.trim($kelas->suffix_walas);
        // var_dump($kelas->nama_walas_lengkap, $nama_walas);
        $templateProcessor->setValue("nbm_walas", $kelas->nbm_walas ?? '-');
        $templateProcessor->setValue("nama_walas", $nama_walas ?? '-');
        $templateProcessor->setValue("nama_walas_arab", $kelas->nama_walas_arab ?? '-');
        // $templateProcessor->setValue("tanggal", dateIndo('2025-12-19'));

        // Set HTTP headers to force download
        $fileName = "$santri->nama-SMK.docx";
        $savePath = WRITEPATH . 'documents/' . $fileName;

        // Make sure directory exists
        if (!is_dir(WRITEPATH . 'documents')) {
            mkdir(WRITEPATH . 'documents', 0777, true);
        }

        // Save the processed file
        $templateProcessor->saveAs($savePath);
        $pdfConverter = new PdfConverter();
        $pdfBinary = $pdfConverter->convertWordToPdf($savePath);
        unlink($savePath);

        if ($download) {
            // // 4. Send headers for the "Save As" dialog
            // header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            // header('Content-Disposition: attachment; filename='.$fileName);
            // header('Cache-Control: max-age=0');
            // header('Content-Length: ' . filesize($savePath));

            // // 5. Output the file content and delete the temporary file
            // readfile($savePath);
            // unlink($savePath); // Cleans up the server
            // exit;
            // 1. Clear output buffers to prevent corruption from whitespace or notices
            if (ob_get_level()) {
                ob_end_clean();
            }

            // 2. Set headers instructing the browser to render a PDF inline
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="document.pdf"');
            header('Content-Length: ' . strlen($pdfBinary));
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // 3. Output the binary raw data and stop execution
            echo $pdfBinary;
            exit;
        } else {    
            $fileName =  preg_replace('/[^A-Za-z0-9_\-]/', '-',$santri->nama).".pdf";
            $savePath = WRITEPATH . 'documents/' . $fileName;
            file_put_contents($savePath, $pdfBinary);
            return $this->respondCreated($savePath);
        }

    }

    public function download_raport_pengasuhan()
    {
        while (ob_get_level() > 0) { ob_end_clean(); }

        $id_semester = $this->request->getGetPost('id_semester');
        $id_kelas = $this->request->getGetPost('id_kelas');
        $semester = model('DataSemesterModel')->getData($id_semester);
        $tanggal = $this->request->getGetPost('tanggal');
        $id_santri = $this->request->getGetPost('id_santri');
        $download = $this->request->getGetPost('download') == '1';
        $kelas = model('DataKelasAjarModel')->getDataWhere(whereAnd:[
            'id_kelas' => $id_kelas,
            'tahun_ajaran' => $semester->tahun_ajaran ?? ''
        ]);

        $result = array_values($this->rekapitulasi(TRUE));
        
        $templatePath = APPPATH . '../templates/raport-pengasuhan.docx';

        $templateProcessor = new TemplateProcessor($templatePath);

        $santri = [];
        foreach ($result as $key => $value) {
            if ($value->id_santri == $id_santri)
                $santri = $value;
        }

        if (empty($santri))
            return $this->respond([
                'message' => 'Tidak ada data santri',
            ], 401);
        
        // return $this->respondCreated($santri);
        $templateProcessor->setValue("tahun_ajaran", $semester->tahun_ajaran);
        $templateProcessor->setValue("nama", strtoupper($santri->nama));
        $templateProcessor->setValue("nama_arab", $santri->nama_arab);
        $templateProcessor->setValue("stb", $santri->stb ?? '');
        $templateProcessor->setValue("stb_arab", to_arabic_number($santri->stb) ?? '');
        $templateProcessor->setValue("kamar", $santri->kamar ?? '');
        $templateProcessor->setValue("kamar_arab", $santri->kamar_arab ?? '');
        $templateProcessor->setValue("semester_small", ucfirst($semester->semester));
        $templateProcessor->setValue("semester_small_arab", $semester->semester == 'gasal' ? 'اَلْفَصْلُ الدِّرَاسِيُّ الْأَوَّلُ' : 'اَلْفَصْلُ الدِّرَاسِيُّ الثَّانِي');
        $templateProcessor->setValue("kelas", $santri->kelas);
        $kelas_part = str_split($santri->kelas);
        $templateProcessor->setValue("kelas_huruf_arab", class_to_arabic($kelas_part[0]));
        $templateProcessor->setValue("kelas_arab", to_arabic_number($kelas_part[1] ?? ''));

        for ($i=1; $i < 18; $i++) { 
            $index = "nilai_$i";
            $nilai = $santri->pengasuhan[$index]->nilai ?? 0;
            $templateProcessor->setValue($index, get_sikap_short($nilai));
            $templateProcessor->setValue($index."_arab", get_sikap_arab($nilai));
        }
        $templateProcessor->setValue("tanggal", dateIndo($tanggal ?? date('Y-m-d')));
        $templateProcessor->setValue("tanggal_arab", dateIndoArabic($tanggal ?? date('Y-m-d')));

        //Kehadiran
        $sakit = $santri->pengasuhan['sakit']->nilai ?? 0;
        $templateProcessor->setValue("sakit", $sakit);
        $templateProcessor->setValue("sakit_arab", to_arabic_number($sakit));
        $izin = $santri->pengasuhan['izin']->nilai ?? 0;
        $templateProcessor->setValue("izin", $izin);
        $templateProcessor->setValue("izin_arab", to_arabic_number($izin));
        $alfa = $santri->pengasuhan['alfa']->nilai ?? 0;
        $templateProcessor->setValue("alfa", $alfa);   
        $templateProcessor->setValue("alfa_arab", to_arabic_number($alfa));

        // Wali Kamar
        // $santri->wamar_signature = model('DataGuruModel')->getData(1)->signature ?? '';
        if (!empty($santri->wamar_signature)) {
            parse_str(parse_url($santri->wamar_signature, PHP_URL_QUERY), $params);
            $relative = $params['file'] ?? '';
            $relative = urldecode($relative);
            $filepath = WRITEPATH. $relative;
            // var_dump($filepath);exit;
            if (file_exists($filepath) && is_readable($filepath)) {
                $templateProcessor->setImageValue('ttd', [
                    'path' => $filepath,
                    'height' => 80,
                    'ratio' => true
                ]);
            } else {
                $templateProcessor->setValue("ttd", "");
            }
        }
        else 
            $templateProcessor->setValue("ttd", "");

        $templateProcessor->setValue("nbm_wamar", $santri->nbm_wamar ?? '-');
        $templateProcessor->setValue("nama_wamar", $santri->nama_wamar_lengkap ?? '-');
        $templateProcessor->setValue("nama_wamar_arab", $santri->nama_wamar_arab ?? '-');

        // Set HTTP headers to force download
        $fileName = "$santri->nama-PENGASUHAN.docx";
        $fileName =  preg_replace('/[^A-Za-z0-9_\-]/', '-',$fileName);
        $savePath = WRITEPATH . "documents/$fileName.docx";

        // Make sure directory exists
        if (!is_dir(WRITEPATH . 'documents')) {
            mkdir(WRITEPATH . 'documents', 0777, true);
        }

        // @ob_end_clean();
        // ini_set('output_buffering', 'off');
        // ini_set('zlib.output_compression', '0');
        // Save the processed file
        
        // Save the processed file
        $templateProcessor->saveAs($savePath);
        $pdfConverter = new PdfConverter();
        $pdfBinary = $pdfConverter->convertWordToPdf($savePath);
        unlink($savePath);

        if ($download) {
            // // 4. Send headers for the "Save As" dialog
            // header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            // header('Content-Disposition: attachment; filename='.$fileName);
            // header('Cache-Control: max-age=0');
            // header('Content-Length: ' . filesize($savePath));

            // // 5. Output the file content and delete the temporary file
            // readfile($savePath);
            // unlink($savePath); // Cleans up the server
            // exit;
            // 1. Clear output buffers to prevent corruption from whitespace or notices
            if (ob_get_level()) {
                ob_end_clean();
            }

            // 2. Set headers instructing the browser to render a PDF inline
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="document.pdf"');
            header('Content-Length: ' . strlen($pdfBinary));
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');

            // 3. Output the binary raw data and stop execution
            echo $pdfBinary;
            exit;
        } else {    
            $fileName =  preg_replace('/[^A-Za-z0-9_\-]/', '-',$santri->nama)."-PENGASUHAN.pdf";
            $savePath = WRITEPATH . 'documents/' . $fileName;
            file_put_contents($savePath, $pdfBinary);
            return $this->respondCreated($savePath);
        }

    }
}