<?php

namespace App\Controllers;

use App\Controllers\BaseDataController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;

class BasePageController extends BaseDataController
{
    private $modelAnggota;

    public function __construct()
    {
        parent::__construct();
        
        $this->modelAnggota = model('AnggotaModel');
    }

    public function index()
    {
        $where = $this->request->getGetPost('where') ?? [];
        $whereOr = $this->request->getGetPost('or') ?? [];
        $whereIn = $this->request->getGetPost('in') ?? [];
        $limit = $this->request->getGetPost('limit') ?? 5;
        $offset = $this->request->getGetPost('offset') ?? 0;
        $grouping = $this->request->getGetPost('grouping') ?? ['id'];

        $data = $this->model->getAll($where,$whereOr,'tanggal desc, id',
        $limit, $offset, $grouping, $whereIn);

        return $this->respondCreated($data);

    }

    public function get_before()
    {
        $postData = $this->request->getGetPost();
        $id_anggota = $postData['id_anggota'] ?? userdata()->id_anggota;
        $data = $this->model->where('id_anggota', $id_anggota)->orderBy('tanggal desc')->find();
        $now = date('Y-m-d');
        $tanggal = $data[0]->tanggal ?? $now;

        // var_dump($tanggal, $now);
        return $this->respondCreated(get_date_interval($tanggal ?? $now, $now));
    }

    
    public function createChart($attr = 'data_chart')
    {
        $postData = $this->request->getGetPost();
        $type = $postData['tipe'] ?? 'week';
        $end = $postData['end'] ?? date('Y-m-d');
        $start = $postData['start'] ?? date('Y-m-d');
        $id_anggota = $postData['id_anggota'] ?? userdata()->id_anggota;
        $where_anggota = "id_anggota IN ($id_anggota)";
        $anggotas = $this->modelAnggota->where(["id IN ($id_anggota)" => NULL])->findAll();

        $date_range = getDateRange($start, $end);
        $data = $this->model->getAll(
            [
                $where_anggota => NULL,
                "tanggal >= '$start'" => NULL,
                "tanggal <= '$end'" => NULL,
            ]
        );
        $_data = [];
        // var_dump($data);
        foreach ($data as $key => $d) {
            $d->id_anggota = "$d->id_anggota-$d->nama";
            if (empty($_data[$d->id_anggota][$d->tanggal])) {
                $_data[$d->id_anggota][$d->tanggal] = $d->$attr;
            } else {
                $_data[$d->id_anggota][$d->tanggal] += $d->$attr;
            }
        }
        // var_dump(userdata());
            // foreach ($anggotas as $key => $d) {
            //     $_data["$d->id-$d->nama"] =  [date('Y-m-d') => '0'];
            // }
        // var_dump($_data);
        $total = $labels = [];
        $max = $min = 0;
        $datasets = [];
        foreach ($anggotas as $ind => $d) {
            $data_ang = $_data["$d->id-$d->nama"] ?? [];
            // var_dump($id_anggota);
            foreach ($date_range as $key => $tgl) {
                // var_dump($tgl);
                $labels[$tgl] = date("d M", strtotime($tgl));
                $total[$tgl] = $data_ang[$tgl] ?? 0;
            }
            
            $color =  setRandomColor();
            $datasets[] = (object)[
                'label' => $d->nama,
                'data' => array_values($total),
                'tension' => 0.1,
                'borderColor' => $color,
                'backgroundColor' => $color,
                'pointRadius' => 5,
            ];
            $tmp_max = empty($total) ? 1 : max($total);
            $tmp_min = empty($total) ? -1 : min($total);
            if ($tmp_max > $max) $max = $tmp_max;
            if ($tmp_min < $min) $min = $tmp_min;
        }
        
        // $datasets = array_values($datasets);
        $labels = array_values($labels);
        return $this->respondCreated(compact('labels','datasets','max','min'));
    }
}