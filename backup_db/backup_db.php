<?php
session_start();
if (!isset($_SESSION['allow_backup']) || $_SESSION['allow_backup'] !== true) {
	header("Location: ./");
    die("Unauthorized access.");
}

$date=date("Y-m-d");
$time=date("H:i:s");

include("../includes/connection.php");

// Backup Starts

include ('dumper.php');

try {
	$world_dumper = Shuttle_Dumper::create(array(
		'host' => $host,
		'username' => $username,
		'password' => base64_decode($password),
		'db_name' => $db_name,
	));
	
	//~ $file_name="penguin.sql";
	$file_name=$db_name.".sql.gz";

	// dump the database to gzipped file
	$world_dumper->dump($file_name);

	// dump the database to plain text file
	//$world_dumper->dump($file_name);
	
	header('Content-Description: File Transfer');
	header('Content-Type: application/force-download');
	header("Content-Disposition: attachment; filename=\"" . basename($file_name) . "\";");
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file_name));
	ob_clean();
	flush();
	readfile($file_name); //showing the path to the server where the file is to be download
	exit;
} catch(Shuttle_Exception $e) {
	echo "Couldn't dump database: " . $e->getMessage();
}

// Backup Ends

// optional: clear session flag
unset($_SESSION['allow_backup']);
?>

