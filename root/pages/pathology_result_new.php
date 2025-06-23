<?php
$instrument_str="display:none;";
if($instrument_wise_normal_range==1)
{
	$instrument_str="";
}
?>
<!--header-->
<div id="content-header">
  <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
  <table class="table table-bordered table-condensed">
    <tr>
      <td colspan="2">
        <b>From</b>
        <input class="form-control datepicker" type="text" name="fdate" id="fdate" value="<?php echo date("Y-m-d"); ?>">
        <b>To</b>
        <input class="form-control datepicker" type="text" name="tdate" id="tdate" value="<?php echo date("Y-m-d"); ?>">

        <?php
				if($glob_patient_type==0)
				{
					$pat_typ="display:none;";
				}
				?>
        <select id="pat_type" style="<?php echo $pat_typ;?>">
          <option value="opd_id">OPD</option>
          <option value="ipd_id">IPD</option>
        </select>

        <select id="dept">
          <option value="0">--All Dept--</option>
          <?php
					$dept=mysqli_query($link,"select distinct type_id,type_name from testmaster where category_id='1' order by type_name");
					while($dep=mysqli_fetch_array($dept))
					{
						echo "<option value='$dep[type_id]'>$dep[type_name]</option>";
					}
					?>
        </select>
        <button class="btn btn-search" onClick="load_pat_details()" style="margin-bottom: 10px;"><i class="icon-search"></i> View</button>
      </td>
    </tr>
    <tr>
      <td colspan="2"><b>Enter</b>
        <select id="s_typ" class="span2">
          <!--<option value="opd">OPD ID</option>
					<option value="ipd">IPD ID</option>-->
          <option value="pin">Bill No.</option>
          <option value="uhid">UHID No.</option>
          <option value="name">NAME</option>
        </select>

        <input type="text" id="p_no" onkeyup="load_pat_info(this.value,event)" autofocus>
		
		<select id="instrument_id" onchange="instrument_change()" style="<?php echo $instrument_str; ?>">
		<?php
			$qry=mysqli_query($link, "SELECT `id`, `name` FROM `lab_instrument_master` WHERE `id`>=0");
			while($data=mysqli_fetch_array($qry))
			{
				echo "<option value='$data[id]'>$data[name]</option>";
			}
		?>
		</select>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <div id="patient_info" style="overflow:scroll;overflow-x:hidden;max-height:300px"></div>

        <div id="patient_info2" style="display:none">
			
        </div>
      </td>
    </tr>
  </table>

  <div id="loader" style="margin-top:-10%;"></div>

  <div id="back" onclick="hid_div1()"> </div>

  <!-- <input type="button" id="mod" style="display:none" data-toggle="collapse" data-target="#demo"></input>
  <input type="hidden" id="mod_chk" value="0" />
  <div id="demo" class="collapse">
    <div class="accordion-inner">
      <div id="results"> </div>
    </div>
  </div> -->

  <!-- <input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none" />
  <input type="hidden" id="mod_chk" value="0" />
  <div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
    data-keyboard="false">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body">
          <div id="results"> </div>
        </div>
      </div>
    </div>
  </div> -->

<!-- Trigger the modal with a button -->
<button type="button" class="btn btn-info btn-lg" id="modal_btn_repeat" data-toggle="modal" data-target="#myModal_repeat" style="display:none;">Repeat Param</button>
<div id="myModal_repeat" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<!--<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Modal Header</h4>
			</div>-->
			<div class="modal-body">
				<div id="result_repeat"></div>
			</div>
			<!--<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>-->
		</div>
	</div>
</div>

  <input type="hidden" id="mod_chk2" value="0" />
  <input type="hidden" id="add_summ" value="0" />
  <input type="hidden" id="acc_id" value="0" />
  <input type="hidden" id="ival" value="0" />
  <input type="button" data-toggle="modal" data-target="#myModal2" id="mod2" style="display:none" />

  <div class="modal fade" id="myModal2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
    data-keyboard="false">
    <div class="modal-dialog" id="modal_dial">
      <div class="modal-content">
        <div class="modal-body">
          <div id="results1"></div>
        </div>
      </div>
    </div>
  </div>
  <!-- <div id="results"> </div>
        -->

  <div class="text-center"
    style="position: fixed; bottom: -20px; right: 0px; padding: 10px; background: #fff; color: #000; box-shadow: 0 0 10px rgba(0,0,0,0.5); z-index:100;">
    <table class="table table-bordered table-condensed">
      <tr>
        <td class=""><span class="btn_round_msg red"></span> Not Reported</td>
        <td class=""><span class="btn_round_msg yellow"></span> Printed</td>
        <td class=""><span class="btn_round_msg green"></span> Reported</td>
        <td class=""><span class="btn_round_msg grey"></span> Printed</td>
      </tr>
    </table>
  </div>

</div>
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<link href="../fine-uploader/fine-uploader-new.css" rel="stylesheet" type="text/css" />
<link href="../fine-uploader/fine-uploader-gallery.css" rel="stylesheet" type="text/css" />
<link href="../fine-uploader/fine-uploader-gallery.min.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../fine-uploader/fine-uploader.min.js"></script>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<link rel="stylesheet" href="../css/animate.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->

<link href="../css/jquery.gritter.css" rel="stylesheet" />
<script src="../js/jquery.gritter.min.js"></script>

<script src="../js/jquery.dataTables.min_all.js"></script>
<script>
$(document).ready(function() {
	$("#loader").hide();
	$(".datepicker").datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd',
		maxDate: '0',
		yearRange: "-150:+0",
		//defaultDate:'2000-01-01',
	});
	load_pat_details();

	$('#tabs li a:not(:first)').addClass('inactive');
	$('.container').hide();
	$('.container:first').show();
	
	$('#tabs li a').click(function() {
		var t = $(this).attr('id');
		if ($(this).hasClass('inactive')) { //this is the start of our condition 
			$('#tabs li a').addClass('inactive');
			$(this).removeClass('inactive');
			$('.container').hide();
			$('#' + t + 'C').fadeIn('slow');
		}
	});
	
	$("#instrument_id").val(getCookie("instrument_id"));
});

function instrument_change()
{
	setCookie("instrument_id",$("#instrument_id").val(),300);
	
	var uhid= $("#uhid_td").text().trim();
	var opd_id= $("#opd_td").text().trim();
	var ipd_id= $("#ipd_td").text().trim();
	var batch_no= $("#batch_td").text().trim();
	
	if(opd_id)
	{
		var pin=opd_id;
	}
	if(ipd_id)
	{
		var pin=ipd_id;
	}
	
	load_test_detail(uhid, pin, batch_no);
}

var sel_pser = 1;
var sel_divser = 0;

function alertmsg(msg, n) {
	$.gritter.add({
		//title:	'Normal notification',
		text: '<h5 style="text-align:center;">' + msg + '</h5>',
		time: 1000,
		sticky: false
	});
	if (n > 0) {
		$(".gritter-item").css("background", "#237438");
	}
}

function hid_div1() {
	if ($("#mod_chk").val() == 1) {
		$("#results").fadeOut(100, function() {
			$("#mod_chk").val("0");
			$("#mod").click();
		})
		//$("#test_id").focus();
		//$("#results").css({'display':'none'});
	}
}

