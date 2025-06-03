<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Rekapitulasi extends BaseController
{
    public function __construct()
    {
        $this->financeModel = model('FinanceModel');
    }

    public function index($is_data = FALSE)
    {
        $start = $this->request->getGet('start');
        $end = $this->request->getGet('end');
        $jenis = $this->request->getGet('jenis');
        $group = $this->request->getGet('group') == 'kelas';

        $column = [];
        $total = (object)[
            'total' => 0,
            'total_pemasukan' => 0,
            'total_pengeluaran' => 0,
            'saldo' => 0,
            'column' => [],
        ];
        if ($jenis == 'monthrange') {
            $start = $tmp_start = date('Y-m-01',strtotime($start));
            $end = date('Y-m-t',strtotime($end));
            do {
                $column[] = (object)[
                    'text' => date('F Y', strtotime($tmp_start)),
                    'date_start' => $tmp_start,
                    'date_end' => date('Y-m-t',strtotime($tmp_start)),
                    'pengeluaran' => 0,
                    'pemasukan' => 0,
                ];
                $total->column[] = (object)[
                    'pengeluaran' => 0,
                    'pemasukan' => 0
                ];
                $tmp_start = date('Y-m-01', strtotime($tmp_start." +1 month"));
            } while ($tmp_start <= $end);
        } else {
            $tmp_start = $start;
            do {
                $column[] = (object)[
                    'text' => date('j M y', strtotime($tmp_start)),
                    'date_start' => $tmp_start,
                    'date_end' => $tmp_start,
                    'pengeluaran' => 0,
                    'pemasukan' => 0,
                ];
                $total->column[] = (object)[
                    'pengeluaran' => 0,
                    'pemasukan' => 0
                ];
                $tmp_start = date('Y-m-d', strtotime($tmp_start." +1 day"));
            } while ($tmp_start <= $end);
        }

        $data = $this->financeModel->getCount($start, $end);
        $saldo = $this->financeModel->getSaldo($start);
        $result = [];
        foreach ($saldo as $key => $d) {
            $ind = $group ? $d->kelas : $d->id_santri;
            if (empty($result[$ind])) {
                $santri = new \StdClass;
                if ($group) {
                    $santri->id_santri = '';
                    $santri->kelas = $d->kelas;
                    $santri->text = $d->kelas;
                } else {
                    $santri->id_santri = $d->id_santri;
                    $santri->kelas = '';
                    $santri->text = "$d->kelas - $d->nama";
                }
                $santri->column = array_map(function ($object) { return clone $object; }, $column);
                $santri->total = 0;
                $santri->total_pemasukan = 0;
                $santri->total_pengeluaran = 0;
                $santri->saldo = 0;
                $result[$ind] = $santri;
            }

            $jumlah = $d->jenis * $d->jumlah;
            $result[$ind]->saldo += $jumlah;
            $result[$ind]->total += $jumlah;
            $total->saldo += $jumlah;
            $total->total += $jumlah;
        }
        // echo json_encode([$result]);exit;
        foreach ($data as $key => $d) {
            $ind = $group ? $d->kelas : $d->id_santri;
            if (empty($result[$ind])) {
                $santri = new \StdClass;
                if ($group) {
                    $santri->id_santri = '';
                    $santri->kelas = $d->kelas;
                    $santri->text = $d->kelas;
                } else {
                    $santri->id_santri = $d->id_santri;
                    $santri->kelas = '';
                    $santri->text = "$d->kelas - $d->nama";
                }
                $santri->column = array_map(function ($object) { return clone $object; }, $column);
                $santri->total = 0;
                $santri->total_pemasukan = 0;
                $santri->total_pengeluaran = 0;
                $santri->saldo = 0;
                $result[$ind] = $santri;
            }

            foreach ($result[$ind]->column as $key => $col) {
                if ($d->tanggal >= $col->date_start && $d->tanggal <= $col->date_end) {
                    if ($d->jenis == -1) {
                        $col->pengeluaran -= $d->jumlah;
                        $result[$ind]->total -= $d->jumlah;
                        $result[$ind]->total_pengeluaran -= $d->jumlah;
                        $total->column[$key]->pengeluaran -= $d->jumlah;
                        $total->total -= $d->jumlah;
                        $total->total_pengeluaran -= $d->jumlah;
                    }
                    else {
                        $col->pemasukan += $d->jumlah;
                        $result[$ind]->total += $d->jumlah;
                        $result[$ind]->total_pemasukan += $d->jumlah;
                        $total->column[$key]->pemasukan += $d->jumlah;
                        $total->total += $d->jumlah;
                        $total->total_pemasukan += $d->jumlah;
                    }
                }
            }
        }
        $result = array_values($result);
        $return = ['data' => $result, 'total' => $total];
        if ($is_data)
            return $return;

        return $this->respondCreated($return);
    }


    public function download()
    {
        $filename = 'REKAPITULASI-DATA-KEUANGAN';
        // var_dump($filename);exit;
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
                    ->setCreator('Codev-App')
                    ->setTitle('Finance App');
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $data = $this->index(TRUE);
        $rows = excelColumnRange('D','ZZ');
        // echo json_encode($data);exit();
        $spreadsheet->getActiveSheet()->setCellValue('A1', 'No');
        $spreadsheet->getActiveSheet()->setCellValue('B1', 'Nama');
        $spreadsheet->getActiveSheet()->setCellValue('C1', 'Saldo');
        $ind = 0;
        foreach ($data['data'][0]->column as $key => $col) {
            $spreadsheet->getActiveSheet()->setCellValue("$rows[$ind]1", $col->text);
            $spreadsheet->getActiveSheet()->setCellValue("$rows[$ind]2", 'Pemasukan');
            $ind++;
            $spreadsheet->getActiveSheet()->setCellValue("$rows[$ind]2", 'Pengeluaran');
            $ind++;
        }
        $spreadsheet->getActiveSheet()->setCellValue("$rows[$ind]1", 'Total');

        $num = 3;
        foreach ($data['data'] as $d) {
            $spreadsheet->getActiveSheet()->setCellValue("A$num", ($num - 2));
            $spreadsheet->getActiveSheet()->setCellValue("B$num", $d->text);
            $spreadsheet->getActiveSheet()->setCellValue("C$num", $d->saldo);
            $ind = 0;
            foreach ($d->column as $key => $col) {
                $spreadsheet->getActiveSheet()->setCellValue("$rows[$ind]$num", $col->pemasukan);
                $ind++;
                $spreadsheet->getActiveSheet()->setCellValue("$rows[$ind]$num", $col->pengeluaran);
                $ind++;
            }
            $spreadsheet->getActiveSheet()->setCellValue("$rows[$ind]$num", $d->total);
            $num++;
        }

        $spreadsheet->getActiveSheet()->setCellValue("B$num", 'Total');
        $spreadsheet->getActiveSheet()->setCellValue("C$num", $data['total']->saldo);
        $ind = 0;
        foreach ($data['total']->column as $key => $col) {
            $spreadsheet->getActiveSheet()->setCellValue("$rows[$ind]$num", $col->pemasukan);
            $ind++;
            $spreadsheet->getActiveSheet()->setCellValue("$rows[$ind]$num", $col->pengeluaran);
            $ind++;
        }
        $spreadsheet->getActiveSheet()->setCellValue("$rows[$ind]$num", $data['total']->total);

        for ($i = 'A'; $i !=  $spreadsheet->getActiveSheet()->getHighestColumn(); $i++) {
            $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
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
}   