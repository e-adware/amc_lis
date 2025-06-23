<div id="content-header">
    <div class="header_div"> <span class="header"> Test Status Details</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div>
		<table class="table table-bordered table-condensed text-center" id="search_test">
			<tr>
				<th colspan="3" style="text-align:center">
					<b>From</b>
					<input class="form-control datepicker" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" >
					<b>To</b>
					<input class="form-control datepicker" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" ><br>
				</th>
			</tr>
			<tr>
				<th style="text-align:center">
					Select Category <br/>
					<select id="cat" class="span3" onchange="load_dep_test()">
						<option value="0">-All-</option>
						<option value="1">PATHOLOGY</option>
						<option value="2">RADIOLOGY</option>
						<option value="3">CARDIOLOGY</option>
					</select>
				</th>
				<th style="text-align:center">
					Select Department <br/>
					<span id="dep_span">
						<select id="dep" class="span4" onchange="load_test()">
							<option value="0">-All-</option>
							<?php
							$dept=mysqli_query($link,"select * from test_department order by name");
							while($dep=mysqli_fetch_array($dept))
							{
								echo "<option value='$dep[id]'>$dep[name]</option>";
							}
							?>
						</select>
					</span>
				</th>
				<th style="text-align:center">
					Select Test <br/>
					<span id="test_span">
						<select id="test" class="span4">
							<option value="0">-All-</option>
							<?php
							$test=mysqli_query($link,"select * from testmaster order by testname");
							while($tst=mysqli_fetch_array($test))
							{
								echo "<option value='$tst[testid]'>$tst[testname]</option>";
							}
							?>
						</select>
					</span>
				</th>
			</tr>
			<tr>
				<th colspan="3" style="text-align:center">
					Status 
					<select id="status">
						<option value="0">All</option>
						<option value="1">Done</option>
						<option value="2">Pending</option>
					</select>
				</th>
			</tr>
			<tr>
				<th colspan="3" style="text-align:center">
					<button class="btn btn-info" onclick="search()">Search</button>
				</th>
			</tr>
		</table>
	</div>
	
	<div id="search_data">
	
	
	
	</div>
</div>

<link rel="stylesheet" href="../css/select2.min.css" />
<link rel="stylesheet" href="../css/loader.css" />
<script src="../js/select2.min.js"></script>
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />

<style>
.table .head{ background-color:#ccc}
#print_header{display:none;}
@media print
{
	#header ,#search,.modal,#sidebar,#search_test {display:none;} 
	table tr td,table tr td{ font-size:10px;}
	.head{font-size:12px;}
	#search_data{ margin-left:-50px;}
	#print_header{display:block;}
}
</style>

<script>
$(document).ready(function()
			{
				$("select").select2({ theme: "classic" });
				$(".datepicker").datepicker({
					dateFormat: 'yy-mm-dd',
					maxDate: '0',
				});
			})


function load_dep_test()
{
	$.post("pages/all_test_details_ajax.php",
	{
		cat:$("#cat").val(),
		type:1
	},
	function(data,status)
	{
		$("#dep_span").html(data);
		$("select").select2({ theme: "classic" });
		load_test();
	})
}

function load_test()
{
	$.post("pages/all_test_details_ajax.php",
	{
		cat:$("#cat").val(),
		dep:$("#dep").val(),
		type:2,
	},
	function(data,status)
	{
		$("#test_span").html(data);
		$("select").select2({ theme: "classic" });
	})
}

function search()
{
	$.post("pages/all_test_details_ajax.php",
	{
		from:$("#from").val(),
		to:$("#to").val(),
		cat:$("#cat").val(),
		dep:$("#dep").val(),
		test:$("#test").val(),
		status:$("#status").val(),
		type:3
	},
	function(data,status)
	{
		$("#search_data").html(data);
	})	
}
</script>
