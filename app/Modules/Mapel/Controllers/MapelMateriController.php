<?php

namespace Modules\Mapel\Controllers;

use App\Controllers\BaseDataController;

class MapelMateriController extends BaseDataController
{
    
    public $modelPembagian;

    public function __construct()
    {  
        parent::__construct();
        
        $this->model = model('MapelMateriModel');
        $this->modelPembagian = model('MapelPembagianModel');
    }

    public function getSummary($return = FALSE)
    {
        $tahun_ajaran = $this->request->getGetPost('tahun_ajaran');
        $id_guru = $this->request->getGetPost('id_guru');

        $this->model->selects = ['id id_materi'];
        $this->model->relations['id_materi'] = [
            'foreign_key' => 'id',
            'local_key' => 'id_materi',
            'table' => PREFIX_TABLE.'aka_sub_materi',
            'type' => 'left',
            'selects' => [
                'id id_sub','sub_materi','tujuan_pembelajaran','poin','deskripsi'
            ],
            // 'group_by' => 'id_materi'
        ];
        $materis = $this->model->getAll(whereAnd: [
            'id_guru' => $id_guru,
        ],
        havingAnd:[
            'tahun_ajaran' => $tahun_ajaran,
        ]);
        // var_dump($materis);
        $list_materis = [];
        foreach ($materis as $key => $d) {
            $ind = "$d->id_mapel-$d->tingkat";
            $id_materi = $d->id_materi;
            if (empty($list_materis[$ind][$id_materi])) {
                $list_materis[$ind][$id_materi] = clone $d;
                $list_materis[$ind][$id_materi]->sub_materi = [];
            }

            $list_materis[$ind][$id_materi]->sub_materi[] = (object) [
                'sub_materi' => $d->sub_materi,
                'tujuan_pembelajaran' => $d->tujuan_pembelajaran,
                'poin' => $d->poin,
                'id_sub' => $d->id_sub,
            ];
        }

        // var_dump($list_materis);
        
        $this->modelPembagian->selects = ["{n}GROUP_CONCAT(kelas) daftar_kelas"];

        $datas = $this->modelPembagian->getAll(whereAnd: [
            'id_guru' => $id_guru,
        ], 
        havingAnd:[
            'tahun_ajaran' => $tahun_ajaran,
        ],
        groupBy: ['id_semester', 'id_guru', 'id_mapel','tingkat'],
        order:'id_semester, tingkat, id_mapel, id_guru');
        
        // var_dump($datas);
        $results = [];  
        foreach ($datas as $key => $d) {
            $ind = $d->id;
            if (empty($results[$ind])) {
                $results[$ind] = (object) [
                    'id' => $d->id,
                    'tahun_ajaran' => $d->tahun_ajaran,
                    'id_semester' => $d->id_semester,
                    'id_guru' => $d->id_guru,
                    'id_mapel' => $d->id_mapel,
                    'kode_mapel' => $d->kode_mapel,
                    'nama_mapel' => $d->nama_mapel,
                    'nama_mapel_arab' => $d->nama_mapel_arab,
                    'nama_guru' => $d->nama_guru,
                    'nama_guru_lengkap' => $d->nama_guru_lengkap,
                    'nbm_guru' => $d->nbm_guru,
                    'nama_unit' => $d->nama_unit,
                    'nama_kepala' => $d->nama_kepala,
                    'nama_kepala_lengkap' => $d->nama_kepala_lengkap,
                    'nama_kepala_arab' => $d->nama_kepala_arab,
                    'nbm_kepala' => $d->nbm_kepala,
                    'kepala_signature' => $d->kepala_signature,
                    'jam' => $d->jam,
                    'pertemuan' => $d->pertemuan,
                    'jam_per_pertemuan' => $d->jam_per_pertemuan,
                    'kelas' => $d->daftar_kelas ?? '',   
                    'tingkat' => $d->tingkat,
                    'fase' => $d->tingkat < 4 ? 'D' : ($d->tingkat < 6 ? 'E' : 'F'),
                    'minggu' => $d->minggu,
                    'is_expand' => false,
                    'materi' => array_values($list_materis["$d->id_mapel-$d->tingkat"] ?? []),
                ];
            }
        }

        $results = array_values($results);

        return $this->respondCreated($results);
    }
    
