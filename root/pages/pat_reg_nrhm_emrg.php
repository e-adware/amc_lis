<?php
$man=0;

$exp=mysqli_fetch_array(mysqli_query($link,"select * from bill_enable_details where expire_type='0'"));
if($exp)
{
	$n_date=date("Y-m-d H:i");
	$c_date=$exp[expire_date]." ".$exp[expire_time];
	
	if($n_date>=$c_date)
	{
		$man=0;
		mysqli_query($link,"update bill_enable_details set expire_type='1'");
	}
	else
	{
		$man=1;
	}
}
?>

<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Phelbotomy Sample Receive(NRHM Emergency)</span></div>
     &nbsp; (<b style="color:#ff0000;">*</b> marks are compulsory)
</div>
<!--End-header-->
<div class="container-fluid">
	
<div style="position:fixed;right:0" id="bar_setting">
	<?php 
		$ip=$_SERVER['REMOTE_ADDR'];
		
		$chk_bar=mysqli_fetch_array(mysqli_query($link,"select * from barcode_setting where ip_address='$ip'"));
		if($chk_bar[ip_address])
		{
			?><button class='btn btn-info btn-success' onclick="load_bar_setting('<?php echo $ip;?>')"><i class='icon-wrench'></i></button><?php
		}
	?>
