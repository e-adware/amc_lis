<?php
$emp_id=trim($_SESSION["emp_id"]);
$branch_display="display:none;";
if($p_info["levelid"]==1)
{
	$branch_str="";
	
	$branch_display="display:none;";
	$branch_num=mysqli_num_rows(mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 "));
	if($branch_num>1)
	{
		$branch_display="display:;";
	}
	
	$dept_sel_dis="";
}
else
{
	$branch_str=" AND branch_id='$p_info[branch_id]'";
	$branch_display="display:none;";
	
	$dept_sel_dis="disabled";
}

$branch_id=$p_info["branch_id"];
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Balance Patient List</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<b>Search Patient</b>
	<input type="text" id="search_data" onkeyup="view_all()" placeholder="Search Patient Bill No">
	<select id="branch_id" class="span3" onChange="view_all('opd_account')" style="<?php echo $branch_display; ?>">
	<?php
		$branch_qry=mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
		while($branch=mysqli_fetch_array($branch_qry))
		{
			if($branch_id==$branch["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
			echo "<option value='$branch[branch_id]' $branch_sel>$branch[name]</option>";
		}
	?>
	</select>
	<div id="load_all" class="ScrollStyle">
		
	</div>
</div>
<input type="hidden" id="list_start" value="50">
<!-- Loader -->
<div id="loader" style="margin-top:0%;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<script>
	$(document).ready(function(){
		$("#loader").hide();
		view_all();
		
		$('#load_all').on('scroll', function() {
			var div_height = $(this).get(0).scrollHeight;
			var div = $(this).get(0);
			
			if(div.scrollTop + div.clientHeight >= div.scrollHeight) {
				var list_start=$("#list_start").val().trim();
				list_start=parseInt(list_start)+50;
				$("#list_start").val(list_start);
				view_all();
			}
		});
	});
	function view_all()
	{
		$("#loader").show();
		$.post("pages/balance_receive_data.php",
		{
			type:"load_all_pat",
			search_data:$("#search_data").val(),
			list_start:$("#list_start").val(),
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").html(data);
		})
	}
	function redirect_page(uhid,ipd)
	{
		window.location="processing.php?param=115&uhid="+uhid+"&ipd="+ipd;
	}
	function search(inputVal)
	{
		var table = $('#tblData');
		table.find('tr').each(function(index, row)
		{
			var allCells = $(row).find('td');
			if(allCells.length > 0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
					var regExp = new RegExp(inputVal, 'i');
					if(regExp.test($(td).text()))
					{
						found = true;
						return false;
					}
				});
				if(found == true)
				{
					$("#no_record").text("");
					$(row).show();
				}else{
					$(row).hide();
					var n = $('tr:visible').length;
					if(n==1)
					{
						$("#no_record").text("No matching records found");
					}else
					{
						$("#no_record").text("");
					}
				}
				//if(found == true)$(row).show();else $(row).hide();
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
