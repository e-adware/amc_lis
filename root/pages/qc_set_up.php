
<?php
/*
$d=mysqli_query($link,"select * from test_sample_result");
while($dd=mysqli_fetch_array($d))
{
	$val=mysqli_fetch_array(mysqli_query($link,"select * from test_sample_result_repeat where testid='$dd[testid]' and result!='' and slno>500"));
	echo "update test_sample_result set result='$val[result]' where testid='$dd[testid]'";
	mysqli_query($link,"update test_sample_result set result='$val[result]' where testid='$dd[testid]'");
}
*/
?>

<div id="content-header">
    <div class="header_div"> <span class="header"> QC SETUP</span></div>
</div>


<div class="container-fluid">
	
	<div class='btn-group' style='border-bottom:1px solid #CCC;width:100%;text-align:center'>
		<button class="btn btn-primary" id="qc_master" onclick="load_det(this)">QC Master</button>
		<button class="btn btn-primary" id="test_assign" onclick="load_det(this)">Test Assign</button>
		<button class="btn btn-primary" id="normal_range" onclick="load_det(this)">Set Normal Range</button>
	</div>		


	<div id="data_detail"></div>
	
</div>	

<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"/>
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="border-radius:0;display:none">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div id="results"> </div>
			</div>
		</div>
	</div>
</div>

<div id="loader" style="margin-top:-10%;display:none;z-index:99"></div>	
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<link rel="stylesheet" href="../css/animate.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>

$(document).ready(function(){
		show_test();
	});

function show_test()
{
	$.post("pages/qc_set_up_ajax.php",
	{
		instr:$("#instrument").val(),
		primary:$("#primary").val(),
		secondary:$("#secondary").val(),
		tname:$("#testname").val(),
		dept:$("#test_dep").val(),
		vac:$("#test_vaccu").val(),
		type:"load_test"
	},
	function(data,status)
	{
		$("#test_data").html(data);
	})
}

function load_det(val)
{
	$(".btn-group button").prop("class","btn btn-primary");
	$(val).prop("class","btn btn-default");
	
	$.post("pages/qc_set_up_ajax.php",
	{
		type:$(val).prop("id")
	},
	function(data,status)
	{
		$("#data_detail").html(data);
		
		if($(val).prop("id")=="test_assign")
		{
			show_test();
		}
	})
}

