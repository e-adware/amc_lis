<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span> </div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="ScrollStyle">
		<table class="table table-bordered table-condensed text-center">
			<tr>
				<th>#</th>
				<th>UHID</th>
				<th>PIN</th>
				<th>Patient Name</th>
				<th>Bill Amount</th>
				<th>Discount Amount</th>
				<th>Reason</th>
				<th>User</th>
				<th>Approve</th>
			</tr>
		<?php
			$rupees_symbol="&#x20b9; ";
			$date=date("Y-m-d");
			$n=1;
			$qq_qry=mysqli_query($link, " SELECT * FROM `discount_approve` WHERE `approve_by`='0' ORDER BY `slno` DESC ");
			while($qq=mysqli_fetch_array($qq_qry))
			{
				$pat_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$qq[patient_id]' "));
				$user=mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$qq[user]' "));
				
			?>
				<tr>
					<td><?php echo $n; ?></td>
					<td><?php echo $pat_info["patient_id"];?></td>
					<td><?php echo $qq["pin"]; ?></td>
					<td><?php echo $pat_info["name"]; ?></td>
					<td><?php echo $rupees_symbol.$qq["bill_amount"]; ?></td>
					<td><?php echo $rupees_symbol.$qq["dis_amount"]; ?></td>
					<td><?php echo $qq["reason"]; ?></td>
					<td><?php echo $user["name"]; ?></td>
					<td>
						<button class="btn btn-info" onClick="dis_approve('<?php echo $qq["patient_id"]; ?>','<?php echo $qq["pin"]; ?>','<?php echo $qq["dis_amount"]; ?>')">Approve</button>
					</td>
				</tr>
			<?php
				$n++;
			}
		?>
		</table>
	</div>
</div>
<script>
	function dis_approve(patient_id,pin,dis_amount)
	{
		var msg="<h5>Are you sure want to approve</h5><br>Discount:<input type='text' value='"+dis_amount+"' id='dis_amt' autofocus />";
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: msg,
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Approve',
					className: "btn btn-danger",
					callback: function() {
						$.post("pages/discount_approve_data.php",
						{
							type:"approve_discount",
							patient_id:patient_id,
							pin:pin,
							dis_amt:$("#dis_amt").val(),
							user:$("#user").text().trim(),
						},
						function(data,status)
						{
							bootbox.dialog({message: "<b>Approved</b>"+data});
							setTimeout(function(){
									window.location.reload(true);
							 }, 2000);
						})
					}
				}
			}
		});
	}
</script>
<style>
.ScrollStyle
{
    max-height: 500px;
    overflow-y: scroll;
}
</style>
