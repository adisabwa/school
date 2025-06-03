<?php

use App\Models\PsbModel;

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
    $data = $psb->where(['status' => '2'])->findAll();
    $count = count($data) + 1 + $plus;

    return 'simak000'.$count;

}