function hid_div(e)
{
	var unicode = e.keyCode ? e.keyCode : e.charCode;
	if (unicode == 13) {
		var act = document.activeElement.id;
		if (!act) {
			document.getElementById("info1").focus();
		} else {
			var clsn = $("#" + act).attr("class");
			var nam = $("#" + act).attr("name");
			var val = nam.replace(/^\D+/g, '');
			val = parseInt(val) + 1;

			document.getElementsByName(clsn + val)[0].focus();
			//alert(clsn + val);
		}
	} else if (unicode == 27) {
			esc(100);
	} else if (e.ctrlKey == 1) {
		if (unicode == 103) {
			e.preventDefault();
			$("#g_print").click();
		}
		if (unicode == 104) {
			e.preventDefault();
			$("#grp_print_rpt").click();
		}
		if (unicode == 117) {
			e.preventDefault();
			$("#save").click();
		}
		if (unicode == 105) {
			e.preventDefault();
			$("#summary").click();
		}
		if (unicode == 112) {
			e.preventDefault();
			$("#print").click();
		}
		if (unicode == 108) {
			e.preventDefault();
			$("#image").click();
		}
		if (unicode == 110) {
			e.preventDefault();
			location.reload();
		}
		if (unicode == 32) {
			e.preventDefault();
			$("#select_all").click();
			select_all();
		}
		if (unicode == 90) {
			e.preventDefault();
			group_print();
			
			setTimeout(function(){
				$("#g_print").hide();
				$("#grp_print_rpt").focus();
			},500);
		}
		if (unicode == 88) {
			e.preventDefault();
			group_print_test();
		}
	}
}

function edit_intext(val, chk) {
  $("#in_txt").text(val);
  // $("#p_no").focus();

  $("#chk_typ").val(chk);
}

function load_pat_info(val, e) {
  var unicode = e.keyCode ? e.keyCode : e.charCode;
  if (unicode == 13) {
    var pinfo = $("#path_pat" + sel_pser).text().split("@");

    load_test_detail(pinfo[1], pinfo[2], pinfo[3]);
  } else if (unicode == 40) {
    var chk = sel_pser + 1;
    var cc = document.getElementById("path_tr" + chk).innerHTML;
    if (cc) {
      sel_pser = sel_pser + 1;
      $("#path_tr" + sel_pser).css({
        'color': '#419641',
        'transform': 'scale(0.95)',
        'font-weight': 'bold',
        'transition': 'all .2s'
      });
      var sel_pser1 = sel_pser - 1;
      $("#path_tr" + sel_pser1).css({
        'color': 'black',
        'transform': 'scale(1.0)',
        'font-weight': 'normal',
        'transition': 'all .2s'
      });
      var z2 = sel_pser % 1;
      if (z2 == 0) {
        $("#patient_info").scrollTop(sel_divser)
        sel_divser = sel_divser + 38;
      }
    }
  } else if (unicode == 38) {
    var chk = sel_pser - 1;
    var cc = document.getElementById("path_tr" + chk).innerHTML;
    if (cc) {
      sel_pser = sel_pser - 1;
      $("#path_tr" + sel_pser).css({
        'color': '#419641',
        'transform': 'scale(0.95)',
        'font-weight': 'bold',
        'transition': 'all .2s'
      });
      var sel_pser1 = sel_pser + 1;
      $("#path_tr" + sel_pser1).css({
        'color': 'black',
        'transform': 'scale(1.0)',
        'font-weight': 'normal',
        'transition': 'all .2s'
      });
      var z2 = sel_pser % 1;
      if (z2 == 0) {
        sel_divser = sel_divser - 38;
        $("#patient_info").scrollTop(sel_divser)

      }
    }
  } else {
    if (val.length > 1) {
      $.post("pages/pathology_pat_info.php", {
          val: val,
          s_typ: $("#s_typ").val(),
          pat_type: $("#pat_type").val(),
          type: 2
        },
        function(data, status) {
          $("#patient_info").html(data).slideDown(300);
        })
    }
  }
}

function load_pat_details() {
  $.post("pages/pathology_pat_info.php", {
      fdate: $("#fdate").val(),
      tdate: $("#tdate").val(),
      dept: $("#dept").val(),
      pat_type: $("#pat_type").val(),
      type: 1
    },
    function(data, status) {
      $("#patient_info").html(data).slideDown(200);
      $("#patient_info2").hide();
      var sel_pser=1;
      
      $("#p_no").focus();
    })
}

var s_tst = 1;
var s_tst_div = 0;
function load_test_detail(pid, pin, batch) {
  $.post("pages/patient_test_info.php", {
      uhid: pid,
      pin: pin,
      batch_no: batch,
    },
    function(data, status) {
      $("#patient_info2").html(data);
      $("#patient_info").slideUp(500, function() {
        $("#patient_info2").fadeIn(500, function() {
          $("#test_info").slideDown(200);
          $("#test_id").focus();
          $("html, body").animate({
            scrollTop: 950
          })
        })
      })
      $("html, body").animate({
        scrollTop: 950
      });
      s_tst = 1;
    })
}

function path_select_test(val, e) {
  var unicode = e.keyCode ? e.keyCode : e.charCode;
  if (unicode == 13) {
    var tinfo = $("#test_dis" + s_tst).text();
    var spl = tinfo.split("@");
    // alert(s_tst);
    // alert(spl[1] + spl[2]);
    $("#btn_" + spl[2]).click();
  } else if (unicode == 27) {
    esc(100);
    load_pat_details();
  } else if (unicode == 40) {
    var chk = s_tst + 1;
    var cc = document.getElementById("test_tr" + chk).innerHTML;
    if (cc) {
      s_tst = s_tst + 1;
      $("#test_tr" + s_tst).css({
        'color': '#419641',
        'transform': 'scale(0.95)',
        'font-weight': 'bold',
        'transition': 'all .2s'
      });
      var s_tst1 = s_tst - 1;
      $("#test_tr" + s_tst1).css({
        'color': 'black',
        'transform': 'scale(1.0)',
        'font-weight': 'normal',
        'transition': 'all .2s'
      });
      var z2 = s_tst % 1;
      if (z2 == 0) {
        $("#test_info").scrollTop(s_tst_div)
        s_tst_div = s_tst_div + 38;
      }
    }
  } else if (unicode == 38) {
    var chk = s_tst - 1;
    var cc = document.getElementById("test_tr" + chk).innerHTML;
    if (cc) {
      s_tst = s_tst - 1;
      $("#test_tr" + s_tst).css({
        'color': '#419641',
        'transform': 'scale(0.95)',
        'font-weight': 'bold',
        'transition': 'all .2s'
      });
      var s_tst1 = s_tst + 1;
      $("#test_tr" + s_tst1).css({
        'color': 'black',
        'transform': 'scale(1.0)',
        'font-weight': 'normal',
        'transition': 'all .2s'
      });
      var z2 = s_tst % 1;
      if (z2 == 0) {
        s_tst_div = s_tst_div - 38;
        $("#test_info").scrollTop(s_tst_div);
      }
    }
  } else {
    var pin = $("#opd_td").text();
    if ($("#ipd_td").text() !== '') {
      pin = $("#ipd_td").text()
    }
    // s_tst = 1
    $.post("pages/patient_test_info.php", {
        uhid: $("#uhid_td").text(),
        pin: pin,
        batch_no: $("#batch_td").text(),
        val: val
      },
      function(data, status) {
        $("#patient_info2").html(data);
        $("#test_info").show();
        $("#test_id").focus().val(val);
        var s_tst=1;
      })
  }
}

