<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Token List</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered table-condensed">
		<tr>
			<td>
				<b>From</b><!-- <?php echo date("Y-m-d"); ?> -->
				<input class="form-control datepicker span2" type="text" name="from" id="from" value="" >
				<b>To</b>
				<input class="form-control datepicker span2" type="text" name="to" id="to" value="" >
				<button class="btn btn-success" onClick="view_all()" style="margin-top: -1%;" >View</button>			
				<button class="btn btn-success" onClick="synchronize()" style="float:right;" >Synchronize</button>			
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
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
		});
		view_all();
	});
	function synchronize()
	{
		$("#loader").show();
		$.post("pages/client_complaint_compose_data.php",
		{
			type:"synchronize_all_complaints",
		},
		function(data,status)
		{
			$("#loader").hide();
			bootbox.dialog({ message: data});
		
			setTimeout(function(){
				window.location.reload(true);
			 }, 2000);
		})
	}
	function view_all()
	{
		$("#loader").show();
		$.post("pages/client_complaint_compose_data.php",
		{
			type:"load_all_complaints",
			from:$("#from").val(),
			to:$("#to").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		})
	}
	function view_each_complain(client_id,complaint_id)
	{
		bootbox.dialog({ message: "<b>Redirecting to inbox</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> "});
		
		setTimeout(function(){
			window.location="processing.php?param=302&client_id="+client_id+"&complaint_id="+complaint_id;
		 }, 2000);
	}
</script>
<style>
textarea {
    resize: none;
}
</style>
