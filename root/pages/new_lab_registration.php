<?php
$c_user=$_SESSION["emp_id"];

$branch_str=" AND `branch_id`='$p_info[branch_id]'";
$element_style="display:none";
$center_span="";
if($p_info["levelid"]==1)
{
	$branch_str="";
	//$element_style="";
	//$center_span="";
}

$emp_access=mysqli_fetch_array(mysqli_query($link, " SELECT `levelid`,`edit_info`,`edit_payment`,`cancel_pat`,`discount_permission` FROM `employee` WHERE `emp_id`='$c_user' "));

$center_disable="";
if($emp_access["levelid"]==8)
{
	$center_disable="disabled";
}

$p_type_id=2; // LAB
$category_id=1; // 0=all,1=patho,2=radio,3=cardio
$dept_id=0; // 0=all

$p_type_master=mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `patient_type_master` WHERE `p_type_id`='$p_type_id'"));

$uhid=base64_decode($_GET["uhid"]);
$uhid=trim($uhid);

$pin=base64_decode($_GET["opd"]);
$pin=trim($pin);

if(!$uhid){ $uhid=0; }
if(!$pin){ $pin=0; }

$btn_name="Save";
if($uhid==0)
{
	$btn_name="Update";
}

$patient_id=$uhid;
$opd_id=$pin;

$test_readonly="";
if($opd_id!=0)
{
	if($emp_access["edit_payment"]==0)
	{
		//$test_readonly="readonly";
	}
}

$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));

$pat_name_str=explode(". ",$pat_info["name"]);
$pat_name_title=trim($pat_name_str[0]);
$pat_name=trim($pat_name_str[1]." ".$pat_name_str[2]." ".$pat_name_str[3]." ".$pat_name_str[4]);

if($pat_info["dob"])
{
	$pat_info["dob"]=date("d-m-Y",strtotime($pat_info["dob"]));
}

$pat_reg=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$opd_id' "));

$ref_doc_name=mysqli_fetch_array(mysqli_query($link, " SELECT `refbydoctorid`, `ref_name` FROM `refbydoctor_master` WHERE `refbydoctorid`='$pat_reg[refbydoctorid]' "));

$ref_doc_name["refbydoctorid"]=101;

$item_num=mysqli_num_rows(mysqli_query($link, "SELECT * FROM `patient_test_details` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id'"));
$item_num=$item_num+1;

$uhid_str=base64_decode($_GET['uhid_str']);
$pin_str=base64_decode($_GET['pin_str']);
$fdate_str=base64_decode($_GET['fdate_str']);
$tdate_str=base64_decode($_GET['tdate_str']);
$name_str=base64_decode($_GET['name_str']);
$phone_str=base64_decode($_GET['phone_str']);
$param_str=base64_decode($_GET['param_str']);
$pat_type_str=base64_decode($_GET['pat_type_str']);

$str="";
if($_GET["uhid_str"])
{
	$str.="&uhid_str=$uhid_str";
	$refresh_str.="&uhid_str=$uhid_str";
}

if($_GET["pin_str"])
{
	$str.="&pin_str=$pin_str";
	$refresh_str.="&pin_str=$pin_str";
}

if($_GET["fdate_str"])
{
	$str.="&fdate_str=$fdate_str";
	$refresh_str.="&fdate_str=$fdate_str";
}

if($_GET["tdate_str"])
{
	$str.="&tdate_str=$tdate_str";
	$refresh_str.="&tdate_str=$tdate_str";
}

if($_GET["name_str"])
{
	$str.="&name_str=$name_str";
	$refresh_str.="&name_str=$name_str";
}

if($_GET["phone_str"])
{
	$str.="&phone_str=$phone_str";
	$refresh_str.="&phone_str=$phone_str";
}

if($_GET["param_str"])
{
	$str.="&param=$param_str";
}

if($_GET["pat_type_str"])
{
	$str.="&pat_type_str=$pat_type_str";
	$refresh_str.="&pat_type_str=$pat_type_str";
}


?>
<!--header-->
<div id="content-header">
    <div class="header_div">
		<span class="header"> <?php echo $menu_info["par_name"]; ?></span>
		<br>
		<small>
			( <b style="color:#ff0000;">*</b> ) mark mandatory
		</small>
		<?php if(!$str){ ?>
		<span style="float:right;display:none;">
			<button class="btn btn-search" id="token_list" onclick="estimate_receipt()"><i class="icon-search"></i> Estimate Receipt</button>
		</span>
		<?php } ?>
    </div>
