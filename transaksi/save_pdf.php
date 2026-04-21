<?php
session_start();
if (!isset($_SESSION['login'])) {
    http_response_code(403);
    exit('Forbidden');
}

// Ensure the PDF directory exists
$pdf_dir = __DIR__ . '/../PDF';
if (!file_exists($pdf_dir)) {
    mkdir($pdf_dir, 0777, true);
}

// Receive the JSON payload
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['pdfData']) && isset($data['filename'])) {
    $filename = basename($data['filename']);
    $pdfData = $data['pdfData'];
    
    // The datauristring looks like: data:application/pdf;filename=generated.pdf;base64,JVBERi0xLjMK...
    // Or just: data:application/pdf;base64,...
    $parts = explode(',', $pdfData);
    if (count($parts) == 2) {
        $base64_data = $parts[1];
        $decoded_pdf = base64_decode($base64_data);
        
        $filepath = $pdf_dir . '/' . $filename;
        file_put_contents($filepath, $decoded_pdf);
        
        echo json_encode(['status' => 'success', 'message' => 'PDF tersimpan di folder PDF/', 'path' => $filepath]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid PDF data format']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing data']);
}
?>