    public function generateListMateri()
    {
        $gemini = new \App\Libraries\GeminiService();

        $reset = $this->request->getGetPost('reset');
        $id_semester = $this->request->getGetPost('id_semester');
        $id_guru = $this->request->getGetPost('id_guru');
        $id_mapel = $this->request->getGetPost('id_mapel');
        $nama_mapel = $this->request->getGetPost('nama_mapel');
        $nama_unit = $this->request->getGetPost('nama_unit');
        $tingkat = $this->request->getGetPost('tingkat');
        $jam = $this->request->getGetPost('jam');
        $pertemuan = $this->request->getGetPost('pertemuan');
        $jam_per_pertemuan = $this->request->getGetPost('jam_per_pertemuan');
        $minggu = $this->request->getGetPost('minggu');
        $jml_materi = $this->request->getGetPost('jml_materi');
        $jam_lama = $this->request->getGetPost('jam_lama') ?? 0;
        $pertemuan_lama = $this->request->getGetPost('pertemuan_lama') ?? 0;
        $notes = $this->request->getGetPost('notes');
        $new_tingkat = $tingkat + 6;        

        $prompt = "Buatkan list materi untuk mata pelajaran $nama_mapel dan alokasi jam dan pertemuannya untuk kelas $new_tingkat untuk unit $nama_unit , dengan catatan $notes.
            Minggu aktif sebanyak $minggu minggu. Total jam pelajaran $jam JP dan per minggu, 1 minggu ada $pertemuan pertemuan dan pembagian jam per pertemuan per minggu nya $jam_per_pertemuan. Total jam dan pertemuan dikurangi dengan jumlah jam lama sebesar $jam_lama JP dan pertemuan sebesar $pertemuan_lama pertemuan";
        
        $schema = [
            'type' => 'ARRAY',
            'description' => 'Daftar materi, alokasi jam dan pertemuannya',
            'items' => [
                'type' => 'OBJECT',
                'description' => 'Data materi',
                'required' => [
                    'materi','jam','pertemuan',
                ],
                'properties' => [
                    'materi' => [
                        'type' => 'STRING',
                        'description' => 'Judul materi'
                    ],
                    'jam' => [
                        'type' => 'NUMBER',
                        'description' => 'Total Alokasi jam pelajaran'
                    ],
                    'pertemuan' => [
                        'type' => 'NUMBER',
                        'description' => 'jumlah pertemuan'
                    ],
                ]
            ]
        ];

        // Otomatis me-return Array PHP terstruktur
        $hasil = $gemini->generate($prompt, null, $schema);
        // Akses: $hasil['nama_lowongan']
        $this->model->transBegin();

        $start = 1;
        if ($reset == '1') {
            $this->model->where([
                'id_semester' => $id_semester,
                'id_guru' => $id_guru,
                'id_mapel' => $id_mapel,
                'tingkat' => $tingkat,
            ])->delete();
        } else {
            $start = $jml_materi - 1;
        }

        foreach ($hasil as $key => $value) {
           $this->model->insert([
                'id_semester' => $id_semester,
                'id_guru' => $id_guru,
                'id_mapel' => $id_mapel,
                'tingkat' => $tingkat,
                'no' => $key + $start,
                'materi' => $value['materi'],
                'jam' => $value['jam'],
                'pertemuan' => $value['pertemuan'],
           ]);
        }

        if ($this->model->transStatus() === false) {
            $this->model->transRollback();
            return $this->failServerError();
        } else {
            $this->model->transCommit();
            return $this->respondCreated();
        }
    }
    
