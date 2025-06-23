<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center" style="display:none;">
		<tr>
			<td>
				<center>
					<b>From</b>
					<input class="form-control datepicker span2" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" >
					<b>To</b>
					<input class="form-control datepicker span2" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" >
					
					<br>
					<button class="btn btn-success" onClick="view_all('graph','0')">View </button>
				</center>
			</td>
		</tr>
	</table>
	<div id="load_all" class="">
		<div class="row" style="display:none;">
			<div class="child_snip span5">
				<div class="child">
					<button class="btn btn-primary" disabled style="width:100%;">
						IPD PATIENT COUNT
						<span class="btn btn-default" onclick="ipd_pat_number('1')" style="float:right;"><i class="icon-refresh"></i></span>
					</button>
				</div>
				<div id="ipd_pat_number_graph"></div>
				<div id="loader1x" style="margin-top:-60%;"></div>
			</div>
			<!--<div class="span0"></div>-->
			<div class="child_snip span6">
				<div class="child">
					<button class="btn btn-primary" disabled style="width:100%;">
						IPD PATIENT DETAILS
						<span class="btn btn-default" onclick="ipd_pat_details('2')" style="float:right;"><i class="icon-refresh"></i></span>
					</button>
				</div>
				<div id="ipd_pat_details_graph"></div>
			</div>
		</div>
		
		<div class="row" style="display:none;">
			<div class="child_snip2 span5">
				<div class="child">
					<button class="btn btn-primary" disabled style="width:100%;">
						OPD PATIENT DETAILS
						<span class="btn btn-default" onclick="opd_pat_details('3')" style="float:right;"><i class="icon-refresh"></i></span>
					</button>
				</div>
				<div id="loader2x" style="margin-top:-25%;"></div>
				<div id="opd_pat_details_graph"></div>
			</div>
			<!--<div class="span0"></div>-->
			<div class="child_snip2 span6">
				<div class="child">
					<button class="btn btn-primary" disabled style="width:100%;">
						BED OCCUPANCY BY WARDS
						<span class="btn btn-default" onclick="bed_occupancy_ward('4')" style="float:right;"><i class="icon-refresh"></i></span>
					</button>
				</div>
				<div id="bed_occupancy_by_wards_graph"></div>
			</div>
		</div>
		
		<div class="row" style="display:none;">
			<div class="child_snip2 span5">
				<div class="child">
					<button class="btn btn-primary" disabled style="width:100%;">
						MONTHLY OPD SALES
						<span class="btn btn-default" onclick="monthly_opd_sale('5')" style="float:right;"><i class="icon-refresh"></i></span>
					</button>
				</div>
				<div id="loader3x" style="margin-top:10%;"></div>
				<div id="monthly_opd_sale_graph"></div>
			</div>
			<!--<div class="span0"></div>-->
			<div class="child_snip2 span6">
				<div class="child">
					<button class="btn btn-primary" disabled style="width:100%;">
						MONTHLY IPD SALES
						<span class="btn btn-default" onclick="monthly_ipd_sale('6')" style="float:right;"><i class="icon-refresh"></i></span>
					</button>
				</div>
				<div id="monthly_ipd_sale_graph"></div>
			</div>
		</div>
		
		<div class="row">
			<div class="child_snip2 span11">
				<div class="child">
					<button class="btn btn-primary" disabled style="width:100%;">
						DAILY TOTAL SALES
						<span class="btn btn-default" onclick="daily_total_sale('10')" style="float:right;"><i class="icon-refresh"></i></span>
					</button>
				</div>
				<div id="loader10" style="margin-top:-60%;"></div>
				<div id="daily_total_sale_graph"></div>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="child_snip2 span11">
				<div class="child">
					<button class="btn btn-primary" disabled style="width:100%;">
						WEEKLY TOTAL SALES
						<span class="btn btn-default" onclick="weekly_total_sale('9')" style="float:right;"><i class="icon-refresh"></i></span>
					</button>
				</div>
				<div id="loader9" style="margin-top:-25%;"></div>
				<div id="weekly_total_sale_graph"></div>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="child_snip2 span11">
				<div class="child">
					<button class="btn btn-primary" disabled style="width:100%;">
						MONTHLY TOTAL SALES
						<span class="btn btn-default" onclick="monthly_total_sale('7')" style="float:right;"><i class="icon-refresh"></i></span>
					</button>
				</div>
				<div id="loader7" style="margin-top:8%;"></div>
				<div id="monthly_total_sale_graph"></div>
			</div>
		</div>
		<br>
		<div class="row">
			<!--<div class="span0"></div>-->
			<div class="child_snip2 span11">
				<div class="child">
					<button class="btn btn-primary" disabled style="width:100%;">
						MONTHLY TOTAL SALES
						<span class="btn btn-default" onclick="monthly_total_collection('8')" style="float:right;"><i class="icon-refresh"></i></span>
					</button>
				</div>
				<div id="loader8" style="margin-top:40%;"></div>
				<div id="monthly_total_collection_graph"></div>
			</div>
		</div>
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
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
			//defaultDate:'2000-01-01',
		});
		view_all();
	});
	function view_all()
	{
		daily_total_sale('10');
		setTimeout(function(){
			weekly_total_sale('9');
		},1000);
		setTimeout(function(){
			monthly_total_sale('7');
		},2000);
		setTimeout(function(){
			monthly_total_collection('8');
		},3000);
		//~ ipd_pat_number('1');
		
		//~ setTimeout(function(){
			//~ ipd_pat_details('2');
		//~ },1000);
		//~ setTimeout(function(){
			//~ opd_pat_details('3');
		//~ },2000);
		//~ setTimeout(function(){
			//~ bed_occupancy_ward('4');
		//~ },3000);
		//~ setTimeout(function(){
			//~ monthly_opd_sale('5');
		//~ },4000);
		//~ setTimeout(function(){
			//~ monthly_ipd_sale('6');
		//~ },5000);
		//~ setTimeout(function(){
			//~ monthly_total_sale('7');
		//~ },9000);
		//~ setTimeout(function(){
			//~ monthly_total_collection('8');
		//~ },7000);
		
	}
	function ipd_pat_number(typ)
	{
		$("#loader1").show();
		$.post("pages/ceo_dashboard_graph_data.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
		},
		function(data,status)
		{
			$("#loader1").hide();
			var res=data.split("@");
			ipd_pat_number_graph(res[0],res[1],res[2],res[3]);
		})
	}
	function ipd_pat_details(typ)
	{
		$("#loader1").show();
		$.post("pages/ceo_dashboard_graph_data.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
		},
		function(data,status)
		{
			//alert(data);
			$("#loader1").hide();
			var res=data.split("@");
			ipd_pat_details_graph(res[0],res[1],res[2],res[3],res[4]);
		})
	}
	function opd_pat_details(typ)
	{
		$("#loader2").show();
		$.post("pages/ceo_dashboard_graph_data.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
		},
		function(data,status)
		{
			//alert(data);
			$("#loader2").hide();
			var res=data.split("@");
			opd_pat_details_graph(res[0],res[1],res[2],res[3],res[4]);
		})
	}
	function bed_occupancy_ward(typ)
	{
		$("#loader2").show();
		$.post("pages/ceo_dashboard_graph_data.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
		},
		function(data,status)
		{
			//alert(data);
			$("#loader2").hide();
			var res=data.split("@");
			bed_occupancy_by_wards_graph(res[0],res[1],res[2],res[3],res[4]);
			//~ bed_occupancy_by_wards_graph(data);
		})
	}
	function monthly_opd_sale(typ)
	{
		$("#loader3").show();
		$.post("pages/ceo_dashboard_graph_data.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
		},
		function(data,status)
		{
			//alert(data);
			$("#loader3").hide();
			var res=data.split("@#@");
			monthly_opd_sale_graph(res[0],res[1],res[2],res[3],res[4],res[5],res[6],res[7],res[8],res[9],res[10],res[11]);
		})
	}
	function monthly_ipd_sale(typ)
	{
		$("#loader3").show();
		$.post("pages/ceo_dashboard_graph_data.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
		},
		function(data,status)
		{
			//alert(data);
			$("#loader3").hide();
			var res=data.split("@#@");
			monthly_ipd_sale_graph(res[0],res[1],res[2],res[3],res[4],res[5],res[6],res[7],res[8],res[9],res[10],res[11]);
		})
	}
	function daily_total_sale(typ)
	{
		$("#loader10").show();
		$.post("pages/ceo_dashboard_graph_data.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
		},
		function(data,status)
		{
			//alert(data);
			$("#loader10").hide();
			var res=data.split("@#@");
			daily_total_sale_graph(res[0],res[1],res[2],res[3],res[4],res[5],res[6],res[7],res[8],res[9],res[10],res[11]);
		})
	}
	function weekly_total_sale(typ)
	{
		$("#loader9").show();
		$.post("pages/ceo_dashboard_graph_data.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
		},
		function(data,status)
		{
			//alert(data);
			$("#loader9").hide();
			var res=data.split("@#@");
			weekly_total_sale_graph(res[0],res[1],res[2],res[3],res[4],res[5],res[6],res[7],res[8],res[9],res[10],res[11]);
		})
	}
	function monthly_total_sale(typ)
	{
		$("#loader7").show();
		$.post("pages/ceo_dashboard_graph_data.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
		},
		function(data,status)
		{
			//alert(data);
			$("#loader7").hide();
			var res=data.split("@#@");
			monthly_total_sale_graph(res[0],res[1],res[2],res[3],res[4],res[5],res[6],res[7],res[8],res[9],res[10],res[11]);
		})
	}
	function monthly_total_collection(typ)
	{
		$("#loader8").show();
		$.post("pages/ceo_dashboard_graph_data.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
		},
		function(data,status)
		{
			//alert(data);
			$("#loader8").hide();
			var res=data.split("@#@");
			monthly_total_collection_graph(res[0],res[1],res[2],res[3]);
		})
	}
	
