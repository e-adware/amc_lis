<?php
$barcode_num=$_GET["barcode_num"];
$name=$_GET["name"];
$age=$_GET["age"];
$dob=$_GET["dob"];
$sex=$_GET["sex"];
$uhid=$_GET["uhid"];
$opd_id=$_GET["opd_id"];
$reg_time=$_GET["reg_time"];
$paddrss=$_GET["paddrss"];

$left_margin=13;
$top_margin=10;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
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
				
				var left_margin="<?php echo $left_margin;?>";
				var top_margin="<?php echo $top_margin;?>";
				
				//var pat_name="<?php echo $name;?>";
				var pat_name="<?php echo $name.'('.$age.' / '.$sex.')';?>";
				var age="<?php echo $age;?>";
				var dob="<?php echo $dob;?>";
				var sex="<?php echo $sex;?>";
				var uhid="<?php echo $uhid;?>";
				var opd_id="<?php echo $opd_id;?>";
				var reg_time="<?php echo $reg_time;?>";
				var addrs="<?php echo $paddrss;?>";
				
				pat_age_sex=age+" "+sex;
				
				var cmds="";
			<?php
				$i=1;
				while($i<=$barcode_num)
				{
			?>
					cmds += "^XA"; // Start
					cmds += "^FS^CF0,25^FO"+left_margin+",5^FD: "+pat_name;
					//cmds += "^FS^CF0,25^FO"+left_margin+","+parseInt(parseInt(top_margin)+parseInt(20))+"^FDBill No.: "+opd_id;
					cmds += "^FS^CF0,25^FO"+left_margin+","+parseInt(parseInt(top_margin)+parseInt(20))+"^FDUnit No.: "+uhid;
					cmds += "^FS^CF0,25^FO"+left_margin+","+parseInt(parseInt(top_margin)+parseInt(45))+"^FDDate: "+reg_time;
					cmds += "^FS^CF0,25^FO"+left_margin+","+parseInt(parseInt(top_margin)+parseInt(70))+"^FD: "+addrs;
					cmds += "^FS^CF0,25^FO"+left_margin+","+parseInt(parseInt(top_margin)+parseInt(95))+"^BY2^BCN,70,Y,N,N^FD"+opd_id; // Barcode Data
					cmds += "^XZ"; // End
					
			<?php
					$i++;
				}
			?>
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
