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

    public function download_kalender()
    {
        $id_semester = $this->request->getPostGet('where')['id_semester'] ?? -1;
        var_dump($id_semester);
        $semester = model('DataSemesterModel')->find($id_semester);
        $datas = [];
        $start = $semester->tanggal_mulai;
        do {
            $datas[$start] = [
                'date' => $start,
                'tanggal' => date('j', strtotime($start)),
                'nama_hari' => date('l', strtotime($start)),
                'short_hari' => date('D', strtotime($start)),                
            ];
            $start = date('Y-m-d', strtotime('+1 day', strtotime($start)));
        } while (strtotime($start) <= strtotime($semester->tanggal_selesai));
        // var_dump($datas);exit;
        $data = $this->index(TRUE);
        $keterangan = [];
        foreach ($data as $key => $value) {
            $start = $value->tanggal_mulai;
            $end = $value->tanggal_selesai;
            $tmp_start = $start;
            do {                
                // var_dump('Kefiatan', $value->tanggal_mulai, $start);
                $datas[$tmp_start]['color'] = $value->color;
                $datas[$tmp_start]['shape'] = $value->shape;
                $tmp_start = date('Y-m-d', strtotime('+1 day', strtotime($tmp_start)));
            } while (strtotime($tmp_start) <= strtotime($end));
            
            $bulan = date('Y-m', strtotime($start));
            $bulanEnd = date('Y-m', strtotime($end));
            $keterangan[$bulan][$value->id] = $value;
            if ($bulan != $bulanEnd) {
                $keterangan[$bulanEnd][$value->id] = unserialize(serialize($value));
                $keterangan[$bulan][$value->id]->tanggal_selesai = date('Y-m-t', strtotime($start));
                $keterangan[$bulanEnd][$value->id]->tanggal_mulai = date('Y-m-01', strtotime($end));
            }
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
        $html = view('kmi/kaldik', [
            'bulans' => $bulans,
            'keterangan' => $keterangan,
            'semester' => $semester,
        ]);
        // echo $html; // For debugging, remove in production
        // return;
        $pdf = new PdfBuilder();
        $pdf->generatePdf($html, TRUE);
    }


}