</script>
<style>
.ScrollStyle
{
    max-height: 400px;
    overflow-y: scroll;
}

.parent
{
	font-size: 12px;  /* parent value */
}

.snip
{
	margin:0;
	padding:2px;
	display: inline-block;
}

.child_snip
{
	//margin:0;
	padding:0px;
	display: inline-block;
	height:300px;
	//max-height:250px;
	//overflow-y:scroll;
	border: 5px solid #000;
	box-shadow: 5px 5px;
}

.child_snip2
{
	//margin:0;
	padding:0px;
	display: inline-block;
	height:400px;
	//max-height:250px;
	//overflow-y:scroll;
	border: 5px solid #000;
	box-shadow: 5px 5px;
}
</style>

<style>
#ipd_pat_number_graph, #ipd_pat_details_graph
{
  width: 100%;
  height: 250px;
}
#opd_pat_details_graph, #bed_occupancy_by_wards_graph
{
  width: 100%;
  height: 350px;
}
#monthly_opd_sale_graph, #monthly_ipd_sale_graph, #monthly_total_sale_graph, #weekly_total_sale_graph, #daily_total_sale_graph
{
  width: 100%;
  height: 350px;
}
#monthly_total_collection_graph
{
  width: 100%;
  height: 350px;
}

</style>
<!-- Resources -->
<script src="../jss/graph/core.js"></script>
<script src="../jss/graph/charts.js"></script>
<script src="../jss/graph/material.js"></script>
<script src="../jss/graph/animated.js"></script>

