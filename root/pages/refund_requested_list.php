<?php
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
	<input type="text" id="search_data" onkeyup="view_all()" placeholder="Search">
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
	<div id="load_all" class="ScrollStyle">
		
	</div>
</div>
<input type="hidden" id="list_start" value="50">
<!-- Loader -->
<div id="loader" style="display:;margin-top:-10%;"></div>
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

<script>
	$(document).ready(function()
	{
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
		$.post("pages/refund_request_ajax.php",
		{
			type:"refund_requested_pat_list",
			val:$("#search_data").val(),
			list_start:$("#list_start").val(),
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		})
	}
	function refund_request_process(uhid,opd_id,refund_request_id)
	{
		//bootbox.dialog({ message: "<span id='popup_text'><b>Re-directing</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> </span>",closeButton: false});
		
		window.location="?param="+btoa(313)+"&uhid="+btoa(uhid)+"&opd_id="+btoa(opd_id)+"&rrid="+btoa(refund_request_id);
		
	}
</script>
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
