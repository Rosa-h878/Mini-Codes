<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Die Daten, die in die Excel-Tabelle eingefügt werden sollen
$data = [
    [90010, "Accounting & Audit"],
    [90020, "Employee Training"],
    [90030, "Fahrzeugkosten"],
    [90040, "Get Together / Teambuilding"],
    [90050, "Hospitality costs"],
    [90060, "Insurance"],
    [90070, "IT Related"],
    [90080, "Legal"],
    [90090, "Marketing incl. PR"],
    [90100, "Personal"],
    [90110, "Mobile/land lines"],
    [90120, "Office rent"],
    [90130, "Other"],
    [90140, "Recruiting Fee"],
    [90150, "Travel"],
    [90160, "Utilities/Office"],
    [90170, "Workwear"],
];

// Erstelle ein neues Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Setze die Spaltenüberschriften
$sheet->setCellValue('A1', 'Kostenstelle Nr.');
$sheet->setCellValue('B1', 'Bezeichnung');
$sheet->setCellValue('C1', 'SQL Abfrage');

// Füge die Daten in die Tabelle ein
$row = 2; // Beginne mit der zweiten Zeile, da die erste für die Überschriften verwendet wird
foreach ($data as $entry) {
    $sheet->setCellValue('A' . $row, $entry[0]);
    $sheet->setCellValue('B' . $row, $entry[1]);
    $sheet->setCellValue('C' . $row, "INSERT INTO M_COST1(CODE, NAME) VALUES('$entry[0]', '$entry[1]')");
    $row++;
}

// Schreibe die Datei in das XLSX-Format
$writer = new Xlsx($spreadsheet);
$writer->save('Kostenstellen.xlsx');

echo "Die Excel-Datei wurde erfolgreich erstellt.";