<?php
include'../../includes/connection.php';
$uhid=$_GET['uhid'];
$visitid=$_GET['opdid'];
$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` limit 0,1 "));
$pat_info=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `patient_info` WHERE `patient_id`='$uhid' "));
$contact="";
if($company_info['phone1'])
$contact=$company_info['phone1'];
if($company_info['phone2'])
$contact.=", ".$company_info['phone2'];
if($company_info['phone3'])
$contact.=", ".$company_info['phone3'];
?>
<html>
	<head>
		<title>Doctor Requisition Report</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
<style>
.table > thead > tr > th, .table > tbody > tr > th, .table > tfoot > tr > th, .table > thead > tr > td, .table > tbody > tr > td, .table > tfoot > tr > td
{
	padding: 2px;
	//border:1px solid;
	border-top:none;
}
.table
{
	margin-bottom:5px;
	font-size:10px;
}
.cfltbl tbody>tr>td
{
	font-size:10px;
}
.cfltbl
{
	display:inline-block;
}
label
{
	padding:3px;
	border:1px solid #dddddd;
	border-radius:5px;
	display:inline-block;
	margin-left:10px;
	font-size:10px;
}
.tbl > thead > tr > th, .tbl > tbody > tr > th, .tbl > tfoot > tr > th, .tbl > thead > tr > td, .tbl > tbody > tr > td, .tbl > tfoot > tr > td
{
	//border:1px solid;
}
@media print
{
	label
	{
		border:none;
		padding:0px;
		margin-left:10px;
		font-size:10px;
	}
}
</style>
	</head>
	<body>
		<div class="container-fluid">
			<div class="row">
				<table class="tbl table table-condensed">
					<tr>
						<td width="90%" style="text-align:center;border:1px solid;">
							<span style="font-size:20px;"><?php echo $company_info['name']; ?></span><br/>
							Ph No. <?php echo $contact; ?>
						</td>
						<td style="border:1px solid;">Case No.<br/>000<?php //echo $company_info['name']; ?></td>
					</tr>
				</table>
			<?php //echo $company_info['name']; ?>
			</div>
			<div class="row">
				<table class="table table-condensed" border="3">
					<tr>
						<td style="border-right:2px solid;" width="50%">
							<table class="tbl table table-condensed">
								<tr>
									<td style="border-right:1px solid;border-bottom:1px solid;" width="20%">Name :</td><td style="border-bottom:1px solid;"></td>
								</tr>
								<tr>
									<td style="border-right:1px solid;border-bottom:1px solid;">Address :</td><td style="border-bottom:1px solid;"></td>
								</tr>
								<tr>
									<td style="border-right:1px solid;border-bottom:1px solid;">C/C :</td><td style="border-bottom:1px solid;"></td>
								</tr>
								<tr>
									<td style="border-right:1px solid;border-bottom:1px solid;">Med. Hist :</td><td style="border-bottom:1px solid;"></td>
								</tr>
								<tr>
									<td style="border-right:1px solid;border-bottom:1px solid;">Family :</td>
									<td style="border-bottom:1px solid;">
										<label><input type="checkbox" value="1" /> TB</label> <label><input type="checkbox" value="1" /> Diabetics</label>
									</td>
								</tr>
								<tr>
									<td style="border-right:1px solid;border-bottom:1px solid;">History :</td>
									<td style="border-bottom:1px solid;">
										<label><input type="checkbox" value="1" /> Twin</label> <label><input type="checkbox" value="1" /> High BP</label>
									</td>
								</tr>
								<tr>
									<td style="border-right:1px solid;border-bottom:1px solid;">Mens Hist :</td>
									<td style="border-bottom:1px solid;">
										Menschr <u>___</u> L.M.P <u>___</u><br/>
										Cycle <u>___</u> E.D.C <u>___</u><br/>
										Flow <u>___</u> Pain <u>___</u><br/>
									</td>
								</tr>
								<tr>
									<td style="border-right:1px solid;border-bottom:1px solid;">CFL</td>
									<td style="border-bottom:1px solid;">
										<table class="cfltbl">
											<tr>
												<td>24878</td>
												<td>329789</td>
											</tr>
											<tr>
												<td>32689</td>
												<td>3289</td>
											</tr>
										</table>
										<table class="cfltbl">
											<tr>
												<td>L.D ___ Yrs___M</td>
											</tr>
											<tr>
												<td>L.A ___ Yrs___M</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td colspan="2">
									&nbsp;
									</td>
								</tr>
							</table>
						</td>
						<td>
							<table class="table table-condensed">
								<tr>
									<td style="border-bottom:1px solid;" width="20%">UHID :</td><td style="border-bottom:1px solid;"></td>
									<td style="border-bottom:1px solid;">PIN :</td><td style="border-bottom:1px solid;">Date</td>
								</tr>
								<tr>
									<td style="border-bottom:1px solid;">C/O :</td><td colspan="3" style="border-bottom:1px solid;"></td>
								</tr>
								<tr>
									<td style="border-right:1px solid;border-bottom:1px solid;">Age/Sex :</td><td style="border-right:1px solid;border-bottom:1px solid;"></td><td style="border-right:1px solid;border-bottom:1px solid;"></td><td style="border-bottom:1px solid;"></td>
								</tr>
								<tr>
									<td style="border-right:1px solid;border-bottom:1px solid;">G.E. :</td>
									<td colspan="3" style="border-bottom:1px solid;">
										<table class="cfltbl" width="100%" height="100%">
											<tr>
												<td>Wt.</td><td>1</td><td>Pallor</td><td>2</td><td>Oedemo</td><td>3</td>
											</tr>
											<tr>
												<td>Pulse.</td><td>787</td><td>B.P</td><td>255</td><td>Joundice</td><td>548</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td style="border-right:1px solid;border-bottom:1px solid;">Se :</td>
									<td colspan="3" style="border-bottom:1px solid;">
										
									</td>
								</tr>
								<tr>
									<td style="border-right:1px solid;border-bottom:1px solid;">Obgy :</td>
									<td colspan="3" style="border-bottom:1px solid;">
										
									</td>
								</tr>
								<tr>
									<td style="border-right:1px solid;border-bottom:1px solid;">P/A :</td>
									<td colspan="3" style="border-bottom:1px solid;">
										
									</td>
								</tr>
								<tr>
									<td style="border-right:1px solid;border-bottom:1px solid;">P/V :</td>
									<td colspan="3" style="border-bottom:1px solid;">
										
									</td>
								</tr>
								<tr>
									<td style="border-right:1px solid;border-bottom:1px solid;">P/D :</td>
									<td colspan="3" style="border-bottom:1px solid;">
										
									</td>
								</tr>
								<tr>
									<td>Lab Notes :</td>
									<td colspan="3">
										
									</td>
								</tr>
								<tr>
									<td>Treatments :</td>
									<td colspan="3">
										
									</td>
								</tr>
							</table>
							<span style="float:right;">Signature</span>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>
