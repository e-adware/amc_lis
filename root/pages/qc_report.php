<div id="content-header">
    <div class="header_div"> <span class="header"> QC REPORTS</span></div>
</div>


<div class="container-fluid">
	
	
	<div class='btn-group' style='border-bottom:1px solid #CCC;width:100%;text-align:center'>
		<button class="btn btn-primary" id="qc_gen" onclick="load_det(this)">Generate QC</button>
		<button class="btn btn-primary" id="qc_report" onclick="load_det(this)">Reports</button>
	</div>		
	
	
	<!--
	<ul class="nav nav-tabs">
	  
	  <li class="dropdown">
		<a class="dropdown-toggle" data-toggle="dropdown" href="#">Generate/View QC 
		<span class="caret"></span></a>
		<ul class="dropdown-menu">
		  <li><a href="#" id="qc_gen" onclick="load_det(this)">Add New</li>
		  <li><a href="#">Submenu 1-2</a></li>
		  <li><a href="#">Submenu 1-3</a></li>
		</ul>
	  </li>
	  <li><a href="#">Menu 2</a></li>
	  <li><a href="#">Menu 3</a></li>
	</ul>
	-->
	<div id="data_detail"></div>
</div>
<div id="loader" style="margin-top:-10%;display:none"></div>	
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<link rel="stylesheet" href="../css/animate.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />

<script>
function load_det(val)
{
	$(".btn-group button").prop("class","btn btn-primary");
	$(val).prop("class","btn btn-default");
	
	$.post("pages/qc_report_ajax.php",
	{
		type:$(val).prop("id")
	},
	function(data,status)
	{
		$("#data_detail").html(data);
		
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
		});
	})
}

function load_primary()
{
	$.post("pages/qc_report_ajax.php",
	{
		instr:$("#instrument").val(),
		type:"load_primary"
	},
	function(data,status)
	{
		$("#primary_sel").html(data);
	})	
}

function load_secondary()
{
	$.post("pages/qc_report_ajax.php",
	{
		instr:$("#instrument").val(),
		primary:$("#primary").val(),
		type:"load_second"
	},
	function(data,status)
	{
		$("#secondary_sel").html(data);
	})
}
function load_qc_text()
{
	$.post("pages/qc_report_ajax.php",
	{
		instr:$("#instrument").val(),
		primary:$("#primary").val(),
		secondary:$("#secondary").val(),
		type:"load_qc_text"
	},
	function(data,status)
	{
		$("#qc_text").val(data);
	})
}

function show_test()
{
	$.post("pages/qc_report_ajax.php",
	{
		instr:$("#instrument").val(),
		primary:$("#primary").val(),
		secondary:$("#secondary").val(),
		type:"show_test"
	},
	function(data,status)
	{
		$("#test_data").html(data);
		$("#gc").fadeIn(200);
	})
}

function check_tst(val)
{
	if($("#icon_check_"+val+"").prop("class")=="icon-check")
	{
		$("#icon_check_"+val+"").prop("class","icon-check-empty")
	}
	else
	{
		$("#icon_check_"+val+"").prop("class","icon-check")
	}	
}

function generate_qc()
{
	$("#loader").show();
	var instr=$("#instrument").val();
	var primary=$("#primary").val();
	var secondary=$("#secondary").val();
	
	var tst_list="";
	var tst=$(".icon-check");
	for(var i=1;i<tst.length;i++)
	{
		tst_list+="@qc_tst@"+$(tst[i]).attr("value");
	}
	
	$.post("pages/qc_report_ajax.php",
	{
		instr:instr,
		primary:primary,
		secondary:secondary,
		test_list:tst_list,
		user:$("#user").text(),
		type:"save_gc"
	},
	function(data,status)
	{
		alert(data);
		$("#qc_gen").click();
		$("#loader").hide();
	})
}
function load_qc_det(instr,primary,secondary,qc,elem)
{
	$.post("pages/qc_report_ajax.php",
	{
		instr:instr,
		primary:primary,
		secondary:secondary,
		qc:qc,
		type:"load_qc"
	},
	function(data,status)
	{
		$("#qc_detail").html(data);
		
		$("#gen_bar_ul li.active").removeClass("active");
		$(elem).parent().addClass("active");
	})
	
}

function show_report_test()
{
	if($("#test_list").length>0)
	{
		$.post("pages/qc_report_ajax.php",
		{
			instr:$("#instrument").val(),
			primary:$("#primary").val(),
			secondary:$("#secondary").val(),
			type:"load_test_sel"
		},
		function(data,status)
		{
			$("#test_list_span").html(data);
		})
	}
}

function load_primary_rep()
{
	$.post("pages/qc_report_ajax.php",
	{
		instr:$("#instrument").val(),
		type:"load_primary_rep"
	},
	function(data,status)
	{
		$("#primary_sel").html(data);
	})	
}
function load_secondary_rep()
{
	$.post("pages/qc_report_ajax.php",
	{
		instr:$("#instrument").val(),
		primary:$("#primary").val(),
		type:"load_second_rep"
	},
	function(data,status)
	{
		$("#secondary_sel").html(data);
	})
}
function load_report()
{
	$.post("pages/qc_report_ajax.php",
	{
		fdate:$("#fdate").val(),
		tdate:$("#tdate").val(),
		instr:$("#instrument").val(),
		primary:$("#primary").val(),
		secondary:$("#secondary").val(),
		test:$("#test_list").val(),
		type:"load_report"
	},
	function(data,status)
	{
		$("#report_data").html(data);
	})
}
</script>

<style>
.qc_test{ display:inline-block;width:300px;font-weight:bold;font-size:12px;padding:5px;cursor:pointer}

#test_data{ max-height:350px;overflow:scroll;overflow-x:hidden;}

.qc_info{ display:inline-block;width:200px;font-size:15px;margin:10px 0px 10px 0px;padding:5px;text-align:left}

.out_of_range td{font-weight:bold;color:#e94747;}
.out_of_range_td{font-weight:bold;color:#e94747;}
</style>