</div>
<!--End-header  container-fluid -->
<div class="">
	<div>
		<span style="float:right;">
		<?php if($str){ ?>
			<button class="btn btn-back" id="add" onclick="window.location='processing.php?v=0<?php echo $str; ?>'"><i class="icon-backward"></i> Back</button>
		<?php } ?>
		</span>
	<?php if($uhid==0){ ?>
		<div class="search_div" style="display:none;">
			<table id="padd_tbl" class="table table-condensed">
				<tr>
					<th>UHID</th>
					<td>
						<input type="text" class="span2" id="search_uhid" onkeyup="load_emp(this.value,event,'uhid')" placeholder="Type UHID" >
					</td>
					<th><?php echo $p_type_master["prefix"]; ?></th>
					<td>
						<input type="text" class="span2" id="search_pin" onkeyup="load_emp(this.value,event,'pin')" placeholder="Type Bill No" >
					</td>
					<th>Name</th>
					<td>
						<input type="text" class="span2" id="search_name" onkeyup="load_emp(this.value,event,'name')" placeholder="Type Name" >
					</td>
					<th>Phone No</th>
					<td>
						<input type="text" class="span2" class="span2" id="search_phone" onkeyup="load_emp(this.value,event,'phone')" placeholder="Type Phone Numebr" >
					</td>
				</tr>
				<tr>
					<td colspan="8">
						<div id="pateint_list" style="max-height:450px;overflow-y:scroll;">
							
						</div>
					</td>
				</tr>
			</table>
		</div>
	<?php } ?>
		<div class="patient_info_div" style="<?php echo $edit_info_style; ?>">
			<table id="patient_info_tbl" class="table table-condensed" style="background-color:#FFF">
				<tr>
					<th colspan="6" style="text-align:center;">
						<h4>Patient Information</h4>
					</th>
				</tr>
		<?php
			if(!$pat_reg)
			{
		?>
				<tr>
					<th>Search UHID</th>
					<td colspan="5">
						<input type="text" class="span3" id="uhid_search" maxlength="20" onKeyup="uhid_search(this,event)" value="<?php echo $pat_info["uhid"]; ?>" placeholder="Search UHID" autofocus>
						<div id="load_patient_phone"></div>
						<input type="hidden" id="pat_tr_select" value="1">
					</td>
				</tr>
		<?php
			}
		?>
				<tr>
					<th>Name <b style="color:#ff0000;">*</b></th>
					<td>
						<select id="name_title" onChange="name_title_ch(this.value)" onKeyup="name_title_up(this,event)" class="span1 pat_info">
						<?php
							$title_qry=mysqli_query($link, " SELECT * FROM `name_title` ORDER BY `title_id` ");
							while($val=mysqli_fetch_array($title_qry))
							{
								if($pat_name_title."."==$val['title']){ $title_sel="selected"; }else{ $title_sel=""; }
								echo "<option value='$val[title]' $title_sel>$val[title]</option>";
							}
						?>
						</select>
						<input type="text" class="capital pat_info" id="pat_name" onKeyup="pat_name_up(this,event)" maxlength="50" value="<?php echo $pat_name; ?>" style="width: 180px;">
					</td>
					<th style="display:none;">DOB (DD-MM-YYYY) </th>
					<td style="display:none;">
						<input type="text" id="dob" class="span2 dob pat_info" maxlength="10" placeholder="DD-MM-YYYY" onKeyup="dob_up(this,event)" value="<?php echo $pat_info["dob"]; ?>" onpaste="return false;" ondrop="return false;">
					</td>
					<th>Age <b style="color:#ff0000;">*</b></th>
					<td>
						<span>
							<input type="text" id="age_y" class="numericc pat_info" onKeyup="age_y_check(this,event)" placeholder="Years" title="Years" style="width: 50px;" onpaste="return false;" ondrop="return false;" maxlength="3">
							<input type="text" id="age_m" class="numericc pat_info" onKeyup="age_m_check(this,event)" placeholder="Months" title="Months" style="width: 50px;" onpaste="return false;" ondrop="return false;" maxlength="2">
							<input type="text" id="age_d" class="numericc pat_info" onKeyup="age_d_check(this,event)" placeholder="Days" title="Days" style="width: 50px;" onpaste="return false;" ondrop="return false;" maxlength="2">
						</span>
						<span style="display:none;">
							<input type="text" id="age" class="span1" onKeyup="age_check(this.value,event)" onBlur="border_color_blur(this.id,this.value)" value="<?php echo $pat_info["age"]; ?>">
							<text id="year"><?php echo $pat_info["age_type"]; ?></text>
							<span id="g_id" style="display:none;"><input type="text" id="grdn_name" class="span3" placeholder="Enter Guardian Name" onKeyup="caps_it(this.value,this.id,event)" onBlur="border_color_blur(this.id,this.value)"></span>
						</span>
					</td>
				</tr>
				<tr>
					<th>Sex <b style="color:#ff0000;">*</b></th>
					<td>
						<select id="sex" class="span3 pat_info" onKeyup="sex_up(this,event)" onchange="search_test()">
							<option value="Male" <?php if($pat_info["sex"]=="Male"){ echo "selected"; } ?>>Male</option>
							<option value="Female" <?php if($pat_info["sex"]=="Female"){ echo "selected"; } ?>>Female</option>
							<option value="Other" <?php if($pat_info["sex"]=="Other"){ echo "selected"; } ?>>Other</option>
						</select>
					</td>
					
					<th>City / Village</th>
					<td>
						<input type="text" class="capital pat_info" onkeyup="city_up(this,event)" id="city" value="<?php echo $pat_info["city"]; ?>" list="city_datalist">
						<datalist id="city_datalist">
						<?php
							//~ $city_datalist_qry=mysqli_query($link," SELECT DISTINCT `city` FROM `patient_info_rel` WHERE `city`!='' ORDER BY `city` ");
							//~ while($city_datalist=mysqli_fetch_array($city_datalist_qry))
							//~ {
								//~ echo "<option value='$city_datalist[city]'></option>";
							//~ }
						?>
						</datalist>
					</td>
				</tr>
				<tr>
					<th>UHID <b style="color:#ff0000;">*</b></th>
					<td colspan="3">
						<input type="text" class="span3 pat_info" onkeyup="pat_uhid_up(this,event)" id="pat_uhid" maxlength="20" value="<?php echo $pat_info["uhid"]; ?>">
					</td>
				</tr>
				
				<tr style="display:none;">
					<th style="display:none;">Phone</th>
					<td style="display:none;">
						<input type="text"class="span2 numericc pat_info" id="phone" maxlength="10" onKeyup="phone_up(this,event)" value="<?php echo $pat_info["phone"]; ?>" onpaste="return false;" ondrop="return false;">
					</td>
				</tr>
				
			</table>
		</div>
		<div class="doctor_info_div" id="test_sel" style="<?php echo $edit_payment_style; ?>">
			<div id="list_all_test" class="up_div"></div>
			<div id="msg" align="center"></div>
			<div id="load_patient_info_div" style="display:none;"></div>
			<table id="doctor_info_tbl" class="table table-condensed" style="background-color:#FFF">
				<tr>
					<th colspan="6" style="text-align:center;">
						<h4>Investigation Information</h4>
					</th>
				</tr>
				<tr style="display:none;">
					<th style="width: 14%;">Referred By <b style="color:#ff0000;">*</b></th>
					<td colspan="1">
						<input type="text" class="span3" name="r_doc" id="r_doc" onFocus="ref_load_focus()" onKeyUp="ref_load_refdoc(this.value,event,'opd')" onBlur="javascript:$('#ref_doc').fadeOut(500)" value="<?php echo $ref_doc_name["ref_name"]; ?>" />
						<input type="hidden" id="refbydoctorid" value="<?php echo $ref_doc_name["refbydoctorid"]; ?>">
						<!--<button class="btn btn-new btn-mini " name="new_doc" id="new_doc" value="New" onClick="load_new_ref_doc()" style="margin-bottom: 15px;"><i class="icon-edit"></i> New</button>-->
						<div id="doc_info"></div>
						<div id="ref_doc" align="center">
							<table style="background-color:#FFF;" class="table table-bordered table-condensed" id="center_table">
								<th>ID</th>
								<th>Doctor Name</th>
								<?php
									$d=mysqli_query($link, "select * from refbydoctor_master where refbydoctorid='101' order by ref_name");
									$i=1;
									while($d1=mysqli_fetch_array($d))
									{
								?>
									<tr onClick="doc_load('<?php echo $d1['refbydoctorid'];?>','<?php echo $d1['ref_name'];?>','<?php echo $mrk['name'];?>')" style="cursor:pointer" <?php echo "id=doc".$i;?>>
										<td>
											<?php echo $d1['refbydoctorid'];?>
										</td>
										<td>
											<?php echo $d1['ref_name'];?>
											<div <?php echo "id=dvdoc".$i;?> style="display:none;">
												<?php echo "#".$d1['refbydoctorid']."#".$d1['ref_name'];?>
											</div>
										</td>
									</tr>
								<?php
									$i++;
									}
								?>
							</table>
						</div>
					</td>
					
					<th style="<?php echo $element_style; ?>">Branch <b style="color:#ff0000;">*</b></th>
					<td colspan="1" style="<?php echo $element_style; ?>">
						<select id="branch_id" class="span2 branch_id" onchange="branch_change(this,event)" onkeyup="branch_up(this,event)">
						<?php
							$qry=mysqli_query($link, "SELECT `branch_id`,`name` FROM `company_name` WHERE `name`!='' $branch_str ORDER BY `branch_id` ASC");
							while($data=mysqli_fetch_array($qry))
							{
								if($pat_reg)
								{
									if($pat_reg["branch_id"]==$data["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
								}
								else
								{
									if($data["branch_id"]==$p_info["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
								}
								echo "<option value='$data[branch_id]' $branch_sel>$data[name]</option>";
							}
						?>
						</select>
					</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<th>Patient Type <b style="color:#ff0000;">*</b></th>
					<td style="width: 300px;">
						<select id="reg_type" onchange="reg_type_ch()" onkeyup="reg_type_up(this,event)">
							<option value="1" <?php if($pat_reg["reg_type"]==1){ echo "selected"; } ?>>OPD</option>
							<option value="2" <?php if($pat_reg["reg_type"]==2){ echo "selected"; } ?>>IPD</option>
						</select>
					</td>
					<th class="ipd_td" style="display:none;width: 200px;">IPD No. <b style="color:#ff0000;">*</b></th>
					<td class="ipd_td" style="display:none;">
						<input type="text" class="span pat_info" onkeyup="hospital_no_up(this,event)" id="hospital_no" maxlength="20" value="<?php echo $pat_reg["hospital_no"]; ?>">
					</td>
					<th class="ipd_td" style="display:none;">Ward. <b style="color:#ff0000;">*</b></th>
					<td class="ipd_td" style="display:none;">
						<select id="ward_id" onkeyup="ward_id_up(this,event)">
							<option value="0">Select</option>
					<?php
						$qry=mysqli_query($link, "SELECT `ward_id`, `name` FROM `ward_master` WHERE `name`!='' ORDER BY `name` ASC");
						while($data=mysqli_fetch_array($qry))
						{
							if($data["ward_id"]==$pat_reg["hguide_id"]){ $sel="selected"; }else{ $sel=""; }
							echo "<option value='$data[ward_id]' $sel>$data[name]</option>";
						}
					?>
						</select>
					</td>
					<td class="opd_td" colspan="5"></td>
				</tr>
				<tr>
					<th>Bill Type <b style="color:#ff0000;">*</b></th>
					<td colspan="1">
						<select id="center_no" class="<?php echo $center_span; ?> center_no" onchange="center_no_change_new(this,event)" onkeyup="center_no_up(this,event)" <?php echo $center_disable; ?>>
							<option value="0">Select</option>
						</select>
					</td>
					<th style="width: 200px;">Receipt No. <b style="color:#ff0000;">*</b></th>
					<td>
						<input type="text" class="doctor_info" onkeyup="receipt_no_up(this,event)" id="receipt_no" maxlength="20" value="<?php echo $pat_reg["receipt_no"]; ?>">
					</td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<th style="display:none;">Select Category <b style="color:#ff0000;">*</b></th>
					<td style="display:none;">
						<select class="span2 doctor_info" id="category_id" onchange="category_change(this,event)" onKeyUp="category_up(this,event)">
					<?php
						$cat_qry=mysqli_query($link, " SELECT `category_id`, `name` FROM `test_category_master` WHERE `name`!='' ORDER BY `category_id` ASC ");
						while($cat=mysqli_fetch_array($cat_qry))
						{
							if($cat["category_id"]==$category_id){ $cat_sel="selected"; }else{ $cat_sel=""; }
							echo "<option value='$cat[category_id]' $cat_sel>$cat[name]</option>";
						}
					?>
						</select>
					</td>
					<th style="display:none;">Select Department <b style="color:#ff0000;">*</b></th>
					<td style="display:none;">
						<select class="span2 doctor_info" id="dept_id" onchange="dept_change(this,event)" onKeyUp="dept_up(this,event)">
							<option value="0">Select</option>
					<?php
						$dept_qry=mysqli_query($link, " SELECT `id`, `name` FROM `test_department` WHERE `name`!='' ORDER BY `name` ASC ");
						while($dept=mysqli_fetch_array($dept_qry))
						{
							if($dept_id==$dept["id"]){ $dept_sel="selected"; }else{ $dept_sel=""; }
							echo "<option value='$dept[id]' $dept_sel>$dept[name]</option>";
						}
					?>
						</select>
					</td>
				</tr>
				<tr>
					<th class="span3">Select Test <b style="color:#ff0000;">*</b></th>
					<td colspan="5">
						<input type="text" class="doctor_info" name="test" id="test" onKeyUp="select_test_new(this.value,event)" style="width:46%;" placeholder="Search Test Name Here" <?php echo $test_readonly; ?> />
						<button class="btn btn-search btn-mini" id="test_view_btn" onClick="show_selected_test()" style="margin-bottom: 1%;display:none;"><i class="icon-eye-open"></i> Test</button>
						<input type="hidden" id="test_ids" value="">
						<input type="hidden" name="tr_counter" id="tr_counter" class="form-control" value="<?php echo $item_num; ?>"/>
					</td>
					<th style="display:none;">Total</th>
					<td style="display:none;">
						<input type="text" id="total" value="<?php echo $tot_amount; ?>" readonly>
					</td>
				</tr>
				<tr>
					<td colspan="6">
						<div id="test_d">
							
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div class="payment_info_div" style="<?php echo $edit_payment_style; ?>">
			<table id="payment_info_tbl" class="table table-condensed" style="background-color:#FFF">
				<!--<tr>
					<th colspan="6" style="text-align:center;">
						<h4>Payment Information</h4>
					</th>
				</tr>-->
				<tr>
					<td colspan="6">
						<div id="payment_info_div"></div>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<!-- Loader -->
<div id="loader" style="margin-top:0%;"></div>
<link rel="stylesheet" href="../css/loader.css" />

<input type="hidden" class="span1" id="p_type_id" value="<?php echo $p_type_id; ?>">
<input type="hidden" class="span1" id="patient_id" value="<?php echo $patient_id; ?>">
<input type="hidden" class="span1" id="opd_id" value="<?php echo $opd_id; ?>">
<input type="hidden" class="span1" id="pat_reg_type" value="0">

<input type="button" data-toggle="modal" data-target="#myModal1" id="mod" style="display:none"/>
<input type="hidden" id="mod_chk" value="0"/>
<div class="modal fade" id="myModal1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div id="results"> </div>
			</div>
		</div>
	</div>
</div>

<input type="button" data-toggle="modal" data-target="#myModal2" id="mod2" style="display:none"/>
<input type="hidden" id="mod_chk2" value="0"/>
<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div id="results2"> </div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		$("#loader").hide();
		$("#myModal1").hide();
		
		setTimeout(function(){
			
			if($("#patient_id").val()!="0")
			{
				cal_age_all('');
			}
			
			load_center();
			
			var item_chk=$("#test_list tr").length;
			if(item_chk>0)
			{
				$("#test_view_btn").show();
			}
			
		},100);
		setTimeout(function(){
			reg_type_ch();
		},300);
		setTimeout(function(){
			if($("#patient_id").val()!="0" && $("#opd_id").val()!="0")
			{
				load_saved_test_list();
			}
		},500);
		setTimeout(function(){
			load_payment_info();
		},700);
	});
	
	$(document).on('keyup', ".capital", function () {
		$(this).val(function (_, val) {
			return val.toUpperCase();
		});
	});
	
	$(document).on('keyup', ".numericc", function () {
		$(this).val(function (_, val) {
			var num=parseInt(val);
			if(!num){ num=""; }
			return num;
		});
	});
	$(document).on('keyup', ".numericfloat", function () {
		$(this).val(function (_, val)
		{
			if(val==0)
			{
				return val;
			}
			else if(val==".")
			{
				return "0.";
			}
			else
			{
				var n=val.length;
				var numex=/^[0-9.]+$/;
				if(val[n-1].match(numex))
				{
					return number;
				}
				else
				{
					val=val.slice(0,n-1);
					return val;
				}
			}
		});
	});
	
	function reg_type_ch()
	{
		if($("#reg_type").val()==1)
		{
			$(".ipd_td").hide();
			$(".opd_td").show();
		}
		if($("#reg_type").val()==2)
		{
			$(".opd_td").hide();
			$(".ipd_td").show();
		}
	}
	
	var _changeInterval = null;
	function uhid_search(dis,e)
	{
		clearInterval(_changeInterval)
		_changeInterval = setInterval(function() {
			// Typing finished, now you can Do whatever after 2 sec
			clearInterval(_changeInterval);
			uhid_search_data(dis,e);
		}, 500);
	}
	
	function uhid_search_data(dis,e)
	{
		var pat_tr_select=$("#pat_tr_select").val();
		
		if(e.which==13) // Enter
		{
			var pat_tr_select=parseInt(pat_tr_select);
			var pat_uhid=$("#pat_uhid"+pat_tr_select).val();
			if(pat_uhid)
			{
				load_emp_details(pat_uhid,'uhid');
			}
			else
			{
				$("#pat_uhid").val($(dis).val());
				$("#name_title").focus();
			}
			return false;
		}
		
		if(e.which==38) // Up
		{
			var pat_tr_select_next=parseInt(pat_tr_select)-1;
			if($("#pat_tr"+pat_tr_select_next).is(":visible"))
			{
				select_next_tr(pat_tr_select_next);
			}
			return false;
		}
		if(e.which==40) // Down
		{
			var pat_tr_select_next=parseInt(pat_tr_select)+1;
			if($("#pat_tr"+pat_tr_select_next).is(":visible"))
			{
				select_next_tr(pat_tr_select_next);
			}
			return false;
		}
		
		$.post("pages/new_lab_registration_data.php",
		{
			type:"uhid_search",
			uhid:$(dis).val(),
		},
		function(data,status)
		{
			$("#load_patient_phone").html(data);
			$("#pat_tr_select").val(1);
			$("#pat_tr1").css({"background-color":"yellow"});
		})
	}
	function select_next_tr(tr_num)
	{
		$(".pat_tr").css({"background-color":"white"});
		$("#pat_tr"+tr_num).css({"background-color":"yellow"});
		
		$("#pat_tr_select").val(tr_num);
	}
	
	function load_center()
	{
		$.post("pages/new_lab_registration_data.php",
		{
			type:"load_center",
			branch_id:$("#branch_id").val(),
			patient_id:$("#patient_id").val(),
			opd_id:$("#opd_id").val(),
		},
		function(data,status)
		{
			$("#center_no").html(data);
		})
	}
	
	var emp_d=1;
	var emp_div=0;
	function load_emp(val,e,typ)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			var eid=$("#e_id"+emp_d+"").val();
			eid=eid.split("@@");
			var tst=$("#testt").val();
			load_emp_details(eid[0],eid[1]);
		}
		else if(unicode==38)
		{
			var chk=emp_d-1;
			var cc=$("#row_id"+chk+"").html();
			if(cc)
			{
				$(".all_pat_row").css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				
				emp_d=emp_d-1;
				$("#row_id"+emp_d).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
				var emp_d1=emp_d+1;
				$("#row_id"+emp_d1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				var z2=emp_d%1;
				if(z2==0)
				{
					emp_div=emp_div-30;
					$("#pateint_list").scrollTop(emp_div)
					
				}
			}
		}
		else if(unicode==40)
		{
			var chk=emp_d+1;
			var cc=$("#row_id"+chk+"").html();
			if(cc)
			{
				$(".all_pat_row").css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				
				emp_d=emp_d+1;
				$("#row_id"+emp_d).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
				var emp_d1=emp_d-1;
				$("#row_id"+emp_d1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				var z2=emp_d%1;
				if(z2==0)
				{
					$("#pateint_list").scrollTop(emp_div)
					emp_div=emp_div+30;
				}
			}
		}
		else
		{
			if(val.length>0)
			{
				$.post("pages/new_lab_registration_data.php",
				{
					val:val,
					type:"search_patients",
					typ:typ,
					p_type_id:$("#p_type_id").val(),
				},
				function(data,status)
				{
					$("#pateint_list").slideDown(400).html(data);
				})
			}else if(val.length==0)
			{
				$("#pateint_list").html("");
			}
		}
	}
	function load_emp_details(uhid,typ)
	{
		$("#patient_id").val(uhid);
		$("#pateint_list").slideUp(400);
		$(".patient_info_div").slideUp(500);
		
		$("#pat_reg_type").val("1");
		
		$("#reg_type").focus();
		
		load_patient_info();
		setTimeout(function(){
			load_payment_info();
		},50);
	}
	
	function load_patient_info()
	{
		$.post("pages/new_lab_registration_data.php",
		{
			type:"load_patient_info",
			patient_id:$("#patient_id").val(),
		},
		function(data,status)
		{
			$("#load_patient_info_div").html(data).show();
		})
	}
	function load_payment_info()
	{
		$("#loader").show();
		$.post("pages/new_lab_registration_data.php",
		{
			type:"load_payment_info",
			patient_id:$("#patient_id").val(),
			opd_id:$("#opd_id").val(),
			p_type_id:$("#p_type_id").val(),
		},
		function(data,status)
		{
			//$("#payment_info_div").html(data);
			
			$("#advance_paid_div").html("").slideUp(400);
			$("#payment_info_div").html(data).slideDown(900);
			
			if($("#opd_id").val()!="0")
			{
				setTimeout(function(){
					//scrollPage(850);
				},100);
			}
			setTimeout(function(){
				$("#loader").hide();
				if($("#opd_id").val()!="0")
				{
					$("#load_sample_btn").focus();
				}
			},1200);
		})
	}
	
	function branch_change(dis,e)
	{
		load_center();
		$("#ad_doc").val("");
		$("#consultantdoctorid").val("0");
	}
	function branch_up(dis,e)
	{
		$(dis).css({"border-color":""});
		if(e.which==13)
		{
			if($(dis).val()=="0")
			{
				$(dis).css({"border-color":"red"});
			}
			else
			{
				$("#center_no").focus();
			}
		}
	}
	
	function name_title_up(dis,e)
	{
		if(e.which==13)
		{
			$("#pat_name").focus();
		}
	}
	
	function pat_name_up(dis,e)
	{
		$(dis).css({"border-color":""});
		if(e.which==13)
		{
			if($(dis).val()=="")
			{
				$(dis).css({"border-color":"red"});
			}
			else
			{
				//$("#dob").focus();
				$("#age_y").focus();
			}
		}
	}
	
	function dob_up(dis,e)
	{
		$("#dob").css({"border-color":""});
		$("#age_y").css({"border-color":""});
		$("#age_m").css({"border-color":""});
		$("#age_d").css({"border-color":""});
		
		var val=$(dis).val();
		
		//alert(val[2]);
		
		//~ var txt2 = val.slice(0, 2) + "-" + val.slice(2);
		//~ alert(txt2);
		
		var len=val.length;
		if(len<=11)
		{
			if(len==2 || len==5)
			{
				$("#dob").val(val+"-");
			}
			if(len>9)
			{
				//~ cal_age(e);
				cal_age_all(e);
			}
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(unicode==13)
			{
				if(len>=10)
				{
					if($("#age_y").val()=="" || $("#age_m").val()=="" || $("#age_d").val()=="")
					{
						$("#age_y").focus();
					}
					else
					{
						$("#sex").focus();
					}
				}else
				{
					$("#age_y").focus();
				}
			}
			var n=val.length;
			var numex=/^[0-9-]+$/;
			if(val[n-1].match(numex))
			{
				
			}
			else
			{
				val=val.slice(0,n-1);
				$("#dob").val(val);
			}
		}
	}
	function sex_up(dis,e)
	{
		if(e.which==13)
		{
			//$("#phone").focus();
			$("#city").focus();
		}
	}
	function phone_up(dis,e)
	{
		$(dis).css({"border-color":""});
		if(e.which==13)
		{
			//~ if($(dis).val()=="")
			//~ {
				//~ $(dis).css({"border-color":"red"});
			//~ }
			//~ else if($(dis).val().length!=10)
			//~ {
				//~ $(dis).css({"border-color":"red"});
			//~ }
			//~ else
			//~ {
				//~ $("#marital_status").focus();
			//~ }
			$("#marital_status").focus();
		}
	}
	
	function city_up(dis,e)
	{
		if(e.which==13)
		{
			$("#pat_uhid").focus();
		}
		$(dis).css({"border-color":""});
		//~ if(e.which==13)
		//~ {
			//~ if($(dis).val()=="")
			//~ {
				//~ $(dis).css({"border-color":"red"});
			//~ }
			//~ else
			//~ {
				//~ //$("#police").focus();
				//~ $("#r_doc").focus();
			//~ }
		//~ }
	}
	
	function pat_uhid_up(dis,e)
	{
		$(dis).css({"border-color":""});
		if(e.which==13)
		{
			if($(dis).val()=="")
			{
				$(dis).css({"border-color":"red"});
			}
			else
			{
				$("#reg_type").focus();
			}
		}
	}
	
	function reg_type_up(dis,e)
	{
		$(dis).css({"border-color":""});
		if(e.which==13)
		{
			if($("#reg_type").val()==1)
			{
				$("#center_no").focus();
			}
			if($("#reg_type").val()==2)
			{
				$("#hospital_no").focus();
			}
		}
	}
	
	function hospital_no_up(dis,e)
	{
		$(dis).css({"border-color":""});
		if(e.which==13)
		{
			if($(dis).val()=="")
			{
				$(dis).css({"border-color":"red"});
			}
			else
			{
				$("#ward_id").focus();
			}
		}
	}
	
	function ward_id_up(dis,e)
	{
		$(dis).css({"border-color":""});
		if(e.which==13)
		{
			if($(dis).val()=="0")
			{
				$(dis).css({"border-color":"red"});
			}
			else
			{
				$("#center_no").focus();
			}
		}
	}
	
	function center_no_up(dis,e)
	{
		$(dis).css({"border-color":""});
		if(e.which==13)
		{
			$("#receipt_no").focus();
		}
	}
	
	function receipt_no_up(dis,e)
	{
		$(dis).css({"border-color":""});
		if(e.which==13)
		{
			if($(dis).val()=="")
			{
				$(dis).css({"border-color":"red"});
			}
			else
			{
				$("#test").focus();
			}
		}
	}
	
	// Refer Doctor Start
	function ref_load_focus()
	{
		$("#ref_doc").fadeIn(500);
		$("#r_doc").select();
		setTimeout(function(){ $("#chk_val2").val(1)},1000);
	}
	var doc_tr=1;
	var doc_sc=0;
	function ref_load_refdoc(val,e,typ)
	{
		$("#r_doc").css({"border-color":""});
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode!=13)
		{
			if(unicode!=40 && unicode!=38)
			{
				$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
				$("#ref_doc").fadeIn(500);
				$.post("pages/new_lab_registration_data.php",
				{
					val:val,
					type:"load_ref_doctor",
				},
				function(data,status)
				{
					$("#ref_doc").html(data);
					doc_tr=1;
					doc_sc=0;
				})	
			}
			else if(unicode==40)
			{
				var chk=doc_tr+1;
				var cc=document.getElementById("doc"+chk).innerHTML;
				if(cc)
				{
					doc_tr=doc_tr+1;
					$("#doc"+doc_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var doc_tr1=doc_tr-1;
					$("#doc"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=doc_tr%1;
					if(z3==0)
					{
						$("#ref_doc").scrollTop(doc_sc)
						doc_sc=doc_sc+30;
					}
				}
			}
			else if(unicode==38)
			{
				var chk=doc_tr-1;
				var cc=document.getElementById("doc"+chk).innerHTML;
				if(cc)
				{
					doc_tr=doc_tr-1;
					$("#doc"+doc_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var doc_tr1=doc_tr+1;
					$("#doc"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=doc_tr%1;
					if(z3==0)
					{
						doc_sc=doc_sc-30;
						$("#ref_doc").scrollTop(doc_sc)
					}
				}
			}
		}
		else
		{
			var docs=document.getElementById("dvdoc"+doc_tr).innerHTML.split("#");
			var doc_id=docs[1].trim();
			var doc_naam=docs[2].trim();
			//$("#r_doc").val(doc_naam+"-"+doc_id);
			var d_in=docs[3];
			//$("#doc_mark").val(docs[5]);
			$("#doc_info").html(d_in);
			$("#doc_info").fadeIn(500);
			
			doc_load(doc_id,doc_naam);
		}
	}
	function doc_load(doc_id,name)
	{
		$("#refbydoctorid").val(doc_id);
		$("#r_doc").val(name);
		$("#doc_info").html("");
		$("#ref_doc").fadeOut(500);
		//$("#dept_id").focus();
		
		if($("#branch_id").is(":visible"))
		{
			$("#branch_id").focus();
		}
		else if($("#center_no").is(":visible"))
		{
			if($("#center_no").is(":disabled"))
			{
				$("#test").focus();
			}
			else
			{
				$("#center_no").focus();
			}
		}
		
	}
	// Refer Doctor End
	
	
	// Test Search Start
	function test_enable()
	{
		setTimeout(function(){ $("#chk_val").val(1)},500);	
		$({myScrollTop:window.pageYOffset}).animate({myScrollTop:110}, {
			duration: 1000,
			easing: 'swing',
			step: function(val){
				window.scrollTo(0, val);
			}
		});
	}
	var t_val=1;
	var t_val_scroll=0;

	var _changeInterval = null;
	function select_test_new(val,e)
	{
		if($("#patient_id").val()=="0")
		{
			scrollPage(280);
		}
		else
		{
			if($("#opd_id").val()=="0")
			{
				scrollPage(40);
			}
			else
			{
				scrollPage(330);
			}
		}
		
		clearInterval(_changeInterval)
		_changeInterval = setInterval(function() {
			// Typing finished, now you can Do whatever after 2 sec
			clearInterval(_changeInterval);
			select_test_new_res(val,e);
		}, 100);
	}
	function select_test_new_res(val,e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			var tst=document.getElementsByClassName("test"+t_val);
			load_test_new(''+tst[1].value.trim()+'',''+tst[2].innerHTML.trim()+'',''+tst[3].innerHTML.trim()+'');
			$("#list_all_test").slideDown(400);
			$("#test").val("").focus();
		}
		else if(unicode==40)
		{
			var chk=t_val+1;
			var cc=document.getElementById("td"+chk).innerHTML;
			if(cc)
			{
				t_val=t_val+1;
				$("#td"+t_val).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
				var t_val1=t_val-1;
				$("#td"+t_val1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				var z2=t_val%1;
				if(z2==0)
				{
					$("#test_d").scrollTop(t_val_scroll)
					t_val_scroll=t_val_scroll+30;
				}
			}	
		}
		else if(unicode==38)
		{
			var chk=t_val-1;
			var cc=document.getElementById("td"+chk).innerHTML;
			if(cc)
			{
				t_val=t_val-1;
				$("#td"+t_val).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
				var t_val1=t_val+1;
				$("#td"+t_val1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				var z2=t_val%1;
				if(z2==0)
				{
					t_val_scroll=t_val_scroll-30;
					$("#test_d").scrollTop(t_val_scroll)
					
				}
			}	
		}
		else if(unicode==27)
		{
			setTimeout(function(){
				
				$("#list_all_test").slideUp(500)
				$("#test_d").html("");
				
				$("#pat_save_btn").focus();
			},100);
		}
		else
		{
			search_test();
		}
	}
	
	function search_test()
	{
		$.post("pages/new_lab_registration_data.php",
		{
			type:"search_test",
			test:$("#test").val(),
			patient_id:$("#patient_id").text().trim(),
			opd_id:$("#opd_id").text().trim(),
			center_no:$("#center_no").val(),
			category_id:$("#category_id").val(),
			dept_id:$("#dept_id").val(),
			refbydoctorid:$("#refbydoctorid").val(),
			sex:$("#sex").val(),
		},
		function(data,status)
		{
			$("#test_d").html(data);
			t_val=1;
			t_val_scroll=0;
			$("#test_d").scrollTop(t_val_scroll)
		})
	}
	
	function load_test_click(id,name,rate)
	{
		load_test_new(id,name,rate);
		$("#list_all_test").slideDown(400);
	}
	
	
	function load_test_new(id,name,rate)
	{
		//alert(id+" "+name+" "+rate);
		var item_chk=$("#test_list tr").length;
		if(!item_chk){ item_chk=0; }
		
		if(item_chk==0)
		{
			load_table(id,name,rate);
		}
		else
		{
			load_items(id,name,rate);
		}
	}
	function load_table(id,name,rate)
	{
		$.post("pages/new_lab_registration_data.php",
		{
			type:"load_item_table",
		},
		function(data,status)
		{
			$("#list_all_test").html(data);
			load_items(id,name,rate);
		})
	}
	
	function load_items(testid,name,rate)
	{
		var test_ids=$("#test_ids").val();
		
		var testid_chk="##"+testid+"@0";
		
		var index = test_ids.indexOf(testid_chk); // Find the index of the character
        if(index !== -1)
        {
			$("#test_sel").css({'opacity':'0.5'});
			$("#msg").html("<span style='color:red;font-weight:bold;'>Already Selected</span>");
			var x=$("#test_sel").offset();
			var w=$("#msg").width()/2;
			$("#msg").css({'top':x.top,'left':'50%','margin-left':-w+'px'});
			$("#msg").fadeIn(500);
			setTimeout(function(){$("#msg").fadeOut(500,function(){$("#test_sel").css({'opacity':'1.0'}); })},2000);
			return false;
		}
		
		test_ids=test_ids+"##"+testid+"@0";
		$("#test_ids").val(test_ids);
		load_test_list();
	}
	
	function load_items_old(id,name,rate)
	{
		var each_row=$(".each_row");
		for(var i=0;i<each_row.length;i++)
		{
			var tr_counter=each_row[i].value;
			
			var testid=$("#testid"+tr_counter).val();
			
			if(testid==id)
			{
				$("#test_sel").css({'opacity':'0.5'});
				$("#msg").html("<span style='color:red;font-weight:bold;'>Already Selected</span>");
				var x=$("#test_sel").offset();
				var w=$("#msg").width()/2;
				$("#msg").css({'top':x.top,'left':'50%','margin-left':-w+'px'});
				$("#msg").fadeIn(500);
				setTimeout(function(){$("#msg").fadeOut(500,function(){$("#test_sel").css({'opacity':'1.0'}); })},2000);
				return false;
			}
		}
		
		var tr_counter=$("#tr_counter").val().trim();
		
		$.post("pages/new_lab_registration_data.php",
		{
			type:"add_items",
			testid:id,
			test_name:name,
			test_rate:rate,
			tr_counter:tr_counter,
			c_discount:$("#c_discount").val(),
			center_no:$("#center_no").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#item_footer").before(data);
			
			var next_tr_counter=parseInt($("#tr_counter").val())+1;
			$("#tr_counter").val(next_tr_counter);
			
			$("#list_all_test").animate({ scrollTop: 2900 });
			
			setTimeout(function(){
				$("#test").focus();
			},300);
		})
	}
	function load_test_list()
	{
		$.post("pages/new_lab_registration_data.php",
		{
			type:"load_test_list",
			branch_id:$("#branch_id").val(),
			p_type_id:$("#p_type_id").val(),
			patient_id:$("#patient_id").val(),
			opd_id:$("#opd_id").val(),
			center_no:$("#center_no").val(),
			test_ids:$("#test_ids").val(),
			c_discount:$("#c_discount").val(),
			refbydoctorid:$("#refbydoctorid").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#list_all_test").html(data);
			
			$("#list_all_test").animate({ scrollTop: 2900 });
			
			setTimeout(function(){
				$("#test").focus();
			},100);
		})
	}
	function load_saved_test_list()
	{
		$.post("pages/new_lab_registration_data.php",
		{
			type:"load_saved_test_list",
			p_type_id:$("#p_type_id").val(),
			patient_id:$("#patient_id").val(),
			opd_id:$("#opd_id").val(),
			center_no:$("#center_no").val(),
			test_ids:$("#test_ids").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#list_all_test").html(data);
			
			$("#list_all_test").animate({ scrollTop: 2900 });
			$("#test_view_btn").show();
			
			setTimeout(function(){
				$("#test").focus();
			},100);
		})
	}
	
	// Test Search End
	
	function remove_tr(val,testid)
	{
		var test_ids=$("#test_ids").val();
		
		var testid_replace="##"+testid+"@0";
		
		var new_test_ids = test_ids.replace(testid_replace, ""); // Replace with ''
		
		$("#test_ids").val(new_test_ids);
		
		load_test_list();
	}
	
	// Save Patient Start
	function pat_save()
	{
		if($("#pat_name").val()=="" && $("#patient_id").val()=="0")
		{
			alert("1");
			//scrollPage(210);
			$("#pat_name").focus().css({"border-color":"red"});
			return false;
		}
		if($("#dob").val()=="" && $("#patient_id").val()=="0")
		{
			alert("2");
			//scrollPage(210);
			$("#age_y").focus().css({"border-color":"red"});;
			return false;
		}
		//~ if(($("#phone").val()=="" && $("#patient_id").val()=="0") || ($("#phone").val().length!=10 && $("#patient_id").val()=="0"))
		//~ {
			//~ scrollPage(230);
			//~ $("#phone").focus().css({"border-color":"red"});;
			//~ return false;
		//~ }
		//~ if($("#city").val()=="" && $("#patient_id").val()=="0")
		//~ {
			//~ scrollPage(230);
			//~ $("#city").focus().css({"border-color":"red"});;
			//~ return false;
		//~ }
		
		if($("#pat_uhid").val().trim()=="" && $("#patient_id").val()=="0")
		{
			alert("3");
			$("#pat_uhid").val("").focus().css({"border-color":"red"});;
			return false;
		}
		
		if($("#refbydoctorid").val()=="0" || $("#refbydoctorid").val()=="")
		{
			alert("4");
			$("#r_doc").val("").focus().css({"border-color":"red"});;
			return false;
		}
		
		if($("#reg_type").val()=="" || $("#reg_type").val()=="0")
		{
			$("#reg_type").focus().css({"border-color":"red"});;
			return false;
		}
		if($("#reg_type").val()==2) // IPD
		{
			if($("#hospital_no").val()=="" || $("#hospital_no").val()=="0")
			{
				$("#hospital_no").focus().css({"border-color":"red"});;
				return false;
			}
			if($("#ward_id").val()=="0")
			{
				$("#ward_id").focus().css({"border-color":"red"});;
				return false;
			}
		}
		
		if($("#center_no").val()=="")
		{
			$("#center_no").val("").focus().css({"border-color":"red"});;
			return false;
		}
		
		if($("#receipt_no").val().trim()=="")
		{
			$("#receipt_no").val("").focus().css({"border-color":"red"});;
			return false;
		}
		
		//~ if($("#test_ids").val()=="")
		//~ {
			//~ $("#test").focus().css({"border-color":"red"});
			//~ return false;
		//~ }
		
		// Test selection
		var each_row=$(".each_row");
		var test_all="";
		for(var i=0;i<each_row.length;i++)
		{
			var tr_counter=each_row[i].value;
			
			var testid=$("#testid"+tr_counter).val();
			
			var test_rate=parseFloat($("#test_rate"+tr_counter).val());
			if(!test_rate){ test_rate=0; }
			
			var discount_each=parseFloat($("#discount_each"+tr_counter).val());
			if(!discount_each){ discount_each=0; }
			
			if(testid)
			{
				test_all=test_all+"##"+testid+"@"+test_rate+"@"+discount_each;
			}
		}
		if(test_all=="")
		{
			bootbox.dialog({ message: "<h4>None test selected</h4> ",size:"small"});
			setTimeout(function(){
				bootbox.hideAll();
				scrollPage(380);
				$("#test").focus();
			},2000);
			return false;
		}
		
		// Payment Part
		var total=parseInt($("#total").val());
		if(!total){ total=0; }
		
		$("#loader").show();
		$("#save_tr").hide();
		
		$.post("pages/new_lab_registration_data.php",
		{
			type:"pat_save",
			branch_id:$("#branch_id").val(),
			save_type:$("#save_type").val(),
			p_type_id:$("#p_type_id").val(),
			patient_id:$("#patient_id").val(),
			opd_id:$("#opd_id").val(),
			pat_reg_type:$("#pat_reg_type").val(),
			
			name_title:$("#name_title").val(),
			pat_name:$("#pat_name").val(),
			dob:$("#dob").val(),
			sex:$("#sex").val(),
			phone:$("#phone").val(),
			city:$("#city").val(),
			pat_uhid:$("#pat_uhid").val(),
			
			refbydoctorid:$("#refbydoctorid").val(),
			reg_type:$("#reg_type").val(),
			hospital_no:$("#hospital_no").val(),
			ward_id:$("#ward_id").val(),
			center_no:$("#center_no").val(),
			receipt_no:$("#receipt_no").val(),
			test_all:test_all,
		},
		function(data,status)
		{
			//alert(data);
			$("#loader").hide();
			$("#save_tr").show();
			
			var res=data.split("@");
			$("#patient_id").val(res[0]);
			$("#opd_id").val(res[1]);
			
			bootbox.dialog({ message: "<h5>"+res[2]+"</h5> "});
			setTimeout(function(){
				bootbox.hideAll();
			},2000);
			
			if(res[1]!="0")
			{
				load_saved_test_list();
				load_payment_info();
			}
		})
	}
	// Save Patient End
	
	// Sample Start
	function load_sample(uhid,opd,ipd,batch_no)
	{
		$.post("pages/phlebo_load_sample.php",
		{
			uhid:uhid,
			opd:opd,
			ipd:ipd,
			batch_no:batch_no,
			lavel:$("#lavel_id").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#results").html(data);
			$("#mod").click();
			$("#results").fadeIn(500,function(){ load_vaccu(); });
			$(".disease_id").select2({ theme: "classic" });
		})
	}
	function sample_accept(pid,opd,ipd,batch_no)
	{
		bootbox.dialog({ message: "<p id='phlb_msg'><b>Saving...</b></p>"});
		
		var glob_barcode=1;
		
		var vac="";
		var vac_l=$(".icon-check");
		for(var i=0;i<vac_l.length;i++)
		{
			if($(vac_l[i]).prop("id"))
			{
				vac+="@@"+$(vac_l[i]).prop("id");
			}
		}
		
		var vac_n="";
		var vac_l_n=$(".icon-check-empty");
		for(var j=0;j<vac_l_n.length;j++)
		{
			if($(vac_l_n[j]).prop("id"))
			{
				vac_n+="@@"+$(vac_l_n[j]).prop("id");
			}
		}
		
		var tst_vac="";
		var tst_vac_n=$(".tst_vac:checked");
		for(var j=0;j<tst_vac_n.length;j++)
		{
			if($(tst_vac_n[j]).val())
			{
				if(j==0)
				{
					tst_vac=$(tst_vac_n[j]).val();
				}
				else
				{
					tst_vac+=","+$(tst_vac_n[j]).val();
				}
			}
		}
		
		$.post("pages/phlebo_save_sample.php",
		{
			pid:pid,
			opd_id:opd,
			ipd_id:ipd,
			batch_no:batch_no,
			vac:vac,
			vac_n:vac_n,
			tst_vac:tst_vac,
			user:$("#user").text()
		},
		function(data,status)
		{
			if(glob_barcode==0)
			{
				$("#phlb_msg").html("<b>Saved</b>");
				setTimeout(function()
				{
					bootbox.hideAll();
				},1000);
			}
			else
			{
				$("#phlb_msg").html("<b>Saved. Redirecting to Barcode Generation</b>");	
				setTimeout(function()
				{
					bootbox.hideAll();
					var user=$("#user").text();
					var url="pages/barcode_generate.php?pid="+pid+"&opd_id="+opd+"&ipd_id="+ipd+"&batch_no="+batch_no+"&user="+user+"&vac="+vac+"&tst_vac="+tst_vac;
					window.open(url,'','fullscreen=yes,scrollbars=yes');
					
				},1000);
			}
		})
	}
	function check_vac(elem,val)
	{
		if($("#"+val+"").prop("class")=="icon-check")
		{
			$("#"+val+"").prop("class","icon-check-empty")
			$("#smp_td_"+elem+"").css({'background-color':'rgb(234, 164, 130)'});
			
			$(".test_vac_cls"+val).prop("checked", false);
		}
		else
		{
			$("#"+val+"").prop("class","icon-check")
			$("#smp_td_"+elem+"").css({'background-color':'rgb(146, 217, 146)'});
			
			$(".test_vac_cls"+val).prop("checked", true);
		}
	}
	
	function checked_test(elem,vac,testid)
	{
		var vaccu_test_num=$(".test_vac_cls"+vac+"").length;
		var vaccu_test_chk_num=$(".test_vac_cls"+vac+":checked").length;
		
		if(vaccu_test_chk_num>0)
		{
			$("#"+vac+"").prop("class","icon-check")
			$("#smp_td_"+elem+"").css({'background-color':'rgb(146, 217, 146)'});
		}
		else
		{
			$("#"+vac+"").prop("class","icon-check-empty")
			$("#smp_td_"+elem+"").css({'background-color':'rgb(234, 164, 130)'});
		}
	}
	function barcode_single(pid,opd,ipd,batch_no,vc)
	{
		
		var tst_vac="";
		var tst_vac_n=$(".tst_vac:checked");
		for(var j=0;j<tst_vac_n.length;j++)
		{
			if($(tst_vac_n[j]).val())
			{
				if(j==0)
				{
					tst_vac=$(tst_vac_n[j]).val();
				}
				else
				{
					tst_vac+=","+$(tst_vac_n[j]).val();
				}
			}
		}
		
		var user=$("#user").text();
		var url="pages/barcode_generate.php?pid="+pid+"&opd_id="+opd+"&ipd_id="+ipd+"&batch_no="+batch_no+"&user="+user+"&vac="+vc+"&tst_vac="+tst_vac+"&sing="+1;
		window.open(url,'','fullscreen=yes,scrollbars=yes');
	}
	function select_all()
	{
		if($("#sel_all").val()=="Select All")
		{
			$(".icon-check-empty").prop("class","icon-check");	
			$("#sel_all").val("De-Select All");
			$("#sel_all").html("<i class='icon-list-ul'></i> De-Select All");
			$(".icon-check").parent().css({'background-color':'rgb(146, 217, 146)'});
			
			$(".tst_vac").prop("checked", true);
		}
		else if($("#sel_all").val()=="De-Select All")
		{
			$(".icon-check:not('[name=vacc_done]')").prop("class","icon-check-empty");
			$("#sel_all").val("Select All");
			$("#sel_all").html("<i class='icon-list-ul'></i> Select All");
			$(".icon-check-empty").parent().css({'background-color':'rgb(234, 164, 130)'});
			
			$(".tst_vac").prop("checked", false);
		}
	}
	
	function vac_note(pid,opd,ipd,batch_no,vac,vname)
	{
		bootbox.dialog({
			title: 'Add Note For '+vname,
			message: "<p><input type='text' id='note_text_"+vac+"' style='width:90%'/></p>",
			size: 'large',
			buttons: {
				save: {
					label: "<i class='icon-save'></i> Save",
					className: 'btn-success',
					callback: function(){
						var note=$("#note_text_"+vac+"").val();
						$.post("pages/phlebo_sample_note.php",
						{
							pid:pid,
							opd_id:opd,
							ipd_id:ipd,
							batch_no:batch_no,
							vac:vac,
							note:note,
							user:$("#user").text()
						},
						function(data,status)
						{
							if(data==1)
							{
								$("#vac_saved_note_"+vac+"").val(note);
								$("#note_"+vac+"").prop("class","btn btn-success");
								$("#note_"+vac+"").val("view");
							}
							else
							{
								$("#note_"+vac+"").prop("class","btn btn-info");
								$("#note_"+vac+"").val("note");
								$("#vac_saved_note_"+vac+"").val("");
							}
						})
					}
				},
				close: {
					label: "<i class='icon-off'></i> Close",
					className: 'btn-danger',
					callback: function(){
						console.log('Custom button clicked');
						
					}
				}
			}
		});
		
		var nt=$("#vac_saved_note_"+vac+"").val();
		$("#note_text_"+vac+"").val(nt).focus();
		
		setTimeout(function(){ $("#note_text_"+vac+"").focus();},500);
		
	}
	// Sample End
	
	function new_registration()
	{
		var param_id=$("#param_id").val();
		
		window.location.href="?param="+btoa(param_id);
	}
	
	function scrollPage(val)
	{
		$({myScrollTop:window.pageYOffset}).animate({myScrollTop:val}, {
			duration: 1000,
			easing: 'swing',
			step: function(val){
				window.scrollTo(0, val);
			}
		});
	}
	
	function estimate_receipt()
	{
		url="pages/estimate_receipt.php?type=1&bid="+$("#branch_id").val();
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function print_receipt(url)
	{
		var uhid=$("#patient_id").val();
		url=url+"&uhid="+btoa(uhid);
		
		var opd_id=$("#opd_id").val();
		url=url+"&opdid="+btoa(opd_id);
		
		var user=$("#user").text().trim();
		url=url+"&user="+btoa(user);
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function load_test_print()
	{
		$.post("pages/pat_reg_prints.php",
		{
			uhid:$("#patient_id").val(),
			opd_id:$("#opd_id").val()
		},
		function(data,status)
		{
			$("#results2").html(data);
			//$(".modal-dialog").css({'width':'500px'});
			$("#mod2").click();
			//$("#mod_chk").val("1");
			$("#results").fadeIn(500);
		})
	}
	function print_indiv(uhid,visit)
	{
		var norm=$(".norm:checked");
		var norm_l=0;
		if(norm.length>0)
		{
			for(var i=0;i<norm.length;i++)
			{
				norm_l=norm_l+"@"+$(norm[i]).val();
			}
		}
		
		var path=$(".path:checked");
		var path_l=0;
		if(path.length>0)
		{
			for(var j=0;j<path.length;j++)
			{
				path_l=path_l+"@"+$(path[j]).val();
			}
		}
		
		
		var rad=$(".rad:checked");
		var rad_l=0;
		if(rad.length>0)
		{
		for(var k=0;k<rad.length;k++)
			{
				rad_l=rad_l+"@"+$(rad[k]).val();
			}
		}

		//var url="pages/print1_rpt_indv.php?uhid="+uhid+"&opd_id="+visit+"&norm="+norm_l+"&path="+path_l+"&rad="+rad_l;
		var url="pages/print1_rpt_indv.php?uhid="+uhid+"&opdid="+visit+"&norm="+norm_l+"&path="+path_l+"&rad="+rad_l;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1050');
	}
	
	function show_selected_test()
	{
		if($("#list_all_test").css('display')=="none")
		{
			$("#list_all_test").slideDown();
			//$("#test_view_btn").hide();
		}
		else
		{
			$("#list_all_test").slideUp(500)
			$("#test_d").html("");
			
			var item_chk=$("#test_list tr").length;
			if(item_chk>0)
			{
				$("#test_view_btn").show();
			}
		}
	}
	
	function print_req(dep)
	{
		var pid=$("#patient_id").val();
		var opd_id=$("#opd_id").val();
		
		//url="pages/phlebo_gen_req.php?patient_id="+pid+"&opd_id="+opd_id+"&dep="+dep;
		
		url="pages/test_requisition_form_print.php?patient_id="+pid+"&opd_id="+opd_id+"&ipd_id=&batch_no=1&dep_id="+dep;
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>

<style>
label {
	display: inline;
}
.table-condensed > tbody > tr > td, .table-condensed > tbody > tr > th, .table-condensed > tfoot > tr > td, .table-condensed > tfoot > tr > th, .table-condensed > thead > tr > td, .table-condensed > thead > tr > th
{
	padding: 2px;
}
.list_cls
{
	padding: 2px 6px;
	height: 15px !important;
	border:0 !important;
}

.test_rate
{
	pointer-events: none;
}
<?php
	if($centre_discount_num==0)
	{
?>
.discount_each
{
	display:none;
	pointer-events: none;
}
<?php
	}
	else
	{
?>
.discount_each
{
	pointer-events: none;
}
<?php
	}
?>
#myModal1
{
	left: 18%;
	width:95%;
	height: 600px;
}
.modal.fade.in {
    top: 1%;
}
.modal-body
{
	max-height: 550px;
}
</style>
