<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");

$date=date('Y-m-d');
$time=date('H:i:s');

$type=$_POST['type'];

if($type==1)
{
	$uhid=$_POST['uhid'];
	$ipd=$_POST['ipd'];
	$usr=$_POST['usr'];
	$qq=mysqli_query($link,"SELECT DISTINCT `indent_num` FROM `patient_medicine_detail` WHERE `patient_id`='$uhid' AND `pin`='$ipd' AND `type`='2'");
	$num=mysqli_num_rows($qq);
	if($num>0)
	{
	?>
	<table class="table table-bordered table-condensed" id="" width="100%">
		<tr>
			<th rowspan="2">#</th>
			<th rowspan="2">Drug Name</th>
			<th colspan="3"><center>Quantity</center></th>
			<th rowspan="2"><center>Indent No. / Return</center></th>
			<th rowspan="2"><center>Date Time</center></th>
			<th rowspan="2"><center>User</center></th>
		</tr>
		<tr>
			<th>Claimed</th>
			<th>Received</th>
			<th>Returned</th>
		</tr>
		<?php
		while($rr=mysqli_fetch_array($qq))
		{
		$n=1;
		$q=mysqli_query($link,"SELECT * FROM `patient_medicine_detail` WHERE `patient_id`='$uhid' AND `pin`='$ipd' AND `indent_num`='$rr[indent_num]' AND `type`='2'");
		$inum=mysqli_num_rows($q);
		while($r=mysqli_fetch_array($q))
		{
			$m=mysqli_fetch_array(mysqli_query($link,"SELECT `item_name` FROM `item_master` WHERE `item_id`='$r[item_code]'"));
			$dis_none="";
			if($r["status"]>0)
			{
				$dis_none="style='display:none;'";
			}
			
			$item_return_quantity=0;
			$item_return=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `ph_item_return_master` WHERE `bill_no`='$r[bill_no]' AND `indent_num`='$r[indent_num]' AND `item_code`='$r[item_code]' "));
			if($item_return)
			{
				$item_return_quantity=$item_return["return_qnt"];
			}
			
			$isu=mysqli_fetch_array(mysqli_query($link,"SELECT `sale_qnt` FROM `ph_sell_details` WHERE `bill_no`='$r[bill_no]' AND `item_code`='$r[item_code]'"));

			if (!$isu['sale_qnt']) {
					$isu['sale_qnt'] = 0;
			}
			
			$emp_info=mysqli_fetch_array(mysqli_query($link," SELECT `name` FROM `employee` WHERE `emp_id`='$r[user]' "));
		?>
		<tr>
			<td><?php echo $n;?></td>
			<td><?php echo $m['item_name'];?></td>
			<td><?php echo $r['quantity'];?> </td>
			<td><?php echo $isu['sale_qnt'];?></td>
			<td><?php echo $item_return_quantity;?></td>
			<?php
			if($inum>0)
			{
			?>
			<td rowspan="<?php echo $inum;?>">
				<?php echo $r['indent_num'];
				if($r["status"]>0)
				{
				?>
				<span style="float:right;"><button type="button" class="btn btn-warning btn-mini" onclick="return_request('<?php echo base64_encode($uhid);?>','<?php echo base64_encode($ipd);?>','<?php echo base64_encode($r['indent_num']);?>')"><i class="icon-reply"></i> Return Request</button></span>
				<?php
				}
				?>
			</td>
			<?php
			$inum=0;
			}
			?>
			<td>
				<?php echo convert_date_g($r['date']);?>
				<?php echo convert_time($r['time']);?>
			</td>
			<td>
				<?php echo $emp_info['name'];?>
				<button class="btn btn-mini btn-danger text-right" onClick="del_indent_medicine('<?php echo $r["slno"]; ?>')" <?php echo $dis_none; ?>><i class="icon-remove-sign"></i></button>
			</td>
		</tr>
		<?php
		$n++;
		}
		?>
		<tr>
			<td colspan="8" style="padding:1px;background:#222;"></td>
		</tr>
		<?php
		}
		?>
	</table>
	<button type="button" class="btn btn-info" id="indad" onclick="$('#hide_ind_list').show();$('#indad').hide();$('#ins_ind').hide();$('#printInd').hide()"><i class="icon-plus"></i> Add New</button>
	<!--<button type="button" class="btn btn-primary" id="printInd" onclick="print_indent_list()"><i class="icon-print"></i> Print Indent List</button>-->
	<?php
	$qq=mysqli_query($link,"SELECT DISTINCT `indent_num` FROM `ipd_pat_medicine_indent` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'");
	if(mysqli_num_rows($qq)>0)
	{
		echo "<br/>";
	}
	while($rr=mysqli_fetch_assoc($qq))
	{
	?>
	<button type="button" class="btn btn-primary btn-mini" onclick="printIndent('<?php echo base64_encode($rr['indent_num']);?>')">Indent <?php echo $rr['indent_num'];?></button>
	<?php
	}
	}
	else
	{
	?>
	<button type="button" class="btn btn-info" id="indad" onclick="$('#hide_ind_list').show();$('#indad').hide();$('#ins_ind').hide()"><i class="icon-plus"></i> Add</button>
	<?php
	}
	?>
	<div id="hide_ind_list" style="display:none;">
		<div class="row">
			<div class="span7" style="width: 65%;">
				<table class="table table-condensed" id="">
					<tr>
						<td>
							Drug Name: <input type="text" class="span6" id="ind_med" onFocus="load_ind_medi()" onkeyup="load_ind_medi1(this.value,event)" onBlur="javascript:$('#ind_med_list').fadeOut(500)" />
							<button type="button" class="btn btn-danger" onclick="cancelInd()">Cancel</button>
							<input type="text" class="span6" id="mediid" style="display:none;" />
							<div id="ind_med_list" style="width: 650px;">
							</div>
						</td>
					</tr>
					<tr id="ind_data" style="display:none;">
						<td>
							Quantity: <input type="text" class="span1" onkeyup="meditab(this.id,event)" id="qnt" placeholder="Quantity" />
							<button type="button" class="btn btn-primary" id="indsv" onclick="add_ind_data()"><i class="icon-plus"></i> Add</button>
							<button type="button" class="btn btn-danger" onclick="$('#ind_med').val('');$('#mediid').val('');$('#indad').show();$('#select_load').html('');$('#ind_data').hide(500);$('#hide_ind_list').hide(500)"><i class="icon-ban-circle"></i> Cancel</button>
						</td>
					</tr>
					<tr>
						<td id="select_load">
						
						</td>
					</tr>
					<tr>
						<td>
							<span class="text-right"><button type="button" class="btn btn-primary" id="ins_ind" onclick="insert_final_ind()"><i class="icon-file"></i> Save</button></span>
						</td>
					</tr>
				</table>
			</div>
			<div class="span3" style="max-height: 460px;overflow-y: scroll;width: 25%;">
				<table class="table table-condensed">
					<thead class="table_header_fix">
						<tr>
							<th>Frequently Used Medicines</th>
						</tr>
					</thead>
		<?php
				$qry=mysqli_query($link, "SELECT `item_code`,COUNT(`item_code`),`quantity` FROM `patient_medicine_detail` WHERE `patient_id`='$uhid' AND `pin`='$ipd' GROUP BY `item_code` ORDER BY COUNT(`item_code`) DESC");
				while($data=mysqli_fetch_array($qry))
				{
					//$item_info=mysqli_fetch_array(mysqli_query($link, "SELECT `item_id`,`item_name` FROM `item_master` WHERE `item_id`='$data[item_code]'"));
					
					$item_info=mysqli_fetch_array(mysqli_query($link, "SELECT a.`item_id`,a.`item_name`,b.`quantity` FROM `item_master` a,ph_stock_master b WHERE a.`item_id`=b.`item_code` and  a.`item_id`='$data[item_code]' and b.`substore_id`='1' and b.`quantity`>0"));
		?>
					<tr>
						<td>
							<a class="btn btn-link" style="padding: 0;" onclick="select_med_ind('<?php echo $item_info["item_id"]; ?>','<?php echo $item_info["item_name"]; ?>')"><?php echo $item_info["item_name"]; ?></a>
						</td>
					</tr>
		<?php
				}
		?>
				</table>
			</div>
		</div>
	</div>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<script src="../jss/indent_medicine.js"></script>
	<script>
	function del_indent_medicine(slno)
	{
		bootbox.dialog({
			message: "<h5>Are you sure want to delete ?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Delete',
					className: "btn btn-danger",
					callback: function() {
						$.post("pages/nursing_load_g.php",
						{
							slno:slno,
							type:"delete_indent_medicine",
						},
						function(data,status)
						{
							medicine_indent();
						})
					}
				}
			}
		});
	}
	function return_request(uhid,ipd,ino)
	{
		$.post("pages/nursing_load_g.php",
		{
			uhid:uhid,
			ipd:ipd,
			ino:ino,
			usr:$("#user").text().trim(),
			type:"indent_return_request",
		},
		function(data,status)
		{
			$("#result").html(data);
			$("#rep").click();
			//$("html,body").animate({scrollTop: '520px'},1000);
		})
	}
	function return_check(val,sl)
	{
		if(val=="")
		{
			val=0;
		}
		else
		{
			val=parseInt(val);
		}
		var isu=$("#isu"+sl).val().trim();
		if(isu=="")
		{
			isu=0;
		}
		else
		{
			isu=parseInt(isu);
		}
		var req=$("#req"+sl).val().trim();
		if(req=="")
		{
			req=0;
		}
		else
		{
			req=parseInt(req);
		}
		isu=(isu-req);
		if(val>isu)
		{
			$("#ret"+sl).css({"border":"1px solid #FF0000","box-shadow":"0px 0px 8px 1px #FF0000"});
			$("#ret"+sl).addClass("err");
		}
		else
		{
			$("#ret"+sl).css({"border":"","box-shadow":""});
			$("#ret"+sl).removeClass("err");
		}
	}
	function return_final(ino)
	{
		if($(".err").length>0)
		{
			$(".err:first").focus();
		}
		else
		{
			$("#retSave").attr("disabled",true);
			var tr=$(".all_tr");
			//alert(tr.length);
			var items=[];
			for(var i=0; i<(tr.length); i++)
			{
				if($(".all_tr:eq("+i+")").find('td:eq(3) input:first').val().trim()!="")
				{
					var item={
						"itm":$(".all_tr:eq("+i+")").find('td:eq(1) input:first').val().trim(),
						"qnt":$(".all_tr:eq("+i+")").find('td:eq(4) input:first').val().trim()
						};
					items.push(item);
				}
			}
			//alert(items);
			$.post("pages/nursing_load_g.php",
			{
				uhid:$("#uhid").val().trim(),
				ipd:$("#ipd").val().trim(),
				usr:$("#user").text().trim(),
				ino:ino,
				items:items,
				type:"save_final_return",
			},
			function(data,status)
			{
				$("#rep").click();
				//alert(data);
				medicine_indent();
			});
		}
	}
	function printIndent(ind)
	{
		var uhid=$("#uhid").val().trim();
		var ipd=$("#ipd").val().trim();
		var url = "pages/ph_indent_bill_print.php?pId="+btoa(uhid)+"&iPd="+btoa(ipd)+"&iNo="+ind;
		window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function print_indent_list()
	{
		var uhid=btoa($("#uhid").val().trim());
		var ipd=btoa($("#ipd").val().trim());
		var usr=btoa($("#user").text().trim());
		//var url="pages/ipd_pat_medicine_indent_print.php?uhid="+uhid+"&ipd="+ipd+"&user="+usr;
		var url="pages/ph_indent_bill_print.php?uhid="+uhid+"&ipd="+ipd+"&user="+usr;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function cancelInd()
	{
		$('#hide_ind_list').hide();
		$('#indad').show();
		$('#ins_ind').show();
		if($("#printInd").length>0)
		{
			$('#printInd').show();
		}
	}
	</script>
	<?php
}

if($type==999)
{
	
}
