<?php
$PoiU=$_GET["PoiU"];
$name=$_GET["name"];
$age=$_GET["age"];
$sex=$_GET["sex"];
$uhid=$_GET["uhid"];
$reg=$_GET["pin"];
$hosp=$_GET["hosp"];
$memoDt=$_GET["memoDt"];
$slNo=$_GET["slNo"];

//$id_str="ID: ".$reg;

$barcode_str="";

$PoiU=explode("@@",$PoiU);
foreach($PoiU as $text)
{
	if($text)
	{
		$left_margin=330;
		$top_margin=15;
		
		$text=explode("==", $text);
		
		$barcode_id=$text[0];
		$vaccu_name=$text[1];
		$test_suffix=$text[2];
		$dept_serial=$text[3];
		$test_serial=$text[4];
		
		//$age_sex_str=$age." / ".$sex[0];
		$age_sex_str=$memoDt;
		$age_sex_str.=" (".$vaccu_name.")";
		
		$id_str="Hosp No: ".$uhid;
		if($dept_serial)
		{
			//$id_str.=" (".$dept_serial.")";
		}
		if($test_serial)
		{
			$id_str.=" (".$test_serial.")";
		}
		if($slNo)
		{
			$id_str.=" (".$slNo.")";
		}
		
		$barcode_str.= "^XA"; // Start
		$barcode_str.= "^FS^CF0,25^FO".$left_margin.",".$top_margin."^FD".$name; // Patient Name
		
		$top_margin+=25;
		
		$barcode_str.= "^FS^CF0,25^FO".$left_margin.",".$top_margin."^BY1,2,70^BCN,50,N,N,N^FD".$barcode_id; // Barcode Data
		
		$top_margin+=60;
		
		$barcode_str.= "^FS^CF0,25^FO".$left_margin.",".$top_margin."^FD".$barcode_id;
		
		$top_margin+=25;
		
		$barcode_str.= "^FS^CF0,20^FO".$left_margin.",".$top_margin."^FD".$age_sex_str;
		
		$top_margin+=25;
		
		$left_margin-=100;
		
		$barcode_str.= "^FS^CF0,25^FO".$left_margin.",".$top_margin."^FD".$id_str;
		$barcode_str.= "^XZ<br>"; // End
	}
}
//~ echo $barcode_str;
//~ exit;

//https://gist.github.com/metafloor/773bc61480d1d05a976184d45099ef56
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>untitled</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="generator" content="Geany 1.32" />
	<script src="zip.js"></script>
	<script src="zip-ext.js"></script>
	<script src="deflate.js"></script>
	<script src="JSPrintManager.js"></script>
	 
	<script src="bluebird.min.js"></script>
	<script src="jquery-3.2.1.slim.min.js"></script>
	<script>
	 
		//WebSocket settings
		JSPM.JSPrintManager.auto_reconnect = true;
		JSPM.JSPrintManager.start();
		JSPM.JSPrintManager.WS.onStatusChanged = function () {
			if (jspmWSStatus()) {
				//get client installed printers
				JSPM.JSPrintManager.getPrinters().then(function (myPrinters) {
					var options = '';
					for (var i = 0; i < myPrinters.length; i++) {
						options += '<option>' + myPrinters[i] + '</option>';
					}
					$('#installedPrinterName').html(options);
				});
			}
		};
	 
		//Check JSPM WebSocket status
		function jspmWSStatus() {
			if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Open)
				return true;
			else if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Closed) {
				//~ alert('JSPrintManager (JSPM) is not installed or not running! Download JSPM Client App from https://neodynamic.com/downloads/jspm');
				alert('Open terminal, copy paste     sudo dpkg -i jspm-2.0.20.401-amd64.deb');
				return false;
			}
			else if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.BlackListed) {
				alert('JSPM has blacklisted this website!');
				return false;
			}
		}
	 
		//Do printing...
		function print(o) {
			if (jspmWSStatus()) {
				
				var cpj = new JSPM.ClientPrintJob();
					//Set Printer type (Refer to the help, there many of them!)
					if ($('#useDefaultPrinter').prop('checked')) {
						cpj.clientPrinter = new JSPM.DefaultPrinter();
					} else {
						cpj.clientPrinter = new JSPM.InstalledPrinter($('#installedPrinterName').val());
					}
				
				var cmds="<?php echo $barcode_str;?>";
				
				//alert(cmds);
				cpj.printerCommands = cmds;
				cpj.sendToClient();
			}
		}
	</script>
</head>

<body>
	<div style="text-align:center">
		<h1>Print Zebra ZPL commands from Javascript</h1>
		<hr />
		<label class="checkbox">
			<input type="checkbox" id="useDefaultPrinter" checked/> <strong>Print to Default printer</strong>
		</label>
		<p>or...</p>
		<div id="installedPrinters">
			<label for="installedPrinterName">Select an installed Printer:</label>
			<select name="installedPrinterName" id="installedPrinterName"></select>
		</div>
		<br /><br />
		<button type="button" id="print_btn" onclick="print();">Print Now...</button>
	</div>
</body>

</html>

<script>
$(document).ready(function(){
	setTimeout(function(){
		$("#print_btn").click();
	},1000);
	
	setTimeout(function(){
		window.close();
	},3000);
});
</script>
