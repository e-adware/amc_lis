<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Ref Doctor Contribution</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center">
		<tr>
			<td>
				<center>
					<span class="side_name">From</span>
					<input class="form-control datepicker span2" type="text" name="date1" id="date1" value="<?php echo date("Y-m-d"); ?>" style="margin-left: 47px;" readonly>
					<span class="side_name">To</span>
					<input class="form-control datepicker span2" type="text" name="date2" id="date2" value="<?php echo date("Y-m-d"); ?>" style="margin-left: 25px;" readonly>
					<!--
					<select multiple id="refbydoctorid" class="span5">
					<?php
						$con_doc_qry=mysqli_query($GLOBALS["___mysqli_ston"], " SELECT `refbydoctorid`,`ref_name` FROM `refbydoctor_master` ORDER BY `ref_name` ");
						while($con_doc=mysqli_fetch_array($con_doc_qry))
						{
							echo "<option value='$con_doc[refbydoctorid]'>$con_doc[ref_name]</option>";
						}
					?>
					</select>
					<select id="encounter" class="span2" style="display:none;">
						<option value="0">All Visit Type</option>
					<?php
						$qq_qry=mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `status`='0' ORDER BY `p_type_id` ");
						while($qq=mysqli_fetch_array($qq_qry))
						{
							echo "<option value='$qq[p_type_id]'>$qq[p_type]</option>";
						}
					?>
					</select>
					<br>
					<button class="btn btn-success" onClick="view_all('bill_wise')">Bill Wise</button>
					
					<button class="btn btn-success" onClick="view_all('bill_wise_detail')">Bill Wise Detail</button>-->
					
				</center>
			</td>
		</tr>
	</table>
	<input type="text" class="span5" id="doc_name" onkeyup="load_doctors()" placeholder="Search Doctor Here" autofocus>
	<div class="row">
		<div class="span5">
			<div id="load_doctors" class="ScrollStyledoc">
				
			</div>
			<input type="hidden" id="sel_refbydoctorid" value="0">
		</div>
		<div class="span5">
				<div id="load_sel_doctors" class="ScrollStyledoc">
			</div>
		</div>
	</div>
	<center>
		<button class="btn btn-success all" id="dept_wise" onClick="view_all('dept_wise')"><i class="icon-search"></i> Department Wise</button>
		<button class="btn btn-success all" id="summary_wise" onClick="view_all('summary_wise')"><i class="icon-search"></i> Doctor's Summary</button>
		<button class="btn btn-success all" id="bill_wise" onClick="view_all('bill_wise')"><i class="icon-search"></i> Bill Wise</button>
		<button class="btn btn-success all" id="bill_wise_detail" onClick="view_all('bill_wise_detail')"><i class="icon-search"></i> Bill Wise Detail</button>
	</center>
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

<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>

<script>
	$(document).ready(function(){
		$("#loader").hide();
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
			//defaultDate:'2000-01-01',
		});
		//$("#refbydoctorid").select2({ theme: "classic" });
		$("#refbydoctorid").select2({
			dropdownAutoWidth: true,
			multiple: true,
			width: '100%',
			height: '30px',
			placeholder: "All Doctors",
			allowClear: true
		});
		$('.select2-search__field').css('width', '100%');
		
		load_doctors();
	});
	
	function view_all(typ)
	{
		$(".all").not("#"+typ).addClass('btn-success');
		$("#"+typ).removeClass('btn-success');
		$("#"+typ).addClass('btn-primary');
		
		if(!$("#sel_refbydoctorid").val())
		{
			var ref_docs="0";
		}
		else
		{
			//var ref_docs=$("#refbydoctorid").val().toString();
			var ref_docs=$("#sel_refbydoctorid").val();
		}
		
		$("#loader").show();
		$.post("pages/dal_com_calc_data.php",
		{
			type:typ,
			date1:$("#date1").val(),
			date2:$("#date2").val(),
			refbydoctorid:ref_docs,
			encounter:$("#encounter").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		})
	}
	
	function print_page(typ,dt1,dt2,doc,encounter)
	{
		var url="pages/dal_com_calc_print.php?typ="+btoa(typ)+"&dt1="+btoa(dt1)+"&dt2="+btoa(dt2)+"&rdoc="+btoa(doc)+"&tp="+btoa(encounter);
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function export_page(typ,dt1,dt2,doc,encounter)
	{
		if(typ=="dept_wise")
		{
			var url="pages/dal_com_calc_doctor_wise_xls.php?typ="+btoa(typ)+"&dt1="+btoa(dt1)+"&dt2="+btoa(dt2)+"&rdoc="+btoa(doc)+"&tp="+btoa(encounter);
		}
		if(typ=="summary_wise")
		{
			var url="pages/dal_com_calc_summary_wise_xls.php?typ="+btoa(typ)+"&dt1="+btoa(dt1)+"&dt2="+btoa(dt2)+"&rdoc="+btoa(doc)+"&tp="+btoa(encounter);
		}
		if(typ=="bill_wise")
		{
			var url="pages/dal_com_calc_bill_wise_xls.php?typ="+btoa(typ)+"&dt1="+btoa(dt1)+"&dt2="+btoa(dt2)+"&rdoc="+btoa(doc)+"&tp="+btoa(encounter);
		}
		if(typ=="bill_wise_detail")
		{
			var url="pages/dal_com_calc_bill_wise_detail_xls.php?typ="+btoa(typ)+"&dt1="+btoa(dt1)+"&dt2="+btoa(dt2)+"&rdoc="+btoa(doc)+"&tp="+btoa(encounter);
		}
		document.location=url;
	}
	
	function load_doctors()
	{
		$("#loader").show();
		$.post("pages/dal_com_calc_data.php",
		{
			type:"load_doctors",
			doc_name:$("#doc_name").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_doctors").show().html(data);
		})
	}
	
	function select_doc(doc_id)
	{
		var sel_docs=$("#sel_refbydoctorid").val();
		
		let position = sel_docs.search(doc_id);
		
		if(position>=0)
		{
			alert("Already selected");
			return false;
		}
		
		$("#sel_refbydoctorid").val(sel_docs+","+doc_id);
		
		$("#tr"+doc_id).hide();
		
		load_sel_doctors();
	}
	
	function load_sel_doctors()
	{
		$("#loader").show();
		$.post("pages/dal_com_calc_data.php",
		{
			type:"load_sel_doctors",
			sel_refbydoctorids:$("#sel_refbydoctorid").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_sel_doctors").show().html(data);
		})
	}
	
	function remove_doc(doc_id)
	{
		var sel_docs=$("#sel_refbydoctorid").val();
		
		var remove_doc=","+doc_id;
		
		sel_docs = sel_docs.replace(remove_doc,"");
		
		$("#sel_refbydoctorid").val(sel_docs);
		
		$("#tr"+doc_id).show();
		
		load_sel_doctors();
	}
</script>
<style>
.ScrollStyle
{
    max-height: 400px;
    overflow-y: scroll;
}
.ScrollStyledoc
{
    max-height: 200px;
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
.select2-dropdown
{
	z-index:99999 !important;
}
.select2
{
	margin-bottom: 1%;
}

.side_name
{
	border: 1px solid #ddd;
	background-color: #fff;
	padding: 4px;
	position: absolute;
	font-weight:bold;
}

.span5{
	width: 45% !important;
}
</style>
