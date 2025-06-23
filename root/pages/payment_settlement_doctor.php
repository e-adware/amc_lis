<?php
if($p_info["levelid"]==1 && $p_info["branch_id"]==1)
{
	$branch_str="";
	$branch_display="";
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
					<select id="branch_id" class="span2" onChange="load_doctors()" style="<?php echo $branch_display; ?>">
					<?php
						$branch_qry=mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
						while($branch=mysqli_fetch_array($branch_qry))
						{
							if($branch_id==$branch["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
							echo "<option value='$branch[branch_id]' $branch_sel>$branch[name]</option>";
						}
					?>
					</select>
					<b>From</b>
					<input class="form-control datepicker span2" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" readonly>
					<b>To</b>
					<input class="form-control datepicker span2" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" readonly>
					<select id="con_cod_id" onChange="view_all('doctor_account')" class="span2">
						<option value="0">Select Doctor</option>
					<?php
						//$con_doc_qry=mysqli_query($link, " SELECT * FROM `consultant_doctor_master` order by `Name` ");
						while($con_doc=mysqli_fetch_array($con_doc_qry))
						{
							echo "<option value='$con_doc[consultantdoctorid]'>$con_doc[Name]</option>";
						}
					?>
					</select>
					<select id="dept_id" onChange="view_all('doctor_account')" class="span2" style="display:;">
						<option value="0">All Department</option>
					<?php
						$dept_qry=mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `status`=0  ORDER BY `p_type_id` ");
						while($dept=mysqli_fetch_array($dept_qry))
						{
							echo "<option value='$dept[p_type_id]'>$dept[p_type]</option>";
						}
					?>
					</select>
					<br>
					<button class="btn btn-success" onClick="view_all('doctor_account')">View Account</button>
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
		view_all('opd_account');
		$(".datepicker").datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: "yy-mm-dd",
			maxDate: "0",
			yearRange: "-150:+0",
		});
		$("select").select2({ theme: "classic" });
		load_doctors();
	});
	function load_doctors()
	{
		$("#loader").show();
		$.post("pages/payment_settlement_doctor_data.php",
		{
			type:"load_doctors",
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#con_cod_id").show().html(data);
		})
	}
	function view_all(typ)
	{
		$("#loader").show();
		$.post("pages/payment_settlement_doctor_data.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
			con_cod_id:$("#con_cod_id").val(),
			dept_id:$("#dept_id").val(),
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		})
	}
	
	function each_chk(dis,doc)
	{
		//alert($(dis).val());
		if($(dis).is(":checked"))
		{
			$(dis).prop("checked", true);
		}
		else
		{
			$(dis).prop("checked", false);
		}
		check_all_chk(doc);
	}
	function select_all(val,doc)
	{
		if(val==1)
		{
			$(".chk"+doc).prop("checked",true);
			
			$("#select_btn"+doc+"1").hide();
			$("#select_btn"+doc+"2").show();
		}
		if(val==2)
		{
			$(".chk"+doc).prop("checked",false);
			
			$("#select_btn"+doc+"2").hide();
			$("#select_btn"+doc+"1").show();
		}
		check_all_chk(doc);
	}
	
	function check_all_chk(doc)
	{
		var all_serv_len=$(".chk"+doc).length;
		var chk_serv_len=$(".chk"+doc+":checked").length;
		
		if(all_serv_len==chk_serv_len)
		{
			$("#select_btn"+doc+"1").hide();
			$("#select_btn"+doc+"2").show();
		}
		else
		{
			$("#select_btn"+doc+"2").hide();
			$("#select_btn"+doc+"1").show();
		}
		
		var amount=0;
		var all_serv_chk=$(".chk"+doc+":checked");
		for(var i=0;i<all_serv_chk.length;i++)
		{
			var val=all_serv_chk[i].value;
			
			val=val.split("##");
			
			amount=parseInt(amount)+parseInt(val[4]);
		}
		
		$("#total_amount_ech_doc"+doc).text(amount.toFixed(2));
	}
	
	function save(doc)
	{
		var doc_name=$("#con_cod_id").find(":selected").text();
		
		var amount=0;
		var all_fees="";
		var all_serv_chk=$(".chk"+doc+":checked");
		
		if(all_serv_chk.length==0)
		{
			alert("Select fee");
			return false;
		}
		
		for(var i=0;i<all_serv_chk.length;i++)
		{
			var val=all_serv_chk[i].value;
			
			all_fees=all_fees+"@$@"+val;
			
			val=val.split("##");
			
			amount=parseInt(amount)+parseInt(val[4]);
		}
		
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to give cash of amount "+amount.toFixed(2)+" to "+doc_name+" ?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function() {
						$("#loader").show();
						$.post("pages/payment_settlement_doctor_data.php",
						{
							type:"save",
							branch_id:$("#branch_id").val(),
							con_cod_id:$("#con_cod_id").val(),
							all_fees:all_fees,
							user:$("#user").text().trim(),
						},
						function(data,status)
						{
							$("#loader").hide();
							alert(data);
							view_all("doctor_account");
						})
					}
				}
			}
		});
	}
	
	function print_page(date1,date2,con_cod_id,dept_id,branch_id)
	{
		url="pages/payment_settlement_doctor_print.php?date1="+date1+"&date2="+date2+"&con_cod_id="+con_cod_id+"&dept_id="+dept_id+"&branch_id="+branch_id;
		
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1200');
	}
</script>
<style>
.ScrollStyle
{
    max-height: 400px;
    overflow-y: scroll;
}
.table th, .table td
{
	padding:0;
}
</style>