</div>	
	
	<br/>
	<div id="search_det" style="display:none">
		<button class="btn btn-info btn-mini" onclick="load_search()">Click to Entry</button>
		<br/>
		<table id="padd_tbl" class="table table-condensed">
			<tr>
				<td colspan="4">
					<center>
						<h4>Search</h4>
					</center>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<center>
						<b>From</b>
						<input class="form-control datepicker" type="text" name="fdate" id="fdate" value="<?php echo date("Y-m-d"); ?>" >
						<b>To</b>
						<input class="form-control datepicker" type="text" name="tdate" id="tdate" value="<?php echo date("Y-m-d"); ?>" >
					</center>
				</td>
			</tr>
			<tr>
				<th>Sample No <br/>
					<input type="text" style="width:100px;" value="NHRM_EMER/" readonly >
					<input type="text" style="width:120px;" id="bill_no_upd" onkeyup="load_emp_event(event)" placeholder="Sample NO" >
				</th>
				<th>HOSP.NO <br/>
					<input type="text" id="hosp_no_upd" onkeyup="load_emp_event(event)" placeholder="HOSP NO" >
				</td>
				
				<th>
					<input type='hidden' value="" id="patient_no_upd" onkeyup="load_emp_event(event)" placeholder="Enter PATIENT.NO"/>
				</th>
				<th>Name <br/>
					<input type="text" id="name_upd" onkeyup="load_emp_event(event)" placeholder="Type Name" >
				</td>
			</tr>
			<tr>
				<th colspan="4" style="text-align:center">
					<input type="button" id="search_pat" value="Search" class="btn btn-info" onclick="load_pat_upd()"/>
					<input type="button" id="search_clr" value="Clear" class="btn btn-info" onclick="$('#hosp_no_upd').val('');$('#bill_no_upd').val('');$('#name_upd').val('');"/>
				</th>
			</tr>
			<tr>
				<td colspan="4">
					<div id="pat_list_upd">
						
					</div>
				</td>
			</tr>
		</table>
	
	</div>
	
	
	<div id="reg_div" tabindex='1' onkeyup="move_focus(event)">
		<button class="btn btn-info btn-mini" onclick="load_search()">Click to Search</button>
		<br/><br/>
		
		<table class="table table-bordered table-condensed">
		<tr>
			<th colspan="6">
				<select id="o_i_pd" style="display:none">
					<option value="5">EMER</option>
				</select>
			</th>
		</tr>
		<tr>
			<th>
				Patient Type:
			</th>
			<th>
			<?php
			if($man==0)
			{
				?> <input type="hidden" id="bill_no" onkeyup="check_barcode(event)" name="c_3" ondrop="drag_data()"/> <?php
			}
			else
			{
				?> <input type="hidden" id="bill_no" onkeyup="check_barcode_mon(event)" name="c_3" ondrop="drag_data()"/> <?php
			}
			?>
			<select id="pat_type" name="c_0" onkeyup="select_enter(this.id,event)" onchange="load_test('','')" autofocus><option>IPD</option><option>OPD</option></select>
			</th>
			<!--<th><input type="text" id="bill_no" onkeydown="check_barcode(event)" name="c_3" ondrop="drag_data()"/></th>
			<th><input type="text" id="bill_no" onkeydown="check_barcode_mon(event)" name="c_3" ondrop="drag_data()"/></th>-->
			<th style="width:100px">Hosp No. <b style="color:#ff0000;">*</b>:</th>
			<td colspan="2"> <input type="text" id="hosp_no" name="c_4" placeholder="Enter Hosp. No" onkeyup="check_hosp_no(event)"  autofocus /> </td>
			<td><span id="date_serial"></span></td>
		</tr>
		<tr>
			<th>Name <b style="color:#ff0000;">*</b></th>
			<td ><input type="text" id="name"  name="c_5" onkeyup="change_up()" onblur="change_up()"/></td>
			<th>Address</th>
			<th><input type="text" id="add" name="c_6" class="no_imp"/></th>
			<th>Phone</th>
			<th><input type="text" id="phone" name="c_7" class="no_imp" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'');" maxlength="10" /></th>
		</tr>
		<tr>
			<th style="width:200px">Age <b style="color:#ff0000;">*</b> :<input type="text" id="age"  style="width:50px"  name="c_8"/> <select id="age_type" name="c_9" style="width:50px"><option value="Years">Y</option><option value="Months">M</option><option value="Days">D</option></select></th>
			<th>Sex <b style="color:#ff0000;">*</b> :<select id="sex" name="c_10" style="width:50px"><option value="Male">M</option><option value="Female">F</option></select></th>
			<th>
				Sample No. <b style="color:#ff0000;">*</b>
			</th>
			<td>
				<input type="text" class="imp span2" id="samp_no" name="c_11" />
			</td>
			<th>Ward <b style="color:#ff0000;">*</b></th>
			<th>
				<select id="ward"  name="c_12">
					<option value="0">Select Ward</option>
					<?php
						$ward=mysqli_query($link,"select * from ward_master order by ward_name");
						while($w=mysqli_fetch_array($ward))
						{
							echo "<option value='$w[id]'>$w[ward_name]</option>";
						}
					?>
				</select>
				
				<!---------OPD Dept-------------->
				<select id="dept" style="display:none">
					<option value="0">--Select Department--</option>
					<?php
						$dept=mysqli_query($link,"select * from department order by name");
						while($dp=mysqli_fetch_array($dept))
						{
							echo "<option value='$dp[id]'>$dp[name]</option>";
						}
					?>
				</select>
				<!-------------------------------->
			</th>
		</tr>
		<tr>
			<th>Disease</th>
			<th>
				<select id="pat_dis" onchange="save_disease(this.value)" name="c_13">
					<option value="0">None</option>
					<?php
					$pat_dis=mysqli_fetch_array(mysqli_query($link,"select * from patient_disease_details where patient_id='$uhid' and opd_id='$opd_id'"));
					$dis=mysqli_query($link,"select * from disease_master order by name");
					while($ds=mysqli_fetch_array($dis))
					{
						if($pat_dis[disease_id]==$ds[id]) { $sel="";}else{ $sel="";}
						echo "<option value='$ds[id]' $sel>$ds[name]</option>";
					}
					?>
				</select>
			</th>
			<th colspan="4">
				<label style="display:none;"><input type="checkbox" id="nr" onclick="check_pat_type()"> NR</label>
				<select id="pat_type_covid" name="c_14" onkeyup="select_enter(this.id,event)">
					<option value="0">--Select Patient Type(Covid)--</option>
					<option value="1">COVID-19</option>
					<option value="2">COVID-19 Suspected</option>
					<option value="3">COVID-19 Treated</option>
				</select>
				<select id="pat_type_nrhm" name="c_15" onkeyup="select_enter(this.id,event)" style="width:150px;">
					<option value="0">Select Patient Type</option>
					<option value="1">Pregnant Women</option>
					<option value="2">Infant</option>
					<option value="3">Cancer</option>
				</select>
				<select id="free" onchange="load_auth()" style="display:none;" onkeyup="select_enter(this.id,event)">
				<!--<select id="free" onkeydown="select_enter(event,'p_free')" autofocus>-->
					<option value="0">Generel</option>
					<?php
					$free=mysqli_query($link,"select * from pat_free_master order by id");
					while($fr=mysqli_fetch_array($free))
					{
						echo "<option value='$fr[id]'>$fr[free_name]</option>";
					}	
					?>
				</select>
				<select id="auth" style="display:none" name="" onkeyup="select_enter(this.id,event)">
					<option value="0">--Select Auth. Person--</option>
					<?php
					$auth=mysqli_query($link,"select * from pat_free_auth order by seq");
					while($at=mysqli_fetch_array($auth))
					{
						echo "<option value='$at[id]'>$at[auth_person]</option>";
					}
					?>	
				</select>
				
				<select id="auth_disc" name="c_13" style="display:none;width:100px;" onkeyup="select_enter(this.id,event)">
						<option value="0">-Select-</option>
						<option>25</option> <option>50</option> <option>75</option> <option>100</option>
				</select>
			</th>
		</tr>
		<tr>
			<th colspan="7">
			<div style="border-bottom:1px solid #DDDDDD">
			<div align="center" style="border-bottom:1px solid #CCC">
				<input type="hidden" class="span5" id="srch_test" onfocus="load_tests1()" onblur="setTimeout(function(){$('#ref_doc').empty().hide();},600);" placeholder="Search Test Name" />
				<input type="text" placeholder="Search Test" onkeyup="select_test(this.value,event)" id="tst_search" name="c_13" onfocus="slide_test()"/> 
			</div>		
			
			<div style="display:none;">
				
				<select id="">
					<option value="0">Select Department</option>
					<?php
						$dep=mysqli_query($link,"select * from test_department order by name");
						while($dp=mysqli_fetch_array($dep))
						{
							echo "<option value='$dp[id]'>$dp[name]</option>";
						}
					?>
					</select>
			</div>
			
			
			</div>
			<input type="hidden" id="patient_id"/>
			<input type="hidden" id="opd_id"/>
			<input type="hidden" id="batch"/>
			<input type="hidden" id="chk_val1"/>
			<input type="hidden" id="chk_val2"/>
			<div id="ref_doc"></div>
			<div id="test_list">
				
			</div>
			</th>
		</tr>
		<tr>
			<th colspan="7"><div style="font-size:15px;text-align:center">Patient No: <span id="patient_no"></span></div></th>
		</tr>
		<tr>
			<td colspan="7" style="text-align:center">
			<?php
			$sampl_det=mysqli_query($link,"select * from recp_sample order by id");
			while($smp_d=mysqli_fetch_array($sampl_det))
			{
				?>
				<div style="display:inline-block;width:200px;padding:5px;background:#CCC;font-weight:bold;cursor:pointer" onclick="check_sample(<?php echo $smp_d[id];?>)" onmouseover="view_sample(<?php echo $smp_d[id];?>)">
					<?php echo $smp_d[name];?> 
					<select id="<?php echo $smp_d[id];?>" class="recp_smp" style="width:120px">
						<option value="1">Received</option>
						<option value="0">Not Received</option>
					</select>
				</div>	
				<?php
			}
			?>
			</td>
		</tr>
		<tr>
			<td colspan="7" style="text-align:center">
				<button id="save" class="btn btn-success" onclick="save_data(this.value)" value="save">Save</button>
				<button id="print" class="btn btn-primary" onclick="print_barcode()">Print Barcode</button>
				<button id="print" class="btn btn-primary" onclick="load_new()">New</button>
				<button id="cancel_pat" class="btn btn-danger" onclick="load_canc_pat()" style="display:none">Delete This Patient</button>
			</td>
		</tr>
		</table>
	</div>
