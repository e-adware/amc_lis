<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center">
		<tr>
			<td>
				<center>
					<b>From</b>
					<input class="form-control span2 datepicker" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" >
					<b>To</b>
					<input class="form-control span2 datepicker" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" >
					
					<select id="head_id" onChange="view_all('view')" class="span2">
						<option value="0">All Department</option>
					<?php
						$head_qry=mysqli_query($link, " select distinct a.type_id,a.type_name from testmaster a, patient_test_details b where a.testid=b.testid and a.type_name!='' and a.type_name!='0' and a.category_id='1' order by a.type_name ");
						while($head=mysqli_fetch_array($head_qry))
						{
							echo "<option value='$head[type_id]'>$head[type_name]</option>";
						}
					?>
					</select>
					<br>
					<button class="btn btn-success" onClick="view_all('view')">View</button>
					<button class="btn btn-success" onClick="view_all('report')">Report</button>
				</center>
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		
	</div>
	<!--<input type="button" value="Print" onClick="print_doc_pat('load_all')" class="btn btn-success">-->
</div>
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../jss/moment.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<!-- Select2 Plugin -->
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<script>
	$(document).ready(function(){
		$("#loader").hide();
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
		});
		$("#head_id").select2({ theme: "classic" });
	});
	function view_all(typ)
	{
		$("#loader").show();
		$.post("pages/pat_test_out_data.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
			head_id:$("#head_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		})
	}
	function each_test_change(testslno, val)
	{
		if(val==1)
		{
			var msg="Are you sure this test is sent out-side ?";
		}
		if(val==0)
		{
			var msg="Are you sure this test is not sent out-side ?";
		}
		bootbox.dialog({
			message: "<h5>"+msg+"</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					  view_all('view');
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Confirm',
					className: "btn btn-danger",
					callback: function() {
						each_test_change_ok(testslno, val);
					}
				}
			}
		});
	}
	function each_test_change_ok(testslno, val)
	{
		$("#loader").show();
		$.post("pages/pat_test_out_data.php",
		{
			type:"check_outside",
			testslno:testslno,
			val:val,
		},
		function(data,status)
		{
			$("#loader").hide();
			view_all('view');
		})
	}
	function print_page(val,date1,date2,head_id)
	{
		if(val=="report")
		{
			url="pages/pat_test_out_report_print.php?date1="+date1+"&date2="+date2+"&head_id="+head_id;
		}
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
<style>
.ScrollStyle
{
    max-height: 400px;
    overflow-y: scroll;
}
@media print {
  body * {
    visibility: hidden;
  }
  #load_all, #load_all * {
    visibility: visible;
  }
  #load_all {
	  overflow:visible;
    position: absolute;
    left: 0;
    top: 0;
  }
}
p {
    margin: 0 0 0px;
}
label
{
	display: inline;
}
</style>
