<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class FileController extends BaseController
{
    public function getFile()
    {
        // Define the path to the writable uploads directory
        $fileName = $this->request->getGet('file');
        $filePath = WRITEPATH . $fileName;

        // var_dump($filePath, $_GET);
        // Check if the file exists
        if (file_exists($filePath)) {
            // Get the file's MIME type
            $mimeType = mime_content_type($filePath);
            
            // Serve the file to the browser
            return $this->response->setHeader('Content-Type', $mimeType)
                                  ->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"')
                                  ->setBody(file_get_contents($filePath));
        } else {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('File not found: ' . $fileName);
        }
    }
}
