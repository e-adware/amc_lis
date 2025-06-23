<?php
/*
$test=mysqli_query($link,"select * from Testparameter where TestId='33' order by sequence");
while($tst=mysqli_fetch_array($test))
{
	mysqli_query($link,"insert into lis_testing(testid,paramid) values('$tst[TestId]','$tst[ParamaterId]')");
}
*/
?>


<div id="content-header">
    <div class="header_div"> <span class="header"> LIS Testing</span></div>
</div>

<div class="container-fluid">
	
<table class="table table-bordered">

	<tr>
		<td> Enter ID</td> <td> <input type="text" id="pin"/></td>
	</tr>
	<tr>
		<td> Select Test</td>
		<td>
			<select id="test_s">
			<?php
				$tst_s=mysqli_query($link,"select distinct(testid) from lis_testing order by slno");
				while($ts_s=mysqli_fetch_array($tst_s))
				{
					$tname=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where testid='$ts_s[testid]'"));
					echo "<option value='$ts_s[testid]'>$tname[testname]</option>";
				}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2" style="text-align:center">
			<button class="btn btn-info" onclick="lis_insert()">Upload</button>
		</td>
	</tr>

</table>	
	
	
	
</div>	


<script>
	function lis_insert()
	{
		var pin=$("#pin").val().trim();
		
		if(pin!='')
		{
			$.post("pages/lis_testing_ajax.php",
			{
				pin:pin,
				test:$("#test_s").val(),
				type:1
			},
			function(data,status)
			{
				bootbox.alert("Uploaded");
			})
		}
		else
		{
			$("#pin").attr("placeholder","Can not be blank").css({'border':'1px solid red;'})
		}
	}

</script>
