<?php

use Modules\Psb\Models\PsbModel;

function setStatusText($value) {
    if ($value == '0') return 'Terdaftar';
    else if ($value == '1') return 'Sudah Dibayar';
    else if ($value == '2') return 'Terverifikasi';
    else if ($value == '-1') return 'Koreksi Data';
    else '';
}

function setStatusColor($value) {
    if ($value == '0') return '#e8a633';
    else if ($value == '1') return '#20a3ba';
    else if ($value == '2') return '#18c953';
    else if ($value == '-1') return '#c93018';
    else '';
}

function getNomorPendaftaran($plus = 0) {
    $psb = new PsbModel();
    $data = $psb->where(['no_pendaftaran !=' => ''])->orderBy('id desc')->findAll();
    if ($data) {
        $data =  $data[0];
        $no_pendaftaran = $data->no_pendaftaran;
        $count = str_replace('PPMDA','', $no_pendaftaran);
        $count = (int) $count;
        $count++;
        $count = str_pad($count, 5, "0", STR_PAD_LEFT);
        // var_dump($count);exit;
    } else {
        $count = 1;
    }

    return 'PPMDA'.$count;

}