function esc(time) {
  // testid: $("#acc_id").val();
  // ival: $("#ival").val();
  var testid = $("#acc_id").val();
  if ($("#collapse" + testid).hasClass("in") == true) {
    $("#results" + testid).empty();
    $('#btn_' + testid).click();
    setTimeout(function(){$('#test_id').focus();},time);
  } else {
    $('#p_no').focus();
  }
}


function load_test_param1(val, testid) {
  if ($("#collapse" + testid).hasClass("in") == true) {
    $("#results" + testid).empty();
  } else {
    var tinfo = $("#test_dis" + val).text();
    load_test_param(tinfo, testid);
    // alert(val + "  " + testid);
    $("#ival").val(val);
  }


}

function load_test_param(tinfo, testid) {
  var tinfo = tinfo.trim();
  // if($("#summary_div").is(":show")) {alert("summ show")}
  $.post("pages/pathology_load_param.php", {
      uhid: $("#uhid_td").text().trim(),
      opd_id: $("#opd_td").text().trim(),
      ipd_id: $("#ipd_td").text().trim(),
      batch_no: $("#batch_td").text(),
      tinfo: tinfo,
      user: $("#user").text().trim(),
      level: $("#lavel_id").val(),
      instrument_id: $("#instrument_id").val()
    },
    function(data, status) {
      if ($("#summary_div").is(":visible")) {} else {
        $("#results" + testid).html(data);
      }
      //$(".modal-dialog").css({'width':'1000px'});
      //if($("#mod_chk").val()==0)
      $("#acc_id").val(testid);

      // $("#mod").click();
      $("#add_summ").val("0");
      $("#results").fadeIn(500, function() {
        $("#results").animate({
          scrollTop: 0
        }, "fast");
      })
      setTimeout(function() {
        // $(".t_par: first").focus();
        if($("input.t_par:first").length > 0){
          $("input.t_par:first").focus();
        } else {
          //$("#o0").focus();
          $("#growth_val").focus();
          if($("#growth_val").val()>0)
          {
				load_culture_data();
		  }
        }
      }, 600);

      $("#summary_div").hide();
    })
}

function save_test_param(testid,validate) {
	var accordion = $("#acc_id").val();
	var all = "";
	var par = document.getElementsByClassName("t_par");
	for (var i = 0; i < par.length; i++) {
		if (par[i].id != "Save") {
			if (par[i].value) {
				all = all + par[i].value + "$" + par[i].id + "@";
			}
		}
	}
	var chk = 0;
	if ($("#dlc_check").val() != '') {
		var dlc_tot = 0;
		var dlc = $("#dlc_check").val().split(",");
		for (var i = 0; i < dlc.length; i++) {
			if ($("#" + dlc[i] + "").length > 0) {
				if ($("#" + dlc[i] + "").val() != '') {
					dlc_tot = dlc_tot + parseFloat($("#" + dlc[i] + "").val());
				}
			}
		}
		if (dlc_tot != 100) {
			chk = 1;
			alert("DLC total is: " + dlc_tot + ".It must be 100");
		}
	}
	if (chk == 0) {
		$.post("pages/pathology_save_param.php", {
			uhid: $("#uhid_td").text().trim(),
			opd_id: $("#opd_td").text().trim(),
			ipd_id: $("#ipd_td").text().trim(),
			batch_no: $("#batch_td").text().trim(),
			user: $("#user").text().trim(),
			testid: testid,
			validate: validate,
			tech: $("#techn").val(),
			doc: $("#doc_list").val(),
			instrument_id: $("#instrument_id").val(),
			all: all
		},
		function(data, status) {
			//alert(data);
			var pin = $("#opd_td").text();
			if ($("#ipd_td").text() != '') {
				pin = $("#ipd_td").text();
			}
			//load_test_detail($("#uhid_td").text(), pin, $("#batch_td").text());
			// alert("Saved");
			if(validate=="1")
			{
				alertmsg("Saved & Validated", 1);
			}
			else
			{
				alertmsg("Saved", 1);
			}
			load_normal(testid, all);
			// $("#btn_" + accordion).click();
			$("#collapse" + testid).toggleClass('accordion-body collapse');
			// alert(accordion);
			esc(1000);
			path_select_test($("#test_id").val(),'');
		})
	}
}

function load_normal(tst, all) {
  $.post("pages/pathology_load_normal.php", {
      uhid: $("#uhid_td").text(),
      opd_id: $("#opd_td").text(),
      ipd_id: $("#ipd_td").text(),
      batch_no: $("#batch_td").text(),
      testid: tst,
      all: all
    },
    function(data, status) {
      var rang = data.split("#@rangechk@#");
      for (var i = 0; i < rang.length; i++) {
        if (rang[i]) {
          var nrang = rang[i].split("#@rangechkpenguin@#");
          $("#norm_" + nrang[0] + "").html(nrang[1]);
        }
      }
    })
}

function save_culture(testid,iso_no,validate)
{
	var all = "";
	var par = $(".t_par"+":not([id*='_mic']");
	for (var i = 0; i < par.length; i++) {
		if (par[i].id != "Save") {
			if (par[i].value) {
				var pid = par[i].id;
				var pval = par[i].value;;
				if ($("#" + pid + "_mic").length > 0) {
					if ($("#" + pid + "_mic").val().trim() != '0') {
						all = all + par[i].value.toUpperCase() + " (" + $("#" + pid + "_mic").val() + ")$" + par[i].id + "@";
					} else {
						all = all + par[i].value + "$" + par[i].id + "@";
					}
				} else {
					all = all + par[i].value + "$" + par[i].id + "@";
				}
			}
		}
	}
	
	$.post("pages/pathology_save_param.php", {
		uhid: $("#uhid_td").text().trim(),
		opd_id: $("#opd_td").text().trim(),
		ipd_id: $("#ipd_td").text().trim(),
		batch_no: $("#batch_td").text().trim(),
		user: $("#user").text().trim(),
		testid: testid,
		iso_no:iso_no,
		validate: validate,
		all: all,
		iso_no_total:$("#iso_no_total").val(),
	},
	function(data, status) {
		
		var pin = $("#opd_td").text().trim();
		if ($("#ipd_td").text().trim()!= '') {
			pin = $("#ipd_td").text().trim();
		}
		if(iso_no==0)
		{
			load_test_detail($("#uhid_td").text().trim(),pin,$("#batch_td").text().trim());
		}
		//~ alert("Saved");
		//~ $("#print").focus();
		
		if(validate=="1")
        {
			alertmsg("Saved & Validated", 1);
		}
        else
        {
			alertmsg("Saved", 1);
		}
	})
}

function save_widal(testid,validate) {
	var oval = "";
	var hval = "";
	var ahval = "";
	var bhval = "";
	for (var iw = 0; iw < 6; iw++) {
		var aow = document.getElementById("o" + iw).value;
		oval = oval + "@@" + aow
	}
	for (var iw1 = 0; iw1 < 6; iw1++) {
		var hw = document.getElementById("h" + iw1).value;
		hval = hval + "@@" + hw
	}
	for (var iw2 = 0; iw2 < 6; iw2++) {
		var ahw = document.getElementById("ah" + iw2).value;
		ahval = ahval + "@@" + ahw
	}
	for (var iw3 = 0; iw3 < 6; iw3++) {
		var bhw = document.getElementById("bh" + iw3).value;
		bhval = bhval + "@@" + bhw
	}
	main_save_widal(oval, hval, ahval, bhval,testid,validate);
}

