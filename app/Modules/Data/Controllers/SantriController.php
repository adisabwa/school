<?php

namespace Modules\Data\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;
use App\Controllers\BaseDataController;

class SantriController extends BaseDataController
{
    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('SantriModel');
    }

    public function search()
    {
        $whereAnd = $this->request->getGet('and') ?? [];
        $whereOr = $this->request->getGet('or') ?? ['1=1'];
        $order = $this->request->getGet('order') ?? [];
        $order = implode(",", $order);

        $data = $this->model->getAll($whereAnd, $whereOr, $order)[0] ?? [];

        // var_dump($this->model->db->getLastQuery());
        return $this->respondCreated($data);
    }

    public function kelas()
    {
        return $this->respondCreated($this->model->getKelas());
    }

    public function template()
    {
        $filename = 'TEMPLATE-UPLOAD-DATA-SANTRI';
        // var_dump($filename);exit;
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
                    ->setCreator('Codev-App')
                    ->setTitle('Finance App');
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->setCellValue('A1', 'No');
        $spreadsheet->getActiveSheet()->setCellValue('B1', 'Nama Santri');
        $spreadsheet->getActiveSheet()->setCellValue('C1', 'Tingkat');
        $spreadsheet->getActiveSheet()->setCellValue('D1', 'Kelas');
        $spreadsheet->getActiveSheet()->setCellValue('A2', 'Cth');
        $spreadsheet->getActiveSheet()->setCellValue('B2', 'Adi Sabwa');
        $spreadsheet->getActiveSheet()->setCellValue('C2', '1');
        $spreadsheet->getActiveSheet()->setCellValue('D2', '1A');

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
        $data_santri = [];
        $error_santri = [];
        $row = 2;
        $save = true;
        $no = $sheet->getCell('A'.$row)->getValue();
        // var_dump($no);exit;
        while (!empty($no)) {
            $this->validation->reset();

            $this->validation->setRule('nama', 'Nama', 'required');
            $this->validation->setRule('tingkat', 'Tingkat', 'required');
            $this->validation->setRule('kelas', 'Kelas', 'required');

            $data = [
                'nama'      => trim($sheet->getCell('B'.$row)->getValue()),
                'tingkat'   => trim($sheet->getCell('C'.$row)->getValue()),
                'kelas'     => trim($sheet->getCell('D'.$row)->getValue()),
            ];

            $row++;
            $no = $sheet->getCell('A'.$row)->getValue();

            // var_dump($data);
            // var_dump(in_array($data['no_id'],['-','','+',NULL]));
            if ($this->validation->run($data)) {
                $check = $this->santriModel->where($data)->first();

                if (!empty($check)) {
                    $data["created_by"] =  userdata()->id;
                    $save = $this->santriModel->update($check['id'], $data);
                    $data['id'] = $check['id'];

                } else {
                    $save = $this->santriModel->insert($data);
                    $data['id'] = $this->santriModel->insertID();
                }
                $data_santri[] = $data;
            } else {
                $data['error'] = implode(', ', $this->validation->getErrors());
                $error_santri[] = $data;
            }
            // Your idea go here...
        }
        unlink($filename);
        array_walk($error_santri, function(&$val) {
            $val['keterangan'] = $val['nama'];
        });
        // return sendInternalServerError();
        if ($save) return $this->respondCreated(['data' => $data_santri,'error' => $error_santri]);
        else return $this->failServerError();
    }
}