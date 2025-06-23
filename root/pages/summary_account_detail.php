<?php
if($p_info["levelid"]==1)
{
	$branch_str="";
	
	$branch_display="display:none;";
	$branch_num=mysqli_num_rows(mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 "));
	if($branch_num>1)
	{
		$branch_display="display:;";
	}
}
else
{
	$branch_str=" AND branch_id='$p_info[branch_id]'";
	$branch_display="display:none;";
}

$branch_id=$p_info["branch_id"];
?>
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
					<select id="branch_id" class="span2" onChange="load_users()" style="<?php echo $branch_display; ?>">
					<?php
						$branch_qry=mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
						while($branch=mysqli_fetch_array($branch_qry))
						{
							if($branch_id==$branch["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
							echo "<option value='$branch[branch_id]' $branch_sel>$branch[name]</option>";
						}
					?>
					</select>
					<select id="user_entry" class="span2" onChange="view_all('summary_account_detail')">
						<option value="0">Select User</option>
					</select>
					
					<select id="encounter" class="span2" onChange="view_all('summary_account_detail')" style="display:none;">
					<?php
						$qq_qry=mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `status`=0 AND `p_type_id`=2 ORDER BY `p_type_id` ");
						while($qq=mysqli_fetch_array($qq_qry))
						{
							echo "<option value='$qq[p_type_id]'>$qq[p_type]</option>";
						}
					?>
					</select>
					
					<b>From</b>
					<input class="form-control datepicker span2" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" readonly>
					<b>To</b>
					<input class="form-control datepicker span2" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" readonly>
					
					<br>
					<br>
					<!--<button class="btn btn-success" onClick="view_all('summary_account_detail')"><i class="icon-search icon-large"></i> View Detail Account </button>-->
					<button class="btn btn-success" onClick="view_all('userwise_account')"><i class="icon-search icon-large"></i> View Userwise Account </button>
					<!--<button class="btn btn-success" onClick="view_all('deptwise_test')"><i class="icon-search icon-large"></i> Dept Wise Test </button>-->
					<button class="btn btn-success" onClick="view_all('balance_patient')"><i class="icon-search icon-large"></i> Balance Patient(s) </button>
					<br>
					<br>
					<button class="btn btn-success" onClick="view_all('details_report')"><i class="icon-search icon-large"></i>Details Report </button>
					<button class="btn btn-success" onClick="view_all('monthly_accounts')"><i class="icon-search icon-large"></i>Monthly Accounts</button>
					<button class="btn btn-success" onClick="view_all('summary_accounts')"><i class="icon-search icon-large"></i>Summary Accounts</button>
					<button class="btn btn-success" onClick="view_all('print_summary')"><i class="icon-search icon-large"></i>Print Summary</button>
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
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<script>
	$(document).ready(function(){
		$("#loader").hide();
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
		});
		load_users();
	});
	function load_users()
	{
		$("#loader").show();
		$.post("pages/summary_account_detail_data_new.php",
		{
			type:"load_users",
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#user_entry").show().html(data);
			//view_all('summary_account_detail');
		})
	}
	function view_all(typ)
	{
		$("#loader").show();
		$.post("pages/summary_account_detail_data_new.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
			encounter:$("#encounter").val(),
			branch_id:$("#branch_id").val(),
			user_entry:$("#user_entry").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			//$("#load_all").slideUp(500,function(){ $("#load_all").html(data).slideDown(500); });
			$("#load_all").html(data).slideDown(500);
		})
	}
	function export_excel()
	{
		var date1=$("#from").val();
		var date2=$("#to").val();
		var encounter=$("#encounter").val();
		var branch_id=$("#branch_id").val();
		var user_entry=$("#user_entry").val();
		
		var url="pages/summary_account_detail_print_xls.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry+"&branch_id="+branch_id;
		document.location=url;
	}
	function print_page(val,date1,date2,encounter,user_entry,branch_id)
	{
		var user=$("#user").text().trim();
		if(val=="summary_account_detail")
		{
			url="pages/summary_account_detail_print.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry+"&branch_id="+branch_id+"&EpMl="+user;
		}
		if(val=="userwise_account")
		{
			url="pages/userwise_account_print.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry+"&branch_id="+branch_id+"&EpMl="+user;
		}
		if(val=="deptwise_test")
		{
			url="pages/deptwise_test_account_print.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry+"&branch_id="+branch_id+"&EpMl="+user;
		}
		if(val=="balance_patient")
		{
			url="pages/balance_patient_list_print.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry+"&branch_id="+branch_id+"&EpMl="+user;
		}
		if(val=="details_report")
		{
			url="pages/details_report_list_print.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry+"&branch_id="+branch_id+"&EpMl="+user;
		}
		if(val=="monthly_accounts")
		{
			url="pages/monthly_accounts_list_print.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry+"&branch_id="+branch_id+"&EpMl="+user;
		}
		if(val=="summary_accounts")
		{
			url="pages/summary_accounts_list_print.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry+"&branch_id="+branch_id+"&EpMl="+user;
		}
		if(val=="print_summary")
		{
			url="pages/print_summary_list_print.php?date1="+date1+"&date2="+date2+"&encounter="+encounter+"&user_entry="+user_entry+"&branch_id="+branch_id+"&EpMl="+user;
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
.ipd_serial
{
	display:none;
}
</style>
