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
<style>
	#padd_tbl th, #padd_tbl td
	{padding:0px;}
</style>
	
	<table id="padd_tbl" class="table table-condensed">
		<tr>
			<td colspan="8">
				<select id="branch_id" class="span2" onChange="view_all('opd_account')" style="<?php echo $branch_display; ?>">
				<?php
					$branch_qry=mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
					while($branch=mysqli_fetch_array($branch_qry))
					{
						if($branch_id==$branch["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
						echo "<option value='$branch[branch_id]' $branch_sel>$branch[name]</option>";
					}
				?>
				</select>
				<center>
					<h4>Search Patient</h4>
				</center>
			</td>
		</tr>
		<tr>
			<th>UHID</th>
			<td>
				<input type="text" id="search_uhid" onkeyup="load_emp(this.value,event,'uhid')" placeholder="Type UHID" />
			</td>
			<th>Bill No</th>
			<td>
				<input type="text" id="search_pin" onkeyup="load_emp(this.value,event,'pin')" placeholder="Type Bill No." autofocus />
			</td>
			<th>Name</th>
			<td>
				<input type="text" id="search_name" onkeyup="load_emp(this.value,event,'name')" placeholder="Type Name" />
			</td>
			<th>Phone No</th>
			<td>
				<input type="text" id="search_fname" onkeyup="load_emp(this.value,event,'phone')" placeholder="Type Phone No." />
			</td>
		</tr>
		<tr>
			<td colspan="8">
				<div id="emp_list" style="max-height:450px;overflow-y:scroll;">
					
				</div>
			</td>
		</tr>
	</table>
	<input type="hidden" id="chk_val2" value="0"/>
</div>
<!-- Loader -->
<div id="loader" style="display:none;margin-top:-10%;"></div>
<link rel="stylesheet" href="../css/loader.css" />

<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"/>
<input type="hidden" id="mod_chk" value="0"/>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div id="results"> </div>
				</div>
			</div>
		</div>
	</div>
<style>
<!--
.alert_msg
{
	position: absolute;
	top: 20%;
	left: 40%;
	color: green;
}-->
#myModal
{
	left: 23%;
	width:95%;
}
.modal.fade.in
{
	top: 3%;
}
.modal-body
{
	max-height: 540px;
}
</style>
<script>
	$(document).ready(function()
	{
		
	});
//===========================================================
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
		load_emp_details(eid[0],eid[1],typ,eid[3]);
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
				$("#emp_list").scrollTop(emp_div)
				
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
				$("#emp_list").scrollTop(emp_div)
				emp_div=emp_div+30;
			}
		}
	}
	else
	{
		if(val.length>0)
		{
			$.post("pages/refund_request_ajax.php",
			{
				val:val,
				type:1,
				typ:typ,
				branch_id:$("#branch_id").val(),
			},
			function(data,status)
			{
				$("#emp_list").html(data);
			})
		}else if(val.length==0)
		{
			$("#emp_list").html("");
		}
	}
	
}
function load_emp_details(uhid,opd_id,typ,v_typ)
{
	bootbox.dialog({ message: "<b>Please wait...</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> "});
	
	setTimeout(function()
	{
		//window.location="processing.php?param=181&uhid="+uhid+"&opd="+opd_id;
		window.location="?param="+btoa(311)+"&uhid="+btoa(uhid)+"&opd="+btoa(opd_id);
	 }, 500);
}
</script>
