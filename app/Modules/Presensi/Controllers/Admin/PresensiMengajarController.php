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
        $id_sesi = $this->sesiModel->getSesiNow()->id ?? -1;
        $id_sesi = 1;
        $id_semester = $this->semesterModel->getSemesterNow()->id;
        $tanggal = date('Y-m-d');
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

}