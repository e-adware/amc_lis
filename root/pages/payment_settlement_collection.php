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
					<select id="user_entry" class="span2">
						<option value="0">Select User</option>
					</select>
					
					<b>From</b>
					<input class="form-control datepicker span2" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" readonly>
					<b>To</b>
					<input class="form-control datepicker span2" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" readonly>
					
					<br>
					<button class="btn btn-search"  onClick="view_all(0)"><i class="icon-search"></i> Search All</button>
					<button class="btn btn-reset"  onClick="view_all(1)"><i class="icon-search"></i> Search Pending</button>
					<button class="btn btn-new"  onClick="view_all(2)"><i class="icon-search"></i> Search Paid</button>
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
		load_users();
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
		});
		//$("#user_entry").select2({ theme: "classic" });
	});
	function load_users()
	{
		$("#loader").show();
		$.post("pages/payment_settlement_collection_data.php",
		{
			type:"load_users",
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#user_entry").show().html(data);
		})
	}
	function view_all(val)
	{
		$("#loader").show();
		$.post("pages/payment_settlement_collection_data.php",
		{
			type:"load_patients",
			val:val,
			date1:$("#from").val(),
			date2:$("#to").val(),
			user_entry:$("#user_entry").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
			$("#save_btn").hide();
		})
	}
	function each_pat_change(val)
	{
		var tot_chk=$(".each_pat").length;
		var chk=$(".each_pat:checked").length;
		
		calculate_amount();
	}
	function select_all(val)
	{
		if(val==1)
		{
			$("#select_btn").hide();
			$("#de_select_btn").show();
			
			$(".each_pat").prop("checked", true);
		}
		if(val==2)
		{
			$("#select_btn").show();
			$("#de_select_btn").hide();
			
			$(".each_pat").prop("checked", false);
		}
		calculate_amount();
	}
	function calculate_amount()
	{
		var chk=$(".each_pat:checked");
		
		if(chk.length==0)
		{
			//$("#amount_to_pay_tr").hide();
			$("#save_btn").hide();
		}
		else
		{
			//$("#amount_to_pay_tr").show();
			$("#save_btn").show();
		}
		
		var total_amount=0;
		
		for(var i=0;i<chk.length;i++)
		{
			var tr_counter=chk[i].value;
			
			var each_amount=parseFloat($("#each_amount"+tr_counter).val());
			if(!each_amount){ each_amount=0; }
			
			total_amount=parseFloat(total_amount)+parseFloat(each_amount);
		}
		
		total_amount=total_amount.toFixed(2);
		$("#amount_to_pay").text(total_amount);
	}
	function save_payment()
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to save payment</h5>",
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
						var chk=$(".each_pat:checked");
						
						if(chk.length==0)
						{
							bootbox.dialog({ message: "<h4>Select patient</h4>"});
							setTimeout(function(){
								bootbox.hideAll();
							},2000);
							return false;
						}
						var all_pat="";
						for(var i=0;i<chk.length;i++)
						{
							var tr_counter=chk[i].value;
							
							var opd_id=$("#opd_id"+tr_counter).val();
							if(opd_id)
							{
								var each_amount=parseFloat($("#each_amount"+tr_counter).val());
								if(!each_amount){ each_amount=0; }
								
								all_pat=all_pat+"@@"+opd_id+"##"+each_amount;
							}
						}
						
						if(all_pat=="")
						{
							bootbox.dialog({ message: "<h4>Select patient</h4>"});
							setTimeout(function(){
								bootbox.hideAll();
							},2000);
							return false;
						}
						
						$("#save_btn").hide();
						$("#loader").show();
						$.post("pages/payment_settlement_collection_data.php",
						{
							type:"save_payment",
							branch_id:$("#branch_id").val(),
							all_pat:all_pat,
							user:$("#user").text().trim(),
						},
						function(data,status)
						{
							
							bootbox.dialog({ message: "<h4>"+data+"</h4>"});
							setTimeout(function(){
								bootbox.hideAll();
							},2000);
							$("#loader").hide();
							view_all(2);
						})
					}
				}
			}
		});
	}
</script>
<style>
.ScrollStyle
{
    max-height: 400px;
    overflow-y: scroll;
}
</style>
