<?php
$p_info["branch_id"]=1;
if($p_info["levelid"]==1 && $p_info["branch_id"]==1)
{
	$branch_str="";
	$branch_display="display:none;";
	
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
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div id="input_div">
		<table class="table table-bordered text-center">
			<tr>
				<td>
					<b>UHID</b>
					<input list="browsrs" type="text" class="span2" id="uhid" >
					<b>Bill No</b>
					<input type="text" class="span2" id="pin" onkeyup="pin_up(this,event)">
					<button class="btn btn-info" onClick="view_all()" style="margin-top: -1%;" ><i class="icon-search"></i> Search</button>
				</td>
				<td style="display:none;">
					<b>Phone</b>
					<input type="text" class="span2" id="phone" onKeyup="view_all()">
				</td>
			</tr>
		</table>
	</div>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		
	</div>
</div>
<input type="hidden" id="list_start" value="50">
<!-- Loader -->
<div id="loader" style="margin-top:0%;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<!-- Time -->
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />

<script>
	$(document).ready(function(){
		$("#loader").hide();
		view_all();
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
			//defaultDate:'2000-01-01',
		});
		
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
		$("#pin").focus();
	});
	$( document ).tooltip();
	
	function pin_up(dis,e)
	{
		if(e.which==13)
		{
			view_all();
		}
	}
	
	function view_all()
	{
		$("#loader").show();
		$.post("pages/test_result_qr_data.php",
		{
			type:"load_all_pat",
			pat_uhid:$("#uhid").val(),
			pin:$("#pin").val(),
			phone:$("#phone").val(),
			list_start:$("#list_start").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			//$("#load_all").show().html(data);
			$("#input_div").slideDown(500);
			$("#load_all").slideUp(500,function(){ $("#load_all").html(data).slideDown(500); });
		})
	}
	
	function redirect_page(uhid,opd,ipd,batch,testid)
	{
		$("#loader").show();
		$.post("pages/test_result_qr_data.php",
		{
			type:"load_result",
			uhid:uhid,
			opd:opd,
			ipd:ipd,
			batch:batch,
			testid:testid,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#input_div").slideUp(500);
			$("#load_all").slideUp(500,function(){ $("#load_all").html(data).slideDown(500); });
		})
	}
</script>
<style>
.ScrollStyle
{
    max-height: 400px;
    overflow-y: scroll;
}
label
{
	display: inline;
}
.modal.fade.in {
    top: 1%;
}
</style>
<?php
//~ $post = [
    //~ 'username' => 'user1',
    //~ 'password' => 'passuser1',
    //~ 'gender'   => 1,
//~ ];
//~ $ch = curl_init();
//~ curl_setopt($ch, CURLOPT_URL, 'http://e-adware.com/website/index.php/contact');
//~ curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//~ curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
//~ $response = curl_exec($ch);
//~ var_export($response);
?>
