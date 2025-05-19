<?php
header('Content-Type: application/json');

require_once __DIR__ . '/./vendor/autoload.php';

use RandomState\Camelot\Camelot;
use RandomState\Camelot\Areas;

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

try {
    // Check if a file was uploaded
    if (!isset($_FILES['pdf_file'])) {
        throw new Exception('No file was uploaded');
    }
    // check if the page form part is set
    if (!isset($_POST['pages'])) {
        throw new Exception('No pages form part was set');
    }

    $file = $_FILES['pdf_file'];

    // Validate file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if ($mimeType !== 'application/pdf') {
        throw new Exception('Uploaded file must be a PDF');
    }

    // Define upload directory with absolute path
    $uploadDir = '/tmp/';

    // Generate unique filename
    $filename = uniqid() . '.pdf';
    $destination = $uploadDir . $filename;

    // Move uploaded file
    if (!is_dir($uploadDir)) {
        throw new Exception('Upload directory does not exist');
    }

    if (!is_writable($uploadDir)) {
        throw new Exception('Upload directory is not writable');
    }

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        // File successfully uploaded, now process it with Camelot
        $camelot = Camelot::stream($destination)->inAreas(
            Areas::from(0,842,595,0)
        );

        $output = $camelot->pages($_POST['pages'])->save($destination . '.csv');

        // Collect all CSV content and structure it as JSON
        $pages = [];
        foreach (glob($destination . '-page-*-table-*.csv') as $csvFile) {
            $pageData = [];
            if (($handle = fopen($csvFile, 'r')) !== false) {
                while (($row = fgetcsv($handle)) !== false) {
                    $pageData[] = $row;
                }
                fclose($handle);
            }
            $pages[] = $pageData;
        }

        // Clean up: delete the uploaded file after processing
        if (file_exists($destination)) {
            unlink($destination);
        }
        foreach (glob($destination . '-page-*-table-*.csv') as $csvFile) {
            unlink($csvFile);
        }

        // Return the JSON response
        echo json_encode(['pages' => $pages]);
    } else {
        throw new Exception('Error saving the file: ' . error_get_last()['message']);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'received unsuccessfully', 'error' => $e->getMessage()]);
}


/*

use RandomState\Camelot\Camelot;
use League\Csv\Reader;

$tables = Camelot::lattice('/path/to/my/file.pdf')
       ->extract();

$csv = Reader::createFromString($tables[0]);
$allRecords = $csv->getRecords();
*/