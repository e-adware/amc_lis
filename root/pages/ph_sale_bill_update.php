<!--header-->
<?php
			include("../../includes/connection.php");
			session_start();
			$qsl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select emp_id,name,levelid from employee where emp_id='$_SESSION[emp_id]'"));
			$userid=$qsl['emp_id'];
			
?>

<div id="content-header">
    <div class="header_div"> <span class="header">Sales Bill Update</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="" style="margin-bottom:10px;text-align:center;">
		<b>From</b>
		<input class="form-control" type="text" name="fdate" id="fdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
		<b>To</b>
		<input class="form-control" type="text" name="tdate" id="tdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
	</div>
	
	<div class="" style="margin-left:0px;">
		<table class="table table-condensed table-bordered" style="width:50%;margin:0 auto;">
			<tr>
				<td colspan="2"><input type="hidden" id="txtlviid" value="<?php echo $qsl[levelid];?>"/></td>
			</tr>
			
			<!--<tr>
				<td>Select Order No</td>
				<td>
					<select id="ord">
						<option value="0">--Select--</option>
						<?php
						$q=mysqli_query($link,"SELECT DISTINCT `order_no` FROM `ph_purchase_receipt_master`");
						while($r=mysqli_fetch_array($q))
						{
						?>
						<option value="<?php echo $r['order_no'];?>"><?php echo $r['order_no'];?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>-->
			<tr>
				<td colspan="2" style="text-align:center">
					<button type="button" class="btn btn-info" onclick="srch()">Search</button>
				</td>
			</tr>
		</table>
	</div>
	<div id="res" style="margin-left:0px;max-height:400px;overflow-y:scroll;">
		
	</div>
</div>
<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd',maxDate:"0"});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd',minDate:"0"});
	});
	
	function srch()
	{ 
			///////for current date in script
			var vlvlid=$("#txtlviid").val();
			var d = new Date();
			var month = d.getMonth()+1;
			var day = d.getDate();
			var curdt = d.getFullYear() + '-' +(month<10 ? '0' : '') + month + '-' +(day<10 ? '0' : '') + day;
			//alert(curdt);
			///////end/////////////
			var jj=1;
			if(vlvlid !=='1')
			{

					///////For Date difference
					
					var dat1=document.getElementById("fdate").value;
					var dat2=curdt;
					var date1 = new Date(dat1);
					var date2 = new Date(dat2);
					var timeDiff = Math.abs(date2.getTime() - date1.getTime());
					var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
					if (diffDays>0)
					{
					  jj=0;
					  alert("You Can not view  Back dated Report...");
					}
					//////end difference/////////////
			}		
            if(jj==1)		  
             {
				$.post("pages/ph_load_data_ajax.php"	,
				{
					fdate:$("#fdate").val(),
					tdate:$("#tdate").val(),
					type:"loadsalesbillupdate",
				},
				function(data,status)
				{
					$("#res").html(data);
				})
		  }
	}
	function rcv_rep_exp(ord)
	{
		var url="pages/purchase_receive_rep_xls.php?orderno="+ord;
		document.location=url;
	}
	function redirect_sale_frm(billno)
	{
		
		bootbox.dialog({ message: "<b>Redirecting to Sales</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> "});
		setTimeout(function(){
			window.location="processing.php?param=20&billno="+billno;
		 }, 2000);
	}
	
</script>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<style>
tr:hover
{
	background:none;
}
</style>