</div>	

<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"/>
<div class="modal fade" id="myModal" tabindex='-1' role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal_dial">
		<div class="modal-content">
			<div class="modal-body">
				<div id="results"> </div>
			</div>
		</div>
	</div>
</div>

<style>

#myModal
{
	width: 85%;
	left: 28%;
	top:3%;
	height:380px;
	display:none;
}

	
#myModal .modal-body
{
	max-height: 470px;
}

.ScrollStyle
{
    max-height: 550px;
    overflow-y: scroll;
}
.btn_round_msg
{
	color:#000;
	padding:2px;
	border-radius: 7em;
	padding-right:10px;
	padding-left:10px;
	box-shadow: inset 1px 1px 0 rgba(0,0,0,0.6);
	transition: all ease-in-out 0.2s;
}
.red
{
	background-color: #d59a9a;
}
.green
{
	background-color:#9dcf8a;
}
.yellow
{
	background-color:#f6e8a8;
}
input[type="checkbox"]:not(old) + label, input[type="radio"]:not(old) + label
{
	display: inline-block;
	margin-left:0;
	line-height: 1.5em;
}
.modal.fade.in {
    top: 1%;
}
.modal-body
{
	max-height: 550px;
}

.tst_span
{
	display:inline-block;
	width:300px;
	padding:5px;
	font-size:12px !important;
	cursor:pointer;
	margin-bottom:5px;
}

.tst_span:hover
{
	transform:scale(1.05);
	transition:all .2s;
}

#test_list
{
	height:400px;
	overflow:scroll;
	overflow-x:hidden;
}
#date_serial
{
	//display: none;
	font-weight:bold;
	font-size:20px;
	text-align:right;
}

#samp_det_table td {
  cursor: pointer;
}
label.sampLabel
{
	display: inline-block;
	margin: 0px;
	border: 1px dotted #888;
	padding: 2px;
	padding-left: 5px;
	padding-right: 5px;
}
label.sampLabel:hover
{
	border: 1px solid #444;
	box-shadow: 0px 0px 8px 0px #444;
	transition: 0.3s;
}
input[type="radio"]
{
	margin: 0px;
}
#ref_doc
{
	width: 600px;
	left: 30%;
}
</style>

<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../jss/moment.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />

<script>
	$(document).on("contextmenu",function(e){
				e.preventDefault();
				//~ if($("#user").text().trim()!='101' || $("#user").text().trim()!='101')
				//~ {
					//~ e.preventDefault();
				//~ }
			});


	$(document).ready(function(){
		load_serial(5);
		load_test('','');
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
		});
		
		//var myVar = setInterval(check_data, 1500);
		//load_sample('110','1423/0625','','1');
		
		var names=document.getElementById("srch_test");
		var timeout = null; // Init a timeout variable to be used below // Listen for keystroke events
		names.onkeyup = function(e) // Init a timeout variable to be used below
		{
			clearTimeout(timeout);
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(unicode==13 || unicode==27 || unicode==38 || unicode==40)
			{
				load_tests(names.value,e);
			}
			else
			{
				timeout = setTimeout(function()
				{
					load_tests(names.value,e);
				}, 500);
			}
		};
	});

