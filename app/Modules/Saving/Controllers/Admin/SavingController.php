<?php

namespace Modules\Saving\Controllers\Admin;

use App\Controllers\BaseDataController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;

class SavingController extends BaseDataController
{
    public $santriModel;

    public function __construct()
    {
        parent::__construct();
        
        $this->model = model('SavingModel');
        $this->santriModel = model('SantriModel');
    }

    public function template()
    {
        $filename = 'TEMPLATE-UPLOAD-DATA-KEUANGAN';
        // var_dump($filename);exit;
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
                    ->setCreator('Codev-App')
                    ->setTitle('Finance App');
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
        $santris = $this->santriModel->findAll();
        $_santris = [];
        foreach ($santris as $key => $value) {
            $_santris[$value['id']] = $value['nama'];
        }
        $data_finance = [];
        $error_finance = [];
        $row = 2;
        $save = true;
        $no = $sheet->getCell('A'.$row)->getValue();
        // var_dump($no);exit;
        while (!empty($no)) {
            $this->validation->reset();

            $this->validation->setRule('id_santri', 'Santri', 'required');
            $this->validation->setRule('keterangan', 'Keterangan', 'required');
            $this->validation->setRule('nominal', 'Nominal', 'required');
            $this->validation->setRule('tanggal', 'Tanggal', 'required');

            $nama = trim($sheet->getCell('B'.$row)->getValue());
            $debit = trim($sheet->getCell('D'.$row)->getValue());
            $kredit = trim($sheet->getCell('E'.$row)->getValue());
            $tanggal = trim($sheet->getCell('F'.$row)->getValue());
            if (empty($tanggal) || $tanggal == '0000-00-00')
                $tanggal = date('Y-m-d');
            $id_santri = array_search($nama, $_santris);
            if (empty($debit) || $debit == '-') {
                $jenis = '1';
                $nominal = $kredit;
            } else {
                $jenis = '-1';
                $nominal = $debit;
            }
            $tanggal = date('Y-m-d', strtotime($tanggal));
            $data = [
                'id_santri' => $id_santri,
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
                $save = $this->model->insert($data);
                $data['id'] = $this->model->insertID();
                $data_finance[] = $data;
            } else {
                $data['error'] = implode(', ', $this->validation->getErrors());
                $data['nama'] = $nama;
                $error_finance[] = $data;
            }
            // Your idea go here...
        }
        unlink($filename);
        array_walk($error_finance, function(&$val) {
            $val['keterangan'] = $val['nama'];
        });
        // return sendInternalServerError();
        if ($save) return $this->respondCreated(['data' => $data_finance,'error' => $error_finance]);
        else return $this->failServerError();
    }


}