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
					<input class="form-control datepicker" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" >
					<b>To</b>
					<input class="form-control datepicker" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" >
					<!--<select id="service_id" class="span2" onChange="view_all('doctor_service_report')">
						<option value="0">All Service</option>
					<?php
						$charge_qry=mysqli_query($link, " SELECT `charge_id`, `charge_name` FROM `charge_master` WHERE `group_id`='142' ORDER BY `charge_name` ");
						while($charge=mysqli_fetch_array($charge_qry))
						{
							echo "<option value='$charge[charge_id]'>$charge[charge_name]</option>";
						}
					?>
					</select>-->
					<select id="consultantdoctorid">
						<option value="0">All Doctor</option>
					<?php
						$con_doc_qry=mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `consultant_doctor_master` order by `Name` ");
						while($con_doc=mysqli_fetch_array($con_doc_qry))
						{
							echo "<option value='$con_doc[consultantdoctorid]'>$con_doc[Name]</option>";
						}
					?>
					</select>
					<br>
					<button class="btn btn-success" onClick="view_all('attend')">Consultant Doctor</button>
					<button class="btn btn-success" onClick="view_all('admit')">Admitted Doctor</button>
					
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
<script>
	$(document).ready(function(){
		$("#loader").hide();
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
		});
	});
	function view_all(typ)
	{
		$("#loader").show();
		$.post("pages/admit_pat_list_qry.php",
		{
			type:"view_admit_pat_list",
			typ:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
			consultantdoctorid:$("#consultantdoctorid").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		})
	}
	function print_page(typ,dt1,dt2,doc)
	{
		url="pages/admit_pat_list_print.php?typ="+typ+"&fdate="+dt1+"&tdate="+dt2+"&doc="+doc;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function print_doc_pat(el)
	{
		$("#excel_btn_hide").hide();
		var restorepage = $('body').html();
		var printcontent = $('#' + el).clone();
		$('body').empty().html(printcontent);
		window.print();
		$('body').html(restorepage);
		
		/*var disp_setting="toolbar=yes,location=no,";
		disp_setting+="directories=yes,menubar=yes,";
		disp_setting+="scrollbars=yes,width=650, height=600, left=100, top=25";
		var content_vlue = document.getElementById(el).innerHTML;
		var docprint=window.open("","",disp_setting);
		docprint.document.open();
		docprint.document.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"');
		docprint.document.write('"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">');
		docprint.document.write('<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">');
		docprint.document.write('<head><title>My Title</title>');
		docprint.document.write('<style type="text/css">body{ margin:0px;');
		docprint.document.write('font-family:verdana,Arial;color:#000;');
		docprint.document.write('font-family:Verdana, Geneva, sans-serif; font-size:12px;}');
		docprint.document.write('a{color:#000;text-decoration:none;} </style>');
		docprint.document.write('</head><body onLoad="self.print()"><center>');
		docprint.document.write(content_vlue);
		docprint.document.write('</center></body></html>');
		docprint.document.close();
		docprint.focus();
		*/
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
</style>
