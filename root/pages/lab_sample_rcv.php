<div id="content-header">
    <div class="header_div"> <span class="header"> Laboratory Sample Processing</span></div>
</div>

<div class="container-fluid">

<br/>
<div align='center'>
	<input type="text"id="scan" placeholder="Scanned ID" style="width:300px"  autofocus/>
</div>

<table class="table table-bordered text-center">
		<tr>
			<td>
				<b>From</b>
				<input class="form-control datepicker" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" >
				<b>To</b>
				<input class="form-control datepicker" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" ><br>
				<button class="btn btn-success" onClick="load_data_det()" style="margin-left: 47%;">View</button>
			</td>
			<td>
				<b>Name</b>
				<input type="text" id="pat_name" onKeyup="load_data_event(event)">
			</td>
			
			<td>
				<b>ID</b>
				<input type="text" class="span2" id="var_id" onKeyup="load_data_event(event)">
			</td>
		</tr>
	</table>

	<div id="data_det">

	</div>
</div>
<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"/>
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="border-radius:0;display:none">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div id="results"> </div>
			</div>
		</div>
	</div>
</div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<link rel="stylesheet" href="../css/animate.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />

<script>
	setInterval(function(){ check_scan(); }, 2000);
	
	//setTimeout(function(){ $("#scan").val("10101201S");},1000);
	
	$(document).ready(function(){
		$("#loader").hide();
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
		});
		
		load_data_det();
	});
	
	function load_data_det()
	{
		$.post("pages/lab_sample_rcv_ajax.php",
		{
			from:$("#from").val(),
			to:$("#to").val(),
			name:$("#pat_name").val(),
			vid:$("#var_id").val(),
			type:"load_data"
		},
		function(data,status)
		{
			$("#data_det").html(data);
		})
	}
	
	function check_scan()
	{
		if($("#scan").val().length>5)
		{
			$.post("pages/lab_sample_rcv_ajax.php",
			{
				val:$("#scan").val(),
				type:"scan"
			},
			function(data,status)
			{
				if(data!="no data" && data!="scanned")
				{
					$("#scan").val("");
					$("#results").html(data);
					$("#mod").click();
					$("#results").fadeIn(500,function(){ var count=setInterval(function(){ load_count(); },1000)});
				}
				else
				{
					if(data=="scanned")
					{
						//alert(data);
						bootbox.dialog({ message: "<h5>Already Scanned</h5>"});
						setTimeout(function(){
									bootbox.hideAll();
									$("#scan").val("").focus();
								},1500);
						
					}
				}
			})
		}
	}
	
	function load_count()
	{
		if(parseInt($("#count").text())==0)
		{
			accept_barcode();
			
		}
		else if($("#count").text()=="..")
		{
			
		}
		else
		{
			var nval=parseInt($("#count").text())-1;
			$("#count").text(nval);
		}
	}
	
	function accept_barcode()
	{
		$("#count").text("..");
		$.post("pages/lab_sample_rcv_ajax.php",
		{
			barcode:$("#barcode").val(),
			user:$("#user").text(),
			type:"save_sample"
		},
		function(data,status)
		{
			bootbox.dialog({ message: "<h5>Saved</h5>"});
			setTimeout(function(){
						bootbox.hideAll();
						$("#mod").click();
						load_data_det();
					},1000);
			
		})
	}
	function view_sample(bar)
	{
		$.post("pages/lab_sample_rcv_ajax.php",
			{
				val:bar,
				view:1,
				type:"scan"
			},
			function(data,status)
			{
				$("#results").html(data);
				$("#mod").click();
				$("#results").fadeIn(500);
			})
	}
	function load_data_event(e)
	{
		if(e.which==13)
		{
			load_data_det();
		}
	}
</script>

<style>
#myModal
{
	left: 43%;
	width:55%;
	height: 400px;
}
.modal.fade.in {
    top: 1%;
}
.modal-body
{
	max-height: 550px;
}
.table-report tr:first-child th
{
  background:#666 !important;
  
  color:#fff;
  font-weight:bold;
}
.table-report tr td{
	  background: white;
}
.tst_div{display:inline-block;width:200px;}
</style>
