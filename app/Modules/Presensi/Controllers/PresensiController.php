<?php

namespace Modules\Presensi\Controllers;

use App\Controllers\BaseDataController;
use App\Libraries\NotificationManager;

class PresensiController extends BaseDataController
{
    public $sesiModel;
    public $semesterModel;
    public $jadwalModel;
    public $jadwalDetailModel;
    public $notificationModel;

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('PresensiMengajarModel');
        $this->sesiModel = model('DataSesiModel');
        $this->semesterModel = model('DataSemesterModel');
        $this->jadwalModel = model('MapelPenjadwalanModel');
        $this->notificationModel = model('NotificationModel');
    }

    public function createNotifications($return = FALSE)
    {
        $notif = new NotificationManager;
        $hour = date('H:i:00', strtotime('+5 minutes'));
        // $hour = '07:55:00';
        // var_dump($hour);

        $sesi = $this->sesiModel->getDataWhere(whereAnd:[
            'waktu_mulai <=' => $hour,
            'waktu_selesai >=' => $hour,
        ]);
        // $sesi = $this->sesiModel->find(4);
        // var_dump($this->sesiModel->getLastQuery());
        if (!$sesi || !is_numeric($sesi->sesi))
            return $this->failServerError('Sekarang bukan sesi mengajar');

        $id_sesi = $sesi->id;
        $no_sesi = $sesi->sesi;
        $id_semester = $this->semesterModel->getSemesterNow()->id;
        $tanggal = date('Y-m-d');
        $tanggal = '2026-08-24';
        
        $time = date('H:i:s');
        // $time = '08:00:00';
        $diff = strtotime($time) - strtotime($sesi->waktu_mulai);
        // telat 20 menit
        $is_telat = $diff > (20 * 60) ? '1' : '0';
        // var_dump($time, $diff);

        $hari = getHari($tanggal);

        $pembagianMapel = $this->jadwalModel->getAll(whereAnd: [
                '{n}id_semester'   => $id_semester,
                '{n}hari'   => $hari,
                '{n}id_sesi' => $id_sesi,
            ]);

        foreach ($pembagianMapel as $key => $value) {
            $data = [
                'id_guru' => $value->id_guru,
                'judul' => "REMINDER MENGAJAR ($value->kelas)",
                'pesan' => "&#128215; Mengajar $value->nama_mapel ($value->kelas) jam ke-$value->sesi",
                'next_route' => 'p/presensi/dashboard',
                'next_url' => site_url('p/presensi/dashboard'),
                'query' => '',
            ];
            $save = $notif->saveNotification($data);
        }

        if ($save)
            return $this->respondCreated();
        else
            return $this->failServerError();
    }
}