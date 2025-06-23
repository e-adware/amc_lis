<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">IPD Lab Registration Form</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<!--<h2 class="alert_msg"></h2>-->
<?php
	
?>
	<table id="padd_tbl" class="table table-condensed">
		<tr>
			<td colspan="6">
				<center>
					<h4>Search</h4>
				</center>
			</td>
		</tr>
		<tr>
			<th>UHID</th>
			<td>
				<input type="text" id="search_uhid" onkeyup="load_emp(this.value,event,'uhid')" placeholder="Type UHID" autofocus >
			</td>
			<th>Name</th>
			<td>
				<input type="text" id="search_name" onkeyup="load_emp(this.value,event,'name')" placeholder="Type Name" >
			</td>
		</tr>
		<tr>
			<td colspan="6">
				<div id="emp_list" style="max-height:450px;overflow-y:scroll;">
					
				</div>
			</td>
		</tr>
	</table>
</div>

<script>
var emp_d=1;
var emp_div=0;
function load_emp(val,e,typ)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==13)
	{
		var eid=$("#e_id"+emp_d+"").val();
		//eid=eid.split("@@");
		//var tst=$("#testt").val();
		load_emp_details(eid);
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
			$.post("pages/pat_regd_pat_master.php",
			{
				val:val,
				type:11,
				typ:typ,
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

function load_emp_details(uhid)
{
	bootbox.dialog({ message: "<b>Redirecting to Patient Dashboard</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> "});
	
	setTimeout(function(){
		window.location="processing.php?param=3&uhid="+uhid+"&lab=1";
	 }, 2000);
}
</script>