function main_save_widal(ov, hv, ahv, bhv,testid,validate)
{
	$.post("pages/test_param_widal_save.php", {
		ov: ov,
		hv: hv,
		ahv: ahv,
		bhv: bhv,
		uhid: $("#uhid_td").text().trim(),
		opd_id: $("#opd_td").text().trim(),
		ipd_id: $("#ipd_td").text().trim(),
		batch_no: $("#batch_td").text().trim(),
		testid:testid,
		validate:validate,
		imp: $("#imp").val(),
		user: $("#user").text().trim(),
		tech: $("#techn").val(),
		doc: $("#doc_list").val(),
		specimen: $("#specimen").val(),
		incubation_temp: $("#incubation_temp").val(),
		method: $("#method").val(),
	},
	function(data, status) {
		var pin = $("#opd_td").text();
		if ($("#ipd_td").text().trim()!= '') {
			pin = $("#ipd_td").text().trim();
		}
		load_test_detail($("#uhid_td").text().trim(),pin,$("#batch_td").text().trim());
		// alert("Saved");
		if(validate==0)
		{
			alertmsg("Saved", 1);
		}
		else
		{
			alertmsg("Saved & Validated", 1);
		}
		
		esc(1000);
		// $("#print").focus();
	})
}

function widal_rep(tid) {
  var uhid = $("#uhid_td").text();
  var opd_id = $("#opd_td").text();
  var ipd_id = $("#ipd_td").text();
  var batch_no = $("#batch_td").text();

  var url = "pages/report_print_widal.php?uhid=" + uhid + "&opd_id=" + opd_id + "&ipd_id=" + ipd_id + "&batch_no=" +
    batch_no + "&tstid=" + tid;

  var win = window.open(url, '', 'fullScreen=yes,scrollbars=yes,menubar=yes');
}
/*
function check_range(param,val,i)
{
	$.post("pages/pathology_normal_range_new.php",
	{
		uhid:$("#uhid_td").text(),
		param:param,
		val:val,
		typ:'2'
	},
	function(data,satus)
	{
		var data=data.split("#");
		if(data[1]=="Error")
		{
						
			$("#norm"+i).html("<b><i>Out of Range("+data[0]+")</i></b>");
			//$("#norm"+i).slideDown(300);
			$("#norm"+i).css({'display':'inline'});
		}
		else
		{
			//$("#norm"+i).slideUp(300);
			$("#norm"+i).css({'display':'none'});
		}
	})
	
}*/

//~ function dlc_total(param,test)
//~ {

//~ if(test=='2' || test=='21')
//~ {

//~ if(param==142 || param==213)
//~ {
//~ var a=parseInt($("#188").val());
//~ if(!a){a=0;}
//~ var b=parseInt($("#141").val());
//~ if(!b){b=0}
//~ var c=parseInt($("#126").val());
//~ if(!c){c=0}
//~ var d=parseInt($("#127").val());
//~ if(!d){d=0}
//~ var e=parseInt($("#128").val());
//~ if(!e){e=0}
//~ var f=parseInt($("#129").val());
//~ if(!f){f=0}
//~ var g=parseInt($("#142").val());
//~ if(!g){g=0}
//~ var h=parseInt($("#191").val());
//~ if(!h){h=0}
//~ var i=parseInt($("#190").val());
//~ if(!i){i=0}
//~ var j=parseInt($("#189").val());
//~ if(!j){j=0}
//~ var k=parseInt($("#193").val());
//~ if(!k){k=0}
//~ var l=parseInt($("#192").val());
//~ if(!l){l=0}
//~ var m=parseInt($("#213").val());
//~ if(!m){m=0}
//~ var n=parseInt($("#282").val());
//~ if(!n){n=0}

//~ var o=parseInt($("#231").val());
//~ if(!o){o=0}

//~ var p=parseInt($("#570").val());
//~ if(!p){p=0}

//~ var tot=a+b+c+d+e+f+g+h+i+j+k+l+m+n+o+p;
//~ if(tot!=100)
//~ {
//~ alert("DLC Total is "+tot+".It must be 100");
//~ document.getElementById("save").disabled=true;
//~ document.getElementById("188").focus();

//~ }
//~ else
//~ {
//~ document.getElementById("save").disabled=false;
//~ }
//~ }
//~ }
//~ }
function print_report(tid, pos) {
  var tstid = tid;
  var uhid = $("#uhid_td").text();
  var opd_id = $("#opd_td").text();
  var ipd_id = $("#ipd_td").text();
  var batch_no = $("#batch_td").text();
  var user = $("#user").text();

  if (!pos) {
    var url = "pages/report_print_path.php?uhid=" + uhid + "&opd_id=" + opd_id + "&ipd_id=" + ipd_id + "&batch_no=" +
      batch_no + "&tstid=" + tid + "&user=" + user;
  } else if (pos == "2") {
    var url = "pages/report_print_cult.php?uhid=" + uhid + "&opd_id=" + opd_id + "&ipd_id=" + ipd_id + "&batch_no=" +
      batch_no + "&tstid=" + tid + "&user=" + user;
  } else if (pos == "3") {
    var url = "pages/report_print_mont.php?uhid=" + uhid + "&opd_id=" + opd_id + "&ipd_id=" + ipd_id + "&batch_no=" +
      batch_no + "&tstid=" + tid + "&user=" + user;
  } else if (pos == "4") {
    var url = "pages/report_print_re.php?uhid=" + uhid + "&opd_id=" + opd_id + "&ipd_id=" + ipd_id + "&batch_no=" +
      batch_no + "&tstid=" + tid + "&user=" + user;
  } else if (pos == "10") {
    var url = "pages/report_print_widal.php?uhid=" + uhid + "&opd_id=" + opd_id + "&ipd_id=" + ipd_id + "&batch_no=" +
      batch_no + "&tstid=" + tid + "&user=" + user;
  } else if (pos == "11") {
    var url = "pages/report_print_path_pad.php?uhid=" + uhid + "&opd_id=" + opd_id + "&ipd_id=" + ipd_id +
      "&batch_no=" + batch_no + "&tstid=" + tid + "&user=" + user;
  }
  /*
  if(tid=="92")
  {
  	var url="pages/report_print_renal.php?uhid="+uhid+""&opd_id="+opd_id+"&ipd_id="+ipd_id+"&tstid="+tid;
  }
  */
  var url = "pages/report_print_path_group.php?uhid=" + uhid + "&opd_id=" + opd_id + "&ipd_id=" + ipd_id +
    "&batch_no=" + batch_no + "&tests=@" + tid + "&user=" + user;
  var win = window.open(url, '', 'fullScreen=yes,scrollbars=yes,menubar=yes');
}

function export_word() {
  var uhid = $("#uhid_td").text();
  var opd_id = $("#opd_td").text();
  var ipd_id = $("#ipd_td").text();
  var batch_no = $("#batch_td").text();

  var url = "pages/pathology_report_word.php?uhid=" + uhid + "&opd_id=" + opd_id + "&ipd_id=" + ipd_id +
    "&batch_no=" +
    batch_no;
  document.location = url;
}

function select_all() {
  if ($("#select_all").is(":checked")) {
    $(".tst:not(:checked)").each(function() {
      $(this).prop("checked", true);

      var t_prnt = $("#test_print").val();
      var nval = t_prnt + "@" + $(this).val();
      $("#test_print").val(nval);
    })
  } else {
    $(".tst").prop('checked', false);
    $("#test_print").val("");
  }
}

