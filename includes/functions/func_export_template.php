<?php

require_once('../db_config.php');
require_once('../Excel/vendor/autoload.php');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function update_sf($pdo)
{
    try {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . '/sf_suite/school_forms_temp/SF-Suite_Importing-Template-2025.xlsx';
        
        // Load the .xls spreadsheet file
        $spreadsheet = IOFactory::load($filePath);
        // Create a new filename for the downloaded file based on the current date and time
        $newFileName = 'SF-Suite_Exporting_Template_' . date('Y-m-d_H-i-s') . '.xlsx';
        // Create a new Xlsx writer to output the processed file as a .xlsx
        $writer = new Xlsx($spreadsheet);
        // Clean any previous output to prevent issues with file download
        ob_clean();
        // Set the appropriate headers for file download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $newFileName . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: private');  // For older versions of Internet Explorer
        header('Pragma: no-cache');  // For older browsers
        // Send the file directly to the browser
        $writer->save('php://output');
        exit;
    } catch (Exception $e) {
        // Handle any exceptions (like file not found) and show the error message
        echo 'Error: ' . $e->getMessage();
    }
}
// Call the function to process the file and trigger the download
update_sf($pdo);
?>