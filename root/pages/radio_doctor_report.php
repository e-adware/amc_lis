<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title><?php echo $brand;?> - <?php echo $location;?></title>
		<!--CSS-->
		<link rel="stylesheet" href="../../css/bootstrap.min.css" />
		<link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
		
		<link href="../../font-awesome/css/font-awesome.css" rel="stylesheet" />
		<link rel="stylesheet" href="../../css/custom.css" />
		
		<script src="../../js/jquery.min.js"></script>
		<script src="../../js/bootstrap.min.js"></script>
		
		<link rel="stylesheet" href="../include/css/jquery-ui.css" />
		<script src="../include/js/jquery-ui.js"></script>
		<!-- Time -->
		<link rel="stylesheet" href="../include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
		<link rel="stylesheet" href="../include/jquery.ui.timepicker.css?v=0.3.3" type="text/css" />
		<script type="text/javascript" src="../include/ui-1.10.0/jquery.ui.core.min.js"></script>
		<script type="text/javascript" src="../include/jquery.ui.timepicker_old.js?v=0.3.3"></script>
		<script>
			
			$(document).ready(function(){
				$(".datepicker").datepicker({
					dateFormat: 'yy-mm-dd',
					maxDate: '0',
				});
			});
			
			function load_report_data(doc)
			{
				$.post("radio_doctor_report_ajax.php",
				{
					date:$("#date").val(),
					doc:doc,
					type:"report"
				},
				function(data,status)
				{
					$("#report_data").html(data);
				})
			}
			
			
		</script>
		
		
	</head>
	<body onblur="window.close()">
		
		<?php
		include("../../includes/connection.php");
		
		$doc=$_GET['doc'];		
		
		
		$name=mysqli_fetch_array(mysqli_query($link,"select * from lab_doctor where id='$doc'"));
		
		?>
		
		<div id="header">
		  <span><h2 class="text-center"> Reporting Doctor: <b><?php echo $name[name];?></b></h2></span>
		</div>
		<hr/>
		<div class="container-fluid">
		
		<table class="table table-bordered text-center">
		<tr>
			<td>Select Date</td>
			<td><input class="form-control datepicker" type="text" name="date" id="date" value="<?php echo date("Y-m-d"); ?>" style="height: auto;" ></td>
			<td>
				<button type="button" id="ser" name="ser" class="btn btn-success" onClick="load_report_data('<?php echo $doc;?>')">Search</button>
				<button type="button" id="close" name="close" class="btn btn-danger" onClick="window.close()">Close</button>
			</td>
		</tr>	
		</table>

	
	<div id="report_data">
	
		<script>load_report_data('<?php echo $doc;?>')</script>
	
	</div>
	
	
	</div>
	
	
	
	
	
	</body>
</html>
