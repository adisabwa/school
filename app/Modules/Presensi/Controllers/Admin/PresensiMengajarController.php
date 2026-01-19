<?php

namespace Modules\Presensi\Controllers\Admin;

use App\Controllers\BaseDataController;

class PresensiMengajarController extends BaseDataController
{
    public $sesiModel;
    public $semesterModel;
    public $jadwalModel;
    public $jadwalDetailModel;

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('PresensiMengajarModel');
        $this->sesiModel = model('DataSesiModel');
        $this->semesterModel = model('DataSemesterModel');
        $this->jadwalModel = model('MapelPenjadwalanModel');
    }

    public function index($return = FALSE)
    {
        // $tanggal = date('Y-m-d');
        $sesi = $this->sesiModel->getSesiNow();
        $sesi = $this->sesiModel->find(1);
        if (!$sesi) exit('Bukan sesi mengajar');
        $id_sesi = $sesi->id;
        $id_semester = $this->semesterModel->getSemesterNow()->id;
        $tanggal = date('Y-m-d');
        $time = date('H:i:s');
        $time = '08:00:00';
        $diff = strtotime($time) - strtotime($sesi->waktu_mulai);
        // telat 20 menit
        $is_telat = $diff > (20 * 60) ? '1' : '0';
        // var_dump($time, $diff);

        $hari = getHari($tanggal);
        $id_kelas = $this->request->getGetPost('id_kelas');

        $data = $this->model->getDataWhere(whereAnd:[
            'id_semester'   => $id_semester,
            'id_sesi'   => $id_sesi,
            'tanggal'   => $tanggal,
            'id_kelas'   => $id_kelas,
        ]);
        
        if (empty($data)) {
        
            $pembagianMapel = $this->jadwalModel->getDataWhere(whereAnd: [
                '{n}id_semester'   => $id_semester,
                '{n}id_sesi'   => $id_sesi,
                '{n}hari'   => $hari,
                '{n}id_kelas'   => $id_kelas,
            ]);

            $this->model->insert([
                'id_semester'   => $id_semester,
                'id_sesi'   => $id_sesi,
                'tanggal'   => $tanggal,
                'id_kelas'   => $id_kelas,
                'is_telat'   => $is_telat,
                'waktu_telat'   => $diff - 600, // toleransi 10 menit
                'id_guru'   => $pembagianMapel->id_guru,
                'id_mapel'  => $pembagianMapel->id_mapel,
                'jam'  => $pembagianMapel->jam,
                'kehadiran' => 'hadir',
            ]);

            return $this->index();
        }

        return $this->respondCreated($data);
    }

    public function getAll()
    {
        return parent::index(FALSE);
    }

    public function getAllGrouping()
    {
        $datas = parent::index(TRUE);
        $result = [];
        foreach ($datas as $key => $value) {
            $ind = $value->id_mapel.'_'.$value->id_kelas;
            if (!isset($result[$ind])) {
                $result[$ind] = clone $value;
                $result[$ind]->ids = [$value->id];
            } else {
                $result[$ind]->ids[] = $value->id;
                $result[$ind]->total_hadir += $value->total_hadir;
                $result[$ind]->total_izin += $value->total_izin;
                $result[$ind]->total_alfa += $value->total_alfa;
                $result[$ind]->total_sakit += $value->total_sakit;
                $result[$ind]->jam += $value->jam;
            }
        }
        foreach ($result as $key => $value) {
            $jml = $value->total_hadir + $value->total_izin + $value->total_alfa + $value->total_sakit;
            $value->presentase_hadir = $jml > 0 ? round($value->total_hadir / $jml * 100) : 0;
        }

        return $this->respondCreated(array_values($result));
    }

    public function getSummary()
    {
        $data = parent::index(TRUE);   
        $role = $this->request->getGetPost('role');
        // var_dump($data);
        switch ($role) {
            case 'guru':
                $funcLabel = function($v) { return $v->nama_mapel; };
                $funcGroup = function($v) { return $v->id_mapel; };
                break;
            case 'walas':
                $funcLabel = function($v) { return $v->kelas; };
                $funcGroup = function($v) { return $v->id_kelas; };
                break;
            default:
                $funcLabel = function($v) { return $v->kelas; };
                $funcGroup = function($v) { return $v->id_kelas; };
                break;
        }
        $datasets = [
            'labels'    => [],
            'datasets'  => [
                'hadir' => [
                    'label' => 'Presentase Kehadiran',
                    'data'  => [],
                    'backgroundColor'   => setRandomColor(),
                ],
            ],
        ];

        $sum = [];
        foreach ($data as $key => $value) {
            $ind = $funcGroup($value);
            if (!isset($datasets['labels'][$ind])) {
                $datasets['labels'][$ind] = $funcLabel($value);
                $datasets['datasets']['hadir']['data'][$ind] = $value->total_hadir ?? 0;
                $sum[$ind] = ($value->total_sakit ?? 0) + ($value->total_izin ?? 0) + ($value->total_alfa ?? 0) + ($value->total_hadir ?? 0);
            } else {
                $datasets['datasets']['hadir']['data'][$ind] += $value->total_hadir ?? 0;
                $sum[$ind] += ($value->total_sakit ?? 0) + ($value->total_izin ?? 0) + ($value->total_alfa ?? 0) + ($value->total_hadir ?? 0);
            }
        }

        foreach ($datasets['datasets']['hadir']['data'] as $key => $value) {
            $datasets['datasets']['hadir']['data'][$key] = $sum[$key] > 0 ? round(($value / $sum[$key]) * 100, 2) : 0;
        }
        $datasets['labels'] = array_values($datasets['labels']);
        $datasets['datasets'] = array_values($datasets['datasets']);
        foreach ($datasets['datasets'] as $key => $value) {
            $datasets['datasets'][$key]['data'] = array_values($value['data']);
        }
        return $this->respondCreated($datasets);
    }

}