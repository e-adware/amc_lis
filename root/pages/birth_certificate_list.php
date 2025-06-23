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
					<b>Mother UHID</b>
					<input type="text" class="span2" id="mother_uhid" onKeyup="view_all()" >
				
					<b>Baby UHID</b>
					<input type="text" class="span2" id="baby_uhid" onKeyup="view_all()" >
				</center>
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		
	</div>
</div>
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<script>
	$(document).ready(function(){
		$("#loader").hide();
		view_all();
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
		});
	});
	function view_all()
	{
		$("#loader").show();
		$.post("pages/birth_certificate_data.php",
		{
			type:"load_all_baby",
			mother_uhid:$("#mother_uhid").val(),
			baby_uhid:$("#baby_uhid").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		})
	}
	function print_birth_certificate(uhid,baby_id)
	{
		url="pages/baby_certificate.php?uhid="+uhid+"&baby_id="+baby_id;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
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
</style>
