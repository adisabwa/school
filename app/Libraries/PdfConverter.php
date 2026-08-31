<?php

namespace App\Libraries;

class PdfConverter
{
    public function convertWordToPdf($tempDocxPath)
    {

        if (!$tempDocxPath || !file_exists($tempDocxPath) || !is_readable($tempDocxPath)) {
            return 'No valid Word document found.';
        }


        // --- Update with your Hugging Face direct URL ---
        $gotenbergUrl = 'https://adisabwa-free-pdf-api.hf.space/forms/libreoffice/convert';

        $cfile = new \CURLFile($tempDocxPath, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', basename($tempDocxPath));
        $postData = ['files' => $cfile];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $gotenbergUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); 

        // CRITICAL: Set a fake User-Agent to stop Hugging Face from blocking the PHP script
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
        ]);

        $pdfData = curl_exec($ch);
        $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpStatusCode !== 200 || !$pdfData) {
            return 'Gotenberg conversion failed.';
        }

        return $pdfData;
    }
}
