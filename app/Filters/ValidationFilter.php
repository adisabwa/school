<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services; // Import Services
use App\Libraries\Fields;

class ValidationFilter implements FilterInterface
{
    public function before(RequestInterface $request, $params = null)
    {
        if (is_array($params)){
            $table = $params[0];
            $multiple = $params[1] ?? false;
        }

        $validation = Services::validation();

        $field = new Fields();

        $datas = $field->getFields($table);
        // $koloms = [];
        $posts = $request->getPost();
        if (isset($posts['json'])) {
            $posts = json_decode($posts['json'], true);
        } else {
            $posts = $request->getPost();
        }
        $errors = [];

        if (!$multiple) {
            $posts = [$posts];
        }

        $new_post = [];
        // var_dump($posts);
        foreach ($posts as $key => $postData) {
            $validation->reset(); 
            $oldPost = $postData;
            $id = $postData['id'] ?? -1;
            unset($postData['id']);
            $files_data = $_FILES;
            $folders = [];
            $validationRule = [];
            // var_dump($postData);
            $validationRule = $this->setValidation('', $datas, $postData, $files_data, $field, $id);

            // var_dump($validationRule);
                // return failValidationErrors([]);
            if (empty($validationRule))
                return TRUE;

            if (!$validation->setRules($validationRule)->run($postData)) {
                $errors[$key] = $validation->getErrors();
                continue;
            }

            $postData = $this->groupingData($datas, $oldPost, $field, $folders);
            
            // var_dump($postData);
            // var_dump($_FILES, $folders);
            foreach ($_FILES as $inputName => $fileData) {
                // Get the file object
                // var_dump($inputName, $fileData);
                $file = $request->getFile($inputName);
                if ($file->isValid() && !$file->hasMoved()) {
                    $folder = $folders[$inputName] ?? '';
                    $uploadPath = WRITEPATH . "uploads/$folder";// Ensure this directory exists with the correct permissions
                    // Move the file to the upload folder
                    $old_name = $postData["old_$inputName"] ?? null;
                    if ($old_name) {
                        // 1. Decode agar %2F berubah kembali menjadi / asli
                        $decoded_url = urldecode($old_name); 

                        // 2. Sekarang basename() bisa melihat tanda '/' di depan nama file
                        $newName = basename($decoded_url);
                    } else {
                        $newName = $file->getRandomName(); // Generates a unique name
                    }
                    $file->move($uploadPath, $newName, true);
                    $postData[$inputName] = base_url('/get-files?file=uploads/'.$folder)."/$newName";
                    unset($postData["old_$inputName"]);
                } else {
                    var_dump($file->getErrorString());
                }
            }
            // var_dump($id);
            $postData['id'] = $id;

            $new_post[$key] = $postData;
        }
        
        $posts = $new_post;
        // var_dump($posts, $multiple);
        if (!$multiple) {
            $posts = $posts[0] ?? [];
            $errors = $errors[0] ?? [];
        }
        if (!empty($errors)) {
            return Services::response()->setJSON([
                'status' => '400',
                'error' => '400',
                'messages' => $errors
            ])->setStatusCode(400); // Bad Request
        }
        // array_walk($postData, function(&$value) {
        //     if (empty($value))
        //         $value = NULL;
        // });
        $newRequest = $request->setGlobal('post', $posts);
        return $newRequest;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }

    //Fungsi membuat validation rule
    //prev = prefix untuk array, $datas = Kolom tabel, $postData = data post, $files_data = data_file, $field = Kolom Model, $id = ID utama, untuk bypass pas update
    public function setValidation($prev, $datas, $postData, $files_data, $field, $id)
    {
        $validationRule = [];

        foreach ($datas as $key => $data) {
            $nama = $data->nama_kolom;
            // var_dump($nama, $prev);
            $nama_rule = $prev.$data->nama_kolom;
            $label = $data->label;
            // var_dump($nama, $postData[$nama], isset($postData[$nama]), ($data->required == '1' ? 'required' : 'permit_empty').(empty($data->rules) ? '' : '|'.$data->rules));
            if (isset($postData[$nama])) {
                 if (is_array($postData[$nama])) {
                     $kolom_child = $field->getAllKolom($nama);
                    //  var_dump($nama, $kolom_child);
                     foreach($postData[$nama] as $ind => $element) {
                        $new_prev = "$nama".".$ind.";
                        $validationRule = [...$validationRule, ...$this->setValidation($new_prev, $kolom_child, $postData[$nama][$ind], $files_data[$nama][$ind] ?? [], $field, $id)];
                     }
                    //  var_dump($validationRule);
                 } else {
                     $validationRule[$nama_rule] = [
                         'label' => $label,
                         // 'rules' => 'even'
                         'rules' => ($data->required == '1' ? 'required' : 'permit_empty').(empty($data->rules) ? '' : '|'.$data->rules),
                     ]; 
                    //  var_dump("{id}",$id,$validationRule[$nama]['rules']);
                     // $koloms[] = $data;
                    $validationRule[$nama_rule]['rules'] = str_replace("{id}", is_array($id) ? implode(',', $id) : $id,$validationRule[$nama_rule]['rules']);
                    $validationRule[$nama_rule]['rules'] = str_replace("{field}", $nama_rule, $validationRule[$nama_rule]['rules']);
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
        // var_dump($validationRule);
        return $validationRule;
    }

    public function groupingData($datas, $postData, $field, &$folders)
    {
        helper('functions'); 

        $files_data = $_FILES;

        $tmp_folder = [];
        $double_input = [];
        $input_only = [];
        $tables = [];
        $nama_fk = [];
        $func = [];
        // var_dump($datas);
        foreach ($datas as $key => $data) {
            $nama = $data->nama_kolom;
            $label = $data->label;
            if (isset($postData[$nama])) {
                 if (is_array($postData[$nama])) {
                    $nama_fk[$nama] = $data->nama_fk;
                    $kolom_child = $field->getAllKolom($nama);
                    foreach ($postData[$nama] as $ind => &$elements) {
                        $elements = $this->groupingData($kolom_child, $elements, $field, $folders);
                    }
                    // var_dump($postData[$nama]);
                    $tables[$nama] = $postData[$nama];
                 } else if ($postData[$nama] == '') {
                    unset($postData[$nama]);
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

        // var_dump('after-grouping', $postData);
        foreach ($func as $nama_kolom => $f) {
            $postData[$nama_kolom] = $f($postData[$nama_kolom]);
        }

        // var_dump($double_input);
        foreach ($double_input as $nama_kolom => $data) {
            $koloms = explode('-', $nama_kolom);
            $datas = explode('-', $data);
            // var_dump('double', $koloms, $datas, !in_array($nama_kolom, $koloms));
            foreach ($koloms as $key => $kolom) {
                $postData[$kolom] = $datas[$key] ?? '';
            }
            if (!in_array($nama_kolom, $koloms))
                unset($postData[$nama_kolom]);
        }
        
        // var_dump($tables);
        foreach ($tables as $nama_kolom => $data) {
            $postData['tables'][$nama_kolom] = $data;
            $postData['nama_fk'][$nama_kolom] = $nama_fk[$nama_kolom];
            unset($postData[$nama_kolom]);
        }

        // var_dump($input_only);
        foreach ($input_only as $nama_kolom => $data) {
            unset($postData[$nama_kolom]);
        }
        // var_dump($postData);
        return $postData;
    }
}