<?php
session_start();

$date = date("Y-m-d");

if (!isset($_GET['param'])) {
	//$_GET['param']=base64_encode(82);
}
if (isset($_GET['param'])) {
	$para = base64_decode($_GET['param']);
	include("../inc/header.php");
	require('../includes/global.inc.php');
	require('../includes/global.function.php');
	$t_page = $page[$para];

	if (file_exists($t_page)) {
		if ($menu_access_info) {
			include $t_page;

		} else {
			include $t_page;
			//include "pages/error404.php";
			//echo "<script>window.location='index.php';</script>";
		}
	} else {
		echo "<center><img src='../emoji/404.jpeg'></center>";
	}

} else {
	$para = 0;
	include("../inc/header.php");

	$gret = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM `greetings` WHERE `date`='$date'"));
	if ($gret) {
		$userReview = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM `greetingsReview` WHERE `msg_id`='$gret[msg_id]' AND `emp_id`='$_SESSION[emp_id]'"));
		if (!$userReview) {
			include("welcome.php");
		}
	}

	if ($p_info["levelid"] == 5) {
		require('../includes/global.inc.php');
		require('../includes/global.function.php');

		$t_page = $page[15];

		if (file_exists($t_page)) {
			include $t_page;
		}
	} else {
		?>

		<!--header-->
		<div id="content-header">
			<div class="header_div">
				<span class="header">Dashboard</span>
				<!--<button class="btn btn-default" onClick="show_tel_no(1)" style="margin-left: 84%;">Tel No</button><br>-->
			</div>
		</div>
		<!--End-header-->
		<div class="container-fluid">
			<?php
			if ($p_info['levelid'] == 100 || $p_info['levelid'] == 500) {
				?>
				<table class="table table-condensed table-bordered table-report">
					<tr>
						<th colspan="9" style="text-align:center;">Overall</th>
					</tr>
					<tr>
						<th>Bill Amount</th>
						<th>Discount</th>
						<th>Amount Received</th>
						<th>Balance Received</th>
						<th>Refund</th>
						<th>Net Amount</th>
						<th style="display:none;">Free</th>
						<th>Balance</th>
					</tr>
					<tr>
						<td id="overall_bill"></td>
						<td id="overall_disc"></td>
						<td id="overall_amt_rcv"></td>
						<td id="overall_bal_rcv"></td>
						<td id="overall_ref"></td>
						<td id="overall_net"></td>
						<td style="display:none;" id="overall_free"></td>
						<td id="overall_bal"></td>
					</tr>
				</table>
				<?php
			}
			$overall_bill = 0;
			$overall_disc = 0;
			$overall_amt_rcv = 0;
			$overall_bal_rcv = 0;
			$overall_ref = 0;
			$overall_net = 0;
			$overall_free = 0;
			$overall_bal = 0;

			if (isset($_GET["date"])) {
				$date = $_GET["date"];
			}
			?>
			<div class="parent">
				<?php
				//~ if($p_info['levelid']==1)
				//~ {
				$snp = mysqli_fetch_array(mysqli_query($link, "SELECT `snippets` FROM `level_master` WHERE `levelid`='$p_info[levelid]'"));
				$sn = explode("@", $snp['snippets']);
				$len = sizeof(array_filter($sn));
				for ($s = 0; $s <= $len; $s++) {
					if ($sn[$s]) {
						$snip = "";
						if ($sn[$s] == 1) {
							$snip = "opd_snippets.php";
						}
						if ($sn[$s] == 2) {
							$snip = "invest_snippets.php";
						}
						if ($sn[$s] == 3 && $p_info["branch_id"] == 1) {
							$snip = "ipd_snippets.php";
						}
						if ($sn[$s] == 4) {
							$snip = "casuality_snippets.php";
						}
						if ($sn[$s] == 5) {
							$snip = "daycare_snippets.php";
						}
						if ($sn[$s] == 6) {
							$snip = "dental_snippets.php";
						}
						if ($sn[$s] == 7) {
							$snip = "dialysis_snippets.php";
						}
						if ($sn[$s] == 9) {
							$snip = "service_snippets.php";
						}
						if ($sn[$s] == 15) {
							$snip = "procedure_snippets.php";
						}
						if ($sn[$s] == 14) {
							$snip = "ambulance_snippets.php";
						}

						if ($sn[$s] == 11 && $p_info["branch_id"] == 1) {
							$snip = "bed_snippets.php";
						}
					}
					if ($sn[$s])
						include($snip);
				}
				//}
				//~ if($p_info['levelid']==3 || $p_info['levelid']==19)
				//~ {
				//~ include "opd_snippets_receipt.php";
				//~ include "ipd_snippets_receipt.php";
				//~ include "casuality_snippets_receipt.php";
				//~ include "bed_snippets.php";
				//~ }
				if ($p_info['levelid'] == 100 || $p_info['levelid'] == 500) {
					echo "<script>$('#overall_bill').text('" . number_format($overall_bill, 2) . "');</script>";
					echo "<script>$('#overall_disc').text('" . number_format($overall_disc, 2) . "');</script>";
					echo "<script>$('#overall_amt_rcv').text('" . number_format($overall_amt_rcv, 2) . "');</script>";
					echo "<script>$('#overall_bal_rcv').text('" . number_format($overall_bal_rcv, 2) . "');</script>";
					echo "<script>$('#overall_ref').text('" . number_format($overall_ref, 2) . "');</script>";
					echo "<script>$('#overall_net').text('" . number_format($overall_net, 2) . "');</script>";
					echo "<script>$('#overall_free').text('" . number_format($overall_free, 2) . "');</script>";
					echo "<script>$('#overall_bal').text('" . number_format($overall_bal, 2) . "');</script>";
				}
				?>
			</div>

			<!--<div class="quick-actions_homepage">
<ul class="quick-actions">
<li class="bg_lb my_url"> <a href="#/dash"> <i class="icon-dashboard"></i> <span class="label label-important">20</span> My Dashboard </a> </li>
<li class="bg_lg my_url"> <a href="#/charts/index.php?param=ODE="> <i class="icon-signal"></i> Charts</a> </li>
<li class="bg_ly my_url"> <a href="widgets.html"> <i class="icon-inbox"></i><span class="label label-success">101</span> Widgets </a> </li>
<li class="bg_lo my_url"> <a href="tables.html"> <i class="icon-th"></i> Tables</a> </li>
<li class="bg_ls my_url"> <a href="grid.html"> <i class="icon-fullscreen"></i> Full width</a> </li>
<li class="bg_lo my_url"> <a href="form-common.html"> <i class="icon-th-list"></i> Forms</a> </li>
</ul>
</div>-->

		</div>

		<div style="display: flex; gap: 20px; padding: 5px;">
			<!-- LEFT SIDE (60%) -->
			<div style="width: 55%; display: flex; flex-direction: column; gap: 5px;">
				<!-- TABLE -->
				<div style="max-height:40vh; overflow-y:auto;">
					<table class="table table-bordered table-condensed" style="background-color:white; margin-bottom:0;">
						<thead class="table_header_fix" style="position: sticky; top: 0; background: #fff; z-index: 2;">
							<tr style="background-color: cadetblue; color: white;">
								<th colspan="9" style="text-align:center;">Test Workflow Status</th>
							</tr>
							<tr>
								<th>Slno.</th>
								<th>Test Name</th>
								<th>Samples Received</th>
								<th>Samples Tested</th>
								<th>Samples Pending</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$n = 1;
							$total_received = 0;
							$total_tested = 0;
							$total_pending = 0;
							$test_names = [];
							$test_totals = [];

							$tst_name = mysqli_query($link, "SELECT `testname`,`testid` FROM `testmaster` WHERE `type_id`='20' ORDER BY `testname` ASC");
							while ($test_name = mysqli_fetch_array($tst_name)) {
								$test_id = $test_name['testid'];
								$test_count = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(distinct `opd_id`) AS `total` FROM `phlebo_sample` WHERE `testid`='$test_id' AND `date`= '$date'"));

								$tested = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(distinct a.`opd_id`) AS `completed` FROM `phlebo_sample` a, `approve_details` b WHERE a.`patient_id`=b.`patient_id` AND a.`opd_id`=b.`opd_id` AND a.`ipd_id`=b.`ipd_id` AND a.`testid`=b.`testid` AND a.`date`= '$date' AND b.`testid`='$test_id'"));

								$left = $test_count['total'] - $tested['completed'];

								if ($test_count['total'] > 0 || $tested['completed'] > 0 || $left > 0) {
									$total_received += $test_count['total'];
									$total_tested += $tested['completed'];
									$total_pending += $left;

									$test_names[] = $test_name['testname'];
									$test_totals[] = $test_count['total'];

									?>
									<tr>
										<td><?php echo $n++; ?></td>
										<td><?php echo $test_name['testname']; ?></td>
										<td><?php echo $test_count['total']; ?></td>
										<td><?php echo $tested['completed']; ?></td>
										<td><?php echo $left; ?></td>
									</tr>
									<?php
								}
							}
							?>
						</tbody>
					</table>
				</div>

				<!-- TOTALS -->
				<div
					style="font-weight:bold; background:#f5f5f5; padding:8px; border:1px solid #ddd; border-radius:4px; width:fit-content; margin: 0 auto; text-align:center;">

					<span
						style="background:#17A2B8; color:#fff; padding:6px 14px; border-radius:4px; margin-right:8px; display:inline-block;">
						Total Samples Received: <?php echo $total_received; ?>
					</span>
					<span
						style="background:#4CAF50; color:#fff; padding:6px 14px; border-radius:4px; margin-right:8px; display:inline-block;">
						Total Samples Tested: <?php echo $total_tested; ?>
					</span>
					<span style="background:#ED8936; color:#fff; padding:6px 14px; border-radius:4px; display:inline-block;">
						Total Samples Pending: <?php echo $total_pending; ?>
					</span>
				</div>
				<div
					style="font-weight:bold; background:#f5f5f5; padding:8px; border:1px solid #ddd; border-radius:4px; width:fit-content; margin: 0 auto; text-align:center;">
					<?php
					$pat_counting = mysqli_fetch_array(mysqli_query($link, "SELECT COUNT(`cashMemoNo`) AS `total_pat` FROM `uhid_and_opdid` WHERE `date`='$date'"));
					?>
					<span
						style="background:#A24EDB; color:#fff; padding:6px 14px; border-radius:4px; margin-right:8px; display:inline-block;">
						Total Patients: <?php echo $pat_counting['total_pat']; ?>
					</span>
				</div>

				<!-- BAR CHART -->
				<div style="background:#fff; border-radius:6px; padding:10px;">
					<canvas id="test-bar-chart" height="120"></canvas>
				</div>
			</div>

			<!-- RIGHT SIDE (40%) -->
			<div style="width: 45%;">
				<div id="test-pie-chart" style="width: 100%; min-height: 500px;"></div>

				<div id="digital-clock"
					style="text-align: center; padding: 20px; margin-top: 5%; padding-top: 40px; background: linear-gradient(135deg, #fefcea, #f1da36); color: #333; border-radius: 12px; font-family: 'Courier New', Courier, monospace; box-shadow: rgb(38, 57, 77) 0px 20px 30px -10px;">
					<div style="font-size: 36px; font-weight: bold;" id="clock-time">--:--:-- --</div>
					<div style="font-size: 18px; margin-top: 8px;" id="clock-date">--, -- -- ----</div>
				</div>

			</div>

		</div>



		<style>
			.parent {
				font-size: 12px;
				/* parent value */
			}

			.snip {
				margin: 0;
				padding: 2px;
				display: inline-block;
				width: 48%;
			}

			.child_snip {
				margin: 0;
				padding: 2px;
				display: inline-block;
				width: 100%;
				font-size: 12px;
				/* some value */
				height: 250px;
				max-height: 250px;
				overflow-y: scroll;
			}

			.table-report tr td,
			.table-report tr th {
				padding: 3px;
			}

			.table-report {
				background: #FFFFFF;
			}

			.normal {
				color: #111111;
			}

			.green {
				color: #146C16;
			}

			.red {
				color: #EB1B16;
			}

			@media only screen and (max-width: 600px) {
				.snip {
					display: block;
					width: 100%;
				}
			}

			@media only screen and (min-width: 600px) {
				.snip {
					display: block;
					width: 100%;
				}
			}

			@media only screen and (min-width: 768px) {
				.snip {
					display: block;
					width: 100%;
				}
			}

			@media only screen and (min-width: 990px) {
				.snip {
					display: inline-block;
					width: 48%;
				}
			}

			//====================================
		</style>

		<script src="../js/plotly-latest.js"></script>
		<script src="../js/chart.js"></script>


		<!--Pie Chart -->
		<script>
			const rawLabels = <?php echo json_encode($test_names); ?>;
			const rawValues = <?php echo json_encode($test_totals); ?>;

			const pieLabels = rawLabels.map((label, i) => {
				return `${label} - ${rawValues[i]}`;
			});

			const pieData = [{
				type: 'pie',
				labels: pieLabels,
				values: rawValues,
				textinfo: 'none',
				hoverinfo: 'label+value+percent',
				automargin: true,
				marker: {
					colors: [
						'#636EFA', '#EF553B', '#00CC96', '#AB63FA', '#FFA15A',
						'#19D3F3', '#FF6692', '#B6E880', '#FF97FF', '#FECB52',
						'#1f77b4', '#2ca02c', '#d62728', '#9467bd', '#8c564b',
						'#e377c2', '#7f7f7f', '#bcbd22', '#17becf', '#20b2aa'
					],
					line: {
						color: '#ffffff',
						width: 2
					}
				},
				pull: 0.05
			}];

			const pieLayout = {
				title: {
					text: 'Distribution of Samples Collected by Test (Total)',
					font: {
						size: 24,
						family: 'Segoe UI, Roboto, Arial, sans-serif',
						color: '#333'
					},
					x: 0.5,
					xanchor: 'center'
				},
				height: 600,
				margin: { t: 70, b: 40, l: 40, r: 150 },
				showlegend: true,
				legend: {
					bgcolor: 'rgba(255,255,255,0.8)',
					bordercolor: '#ddd',
					borderwidth: 1,
					orientation: 'v',
					x: 1.05,
					y: 1,
					font: {
						size: 13,
						color: '#333'
					},
					traceorder: 'normal',
					itemclick: 'toggleothers',
					itemdoubleclick: 'toggle'
				},
				paper_bgcolor: '#ffffff',
				plot_bgcolor: '#ffffff',
				annotations: [{
					text: 'Data as of <?php echo date("F Y"); ?>',
					showarrow: false,
					x: 0.5,
					y: -0.15,
					xref: 'paper',
					yref: 'paper',
					font: {
						size: 12,
						color: '#888'
					}
				}],
				transition: {
					duration: 600,
					easing: 'cubic-in-out'
				}
			};

			Plotly.newPlot('test-pie-chart', pieData, pieLayout, { responsive: true });
		</script>



		<!-- Bar Graph -->
		<script>
			const ctx = document.getElementById('test-bar-chart').getContext('2d');

			const chart = new Chart(ctx, {
				type: 'bar',
				data: {
					labels: ['Samples Received', 'Samples Tested', 'Samples Pending'],
					datasets: [{
						label: 'Total',
						data: [
							<?php echo $total_received; ?>,
							<?php echo $total_tested; ?>,
							<?php echo $total_pending; ?>
						],
						backgroundColor: ['#17A2B8', '#4CAF50', '#ED8936'],
						barThickness: 30
					}]
				},
				options: {
					indexAxis: 'y',
					responsive: true,
					plugins: {
						legend: {
							display: false
						},
						title: {
							display: true,
						}
					},
					scales: {
						x: {
							beginAtZero: true,
							title: {
								display: true,
								text: 'Number of Samples'
							}
						},
						y: {
							title: {
								display: false
							}
						}
					}
				}
			});

			$(".modebar").remove(); 
		</script>



		<script>
			$(document).ready(function () {
				//today_stats();
				$(".my_url").click(function () {
					alert();
				});
			});

			function updateClock() {
				const now = new Date();

				let hours = now.getHours();
				const minutes = now.getMinutes().toString().padStart(2, '0');
				const seconds = now.getSeconds().toString().padStart(2, '0');
				const ampm = hours >= 12 ? 'PM' : 'AM';

				hours = hours % 12;
				hours = hours ? hours : 12; // 0 becomes 12
				const formattedTime = `${hours.toString().padStart(2, '0')}:${minutes}:${seconds} ${ampm}`;

				const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
				const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
				const dayName = days[now.getDay()];
				const date = now.getDate();
				const month = months[now.getMonth()];
				const year = now.getFullYear();

				const formattedDate = `${dayName}, ${date} ${month} ${year}`;

				document.getElementById('clock-time').textContent = formattedTime;
				document.getElementById('clock-date').textContent = formattedDate;
			}

			setInterval(updateClock, 1000);
			updateClock();

			function call_me() {
				setTimeout(function () { today_stats(); }, 20000);
			}

			function cash_rep_print(id) {
				var user = btoa(id);
				url = "print_cash_report.php?user=" + user;
				wind = window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
			}

			function today_stats() {
				$.post("page_ajax.php",
					{
						dt: $("#dte").val(),
						usr: $("#user").text().trim(),
						type: "statistics",
					},
					function (data, status) {
						$("#stat").html(data);
						call_me();
					})
			}
			function show_tel_no() {
				url = "pages/telephone_no_list.php";
				wind = window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
			}
		</script>
		<?php
	}
}

echo "<input type='hidden' id='edit_info_cu' value='$p_info[edit_info]' class='span1'>";
echo "<input type='hidden' id='edit_payment_cu' value='$p_info[edit_payment]' class='span1'>";
echo "<input type='hidden' id='cancel_pat_cu' value='$p_info[cancel_pat]' class='span1'>";
echo "<input type='hidden' id='discount_permission_cu' value='$p_info[discount_permission]' class='span1'>";

include("../inc/footer.php");
?>