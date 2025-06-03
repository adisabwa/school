<?php

namespace Modules\Data\Controllers;

use App\Controllers\BaseDataController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;

class GroupController extends BaseDataController
{
    private $modelAnggota;

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('GroupModel');
        $this->modelAnggota = model('GroupAnggotaModel');
    }

    public function index()
    {
        $where = $this->request->getGetPost('where') ?? [];
        $or = $this->request->getGetPost('or') ?? [];
        $order = $this->request->getGetPost('order') ?? [];
        $limit = $this->request->getGetPost('limit') ?? 5;
        $offset = $this->request->getGetPost('offset') ?? 0;

        $order = implode(",", $order);

        $data = $this->model->getAll($where, $or, 'type desc, nama asc', $limit, $offset);
        // var_dump($this->model->getLastQuery());

        return $this->respondCreated($this->grouping_data($data));
    }
    
    public function get()
    {
        $id = $this->request->getGet('id');
        $data = $this->model->find($id);
        if (!empty($data))
        $data->mu_group_anggota = array_map(function($val){
            return (object)[
                'id_anggota'    => $val->id_anggota,
                'type'    => $val->type,
            ];
        }, $this->modelAnggota->getAll(['id_group' => $id]) );

        return $this->respondCreated(($data));
    }

    public function grouping_data($data)
    {
        $results = [];
        foreach ($data as $key => $d) {
            $ind = md5($d->id);
            $d->checked = false;
            if (empty($results[$ind])) {
                $results[$ind] = (object) [
                    'id' => $d->id,
                    'nama_group' => $d->nama_group,
                    'anggota' => [],
                    'show'  => false,
                ];
            }
            $results[$ind]->anggota[] = (object) [
                'id_group' => $d->id_group,
                'id_anggota' => $d->id_anggota,
                'type' => $d->type,
                'nama' => $d->nama,
            ];
        }
        return array_values($results);
    }

    public function get_anggota()
    {
        $user = userdata();
        $id = $this->request->getGetPost('id') ?? userdata()->id_anggota;
        $data = $this->modelAnggota->where(['id_anggota' => $id,'type' => 'mentor'])->find()[0] ?? [];
        $datas = [];
        
        if ($user->role == 'super-admin')
            $where_group = "1=1";
        else if ($user->role == 'admin')
            $where_group = "id_unit='$user->id_unit'" ; 
        else
            $where_group = "id_group='$user->id_group'";
        
        if (!empty($data)) {
            $datas = $this->modelAnggota->getAll([
                $where_group => NULL
            ]);
        }

        return $this->respondCreated($datas);
    }

    public function template()
    {
        $filename = 'TEMPLATE-UPLOAD-DATA-PENGHASILAN';
        // var_dump($filename);exit;
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
                    ->setCreator('Codev-App')
                    ->setTitle('Finance App');
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->setCellValue('A1', 'No');
        $spreadsheet->getActiveSheet()->setCellValue('B1', 'Label Prm');
        $spreadsheet->getActiveSheet()->setCellValue('C1', 'Rentang Awal');
        $spreadsheet->getActiveSheet()->setCellValue('D1', 'Rentang Akhir');
        $spreadsheet->getActiveSheet()->setCellValue('A2', 'Cth');
        $spreadsheet->getActiveSheet()->setCellValue('B2', 'Gol. 2');
        $spreadsheet->getActiveSheet()->setCellValue('C2', '0');
        $spreadsheet->getActiveSheet()->setCellValue('D2', '1000000');

        for ($i = 'A'; $i !=  $spreadsheet->getActiveSheet()->getHighestColumn(); $i++) {
            $spreadsheet->getActiveSheet()->getColumnDimension($i)->setAutoSize(TRUE);
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        // ob_end_clean();
        $writer->save('php://output');
    }

    public function upload()
    {
        $validationRule = [
            'file' => [
                'uploaded[file]',
                'max_size[file,2048]',
                'ext_in[file,xls,xlsx]',
            ],
        ];

        if (! $this->validate($validationRule)) {
            return $this->fail($this->validator->getErrors());
        }

        $file = $this->request->getFile('file');
        // var_dump($file);exit;
        if ($file->hasMoved()) {
            return $this->fail(['file' => 'The file has already been moved.']);
        }

        $filename = WRITEPATH . 'uploads/' . $file->store();
        // $file = new File($filename);
        // var_dump($filename);exit;
        /**  Identify the type of $inputFileName  **/
        $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($filename);
        /**  Create a new Reader of the type that has been identified  **/
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        $spreadsheet = $reader->load($filename);
        // var_dump($inputFileType, $reader, $spreadsheet);exit;
        // $spreadsheet->setActiveSheetIndexByName('Sheet1');
        $sheet = $spreadsheet->getActiveSheet();
        $data_pegnghasilan = [];
        $error_pegnghasilan = [];
        $row = 2;
        $save = true;
        $no = $sheet->getCell('A'.$row)->getValue();
        // var_dump($no);exit;
        while (!empty($no)) {
            $this->validation->reset();

            $this->validation->setRule('label', 'Label', 'required');
            $this->validation->setRule('dari', 'Rentang Awal', 'required');
            $this->validation->setRule('hingga', 'Rentang Akhir', 'required');

            $data = [
                'label'      => trim($sheet->getCell('B'.$row)->getValue()),
                'dari'   => trim($sheet->getCell('C'.$row)->getValue()),
                'hingga'     => trim($sheet->getCell('D'.$row)->getValue()),
            ];

            $row++;
            $no = $sheet->getCell('A'.$row)->getValue();

            // var_dump($data);
            // var_dump(in_array($data['no_id'],['-','','+',NULL]));
            if ($this->validation->run($data)) {
                $check = $this->pelanggaranModel->where($data)->first();

                if (!empty($check)) {
                    $data["created_by"] =  userdata()->id;
                    $save = $this->pelanggaranModel->update($check['id'], $data);
                    $data['id'] = $check['id'];

                } else {
                    $save = $this->pelanggaranModel->insert($data);
                    $data['id'] = $this->pelanggaranModel->insertID();
                }
                $data_pegnghasilan[] = $data;
            } else {
                $data['error'] = implode(', ', $this->validation->getErrors());
                $error_pegnghasilan[] = $data;
            }
            // Your idea go here...
        }
        unlink($filename);
        array_walk($error_pegnghasilan, function(&$val) {
            $val['keterangan'] = $val['label'];
        });
        // return sendInternalServerError();
        if ($save) return $this->respondCreated(['data' => $data_pegnghasilan,'error' => $error_pegnghasilan]);
        else return $this->failServerError();
    }
}
