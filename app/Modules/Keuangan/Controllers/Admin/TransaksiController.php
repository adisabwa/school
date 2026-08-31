<?php

namespace Modules\Keuangan\Controllers\Admin;

use App\Controllers\BaseDataController;
use App\Libraries\PdfBuilder;
use PhpOffice\PhpSpreadsheet\IOFactory as IOFactorySpreadsheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory as IOFactoryWord;

class TransaksiController extends BaseDataController
{

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('Modules\Keuangan\Models\TransaksiModel');

    }

    public function summary($return_data = FALSE)
    {
        $where = $this->request->getGetPost() ?? [];
        $start_date = $where['start_date'] ?? date('Y-m-d');
        $end_date = $where['end_date'] ?? date('Y-m-d');
        $month = $where['selected_month'] ?? date('m');
        $year = $where['selected_year'] ?? date('Y');
        $period_type = $where['period_type'] ?? 'monthly';
        if ($period_type == 'monthly') {
            $start_date = $year . '-' . $month . '-01';
            $end_date = $year . '-' . $month . '-31';
        } else if ($period_type == 'yearly') {
            $start_date = $year . '-01-01';
            $end_date = $year . '-12-31';
        }

        $dimension = $where['dimension'] ?? 'id';
        $subtotal_group = $where['subtotal_group'] ?? 'id';
        $select_plus = "";
        $label_default = "";

        switch ($dimension) {
            case 'pos':
                $var_id = 'id_pos_report';
                $getLabel = function($val) {
                    return $val->nama_pos_report." ( ".ucfirst($val->jenis)." )";
                };
                $sortData = function($a, $b) {
                    if ($a->jenis == $b->jenis) {
                        return $a->label > $b->label;
                    }
                    return $a->jenis > $b->jenis;
                };
                $var_nominal = 'nominal_pos_report';
                $select_plus = ", 
                    COALESCE(sub.id_pos, sub.id_pos_iuran) id_pos_report,
                    COALESCE(sub.nama_pos, sub.nama_pos_iuran) nama_pos_report,
                    COALESCE(sub.keterangan_pos, sub.keterangan_pos_iuran) keterangan_pos_report,
                    COALESCE(sub.nominal_iuran, sub.nominal_alokasi) nominal_pos_report";
                $this->model->relations['id_transaksi'] = [
                    'model' => 'Modules\Keuangan\Models\IuranSantriModel',
                    'alias' => 'iuran_santri',
                    'on_condition' => [
                        "{f}id_transaksi=sch_keu_transaksi.id"
                    ],
                    'type' => 'left',
                    'selects' => [
                        "id_iuran",
                        "nama_iuran",
                        "is_tunggakan",
                        "tipe",
                        "sasaran",
                        "nominal nominal_iuran",
                        "id_pos id_pos_iuran",
                        "nama_pos nama_pos_iuran",
                        "keterangan_pos keterangan_pos_iuran",
                    ],
                    // 'group_by' => ['sch_keu_iuran.id_pos'],
                ];
                break;

            case 'kas':
                $var_id = 'id_kas';
                $var_label = 'nama_kas';
                $var_nominal = 'nominal_disetor';
                $getLabel = function($val) {
                    return $val->nama_kas;
                };
                $sortData = function($a, $b) {
                    return $a->label > $b->label;
                };
                break;

            case 'santri':
                $var_id = 'id_santri';
                $var_label = 'nama';
                $var_nominal = 'nominal_disetor';
                $label_default = 'Transaksi Umum';
                $getLabel = function($val) {
                    return $val->id_santri ? "$val->nama ( ".($val->kelas ?? 'Tidak Aktif')." )" : 'Transaksi Umum';
                };
                $sortData = function($a, $b) {
                    if ($a->id == NULL) return 1;
                    return $a->label > $b->label;
                };
                break;
            
            default:
                $var_id = 'id_pos_report';
                $var_label = 'nama_pos_report';
                $var_nominal = 'nominal_pos_report';
                $getLabel = function($val) { return ''; };
                $sortData = function($a, $b) { return 1; };            # code...
                break;
        }
        // var_dump($dimension, $groupBy);
        $addWhere = $where['where'] ?? [];
        $subquery = $this->model->getAll(whereAnd: [...$addWhere,...[
            // 'tanggal >=' => $start_date,
            'tanggal <=' => $end_date,
        ]], return_data: TRUE);

        // var_dump($subquery);
        $summary = \Config\Database::connect()->table("({$subquery}) AS sub");
        $summary = $summary->select("* $select_plus")
            ->get()->getResult();
        
            // "COUNT({f}id) jumlah_transaksi",
            // "{n}COALESCE(id_pos, id_pos_iuran) id_pos",
            // "{n}COALESCE(nama_pos, nama_pos_iuran) nama_pos",
            // "{n}COALESCE(keterangan_pos, keterangan_pos_iuran) keterangan_pos",
            // "{n}COALESCE(nominal_iuran, nominal_alokasi) nominal_pos",
        
        $results = [];
        switch($subtotal_group) {
            case 'daily':
                $getInd = function($val) { return $val->tanggal; };
                $getLabelGroup = function($val) { return dateIndo($val->tanggal); };
                break;
            case 'monthly':
                $getInd = function($val) { return substr($val->tanggal, 5, 2); };
                $getLabelGroup = function($val) { return formatTanggalIndonesia($val->tanggal, false, 'MMMM yyyy'); };
                break;
            default:
                $getInd = function($val) { return '1'; };
                $getLabelGroup = function($val) { return NULL; };
                break;
        }

        foreach ($summary as $key => $value) {
            $find = $value->tanggal < $start_date ? 'before' : $getInd($value);
            if (empty($results[$find])) {
                $results[$find] = (object)[
                    'id' => $find,
                    'label' => $getLabelGroup($value),
                    'datas' => [],
                    'jml_transaksi' => 0,
                    'nominal_masuk' => '0',
                    'nominal_keluar' => '0',
                    'saldo' => '0',
                ];
            }

            $ind = $value->$var_id;
            if (empty($results[$find]->datas  [$ind])) {
                $results[$find]->datas[$ind] = (object)[
                    'id' => $value->$var_id,
                    'label' => $getLabel($value) ?? '',
                    'jenis' => $value->jenis,
                    'jml_transaksi' => 0,
                    'nominal_masuk' => '0',
                    'nominal_keluar' => '0',
                    'saldo' => '0',
                    'transaksis' => [],
                ];
            }
            
            $ind_transaksi = $value->id;
            $nominal_masuk =  $value->jenis == 'pengeluaran' ? 0 : $value->$var_nominal;
            $nominal_keluar =  $value->jenis == 'pengeluaran' ? $value->$var_nominal : 0;
            $saldo = $nominal_masuk - $nominal_keluar;


            if (empty($results[$find]->datas  [$ind]->transaksis[$ind_transaksi])) {
                $results[$find]->datas[$ind]->transaksis[$ind_transaksi] = (object)[
                    'id' => $value->id,
                    'tanggal' => $value->tanggal,
                    'keterangan' => $value->keterangan,
                    'nominal_masuk' => $nominal_masuk,
                    'nominal_keluar' => $nominal_keluar,
                    'id_iuran' => [$value->id_iuran ?? NULL],
                    'nominal_masuk_array' => [$nominal_masuk],
                    'nominal_keluar_array' => [$nominal_keluar],
                    'saldo' => $saldo,
                    'jenis' => $value->jenis,
                ];
            } else {
                $results[$find]->datas[$ind]->transaksis[$ind_transaksi]->nominal_masuk += $nominal_masuk;
                $results[$find]->datas[$ind]->transaksis[$ind_transaksi]->nominal_keluar += $nominal_keluar;
                $results[$find]->datas[$ind]->transaksis[$ind_transaksi]->id_iuran[] = $value->id_iuran ?? NULL;
                $results[$find]->datas[$ind]->transaksis[$ind_transaksi]->nominal_iuran[] = $value->id_iuran ?? NULL;
                $results[$find]->datas[$ind]->transaksis[$ind_transaksi]->nominal_masuk_array[] = $nominal_masuk;
                $results[$find]->datas[$ind]->transaksis[$ind_transaksi]->nominal_keluar_array[] = $nominal_keluar;
                $results[$find]->datas[$ind]->transaksis[$ind_transaksi]->saldo += $saldo;

            }

            $results[$find]->datas[$ind]->jml_transaksi++;
            $results[$find]->datas[$ind]->nominal_masuk += $nominal_masuk;
            $results[$find]->datas[$ind]->nominal_keluar += $nominal_keluar;
            $results[$find]->datas[$ind]->saldo += $saldo;
            $results[$find]->jml_transaksi++;
            $results[$find]->nominal_masuk += $nominal_masuk;
            $results[$find]->nominal_keluar += $nominal_keluar;
            $results[$find]->saldo += $saldo;

        }
        
        foreach ($results as $key => $value) {
            usort($value->datas, $sortData);
        }
        
        if ($return_data)
            return $results;
    
        return $this->respondCreated(array_values($results));
    }

    public function dynamicTitle() {
        $post = $this->request->getGetPost();
        $dimensionLabels = [
            'santri' => 'Per Santri',
            'pos' => 'Per POS',
            'cash' => 'Per Kas',
        ];

        $periodLabels = [
            'daily' => 'Harian',
            'monthly' => 'Bulanan',
            'yearly' => 'Tahunan',
        ];

        $title = "Laporan ".$dimensionLabels[$post['dimension']] ?? ''.$periodLabels[$post['period_type']] ?? '';

        if ($post['period_type'] === 'monthly') {
            $title .= $post['selected_month'] === 'ALL' ? ' ' : (' '.formatTanggalIndonesia($post['selected_year'].'-'.$post['selected_month'].'-01', false, 'MMMM yyyy'));
        } else if ($post['period_type'] === 'yearly') {
            $title .= " ".$post['selected_year'];
        }
        return $title;
    }
    
    public function download()
    {
        $post = $this->request->getGetPost();
        
        $result = array_values($this->summary(TRUE));
        $title = $this->dynamicTitle();
        
        $filename = APPPATH . '../templates/laporan-keuangan.xlsx';
        /**  Create a new Reader of the type that has been identified  **/
        $spreadsheet = IOFactorySpreadsheet::load($filename);
        $sheet = $spreadsheet->setActiveSheetIndex(0);

        $images = backupImageInfo($spreadsheet);

        $sheet->setCellValue("A1", $title);
        // var_dump($images);exit;
        $cols = excelColumnRange('A','AAA');

        $row = 4;
        
        foreach ($result as $key => $group) {
            if ($group->id == 'before') {
                $sheet->setCellValue("B$row", "Saldo Awal");  
                $sheet->setCellValue("C$row", sumOfArray($group->datas, 'jml_transaksi'));
                $sheet->setCellValue("D$row", sumOfArray($group->datas, 'nominal_masuk'));
                $sheet->setCellValue("E$row", sumOfArray($group->datas, 'nominal_keluar'));
                $sheet->setCellValue("F$row", sumOfArray($group->datas, 'saldo'));
                $row++;
            } else {
                if ($group->label){
                    $sheet->setCellValue("B$row", $group->label);
                    $row++;
                }
                foreach ($group->datas as $key => $data) {
                    $sheet->setCellValue("A$row", $key + 1);
                    $sheet->setCellValue("B$row", $data->label);
                    $sheet->setCellValue("C$row", $data->jml_transaksi);
                    $sheet->setCellValue("D$row", $data->nominal_masuk);
                    $sheet->setCellValue("E$row", $data->nominal_keluar);
                    $sheet->setCellValue("F$row", $data->saldo);
                    $row++;
                }
            }
            // $sheet->insertNewRowBefore($row + 1);
        }
        
        $sheet->setCellValue("B$row", "TOTAL SALDO");  
        $sheet->setCellValue("C$row", sumOfArray($result, 'jml_transaksi'));
        $sheet->setCellValue("D$row", sumOfArray($result, 'nominal_masuk'));
        $sheet->setCellValue("E$row", sumOfArray($result, 'nominal_keluar'));
        $sheet->setCellValue("F$row", sumOfArray($result, 'saldo'));
        
        $filename = "$title";
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
    
}