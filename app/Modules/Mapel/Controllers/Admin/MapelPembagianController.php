<?php

namespace Modules\Mapel\Controllers\Admin;

use App\Controllers\BaseDataController;

class MapelPembagianController extends BaseDataController
{
    public $santriModel;

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('MapelPembagianModel');
    }

    public function options_penjadwalan()
    {
        $where = $this->request->getGet('where') ?? [];
        // var_dump($where);
        return $this->respondCreated($this->model->getOptionsPenjadwalan($where));
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

        $id_semester = $this->request->getGetPost('id_semester') ?? model('DataSemesterModel')->getSemesterNow()->id ?? '';
        $kelas_opt = model('DataKelasModel')->getOptions();
        $guru_opt = model('DataGuruModel')->getOptions();
        $mapel_opt = model('MapelModel')->getOptions();

        $filename = WRITEPATH . 'uploads/' . $file->store();

        $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filename);
        /**  Create a new Reader of the type that has been identified  **/
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($filename);
        // var_dump($inputFileType, $reader, $spreadsheet);exit;
        // $spreadsheet->setActiveSheetIndexByName('Sheet1');
        $sheet = $spreadsheet->getActiveSheet();
        $classes = [];
        $start = 'G';
        $cols = [];
        $kelas = $sheet->getCell($start."7")->getValue();
        while (!empty($kelas)) {
            $cols[] = $start;
            $id_kelas = getValueFromOption($kelas, $kelas_opt);
            $classes[$start] = $id_kelas;
            $start++;
            $kelas = $sheet->getCell($start."7")->getValue();
        }

        $lastRow = $sheet->getHighestRow();
        $schedules = [];
        $prev_guru = '';
        $prev_kode = '';
        for ($start_row=8; $start_row < $lastRow; $start_row++) { 
            $guru = $sheet->getCell("C$start_row")->getValue();
            if (empty($guru))
                $id_guru = $prev_guru;
            else
                $id_guru = $prev_guru = getValueFromOption($guru, $guru_opt);

            $kode = $sheet->getCell("D$start_row")->getValue();
            if (empty($kode))
                $kode = $prev_kode;
            else
                $prev_kode = $kode;
            
            $no_kode = $sheet->getCell("E$start_row")->getValue();
            $kode = $kode.$no_kode;

            $mapel = $sheet->getCell("F$start_row")->getValue();
            if (empty($mapel))
                continue;
            else
                $id_mapel = getValueFromOption($mapel, $mapel_opt);

            $sch = [
                'id_semester' => $id_semester,
                'id_guru' => $id_guru,
                'kode_mapel' => $kode,
                'id_mapel' => $id_mapel,
            ];

            foreach($cols as $col){
                $jam = $sheet->getCell("$col$start_row")->getValue();
                if (empty($jam)) continue;
                $schedules[] = [...$sch,
                    ...[
                        'id_kelas' => $classes[$col],
                        'jam'      => $jam,
                    ]
                ];
            }
        }

        $this->model->transBegin();
        
        $result = [];
        $error = [];
        foreach ($schedules as $key => $value) {
            $save = $this->model->insert($value);
            if (!$save)
                $error[] = [$value, $this->model->error()];
        }
        // var_dump($schedules);
        // var_dump(count($schedules), $error);
        if ($this->model->transStatus() === false) {
            $this->model->transRollback();
            return $this->failServerError();
        } else {
            $this->model->transCommit();
            return $this->respondCreated($schedules);
        }
    }
}