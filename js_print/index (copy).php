<?php
$PoiU=$_GET["PoiU"];
$name=$_GET['name'];
$age=$_GET['age'];
$age_type=$_GET['age_type'];
$sex=$_GET['sex'];
$reg=$_GET['reg'];

$left_margin=200;
$top_margin=10;

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
				
				var pat_name="<?php echo $name;?>";
				var pat_age_sex="";
				var pat_vid="";
				
				var left_margin="<?php echo $left_margin;?>";
				var top_margin="<?php echo $top_margin;?>";
				
				var cmds="";
			<?php
				$PoiU=explode("/",$PoiU);
				foreach($PoiU as $text)
				{
					if($text)
					{
						$text=explode("=", $text);
			?>
						pat_age_sex="";
						pat_age_sex="Age/Sex: "+"<?php echo $age;?>"+" <?php echo $age_type;?>"+" / "+"<?php echo $sex;?>";
						<?php
						if($text[2])
						{
						    ?> pat_age_sex+=" (<?php echo $text[2];?>)" <?php
						    
						}
			?>
				
						pat_vid="";
						pat_vid="ID: "+"<?php echo $reg;?>";
						pat_vid+=" (<?php echo $text[1];?>)"
						
						var barcode_id="<?php echo $text[0];?>";
						
						cmds += "^XA^FS^CF0,30^FO"+left_margin+","+top_margin+"^FD"+pat_name+"^FS^CF0,20^FO"+left_margin+","+parseInt(parseInt(top_margin)+parseInt(130))+"^FD"+pat_age_sex+"^FS^CF0,30^FO"+left_margin+","+parseInt(parseInt(top_margin)+parseInt(155))+"^FD"+pat_vid+"^FS^FO"+left_margin+","+parseInt(parseInt(top_margin)+parseInt(170))+",^FS^CF0,20^FO"+left_margin+",185^FD ^FS^FO"+left_margin+","+parseInt(parseInt(top_margin)+parseInt(35))+"^BY2^BCN,70,Y,N,N^FD"+barcode_id+"^XZ";
						
						<?php
					}
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
