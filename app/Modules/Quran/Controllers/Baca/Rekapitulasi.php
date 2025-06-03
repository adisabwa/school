<?php

namespace Modules\Quran\Controllers\Admin;

use Modules\Data\Controllers\BaseData;
use App\Libraries\PdfBuilder;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use Config\Upload;
use stdClass;

class Rekapitulasi extends BaseData
{
    public $model;

    public function __construct()
    {
        $this->model = model('ListQuranModel');
    }

    public function index()
    {
        $whereAnd = $this->request->getGet('and') ?? [];
        $whereOr = $this->request->getGet('or') ?? [];
        $order = implode(",", $this->request->getPostGet('order') ?? []);
        
        $data = $this->model->getAll($whereAnd, $whereOr);

        // var_dump($this->psbModel->db->getLastQuery());
        return $this->respondCreated($data);
    }

    public function summary()
    {
        $where = $this->request->getGetPost();

        $data = $this->model->getSummary($where);
        $datasets = [];
        $labels = [];
        $def = [];
        $max = $min = 0;
        foreach ($data as $key => $value) {
            if (empty($labels[$value->tanggal])) {
                $labels[$value->tanggal] = date('M y', strtotime($value->tanggal));
                $def[$value->tanggal] = 0;
            }
        }
        $total = $def;
        foreach ($data as $key => $value) {
            if (empty($datasets[$value->tingkat_iqab])) {
                $color =  setRandomColor();
                $tmp = (object)[
                    'label' => $value->tingkat_iqab,
                    'data' => $def,
                    'tension' => 0.1,
                    'borderColor' => $color,
                    'backgroundColor' => $color,
                    'pointRadius' => 5,
                ];
                $datasets[$value->tingkat_iqab] = $tmp;
            }

            $datasets[$value->tingkat_iqab]->data[$value->tanggal] = $value->jumlah;
            $total[$value->tanggal] += $value->jumlah;
            if ($value->jumlah < $min) 
                $min = $value->jumlah;
        }
        
        $color =  setRandomColor();
        $datasets['total'] = (object)[
            'label' => 'Total Pelanggaran',
            'data' => $total,
            'tension' => 0.1,
            'borderColor' => $color,
            'backgroundColor' => $color,
            'pointRadius' => 5,
        ];
        $max = empty($total) ? 0 : max($total);
        foreach ($datasets as $key => $data) {
            $data->data = array_values($data->data);
        }
        $datasets = array_values($datasets);
        $labels = array_values($labels);
        return $this->respondCreated(compact('labels','datasets','max','min'));
    }

}