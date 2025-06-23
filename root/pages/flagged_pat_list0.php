<!--header-->
<div id="content-header">
    <div class="header_div"><span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-condensed">
		<tr>
			<td>
				<div class="btn-group">
					<input type="text" value="From" style="width:60px;font-weight:bold;cursor:default;" disabled />
					<input class="span2" type="text" id="fdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" readonly />
					<input type="text" value="To" style="width:40px;font-weight:bold;cursor:default;" disabled />
					<input class="span2" type="text" id="tdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" readonly />
				</div>
			</td>
			<td>
				<button type="button" class="btn btn-info" onclick="view()">Flagged Patients List</button>
			</td>
		</tr>
	</table>
	<div id="res"></div>
</div>

	<!--modal-->
    <input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none" />
    <input type="text" id="modtxt" value="0" style="display:none" />
    <div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
        style="border-radius:0;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div id="results">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--modal end-->
    
    <!-- Trigger the modal with a button -->
	<button type="button" class="btn btn-info btn-lg" id="modal_btn_repeat" data-toggle="modal" data-target="#myModal_repeat" style="display:none;">Repeat Param</button>
	<div id="myModal_repeat" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<!--<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Modal Header</h4>
				</div>-->
				<div class="modal-body">
					<div id="result_repeat"></div>
				</div>
				<div class="modal-footer" style="text-align:center;">
					<button class="btn btn-close" data-dismiss="modal"><i class="icon-off"></i> Close</button>
				</div>
			</div>
		</div>
	</div>
    
<style>
	input[type="text"], select
	{
		margin-bottom: 0px;
	}
	input[readonly]
	{
		background: #FFF;
		cursor: pointer;
	}
	#myModal, #myModal_repeat
	{
		width: 85%;
		left: 26%;
	}
	.modal-body
	{
		padding: 10px;
	}
	#result_repeat table
	{
		margin-bottom: 0px;
	}
</style>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd', maxDate:0});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd', maxDate:0});
	});
	
	function hid_div(e)
	{
		
	}
	function view()
	{
		$.post("pages/flagged_pat_list_ajax.php",
		{
			fdate	:$("#fdate").val().trim(),
			tdate	:$("#tdate").val().trim(),
			type	:1
		},
		function(data,status)
		{
			$("#res").html(data);
		});
	}
	function back_to_list()
	{
		view();
	}
	function view_flag_pat(uhid,opd_id,ipd_id,batch_no,dept_id)
	{
		$.post("pages/pathology_result_approve_tech_data.php",
		{
			type	:"load_pat_dept_tests",
			uhid	:uhid,
			opd_id	:opd_id,
			ipd_id	:ipd_id,
			batch_no:batch_no,
			dept_id	:dept_id,
			flagVl	:1
		},
		function(data,status)
		{
			//$("#results").html(data);
			$("#res").html(data);
			//$("#mod").click();
		});
	}
	
	function repeat_param_view(uhid,opd_id,ipd_id,batch_no,testid,paramid,iso_no)
	{
		$.post("pages/pathology_reporting_repeat_param_tech.php",
		{
			type:"repeat_param_view",
			uhid:uhid,
			opd_id:opd_id,
			ipd_id:ipd_id,
			batch_no:batch_no,
			testid:testid,
			paramid:paramid,
			iso_no:iso_no,
		},
		function(data,status)
		{
			$("#result_repeat").html(data);
			$("#modal_btn_repeat").click();
			
		})
	}
	
	function unflag_pat(uhid,opd_id,ipd_id,batch_no,dept_id)
	{
		bootbox.dialog({
			message: "Unflagged patient?",
			closeButton: false,
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-inverse",
					callback: function() {
						
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function() {
						alert("ok");
						//$("#canc").click();
					}
				}
			}
		});
	}
	
	function unflag_pat_old(sl)
	{
		bootbox.dialog({
			message: "Unflagged patient?",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-inverse",
					callback: function() {
						//alert("cancelled");
						$("#canc").click();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function() {
						//alert("ok");
						$("#canc").click();
					}
				}
			}
		});
	}
</script>