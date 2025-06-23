<?php
session_start();
$emp_id=trim($_SESSION['emp_id']);
if($emp_id=='101')
{
	$dis_field="";
}else
{
	$dis_field="disabled";
}

if($_GET["branch_id"])
{
	$branch_id=$_GET["branch_id"];
}
else
{
	$branch_id=$p_info["branch_id"];
}

$c_name=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `company_name` WHERE `branch_id`='$branch_id'"));
$c_doc=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `company_documents` WHERE `branch_id`='$branch_id'"));
$opd=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `opd_registration_fees` WHERE `branch_id`='$branch_id'"));
$ipd=mysqli_fetch_array(mysqli_query($link,"SELECT * FROM `ipd_registration_fees` WHERE `branch_id`='$branch_id'"));

?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span7" style="margin-left: 0;">
		<table class="table table-bordered table-condensed">
			<tr>
				<td width="30%">Client ID</td>
				<td>
					<input type="text" id="client_id" value="<?php echo $c_name['client_id'];?>" <?php echo $dis_field; ?> />
					<input type="hidden" id="branch_id" value="<?php echo $c_name['branch_id'];?>" <?php echo $dis_field; ?> />
				</td>
			</tr>
			<tr>
				<td width="30%">Name</td>
				<td><input type="text" id="name" value="<?php echo $c_name['name'];?>" <?php echo $dis_field; ?> /></td>
			</tr>
			<tr>
				<td>No of beds</td>
				<td><input type="text" id="bed" value="<?php echo $c_name['no_of_bed'];?>" /></td>
			</tr>
			<tr>
				<td>Address</td>
				<td><textarea id="addr" style="resize:none;"><?php echo $c_name['address'];?></textarea></td>
			</tr>
			<tr>
				<td>City</td>
				<td><input type="text" id="city" value="<?php echo $c_name['city'];?>" /></td>
			</tr>
			<tr>
				<td>Pin Code</td>
				<td><input type="text" id="pin" value="<?php echo $c_name['pincode'];?>" /></td>
			</tr>
			<tr>
				<td>State</td>
				<td><input type="text" id="state" value="<?php echo $c_name['state'];?>" /></td>
			</tr>
			<tr>
				<td>Phone No 1</td>
				<td><input type="text" id="ph1" value="<?php echo $c_name['phone1'];?>" /></td>
			</tr>
			<tr>
				<td>Phone No 2</td>
				<td><input type="text" id="ph2" value="<?php echo $c_name['phone2'];?>" /></td>
			</tr>
			<tr>
				<td>Phone No 3</td>
				<td><input type="text" id="ph3" value="<?php echo $c_name['phone3'];?>" /></td>
			</tr>
			<tr>
				<td>Email</td>
				<td><input type="text" id="email" value="<?php echo $c_name['email'];?>" /></td>
			</tr>
			<tr>
				<td>Website</td>
				<td><input type="text" id="web" value="<?php echo $c_name['website'];?>" /></td>
			</tr>
			<tr>
				<td>Clinical Establishment Act Registration No</td>
				<td><input type="text" id="cer" value="<?php echo $c_doc['cer'];?>" /></td>
			</tr>
			<tr>
				<td>GST No</td>
				<td><input type="text" id="gst" value="<?php echo $c_doc['gst'];?>" /></td>
			</tr>
			<tr>
				<td>Trade Licence No</td>
				<td><input type="text" id="trade_licence" value="<?php echo $c_doc['trade_licence'];?>" /></td>
			</tr>
			<tr>
				<td>Narcotic Drugs Licence No</td>
				<td><input type="text" id="narcotics" value="<?php echo $c_doc['narcotics'];?>" /></td>
			</tr>
			<tr>
				<td>Bio-Medical Waste Authorization No</td>
				<td><input type="text" id="bmw" value="<?php echo $c_doc['bmw'];?>" /></td>
			</tr>
			<tr>
				<td>Spirit Permit No</td>
				<td><input type="text" id="spirit" value="<?php echo $c_doc['spirit'];?>" /></td>
			</tr>
			<tr>
				<td>(MTP) Act 1971 Registration No</td>
				<td><input type="text" id="mtp" value="<?php echo $c_doc['mtp'];?>" /></td>
			</tr>
			<tr>
				<td>Fire Department Permit No</td>
				<td><input type="text" id="fire" value="<?php echo $c_doc['fire'];?>" /></td>
			</tr>
			<tr>
				<td>Pharmacy Licence No</td>
				<td><input type="text" id="pharmacy" value="<?php echo $c_doc['pharmacy'];?>" /></td>
			</tr>
			<tr>
				<form id="upload_logo_form" method="post" enctype="multipart/form-data">
					<td>Upload Logo</td>
					<td>
						<input type="file" id="client_logo" name="client_logo"  />
						<input type="button" id="upload_logo" name="upload_logo" class="btn btn-default" onclick="upload_logo_client()" value="Upload">
						<br>
						<img src="../images/<?php echo $c_name["client_logo"]; ?>">
						<input type="hidden" id="branch_id_img" name="branch_id_img" value="<?php echo $c_name['branch_id'];?>" />
					</td>
				</form>
				<!--<td>Upload Logo</td>
				<td>
					<input type="file" id="client_logo" />
				</td>
				-->
			</tr>
			<tr>
				<td>Installation Date</td>
				<td><input type="text" class="form-control datepicker span2" id="i_date" value="<?php echo $c_name['i_date'];?>" /></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center;">
					<input type="button" id="sav" class="btn btn-info" onclick="save()" value="Save" />
				</td>
			</tr>
		</table>
	</div>
	<div class="span4">
		<table class="table table-bordered table-condensed" style="display:none;">
			<tr>
				<th><h4>Software Options</h4></th>
			</tr>
			<tr>
				<th>Opd Registration Fee<br/>
				<input type="text" id="opd_fee" value="<?php echo $opd['regd_fee'];?>" placeholder="Opd Visit Fee" />
				</th>
			</tr>
			<tr>
				<th>Opd Emergency Fee<br/>
				<input type="text" id="opd_emer_fee" value="<?php echo $opd['emerg_fee'];?>" placeholder="Opd Emergency Fee" />
				</th>
			</tr>
			<tr>
				<th>Opd Validity<br/>
				<input type="text" id="opd_val" value="<?php echo $opd['validity'];?>" placeholder="Validity" />
				</th>
			</tr>
			<tr>
				<th>Ipd Registration Fee<br/>
				<input type="text" id="ipd_fee" value="<?php echo $ipd['regd_fee'];?>" placeholder="Ipd Visit Fee" />
				</th>
			</tr>
			<tr>
				<th>Ipd Validity<br/>
				<input type="text" id="ipd_val" value="<?php echo $ipd['validity'];?>" placeholder="Validity" />
				</th>
			</tr>
			<tr>
				<th>Vaccutainer charges<br/>
				<label><input type="radio" name="vaccu" <?php if($c_name['vaccu_charge']==1){echo "checked='checked'";}?> value="1" /> Yes</label>
				<label><input type="radio" name="vaccu" <?php if($c_name['vaccu_charge']==0){echo "checked='checked'";}?> value="0" /> no</label>
				</th>
			</tr>
			<?php
			if($c_name['uhid_start']>0)
			$udis="disabled='disabled'";
			else
			$udis="";
			if($c_name['pin_start']>0)
			$pdis="disabled='disabled'";
			else
			$pdis="";
			?>
			<tr style="display:none;">
				<th>UHID Starting Number<br/>
				<input type="text" id="uhidnum" value="<?php echo $c_name['uhid_start'];?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" placeholder="UHID Starting Number" <?php echo $udis;?> />
				<?php if($emp_id==101){ ?>
				<button class="btn btn-warning btn-mini" onClick="resett('uhid')">Reset</button>
				<?php } ?>
				</th>
			</tr>
			<tr style="display:none;">
				<th>PIN Starting Number<br/>
				<input type="text" id="pinnum" value="<?php echo $c_name['pin_start'];?>" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" placeholder="PIN Starting Number" <?php echo $pdis;?> />
				<?php if($emp_id==101){ ?>
				<button class="btn btn-warning btn-mini" onClick="resett('pin')">Reset</button>
				<?php } ?>
				</th>
			</tr>
			<tr>
				<td style="text-align:center;">
					<input type="button" id="s" class="btn btn-info" onclick="save_fees()" value="Save" />
				</td>
			</tr>
		</table>
		
		<table class="table table-bordered table-condensed">
	<?php
		$w=1;
		$qry=mysqli_query($link, "SELECT * FROM `company_fees` WHERE `branch_id`='$branch_id' AND `status`=0 ORDER BY `fees_id` ASC");
		while($data=mysqli_fetch_array($qry))
		{
	?>
		<tr>
			<th><?php echo $data["fees_name"]; ?></th>
			<td>
				<input type="text" class="span1 cls hos<?php echo $w; ?>" id="val<?php echo $data["fees_id"]; ?>" value="<?php echo $data["amount_validity"]; ?>" onkeyup="tab_next(event,'<?php echo $w; ?>','<?php echo $data["fees_id"]; ?>','<?php echo $branch_id; ?>')" title="Hit enter to save">
				<?php echo $data["unit"]; ?>
			</td>
		</tr>
	<?php
			$w++;
		}
	?>
		</table>
	</div>
	<!--modal-->
		<a href="#myAlert" data-toggle="modal" id="dl" class="btn" style="display:none;">A</a>
		<div id="myAlert" class="modal hide">
		  <div class="modal-body">
			  <input type="text" id="idl" style="display:none;" />
			<p>Are You Sure Want To Delete...?</p>
		  </div>
		  <div class="modal-footer">
			<a data-dismiss="modal" onclick="delete_bed()" class="btn btn-primary" href="#">Confirm</a>
			<a data-dismiss="modal" onclick="clrr()" class="btn btn-info" href="#">Cancel</a>
		  </div>
		</div>
	  <!--modal end-->
