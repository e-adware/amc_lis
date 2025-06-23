<?php
include("../../includes/connection.php");
require('../../includes/global.function.php');

$branch_id=mysqli_real_escape_string($link, $_GET["bid"]);

$date=date("Y-m-d");
$time=date("H:i:s");

//$dt_tm=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' and `opd_id`='$opd_id' "));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Estimate Receipt</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../css/custom.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
</head>

<body onafterprint="window.close();" onkeyup="close_window(event)">
	<div class="container-fluid">
		<div>
			<?php include('page_header.php'); ?>
		</div>
		<hr>
		<center>
			<div class="noprint">
				<button class="btn btn-success" onclick="javascript:window.print()"><i class="fa fa-print"></i> Print</button>
				<button class="btn btn-danger" onclick="javascript:window.close()"><i class="fa fa-power-off"></i> Exit</button>
			</div>
		</center>
		<center><h5>Estimate Receipt</h5></center>
		<div id="load_data"></div>
		<div id="entry_data">
			<table class="table table-condensed">
				<tr>
					<td style="text-align:center;">
						<h5>Enter Test</h5>
						<input type="text" class="doctor_info span5" name="test" id="test" onkeyup="select_test_new(this.value,event)" onblur="test_blur()" placeholder="Search Test Name Here" autofocus />
						<input type="hidden" id="test_ids" value="">
						<input type="hidden" name="tr_counter" id="tr_counter" class="form-control" value="1"/>
					</td>
				</tr>
				<tr>
					<td>
						<div id="test_d">
							
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
</body>
</html>
<script>
	
	// Test Search Start
	
	function test_blur()
	{
		$("#test").val("");
		$("#test_d").html("");
		t_val=1;
		t_val_scroll=0;
	}
	
	var t_val=1;
	var t_val_scroll=0;

	var _changeInterval = null;
	function select_test_new(val,e)
	{
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
			test_blur();
		}
		else
		{
			search_test();
		}
	}
	function search_test()
	{
		$.post("new_lab_registration_data.php",
		{
			type:"search_test",
			test:$("#test").val(),
			patient_id:$("#patient_id").text().trim(),
			opd_id:$("#opd_id").text().trim(),
			center_no:$("#center_no").val(),
			category_id:$("#category_id").val(),
			dept_id:$("#dept_id").val(),
		},
		function(data,status)
		{
			$("#test_d").html(data);
			t_val=1;
			t_val_scroll=0;
		})
	}
	
	function load_test_click(id,name,rate)
	{
		load_test_new(id,name,rate);
	}
	function load_test_new(id,name,rate)
	{
		//alert(id+" "+name+" "+rate);
		
		var tr_counter=$("#tr_counter").val();
		
		var test_chk= $('#test_list tr').length;
		if(test_chk==0)
		{
			var test_add="<table class='table table-bordered' id='test_list'>";	
			test_add+="<tr><th style='width: 2%;'>#</th><th style='width: 80%;'>Test Name</th><th style='text-align:right;width: 150%;'>Amount</th><th class='remove_cls' style='text-align:right;width: 3%;'>Remove</th></tr>";
			
			test_add+="<tr id='"+tr_counter+"'><td>"+tr_counter+"<input type='hidden' value='"+id+"' class='test_id'></td><td>"+name+"</td><td style='text-align:right;'>"+rate+"<input type='hidden' value='"+rate+"' id='test_rate"+id+"'></td><td class='remove_cls' style='text-align:right;' onclick='remove_tr("+tr_counter+")'><button class='btn btn-danger btn-mini'>X</button></td></tr>";
			
			test_add+="<tr id='tr_footer'><th></th><th style='text-align:right;'>Total</th><th style='text-align:right;'><span id='total_amount'>0.00</span></th><th></th></tr>";
			test_add+="</table>";
			$("#load_data").html(test_add);
			
			tr_counter++;
			$("#tr_counter").val(tr_counter);
			
			total_amount();
		}
		else
		{
			var t_ch=0;
			var test_l=document.getElementsByClassName("test_id");
			
			for(var i=0;i<test_l.length;i++)
			{
				if(test_l[i].value==id)
				{
					t_ch=1;
				}
			}
			if(t_ch)
			{
				alert("Same test already exists");
				return false;
			}
			else
			{
				var test_add="<tr id='"+tr_counter+"'><td>"+tr_counter+"<input type='hidden' value='"+id+"' class='test_id'></td><td>"+name+"</td><td style='text-align:right;'>"+rate+"<input type='hidden' value='"+rate+"' id='test_rate"+id+"'></td></td><td class='remove_cls' style='text-align:right;' onclick='remove_tr("+tr_counter+")'><button class='btn btn-danger btn-mini'>X</button></td></tr>";
				
				$("#test_list").closest('table').find('tr:last').prev().after(test_add);
				
				tr_counter++;
				$("#tr_counter").val(tr_counter);
				
				total_amount();
			}
		}
	}
	
	function total_amount()
	{
		var test_l=document.getElementsByClassName("test_id");
		var total=0;
		for(var i=0;i<test_l.length;i++)
		{
			total=total+parseInt($("#test_rate"+test_l[i].value).val());
		}
		
		total=total.toFixed(2);
		$("#total_amount").text(total);
	}
	
	function remove_tr(slno)
	{
		$("#"+slno).remove();
		
		total_amount();
	}
	
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			window.close();
		}
	}
</script>
<style>
*{
	font-size:13px;
}
.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
{
	padding: 0 0 0 0;
}
.table
{
	margin-bottom:5px;
}
hr
{
	margin:0;
	border-bottom:0;
	border-top: 1px solid #000;
}

@media print
{
	#entry_data
	{
		display:none;
	}
	.noprint, .remove_cls{
		display:none;
	 }
}
</style>
