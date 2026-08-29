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
        $id_sesi = $this->request->getGetPost('id_sesi');
        $tanggal = $this->request->getGetPost('tanggal') ?? date('Y-m-d');
        // $id_sesi = 1;

        if ($id_sesi) {
            $sesi = $this->sesiModel->getDataWhere(whereAnd:[
                'sesi' => $id_sesi
            ]);
        } else {
            $sesi = $this->sesiModel->getSesiNow();
        }
        // var_dump($id_sesi, $sesi);
        if (!$sesi || !is_numeric($sesi->sesi))
            return $this->failServerError('Sekarang bukan sesi mengajar');
        
        $id_sesi = $sesi->id;
        $no_sesi = $sesi->sesi;
        $id_semester = $this->semesterModel->getSemesterNow()->id;

        // $tanggal = '2026-08-24';
        $hari = getHari($tanggal);
        $id_kelas = $this->request->getGetPost('id_kelas');

        $time = date('H:i:s');

        $pembagianMapel = $this->jadwalModel->getDataWhere(whereAnd: [
            '{n}id_semester'   => $id_semester,
            '{n}hari'   => $hari,
            '{n}id_kelas'   => $id_kelas,
        ], havingAnd:[
            'sesi_awal <=' => $no_sesi,
            'sesi_akhir >=' => $no_sesi,
        ]);
        // $time = '11:00:00';
        // var_dump($time, $pembagianMapel->waktu_mulai);
        $diff = strtotime($time) - strtotime($pembagianMapel->waktu_mulai);
        // telat 10 menit
        $is_telat = $diff > (10 * 60) ? '1' : '0';
        // var_dump($time, $diff);


        $data = $this->model->getDataWhere(whereAnd:[
            'id_semester'   => $id_semester,
            'tanggal'   => $tanggal,
            'id_kelas'   => $id_kelas,
        ], havingAnd:[
            'sesi_awal <=' => $no_sesi,
            'sesi_akhir >=' => $no_sesi,
        ]);
        // var_dump($data);
        // var_dump($this->model->getLastQuery());
        // return $this->respondCreated($data);
        if (empty($data)) {
            // var_dump($this->jadwalModel->getLastQuery());
            // return $this->respondCreated($pembagianMapel);
            // var_dump($hari, $id_semester, $id_kelas, $no_sesi, $pembagianMapel);
            $user = userData();
            $data = [
                'id_semester'   => $id_semester,
                'id_sesi'   => $pembagianMapel->id_sesi,
                'tanggal'   => $tanggal,
                'id_kelas'   => $id_kelas,
                'is_telat'   => $is_telat,
                'waktu_telat'   => $diff - 600, // toleransi 10 menit
                'id_guru'   => $pembagianMapel->id_guru,
                'kelas'   => $pembagianMapel->kelas,
                'nama_guru_lengkap'   => $pembagianMapel->nama_guru_lengkap,
                'id_mapel'  => $pembagianMapel->id_mapel,
                'jam'  => $pembagianMapel->jam,
                'sesi_awal'  => $pembagianMapel->sesi_awal,
                'sesi_akhir'  => $pembagianMapel->sesi_akhir,
                'waktu_mulai' => $pembagianMapel->waktu_mulai,
                'waktu_selesai' => $pembagianMapel->waktu_selesai_akhir,
            ];
            if ($user->id == $pembagianMapel->id_guru) {
                $data['kehadiran'] = 'hadir';
            } else {
                $data['kehadiran'] = 'tidak hadir';
                $data['id_pengganti'] = $user->id;
            }
            // $this->model->insert($data);

            // return $this->index();
        }

        return $this->respondCreated($data);
    }

    public function getAll()
    {
        return parent::index(FALSE);
    }

    public function getAllGroupingGuru()
    {
        
        $where = $this->request->getGetPost('where') ?? ['1=2'];
        $order = $this->request->getGetPost('order') ?? [];
        $order = implode(',', $order);
        
        $this->model->selects = [
            "{n}SUM(jam) total_jam_guru",
            "{n}SUM(IF(kehadiran='hadir',jam,0)) total_hadir_guru",
            "{n}SUM(IF(kehadiran='tidak hadir',jam,0)) total_alfa_guru",
            "{n}SUM(IF(kehadiran='sakit',jam,0)) total_sakit_guru",
            "{n}SUM(IF(kehadiran='pribadi',jam,0)) total_pribadi_guru",
            "{n}SUM(IF(kehadiran='dinas',jam,0)) total_dinas_guru",
            "{n}SUM(IF(kehadiran='persyarikatan',jam,0)) total_persyarikatan_guru",
            "{n}COUNT(IF(is_telat='1',1,NULL)) total_telat_guru",
            "{n}COUNT(IF(tugas='1',1,NULL)) total_tugas_guru",
            "{n}SUM(waktu_telat) total_waktu_telat_guru",
            "GROUP_CONCAT({f}.id) ids",
        ];
        $data = $this->model->getAll(whereAnd: $where, groupBy: ['id_guru'], order: $order);

        return $this->respondCreated(array_values($data));
    }

    public function getAllGrouping()
    {
        $datas = parent::index(TRUE);
        $role = $this->request->getGetPost('role');
        switch ($role) {
            case 'guru':
                $funcInd = function($val){
                    return $val->id_mapel;
                };
                break;
            
            default:
                $funcInd = function($val){
                    return $val->id_kelas;
                };
                break;
        }
        $result = [];

        foreach ($datas as $key => $value) {
            $ind = $funcInd($value);
            if (!isset($result[$ind])) {
                $result[$ind] = clone $value;
                $result[$ind]->ids = [$value->id];
                $result[$ind]->id_mapels = [$value->id_mapel];
            } else {
                $result[$ind]->ids[] = $value->id;
                if (!in_array($value->id_mapel, $result[$ind]->id_mapels))
                    $result[$ind]->id_mapels[] = $value->id_mapel;
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