    public function generateMateri()
    {
        $gemini = new \App\Libraries\GeminiService();

        $nama_mapel = $this->request->getGetPost('nama_mapel');
        $nama_unit = $this->request->getGetPost('nama_unit');
        $tingkat = $this->request->getGetPost('tingkat');
        $tahun_ajaran = $this->request->getGetPost('tahun_ajaran');
        $curiculum = $this->request->getGetPost('curiculum');
        $materi = $this->request->getGetPost('materi');
        $notes = $this->request->getGetPost('notes');
        $tingkat += 6;        

        $prompt = "Buatkan pemetaan pembelajaran untuk mata pelajaran $nama_mapel kelas $tingkat tahun ajaran $tahun_ajaran untuk unit $nama_unit sedang membahas materi $materi di kurikulum $curiculum, dengan catatan $notes.
        Lengkapi CP, TP, Sub Materi, Deskripsi, dan Poin-poin Materi.";
        
        $schema = [
            'type' => 'OBJECT',
            'properties' => [
                'cp' => [
                    'type' => 'STRING',
                    'description' => 'Capaian Pembelajaran secara keseluruhan'
                ],
                'list_materi' => [
                    'type' => 'ARRAY',
                    'description' => 'Daftar materi yang berisi Tujuan Pembelajaran dan detail sub materi',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'tujuan_pembelajaran' => [
                                'type' => 'STRING',
                                'description' => 'Tujuan Pembelajaran'
                            ],
                            'sub_materi' => [
                                'type' => 'STRING',
                                'description' => 'Judul Sub Materi'
                            ],
                            'deskripsi' => [
                                'type' => 'STRING',
                                'description' => 'Penjelasan singkat sub materi'
                            ],
                            'poin' => [
                                'type' => 'ARRAY',
                                'description' => 'Poin-poin penting dalam sub materi',
                                'items' => [
                                    'type' => 'OBJECT',
                                    'properties' => [
                                        'poin' => [
                                            'type' => 'STRING',
                                            'description' => 'Poin yang penting'
                                        ],
                                    ],
                                    'required' => ['poin']
                                ]
                            ]
                        ],
                        'required' => ['tujuan_pembelajaran', 'sub_materi', 'deskripsi', 'poin']
                    ]
                ]
            ],
            'required' => ['cp', 'list_materi']
        ];

        // Otomatis me-return Array PHP terstruktur
        $hasil = $gemini->generate($prompt, null, $schema);
        // Akses: $hasil['nama_lowongan']
        // var_dump($hasil);
        return $this->respondCreated($hasil);
    }
    
    public function saranAsesmen()
    {
        $gemini = new \App\Libraries\GeminiService();

        $data_mapel = $this->request->getGetPost('data_mapel');

        $prompt = "Berdasarkan parameter dari mata pelajaran dengan konfigurasi JSON berikut:\n" . 
          json_encode($data_mapel, JSON_PRETTY_PRINT) . "\n\n" .
          "Berikan saran asesmen untuk pelajaran tersebut skema JSON yang ditentukan.";
        
        $schema = [
            'type' => 'OBJECT',
            'properties' => [
                'overviewReasoning' => [
                    'type' => 'STRING',
                    'description' => 'Strategi asesmen secara umum'
                ],
                'recommendations' => [
                    'type' => 'ARRAY',
                    'description' => 'Daftar rekomendasi asesmen yang disarankan',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'category' => [
                                'type' => 'STRING',
                                'description' => 'Kategori asesmen (diagnostic / formative / summative)'
                            ],
                            'assessmentType' => [
                                'type' => 'STRING',
                                'description' => 'Jenis Asesmen dalam kalimat singkat'
                            ],
                            'explanation' => [
                                'type' => 'STRING',
                                'description' => 'Penjelasan kenapa asesmen ini diperlukan'
                            ],
                            'recommendedInstruments' => [
                                'type' => 'ARRAY',
                                'description' => 'Daftar Instrumen asesmen yang disarankan',
                                'items' => [
                                    'type' => 'STRING',
                                    'description' => 'instrumen asesmen yang disarankan'
                                ]
                            ]
                        ],
                        'required' => ['category', 'assessmentType', 'explanation', 'recommendedInstruments']
                    ]
                ]
            ],
            'required' => ['overviewReasoning', 'recommendations']
        ];

        // Otomatis me-return Array PHP terstruktur
        $hasil = $gemini->generate($prompt, null, $schema);
        // Akses: $hasil['nama_lowongan']
        // var_dump($hasil);
        return $this->respondCreated($hasil);
    }
    
    
    public function generateRpp()
    {
        $gemini = new \App\Libraries\GeminiService();

        $data_mapel = $this->request->getGetPost('data_mapel');
        $konfigurasi_asesmen = $this->request->getGetPost('konfigurasi_asesmen');

        $prompt = "Berdasarkan parameter dari mata pelajaran dengan konfigurasi JSON berikut:\n" . 
          json_encode($data_mapel, JSON_PRETTY_PRINT) . "\n\n" .
          "Dan konfigurasi asesmen dalam bentuk JSON seperti berikut :\n".
          json_encode($konfigurasi_asesmen, JSON_PRETTY_PRINT) . "\n\n" .
          "1JP itu 40 menit. Berikan saran asesmen untuk pelajaran tersebut skema JSON yang ditentukan.";
    
        $schema = [
            'type' => 'OBJECT',
            'description' => 'Objek utama yang memuat seluruh dokumen RPP / Modul Ajar.',
            'required' => [
                'pancasilaProfiles',
                'targetPesertaDidik', 
                'pemahamanBermakna', 'pertanyaanPemantik',
                'kegiatanPembelajaran', 'asesmen', 'pengayaanDanRemedial',
                'refleksiGuruDanSiswa', 'lampiran', 
                'pertemuanList', 
                'asesmenDiagnostik',
                'asesmenFormatif',
                'asesmenSumatif',
            ],
            'properties' => [
                // 2. Profil Pancasila & Informasi Umum
                'pancasilaProfiles' => [
                    'type' => 'ARRAY',
                    'description' => 'Daftar dimensi Profil Pelajar Pancasila yang dikembangkan.',
                    'items' => ['type' => 'STRING']
                ],
                'targetPesertaDidik' => [
                    'type' => 'STRING',
                    'description' => 'Kategori dan karakteristik umum peserta didik.'
                ],
                'pemahamanBermakna' => [
                    'type' => 'STRING',
                    'description' => 'Manfaat nyata yang diperoleh peserta didik setelah mempelajari materi.'
                ],
                'pertanyaanPemantik' => [
                    'type' => 'ARRAY',
                    'description' => 'Daftar pertanyaan untuk memicu pemikiran kritis siswa.',
                    'items' => ['type' => 'STRING']
                ],

                // 3. Kegiatan Pembelajaran Umum
                'kegiatanPembelajaran' => [
                    'type' => 'OBJECT',
                    'description' => 'Gambaran umum sintaks/alur kegiatan pembelajaran (Pendahuluan, Inti, Penutup).',
                    'required' => ['pendahuluan', 'inti', 'penutup'],
                    'properties' => [
                        'pendahuluan' => [
                            'type' => 'OBJECT',
                            'required' => ['duration', 'activities'],
                            'properties' => [
                                'duration' => ['type' => 'STRING', 'description' => 'Durasi waktu pendahuluan.'],
                                'activities' => ['type' => 'ARRAY', 'description' => 'Daftar aktivitas pendahuluan.', 'items' => ['type' => 'STRING']]
                            ]
                        ],
                        'inti' => [
                            'type' => 'OBJECT',
                            'required' => ['duration', 'activities'],
                            'properties' => [
                                'duration' => ['type' => 'STRING', 'description' => 'Durasi waktu kegiatan inti.'],
                                'activities' => ['type' => 'ARRAY', 'description' => 'Daftar aktivitas inti sesuai sintaks model belajar.', 'items' => ['type' => 'STRING']]
                            ]
                        ],
                        'penutup' => [
                            'type' => 'OBJECT',
                            'required' => ['duration', 'activities'],
                            'properties' => [
                                'duration' => ['type' => 'STRING', 'description' => 'Durasi waktu penutup.'],
                                'activities' => ['type' => 'ARRAY', 'description' => 'Daftar aktivitas penutup dan refleksi.', 'items' => ['type' => 'STRING']]
                            ]
                        ]
                    ]
                ],

                // 4. Asesmen
                'asesmen' => [
                    'type' => 'OBJECT',
                    'description' => 'Perencanaan asesmen/penilaian (Diagnostik, Formatif, Sumatif, dan Rubrik).',
                    'required' => ['diagnostik', 'formatif', 'sumatif', 'rubrikPenilaian'],
                    'properties' => [
                        'diagnostik' => [
                            'type' => 'OBJECT',
                            'description' => 'Rencana asesmen diagnostik awal.',
                            'required' => ['deskripsi', 'instrumen'],
                            'properties' => [
                                'deskripsi' => ['type' => 'STRING', 'description' => 'Deskripsi asesmen diagnostik.'],
                                'instrumen' => ['type' => 'ARRAY', 'description' => 'Daftar instrumen diagnostik.', 'items' => ['type' => 'STRING']]
                            ]
                        ],
                        'formatif' => [
                            'type' => 'OBJECT',
                            'description' => 'Rencana asesmen formatif selama proses.',
                            'required' => ['deskripsi', 'instrumen'],
                            'properties' => [
                                'deskripsi' => ['type' => 'STRING', 'description' => 'Deskripsi asesmen formatif.'],
                                'instrumen' => ['type' => 'ARRAY', 'description' => 'Daftar instrumen formatif.', 'items' => ['type' => 'STRING']]
                            ]
                        ],
                        'sumatif' => [
                            'type' => 'OBJECT',
                            'description' => 'Rencana asesmen sumatif akhir bab.',
                            'required' => ['deskripsi', 'instrumen'],
                            'properties' => [
                                'deskripsi' => ['type' => 'STRING', 'description' => 'Deskripsi asesmen sumatif.'],
                                'instrumen' => ['type' => 'ARRAY', 'description' => 'Daftar instrumen sumatif.', 'items' => ['type' => 'STRING']]
                            ]
                        ],
                        'rubrikPenilaian' => [
                            'type' => 'ARRAY',
                            'description' => 'Tabel kriteria dan kualifikasi skor penilaian.',
                            'items' => [
                                'type' => 'OBJECT',
                                'required' => ['kriteria', 'skor4', 'skor3', 'skor2', 'skor1'],
                                'properties' => [
                                    'kriteria' => ['type' => 'STRING', 'description' => 'Nama aspek/kriteria penilaian.'],
                                    'skor4' => ['type' => 'STRING', 'description' => 'Deskripsi performa skor 4 (Sangat Baik).'],
                                    'skor3' => ['type' => 'STRING', 'description' => 'Deskripsi performa skor 3 (Baik).'],
                                    'skor2' => ['type' => 'STRING', 'description' => 'Deskripsi performa skor 2 (Cukup).'],
                                    'skor1' => ['type' => 'STRING', 'description' => 'Deskripsi performa skor 1 (Perlu Bimbingan).']
                                ]
                            ]
                        ]
                    ]
                ],

                // 5. Pengayaan, Remedial, Refleksi & Lampiran
                'pengayaanDanRemedial' => [
                    'type' => 'OBJECT',
                    'description' => 'Strategi tindak lanjut bagi siswa yang melampaui atau belum mencapai KKTP.',
                    'required' => ['pengayaan', 'remedial'],
                    'properties' => [
                        'pengayaan' => ['type' => 'STRING', 'description' => 'Aktivitas pengayaan bagi siswa tuntas.'],
                        'remedial' => ['type' => 'STRING', 'description' => 'Aktivitas perbaikan bagi siswa belum tuntas.']
                    ]
                ],
                'refleksiGuruDanSiswa' => [
                    'type' => 'OBJECT',
                    'description' => 'Pertanyaan refleksi untuk mengevaluasi proses pembelajaran.',
                    'required' => ['refleksiGuru', 'refleksiSiswa'],
                    'properties' => [
                        'refleksiGuru' => ['type' => 'ARRAY', 'description' => 'Daftar pertanyaan refleksi diri guru.', 'items' => ['type' => 'STRING']],
                        'refleksiSiswa' => ['type' => 'ARRAY', 'description' => 'Daftar pertanyaan refleksi untuk siswa.', 'items' => ['type' => 'STRING']]
                    ]
                ],
                'lampiran' => [
                    'type' => 'OBJECT',
                    'description' => 'Dokumen pendukung modul seperti materi, glosarium, dan referensi.',
                    'required' => ['bahanBacaan', 'glosarium', 'daftarPustaka'],
                    'properties' => [
                        'bahanBacaan' => ['type' => 'STRING', 'description' => 'Ringkasan materi bacaan guru & siswa.'],
                        'glosarium' => ['type' => 'STRING', 'description' => 'Daftar definisi istilah-istilah penting.'],
                        'daftarPustaka' => ['type' => 'STRING', 'description' => 'Sumber referensi dan literatur.']
                    ]
                ],

                // 8. Rincian Pertemuan (List Pertemuan)
                'pertemuanList' => [
                    'type' => 'ARRAY',
                    'description' => 'Rincian kegiatan pembelajaran per pertemuan/tatap muka.',
                    'items' => [
                        'type' => 'OBJECT',
                        'required' => ['pertemuanKe', 'alokasiWaktu', 'topikPertemuan', 'pendahuluan', 'inti', 'penutup'],
                        'properties' => [
                            'pertemuanKe' => ['type' => 'INTEGER', 'description' => 'Urutan pertemuan ke-N.'],
                            'alokasiWaktu' => ['type' => 'STRING', 'description' => 'Durasi JP pada pertemuan ini.'],
                            'topikPertemuan' => ['type' => 'STRING', 'description' => 'Sub-materi/topik utama pertemuan ini.'],
                            'pendahuluan' => [
                                'type' => 'OBJECT',
                                'required' => ['duration', 'activities'],
                                'properties' => [
                                    'duration' => ['type' => 'STRING', 'description' => 'Durasi pendahuluan.'],
                                    'activities' => ['type' => 'ARRAY', 'description' => 'Daftar aktivitas pembuka.', 'items' => ['type' => 'STRING']]
                                ]
                            ],
                            'inti' => [
                                'type' => 'OBJECT',
                                'required' => ['duration', 'activities'],
                                'properties' => [
                                    'duration' => ['type' => 'STRING', 'description' => 'Durasi kegiatan inti.'],
                                    'activities' => ['type' => 'ARRAY', 'description' => 'Daftar aktivitas inti per tahapan PBL/model lain.', 'items' => ['type' => 'STRING']]
                                ]
                            ],
                            'penutup' => [
                                'type' => 'OBJECT',
                                'required' => ['duration', 'activities'],
                                'properties' => [
                                    'duration' => ['type' => 'STRING', 'description' => 'Durasi kegiatan penutup.'],
                                    'activities' => ['type' => 'ARRAY', 'description' => 'Daftar aktivitas penutup.', 'items' => ['type' => 'STRING']]
                                ]
                            ]
                        ]
                    ]
                ],

                'asesmenDiagnostik' => [
                    'type' => 'OBJECT',
                    'description' => 'Asesmen diagnostik',
                    'required' => ['daftarJenisSoal','panduan','strategiDiferensiasi'],
                    'properties' => [
                        'daftarJenisSoal' => [
                            'type' => 'ARRAY',
                            'description' => 'Daftar kelompok soal diagnostik sebelum pelajaran',
                            'items' => [
                                'type' => 'OBJECT',
                                'required' => [
                                    'kodeJenisSoal', 'namaJenisSoal','soalHtml','penilaian'
                                ],
                                'properties' => [
                                    'kodeJenisSoal' => [
                                        'type' => 'STRING',
                                        'description' => 'Identifier jenis soal (misal: pilihan_ganda, uraian, tugas_praktik).'
                                    ],
                                    'namaJenisSoal' => [
                                        'type' => 'STRING',
                                        'description' => 'Label nama jenis soal (misal: Bagian I: Pilihan Ganda).'
                                    ],
                                    'soalHtml' => [
                                        'type' => 'STRING',
                                        'description' => 'soal atau instruksi, beserta kunci jawaban dalam HTML. berikan style dalam bentuk tailwind. table border-collapse semua, line page sama semua, font 12px saja. Pilihan ganda jawabannya dibuat jadi 2 kolom. Praktik buat instruksi dalam bentuk list. Jumlah soal sesuai request prompt. Margin soal dan jawaban 0 saja'
                                    ],
                                    'penilaian' => [
                                        'type' => 'STRING',
                                        'description' => 'Dalam HTML. beri style dengan tailwind. Kalau praktik tampilkan rubik penilaian, kalau soal tampilkan nilai per nomor. kalau tabel penilaian pake border, jangan lupa tambah class border-solid, buat 4 kategori. Kalau soal beri nilai setiap nomornya, tabel diberi background gelap, teks terang '
                                    ],
                                ]
                            ]
                        ],
                        'panduan' => [
                            'type' => 'ARRAY',
                            'description' => 'Panduan Pemetaan & Diferensiasi Pembelajaran (Pegangan Guru)',
                            'items' => [
                                'type' => 'OBJECT',
                                'description' => 'Jenis pemahaman siswa',
                                'required' => ['kategori','warna','tindakLanjut'],
                                'properties' => [
                                    'kategori' => [
                                        'type' => 'STRING',
                                        'description' => 'Kategori pemetaan siswa berdasarkan nilai diagnostik'
                                    ],
                                    'warna' => [
                                        'type' => 'STRING',
                                        'description' => 'Jenis warna tailwind. background gelap teks terang'
                                    ],
                                    'tindakLanjut' => [
                                        'type' => 'STRING',
                                        'description' => 'tindak lanjut yang harus dilakukan'
                                    ],
                                ]
                            ]
                        ],
                        'strategiDiferensiasi' => [
                            'type' => 'STRING',
                            'description' => 'strategi pembeda dalam membimbing siswa',
                        ]
                    ],

                ],
                'asesmenFormatif' => [
                    'type' => 'OBJECT',
                    'description' => 'Asesmen formatif',
                    'required' => ['daftarJenisSoal'],
                    'properties' => [
                        'daftarJenisSoal' => [
                            'type' => 'ARRAY',
                            'description' => 'Daftar kelompok soal formatif di pertengahan pelajaran',
                            'items' => [
                                'type' => 'OBJECT',
                                'required' => [
                                    'kodeJenisSoal', 'namaJenisSoal','soalHtml',
                                    // 'penilaian'
                                ],
                                'properties' => [
                                    'kodeJenisSoal' => [
                                        'type' => 'STRING',
                                        'description' => 'Identifier jenis soal .'
                                    ],
                                    'namaJenisSoal' => [
                                        'type' => 'STRING',
                                        'description' => 'Label nama jenis soal (misal: Bagian I: Obsrvasi Siswa).'
                                    ],
                                    'soalHtml' => [
                                        'type' => 'STRING',
                                        'description' => 'soal atau instruksi dalam HTML . berikan style dalam bentuk tailwind. table border-collapse semua, line page sama semua, font 12px saja. Jumlah soal sesuai request prompt'
                                    ],
                                    // 'penilaian' => [
                                    //     'type' => 'STRING',
                                    //     'description' => 'Dalam HTML. beri style dengan tailwind. Kalau praktik tampilkan rubik penilaian, kalau soal tampilkan nilai per nomor. kalau tabel penilaian pake border, jangan lupa tambah class border-solid, buat 4 kategori. Kalau soal beri nilai setiap nomornya, tabel diberi background gelap, teks terang '
                                    // ],
                                ]
                            ]
                        ],
                    ],

                ],
                'asesmenSumatif' => [
                    'type' => 'OBJECT',
                    'description' => 'Asesmen sumatif',
                    'required' => ['daftarJenisSoal','nilaiAkhir','alokasiWaktu'],
                    'properties' => [
                        'daftarJenisSoal' => [
                            'type' => 'ARRAY',
                            'description' => 'Daftar kelompok soal yang dipecah per jenis asesmen, baik asesmen diagnostic, formatif atau sumatif',
                            'items' => [
                                'type' => 'OBJECT',
                                'required' => [
                                    'kodeJenisSoal', 'namaJenisSoal','soalHtml','penilaian'
                                ],
                                'properties' => [
                                    'kodeJenisSoal' => [
                                        'type' => 'STRING',
                                        'description' => 'Identifier jenis soal (misal: pilihan_ganda, uraian, tugas_praktik).'
                                    ],
                                    'namaJenisSoal' => [
                                        'type' => 'STRING',
                                        'description' => 'Label nama jenis soal (misal: Bagian I: Pilihan Ganda).'
                                    ],
                                    'soalHtml' => [
                                        'type' => 'STRING',
                                        'description' => 'soal atau instruksi, beserta kunci jawaban dalam HTML. berikan style dalam bentuk tailwind. table border-collapse semua, line page sama semua, font 12px saja. Pilihan ganda jawabannya dibuat jadi 2 kolom. Praktik buat instruksi dalam bentuk list. Jumlah soal sesuai request prompt'
                                    ],
                                    'penilaian' => [
                                        'type' => 'STRING',
                                        'description' => 'Dalam HTML. beri style dengan tailwind. Kalau praktik tampilkan rubik penilaian, kalau soal tampilkan nilai per nomor. kalau tabel penilaian pake border, jangan lupa tambah class border-solid, buat 4 kategori. Kalau soal beri nilai setiap nomornya, tabel diberi background gelap, teks terang, padding vertikal jangan lupa '
                                    ],
                                ]
                            ]
                        ],
                        'alokasiWaktu' => [
                            'type' => 'STRING',
                            'description' => 'Alokasi waktu pengerjaan asesmen',
                        ],
                        'nilaiAkhir' => [
                            'type' => 'STRING',
                            'description' => 'Dalam HTML, perhitungan tampilkan dalam bentuk rumus, masing masing soal di asesmen sumatif dikali proporsi nilai. beri style dengan tailwind. penilaian akhir berdasarkan total nilai dalah daftar soal, dan kriteria lulus dan tidak lulus. background gelap dan teks terang'
                        ]
                    ]
                ],
            ]
        ];

        // Otomatis me-return Array PHP terstruktur
        $hasil = $gemini->generate($prompt, null, $schema);
        // Akses: $hasil['nama_lowongan']
        // var_dump($hasil);
        return $this->respondCreated($hasil);
    }
}