<!-- Chart code -->
<script>
function ipd_pat_number_graph(inpatient_number,ipd_admission_number,ipd_discharge_number,ipd_total_number)
{
	var full_no=parseInt(ipd_total_number);
	// Themes begin
	am4core.useTheme(am4themes_animated);
	// Themes end

	// Create chart instance
	var chart = am4core.create("ipd_pat_number_graph", am4charts.RadarChart);

	// Add data
	chart.data = [{
	  "hover_text": "Discharge ",
	  "category": "Discharge "+ipd_discharge_number,
	  "value": ipd_discharge_number,
	  "full": full_no
	}, {
	  "hover_text": "Admission ",
	  "category": "Admission "+ipd_admission_number,
	  "value": ipd_admission_number,
	  "full": full_no
	}, {
	  "hover_text": "Inpatient ",
	  "category": "Inpatient "+inpatient_number,
	  "value": inpatient_number,
	  "full": full_no
	}];

	// Make chart not full circle
	chart.startAngle = -90;
	chart.endAngle = 180;
	chart.innerRadius = am4core.percent(10);

	// Set number format
	chart.numberFormatter.numberFormat = "#.#";

	// Create axes
	var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
	categoryAxis.dataFields.category = "category";
	categoryAxis.renderer.grid.template.location = 0;
	categoryAxis.renderer.grid.template.strokeOpacity = 0;
	categoryAxis.renderer.labels.template.horizontalCenter = "right";
	categoryAxis.renderer.labels.template.fontWeight = 500;
	categoryAxis.renderer.labels.template.adapter.add("fill", function(fill, target) {
	  return (target.dataItem.index >= 0) ? chart.colors.getIndex(target.dataItem.index) : fill;
	});
	categoryAxis.renderer.minGridDistance = 5;

	var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());
	valueAxis.renderer.grid.template.strokeOpacity = 0;
	valueAxis.min = 0;
	valueAxis.max = full_no; // Dynamic
	valueAxis.strictMinMax = true;

	// Create series
	var series1 = chart.series.push(new am4charts.RadarColumnSeries());
	series1.dataFields.valueX = "full";
	series1.dataFields.categoryY = "category";
	series1.clustered = false;
	series1.columns.template.fill = new am4core.InterfaceColorSet().getFor("alternativeBackground");
	series1.columns.template.fillOpacity = 0.18;
	series1.columns.template.cornerRadiusTopLeft = 5;
	series1.columns.template.strokeWidth = 0;
	series1.columns.template.radarColumn.cornerRadius = 0;

	var series2 = chart.series.push(new am4charts.RadarColumnSeries());
	series2.dataFields.valueX = "value";
	series2.dataFields.categoryY = "category";
	series2.clustered = false;
	series2.columns.template.strokeWidth = 0;
	series2.columns.template.tooltipText = "{hover_text}: [bold]{value}[/]";
	series2.columns.template.radarColumn.cornerRadius = 5;

	series2.columns.template.adapter.add("fill", function(fill, target) {
	  return chart.colors.getIndex(target.dataItem.index);
	});

	// Add cursor
	//chart.cursor = new am4charts.RadarCursor();
}

function ipd_pat_details_graph(ipd_refund_amount,ipd_discount_amount,ipd_bill_amount,ipd_paid_amount,ipd_balance_amount)
{
	// Themes begin
	am4core.useTheme(am4themes_animated);
	// Themes end

	// Create chart instance
	var chart = am4core.create("ipd_pat_details_graph", am4charts.PieChart);

	// Add data
	chart.data = [
	{
	  "country": "Receive Amount",
	  "litres": ipd_paid_amount
	}, {
	  "country": "Balance",
	  "litres": ipd_balance_amount
	}, {
	  "country": "Discount",
	  "litres": ipd_discount_amount
	}, {
	  "country": "Refund",
	  "litres": ipd_refund_amount
	}
	];

	// Add and configure Series
	var pieSeries = chart.series.push(new am4charts.PieSeries());
	pieSeries.dataFields.value = "litres";
	pieSeries.dataFields.category = "country";
	pieSeries.slices.template.stroke = am4core.color("#fff");
	pieSeries.slices.template.strokeWidth = 2;
	pieSeries.slices.template.strokeOpacity = 1;

	// This creates initial animation
	pieSeries.hiddenState.properties.opacity = 1;
	pieSeries.hiddenState.properties.endAngle = -90;
	pieSeries.hiddenState.properties.startAngle = -90;
}