function close_summary() {
  ival = $("#ival").val();
  tinfo = $("#acc_id").val();
  $('#add_summ').val('0');
  load_test_param1(ival, tinfo);
  $("#summary_div").hide();
}

function group_print() {
  // $("#g_print").attr("onclick", "");
  $("#g_print").hide();
  $.post("pages/pathology_print_group.php", {
      uhid: $("#uhid_td").text().trim(),
      opd_id: $("#opd_td").text().trim(),
      ipd_id: $("#ipd_td").text().trim(),
      batch_no: $("#batch_td").text().trim(),
    },
    function(data, status) {
		$("#print").slideUp(300,function(){ $("#print").html(data).slideDown(300); });
      //$("#print").html(data);
      $("#accordion2").hide();
      //$(".modal-dialog").css({'width':'600px'});
      $("#mod").click();
      $("#mod_chk").val("1");
      $("#results").fadeIn(500, function() {
        $("#grp_print_div").focus();
      });

      /*
      $("#results").html(data);
      $("#results").css({'height':'auto','width':'65%','max-height':'600px'});
      var x=$("#results").height()/2;
      var y=$("#results").width()/2;
      document.getElementById("results").style.cssText+="margin-left:-"+y+"px;margin-top:-"+x+"px";
      
      $("#back").fadeIn(100,function(){ $("#results").slideDown(500);})
      */
    })
}

function single_print_test(testid,iso_no) // iso_no=0 for non-culture
{
	 var uhid = $("#uhid_td").text();
	var opd_id = $("#opd_td").text();
	var ipd_id = $("#ipd_td").text();
	var batch_no = $("#batch_td").text();
	var user = $("#user").text();
	var tst = testid;

	var doc = "";
	
	//var url="pages/report_print_path_group.php?uhid="+uhid+"&opd_id="+opd_id+"&ipd_id="+ipd_id+"&batch_no="+batch_no+"&tests="+tst+"&hlt="+tst+"&user="+user+"&sel_doc="+doc;
	var url = "pages/pathology_report_print.php?uhid=" + btoa(uhid) + "&opd_id=" + btoa(opd_id) + "&ipd_id=" + btoa(ipd_id) + "&batch_no=" + btoa(batch_no) + "&tests=" + btoa(tst) + "&hlt=" + btoa(tst) + "&user=" + btoa(user) +"&sel_doc=" + btoa(doc) + "&view=" + btoa(0) + "&iso_no=" + btoa(iso_no);
	var win = window.open(url, '', 'fullScreen=yes,scrollbars=yes,menubar=yes');
}
function group_print_test() {
  var tst = "";

  var uhid = $("#uhid_td").text();
  var opd_id = $("#opd_td").text();
  var ipd_id = $("#ipd_td").text();
  var batch_no = $("#batch_td").text();
  var user = $("#user").text();
  var tst = $("#test_print").val();

  var doc = "";
  var doc_tot = $(".lab_doc_check:checked");
  for (var i = 0; i < doc_tot.length; i++) {
    doc = doc + "," + $(doc_tot[i]).val();
  }

  //var url="pages/report_print_path_group.php?uhid="+uhid+"&opd_id="+opd_id+"&ipd_id="+ipd_id+"&batch_no="+batch_no+"&tests="+tst+"&hlt="+tst+"&user="+user+"&sel_doc="+doc;
  var url = "pages/pathology_report_print.php?uhid=" + btoa(uhid) + "&opd_id=" + btoa(opd_id) + "&ipd_id=" + btoa(
      ipd_id) + "&batch_no=" + btoa(batch_no) + "&tests=" + btoa(tst) + "&hlt=" + btoa(tst) + "&user=" + btoa(user) +
    "&sel_doc=" + btoa(doc) + "&view=" + btoa(0);
  var win = window.open(url, '', 'fullScreen=yes,scrollbars=yes,menubar=yes');
}

function group_print_test_pdf() {
  var tst = "";

  var uhid = $("#uhid_td").text();
  var opd_id = $("#opd_td").text();
  var ipd_id = $("#ipd_td").text();
  var batch_no = $("#batch_td").text();
  var user = $("#user").text();
  var tst = $("#test_print").val();

  var doc = "";
  var doc_tot = $(".lab_doc_check:checked");
  for (var i = 0; i < doc_tot.length; i++) {
    doc = doc + "," + $(doc_tot[i]).val();
  }

  //var url="pages/report_print_path_group_pdf.php?uhid="+uhid+"&opd_id="+opd_id+"&ipd_id="+ipd_id+"&batch_no="+batch_no+"&tests="+tst+"&hlt="+tst+"&user="+user+"&sel_doc="+doc;
  var url = "pages/pathology_report_print_pdf.php?uhid=" + btoa(uhid) + "&opd_id=" + btoa(opd_id) + "&ipd_id=" + btoa(
      ipd_id) + "&batch_no=" + btoa(batch_no) + "&tests=" + btoa(tst) + "&hlt=" + btoa(tst) + "&user=" + btoa(user) +
    "&sel_doc=" + btoa(doc) + "&view=" + btoa(0);
  var win = window.open(url, '', 'fullScreen=yes,scrollbars=yes,menubar=yes');
}

function group_view_test() {
  var tst = "";

  var uhid = $("#uhid_td").text();
  var opd_id = $("#opd_td").text();
  var ipd_id = $("#ipd_td").text();
  var batch_no = $("#batch_td").text();
  var user = $("#user").text();
  var tst = $("#test_print").val();

  var doc = "";
  var doc_tot = $(".lab_doc_check:checked");
  for (var i = 0; i < doc_tot.length; i++) {
    doc = doc + "," + $(doc_tot[i]).val();
  }


  //var url="pages/report_print_path_group.php?uhid="+uhid+"&opd_id="+opd_id+"&ipd_id="+ipd_id+"&batch_no="+batch_no+"&tests="+tst+"&hlt="+tst+"&user="+user+"&view="+1+"&sel_doc="+doc;
  var url = "pages/pathology_report_print.php?uhid=" + btoa(uhid) + "&opd_id=" + btoa(opd_id) + "&ipd_id=" + btoa(
      ipd_id) + "&batch_no=" + btoa(batch_no) + "&tests=" + btoa(tst) + "&hlt=" + btoa(tst) + "&user=" + btoa(user) +
    "&sel_doc=" + btoa(doc) + "&view=" + btoa(1);
  var win = window.open(url, '', 'fullScreen=yes,scrollbars=yes,menubar=yes');
}

function group_print_all() {
  $(".tst").click();
}

function check_form(id, form, dec) {
  var sqr_chk = 0;
  var form = form.split("@");
  var fr = "";
  for (var i = 0; i < form.length; i++) {
    var chk = form[i].split("p");
    if (chk[1] > 0) {
      if ($("#" + chk[1]).length > 0) {
        fr += $("#" + chk[1]).val();
      } else {
        break;
      }
    } else {
      if (form[i] == "sqr_root") {
        fr += "Math.sqrt(";
        var sqr_chk = 1;
      } else {
        fr += form[i];
        if (sqr_chk == 1) {
          fr += ")";
          sqr_chk = 0;
        }


      }
    }
  }

  var res = eval(fr).toFixed(dec);

  $("#" + id + "").val(res);
}

