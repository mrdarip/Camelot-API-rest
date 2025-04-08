<?php
header('Content-Type: application/json');

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

    $file = $_FILES['pdf_file'];
    
    // Validate file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if ($mimeType !== 'application/pdf') {
        throw new Exception('Uploaded file must be a PDF');
    }

    // Define upload directory with absolute path
    $uploadDir = '/var/www/html/uploads/';

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
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'received correctly']);
    } else {
        throw new Exception('Error saving the file: ' . error_get_last()['message']);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'received unsuccessfully', 'error' => $e->getMessage()]);
}