function opd_pat_details_graph(opd_refund_amount,opd_discount_amount,opd_paid_amount,opd_balance_amount,opd_bill_amount)
{
	// Themes begin
	am4core.useTheme(am4themes_animated);
	// Themes end

	// Create chart instance
	var chart = am4core.create("opd_pat_details_graph", am4charts.XYChart3D);

	// Add data
	chart.data = [{
	  "country": "REFUND",
	  "visits": opd_refund_amount
	}, {
	  "country": "DISCOUNT",
	  "visits": opd_discount_amount
	}, {
	  "country": "RECEIVE",
	  "visits": opd_paid_amount
	}, {
	  "country": "BALANCE",
	  "visits": opd_balance_amount
	}, {
	  "country": "BILL",
	  "visits": opd_bill_amount
	}];

	// Create axes
	let categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
	categoryAxis.dataFields.category = "country";
	categoryAxis.renderer.labels.template.rotation = 270;
	categoryAxis.renderer.labels.template.hideOversized = false;
	categoryAxis.renderer.minGridDistance = 20;
	categoryAxis.renderer.labels.template.horizontalCenter = "right";
	categoryAxis.renderer.labels.template.verticalCenter = "middle";
	categoryAxis.tooltip.label.rotation = 270;
	categoryAxis.tooltip.label.horizontalCenter = "right";
	categoryAxis.tooltip.label.verticalCenter = "middle";

	let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
	valueAxis.title.text = "Amount";
	valueAxis.title.fontWeight = "bold";

	// Create series
	var series = chart.series.push(new am4charts.ColumnSeries3D());
	series.dataFields.valueY = "visits";
	series.dataFields.categoryX = "country";
	series.name = "Visits";
	series.tooltipText = "{categoryX}: [bold]{valueY}[/]";
	series.columns.template.fillOpacity = .8;

	var columnTemplate = series.columns.template;
	columnTemplate.strokeWidth = 2;
	columnTemplate.strokeOpacity = 1;
	columnTemplate.stroke = am4core.color("#FFFFFF");

	columnTemplate.adapter.add("fill", function(fill, target) {
	  return chart.colors.getIndex(target.dataItem.index);
	})

	columnTemplate.adapter.add("stroke", function(stroke, target) {
	  return chart.colors.getIndex(target.dataItem.index);
	})

	chart.cursor = new am4charts.XYCursor();
	chart.cursor.lineX.strokeOpacity = 0;
	chart.cursor.lineY.strokeOpacity = 0;
}
function bed_occupancy_by_wards_graph(daycare_pat_num,female_ward_pat_num,male_ward_pat_num,special_cabin_pat_num,icu_pat_num)
{
	
	//~ var res=data.split("@#@");
	//~ var str="";
	
	//~ res.forEach(function(val) {
		//~ if(val)
		//~ {
			//~ var itm=val.split("##");
			//~ var num=parseInt(itm[1]);
			//~ str+='{"country":"'+itm[0]+'", "visits":'+num+'},';
		//~ }
	//~ });
	//~ str='['+str+']';
	
	
	// Themes begin
	am4core.useTheme(am4themes_animated);
	// Themes end

	// Create chart instance
	var chart = am4core.create("bed_occupancy_by_wards_graph", am4charts.XYChart3D);

	// Add data
	chart.data = [{
	  "country": "DAY CARE",
	  "visits": daycare_pat_num
	}, {
	  "country": "FEMALE WARD",
	  "visits": female_ward_pat_num
	}, {
	  "country": "MALE WARD",
	  "visits": male_ward_pat_num
	}, {
	  "country": "SPECIAL CABIN",
	  "visits": special_cabin_pat_num
	}, {
	  "country": "ICU",
	  "visits": icu_pat_num
	}];
	

	// Create axes
	let categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
	categoryAxis.dataFields.category = "country";
	categoryAxis.renderer.labels.template.rotation = 270;
	categoryAxis.renderer.labels.template.hideOversized = false;
	categoryAxis.renderer.minGridDistance = 20;
	categoryAxis.renderer.labels.template.horizontalCenter = "right";
	categoryAxis.renderer.labels.template.verticalCenter = "middle";
	categoryAxis.tooltip.label.rotation = 270;
	categoryAxis.tooltip.label.horizontalCenter = "right";
	categoryAxis.tooltip.label.verticalCenter = "middle";

	let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
	valueAxis.title.text = "PATIENT NO";
	valueAxis.title.fontWeight = "bold";

	// Create series
	var series = chart.series.push(new am4charts.ColumnSeries3D());
	series.dataFields.valueY = "visits";
	series.dataFields.categoryX = "country";
	series.name = "Visits";
	series.tooltipText = "{categoryX}: [bold]{valueY}[/]";
	series.columns.template.fillOpacity = .8;

	var columnTemplate = series.columns.template;
	columnTemplate.strokeWidth = 2;
	columnTemplate.strokeOpacity = 1;
	columnTemplate.stroke = am4core.color("#FFFFFF");

	columnTemplate.adapter.add("fill", function(fill, target) {
	  return chart.colors.getIndex(target.dataItem.index);
	})

	columnTemplate.adapter.add("stroke", function(stroke, target) {
	  return chart.colors.getIndex(target.dataItem.index);
	})

	chart.cursor = new am4charts.XYCursor();
	chart.cursor.lineX.strokeOpacity = 0;
	chart.cursor.lineY.strokeOpacity = 0;
}
function monthly_opd_sale_graph(m_1_val,m_2_val,m_3_val,m_4_val,m_5_val,m_6_val,m_7_val,m_8_val,m_9_val,m_10_val,m_11_val,m_12_val)
{
	m_1_val=m_1_val.split("@");
	var m_1=m_1_val[0];
	var v_1=parseInt(m_1_val[1]);
	
	m_2_val=m_2_val.split("@");
	var m_2=m_2_val[0];
	var v_2=parseInt(m_2_val[1]);
	
	m_3_val=m_3_val.split("@");
	var m_3=m_3_val[0];
	var v_3=parseInt(m_3_val[1]);
	
	m_4_val=m_4_val.split("@");
	var m_4=m_4_val[0];
	var v_4=parseInt(m_4_val[1]);
	
	m_5_val=m_5_val.split("@");
	var m_5=m_5_val[0];
	var v_5=parseInt(m_5_val[1]);
	
	m_6_val=m_6_val.split("@");
	var m_6=m_6_val[0];
	var v_6=parseInt(m_6_val[1]);
	
	m_7_val=m_7_val.split("@");
	var m_7=m_7_val[0];
	var v_7=parseInt(m_7_val[1]);
	
	m_8_val=m_8_val.split("@");
	var m_8=m_8_val[0];
	var v_8=parseInt(m_8_val[1]);
	
	m_9_val=m_9_val.split("@");
	var m_9=m_9_val[0];
	var v_9=parseInt(m_9_val[1]);
	
	m_10_val=m_10_val.split("@");
	var m_10=m_10_val[0];
	var v_10=parseInt(m_10_val[1]);
	
	m_11_val=m_11_val.split("@");
	var m_11=m_11_val[0];
	var v_11=parseInt(m_11_val[1]);
	
	m_12_val=m_12_val.split("@");
	var m_12=m_12_val[0];
	var v_12=parseInt(m_12_val[1]);
	
	// Themes begin
	am4core.useTheme(am4themes_animated);
	// Themes end

	// Create chart instance
	var chart = am4core.create("monthly_opd_sale_graph", am4charts.XYChart3D);

	// Add data
	chart.data = [{
	  "country": m_1,
	  "visits": v_1
	},{
	  "country": m_2,
	  "visits": v_2
	}, {
	  "country": m_3,
	  "visits": v_3
	}, {
	  "country": m_4,
	  "visits": v_4
	},{
	  "country": m_5,
	  "visits": v_5
	}, {
	  "country": m_6,
	  "visits": v_6
	}, {
	  "country": m_7,
	  "visits": v_7
	},{
	  "country": m_8,
	  "visits": v_8
	}, {
	  "country": m_9,
	  "visits": v_9
	}, {
	  "country": m_10,
	  "visits": v_10
	}, {
	  "country": m_11,
	  "visits": v_11
	}, {
	  "country": m_12,
	  "visits": v_12
	}
	];

	// Create axes
	let categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
	categoryAxis.dataFields.category = "country";
	categoryAxis.renderer.labels.template.rotation = 270;
	categoryAxis.renderer.labels.template.hideOversized = false;
	categoryAxis.renderer.minGridDistance = 20;
	categoryAxis.renderer.labels.template.horizontalCenter = "right";
	categoryAxis.renderer.labels.template.verticalCenter = "middle";
	categoryAxis.tooltip.label.rotation = 270;
	categoryAxis.tooltip.label.horizontalCenter = "right";
	categoryAxis.tooltip.label.verticalCenter = "middle";

	let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
	valueAxis.title.text = "Monthly OPD Sales";
	valueAxis.title.fontWeight = "bold";

	// Create series
	var series = chart.series.push(new am4charts.ColumnSeries3D());
	series.dataFields.valueY = "visits";
	series.dataFields.categoryX = "country";
	series.name = "Visits";
	series.tooltipText = "{categoryX}: [bold]{valueY}[/]";
	series.columns.template.fillOpacity = .8;

	var columnTemplate = series.columns.template;
	columnTemplate.strokeWidth = 2;
	columnTemplate.strokeOpacity = 1;
	columnTemplate.stroke = am4core.color("#FFFFFF");

	columnTemplate.adapter.add("fill", function(fill, target) {
	  return chart.colors.getIndex(target.dataItem.index);
	})

	columnTemplate.adapter.add("stroke", function(stroke, target) {
	  return chart.colors.getIndex(target.dataItem.index);
	})

	chart.cursor = new am4charts.XYCursor();
	chart.cursor.lineX.strokeOpacity = 0;
	chart.cursor.lineY.strokeOpacity = 0;
}