function add_image(tid) {
	$.post("pages/pathology_add_image.php", {
		uhid: $("#uhid_td").text(),
		opd_id: $("#opd_td").text(),
		ipd_id: $("#ipd_td").text(),
		batch_no: $("#batch_td").text(),
		tid: tid
	},
	function(data, status) {
		$("#results1").html(data);
		//if ($("#mod_chk2").val() == 0) {
			$("#modal_dial").animate({
				"width": "700px"
			}, "slow");
			$("#mod2").click();
			$("#results1").fadeIn(200);
			$("#mod_chk2").val("1");
		//}

      var uploader = new qq.FineUploader({
        debug: true,
        element: document.getElementById('fine-uploader'),
        uhid: $("#uhid_td").text(),
        opd_id: $("#opd_td").text(),
        ipd_id: $("#ipd_td").text(),
        batch_no: $("#batch_td").text(),
        request: {
          endpoint: 'pages/load_img.php',
          params: {
            uhid: $("#uhid_td").text(),
            opd_id: $("#opd_td").text(),
            ipd_id: $("#ipd_td").text(),
            batch_no: $("#batch_td").text(),
            tid: tid,
          }
        },
        deleteFile: {
          enabled: true,
          endpoint: '../pad_images',
          params: {
            uhid: $("#uhid_td").text(),
            opd_id: $("#opd_td").text(),
            ipd_id: $("#ipd_td").text(),
            batch_no: $("#batch_td").text(),
          }
        },
        retry: {
          enableAuto: true
        }
      });

      /*
      $("#results1").css({'height':'auto','width':'auto','max-height':'600px'});
      var x=$("#results1").height()/2;
      var y=$("#results1").width()/2+10;
      document.getElementById("results1").style.cssText+="margin-left:-"+y+"px;margin-top:-"+x+"px";
      $("#results1").slideDown(500);
      */

    })

}

function add_summary(tid) {
  $.post("pages/pathology_add_summary.php", {
      uhid: $("#uhid_td").text(),
      opd_id: $("#opd_td").text(),
      ipd_id: $("#ipd_td").text(),
      batch_no: $("#batch_td").text(),
      tid: tid
    },
    function(data, status) {
      $("#results" + tid).html(data);
      $("#summary_div").show();
      // $("#mod2").click();
      //$("#modal_dial").css({"width":"1300px"});
      if ($("#add_summ").val() == 0) {
        //$("#mod2").click();
        $("#results" + tid).fadeIn(200, function() {
          add(tid);
        });
        $("#add_summ").val("1");
      }
      /*
      $("#results1").html(data);
      $("#results1").css({'height':'auto','width':'90%','max-height':'600px'});
      var x=$("#results1").height()/2;
      var y=$("#results1").width()/2+100;
      document.getElementById("results1").style.cssText+="margin-left:-"+y+"px;margin-top:-"+x+"px";
      $("#results1").slideDown(500);
      */
      //
    })
}

// function add() {
//   if (CKEDITOR.instances['article-body']) {
//     CKEDITOR.instances['article-body'].destroy(true);
//   }
//   CKEDITOR.replace('article-body');
//   CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
//   CKEDITOR.config.height = 300;

// }

function add(tid) {
  if (CKEDITOR.instances['article-body' + tid]) {
    CKEDITOR.instances.destroy(true);
  }
  CKEDITOR.replace('article-body' + tid);
  CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
  CKEDITOR.config.height = 300;
}

function add_pad() {
  if (CKEDITOR.instances['pad']) {
    CKEDITOR.instances['pad'].destroy(true);
  }
  CKEDITOR.replace('pad');
  CKEDITOR.config.height = 300;
  $(".modal-dialog").animate({
    width: '1200px'
  }, "slow", function() {
    $("#rad_res").contents().find('body').focus()
  });
}

function save_summary(testid,validate)
{
	var ival = $("#ival").val();
	$.post("pages/pathology_sav_summary.php", {
		uhid: $("#uhid_td").text().trim(),
		opd_id: $("#opd_td").text().trim(),
		ipd_id: $("#ipd_td").text().trim(),
		batch_no: $("#batch_td").text().trim(),
		user: $("#user").text().trim(),
		summ: $("#summary").text().trim(),
		testid: testid,
		validate: validate,
		fdoc: $("#doc_list").val(),
		summ: $("#rad_res").contents().find('body').html()
	},
	function(data, status) {
		$("#summary_div").hide();
		var pin = $("#opd_td").text().trim();
		if ($("#ipd_td").text().trim()!= '') {
			pin = $("#ipd_td").text().trim();
		}
		//$("#cls_sum").click();
		load_test_param1(ival,testid);
		// $("#btn_" + tid).click();
		//load_test_detail($("#uhid_td").text(), pin, $("#batch_td").text());
		//alert("Saved");
		 if(validate=="1")
        {
			alertmsg("Saved & Validated", 1);
		}
        else
        {
			alertmsg("Saved", 1);
		}
	});
}

function data_vis(id) {
  $("#" + id).dblclick();
}

function chk_widal1(sl, id, e) {
  var unicode = e.keyCode ? e.keyCode : e.charCode;
  if (unicode == 13) {
    var a = document.getElementById(id).value;
    if (e.ctrlKey) {
      if (a == "" || a == " ") {
        document.getElementById(id).value = "+VE";
      }
    } else {
      if (a == "" || a == " ") {
        document.getElementById(id).value = "-VE";
      }
    }

    var nsl = parseInt(sl + 1);
    $("[name=t_par" + nsl + "]").focus();
  }
}

function test_print_group(val) {
  if ($("#" + val + "_tst").is(":checked")) {
    var t_prnt = $("#test_print").val();
    var nval = t_prnt + "@" + val;
    $("#test_print").val(nval);
  } else {
    var t_prnt = $("#test_print").val();
    t_prnt1 = t_prnt.replace("@" + val, "");
    $("#test_print").val(t_prnt1);
  }
}

function img_loaddd1() {
  var btnUpload = $('#me2');
  var mestatus = $('#mestatus');
  var files = $('#files1');
  var uhid = $("#uhid_td").text();
  var opd_id = $("#opd_td").text();
  var ipd_id = $("#ipd_td").text();
  var batch_no = $("#batch_td").text();
  new AjaxUpload(btnUpload, {
    action: 'pages/uploadPhoto.php?id=0&uhid=' + uhid + "&opd_id=" + opd_id + "&ipd_id=" + ipd_id + "&batch_no=" +
      batch_no,
    name: 'uploadfile',
    onSubmit: function(file, ext) {
      if (!(ext && /^(jpg|png|jpeg|gif)$/.test(ext))) {
        // extension is not allowed 
        mestatus.text('Only JPG, PNG or GIF files are allowed');
        return false;
      }
      mestatus.html('<img src="../images/ajax-loader.gif" height="16" width="16">');
    },
    onComplete: function(file, response) {
      //On completion clear the status
      mestatus.text('Photo Uploaded Sucessfully!');
      //On completion clear the status
      files.html('');
      //Add uploaded file to list
      if (response === "success") {
        $('<div></div>').appendTo('#files1').html('<img src="../pad_images/ayussh_' + file +
          '" alt="" height="120" width="130" onclick="remove_pic(2)"/><br />').addClass('success1');
      }
    }
  });

}

