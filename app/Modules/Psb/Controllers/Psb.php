<?php

namespace Modules\Psb\Controllers;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;
use PhpParser\Node\Expr\AssignOp\Coalesce;
use Config\Upload;
use stdClass;

class Psb extends BaseController
{
    private $psbModel;
    private $kolomModel;

    public function __construct()
    {
        
        $this->psbModel = model('PsbModel');
    }

    public function get()
    {
        $where = $this->request->getGet();
        return $this->respondCreated(empty($where) ? [] : $this->psbModel->where($where)->find()[0] ?? []);
    }

    public function search()
    {
        $whereAnd = $this->request->getGet('and') ?? [];
        $whereOr = $this->request->getGet('or') ?? [];
        $data = $this->psbModel->getData($whereAnd, $whereOr);

        // var_dump($this->psbModel->db->getLastQuery());
        return $this->respondCreated($data);
    }

    public function store()
    {
        $data_db = $this->request->getPost();
        $files_data = $_FILES;

        foreach ($_FILES as $inputName => $fileData) {
            if ($fileData['error'] === UPLOAD_ERR_OK) {
                // Get the file object
                $file = $this->request->getFile($inputName);
                if ($file->isValid() && !$file->hasMoved()) {
                    $uploadPath = WRITEPATH . 'uploads/psb'; // Ensure this directory exists with the correct permissions

                    // Move the file to the upload folder
                    $newName = $file->getRandomName(); // Generates a unique name
                    $file->move($uploadPath, $newName);
                    $data_db[$inputName] = base_url('/get-files?file=uploads/psb/').$newName;
                } else {
                    var_dump($file->getErrorString());
                }
            }
        }

        // var_dump($data_db);
        if ($data_db['id'] > 0) {
            $save = $this->psbModel->update($data_db['id'], $data_db);
        } else {
            $save = $this->psbModel->insert($data_db);
            $data_db['id'] = $this->psbModel->insertID();
        }
        // Append ID to data
        if ($save) {
            return $this->respondCreated($data_db);
        } else {
            return $this->failServerError();
        }
    }

    public function template()
    {
        $filename = 'TEMPLATE-UPLOAD-DATA-KEUANGAN';
        // var_dump($filename);exit;
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
                    ->setCreator('Codev-App')
                    ->setTitle('Psb App');
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getActiveSheet()->setCellValue('A1', 'No');
        $spreadsheet->getActiveSheet()->setCellValue('B1', 'Nama Santri');
        $spreadsheet->getActiveSheet()->setCellValue('C1', 'Keterangan');
        $spreadsheet->getActiveSheet()->setCellValue('D1', 'Pengeluaran');
        $spreadsheet->getActiveSheet()->setCellValue('E1', 'Pemasukan');
        $spreadsheet->getActiveSheet()->setCellValue('F1', 'Tanggal');
        $spreadsheet->getActiveSheet()->setCellValue('A2', 'Cth');
        $spreadsheet->getActiveSheet()->setCellValue('B2', 'Adi Sabwa');
        $spreadsheet->getActiveSheet()->setCellValue('C2', 'Keterangan Pemasukan / Pengeluaran');
        $spreadsheet->getActiveSheet()->setCellValue('D2', '-');
        $spreadsheet->getActiveSheet()->setCellValue('E2', '100000');
        $spreadsheet->getActiveSheet()->setCellValue('F2', date('Y-m-d'));


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
        $koloms = $this->kolomModel->findAll();
        $_koloms = [];
        foreach ($koloms as $key => $value) {
            $_koloms[$value['id']] = $value['nama'];
        }
        $data_psb = [];
        $error_psb = [];
        $row = 2;
        $save = true;
        $no = $sheet->getCell('A'.$row)->getValue();
        // var_dump($no);exit;
        while (!empty($no)) {
            $this->validation->reset();

            $this->validation->setRule('id_kolom', 'Santri', 'required');
            $this->validation->setRule('keterangan', 'Keterangan', 'required');
            $this->validation->setRule('nominal', 'Nominal', 'required');
            $this->validation->setRule('tanggal', 'Tanggal', 'required');

            $nama = trim($sheet->getCell('B'.$row)->getValue());
            $debit = trim($sheet->getCell('D'.$row)->getValue());
            $kredit = trim($sheet->getCell('E'.$row)->getValue());
            $tanggal = trim($sheet->getCell('F'.$row)->getValue());
            if (empty($tanggal) || $tanggal == '0000-00-00')
                $tanggal = date('Y-m-d');
            $id_kolom = array_search($nama, $_koloms);
            if (empty($debit) || $debit == '-') {
                $jenis = '1';
                $nominal = $kredit;
            } else {
                $jenis = '-1';
                $nominal = $debit;
            }
            $tanggal = date('Y-m-d', strtotime($tanggal));
            $data = [
                'id_kolom' => $id_kolom,
                'keterangan'   => trim($sheet->getCell('C'.$row)->getValue()),
                'nominal'   => $nominal,
                'jenis' => $jenis,
                'tanggal' => $tanggal,
                "created_by"  => userdata()->id,
            ];

            $row++;
            $no = $sheet->getCell('A'.$row)->getValue();

            // var_dump($data);
            // var_dump(in_array($data['no_id'],['-','','+',NULL]));
            if ($this->validation->run($data)) {
                $save = $this->psbModel->insert($data);
                $data['id'] = $this->psbModel->insertID();
                $data_psb[] = $data;
            } else {
                $data['error'] = implode(', ', $this->validation->getErrors());
                $data['nama'] = $nama;
                $error_psb[] = $data;
            }
            // Your idea go here...
        }
        unlink($filename);
        array_walk($error_psb, function(&$val) {
            $val['keterangan'] = $val['nama'];
        });
        // return sendInternalServerError();
        if ($save) return $this->respondCreated(['data' => $data_psb,'error' => $error_psb]);
        else return $this->failServerError();
    }


}