function load_primary()
{
	$.post("pages/qc_set_up_ajax.php",
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
	$.post("pages/qc_set_up_ajax.php",
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
	$.post("pages/qc_set_up_ajax.php",
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


function select_check()
{
	
	if($("#icon_check").prop("class")=="icon-check")
	{
		$("#icon_check").prop("class","icon-check-empty")
		$("#test_list tr").show();
	}
	else
	{
		$("#icon_check").prop("class","icon-check")
		
		$("#test_list tr").hide();
		$("#test_list tr:first").show();
		
		$("#test_list .selected").show();
	}
}

function tst_data_check(val)
{
	var save=0;
	if($("#instrument").val()>0 && $("#primary").val()>0 && $("#secondary").val()>0)
	{
		if($("#tst_check_"+val+"").prop("class")=="icon-check")
		{
			$("#tst_check_"+val+"").prop("class","icon-check-empty")
			$("#tst_check_"+val+"").closest("tr").removeClass("selected");
			save=0;
		}
		else
		{
			$("#tst_check_"+val+"").prop("class","icon-check")
			$("#tst_check_"+val+"").closest("tr").addClass("selected");
			save=1;
		}
		
		$.post("pages/qc_set_up_ajax.php",
		{
			instr:$("#instrument").val(),
			primary:$("#primary").val(),
			secondary:$("#secondary").val(),
			test:$("#tst_id_"+val+"").val(),
			save:save,
			type:"save_instr_test"
		},
		function(data,status)
		{
			
		})
	}
}

function add_instr(val)
{
	$.post("pages/qc_set_up_ajax.php",
	{
		type:"add_instr"
	},
	function(data,status)
	{
		$("#results").html(data);
		if(!val)
		{
			$("#mod").click();
		}
		$("#results").fadeIn(500,function(){ $("#instr_name").focus(); });
	})	
}
function update_instr(id,name)
{
	$("#instr_name").val(name);	
	$("#instr_id_upd").val(id);	
	$("#save_instr_name").html("<i class='icon-edit'></i> Update");	
	
}

function save_instr()
{
	$("#save_instr_name").prop("disabled",true);
	$.post("pages/qc_set_up_ajax.php",
	{
		name:$("#instr_name").val(),
		upd_id:$("#instr_id_upd").val(),
		type:"save_instr"
	},
	function(data,status)
	{
		if(data=="error")
		{
			alert("Already Exist");
			$("#instr_name").css("border","1px solid red");
		}
		else
		{
			$("#instr_list").html(data);
			//$("#mod").click();
			alert("Saved");		
		}
		add_instr(1);
		$("#save_instr_name").prop("disabled",false);	
	})
}
function show_test_event(e)
{
	if(e.which==13)
	{
		show_test();
	}
}
function copy_test()
{
	$.post("pages/qc_set_up_ajax.php",
	{
		instr:$("#instrument").val(),
		primary:$("#primary").val(),
		secondary:$("#secondary").val(),
		type:"copy_test"
	},
	function(data,status)
	{
		$("#myModal").css({'left': '43%','width':'65%'});
		$("#results").html(data);
		$("#mod").click();
		$("#results").fadeIn(500);
	})
}
function sel_all()
{
	if($("#instrument").val()>0)
	{
		if($("#sel_all").prop("class")=="icon-check-empty")
		{
			if(confirm("Selecting all will assign all test to the selected instrument. Are you sure?"))
			{
				$("#sel_all").prop("class","icon-check");
				
				var uncheck=$("#test_list .icon-check-empty")
				for(var i=0;i<uncheck.length;i++)
				{
					$(uncheck[i]).closest("tr").click();
				}
			}
		}
		else
		{
			if(confirm("Un-Selecting will remove all test from the selected instrument. Are you sure?"))
			{
				$("#sel_all").prop("class","icon-check-empty");
				var uncheck=$("#test_list .icon-check")
				for(var i=0;i<uncheck.length;i++)
				{
					$(uncheck[i]).closest("tr").click();
				}
			}
		}
	}
}

function new_qc_master()
{
	$.post("pages/qc_set_up_ajax.php",
	{
		type:"new_qc"
	},
	function(data,status)
	{
		$("#results").html(data);
		$("#mod").click();
		$("#results").fadeIn(500,function(){ $("#instr_name").focus(); });	
	})
}

function add_more()
{
	var sec='<div><input type="text" id="seconday" class="second" placeholder="Add Secondary Lot No"/>';
	sec=sec+' <input type="text" id="qc_text" class="qc_text" placeholder="Add QC Text" style="width:110px"/>';
	sec=sec+' <select class="status" id="status" style="width:100px"><option value="1">Active</option><option value="0">Inactive</option></select>';
	sec=sec+'<button class="btn btn-danger" style="margin-bottom:10px" onclick="remove_second(this)"><i class="icon-minus"></i></button></div>';
	$("#prim_second").append(sec);
}

function remove_second(elem)
{
	$(elem).closest("div").remove();	
}

function save_qc_master(val)
{
	var instrument=$("#instrument").val();
	var primary=$("#primary").val().trim();
	
	var instr_old="";
	var primary_old="";
	if(val=="upd")
	{
		var instr_old=$("#instr_old").val();
		var primary_old=$("#primary_old").val().trim();
	}
	
	if(instrument>0 && primary!='')
	{
		var sec_det="";
		var sec_div=$("#prim_second div");
		for(var i=0;i<sec_div.length;i++)
		{
			var sec_lot=$(sec_div[i]).find("#seconday").val();
			var sec_text=$(sec_div[i]).find("#qc_text").val();
			var sec_stat=$(sec_div[i]).find("#status").val();
			
			sec_det+=sec_lot+"@qc_koushik@"+sec_text+"@qc_koushik@"+sec_stat+"#qc_koushik_done#";
		}
		
			
		$.post("pages/qc_set_up_ajax.php",
		{
			instrument:instrument,
			primary:primary,
			sec:sec_det,
			val:val,
			instr_old:instr_old,
			primary_old:primary_old,
			type:"save_qc"
		},
		function(data,status)
		{
			var adata=data.split("@@");
			if(adata[0]=="Error")
			{
				if(adata[0]!='')
				{
					$("#input[value='"+adata[0]+"']").css({'border':'1px solid red'});
					alert("Error! Primary Lot No already exist under the same instrument");	
				}
				
				
			}
			else
			{
				$.post("pages/qc_set_up_ajax.php",
				{
					type:"qc_master"
				},
				function(data,status)
				{
					$("#data_detail").html(data);
					$("#mod").click();
				})
			}
		})
	}
}

function edit_qc_master(val)
{
	
	$.post("pages/qc_set_up_ajax.php",
	{
		instr:$("#instr_"+val+"").val(),
		primary:$("#primary_"+val+"").val(),
		typ:"update",
		type:"new_qc"
	},
	function(data,status)
	{
		$("#results").html(data);
		$("#mod").click();
		$("#results").fadeIn(500,function(){ $("#instr_name").focus(); });	
	})	
}

function load_normal()
{
	if($("#instrument").val()>0 && $("#primary").val()>0 && $("#secondary").val()>0)
	{
		$.post("pages/qc_set_up_ajax.php",
		{
			instr:$("#instrument").val(),
			primary:$("#primary").val(),
			secondary:$("#secondary").val(),
			type:"test_range"
		},
		function(data,status)
		{
			$("#qc_normal").html(data);
		})	
	}
	
}

function update_normal(sl,typ,e)
{
	if(e.which==13)
	{
		$.post("pages/qc_set_up_ajax.php",
		{
			instr:$("#instrument").val(),
			primary:$("#primary").val(),
			secondary:$("#secondary").val(),
			test:$("#testid_"+sl+"").val(),
			val_from:$("#value_from_"+sl+"").val(),
			val_to:$("#value_to_"+sl+"").val(),
			dis_range:$("#display_"+sl+"").val(),
			typ:typ,
			type:"save_range"
		},
		function(data,status)
		{
			$("#display_"+sl+"").val($("#value_from_"+sl+"").val()+" - "+$("#value_to_"+sl+"").val());
			if(typ=="from")
			{
				$("#value_from_"+sl+"").css({'border':'1px solid green'});
			}
			else
			{
				$("#value_to_"+sl+"").css({'border':'1px solid green'});
			}
		})
	}
}

function hide_test_normal()
{
	$("#qc_normal").empty();
}

function load_primary_copy()
{
	$.post("pages/qc_set_up_ajax.php",
	{
		instr:$("#instrument_1").val(),
		type:"load_primary_copy"
	},
	function(data,status)
	{
		$("#primary_sel_1").html(data);
	})	
	
}
function load_secondary_copy()
{
	$.post("pages/qc_set_up_ajax.php",
	{
		instr:$("#instrument_1").val(),
		primary:$("#primary_1").val(),
		type:"load_second_copy"
	},
	function(data,status)
	{
		$("#secondary_sel_1").html(data);
	})
}
function load_qc_text_copy()
{
	$.post("pages/qc_set_up_ajax.php",
	{
		instr:$("#instrument_1").val(),
		primary:$("#primary_1").val(),
		secondary:$("#secondary_1").val(),
		type:"load_qc_text"
	},
	function(data,status)
	{
		$("#qc_text_1").val(data);
	})
}

function copy_test_data()
{
	var sel_chk=0;
	var select_box=$("#copy_table select");
	for(var i=0;i<select_box.length;i++)
	{
		if($(select_box).val()==0)
		{
			sel_chk++;
			$(select_box).css({'border':'1px solid red'});
		}
	}
	
	if(sel_chk==0)
	{
		$("#loader").show();
		$.post("pages/qc_set_up_ajax.php",
		{
			instr1:$("#instrument_1").val(),
			primary1:$("#primary_1").val(),
			secondary1:$("#secondary_1").val(),
			
			instr2:$("#instrument_2").val(),
			primary2:$("#primary_2").val(),
			secondary2:$("#secondary_2").val(),
			type:"copy_test_data"
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#mod").click();
			show_test();
			
		})
	}
}
</script>

<style>
	.selected td{ background-color: #D6E1E7 !important;color:#192113;border-top:1px solid #CCC;border-bottom:1px solid #CCC;font-weight:bold}
	
	.upd_instr{ cursor:pointer;font-weight:bold}
	
	#myModal
	{
		left: 50%;
		width:45%;
		
	}	
	
</style>
