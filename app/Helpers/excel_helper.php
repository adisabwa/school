<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;

function excelColumnRange($lower, $upper) {
    ++$upper;
    $result = [];
    for ($i = $lower; $i !== $upper; ++$i) {
        $result[] = $i;
    }
    return $result;
}

function duplicateColumn(Spreadsheet $spreadsheet, $column, $destColumn){
    // var_dump($column, $destColumn, $spreadsheet->getSheetCount());
    $sheet = $spreadsheet->getActiveSheet();

    $highestRow = $sheet->getHighestRow();
    for ($row=1; $row < $highestRow; $row++) { 
        $coordinate = "$column$row";
        $destCoordinate = "$destColumn$row";

        $sourceCell = $sheet->getCell($coordinate);

        $sheet->setCellValue($destCoordinate, $sourceCell->getValue());
        $sheet->duplicateStyle($sheet->getStyle($coordinate), $destCoordinate);
    }

    // return $sheet;

}

function duplicateRow(Spreadsheet $spreadsheet, $row, $destRow){
    $sheet = $spreadsheet->getActiveSheet();

    $highestColumn = $sheet->getHighestColumn();
    $cols = excelColumnRange('A', $highestColumn);
    for ($i=0; $i < (count($cols) - 1); $i++) { 
        $col = $cols[$i];
        $coordinate = "$col$row";
        $destCoordinate = "$col$destRow";

        // var_dump($coordinate, $destCoordinate);
        $sourceCell = $sheet->getCell($coordinate);

        $sheet->setCellValue($destCoordinate, $sourceCell->getValue());
        $sheet->duplicateStyle($sheet->getStyle($coordinate), $destCoordinate);
    }

    // return $sheet;

}

function backupImageInfo(Spreadsheet $spreadsheet) {
    $sheet = $spreadsheet->getActiveSheet();
    $drawings = $sheet->getDrawingCollection();
    // var_dump($drawings);
    $images = [];
    foreach ($drawings as $drawing) {
        if ($drawing instanceof Drawing) {
            $images[] = [
                'name' => $drawing->getName(),
                'description' => $drawing->getDescription(),
                'path' => $drawing->getPath(),
                'coordinates' => $drawing->getCoordinates(),
                'offsetX' => $drawing->getOffsetX(),
                'offsetY' => $drawing->getOffsetY(),
                'width' => $drawing->getWidth(),
                'height' => $drawing->getHeight(),
            ];
        }
    }

    return $images;
}

function reApplyImageInfo(Spreadsheet $spreadsheet, $images) {
    $sheet = $spreadsheet->getActiveSheet();
    $drawings = $sheet->getDrawingCollection();
    // Step 3: Re-insert images with original size and properties
    foreach ($images as $img) {
        $drawing = new Drawing();
        $drawing->setName($img['name']);
        $drawing->setDescription($img['description']);
        $drawing->setPath($img['path']);
        $drawing->setCoordinates($img['coordinates']);  // You may want to shift this if the insertion affects it
        $drawing->setOffsetX($img['offsetX']);
        $drawing->setOffsetY($img['offsetY']);
        $drawing->setWidth($img['width']);
        $drawing->setHeight($img['height']);
        $drawing->setResizeProportional(true); // Keeps aspect ratio
        $drawing->setWorksheet($sheet);
    }
}   