</div>

<!-- Loader -->
<div id="loader" style="margin-top:20%;"></div>
<link rel="stylesheet" href="../css/loader.css" />

<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	$(document).ready(function()
	{
		$("#loader").hide();
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			//defaultDate: '+6m',
			dateFormat: 'yy-mm-dd',
			//minDate: '0',
			yearRange: "-10:+100",
		});
	});
	
	function tab_next(e,slno,id,branch_id)
	{
		$("#val"+id).css("border","");
		numeric($("#val"+id).val(),"val"+id)
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			if($("#val"+id).val()=="")
			{
				$("#val"+id).css("border","2px solid #f00");
				return false;
			}
			
			var vall=$("#val"+id).val();
			if(!vall)
			{
				vall=0;
			}
			
			$.post("pages/company_data.php",
			{
				type:"save_company_fees",
				val:vall,
				id:id,
				branch_id:branch_id,
				user:$("#user").text().trim(),
			},
			function(data,status)
			{
				$(".cls").css("border","");
				$("#val"+id).css("border","3px solid green");
			})
			
			var slno=parseInt(slno)+1;
				$(".hos"+slno).focus();
		}
	}
	function numeric(a,id)
	{
		var n=a.length;
		var numex=/^[0-9]+$/;
		if(a.match(numex))
		{
			
		}
		else
		{
			a=parseInt(a);
			if(!a){ a=""; }
			$("#"+id).val(a);
		}
	}
	function save()
	{
		$("#loader").show();
		$.post("pages/company_data.php",
		{
			branch_id:$("#branch_id").val(),
			client_id:$("#client_id").val(),
			name:$("#name").val(),
			bed:$("#bed").val(),
			addr:$("#addr").val(),
			city:$("#city").val(),
			pin:$("#pin").val(),
			state:$("#state").val(),
			ph1:$("#ph1").val(),
			ph2:$("#ph2").val(),
			ph3:$("#ph3").val(),
			email:$("#email").val(),
			web:$("#web").val(),
			cer:$("#cer").val(),
			gst:$("#gst").val(),
			trade_licence:$("#trade_licence").val(),
			narcotics:$("#narcotics").val(),
			bmw:$("#bmw").val(),
			spirit:$("#spirit").val(),
			mtp:$("#mtp").val(),
			fire:$("#fire").val(),
			pharmacy:$("#pharmacy").val(),
			i_date:$("#i_date").val(),
			usr:$("#user").text().trim(),
			type:"save_company_name",
		},
		function(data,status)
		{
			$("#loader").hide();
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				window.location.reload(true);
				//bootbox.hideAll();
			}, 1000);
		})
	}
	function save_fees()
	{
		var vaccu="";
		if(($("input[type='radio']:checked").length)>0)
		{
			vaccu=$("input[type='radio']:checked").val();
		}
		else
		{
			vaccu=0;
		}
		$("#loader").show();
		$.post("pages/company_data.php",
		{
			branch_id:$("#branch_id").val(),
			opd_fee:$("#opd_fee").val(),
			opd_emer_fee:$("#opd_emer_fee").val(),
			opd_val:$("#opd_val").val(),
			ipd_fee:$("#ipd_fee").val(),
			ipd_val:$("#ipd_val").val(),
			vaccu:vaccu,
			uhidnum:$("#uhidnum").val(),
			pinnum:$("#pinnum").val(),
			usr:$("#user").text().trim(),
			type:"save_reg_fees",
		},
		function(data,status)
		{
			$("#loader").hide();
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
				location.reload();
			}, 1000);
		})
	}
	function upload_logo_client()
	{
		$("#loader").show();
		var input = document.getElementById("upload_logo_form");
		formData= new FormData(input);
		$.ajax({
			url: "pages/logo_upload.php", // Url to which the request is send
			type: "POST",             // Type of request to be send, called as method
			data: formData, // Data sent to server, a set of key/value pairs (i.e. form fields and values)
			contentType: false,       // The content type used when sending data to the server.
			cache: false,             // To unable request pages to be cached
			processData:false,        // To send DOMDocument or non processed data file it is set to false
			success: function(data)   // A function to be called if request succeeds
			{
				$("#loader").hide();
				location.reload();
			}
		});
	}
	function resett(q)
	{
		if(q=="uhid")
		{
			$("#uhidnum").prop('disabled', false);
		}
		if(q=="pin")
		{
			$("#pinnum").prop('disabled', false);
		}
	}
</script>
<style>
	label
	{display:inline-block;margin-right:20px;padding:3px;background:#ffffff;border-radius:5px;}
	label:hover
	{display:inline-block;margin-right:20px;padding:3px;background:#ffffff;border-radius:5px;box-shadow:1px 1px 1px 1px #aaaaaa;}

	input, textarea, .uneditable-input
	{
		width: 90%;
	}
</style>
