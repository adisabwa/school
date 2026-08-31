<?php

namespace Modules\Kmi\Controllers\Admin;

use App\Controllers\BaseDataController;
use App\Libraries\PdfBuilder;

class KaldikController extends BaseDataController
{
    public $santriModel;

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('KaldikModel');
    }

    private function getListDate($start, $end)
    {
        $datas = [];
        do {
            $datas[$start] = [
                'date' => $start,
                'tanggal' => date('j', strtotime($start)),
                'nama_hari' => date('l', strtotime($start)),
                'short_hari' => date('D', strtotime($start)),                
            ];
            $start = date('Y-m-d', strtotime('+1 day', strtotime($start)));
        } while (strtotime($start) <= strtotime($end));
        return $datas;
    }

    public function download_kalender()
    {
        $id_semester = $this->request->getPostGet('where')['id_semester'] ?? -1;
        $id_unit = $this->request->getPostGet('where')['id_unit'] ?? -1;
        $return_data = $this->request->getPostGet('data') == '1';
        $breaks = $this->request->getPostGet('page-breaks') ?? [];
        // var_dump($id_semester);
        $semester = model('DataSemesterModel')->getData($id_semester);
        $unit = model('DataUnitModel')->getData($id_unit);
        $datas = $this->getListDate($semester->tanggal_mulai, $semester->tanggal_selesai);
        
        // var_dump($datas);exit;
        $data = $this->index(TRUE);
        // var_dump($this->model->getLastQuery());exit;
        $keterangan = [];
        foreach ($data as $key => $value) {
            $start = $value->tanggal_mulai;
            $end = $value->tanggal_selesai;
            $tmp_start = $start;
            do {                
                // var_dump('Kefiatan', $value->tanggal_mulai, $start);
                // var_dump($tmp_start, $value->id);
                if (!isset($datas[$tmp_start])) {
                    $addData = $this->getListDate(date('Y-m-01', strtotime($tmp_start)), $tmp_start);
                    $datas = $datas + $addData;
                    ksort($datas);
                }
                if (empty($datas[$tmp_start]['id'])) {
                    $datas[$tmp_start]['color'] = [];
                    $datas[$tmp_start]['id'] = [];
                }
                if (!in_array($value->id, $datas[$tmp_start]['id'])) {
                    $datas[$tmp_start]['color'][] = $value->color;
                    $datas[$tmp_start]['id'][] = $value->id;
                } 
                $datas[$tmp_start]['shape'] = $value->shape;
                $tmp_start = date('Y-m-d', strtotime('+1 day', strtotime($tmp_start)));
            } while (strtotime($tmp_start) <= strtotime($end));

            // return $this->respondCreated([
            //     'datas' => $datas,
            // ]);
            $bulan = date('Y-m', strtotime($start));
            $bulanEnd = date('Y-m', strtotime($end));
            $tmp_bulan = $bulan;
            do {                
                // var_dump('Kefiatan', $value->tanggal_mulai, $start);
                $keterangan[$tmp_bulan][$value->id] = unserialize(serialize($value));
                $keterangan[$tmp_bulan][$value->id]->colorHover = '';
                if ($tmp_bulan > $bulan) {
                    $keterangan[$tmp_bulan][$value->id]->tanggal_mulai = date('Y-m-01', strtotime($tmp_bulan));
                } 
                if ($tmp_bulan != $bulanEnd){
                    $keterangan[$tmp_bulan][$value->id]->tanggal_selesai = date('Y-m-t', strtotime($tmp_bulan));
                }
                $tmp_bulan = date('Y-m', strtotime('+1 month', strtotime($tmp_bulan)));
            } while (strtotime($tmp_bulan) <= strtotime($bulanEnd));

            // $bulan = date('Y-m', strtotime($start));
            // $bulanEnd = date('Y-m', strtotime($end));
            // $keterangan[$bulan][$value->id] = $value;
            // $keterangan[$bulan][$value->id]->colorHover = '';
            // if ($bulan != $bulanEnd) {
            //     $keterangan[$bulanEnd][$value->id] = unserialize(serialize($value));
            //     $keterangan[$bulan][$value->id]->tanggal_selesai = date('Y-m-t', strtotime($start));
            //     $keterangan[$bulanEnd][$value->id]->tanggal_mulai = date('Y-m-01', strtotime($end));
            // }
        }
        $bulans = [];
        $month = -1;
        $weekOfMonth = 1;
        $dayOfWeek = -1;
        foreach ($datas as $key => $value) {
            $bulan = date('Y-m', strtotime($key));
            $dayOfWeek = date('N', strtotime($key));
            if ($bulan > $month) {
                $month = $bulan;
                $weekOfMonth = 1;
            }
            $bulans[$bulan][$weekOfMonth][$dayOfWeek] = $value;
            // var_dump($dayOfWeek, $weekOfMonth);
            if ($dayOfWeek == 7) {
                $weekOfMonth = $weekOfMonth + 1;
            }
        }

        // return $this->respondCreated([
        //     'bulans' => $bulans,
        //     'keterangan' => $keterangan,
        // ]);
        if ($return_data) {
            return $this->respondCreated([
                'bulans' => $bulans,
                'keterangan' => $keterangan,
                'semester' => $semester,
                'unit' => $unit,
                'breaks' => $breaks,
            ]);
        }
        $html = view('kmi/kaldik', [
            'bulans' => $bulans,
            'keterangan' => $keterangan,
            'semester' => $semester,
            'unit' => $unit,
            'breaks' => $breaks,
        ]);
            // echo $html; // For debugging, remove in production
            // return;
        $pdf = new PdfBuilder();
        $pdf->generatePdf($html, TRUE);
    }


}