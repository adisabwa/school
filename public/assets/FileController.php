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

    public function downloadZip()
    {
        $files = $this->request->getGetPost('files') ?? [];
        $folders = $this->request->getGetPost('folders') ?? [];
        $zipName = $this->request->getGetPost('zip_name') ?? 'files.zip';
        $deleteOriginal = $this->request->getGetPost('delete_original') ?? FALSE;
        // var_dump($files, $folders, $zipName, $deleteOriginal);
        return zip_and_download($zipName, $files, $folders, $deleteOriginal);
    }
}
