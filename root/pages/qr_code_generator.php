<?php
// Include the PHP QR Code library
include('../../phpqrcode/qrlib.php');
$tempDir = '../../phpqrcode/temp/'; 

// Text to encode
//$text = "https://inventory.ajmalfoundation.in/login/"; // Replace with your desired data
$text = isset($QRcodeContents) ? $QRcodeContents : "No Data Available"; // Default text if none provided

// Output the QR code to an image buffer
ob_start();
QRcode::png($text, null, QR_ECLEVEL_H, 10); // Use high error correction level (H) to tolerate the icon overlap
$qrCodeImageString = ob_get_contents();
ob_end_clean();

// Create an image resource from the QR code string
$qrCodeImage = imagecreatefromstring($qrCodeImageString);

// Desired size for the QR code (200x200)
$qrCodeSize = 200;

// Create a blank image with the desired size (200x200)
$resizedQRCode = imagecreatetruecolor($qrCodeSize, $qrCodeSize);

// Resize the QR code to 200x200
imagecopyresampled($resizedQRCode, $qrCodeImage, 0, 0, 0, 0, $qrCodeSize, $qrCodeSize, imagesx($qrCodeImage), imagesy($qrCodeImage));

// Set the color for the placeholder (white in this case)
$whiteColor = imagecolorallocate($resizedQRCode, 255, 255, 255);

// Desired placeholder size (adjusted for minimal overlap)
$placeholderWidth = $qrCodeSize * 0.2;  // Placeholder will be 20% of the QR code width
$placeholderHeight = $qrCodeSize * 0.2; // Placeholder will be 20% of the QR code height

// Calculate the position to place the placeholder (centered)
$placeholderX = ($qrCodeSize - $placeholderWidth) / 2;
$placeholderY = ($qrCodeSize - $placeholderHeight) / 2;

// Draw the white rectangle placeholder in the center of the QR code
imagefilledrectangle($resizedQRCode, $placeholderX, $placeholderY, $placeholderX + $placeholderWidth, $placeholderY + $placeholderHeight, $whiteColor);

// Load the icon image (PNG)
$iconPath = '../../images/icon.png'; // Path to your icon file
$icon = imagecreatefrompng($iconPath); // Supports PNG icons, you can change to imagecreatefromjpeg() for JPEG icons

// Get the size of the icon
$iconWidth = imagesx($icon);
$iconHeight = imagesy($icon);

// Desired icon size (make it fit within the placeholder)
$iconTargetWidth = $placeholderWidth * 0.8;  // Icon will be 80% of the placeholder width
$iconTargetHeight = $placeholderHeight * 0.8; // Icon will be 80% of the placeholder height

// Calculate the position to place the icon (centered in the placeholder)
$iconX = $placeholderX + ($placeholderWidth - $iconTargetWidth) / 2;
$iconY = $placeholderY + ($placeholderHeight - $iconTargetHeight) / 2;

// Resize and place the icon in the center of the placeholder
imagecopyresampled($resizedQRCode, $icon, $iconX, $iconY, 0, 0, $iconTargetWidth, $iconTargetHeight, $iconWidth, $iconHeight);

// Output the final image with the icon in the placeholder
//~ header('Content-Type: image/png');
//~ imagepng($resizedQRCode);


// Save the final image with the icon in the placeholder
//$outputPath = '../images/qr_code_with_icon.png'; // Path where the image will be saved

$tempDir = '../../phpqrcode/temp/';
if (!file_exists($tempDir))
{
	mkdir($tempDir);
}

$target_file="../../phpqrcode/temp/".$c_user."*.*";
foreach (glob($target_file) as $filename_del) {
	unlink($filename_del);
}


$randomNumber = rand(1000, 9999); // Generates a random number between 1 and 100

$filename = $tempDir.$c_user."".str_replace("/","",$qr_file_name).$randomNumber.".jpg";

$errorCorrectionLevel = 'L'; // L, M, Q, H

$matrixPointSize = 4; // 1,........,10

//QRcode::png($codeContents, $tempDir.''.$filename.'.png', QR_ECLEVEL_S, 8);

//QRcode::png($codeContents, $filename, $errorCorrectionLevel, $matrixPointSize, 2);


//imagepng($resizedQRCode, $filename);


// Save the final image as a .jpg file
imagejpeg($resizedQRCode, $filename, 100); // 100 is the quality (0-100)

// Cleanup
imagedestroy($qrCodeImage);
imagedestroy($resizedQRCode);
imagedestroy($icon);


?>
