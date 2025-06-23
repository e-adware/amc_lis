<?php
session_start();
include("includes/connection.php");
$date = date("Y-m-d");
$company = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_master`"));
$name = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name`"));
if ($name["s_date"] != "0000-00-00") {
	if ($date >= $name["s_date"]) {
		include("root/pages/payment_received_check.php");
		exit();
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>
		<?php echo $company["name"]; ?> |
		<?php echo $name['name']; ?>
	</title>
	<meta charset="UTF-8" />
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" type="image/x-icon" href="images/penguin.ico">
	<link rel="stylesheet" href="css/boots.min.css">
	<link rel="stylesheet" href="css/all.min.css">
	<link rel="stylesheet" href="css/matrix-login.css">
	<link rel="stylesheet" href="css/demo.css">
	<style>
		body {
			margin: 0;
			padding: 0;
			background-color: #2a3033de;
			background-image: url("images/bg.jpg");
			background-repeat: no-repeat;
			background-size: cover;

		}

		.container-fluid {
			margin-top: 50px;
		}

		.login-content {
			background-color: #3e4a516e;
			border-radius: 10px;
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
			padding: 20px;
		}

		.company-logo,
		.client-logo {
			margin-bottom: 20px;
		}

		h4 {
			color: #00c3ff;
			font-size: 24px;
			margin-bottom: 20px;
		}

		p {
			color: #fff;
			font-size: 17px;
		}


		.demo-1 .main-title {
			text-transform: uppercase;
			font-size: 4.2em;
			letter-spacing: 0.1em;
		}



		@media only screen and (max-width : 768px) {
			.demo-1 .main-title {
				font-size: 3em;
			}
		}
	</style>
</head>



<div id="large-header" class="large-header">
	<canvas id="demo-canvas" style="position: absolute; z-index: -999;"></canvas>
	<!-- <h1 class="main-title">Connect <span class="thin">Three</span></h1> -->

	<body class="login" onkeypress="hid_div(event)" style="margin-top:100px; overflow: hidden;">
		<div class="container-fluid">
			<div id="large-header" class="row large-header">
				<!-- First Column: Company Logo and Details -->
				<div class="col-md-4 text-center d-none d-md-block">
					<div class="login-content" style="height: 345px;">
						<div class="company-logo">
							<img src="images/eadware.jpg" class="img-fluid" alt="Company Logo"
								style="width: 168px;">
						</div>
						<h4>
							<?php echo $company["name"]; ?>
						</h4>
						<p>
							<?php echo $company["address"]; ?>
						</p>
					</div>
				</div>

				<!-- Second Column: Login Form -->
				<div class="col-md-4">
					<div id="loginbox" class="mainbox login-content" style="width: 80%;height: 345px;">
						<div class="control-group text-center">
							<h3 style="color: #ff8f00;">
								<?php echo $company["name"]; ?>
							</h3>
						</div>
						<div class="control-group">
							<div class="controls">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text"><img src="img/icons/16/client.png"
												alt="Client Icon"></span>

									</div>
									<input class="form-control" type="text" placeholder="Username" autocomplete="off"
										onkeyup="load_user(this.value, event)" name="uname" id="uname" autofocus />
									<input type="hidden" id="user_id" name="user_id" />
								</div>
							</div>
							<div id="update_p_sim"
								style="position:absolute; width: 82%; background: #fff;margin-left: 4.5%;margin-top: -3px; z-index:9999;">
							</div>
						</div>
						<div class="control-group">
							<div class="controls">
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text"><img src="img/icons/16/lock.png"></img></span>
									</div>
									<input class="form-control" type="password" placeholder="Password" name="pword"
										id="pword" onkeyup="chng_focus(this.id,event)" />
								</div>
							</div>
						</div>
						<center><span id="error_mgs"></span></center>

						<div class="form-actions text-center">
							<button class="btn btn-success" type="button" onclick="submit_form()"
								id="subm">Login</button>
						</div>
					</div>
					<p class="text-center" style="color: #ff8f00;font-family: initial;margin-top:20%;"><span>Coded &amp;
							designed with &hearts; by <span><a href="http://www.e-adware.com" target="_blank"
									style="color: #ff8f00;">e-adware</a></span></span></p>

				</div>


				<!-- Third Column: Client Logo and Details -->
				<div class="col-md-4 text-center d-none d-md-block">
					<div class="login-content" style="height: 345px;">
						<div class="client-logo">
							<img src="images/<?php echo $name["client_logo"]; ?>" class="img-fluid" style="width: 200px;" alt="Client Logo">
						</div>
						<h4>
							<?php echo $name['name']; ?>
						</h4>
						<p>
							<?php echo $name['city'] . ", " . $name['state']; ?>
						</p>
					</div>
				</div>
			</div>
		</div>
</div>




<script src="js/jquery.min.js"></script>
<script src="js/matrix.login.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/boots.min.js"></script>
<script src="js/EasePack.min.js"></script>
<script src="js/rAF.js"></script>
<script src="js/TweenLite.min.js"></script>
</body>

</html>
<script>
	$(document).keydown(function (event) {
		if (event.ctrlKey && event.keyCode == 53) {
			destroy_session();
		}
	});
	function hid_div(e) {
		var unicode = e.keyCode ? e.keyCode : e.charCode;
		if (unicode == 46) {
			if (e.shiftKey == 1) {
				window.location.href = "clear_db.php";
			}
		}
	}

	var sel_pser = 1;
	var sel_divser = 0;
	function load_user(val, e) {
		$("#error_mgs").text("");
		var unicode = e.keyCode ? e.keyCode : e.charCode;
		if (unicode == 13) {
			var user = document.getElementById("log_det" + sel_pser).innerHTML;
			get_user_detail(user);
			sel_pser = 1;
			sel_divser = 0;
		}
		else if (unicode == 40) {
			var chk = sel_pser + 1;
			var cc = document.getElementById("upd_psim" + chk).innerHTML;
			if (cc) {
				sel_pser = sel_pser + 1;
				$("#upd_psim" + sel_pser).css({ 'color': '#419641', 'font-weight': 'bold', 'transition': 'all .2s' });
				var sel_pser1 = sel_pser - 1;
				$("#upd_psim" + sel_pser1).css({ 'color': 'black', 'font-weight': 'normal', 'transition': 'all .2s' });
				var z2 = sel_pser % 1;
				if (z2 == 0) {
					$("#update_p_sim").scrollTop(sel_divser)
					sel_divser = sel_divser + 38;
				}
			}
		}
		else if (unicode == 38) {
			var chk = sel_pser - 1;
			var cc = document.getElementById("upd_psim" + chk).innerHTML;
			if (cc) {
				sel_pser = sel_pser - 1;
				$("#upd_psim" + sel_pser).css({ 'color': '#419641', 'font-weight': 'bold', 'transition': 'all .2s' });
				var sel_pser1 = sel_pser + 1;
				$("#upd_psim" + sel_pser1).css({ 'color': 'black', 'font-weight': 'normal', 'transition': 'all .2s' });
				var z2 = sel_pser % 1;
				if (z2 == 0) {
					sel_divser = sel_divser - 38;
					$("#update_p_sim").scrollTop(sel_divser)

				}
			}
		}
		else {
			if (val.length > 2) {
				$.post("load_login_user.php",
					{
						val: val,
					},
					function (data, status) {
						$("#update_p_sim").html(data);
						$("#update_p_sim").slideDown(500);
					})
			}
			else {
				//$("#update_p_sim").html("");
				$("#update_p_sim").slideUp(500);
			}
		}
	}


	function get_user_detail(user) {
		var user = user.split("@");
		$("#uname").val(user[1]);
		$("#user_id").val(user[2]);
		$("#update_p_sim").slideUp(500);
		$("#pword").focus();
	}
	function chng_focus(id, e) {
		$("#error_mgs").text("");
		var unicode = e.keyCode ? e.keyCode : e.charCode;
		if (unicode == 13) {
			submit_form();
		}
	}
	function submit_form() {
		$.post("login_process.php",
			{
				user_id: $("#user_id").val(),
				uname: $("#uname").val(),
				pword: $("#pword").val(),
			},
			function (data, status) {
				// alert(data);
				if (data == "1") {
					window.location.href = "root/";
				} else if (data == "2") {
					$("#error_mgs").html("Username or password is invalid");
					$("#pword").val("").focus();
				} else if (data == "3") {
					$("#error_mgs").html("Someone has already logged-in in another tab. To log in anyway press <b>Ctrl+5</b>");
					$("#pword").val("").focus();
				}
				else if (data == "5") {
					//window.location.href="root/processing.php?param=15";
					window.location.href = "root/";
				} else if (data == "404") {
					$("#error_mgs").html("<b>User account doesn't have permission to access</b>");
					$("#pword").val("").focus();
				} else if (data == "4") {
					$("#subm").hide();
					$("#error_mgs").html("You either are already logged-in in another device or not properly log-out. To log-out from all devices click the button<br><br><button class='btn btn-warning' onClick='properly_logout()'>Logout</button>");
				}
			})
	}
	function properly_logout() {
		$("#error_mgs").html('');
		$("#subm").show();

		$.post("all_device_logout.php",
			{
				type: "all_device_logout",
				emp_id: $("#user_id").val(),
			},
			function (data, status) {
				if (data == '1') {
					$("#error_mgs").html("Successfully logged-out from all devices. Now log-in");
					$("#pword").val("").focus();
				} else {
					$("#error_mgs").html("Error");
				}
			})
	}
	function destroy_session() {
		$.post("all_device_logout.php",
			{
				type: "destroy_session",
			},
			function (data, status) {

			})
	}





	(function () {

		var width, height, largeHeader, canvas, ctx, points, target, animateHeader = true;

		// Main
		initHeader();
		initAnimation();
		addListeners();

		function initHeader() {
			width = window.innerWidth;
			height = window.innerHeight;
			target = { x: width / 2, y: height / 2 };

			largeHeader = document.getElementById('large-header');
			largeHeader.style.height = height + 'px';

			canvas = document.getElementById('demo-canvas');
			canvas.width = width;
			canvas.height = height;
			ctx = canvas.getContext('2d');

			// create points
			points = [];
			for (var x = 0; x < width; x = x + width / 20) {
				for (var y = 0; y < height; y = y + height / 20) {
					var px = x + Math.random() * width / 20;
					var py = y + Math.random() * height / 20;
					var p = { x: px, originX: px, y: py, originY: py };
					points.push(p);
				}
			}

			// for each point find the 5 closest points
			for (var i = 0; i < points.length; i++) {
				var closest = [];
				var p1 = points[i];
				for (var j = 0; j < points.length; j++) {
					var p2 = points[j]
					if (!(p1 == p2)) {
						var placed = false;
						for (var k = 0; k < 5; k++) {
							if (!placed) {
								if (closest[k] == undefined) {
									closest[k] = p2;
									placed = true;
								}
							}
						}

						for (var k = 0; k < 5; k++) {
							if (!placed) {
								if (getDistance(p1, p2) < getDistance(p1, closest[k])) {
									closest[k] = p2;
									placed = true;
								}
							}
						}
					}
				}
				p1.closest = closest;
			}

			// assign a circle to each point
			for (var i in points) {
				var c = new Circle(points[i], 2 + Math.random() * 2, 'rgba(255,255,255,0.3)');
				points[i].circle = c;
			}
		}

		// Event handling
		function addListeners() {
			if (!('ontouchstart' in window)) {
				window.addEventListener('mousemove', mouseMove);
			}
			window.addEventListener('scroll', scrollCheck);
			window.addEventListener('resize', resize);
		}

		function mouseMove(e) {
			var posx = posy = 0;
			if (e.pageX || e.pageY) {
				posx = e.pageX;
				posy = e.pageY;
			}
			else if (e.clientX || e.clientY) {
				posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
				posy = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
			}
			target.x = posx;
			target.y = posy;
		}

		function scrollCheck() {
			if (document.body.scrollTop > height) animateHeader = false;
			else animateHeader = true;
		}

		function resize() {
			width = window.innerWidth;
			height = window.innerHeight;
			largeHeader.style.height = height + 'px';
			canvas.width = width;
			canvas.height = height;
		}

		// animation
		function initAnimation() {
			animate();
			for (var i in points) {
				shiftPoint(points[i]);
			}
		}

		function animate() {
			if (animateHeader) {
				ctx.clearRect(0, 0, width, height);
				for (var i in points) {
					// detect points in range
					if (Math.abs(getDistance(target, points[i])) < 4000) {
						points[i].active = 0.3;
						points[i].circle.active = 0.6;
					} else if (Math.abs(getDistance(target, points[i])) < 20000) {
						points[i].active = 0.1;
						points[i].circle.active = 0.3;
					} else if (Math.abs(getDistance(target, points[i])) < 40000) {
						points[i].active = 0.02;
						points[i].circle.active = 0.1;
					} else {
						points[i].active = 0;
						points[i].circle.active = 0;
					}

					drawLines(points[i]);
					points[i].circle.draw();
				}
			}
			requestAnimationFrame(animate);
		}

		function shiftPoint(p) {
			TweenLite.to(p, 1 + 1 * Math.random(), {
				x: p.originX - 50 + Math.random() * 100,
				y: p.originY - 50 + Math.random() * 100, ease: Circ.easeInOut,
				onComplete: function () {
					shiftPoint(p);
				}
			});
		}

		// Canvas manipulation
		function drawLines(p) {
			if (!p.active) return;
			for (var i in p.closest) {
				ctx.beginPath();
				ctx.moveTo(p.x, p.y);
				ctx.lineTo(p.closest[i].x, p.closest[i].y);
				ctx.strokeStyle = 'rgba(156,217,249,' + p.active + ')';
				ctx.stroke();
			}
		}

		function Circle(pos, rad, color) {
			var _this = this;

			// constructor
			(function () {
				_this.pos = pos || null;
				_this.radius = rad || null;
				_this.color = color || null;
			})();

			this.draw = function () {
				if (!_this.active) return;
				ctx.beginPath();
				ctx.arc(_this.pos.x, _this.pos.y, _this.radius, 0, 2 * Math.PI, false);
				ctx.fillStyle = 'rgba(156,217,249,' + _this.active + ')';
				ctx.fill();
			};
		}

		// Util
		function getDistance(p1, p2) {
			return Math.pow(p1.x - p2.x, 2) + Math.pow(p1.y - p2.y, 2);
		}

	})();
</script>
<style>
	#upd_psim1 {
		color: #419641;
		font-weight: bold;
	}

	#error_mgs {
		color: #FFF;
		//margin-left: 25%;
	}
</style>