function monthly_ipd_sale_graph(m_1_val,m_2_val,m_3_val,m_4_val,m_5_val,m_6_val,m_7_val,m_8_val,m_9_val,m_10_val,m_11_val,m_12_val)
{
	m_1_val=m_1_val.split("@");
	var m_1=m_1_val[0];
	var v_1=parseInt(m_1_val[1]);
	
	m_2_val=m_2_val.split("@");
	var m_2=m_2_val[0];
	var v_2=parseInt(m_2_val[1]);
	
	m_3_val=m_3_val.split("@");
	var m_3=m_3_val[0];
	var v_3=parseInt(m_3_val[1]);
	
	m_4_val=m_4_val.split("@");
	var m_4=m_4_val[0];
	var v_4=parseInt(m_4_val[1]);
	
	m_5_val=m_5_val.split("@");
	var m_5=m_5_val[0];
	var v_5=parseInt(m_5_val[1]);
	
	m_6_val=m_6_val.split("@");
	var m_6=m_6_val[0];
	var v_6=parseInt(m_6_val[1]);
	
	m_7_val=m_7_val.split("@");
	var m_7=m_7_val[0];
	var v_7=parseInt(m_7_val[1]);
	
	m_8_val=m_8_val.split("@");
	var m_8=m_8_val[0];
	var v_8=parseInt(m_8_val[1]);
	
	m_9_val=m_9_val.split("@");
	var m_9=m_9_val[0];
	var v_9=parseInt(m_9_val[1]);
	
	m_10_val=m_10_val.split("@");
	var m_10=m_10_val[0];
	var v_10=parseInt(m_10_val[1]);
	
	m_11_val=m_11_val.split("@");
	var m_11=m_11_val[0];
	var v_11=parseInt(m_11_val[1]);
	
	m_12_val=m_12_val.split("@");
	var m_12=m_12_val[0];
	var v_12=parseInt(m_12_val[1]);
	
	// Themes begin
	am4core.useTheme(am4themes_animated);
	// Themes end

	// Create chart instance
	var chart = am4core.create("monthly_ipd_sale_graph", am4charts.XYChart3D);

	// Add data
	chart.data = [{
	  "country": m_1,
	  "visits": v_1
	},{
	  "country": m_2,
	  "visits": v_2
	}, {
	  "country": m_3,
	  "visits": v_3
	}, {
	  "country": m_4,
	  "visits": v_4
	},{
	  "country": m_5,
	  "visits": v_5
	}, {
	  "country": m_6,
	  "visits": v_6
	}, {
	  "country": m_7,
	  "visits": v_7
	},{
	  "country": m_8,
	  "visits": v_8
	}, {
	  "country": m_9,
	  "visits": v_9
	}, {
	  "country": m_10,
	  "visits": v_10
	}, {
	  "country": m_11,
	  "visits": v_11
	}, {
	  "country": m_12,
	  "visits": v_12
	}
	];

	// Create axes
	let categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
	categoryAxis.dataFields.category = "country";
	categoryAxis.renderer.labels.template.rotation = 270;
	categoryAxis.renderer.labels.template.hideOversized = false;
	categoryAxis.renderer.minGridDistance = 20;
	categoryAxis.renderer.labels.template.horizontalCenter = "right";
	categoryAxis.renderer.labels.template.verticalCenter = "middle";
	categoryAxis.tooltip.label.rotation = 270;
	categoryAxis.tooltip.label.horizontalCenter = "right";
	categoryAxis.tooltip.label.verticalCenter = "middle";

	let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
	valueAxis.title.text = "Monthly IPD Sales";
	valueAxis.title.fontWeight = "bold";

	// Create series
	var series = chart.series.push(new am4charts.ColumnSeries3D());
	series.dataFields.valueY = "visits";
	series.dataFields.categoryX = "country";
	series.name = "Visits";
	series.tooltipText = "{categoryX}: [bold]{valueY}[/]";
	series.columns.template.fillOpacity = .8;

	var columnTemplate = series.columns.template;
	columnTemplate.strokeWidth = 2;
	columnTemplate.strokeOpacity = 1;
	columnTemplate.stroke = am4core.color("#FFFFFF");

	columnTemplate.adapter.add("fill", function(fill, target) {
	  return chart.colors.getIndex(target.dataItem.index);
	})

	columnTemplate.adapter.add("stroke", function(stroke, target) {
	  return chart.colors.getIndex(target.dataItem.index);
	})

	chart.cursor = new am4charts.XYCursor();
	chart.cursor.lineX.strokeOpacity = 0;
	chart.cursor.lineY.strokeOpacity = 0;
}

