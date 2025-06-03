<?php

namespace App\Libraries;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfBuilder
{
    public function generatePdf($html, $stream = TRUE, $paper = 'A4', $orientation = 'potrait')
    {
        // Configure Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Times New Roman');
        $options->set('isHtml5ParserEnabled', true);
        
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);
        // (Optional) Set paper size and orientation
        $dompdf->setPaper($paper, $orientation);

        // Render the PDF
        $render = $dompdf->render();
        
        if ($stream) {
            // Output the generated PDF to Browser
            $dompdf->stream("document.pdf", ["Attachment" => false]); // Change to true to force download
        } else {
            return $render;
        }
    }
}