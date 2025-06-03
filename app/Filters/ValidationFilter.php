<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services; // Import Services
use App\Models\KolomModel;

class ValidationFilter implements FilterInterface
{

    public function before(RequestInterface $request, $params = null)
    {
        if (is_array($params)){
            $table = $params[0];
        }

        $validation = Services::validation();

        $model = new KolomModel();

        $datas = $model->getAll($table);
        // $koloms = [];
        $postData = $request->getPost();
        $id = $postData['id'] ?? -1;
        unset($postData['id']);
        $files_data = $_FILES;
        $folders = [];
        $validationRule = [];

        $validationRule = $this->setValidation('', $datas, $postData, $files_data, $model, $id, $validationRule);

        // var_dump($validationRule);
            // return failValidationErrors([]);
        if (empty($validationRule))
            return TRUE;

        if (!$validation->setRules($validationRule)->run($postData)) {
            return Services::response()->setJSON([
                'status' => '400',
                'error' => '400',
                'messages' => $validation->getErrors()
            ])->setStatusCode(400); // Bad Request
        }

        $postData = $request->getPost();
        $postData = $this->groupingData($datas, $postData, $model, $folders);
        
        // var_dump($folders);
        
        foreach ($_FILES as $inputName => $fileData) {
            // Get the file object
            $file = $request->getFile($inputName);
            if ($file->isValid() && !$file->hasMoved()) {
                $uploadPath = WRITEPATH . "uploads/$folders[$inputName]";// Ensure this directory exists with the correct permissions
                // Move the file to the upload folder
                $newName = $file->getRandomName(); // Generates a unique name
                $file->move($uploadPath, $newName);
                $postData[$inputName] = base_url('/get-files?file=uploads/'.$folders[$inputName])."/$newName";
            } else {
                var_dump($file->getErrorString());
            }
        }
        
        $postData['id'] = $id;
        // array_walk($postData, function(&$value) {
        //     if (empty($value))
        //         $value = NULL;
        // });
        $newRequest = $request->setGlobal('post', $postData);
        // var_dump($postData);
        return $newRequest;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }

    //Fungsi membuat validation rule
    //prev = prefix untuk array, $datas = Kolom tabel, $postData = data post, $files_data = data_file, $model = Kolom Model, $id = ID utama, untuk bypass pas update
    public function setValidation($prev, $datas, $postData, $files_data, $model, $id)
    {
        $validationRule = [];

        foreach ($datas as $key => $data) {
            $nama = $data->nama_kolom;
            $nama_rule = $prev.$data->nama_kolom;
            $label = $data->label;
            if (isset($postData[$nama])) {
                 if (is_array($postData[$nama])) {
                     $kolom_child = $model->getAll($nama);
                     foreach($postData[$nama] as $ind => $element) {
                        $prev = "$nama".".$ind.";
                        $validationRule = [...$validationRule, ...$this->setValidation($prev, $kolom_child, $postData[$nama][$ind], $files_data[$nama][$ind] ?? [], $model, $id)];
                     }
                 } else {
                     $validationRule[$nama_rule] = [
                         'label' => $label,
                         // 'rules' => 'even'
                         'rules' => ($data->required == '1' ? 'required' : 'permit_empty').(empty($data->rules) ? '' : '|'.$data->rules),
                     ]; 
                     // var_dump("{id}",$id,$validationRule[$nama]['rules']);
                     // $koloms[] = $data;
                    $validationRule[$nama_rule]['rules'] = str_replace("{id}", $id,$validationRule[$nama_rule]['rules']);
                    $validationRule[$nama_rule]['rules'] = str_replace("{field}", $nama_rule,$validationRule[$nama_rule]['rules']);
                 }
            } else if (isset($files_data[$nama])) {
                 $validationRule[$nama_rule] = [
                     'label' => $label,
                     'rules' => "uploaded[$nama]|max_size[$nama,5120]|mime_in[$nama,image/jpg,image/jpeg,image/png,application/pdf]",
                     'errors' => [
                         'uploaded' => 'Anda harus memilih file yang akan diupload.',
                         'max_size' => 'Ukuran file tidak boleh lebih dari 5MB.',
                         'mime_in'  => 'Hanya File JPG, JPEG, PNG atau PDF yang dapat diupload.',
                     ],
                 ];
            }

        }
        return $validationRule;
    }

    public function groupingData($datas, $postData, $model, &$folders)
    {
        helper('functions'); 

        $files_data = $_FILES;

        $tmp_folder = [];
        $double_input = [];
        $input_only = [];
        $tables = [];
        $nama_fk = [];
        $func = [];
        foreach ($datas as $key => $data) {
            $nama = $data->nama_kolom;
            $label = $data->label;
            if (isset($postData[$nama])) {
                 if (is_array($postData[$nama])) {
                    $tables[$nama] = $postData[$nama];
                    $nama_fk[$nama] = $data->nama_fk;
                    $kolom_child = $model->getAll($nama);
                    foreach ($postData[$nama] as $ind => &$elements) {
                        $elements = $this->groupingData($kolom_child, $elements, $model, $folders);
                    }
                 } else {
                     if (str_contains($data->input,'select-double'))
                         $double_input[$nama] = $postData[$nama];
 
                     if ($data->input_only == '1')
                         $input_only[$nama] = $postData[$nama];
 
                     if (!empty($data->function_submit)) {
                         $func[$nama] = $data->function_submit;
                     }
                 }
            } else if (isset($files_data[$nama])) {
                 $folders[$nama] = $data->folder;
            }
        }

        // var_dump($func);
        foreach ($func as $nama_kolom => $f) {
            $postData[$nama_kolom] = $f($postData[$nama_kolom]);
        }

        foreach ($double_input as $nama_kolom => $data) {
            $koloms = explode('-', $nama_kolom);
            $datas = explode('-', $data);
            foreach ($koloms as $key => $kolom) {
                $postData[$kolom] = $datas[$key] ?? '';
            }
            unset($postData[$nama_kolom]);
        }
        
        foreach ($tables as $nama_kolom => $data) {
            $postData['tables'][$nama_kolom] = $data;
            $postData['nama_fk'][$nama_kolom] = $nama_fk[$nama_kolom];
            unset($postData[$nama_kolom]);
        }

        foreach ($input_only as $nama_kolom => $data) {
            unset($postData[$nama_kolom]);
        }
        // var_dump($postData);
        return $postData;
    }
}