function monthly_total_sale_graph(m_1_val,m_2_val,m_3_val,m_4_val,m_5_val,m_6_val,m_7_val,m_8_val,m_9_val,m_10_val,m_11_val,m_12_val)
{
	m_1_val=m_1_val.split("@");
	var m_1=m_1_val[0];
	var v_1=parseInt(m_1_val[1]);
	
	m_2_val=m_2_val.split("@");
	var m_2=m_2_val[0];
	var v_2=parseInt(m_2_val[1]);
	
	m_3_val=m_3_val.split("@");
	var m_3=m_3_val[0];
	var v_3=parseInt(m_3_val[1]);
	
	m_4_val=m_4_val.split("@");
	var m_4=m_4_val[0];
	var v_4=parseInt(m_4_val[1]);
	
	m_5_val=m_5_val.split("@");
	var m_5=m_5_val[0];
	var v_5=parseInt(m_5_val[1]);
	
	m_6_val=m_6_val.split("@");
	var m_6=m_6_val[0];
	var v_6=parseInt(m_6_val[1]);
	
	m_7_val=m_7_val.split("@");
	var m_7=m_7_val[0];
	var v_7=parseInt(m_7_val[1]);
	
	m_8_val=m_8_val.split("@");
	var m_8=m_8_val[0];
	var v_8=parseInt(m_8_val[1]);
	
	m_9_val=m_9_val.split("@");
	var m_9=m_9_val[0];
	var v_9=parseInt(m_9_val[1]);
	
	m_10_val=m_10_val.split("@");
	var m_10=m_10_val[0];
	var v_10=parseInt(m_10_val[1]);
	
	m_11_val=m_11_val.split("@");
	var m_11=m_11_val[0];
	var v_11=parseInt(m_11_val[1]);
	
	m_12_val=m_12_val.split("@");
	var m_12=m_12_val[0];
	var v_12=parseInt(m_12_val[1]);
	
	// Themes begin
	am4core.useTheme(am4themes_animated);
	// Themes end

	// Create chart instance
	var chart = am4core.create("monthly_total_sale_graph", am4charts.XYChart3D);

	// Add data
	chart.data = [{
	  "country": m_1,
	  "visits": v_1
	},{
	  "country": m_2,
	  "visits": v_2
	}, {
	  "country": m_3,
	  "visits": v_3
	}, {
	  "country": m_4,
	  "visits": v_4
	},{
	  "country": m_5,
	  "visits": v_5
	}, {
	  "country": m_6,
	  "visits": v_6
	}, {
	  "country": m_7,
	  "visits": v_7
	},{
	  "country": m_8,
	  "visits": v_8
	}, {
	  "country": m_9,
	  "visits": v_9
	}, {
	  "country": m_10,
	  "visits": v_10
	}, {
	  "country": m_11,
	  "visits": v_11
	}, {
	  "country": m_12,
	  "visits": v_12
	}
	];

	// Create axes
	let categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
	categoryAxis.dataFields.category = "country";
	categoryAxis.renderer.labels.template.rotation = 270;
	categoryAxis.renderer.labels.template.hideOversized = false;
	categoryAxis.renderer.minGridDistance = 20;
	categoryAxis.renderer.labels.template.horizontalCenter = "right";
	categoryAxis.renderer.labels.template.verticalCenter = "middle";
	categoryAxis.tooltip.label.rotation = 270;
	categoryAxis.tooltip.label.horizontalCenter = "right";
	categoryAxis.tooltip.label.verticalCenter = "middle";

	let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
	valueAxis.title.text = "Monthly Total Sales";
	valueAxis.title.fontWeight = "bold";

	// Create series
	var series = chart.series.push(new am4charts.ColumnSeries3D());
	series.dataFields.valueY = "visits";
	series.dataFields.categoryX = "country";
	series.name = "Visits";
	series.tooltipText = "{categoryX}: [bold]{valueY}[/]";
	series.columns.template.fillOpacity = .8;

	var columnTemplate = series.columns.template;
	columnTemplate.strokeWidth = 2;
	columnTemplate.strokeOpacity = 1;
	columnTemplate.stroke = am4core.color("#FFFFFF");

	columnTemplate.adapter.add("fill", function(fill, target) {
	  return chart.colors.getIndex(target.dataItem.index);
	})

	columnTemplate.adapter.add("stroke", function(stroke, target) {
	  return chart.colors.getIndex(target.dataItem.index);
	})

	chart.cursor = new am4charts.XYCursor();
	chart.cursor.lineX.strokeOpacity = 0;
	chart.cursor.lineY.strokeOpacity = 0;
}

