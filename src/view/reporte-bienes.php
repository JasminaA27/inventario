<?php

require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()->setCreator("")->setLastModifiedBy("yo")->setTitle("yo")->setDescription("yo");
$activeWorksheet = $spreadsheet->getActiveSheet();
$activeWorksheet->setTitle("hoja1");
$activeWorksheet->setCellValue('A1', 'Hola Mundo !');
$activeWorksheet->setCellValue('A2', 'DNI');
$activeWorksheet->setCellValue('B2', '77654567');
$activeWorksheet->setCellValue('A3', 'nombre');
$activeWorksheet->setCellValue('B3', 'yanet');

/*header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="hello_world.xlsx"');
header('Cache-Control: max-age=0');*/

/*for ($i =1; $i <= 10; $i++) { 
    $activeWorksheet->setCellValue('A1'. $i, $i);
}

for ($i = 1; $i <= 30; $i++) {
    $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i); // A, B, C...
    $activeWorksheet->setCellValue($columna . '4', $i);
}*/


/*for ($i = 1; $i <= 12; $i++) {
    $fila = $i;
    $activeWorksheet->setCellValue('A' . $fila, 1);          // Primer número
    $activeWorksheet->setCellValue('B' . $fila, 'x');        // Operador
    $activeWorksheet->setCellValue('C' . $fila, $i);         // Segundo número
    $activeWorksheet->setCellValue('D' . $fila, '=');        // Signo igual
    $activeWorksheet->setCellValue('E' . $fila, 1 * $i);      // Resultado
}*/


$writer = new Xlsx($spreadsheet);
$writer->save('tabla_del_1.xlsx');