function img_loaddd() {
  var btnUpload = $('#me');
  var mestatus = $('#mestatus');
  var files = $('#files2');
  var uhid = $("#uhid_td").text();
  var opd_id = $("#opd_td").text();
  var ipd_id = $("#ipd_td").text();
  var batch_no = $("#batch_td").text();
  new AjaxUpload(btnUpload, {
    action: 'pages/uploadPhoto.php?id=1&uhid=' + uhid + "&opd_id=" + opd_id + "&ipd_id=" + ipd_id + "&batch_no=" +
      batch_no,
    name: 'uploadfile',
    onSubmit: function(file, ext) {
      if (!(ext && /^(jpg|png|jpeg|gif)$/.test(ext))) {
        // extension is not allowed 
        mestatus.text('Only JPG, PNG or GIF files are allowed');
        return false;
      }
      mestatus.html('<img src="../images/ajax-loader.gif" height="16" width="16">');
    },
    onComplete: function(file, response) {
      //On completion clear the status
      mestatus.text('Photo Uploaded Sucessfully!');
      //On completion clear the status
      files.html('');
      //Add uploaded file to list
      if (response === "success") {
        $('<div></div>').appendTo('#files2').html('<img src="../pad_images/ayussh_' + file +
          '" alt="" height="120" width="130" onclick="remove_pic(1)"/><br />')
      }
    }
  });

}

function print_report_image(tid) {

  var uhid = $("#uhid_td").text();
  var opd_id = $("#opd_td").text();
  var ipd_id = $("#ipd_td").text();
  var batch_no = $("#batch_td").text();
  var user = $("#user").text();

  var url = "pages/report_print_path_image.php?uhid=" + uhid + "&opd_id=" + opd_id + "&ipd_id=" + ipd_id +
    "&batch_no=" + batch_no + "&tests=" + tid + "&user=" + user
  var win = window.open(url, '', 'fullScreen=yes,scrollbars=yes,menubar=yes');

}

function remove_pic(tid, id) {
  var uhid = $("#uhid_td").text();
  var opd_id = $("#opd_td").text();
  var ipd_id = $("#ipd_td").text();
  var batch_no = $("#batch_td").text();

  if (confirm("Do you really want to remove this image?")) {
    $.post("pages/pathology_remove_img.php", {
        uhid: uhid,
        opd_id: opd_id,
        ipd_id: ipd_id,
        batch_no: batch_no,
        tid: tid,
        slno: id
      },
      function(data, status) {
        $("#img_par_" + id + "").fadeOut(300);
      })
  }
}

function show_upload(id) {
  if (id == "a") {
    $("#uploaded_img").slideUp(200);
    $("#fine-uploader").slideDown(200);
    $("#a").val("View");
    $("#a").attr("id", "b");
  } else {
    $("#uploaded_img").slideDown(200);
    $("#fine-uploader").slideUp(200);
    $("#" + id).val("Upload");
    $("#" + id).attr("id", "a");
  }
}
var g_tr = 0;
var g_scrl = 0;

function select_test_grp(e) {
  e.preventDefault();
  var unicode = e.keyCode ? e.keyCode : e.charCode;
  if (unicode == 13) {
    $("[name=grp_td" + g_tr + "]").click();
  } else if (unicode == 40) {
    var chk = g_tr + 1;
    var cc = document.getElementById("grp_tr" + chk).innerHTML;
    if (cc) {
      g_tr = g_tr + 1;
      $("#grp_tr" + g_tr).css({
        'color': '#419641',
        'transform': 'scale(0.95)',
        'font-weight': 'bold',
        'transition': 'all .2s'
      });
      var g_tr1 = g_tr - 1;
      $("#grp_tr" + g_tr1).css({
        'color': 'black',
        'transform': 'scale(1.0)',
        'font-weight': 'normal',
        'transition': 'all .2s'
      });
      var z2 = g_tr % 1;
      if (z2 == 0) {
        $("#grp_print_div").scrollTop(g_scrl)
        g_scrl = g_scrl + 38;
      }
    }
  } else if (unicode == 38) {
    var chk = g_tr - 1;
    var cc = document.getElementById("grp_tr" + chk).innerHTML;
    if (cc) {
      g_tr = g_tr - 1;
      $("#grp_tr" + g_tr).css({
        'color': '#419641',
        'transform': 'scale(0.95)',
        'font-weight': 'bold',
        'transition': 'all .2s'
      });
      var g_tr1 = g_tr + 1;
      $("#grp_tr" + g_tr1).css({
        'color': 'black',
        'transform': 'scale(1.0)',
        'font-weight': 'normal',
        'transition': 'all .2s'
      });
      var z2 = g_tr % 1;
      if (z2 == 0) {
        g_scrl = g_scrl - 38;
        $("#grp_print_div").scrollTop(g_scrl)

      }
    }
  }
}

function select_textbox(e) {
  var unicode = e.keyCode ? e.keyCode : e.charCode;
  if (unicode == 13) {
    $("[name='t_par1']").focus();
  }
}

function load_note(tid) {
  $.post("pages/pathology_add_note.php", {
      uhid: $("#uhid_td").text(),
      opd_id: $("#opd_td").text(),
      ipd_id: $("#ipd_td").text(),
      batch_no: $("#batch_td").text(),
      tid: tid
    },
    function(data, status) {
      $("#results1").html(data);
      $("#modal_dial").css({
        "width": "800px"
      });
      if ($("#mod_chk2").val() == 0) {
        $("#results1").slideDown(300, function() {
          $("#mod2").click();
          $("#test_note").focus();
        });
        $("#mod_chk2").val("0");
      }
    })
}

function save_note(tid, val) {
  $.post("pages/pathology_save_note.php", {
      uhid: $("#uhid_td").text(),
      opd_id: $("#opd_td").text(),
      ipd_id: $("#ipd_td").text(),
      batch_no: $("#batch_td").text(),
      tid: tid,
      note: $("#test_note").val()
    },
    function(data, status) {
      alert(data);
    })
}
/*function group_wise_print()
{
	var test_l="";
	var grp_t=$(".grp_t_l");
	for(var i=0;i<=grp_t.length;i++)
	{
		test_l=test_l+"@"+$(grp_t[i]).text();
	}
	
	var uhid=$("#uhid_td").text();
	var opd_id=$("#opd_td").text();
	var ipd_id=$("#ipd_td").text();
	var user=$("#user").text();
	
	var url="pages/report_print_path_group_wise.php?uhid="+uhid+"&opd_id="+opd_id+"&ipd_id="+ipd_id+"&tests="+test_l+"&user="+user
	var win=window.open(url,'','fullScreen=yes,scrollbars=yes,menubar=yes');
}*/
function check_entry_range(id, rng, i) {
  var r = rng.split("-");
  if ($("#" + id + "").val() < parseInt(r[0]) || $("#" + id + "").val() > parseInt(r[1])) {
    $("#" + id + "").css({
      'border': '3px solid red'
    });
    $("#" + id + "").attr("name", "aa");
  } else {
    $("#" + id + "").css({
      'border': '1px solid #ccc'
    });
    $("#" + id + "").attr("name", "tpar" + i);
  }
}

function load_org_list(val) {
	$.post("pages/pathology_load_organism_list.php", {
		org: $("#310").val(),
		growth_val:$("#growth_val").val(),
		val: val
	},
	function(data, status) {
		$("#data_list_cult_" + val + "").html(data);
	})
}

function cult_tab_click(iso_no)
{
	$.post("pages/pathology_load_culture_data.php", {
		type:"load_culture_iso_data",
		iso_no:iso_no,
		
		uhid: $("#uhid_td").text().trim(),
		opd_id: $("#opd_td").text().trim(),
		ipd_id: $("#ipd_td").text().trim(),
		batch_no: $("#batch_td").text().trim(),
		testid: $("#sel_testid").val().trim(),
	},
	function(data, status) {
		$(".tab_iso_cls").html("");
		$("#tab_iso"+iso_no).html(data);
		
		setTimeout(function(){
			//$("input.t_par:first").focus();
		},100);
	})
}

