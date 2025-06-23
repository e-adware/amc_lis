<?php
include('../../includes/connection.php');

	// Date format convert
	function convert_date($date)
	{
		if($date)
		{
			$timestamp = strtotime($date); 
			$new_date = date('d M Y', $timestamp);
			return $new_date;
		}
	}
	$rupees_symbol="&#x20b9; ";
	
	$date1=$_GET['date1'];
	$date2=$_GET['date2'];
	
	function convert_number($number) 
	{
		if (($number < 0) || ($number > 999999999)) 
		{ 
		throw new Exception("Number is out of range");
		} 

		$Gn = floor($number / 100000);  /* Lakh (100 kilo) */ 
		$number -= $Gn * 100000; 
		$kn = floor($number / 1000);     /* Thousands (kilo) */ 
		$number -= $kn * 1000; 
		$Hn = floor($number / 100);      /* Hundreds (hecto) */ 
		$number -= $Hn * 100; 
		$Dn = floor($number / 10);       /* Tens (deca) */ 
		$n = $number % 10;               /* Ones */ 

		$res = ""; 

		if ($Gn) 
		{ 
			$res .= convert_number($Gn) . " Lakh"; 
		} 

		if ($kn) 
		{ 
			$res .= (empty($res) ? "" : " ") . 
				convert_number($kn) . " Thousand"; 
		} 

		if ($Hn) 
		{ 
			$res .= (empty($res) ? "" : " ") . 
				convert_number($Hn) . " Hundred"; 
		} 

		$ones = array("", "One", "Two", "Three", "Four", "Five", "Six", 
			"Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", 
			"Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", 
			"Nineteen"); 
		$tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", 
			"Seventy", "Eigthy", "Ninety"); 

		if ($Dn || $n) 
		{ 
			if (!empty($res)) 
			{ 
				$res .= " and "; 
			} 

			if ($Dn < 2) 
			{ 
				$res .= $ones[$Dn * 10 + $n]; 
			} 
			else 
			{ 
				$res .= $tens[$Dn]; 

				if ($n) 
				{ 
					$res .= "-" . $ones[$n]; 
				} 
			} 
		} 

		if (empty($res)) 
		{ 
			$res = "zero"; 
		} 

		return $res; 
	}
	
$page_header="Baby Delivery Report";

?>
<html>
<head>
	<title><?php echo $page_header;?></title>
	<link href="../../css/bootstrap.min.css" type="text/css" rel="stylesheet"/>
	<link href="../../css/custom.css" type="text/css" rel="stylesheet"/>
	<script src="../../js/jquery.min.js"></script>
	<style>
		*{font-size:12px;}
		.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
		{
			padding:1px 1px 1px 5px;
		}
		.no_print
		{
			display:none;
		}
	</style>
</head>
<body onkeypress="close_window(event)">
	<div class="container-fluid">
		<input type="hidden" id="date1" value="<?php echo $date1;?>" />
		<input type="hidden" id="date2" value="<?php echo $date2;?>" />
		<div class="row">
			<div class="">
				<?php include('page_header.php');?>
			</div>
		</div>
		<center>
			<h4><?php echo $page_header;?></h4>
			<b>From <?php echo convert_date($date1); ?> to <?php echo convert_date($date2); ?></b>
		</center>
		<br>
		<div id="res">
		
		</div>
	</div>
</body>
</html>
<script>
	$(document).ready(function(){
		view_all();
	});
	
	window.print();
	
	function view_all()
	{
		$.post("ot_reports_ajax.php",
		{
			date1:$("#date1").val().trim(),
			date2:$("#date2").val().trim(),
			type:"delivery_rep",
		},
		function(data,status)
		{
			$("#res").html(data);
			$("#hide_print").hide();
		})
	}
	
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
</script>