function monthly_total_collection_graph(val,mininum,maximum,month_name)
{
	val=parseInt(val);
	mininum=parseInt(mininum);
	maximum=parseInt(maximum);
	
	// Themes begin
	am4core.useTheme(am4themes_animated);
	// Themes end

	// create chart
	var chart = am4core.create("monthly_total_collection_graph", am4charts.GaugeChart);
	chart.innerRadius = -15;

	var axis = chart.xAxes.push(new am4charts.ValueAxis());
	axis.min = mininum;
	axis.max = maximum;
	axis.strictMinMax = true;

	var colorSet = new am4core.ColorSet();

	var gradient = new am4core.LinearGradient();
	gradient.stops.push({color:am4core.color("red")})
	gradient.stops.push({color:am4core.color("yellow")})
	gradient.stops.push({color:am4core.color("green")})

	axis.renderer.line.stroke = gradient;
	axis.renderer.line.strokeWidth = 20;
	axis.renderer.line.strokeOpacity = 1;

	axis.renderer.grid.template.disabled = true;

	var hand = chart.hands.push(new am4charts.ClockHand());
	hand.radius = am4core.percent(95);

	setInterval(function() {
		hand.showValue(val, 3000, am4core.ease.cubicOut);
		label.text = Math.round(hand.value).toString()+' INR ('+month_name+')';
	}, 1000);
	
	
	
	var labelList = new am4core.ListTemplate(new am4core.Label());
	labelList.template.isMeasured = false;
	labelList.template.background.strokeWidth = 1;
	labelList.template.fontSize = 15;
	labelList.template.padding(10, 20, 10, 20);
	labelList.template.y = am4core.percent(92);
	labelList.template.horizontalCenter = "left";

	var label = labelList.create();
	label.parent = chart.chartContainer;
	label.x = am4core.percent(40);
	label.background.stroke = chart.colors.getIndex(0);
	label.fill = chart.colors.getIndex(0);
	label.text = "0";
}


function weekly_total_sale_graph(m_1_val,m_2_val,m_3_val,m_4_val,m_5_val,m_6_val,m_7_val,m_8_val,m_9_val,m_10_val,m_11_val,m_12_val)
{
	m_1_val=m_1_val.split("@");
	var m_1=m_1_val[0];
	var v_1=parseInt(m_1_val[1]);
	
	m_2_val=m_2_val.split("@");
	var m_2=m_2_val[0];
	var v_2=parseInt(m_2_val[1]);
	
	m_3_val=m_3_val.split("@");
	var m_3=m_3_val[0];
	var v_3=parseInt(m_3_val[1]);
	
	m_4_val=m_4_val.split("@");
	var m_4=m_4_val[0];
	var v_4=parseInt(m_4_val[1]);
	
	m_5_val=m_5_val.split("@");
	var m_5=m_5_val[0];
	var v_5=parseInt(m_5_val[1]);
	
	m_6_val=m_6_val.split("@");
	var m_6=m_6_val[0];
	var v_6=parseInt(m_6_val[1]);
	
	m_7_val=m_7_val.split("@");
	var m_7=m_7_val[0];
	var v_7=parseInt(m_7_val[1]);
	
	m_8_val=m_8_val.split("@");
	var m_8=m_8_val[0];
	var v_8=parseInt(m_8_val[1]);
	
	m_9_val=m_9_val.split("@");
	var m_9=m_9_val[0];
	var v_9=parseInt(m_9_val[1]);
	
	m_10_val=m_10_val.split("@");
	var m_10=m_10_val[0];
	var v_10=parseInt(m_10_val[1]);
	
	m_11_val=m_11_val.split("@");
	var m_11=m_11_val[0];
	var v_11=parseInt(m_11_val[1]);
	
	m_12_val=m_12_val.split("@");
	var m_12=m_12_val[0];
	var v_12=parseInt(m_12_val[1]);
	
	// Themes begin
	am4core.useTheme(am4themes_animated);
	// Themes end

	// Create chart instance
	var chart = am4core.create("weekly_total_sale_graph", am4charts.XYChart3D);

	// Add data
	chart.data = [{
	  "country": m_1,
	  "visits": v_1
	},{
	  "country": m_2,
	  "visits": v_2
	}, {
	  "country": m_3,
	  "visits": v_3
	}, {
	  "country": m_4,
	  "visits": v_4
	},{
	  "country": m_5,
	  "visits": v_5
	}, {
	  "country": m_6,
	  "visits": v_6
	}, {
	  "country": m_7,
	  "visits": v_7
	},{
	  "country": m_8,
	  "visits": v_8
	}, {
	  "country": m_9,
	  "visits": v_9
	}, {
	  "country": m_10,
	  "visits": v_10
	}, {
	  "country": m_11,
	  "visits": v_11
	}, {
	  "country": m_12,
	  "visits": v_12
	}
	];

	// Create axes
	let categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
	categoryAxis.dataFields.category = "country";
	categoryAxis.renderer.labels.template.rotation = 270;
	categoryAxis.renderer.labels.template.hideOversized = false;
	categoryAxis.renderer.minGridDistance = 20;
	categoryAxis.renderer.labels.template.horizontalCenter = "right";
	categoryAxis.renderer.labels.template.verticalCenter = "middle";
	categoryAxis.tooltip.label.rotation = 270;
	categoryAxis.tooltip.label.horizontalCenter = "right";
	categoryAxis.tooltip.label.verticalCenter = "middle";

	let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
	valueAxis.title.text = "Weekly Total Sales";
	valueAxis.title.fontWeight = "bold";

	// Create series
	var series = chart.series.push(new am4charts.ColumnSeries3D());
	series.dataFields.valueY = "visits";
	series.dataFields.categoryX = "country";
	series.name = "Visits";
	series.tooltipText = "{categoryX}: [bold]{valueY}[/]";
	series.columns.template.fillOpacity = .8;

	var columnTemplate = series.columns.template;
	columnTemplate.strokeWidth = 2;
	columnTemplate.strokeOpacity = 1;
	columnTemplate.stroke = am4core.color("#FFFFFF");

	columnTemplate.adapter.add("fill", function(fill, target) {
	  return chart.colors.getIndex(target.dataItem.index);
	})

	columnTemplate.adapter.add("stroke", function(stroke, target) {
	  return chart.colors.getIndex(target.dataItem.index);
	})

	chart.cursor = new am4charts.XYCursor();
	chart.cursor.lineX.strokeOpacity = 0;
	chart.cursor.lineY.strokeOpacity = 0;
}

