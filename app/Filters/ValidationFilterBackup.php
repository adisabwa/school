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
        helper('functions'); 

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
        $double_input = [];
        $input_only = [];
        $func = [];
        foreach ($datas as $key => $data) {
           $nama = $data->nama_kolom;
           $label = $data->label;
           if (isset($postData[$nama])) {
                $validationRule[$nama] = [
                    'label' => $label,
                    // 'rules' => 'even'
                    'rules' => ($data->required == '1' ? 'required' : 'permit_empty').(empty($data->rules) ? '' : '|'.$data->rules),
                ];
                if ($data->input == 'select-double')
                    $double_input[$nama] = $postData[$nama];

                if ($data->input_only == '1')
                    $input_only[$nama] = $postData[$nama];

                if (!empty($data->function_submit)) {
                    $func[$nama] = $data->function_submit;
                }

                $validationRule[$nama]['rules'] = str_replace("{id}",$id,$validationRule[$nama]['rules']);
                // var_dump("{id}",$id,$validationRule[$nama]['rules']);
                // $koloms[] = $data;
           } else if (isset($files_data[$nama])) {
                $validationRule[$nama] = [
                    'label' => $label,
                    'rules' => "uploaded[$nama]|max_size[$nama,5120]|mime_in[$nama,image/jpg,image/jpeg,image/png,application/pdf]",
                    'errors' => [
                        'uploaded' => 'Anda harus memilih file yang akan diupload.',
                        'max_size' => 'Ukuran file tidak boleh lebih dari 5MB.',
                        'mime_in'  => 'Hanya File JPG, JPEG, PNG atau PDF yang dapat diupload.',
                    ],
                ];
                $folders[$nama] = $data->folder;
           }
        }
        var_dump($validationRule, $postData);
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

        foreach ($input_only as $nama_kolom => $data) {
            unset($postData[$nama_kolom]);
        }

        $postData['id'] = $id;
        $newRequest = $request->setGlobal('post', $postData);
        return $newRequest;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}