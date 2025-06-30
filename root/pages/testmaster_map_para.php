<?php
include("../../includes/connection.php");
$testid = $_POST['id'];

$test_info=mysqli_fetch_assoc(mysqli_query($link, "SELECT `testname` FROM `testmaster` WHERE `testid`='$testid'"));

$test_qry=mysqli_query($link, "SELECT `testid`,`testname` FROM `testmaster` WHERE `category_id`='1' ORDER BY `testname` ASC");
?>
<div>
	<table class="table table-bordered table-condensed">
		<tr>
			<td colspan="2">
				<b>ID: <?php echo $testid; ?></b> | 
				<b>Test Name: </b>
				<input type="text" value="<?php echo $test_info['testname']; ?>" readonly>
				<input type="text" id="searchh" onkeyup="search(this.value)" placeholder="Type to search Parameter">
				<select onChange="selected_test(this.value)">
					<option value='0'>Select Test</option>
					<?php
					while ($test = mysqli_fetch_array($test_qry)) {
						echo "<option value='$test[testid]'>$test[testname]</option>";
					}
					?>
				</select>
				<button class="btn btn-info" onclick="load_dlc_check(<?php echo $testid; ?>)" style="margin-top:0px">Add DLC Check</button>
				<button class="btn btn-warning" onclick="load_testParam(<?php echo $testid; ?>)" style="margin-top:0px"> <i class="icon-refresh"></i> Reset Test Param</button>
			</td>
		</tr>
		<tr>
			<td width="35%">
				<div style="height:420px;overflow:auto;overflow-x:hidden" id="param_load">
					<span id="no_record"></span>
				</div>
			</td>
			<td width="65%">
				<div style="height:420px;overflow:auto;overflow-x:hidden" id="testParam_load"></div>
			</td>
		</tr>
	</table>
	<div style="text-align:center;">
		<button class="btn btn-close" data-dismiss="modal"><i class="icon-off"></i> Close</button>
	</div>
</div>
<script>
	function search(inputVal) {
		var table = $('#tblData');
		table.find('tr').each(function (index, row) {
			var allCells = $(row).find('td');
			if (allCells.length > 0) {
				var found = false;
				allCells.each(function (index, td) {
					var regExp = new RegExp(inputVal, 'i');
					if (regExp.test($(td).text())) {
						found = true;
						return false;
					}
				});
				if (found == true) {
					$("#no_record").text("");
					$(row).show();
				} else {
					$(row).hide();
					var n = $('tr:visible').length;
					if (n == 1) {
						$("#no_record").text("No matching records found");
					} else {
						$("#no_record").text("");
					}
				}
			}
		});
	}
	function selected_test(id) {
		$("#searchh").val('');
		load_param(id);
	}
	function load_param(testid)
	{
		$.post("pages/load_testparam.php",
		{
			type:"load_param",
			testid: testid
		},
		function (data, status) {
			$("#param_load").html(data);
		})
	}
	
	function load_testParam(testid)
	{
		$.post("pages/load_testparam.php",
		{
			type:"testParam",
			testid:testid
		},
		function (data, status)
		{
			$("#testParam_load").html(data);
		})
	}
	function load_sample_vaccu(testid,param_id,samp_id)
	{
		$.post("pages/load_testparam.php",
		{
			type:"load_sample_vaccu",
			testid:testid,
			param_id:param_id,
			samp_id:samp_id,
		},
		function (data, status)
		{
			$("#vac_"+param_id).html(data);
			
			//update_sample(testid,param_id,samp_id);
		})
	}
	
	function add_parameter(testid,param_id)
	{
		var error=0;
		var each_row=$(".each_row");
		for(var i=0;i<each_row.length;i++)
		{
			var tr_counter=each_row[i].value;
			
			var selParamId=$("#selParamId"+tr_counter).val();
			
			if(selParamId==param_id)
			{
				alertmsg("Parameter already added", 0);
				error++;
				return false;
			}
		}
		
		if(error==0)
		{
			var tr_counter=$("#tr_counter").val().trim();
			
			$("#loader").show();
			$.post("pages/load_testparam.php",
			{
				type:"add_parameter",
				testid:testid,
				param_id:param_id,
				tr_counter:tr_counter,
			},
			function (data, status)
			{
				$("#loader").hide();
				
				$("#testPramFooter").before(data);
				
				var next_tr_counter=parseInt($("#tr_counter").val())+1;
				$("#tr_counter").val(next_tr_counter);
				
				$("#TestParameterTable").animate({ scrollTop: 5000 });
			})
		}
	}
	
	function add_all_param(testid)
	{
		var each_row=$(".testParamCls");
		for(var i=0;i<each_row.length;i++)
		{
			var testParamVal=each_row[i].value;
			
			$("#add_btn"+testParamVal).click();
		}
	}
	
	function sequence_up(slno,e)
	{
		if(e.which==13)
		{
			var slno=parseInt(slno)+1;
		}
		if(e.which==40)
		{
			var slno=parseInt(slno)+1;
		}
		if(e.which==38)
		{
			var slno=parseInt(slno)-1;
		}
		$(".sequence"+slno).focus();
	}
	
	function saveMapParameter(testid)
	{
		var each_row=$(".each_row");
		if(each_row.length>=0)
		{
			var dataArray = [];
			
			for(var i=0;i<each_row.length;i++)
			{
				var tr_counter=each_row[i].value;
				
				var param_id=$("#selParamId"+tr_counter).val();
				
				var sample_id=$("#samp_"+param_id).val();
				var vaccu_id=$("#vac_"+param_id).val();
				var mandatory=$("#paramMandatory"+param_id+":checked").length;
				var status=$("#paramDontPrint"+param_id+":checked").length;
				var sequence=$("#paramSequence"+param_id).val();
				
				dataArray.push({param_id: param_id, sample_id: sample_id, vaccu_id: vaccu_id, mandatory: mandatory, status: status, sequence: sequence});
			}
		}
		
		var save_data = JSON.stringify(dataArray);
		
		$("#loader").show();
		$.post("pages/load_testparam.php",
		{
			type:"saveMapParameter",
			testid:testid,
			save_data:save_data,
		},
		function (data, status)
		{
			$("#loader").hide();
			
			var res=JSON.parse(data);
			if(res["error"]==0)
			{
				alertmsg(res["message"], 1);
				
				load_testParam(testid)
			}else
			{
				alertmsg(res["message"], 0);
			}
		})
	}
</script>
<?php
mysqli_close($link);
?>
