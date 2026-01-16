<?php

namespace Modules\Mapel\Controllers\Admin;

use App\Controllers\BaseDataController;

class MapelPenjadwalanController extends BaseDataController
{
    public $detailModel;

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('MapelPenjadwalanModel');
    }

    public function index($return = false)
    {
        $data = parent::index(true);
        // var_dump($this->model->getLastQuery());
        array_walk($data, function($a){
            $a->date = nextDateByDay($a->hari);
        });
        $hariIndex = [
            'sabtu'  => 1,
            'ahad'   => 2,
            'senin'  => 3,
            'selasa' => 4,
            'rabu'   => 5,
            'kamis'  => 6,
            'jumat'  => 7,
        ];

        $mapHari = [
            'saturday' => 'sabtu',
            'sunday'   => 'ahad',
            'monday'   => 'senin',
            'tuesday'  => 'selasa',
            'wednesday'=> 'rabu',
            'thursday' => 'kamis',
            'friday'   => 'jumat',
        ];

        $todayName  = $mapHari[strtolower(date('l'))];
        $todayIndex = $hariIndex[$todayName];
        $now = date('H:i');

        usort($data, function ($a, $b) use ($hariIndex, $todayIndex, $now) {

            $aDay = $hariIndex[$a->hari];
            $bDay = $hariIndex[$b->hari];

            // cek apakah sudah lewat waktu_selesai (HARI INI)
            $aExpired = ($aDay === $todayIndex && $now > $a->waktu_selesai);
            $bExpired = ($bDay === $todayIndex && $now > $b->waktu_selesai);

            /*
            GROUP PRIORITY
            0 = hari ini & BELUM lewat waktu_selesai
            1 = hari setelah hari ini
            2 = hari sebelum hari ini
            3 = hari ini & SUDAH lewat waktu_selesai (PALING AKHIR)
            */
            $aGroup = 3;
            $bGroup = 3;

            if ($aDay === $todayIndex && !$aExpired) {
                $aGroup = 0;
            } elseif ($aDay > $todayIndex) {
                $aGroup = 1;
            } elseif ($aDay < $todayIndex) {
                $aGroup = 2;
            }

            if ($bDay === $todayIndex && !$bExpired) {
                $bGroup = 0;
            } elseif ($bDay > $todayIndex) {
                $bGroup = 1;
            } elseif ($bDay < $todayIndex) {
                $bGroup = 2;
            }

            if ($aGroup !== $bGroup) {
                return $aGroup <=> $bGroup;
            }

            // urutkan hari untuk group 1 & 2
            if (($aGroup === 1 || $aGroup === 2) && $aDay !== $bDay) {
                return $aDay <=> $bDay;
            }

            // urutkan waktu mulai
            return
                ($a->waktu_mulai <=> $b->waktu_mulai)
                ?: ($a->kelas <=> $b->kelas)
                ?: ($a->id_sesi <=> $b->id_sesi);
        });

        return $this->respondCreated($data);
    }

    public function upload()
    {
        $validationRule = [
            'file' => [
                'uploaded[file]',
                'max_size[file,12048]',
                'ext_in[file,xls,xlsx]',
            ],
        ];

        if (!$this->validate($validationRule)) {
            return $this->fail($this->validator->getErrors());
        }

        $file = $this->request->getFile('file');
        // var_dump($file);exit;
        if ($file->hasMoved()) {
            return $this->fail(['file' => 'The file has already been moved.']);
        }

        $sesi = model('DataSesiModel')->getAll();
        $sesi_opt = [];
        foreach ($sesi as $key => $value) {
            if (is_numeric($value->sesi))
                $sesi_opt[$value->sesi] = $value->id;
        }
        $mapelPembagian = model('MapelPembagianModel')->getAll(whereAnd:[
            'id_semester' => model('DataSemesterModel')->getSemesterNow()->id,
        ]);
        $pem_opt = [];
        foreach ($mapelPembagian as $key => $value) {
            $pem_opt["$value->kode_mapel-$value->kelas"] =  $value->id;
        }

        $filename = WRITEPATH . 'uploads/' . $file->store();

        $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filename);
        /**  Create a new Reader of the type that has been identified  **/
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($filename);
        // var_dump($inputFileType, $reader, $spreadsheet);exit;
        // $spreadsheet->setActiveSheetIndexByName('Sheet1');
        $sheet = $spreadsheet->getActiveSheet();

        $highestRow = $sheet->getHighestRow();
        $highestCol = "";

        $result = [];
        $currentHari = null;
        $kelasHeader = [];

        // var_dump($pem_opt);
        // return $this->failServerError();
        
        for ($row = 25; $row <= $highestRow; $row++) {
            $cellA = trim((string)$sheet->getCell("A$row")->getValue());
            $cellB = trim((string)$sheet->getCell("B$row")->getValue());

            /** 1️⃣ Deteksi NAMA HARI */
            if (preg_match('/^(SENIN|SELASA|RABU|KAMIS|JUMAT|SABTU|AHAD)$/i', $cellA)) {
                $currentHari = strtoupper($cellA);
                $kelasHeader = [];
                continue;
            }

            /** 2️⃣ Ambil HEADER KELAS */
            if ($cellB === 'Waktu' && $currentHari) {
                $col = 'C';
                do {
                    $kelas = trim((string)$sheet->getCell("$col$row")->getValue());
                    $kelasHeader[$col] = $kelas;
                    $col++;
                } while(!empty($kelas));
                array_pop($kelasHeader);
                $highestCol = --$col;
                continue;
            }

            /** 3️⃣ Data jadwal */
            if ($currentHari && !empty($kelasHeader)) {

                // skip kosong / istirahat
                if ($cellA === '' || stripos($cellA, 'ISTIRAHAT') !== false) {
                    continue;
                }

                foreach ($kelasHeader as $col => $kelas) {
                    $kode = trim((string)$sheet->getCell("$col$row")->getValue());

                    $result[] = [
                        'hari'  => strtolower($currentHari),
                        'id_sesi'  => $sesi_opt[$cellA],
                        'waktu' => $cellB,
                        'kelas' => $kelas,
                        'kode'  => $kode,
                        'jam'   => 1,
                    ];
                }
            }
        }
        $hariIndex = array_flip(['sabtu', 'ahad', 'senin', 'selasa', 'rabu', 'kamis', 'jumat']);
        usort($result, fn($a, $b) =>
            [$hariIndex[$a['hari']], $a['kelas'], $a['id_sesi']] 
            <=>
            [$hariIndex[$b['hari']], $b['kelas'], $b['id_sesi']]
        );

        $merging = [];
        $prev = [
            'hari' => NULL,
            'kode' => NULL,
        ];
        $ind = -1;
        foreach ($result as $key => $value) {
            $check = $prev['kode'] != $value['kode'];
            if ($check || (!$check && $prev['hari'] != $value['hari'])){
                $ind++;
                $merging[$ind] = [
                    'hari'                  => $value['hari'],
                    'id_sesi'               => $value['id_sesi'],
                    'jam'                   => $value['jam'],
                    'id_pembagian_mapel'    => $pem_opt[($value['kode']."-".$value['kelas'])] ?? NULL,
                    'kode_mapel'            => $value['kode'],
                ];
                $prev = $value;
            } else {
                $merging[$ind]['jam'] += 1;
            }
        }

        // var_dump($merging);
        // return $this->failServerError();

        $this->model->transBegin();
        
        $this->model->truncate();
        foreach ($merging as $key => $value) {
            $save = $this->model->insert($value);
        }
        // var_dump($schedules);
        // var_dump(count($schedules), $error);
        if ($this->model->transStatus() === false) {
            $this->model->transRollback();
            return $this->failServerError();
        } else {
            $this->model->transCommit();
            return $this->respondCreated($result);
        }
    }
}