function daily_total_sale_graph(m_1_val,m_2_val,m_3_val,m_4_val,m_5_val,m_6_val,m_7_val,m_8_val,m_9_val,m_10_val,m_11_val,m_12_val)
{
	m_1_val=m_1_val.split("@");
	var m_1=m_1_val[0];
	var v_1=parseInt(m_1_val[1]);
	
	m_2_val=m_2_val.split("@");
	var m_2=m_2_val[0];
	var v_2=parseInt(m_2_val[1]);
	
	m_3_val=m_3_val.split("@");
	var m_3=m_3_val[0];
	var v_3=parseInt(m_3_val[1]);
	
	m_4_val=m_4_val.split("@");
	var m_4=m_4_val[0];
	var v_4=parseInt(m_4_val[1]);
	
	m_5_val=m_5_val.split("@");
	var m_5=m_5_val[0];
	var v_5=parseInt(m_5_val[1]);
	
	m_6_val=m_6_val.split("@");
	var m_6=m_6_val[0];
	var v_6=parseInt(m_6_val[1]);
	
	m_7_val=m_7_val.split("@");
	var m_7=m_7_val[0];
	var v_7=parseInt(m_7_val[1]);
	
	m_8_val=m_8_val.split("@");
	var m_8=m_8_val[0];
	var v_8=parseInt(m_8_val[1]);
	
	m_9_val=m_9_val.split("@");
	var m_9=m_9_val[0];
	var v_9=parseInt(m_9_val[1]);
	
	m_10_val=m_10_val.split("@");
	var m_10=m_10_val[0];
	var v_10=parseInt(m_10_val[1]);
	
	m_11_val=m_11_val.split("@");
	var m_11=m_11_val[0];
	var v_11=parseInt(m_11_val[1]);
	
	m_12_val=m_12_val.split("@");
	var m_12=m_12_val[0];
	var v_12=parseInt(m_12_val[1]);
	
	// Themes begin
	am4core.useTheme(am4themes_animated);
	// Themes end

	// Create chart instance
	var chart = am4core.create("daily_total_sale_graph", am4charts.XYChart3D);

	// Add data
	chart.data = [{
	  "country": m_1,
	  "visits": v_1
	},{
	  "country": m_2,
	  "visits": v_2
	}, {
	  "country": m_3,
	  "visits": v_3
	}, {
	  "country": m_4,
	  "visits": v_4
	},{
	  "country": m_5,
	  "visits": v_5
	}, {
	  "country": m_6,
	  "visits": v_6
	}, {
	  "country": m_7,
	  "visits": v_7
	},{
	  "country": m_8,
	  "visits": v_8
	}, {
	  "country": m_9,
	  "visits": v_9
	}, {
	  "country": m_10,
	  "visits": v_10
	}, {
	  "country": m_11,
	  "visits": v_11
	}, {
	  "country": m_12,
	  "visits": v_12
	}
	];

	// Create axes
	let categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
	categoryAxis.dataFields.category = "country";
	categoryAxis.renderer.labels.template.rotation = 270;
	categoryAxis.renderer.labels.template.hideOversized = false;
	categoryAxis.renderer.minGridDistance = 20;
	categoryAxis.renderer.labels.template.horizontalCenter = "right";
	categoryAxis.renderer.labels.template.verticalCenter = "middle";
	categoryAxis.tooltip.label.rotation = 270;
	categoryAxis.tooltip.label.horizontalCenter = "right";
	categoryAxis.tooltip.label.verticalCenter = "middle";

	let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
	valueAxis.title.text = "Daily Total Sales";
	valueAxis.title.fontWeight = "bold";

	// Create series
	var series = chart.series.push(new am4charts.ColumnSeries3D());
	series.dataFields.valueY = "visits";
	series.dataFields.categoryX = "country";
	series.name = "Visits";
	series.tooltipText = "{categoryX}: [bold]{valueY}[/]";
	series.columns.template.fillOpacity = .8;

	var columnTemplate = series.columns.template;
	columnTemplate.strokeWidth = 2;
	columnTemplate.strokeOpacity = 1;
	columnTemplate.stroke = am4core.color("#FFFFFF");

	columnTemplate.adapter.add("fill", function(fill, target) {
	  return chart.colors.getIndex(target.dataItem.index);
	})

	columnTemplate.adapter.add("stroke", function(stroke, target) {
	  return chart.colors.getIndex(target.dataItem.index);
	})

	chart.cursor = new am4charts.XYCursor();
	chart.cursor.lineX.strokeOpacity = 0;
	chart.cursor.lineY.strokeOpacity = 0;
}
</script>
