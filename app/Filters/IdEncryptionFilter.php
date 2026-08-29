<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class IdEncryptionFilter implements FilterInterface 
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('security');

        // Use specific getters to avoid cross-contamination
        $allData = $request->getGetPost();

        if (!empty($allData)) {
            array_walk_recursive($allData, function (&$item, $key) use ($request) {
                // Enkripsi jika key mengandung 'id' dan value adalah angka
                // var_dump($key, $this->shouldEncrypt($key, $item));
                if ($this->isEncrypted($key, $item)) {
                    $item = decrypt_id($item);
                }
            });
            // var_dump($allData);
            $request = $request->setGlobal('get',$allData);
            $request = $request->setGlobal('post', $allData);
            $request = $request->setGlobal('request', $allData);
        }

        // JSON Handling
        if (strpos($request->getHeaderLine('Content-Type'), 'application/json') !== false) {
            $json = $request->getJSON(true);
            if ($json) {
                array_walk_recursive($json, function (&$item, $key) {
                    if ($this->isEncrypted($key, $item)) {
                        $item = decrypt_id($item);
                    }
                });
                // Finalize the request with the new body
                $request = $request->setBody(json_encode($json));
            }
        }

        // Returning this replaces the global $this->request in your Controller
        return $request; 
    }

    private function isEncrypted($key, $value)
    {
        // Kriteria: key mengandung 'id' dan value bukan angka murni (hex)
        return (strpos($key, 'id') !== false && !is_numeric($value) && is_string($value));
    }

    // AFTER: Enkripsi semua ID dalam JSON response otomatis
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {
        return;
        $contentType = $response->getHeaderLine('Content-Type');
        $body = $response->getBody();

        // 1. HANDLE JSON RESPONSE (Paling Aman & Akurat)
        if (strpos($contentType, 'application/json') !== false) {
            $data = json_decode($body, true);
            if (is_array($data)) {
                array_walk_recursive($data, function (&$item, $key) {
                    // Enkripsi jika key mengandung 'id' dan value adalah angka
                    if ($this->shouldEncrypt($key, $item)) {
                        $item = encrypt_id($item);
                    }
                });
                $response->setJSON($data);
            }
        } 
        
        // 2. HANDLE HTML RESPONSE (Gunakan Regex - Hati-hati)
        // elseif (strpos($contentType, 'text/html') !== false) {
        //     // Mencari pola seperti href="/delete/123" atau id="input_123"
        //     // Pola: Mencari kata 'id' atau slash diikuti angka, tapi bukan bagian dari kata lain
        //     $pattern = '/(?<=\/|id=)"?(\d+)"?/';
            
        //     $newBody = preg_replace_callback($pattern, function($matches) {
        //         return encrypt_id($matches[1]);
        //     }, $body);
            
        //     $response->setBody($newBody);
        // }
        return $response;
    }

    private function shouldEncrypt($key, $value)
    {
        // Hanya enkripsi jika key mengandung 'id' dan value adalah integer/numeric
        return (strpos(strtolower($key), 'id') !== false && is_numeric($value));
    }
}