function load_culture_data()
{
	if($("#growth_val").val()==2) // Growth
	{
		$("#iso_field").show();
	}
	else
	{
		$("#iso_field").hide();
		$("#growth_val").val("1");
		$("#iso_no_total").val("0");
	}
	
	$.post("pages/pathology_load_culture_data.php", {
		type:"load_culture_data",
		growth_val:$("#growth_val").val(),
		iso_no_total:$("#iso_no_total").val(),
		
		uhid: $("#uhid_td").text().trim(),
		opd_id: $("#opd_td").text().trim(),
		ipd_id: $("#ipd_td").text().trim(),
		batch_no: $("#batch_td").text().trim(),
		testid: $("#sel_testid").val().trim(),
	},
	function(data, status) {
		$("#load_culture_data_div").html(data);
		
		setTimeout(function(){
			if($("#iso_no_total").val()>0)
			{
				$("#iso_no_selected").val("1");
			}
			else
			{
				$("#iso_no_selected").val(0);
			}
			
			cult_tab_click(1);
		},100);
	})
}

function growth_val_up(e)
{
	if(e.which==13 && $("#growth_val").val()==1)
	{
		setTimeout(function(){
			$("input.t_par:first").focus();
		},100);
	}
	if(e.which==13 && $("#growth_val").val()==2)
	{
		$("#iso_no_total").focus();
	}
}

function iso_no_total_up(e)
{
	if(e.which==13 && $("#iso_no_total").val()>0)
	{
		setTimeout(function(){
			$("input.t_par:first").focus();
		},100);
	}
}

function repeat_param_save(uhid,opd_id,ipd_id,batch_no,testid,paramid,iso_no,param_name)
{
	bootbox.dialog({
		message: "<h5>Are you sure want to repeat "+param_name+" parameter ?</h5>",
		buttons: {
			cancel: {
				label: '<i class="icon-remove"></i> No',
				className: "btn btn-inverse",
				callback: function() {
					$("#repeat_param"+testid+"tst"+paramid).prop("checked", false);
					bootbox.hideAll();
				}
			},
			confirm: {
				label: '<i class="icon-ok"></i> Yes',
				className: "btn btn-danger",
				callback: function() {
					repeat_param_save_reason(uhid,opd_id,ipd_id,batch_no,testid,paramid,iso_no,param_name);
				}
			}
		}
	});
}
function repeat_param_save_reason(uhid,opd_id,ipd_id,batch_no,testid,paramid,iso_no,param_name)
{
	bootbox.dialog({
		message: "Reason:<input type='text' id='repeat_reason' autofocus />",
		title: "Parameter Repeat Reason",
		buttons: {
			main: {
				label: "Save",
				className: "btn-primary",
				callback: function() {
				if($("#repeat_reason").val()!="")
				{
					$.post("pages/pathology_repeat_param_data.php",
					{
						type:"repeat_parameter_save",
						uhid:uhid,
						opd_id:opd_id,
						ipd_id:ipd_id,
						batch_no:batch_no,
						testid:testid,
						paramid:paramid,
						iso_no:iso_no,
						repeat_reason:$("#repeat_reason").val(),
					},
					function(data,status)
					{
						//alert(data);
						var res=data.split("@$@");
						if(res[0]=="404")
						{
							alertmsg(res[1], 0);
							
							$("#repeat_param"+testid+"tst"+paramid).prop("checked", false);
						}else
						{
							alertmsg(res[1], 1);
							$("#repeat_param_label"+testid+"tst"+paramid).hide();
							$("#repeat_param_view_btn"+testid+"tst"+paramid).show();
						}
					})
				}else
				{
					$("#repeat_param"+testid+"tst"+paramid).prop("checked", false);
					bootbox.alert("Reason cannot blank");
				}
				
			  }
			}
		}
	});
}
function repeat_param_view(uhid,opd_id,ipd_id,batch_no,testid,paramid,iso_no)
{
	$.post("pages/pathology_repeat_param_data.php",
	{
		type:"repeat_param_view",
		uhid:uhid,
		opd_id:opd_id,
		ipd_id:ipd_id,
		batch_no:batch_no,
		testid:testid,
		paramid:paramid,
		iso_no:iso_no,
	},
	function(data,status)
	{
		$("#result_repeat").html(data);
		$("#modal_btn_repeat").click();
		
	})
}

function search(inputVal)
{
	var table = $('#tblData');
	table.find('tr').each(function(index, row)
	{
		var allCells = $(row).find('td');
		if(allCells.length > 0)
		{
			var found = false;
			allCells.each(function(index, td)
			{
				var regExp = new RegExp(inputVal, 'i');
				if(regExp.test($(td).text()))
				{
					found = true;
					return false;
				}
			});
			if(found == true)
			{
				$("#no_record").text("");
				$(row).show();
			}else{
				$(row).hide();
				var n = $('tr:visible').length;
				if(n==1)
				{
					$("#no_record").text("No matching records found");
				}else
				{
					$("#no_record").text("");
				}
			}
			//if(found == true)$(row).show();else $(row).hide();
		}
	});
}
</script>
<style>
#myModal,
#myModal2 {
  left: 25%;
  width: 90%;
}

#myModal_repeat
{
	left: 25%;
	width: 90%;
}
.accordion-heading {
  background: #efefef;
  font-weight: 700;
  border-radius: 3px;
  border-bottom: 1px solid #cdcdcd;
}

.modal.fade.in {
  top: 1%;
}

.modal-body {
  max-height: 520px;
}

.btn_round_msg {
  color: #000;
  padding: 2px;
  border-radius: 7em;
  padding-right: 10px;
  padding-left: 10px;
  box-shadow: inset 1px 1px 0 rgba(0, 0, 0, 0.6);
  transition: all ease-in-out 0.2s;
}

.btn_round_msg1 {
  padding: 5px;
  border-radius: 7em;
  padding-right: 10px;
  padding-left: 10px;
}

.red {
  background-color: #d59a9a;
}

.green {
  background-color: #9dcf8a;
}

.yellow {
	background-color: yellow;
}
.grey {
	background-color: #666666;
}

.grey_btn {
  background-color: #89898D;
}

tr.green:hover td span,
tr.red:hover td span,
tr.yellow:hover td span,
tr.grey:hover td span {
  padding: 8px;
  padding-right: 12px;
  padding-left: 12px;
}

.normal_range {
  display: none;
}

textarea {
  height: 150px;
  width: 800px;
}

.cke_textarea_inline {
  padding: 10px;
  height: 380px;
  overflow: auto;
  border: 1px solid gray;
  -webkit-appearance: textfield;
}

.td_widal {
  width: 140px;
}

.modal {
  z-index: 999 !important;
}

.modal-backdrop {
  z-index: 990 !important;
}

#pat_det_info td b {
  font-size: 15px;
}

#pat_det_info td span {
  font-size: 14px;
}

.btn_round {
  color: #000;
  padding: 3px;
  border-radius: 7em;
  //background-color: #d59a9a; #9dcf8a;
  padding-right: 7px;
  padding-left: 7px;
  box-shadow: inset 1px 1px 0 rgba(0, 0, 0, 0.6);
  transition: all ease-in-out 0.2s;
}

.side_name
{
	border: 1px solid #ddd;
	background-color: #fff;
	padding: 4px;
	position: absolute;
	font-weight:bold;
}
</style>
