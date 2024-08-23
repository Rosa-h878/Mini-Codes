<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Database connection parameters
$server = '***bauflows***';
$db1 = '********';
$db2 = '********';
$user = '***';
$pass = '**********';

// Connection options
$options1 = array(
    "Database" => $db1,
    "UID" => $user,
    "PWD" => $pass,
    "CharacterSet" => "UTF-8"
);

$options2 = array(
    "Database" => $db2,
    "UID" => $user,
    "PWD" => $pass,
    "CharacterSet" => "UTF-8"
);

// Connect to both databases
$conn1 = sqlsrv_connect($server, $options1);
$conn2 = sqlsrv_connect($server, $options2);

if ($conn1 === false || $conn2 === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Function to get columns of a table
function getTableColumns($conn, $tableName)
{
    $query = "SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ?";
    $params = [$tableName];
    $stmt = sqlsrv_query($conn, $query, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $columns = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $columns[$row['COLUMN_NAME']] = $row['DATA_TYPE'];
    }

    return $columns;
}

// Function to get all table names in a database
function getTableNames($conn)
{
    $query = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'";
    $stmt = sqlsrv_query($conn, $query);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $tables = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $tables[] = $row['TABLE_NAME'];
    }

    return $tables;
}

// Get all table names from both databases
$tables1 = getTableNames($conn1);
$tables2 = getTableNames($conn2);

// Initialize Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Table Name')
      ->setCellValue('B1', 'Comparison Result');

$rowIndex = 2;

foreach ($tables1 as $table) {
    if (in_array($table, $tables2)) {
        $columns1 = getTableColumns($conn1, $table);
        $columns2 = getTableColumns($conn2, $table);

        if ($columns1 == $columns2) {
            continue; // Skip tables that are completely the same
        }

        $difference = array_diff_assoc($columns1, $columns2);
        $comparisonResult = "Unterschiede: " . json_encode($difference);
    } else {
        $comparisonResult = "fehlt";
    }

    $sheet->setCellValue("A$rowIndex", $table)
          ->setCellValue("B$rowIndex", $comparisonResult);
    $rowIndex++;
}

foreach ($tables2 as $table) {
    if (!in_array($table, $tables1)) {
        $sheet->setCellValue("A$rowIndex", $table)
              ->setCellValue("B$rowIndex", "Missing in Database1");
        $rowIndex++;
    }
}

// Write to Excel file
$writer = new Xlsx($spreadsheet);
$writer->save('comparison_result.xlsx');

echo "Comparison complete. Results saved in 'comparison_result.xlsx'.";

// Close connections
sqlsrv_close($conn1);
sqlsrv_close($conn2);

?>
