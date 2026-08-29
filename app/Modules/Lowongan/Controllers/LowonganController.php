<?php

namespace Modules\Lowongan\Controllers;


use App\Controllers\BaseDataController;

class LowonganController extends BaseDataController
{

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('LowonganModel');
    }

    public function generateContentData()
    {
        $gemini = new \App\Libraries\GeminiService();

        $fileGambar = $this->request->getFile('poster');
        if (!$fileGambar || !$fileGambar->isValid()) {
            return $this->respond(['error' => 'File gambar tidak valid'], 400);
        }
        $tipeMime = $fileGambar->getMimeType();
        $dataGambarBase64 = base64_encode(file_get_contents($fileGambar->getTempName()));

        $prompt = "Ekstrak data dari gambar lowongan kerja ini menjadi JSON terstruktur yang berisi properti: nama_lowongan, tipe_pekerjaan, perusahaan, alamat_lowongan, keterangan_lowongan, persyaratan, email_lowongan, kontak_lowongan, tanggal_mulai, tanggal_selesai, gaji_start, dan gaji_end.";
        $gambar = [
            'data' => $dataGambarBase64,
            'mimeType' => $tipeMime
        ];
        $schema = [
            "type" => "OBJECT",
            "properties" => [
                "nama_lowongan" => [
                    "type" => "STRING",
                    "description" => "Nama posisi jabatan atau lowongan pekerjaan yang dibuka"
                ],
                "tipe_pekerjaan" => [
                    "type" => "STRING",
                    "description" => "Tipe lowongan pekerjaan dari (full-time, part-time, contract, internship, remote)"
                ],
                "alamat_lowongan" => [
                    "type" => "STRING",
                    "description" => "Lokasi penempatan kerja atau alamat perusahaan"
                ],
                "gaji_start" => [
                    "type" => "STRING",
                    "description" => "Informasi gaji terendah. Isi '0' jika tidak ada."
                ],
                "gaji_end" => [
                    "type" => "STRING",
                    "description" => "Informasi gaji tertinggi. Isi '0' jika tidak ada."
                ],
                "persyaratan" => [
                    "type" => "ARRAY",
                    "items" => ["type" => "STRING"],
                    "description" => "Daftar kualifikasi, syarat, atau kemampuan yang dibutuhkan"
                ],
                "perusahaan" => [
                    "type" => "STRING",
                    "description" => "Nama perusahaan yang membuka lowongan pekerjaan"
                ],
                "keterangan_lowongan" => [
                    "type" => "STRING",
                    "description" => "Deskripsi pekerjaan yang dibuka"
                ],
                "email_lowongan" => [
                    "type" => "STRING",
                    "description" => "Alamat email untuk mengirimkan lamaran pekerjaan atau link pendaftaran yang ada"
                ], 
                "kontak_lowongan" => [
                    "type" => "STRING",
                    "description" => "Nomor kontak untuk informasi lowongan pekerjaan"
                ],
                "tanggal_mulai" => [
                    "type" => "STRING",
                    "description" => "Tanggal mulai pendaftaran lowongan pekerjaan"
                ],
                "tanggal_selesai" => [
                    "type" => "STRING",
                    "description" => "Tanggal akhir pendaftaran lowongan pekerjaan"
                ]
            ],
            "required" => ["nama_lowongan", "perusahaan", "tipe_pekerjaan", "alamat_lowongan", "persyaratan"]
        ];

        // Otomatis me-return Array PHP terstruktur
        $hasil = $gemini->generate($prompt, $gambar, $schema);
        // Akses: $hasil['nama_lowongan']
        // var_dump($hasil);
        return $this->respondCreated($hasil);
    }
}