function check_pat_type()
{
	if($("#nr").prop('checked'))
	{
		$("#pat_type_nrhm").fadeIn(200);
	}
	else
	{
		$("#pat_type_nrhm").fadeOut(200);
		$("#pat_type_nrhm").val("0");
	}
}
function hid_div(e)
{
	
}
//------------------------item search---------------------------------//
function load_tests1()
{
	setTimeout(function(){ $("#chk_val2").val(1)},200);
}
var doc_tr=1;
var doc_sc=0;
function load_tests(val,e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	//alert(unicode);
	if(unicode!=13)
	{
		if(unicode!=40 && unicode!=38)
		{
			if(unicode==27)
			{
				$("#save").focus();
			}
			else
			{
				$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
				$("#ref_doc").fadeIn(200);
				$.post("pages/load_test_list.php",
				{
					val:val,
					type:1
				},
				function(data,status)
				{
					$("#ref_doc").html(data);	
					doc_tr=1;
					doc_sc=0;
				});
			}
		}
		else if(unicode==40)
		{
			var chk=doc_tr+1;
			var cc=document.getElementById("doc"+chk).innerHTML;
			if(cc)
			{
				doc_tr=doc_tr+1;
				$("#doc"+doc_tr).css({'color': '#E72111','transform':'scale(0.95)','font-weight':'bold','font-size':'15px','transition':'all .2s'});
				var doc_tr1=doc_tr-1;
				$("#doc"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','font-size':'13px','transition':'all .2s'});
				var doc_tr2=doc_tr1-1;
				//$("#doc"+doc_tr2).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','font-size':'11px','transition':'all .2s'});
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
				$("#doc"+doc_tr).css({'color': '#E72111','transform':'scale(0.95)','font-weight':'bold','font-size':'15px','transition':'all .2s'});
				var doc_tr1=doc_tr+1;
				$("#doc"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','font-size':'13px','transition':'all .2s'});
				var doc_tr2=doc_tr1+1;
				//$("#doc"+doc_tr2).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','font-size':'11px','transition':'all .2s'});
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
		//$("#r_doc").css('border','');
		var cen_chk1=document.getElementById("chk_val2").value;
		if(cen_chk1!=0)
		{
			var docs=document.getElementById("dvdoc"+doc_tr).innerHTML.trim();
			var vals=JSON.parse(docs);
			doc_load(vals['id'],vals['name']);
			//$("#doc_info").fadeIn(200);
			doc_tr=1;
			doc_sc=0;
		}
	}
}
function doc_load(id,name)
{
	//~ $("#r_doc").val(name);
	//~ $("#doc_id").val(id);
	//~ $("#ref_doc").fadeOut(200).empty();
	add_test_temp(id,name);
	$("#ref_doc").empty().hide();
	$("#srch_test").val('');
}
//------------------------item search end---------------------------------//
function add_test_temp(id,name)
{
	var tr="<tr class='allTr' id='tr"+id+"'><td></td><td><input type='checkbox' class='tst_check' style='display:none;' value='"+id+"' checked />"+name+"</td><td><button type='button' class='btn btn-danger btn-mini' onclick='$(this).parent().parent().remove();set_sl();'><i class='icon-remove'></i></button></td></tr>";
	var len=$("#mytable").length;
	
	if($("#tr"+id).length>0)
	{
		
	}
	else
	{
	if(len>0)
	{
		$("#mytable").append(tr);
	}
	else
	{
		var table="<table class='table table-condensed' id='mytable'><tr><th style='width:5%;'>#</th><th>Test Name</th><th style='width:5%;'></th></tr></table>";
		$("#test_list").append(table);
		$("#mytable").append(tr);
	}
	}
	set_sl();
}
function set_sl()
{
	var len=$(".allTr").length;
	for(var i=0; i<len; i++)
	{
		$(".allTr:eq("+i+")").find('td:eq(0)').text(i+1);
	}
}


function check_data()
{
	if($("#bill_no").val()!='')
	{
		$.post("pages/pat_reg_ajax.php",
		{
			bill:$("#bill_no").val(),
			type:9
		},
		function(data,status)
		{
			if(data=='')
			{
				$("#hosp_no").focus();
				$("#bill_no").prop("readonly",true);
			}
			else
			{
				var info=data.split("@#@");
				bootbox.alert("<h5>Bill No "+$("#bill_no").val()+" is already Exist. <br/> Name: "+info[0]+"<br/> Hosp NO:"+info[1]+" </h5>");
				$("#bill_no").val("").focus();
			}
		})
	}
}
function check_bill()
{
	if($("#bill_no").val().length>5)
	{
		check_data();
	}
	else
	{
		$("#bill_no").val("");
	}
}
function check_barcode(e)
{
	if(e.ctrlKey==1)
	{
		bootbox.dialog({ message: "<h5>CTRL is Disabled</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
					$("#bill_no").val("").focus();
				},1000);
	}
	else
	{
		setTimeout(function(){
		
			check_bill();
		
		 },400);
	}
}
function check_barcode_mon(e)
{
	if(e.which==13)
	{
		$("#hosp_no").focus();
	}
}
function drag_data()
{
	bootbox.dialog({ message: "<h5>Drag&Drop is Disabled</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
					$("#bill_no").val("").focus();
				},1000);
}
function load_serial(val)
{
	$.post("pages/pat_reg_ajax.php",
	{
		val:val,
		type:8
	},
	function(data,status)
	{
		$("#date_serial").html(data);
	})
}
function move_focus(e)
{
	if(e.which==13)
	{
		var act=document.activeElement.id;
		if(!act)
		{
			document.getElementById("hosp_no").focus();	
		}
		else
		{
			  if($("#"+act+"").val().trim()!='' || $("#"+act+"").attr("class")=="no_imp")
			  {
				  var nam=$("#"+act).attr("name");
				  var val=nam.replace( /^\D+/g, '');
				  val=parseInt(val)+1;
				  document.getElementsByName("c_"+val)[0].focus();
					
			  }
			  else
			  {
					//$("#"+act+"").css({'border':'1px solid red'});  
			  }
		}
	}
}
	
function load_test(val,e)
{
	if(e.which==13)
	{
		
	}
	else if(e.which==38)
	{
		
	}
	else if(e.which==40)
	{
		
	}
	else
	{
		$.post("pages/pat_reg_ajax.php",
		{
			pat_type:$("#pat_type").val(),
			val:val,
			type:1
		},
		function(data,status)
		{
			$("#test_list").html("<br/>"+data);
		})
	}
}

function select_check(val)
{
	if($("#check_"+val+"").is(':checked'))
	{
		if($("#save").val()=="save")
		{
			$("#check_"+val+"").prop("checked",false);
			$("#div_"+val+"").css({'background-color':'#EEEEEE'});
		}
		else
		{
			$.post("pages/pat_reg_ajax.php",
			{
				opd_id:$("#opd_id").val(),
				tst:$("#check_"+val+"").val(),
				type:10
			},
			function(data,status)
			{
				if(data>0)
				{
					bootbox.dialog({ message: "<h5>Test is already processed. Can not be removed</h5>"});
					setTimeout(function(){
								bootbox.hideAll();
							},2000);
				}
				else
				{
					$("#check_"+val+"").prop("checked",false);
					$("#div_"+val+"").css({'background-color':'#EEEEEE'});
				}
			})
		}
	}
	else
	{
		$("#check_"+val+"").prop("checked",true);
		$("#div_"+val+"").css({'background-color':'#DDDDDD'});
	}	
}

function check_hosp_no(e)
{
	if(e.which==13)
	{
		$.post("pages/pat_reg_ajax.php",
		{
			hosp_no:$("#hosp_no").val().trim(),
			type:3
		},
		function(data,status)
		{
			if(data!='')
			{
				var info=data.split("@kk@");
				$("#name").val(info[0]);
				$("#sex").val(info[1]);
				$("#age").val(info[2]);
				$("#age_type").val(info[3]);
				$("#slno").focus();
			}
			
		})
	}
}

function save_data(val)
{
	//$("#save").attr("disabled",true);
	//chk_smp(2); //---Check Sample---//
	
	var hosp_no=$("#hosp_no").val().trim();
	var name=$("#name").val().trim();
	var sex=$("#sex").val().trim();
	var age=$("#age").val().trim();
	var age_type=$("#age_type").val().trim();
	var slno=$("#samp_no").val().trim();
	
	var bill_no=$("#bill_no").val();
	
	var free=$("#free").val();	
	
	//var auth=$("#auth").val();	
	//var auth_disc=$("#auth_disc").val();	
	
	var tst="";
	var tst_l=$(".tst_check:checked")
	for(var i=0;i<tst_l.length;i++)
	{
		tst+="@koushik@"+$(tst_l[i]).val();
	}
	
	var recp_samp="";
	var tot_samp=$(".recp_smp");
	for(var i=0;i<=tot_samp.length;i++)
	{
		if($(tot_samp[i]).val()==1)
		{
			recp_samp+="@@"+$(tot_samp[i]).attr("id");
		}
	}
	
	if(hosp_no=="")
	{
		$("#hosp_no").focus();
		console.log("hosp_no");
		return false;
	}
	if(name=="")
	{
		$("#name").focus();
		console.log("name");
		return false;
	}
	if(age=="")
	{
		$("#age").focus();
		console.log("age");
		return false;
	}
	if(sex=="")
	{
		$("#sex").focus();
		console.log("sex");
		return false;
	}
	if(slno=="")
	{
		$("#slno").focus();
		console.log("slno");
		return false;
	}
	if($("#ward").val()=="0")
	{
		$("#ward").focus();
		console.log("ward");
		return false;
	}
	
	if(tst=="")
	{
		bootbox.dialog({ message: "<h5>Please Select Test</h5>"});
		setTimeout(function(){
			bootbox.hideAll();
		},2000);
		console.log("test");
		return false;
	}
	
	$("#save").attr("disabled",true);
	
	if(hosp_no=="" || name=="" || age=="" || tst_l.length==0) 
	{
		bootbox.dialog({ message: "<h5>Please fill up all the info</h5>"});
		setTimeout(function(){
					bootbox.hideAll();
					$("html, body").animate({ scrollTop: 30 },"slow",function(){ });
				},2000);
				
				$("#save").prop("disabled",false);
	}
	else if($("#o_i_pd").val()==0)
	{
		bootbox.dialog({ message: "<h5>Please Select Patient Type</h5>"});
		setTimeout(function(){
					bootbox.hideAll();
					$("html, body").animate({ scrollTop: 30 },"slow",function(){ $("#o_i_pd").focus(); });
					
				},1000);
				
				$("#save").prop("disabled",false);
	}
	/*
	else if(bill_no=="")
	{
		bootbox.dialog({ message: "<h5>Bill No Can Not Be Blank</h5>"});
		setTimeout(function(){
					bootbox.hideAll();
					$("html, body").animate({ scrollTop: 30 },"slow",function(){ $("#bill_no").focus(); });
				},1000);
				
				$("#save").prop("disabled",false);
				
	}
	*/
	/*else if($("#slno").hasClass("error") && val=="save")
	{
		bootbox.dialog({ message: "<h5>Serial No already exist</h5>"});
		setTimeout(function(){
					bootbox.hideAll();
					$("#slno").focus();
				},1000);
	}*/
	else
	{
		if(val=="save")
		{
			bootbox.dialog({ message: "<h5>SAVING...</h5>"});
		}
		else
		{
			bootbox.dialog({ message: "<h5>UPDATING...</h5>"});
		}
		$.post("pages/pat_reg_ajax.php",
		{
			p_type:$("#o_i_pd").val(),
			hosp_no:hosp_no,
			bill_no:bill_no,
			name:name,
			age:age,
			age_type:age_type,
			sex:$("#sex").val(),
			date_serial:$("#date_serial").text(),
			ward:$("#ward").val(),
			dept:$("#dept").val(),
			add:$("#add").val(),
			phone:$("#phone").val(),
			dis:$("#pat_dis").val(),
			recp_samp:recp_samp,
			
			tst:tst,
			
			pat_type:$("#pat_type").val(),
			pat_type_covid:$("#pat_type_covid").val(),
			pat_type_nrhm:$("#pat_type_nrhm").val(),
			samp_no:$("#samp_no").val(),
			free:$("#free").val(),
			auth:$("#auth").val(),
			auth_disc:$("#auth_disc").val(),
			user:$("#user").text().trim(),
			val:val,
			opd_id:$("#opd_id").val(),
			type:2
		},
		function(data,status)
		{
			//alert(data);
			var vl=JSON.parse(data);
			if(vl['response']>0)
			{
				if(val=="save")
				{
					$("#patient_id").val(vl['pid']);
					$("#opd_id").val(vl['opd']);
					$("#batch").val(vl['bch']);
					$("#patient_no").text(vl['opd']);
					$("#save").text("Update");
					bootbox.hideAll();
					bootbox.dialog({ message: "<h5>Saved. Redirecting to barcode generation</h5>"});
					setTimeout(function(){
						bootbox.hideAll();
						load_sample(vl['pid'],vl['opd'],'',vl['bch'])
					},1500);
				}
				else
				{
					bootbox.hideAll();
					bootbox.dialog({ message: "<h5>UPDATED</h5>"});
					setTimeout(function(){	bootbox.hideAll();	},1500);
				}
			}
			else
			{
				bootbox.hideAll();
				bootbox.dialog({ message: "<h5>Error</h5>"});
				setTimeout(function(){	bootbox.hideAll();	},2000);
			}
			//$("#save").prop("disabled",false);
		});
	}
}
function cashMemoBarcode(pid,opd,ipd)
{
	var user=$("#user").text().trim();
	var url="pages/patient_barcode_generate.php?pId="+pid+"&oPd="+opd+"&iPd="+ipd+"&uSr="+user;
	window.open(url,'','fullscreen=yes,scrollbars=yes');
}
function print_barcode()
{
	//~ if($("#save").val()=="save")
	//~ {
		//~ var opd=$("#opd_id").val();
		//~ window.open("pages/barcode_generate.php?opd_id="+opd,'','fullscreen=yes,scrollbars=yes');
		//~ $("#save").val("Update");
		//~ $("#save").text("Update");
	//~ }
	//~ else
	//~ {
		//~ $.post("pages/pat_reg_ajax.php",
		//~ {
			//~ opd_id:$("#opd_id").val(),
			//~ type:7
		//~ },
		//~ function(data,status)
		//~ {
			
			//~ $("#results").html(data);
			//~ $("#mod").click();
		//~ })
	//~ }
	var opd=$("#opd_id").val().trim();
	if(opd=="")
	{
		alert("Patient No not found");
	}
	else
	{
		load_sample($("#patient_id").val().trim(), $("#opd_id").val().trim(), '', $("#batch").val().trim());
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
	})
}
function load_vaccu()
{
	var tst="";
	var samp=$(".samp:checked")
	for(var i=0;i<samp.length;i++)
	{
		var test=$("."+$(samp[i]).attr("id"));
		for(var j=0;j<test.length;j++)
		{
			tst=tst+"@"+$(test[j]).val();
		}
	}

	$(".vac").attr("checked",false);
	$.post("pages/phlebo_load_vaccu.php",
	{
		tst:tst
	},
	function(data,status)
	{
		var vc=data.split("@");
		for(var k=0;k<vc.length;k++)
		{
			if(vc[k])
			{
				$("#vac_"+vc[k]+"").click();
			}
		}
	});
}
function check_vac(elem,val)
{
	if($("#"+val+"").prop("class")=="icon-check")
	{
		$("#"+val+"").prop("class","icon-check-empty")
		$("#smp_td_"+elem+"").css({'background-color':'rgb(234, 164, 130)'});
		
		$(".test_vac_cls"+val).prop("checked", false);
		
		$("#sampStat"+val).attr("disabled",true);
	}
	else
	{
		$("#"+val+"").prop("class","icon-check")
		$("#smp_td_"+elem+"").css({'background-color':'rgb(146, 217, 146)'});
		
		$(".test_vac_cls"+val).prop("checked", true);
		
		$("#sampStat"+val).attr("disabled",false);
	}
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
		
		$(".sampStat").attr("disabled",false);
	}
	//~ else if($("#sel_all").val()=="De-Select All")
	//~ {
		//~ $(".icon-check:not('[name=vacc_done]')").prop("class","icon-check-empty");
		//~ $("#sel_all").val("Select All");
		//~ $("#sel_all").html("<i class='icon-list-ul'></i> Select All");
		//~ $(".icon-check-empty").parent().css({'background-color':'rgb(234, 164, 130)'});
		
		//~ $(".tst_vac").prop("checked", false);
	//~ }
}
function checked_test(elem,vac,testid)
{
	var vaccu_test_num=$(".test_vac_cls"+vac+"").length;
	var vaccu_test_chk_num=$(".test_vac_cls"+vac+":checked").length;
	
	if(vaccu_test_chk_num>0)
	{
		$("#"+vac+"").prop("class","icon-check")
		$("#smp_td_"+elem+"").css({'background-color':'rgb(146, 217, 146)'});
		
		$("#sampStat"+vac).attr("disabled",false);
	}
	else
	{
		$("#"+vac+"").prop("class","icon-check-empty")
		$("#smp_td_"+elem+"").css({'background-color':'rgb(234, 164, 130)'});
		
		$("#sampStat"+vac).attr("disabled",true);
	}
}
function sample_accept(pid,opd,ipd,batch_no)
{
	bootbox.dialog({ message: "<p id='phlb_msg'><b>Saving...</b></p>"});
	
	var glob_barcode=$("#glob_barcode").val();
	
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
	
	var sampStat=[];
	for(var j=1; j<=vac_l.length; j++)
	{
		sampStat.push({
			"vacc"	:$(vac_l[j]).prop("id"),
			"stat"	:$("#sampStat"+($(vac_l[j]).prop("id"))).val()
		});
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
		sampStat:sampStat,
		sampProcess:$("input[name='sampProcess']:checked").val(),
		dailySlno:$("#dailySlno").val().trim(),
		user:$("#user").text().trim()
	},
	function(data,status)
	{
		//alert(data);
		if(glob_barcode==0)
		{
			$("#phlb_msg").html("<b>Saved</b>");
			setTimeout(function()
			{
				//view_all();
				bootbox.hideAll();
			},1000);
		}
		else
		{
			$("#phlb_msg").html("<b>Saved. Redirecting to Barcode Generation</b>");	
			setTimeout(function()
			{
				//view_all();
				bootbox.hideAll();
				var user=$("#user").text().trim();
				var url="pages/barcode_generate.php?pid="+pid+"&opd_id="+opd+"&ipd_id="+ipd+"&batch_no="+batch_no+"&user="+user+"&vac="+vac+"&tst_vac="+tst_vac;
				
				window.open(url,'','fullscreen=yes,scrollbars=yes');
				
			},1000);
		}
	})	
}



function change_up()
{
	var name=$("#name").val();
	var n_name=name.toUpperCase().replace(/\./g, '').replace(/\,/g, '')
	$("#name").val(n_name);
}

function load_search()
{
	$("#search_det").slideToggle(200,function(){ if($("#search_det").is(":visible")){ load_pat_upd(); }});
	$("#reg_div").slideToggle(200);
}
function load_pat_upd()
{
	$.post("pages/pat_reg_ajax.php",
	{
		hosp_no:$("#hosp_no_upd").val(),
		bill_no:$("#bill_no_upd").val(),
		patient_no:$("#patient_no_upd").val(),
		name:$("#name_upd").val(),
		fdate:$("#fdate").val(),
		tdate:$("#tdate").val(),
		pat_type:5,
		type:4
	},
	function(data,status)
	{
		$("#pat_list_upd").html(data);
	})
}

function load_emp_event(e)
{
	if(e.which==13)
	{
		load_pat_upd();
	}
}


function load_pat_details(opdid)
{
	$.post("pages/pat_reg_ajax.php",
	{
		opdid:opdid,
		type:5
	},
	function(data,status)
	{
		//alert(data);
		var vl=JSON.parse(data);
		//var det=data.split("#test_det#");
		//var info=det[0].split("@k_details@");
		//var tst=det[1].split("@tst@");
		
		$("#hosp_no").val(vl['hosp_no']);
		$("#name").val(vl['name']);
		$("#age").val(vl['age']);
		$("#age_type").val(vl['age_type']);
		$("#sex").val(vl['sex']);
		$("#pat_type").val(vl['pat_type']);
		
		//$("#o_i_pd").val(info[6]);
		
		$("#ward").val(vl['ward']);
		
		$("#phone").val(vl['phone']);
		$("#add").val(vl['address']);
		$("#pat_dis").val(vl['disease_id']);
		
		$("#bill_no").val(vl['bill_no']).prop("disabled",true);
		$("#free").val(vl['free_type']);
		$("#samp_no").val(vl['sample_serial']);
		
		$("#pat_type_covid").val(vl['nr_covid']);
		$("#pat_type_nrhm").val(vl['nr_pat_type']);
		
		//$(".recp_smp").val("0");
		//var samp=info[16].split("@@");
		//for(var i=0;i<samp.length;i++)
		{
			//$("#"+samp[i]+"").val("1");
		}
		
		//if(info[8]>0)
		{
			//$("#ath").val(info[8]).fadeIn(200);
		}
		
		//var sno=info[10]+" / "+info[9];
		$("#date_serial").text(vl['ndate']+" / "+vl['date_serial']);
		
		/*
		$(".tst_check:checkbox:checked").click();
		for(var i=0;i<=tst.length;i++)
		{
			if(tst[i])
			{
				if(!$(":checkbox[value='"+tst[i]+"']").prop("checked"))
				{
					//$(":checkbox[value='"+tst[i]+"']").click();
					//$(":checkbox[value='"+tst[i]+"']").click();
					$(":checkbox[value='"+tst[i]+"']").prop("checked",true);
					$(":checkbox[value='"+tst[i]+"']").parent().css({'background-color':'#DDDDDD'});
				}
			}
		}
		*/
		$("#save").val("Update").text("Update");
		$("#opd_id").val(opdid);
		$("#patient_no").text(opdid);
		$("#patient_id").val(vl['patient_id']);
		$("#batch").val('1');
		load_search();
		//load_selected_tests();
		load_selected_test_list();
		if($("#lavel_id").val().trim()==1 || $("#lavel_id").val().trim()==13)
		{
			$("#cancel_pat").show();
		}
	})
	
}
function load_selected_test_list()
{
	$.post("pages/load_test_list.php",
	{
		pid:$("#patient_id").val().trim(),
		opd:$("#opd_id").val().trim(),
		type:3
	},
	function(data,status)
	{
		//alert(data);
		var vl=JSON.parse(data);
		//$("#results").html(data);
		$(".tst_span").css("background-color", "#EEEEEE");
		$(".tst_check").prop("checked", false);
		for(var i=0; i<(vl.length); i++)
		{
			$(".dv"+vl[i]).css("background-color", "#DDDDDD");
			$(".tst"+vl[i]).prop("checked", true);
		}
	});
}
function load_selected_tests()
{
	$.post("pages/load_test_list.php",
	{
		pid:$("#patient_id").val().trim(),
		opd:$("#opd_id").val().trim(),
		type:2
	},
	function(data,status)
	{
		//alert(data);
		$("#test_list").html(data);
	});
}
function load_new()
{
	document.location="index.php?param="+btoa(1045);
}

function load_auth()
{
	if($("#free").val()=="3")
	{
		$("#auth").val("0");
		$("#auth_disc").val("0");
		$("#auth").fadeIn(200);
		$("#auth_disc").fadeIn(200);
	}
	else
	{
		$("#auth").val("0");
		$("#auth_disc").val("0");
		$("#auth").fadeOut(200);
		$("#auth_disc").fadeOut(200);
	}
}
function chk_smp(typ)
{
	$.post("pages/pat_reg_ajax.php",
	{
		entry_type:typ,
		val:$("#slno").val().trim(),
		type:6
	},
	function(data,status)
	{
		if($("#save").val()=="save")
		{
			if(data>0)
			{
				$("#slno").attr("class","span1 error");
				$("#slno").css({'color':"red"});
			}
			else
			{
				$("#slno").attr("class","span1");
				$("#slno").css({'color':"black"});
			}
		}
	})	
}
function print_barc_sel()
{
	var bar="";
	var barc=$(".barc_id:checked");
	for(var i=0;i<barc.length;i++)
	{
		bar=bar+"@"+$(barc[i]).val();
	}
	var opd=$("#opd_id").val();	
	
	window.open("pages/barcode_generate_single.php?opd_id="+opd+"&bar="+bar+"&stick=0",'','fullscreen=yes,scrollbars=yes');
}

function print_barc_sticker()
{
	var opd=$("#opd_id").val();	
	
	window.open("pages/barcode_generate_single.php?opd_id="+opd+"&stick=1",'','fullscreen=yes,scrollbars=yes');	
}

function sel_all()
{
	if($("#sel_all").val()=="Select All")
	{
		$(".barc_id:checkbox").each(function(i)
		{
			if($(this).prop("checked")==false)
			{
				$(this).prop("checked",true);
				$(this).click();
				$(this).prop("checked",true);
				
			}
			
		});
		$("#sel_all").val("De-select All");
		$("#sel_all").text("De-select All");
	}
	else if($("#sel_all").val()=="De-select All")
	{
		$(".barc_id:checkbox").each(function(i)
		{
			if($(this).prop("checked")==true)
			{
				$(this).prop("checked",false);
				$(this).click();
				$(this).prop("checked",false);
				
			}
		});
		$("#sel_all").val("Select All");
		$("#sel_all").text("Select All");
	}
}
function check_sample(val)
{
	if($("#"+val+"").prop("checked"))
	{
		$("#"+val+"").prop("checked",false);
		//$("#"+val+"").css({'color':'black'});
	}
	else
	{
		$("#"+val+"").prop("checked",true);
		
		//$("#"+val+"").css({'color':'green'});
	}
}

var sel_tst=0;
function select_test(val,e)
{
	if(e.which==13)
	{
		$(".tst_span:visible:eq("+sel_tst+")").click();
				
		var elm=$(".tst_span:visible:eq("+sel_tst+")").attr("id");
		var new_div=$("#"+elm+"");
		var attributes = $("#"+elm+"").prop("attributes");
		$("#"+elm+"").remove();
			
		$.each(attributes, function() {
			$("#"+elm+"").attr(this.name, this.value);
		});
			
		$("#tst_list").prepend(new_div);
		
		$(".tst_span:visible").css({'transform':'scale(1.00)','text-decoration':'none'});
		$("#tst_list .tst_span").show();
		$("#tst_search").val("").focus();
		
	}
	else if(e.which==38)
	{
		var chk_span=sel_tst-1;
		if($(".tst_span:visible:eq("+chk_span+")").length>0)
		{
			sel_tst=sel_tst-1;
			$(".tst_span:visible").css({'transform':'scale(1.00)','text-decoration':'none'});
			$(".tst_span:visible:eq("+sel_tst+")").css({'transform':'scale(1.05)','transition':'all .2s','text-decoration':'underline'});
		}
		chk_span=0;
	}
	else if(e.which==40)
	{
		var chk_span=sel_tst+1;
		if($(".tst_span:visible:eq("+chk_span+")").length>0)
		{
			sel_tst=sel_tst+1;
			$(".tst_span:visible").css({'transform':'scale(1.00)','text-decoration':'none'});
			$(".tst_span:visible:eq("+sel_tst+")").css({'transform':'scale(1.05)','transition':'all .2s','text-decoration':'underline'});
		}
		chk_span=0;
	}
	else if(e.which==27)
	{
		$("#save").focus();
	}
	else
	{
		if (val != "")
		{
			val=val.toLowerCase();
			$("#tst_list .tst_span").hide();
			$("#tst_list [class*="+val+"]").show();
			
			//$(".tst_span:eq(0)").focus();
			
		}else
		{
			$("#tst_list .tst_span").show();
			//$(".tst_span:eq(0)").focus();
		}
		sel_tst=0;
	}
}
function slide_test()
{
	$("html, body").animate({ scrollTop: 300 },"slow");
}

function select_enter(id,e)
{
	if(e.which==13)
	{
		if(id=="pat_type")
		{
			$("#hosp_no").focus();
		}
		if(id=="pat_dis")
		{
			$("#samp_no").focus();
		}
		if(id=="pat_type_covid")
		{
			$("#pat_type_nrhm").focus();
		}
		if(id=="pat_type_nrhm")
		{
			$("#tst_search").focus(200);
		}
		if(id=="free")
		{
			if($("#auth:visible").length>0)
			{
				$("#auth").focus();
			}
			else
			{
				$("#tst_search").focus(200);
			}
		}
		if(id=="auth" && $("#"+id).val()!=0)
		{
			if($("#auth_disc:visible").length>0)
			{
				$("#auth_disc").focus();
			}
			else
			{
				$("#tst_search").focus(200);
			}
		}
		if(id=="auth_disc")
		{
			if($("#auth_disc").val()!=0)
			{
				$("#tst_search").focus(200);
			}
		}
	}
	load_test("","");
}

function view_sample(val)
{
	
}

function load_bar_setting(ip)
{
	$.post("pages/pat_reg_ajax.php",
	{
		ip:ip,
		type:11
	},
	function(data,status)
	{
		$("#bar_setting").html(data);	
	})	
}

function bar_change(ip,typ)
{
	$.post("pages/pat_reg_ajax.php",
	{
		ip:ip,
		typ:typ,
		bar_value:$("#bar_value").val(),
		type:12,
	},
	function(data,status)
	{
		load_bar_setting(ip);
	})		
}

function change_bar(e,ip)
{
	if(e.which==13)
	{
		$.post("pages/pat_reg_ajax.php",
		{
			ip:ip,
			bar_value:$("#bar_value").val(),
			type:13,
		},
		function(data,status)
		{
			load_bar_setting(ip);
		})
	}
}
function load_bar_set(ip)
{
	var ipp="'"+ip+"'";
	$("#bar_setting").html('<button class="btn btn-info btn-success" onclick="load_bar_setting('+ipp+')"><i class="icon-wrench"></i></button>');	
}

function load_canc_pat()
{
	if(confirm("Do you really want to cancel this patient?")) {
		var deleteReason = prompt("Please enter the reason for deleting this patient:");
		if(deleteReason === null || deleteReason.trim() === "") {
			alert("Deletion cancelled: reason is required.");
			return; // stop if no reason provided
		}

		$.post("pages/pat_reg_ajax.php",
		{
			opd: $("#opd_id").val(),
			user: $("#user").text(),
			type: 14,
			reason: deleteReason  // send reason here
		},
		function(data, status) {
			if(data == 0) {
				bootbox.dialog({ message: "<h5>Patient Deleted</h5>" });
				setTimeout(function() {
					bootbox.hideAll();
					load_new();
				}, 1000);
			} else if(data == 2) {
				bootbox.dialog({ message: "<h5>Not Authorised to Delete</h5>" });
				setTimeout(function() {
					bootbox.hideAll();
				}, 1000);
			} else {
				bootbox.dialog({ message: "<h5>Cannot be Deleted. Few results are already received or done.</h5>" });
				setTimeout(function() {
					bootbox.hideAll();
				}, 2000);
			}
		});
	}
}
</script>
