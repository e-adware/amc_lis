<?php
include('../../phpqrcode/qrlib.php');

// Start output buffering to capture the image data
ob_start();
QRcode::png($qr_code_data, null, QR_ECLEVEL_L, 10, 2);
$imageData = ob_get_contents();
ob_end_clean();

// Encode the image data in base64
$base64 = base64_encode($imageData);

// Output the base64 data as a data URL
echo 'data:image/png;base64,' . $base64;
?>
