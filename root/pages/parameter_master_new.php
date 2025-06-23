<!--header-->
<div id="content-header">
  <div class="header_div"> <span class="header"> Parameter Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
  <table class="table table-bordered table-condensed">
    <tr>
      <td>
        Search:<input type="text" id="par" name="par" onKeyUp="load_param(this.value,event)" autofocus />
      </td>
      <td>
        <select id="test_param" style="width:400px !important" onchange="load_param('','')">
          <option value="0">--All Test--</option>
          <?php
                    $tst=mysqli_query($link, "select * from testmaster WHERE `testname`!='' order by testname");
          while($t=mysqli_fetch_array($tst)) {
              echo "<option value='$t[testid]'>$t[testname]</option>";
          }
          ?>
        </select>
      </td>
      <td>
        <!-- <span id="in1">
          <button" id="new" value="Create New Parameter" class="btn btn-info" onClick="new_param()"><i
              class="icon-plus"></i> Add New</button>

        </span> -->
        <span id="in1">
          <button" id="new" value="Create New Parameter" class="btn btn-info" onClick="load_param_info('0')"><i
              class="icon-plus"></i> Add New</button>
        </span>
        <span id="in2" style="display:none">
          <input type="text" id="new_param" size="40" /> <input type="button" value="Add" class="btn btn-default"
            onClick="add_param()" /> <input type="button" value="Cancel" class="btn btn-danger"
            onClick="new_param(1)" />
        </span>
      </td>
    </tr>
    <tr>
      <td colspan="3">
        <div id="params" style="height:450px;overflow:auto;overflow-x:hidden">
          <!--<table class="table table-bordered table-condensed">
						<th>ID</th>
						<th>Name</th>
						<th></th>
						<th></th>
						<?php
                  $i=1;
          $par=mysqli_query($GLOBALS["___mysqli_ston"], "select * from  Parameter_old order by Name");
          while($p=mysqli_fetch_array($par)) {
              echo "<tr id='param$i'><td id='par_id$i'>$p[ID]</td><td>$p[Name]</td><td><input type='button' class='btn btn-info' value='Update' onclick='load_param_info($p[ID])' /></td><td><input type='button' class='btn btn-danger' value='Delete' onclick='delete_para($p[ID])' /></td></tr>";
              $i++;
          }
          ?>
					</table>-->
        </div>
      </td>
    </tr>
  </table>
  <!--<div id="back" onClick="$('#results').slideUp(500);$(this).fadeOut(100);$('#pinfo').fadeOut(200)"></div>-->

  <input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none" />
  <div id="myModal" class="modal fade" role="dialog" id="myModal">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
          <h4 class="modal-title">Parameters</h4>
        </div>
        <div class="modal-body">
          <div id="results"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!--
	<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div id="results"></div>
				</div>
			</div>
		</div>
	</div>-->

  <input type="button" data-toggle="modal" data-target="#myModal2" id="mod2" style="display:none" />

  <div class="modal fade" id="myModal2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
          <h4 class="modal-title">Parameter Unit</h4>
        </div>
        <div class="modal-body">
          <div id="results1"></div>
        </div>
      </div>
    </div>
  </div>

  <input type="button" data-toggle="modal" data-target="#myModal3" id="mod3" style="display:none" />

  <div class="modal fade" id="myModal3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body">
          <div id="results2"></div>
        </div>
      </div>
    </div>
  </div>
</div>
<style>
.under_line {
  text-decoration: underline;
  padding-left: 15px;
  font-weight: bold;
  font-size: 15px
}

#myModal,
#myModal2,
#myModal3 {
  left: 28%;
  width: 90%;
}
</style>
<div id="loader" style="margin-top:-10%;display:none"></div>
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<!-- Select2 Plugin -->
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>

<script>
$(document).ready(function() {
  load_param('', '');
  $("#test_param").select2({
    theme: "classic"
  });
});

var sel_pser = 1;
var sel_divser = 0;

function load_param(val, e) {
  var unicode = e.keyCode ? e.keyCode : e.charCode;
  if (unicode == 13) {
    var par_id = document.getElementById("par_id" + sel_pser).innerHTML;
    load_param_info(par_id)
    sel_pser = 1;
    sel_divser = 0;
  } else if (unicode == 40) {
    var chk = sel_pser + 1;
    var cc = document.getElementById("param" + chk).innerHTML;
    if (cc) {
      sel_pser = sel_pser + 1;
      $("#param" + sel_pser).css({
        'color': '#419641',
        'transform': 'scale(0.95)',
        'font-weight': 'bold',
        'transition': 'all .2s'
      });
      var sel_pser1 = sel_pser - 1;
      $("#param" + sel_pser1).css({
        'color': 'black',
        'transform': 'scale(1.0)',
        'font-weight': 'normal',
        'transition': 'all .2s'
      });
      var z2 = sel_pser % 1;
      if (z2 == 0) {
        $("#params").scrollTop(sel_divser)
        sel_divser = sel_divser + 38;
      }
    }
  } else if (unicode == 38) {
    var chk = sel_pser - 1;
    var cc = document.getElementById("param" + chk).innerHTML;
    if (cc) {
      sel_pser = sel_pser - 1;
      $("#param" + sel_pser).css({
        'color': '#419641',
        'transform': 'scale(0.95)',
        'font-weight': 'bold',
        'transition': 'all .2s'
      });
      var sel_pser1 = sel_pser + 1;
      $("#param" + sel_pser1).css({
        'color': 'black',
        'transform': 'scale(1.0)',
        'font-weight': 'normal',
        'transition': 'all .2s'
      });
      var z2 = sel_pser % 1;
      if (z2 == 0) {
        sel_divser = sel_divser - 38;
        $("#params").scrollTop(sel_divser);
      }
    }
  } else {
    $("#loader").show();
    $.post("pages/parameter_master_ajax.php", {
        val: val,
        test: $("#test_param").val()
      },
      function(data, status) {
        $("#params").html(data);
        $("#loader").hide();
      })
  }
}

function load_param_info(id) {
  $.post("pages/parameter_detail.php", {
      id: id
    },
    function(data, status) {
      $("#results").html(data);
      $(".modal-dialog").css({
        'width': '1200px'
      })
      $("#mod").click();

      $("#results").fadeIn(500, function() {
        $("#pinfo").fadeIn(200);
      });

      /*
      $("#back").slideDown(100);
      $("#results").html(data);
      $("#results").css({'width':'800px','height':'400px'});
      var w=$("#results").width()/2;
      var h=$("#results").height()/2;
      document.getElementById("results").style.cssText+="margin-left:-"+w+"px;margin-top:-"+h+"px";
      $("#results").slideDown(500,function(){ $("#pinfo").fadeIn(200)});
      */
    })
}

function load_more_info(val, id) {
  $.post("pages/parameter_more_info.php", {
      val: val,
      id: id
    },
    function(data, status) {
      $("#results1").html(data);
      if (!$("#myModal2").hasClass("fade in")) {
        $("#mod2").click();
        $("#results1").fadeIn(200);
      }
      /*
      $("#results1").html(data);
      if(id)
      {
      	$("#results1").css({'width':'95%','height':'87%'});
      }
      else
      {
      	$("#results1").css({'width':'500px','height':'600px'});
      }
      var w=$("#results1").width()/2+80;
      var h=$("#results1").height()/2;
      document.getElementById("results1").style.cssText+="margin-left:-"+w+"px;margin-top:-"+h+"px";
      $("#results1").slideDown(500);
      */
    })
}

function load_unit(id, name) {
  $("#unit").val(name);
  $("#unit_id").val(id);
  $("#mod2").click();
}

function load_restype(id, name) {
  $("#options").val(name);
  $("#options_id").val(id);
  $("#mod2").click();
}

function hid_div(e) {
  var unicode = e.keyCode ? e.keyCode : e.charCode;
  if (unicode == 27) {
    var res1 = $("#results1").css('display');
    if (res1 == "block") {
      var res2 = $("#results2").css('display');
      if (res2 == "block") {
        $("#results2").slideUp(500);
        $("#mod2").click();
      } else {
        $("#results1").slideUp(500);
        $("#mod").click();
      }
    } else {
      $('#results').slideUp(500);
      $("#back").fadeOut(100);
      $('#pinfo').fadeOut(200)
    }
  }
}

function disable_opt(val) {
  $("#male").attr("disabled", "");
  $("#female").attr("disabled", "");
  $("#than").attr("disabled", "");
  $("#than1").attr("disabled", "");
  $("#val1").attr("disabled", "");
  $("#age").attr("disabled", "");
  $("#val2").attr("disabled", "");
  $("#age2").attr("disabled", "");
  $("#and").attr("disabled", "");
  if (val == 1) {
    $("#male").attr("disabled", "disabled");
    $("#female").attr("disabled", "disabled");
  } else if (val == 3) {
    $("#than").attr("disabled", "disabled");
    $("#than1").attr("disabled", "disabled");
    $("#val1").attr("disabled", "disabled");
    $("#age").attr("disabled", "disabled");
    $("#val2").attr("disabled", "disabled");
    $("#age2").attr("disabled", "disabled");
    $("#and").attr("disabled", "disabled");
  }

}

function display_num(val) {
  if (val == 1) {
    $("#numer").fadeIn(200);
    $("#textual").fadeOut(200);
  } else {
    $("#numer").fadeOut(200);
    $("#textual").fadeIn(200);
  }
}

function add_ref() {
  var dep = document.getElementById("dep").value;
  var selIndex = document.getElementById("dep").selectedIndex;
  var selValue = document.getElementById("dep").options[selIndex].innerHTML;


  var than = document.getElementById("than").value;
  var val1 = document.getElementById("val1").value;
  var age = document.getElementById("age").value;

  var and = document.getElementById("and").value;

  var than2 = document.getElementById("than1").value;
  var val2 = document.getElementById("val2").value;
  var age2 = document.getElementById("age2").value;


  var male = document.getElementById("male");
  var female = document.getElementById("female");
  if (male.checked) {
    var sex = "Male";
  } else if (female.checked) {
    var sex = "Female";
  }
  if (document.getElementById("n_range").checked) {
    var tab = document.getElementById("num_table");
    var tbody = document.createElement("tbody");
    var tr = document.createElement("tr");
    var td1 = document.createElement("td");
    var td2 = document.createElement("td");
    var td3 = document.createElement("td");
    var td4 = document.createElement("td");
    var td5 = document.createElement("td");
    var td6 = document.createElement("td");
    var td7 = document.createElement("td");

    tr.appendChild(td1);
    tr.appendChild(td2);
    tr.appendChild(td3);
    tr.appendChild(td4);
    tr.appendChild(td5);
    tr.appendChild(td6);
    tr.appendChild(td7);

    tbody.appendChild(tr);
    tab.appendChild(tbody);

    var con = "";
    if (dep == 1) {
      con = than + " " + val1 + " " + age + " " + and + " " + than2 + " " + val2 + " " + age2;
    } else if (dep == 2) {
      con = than + " " + val1 + " " + age + " " + and + " " + than2 + " " + val2 + " " + age2 + "/" + sex;
    } else if (dep == 3) {
      con = sex;
    } else if (dep == 4) {
      con = sex;
    } else if (dep == 6) {
      con = than + " " + val1 + " " + age + " " + and + " " + than2 + " " + val2 + " " + age2;
    }

    td1.innerHTML = selValue;
    td2.innerHTML = con;

    td3.innerHTML = "Min Value";
    td4.innerHTML = $("#num_min").val();
    td5.innerHTML = "Max Value";
    td6.innerHTML = $("#num_max").val();
    td7.innerHTML = "";
    tbody.onclick = function() {
      $(this).remove();
    };

    var div = "<div class='new_range' style='display:none'>@" + dep + "@" + con + "@Min Value";
    div += "@" + $("#num_min").val() + "@Max Value" + "@" + $("#num_max").val() + "#";

    td6.innerHTML += div;
  }
}

function range_database(id) {
  var range = "#";
  var tot = document.getElementsByClassName("new_range");
  for (var i = 0; i < tot.length; i++) {
    range = range + tot[i].innerHTML;
  }

  $.post("pages/parameter_save_range.php", {
      id: id,
      range: range,
    },
    function(data, status) {
      bootbox.alert(data);
    })
}

function save_param(id) {
  $.post("pages/parmeter_save_ajax.php", {
      id: id,
      name: $("#pname").val(),
      rtype: $("#rtype").val(),
      unit: $("#unit_id").val(),
      option: $("#options_id").val(),
      meth: $("#meth").val(),
      e_range: $("#ent_range").val(),
      samp: $("#samp").val(),
      vaccu: $("#vacc").val(),
    },
    function(data, status) {
      bootbox.alert("Saved");
      // alert(data);
      var p_val = $("#par").val();
      load_param(p_val, '');
    })
}

function new_param(val) {
  if (!val) {
    $("#in1").fadeOut(200, function() {
      $("#in2").fadeIn(200);
      $("#new_param").val("");
      $("#new_param").focus()
    })
  } else {
    $("#in2").fadeOut(200, function() {
      $("#in1").fadeIn(200)
    })
  }
}

function add_param() {
  $.post("pages/parmeter_save_ajax.php", {
      name: $("#new_param").val()
    },
    function(data, status) {
      $("#in2").fadeOut(200, function() {
        $("#in1").fadeIn(200);
        $("#par").focus()
      })

      $.post("pages/parameter_master_ajax.php", {}, function(data, status) {
        $("#params").html(data);
      })
    })
}

function show_option(id) {
  $.post("pages/parameter_option.php", {
      id: id
    },
    function(data, status) {
		//alert(data);
      $("#option_val").html(data);
      var op_name = $("#" + id + "").text();
      $("#option_id").val(op_name + "@" + id);

      $(".under_line").removeClass("under_line");
      $("#" + id + "").addClass("under_line")
    })
}

function save_option() {
  var op_val = $("#option_id").val().split("@");
  var optText=$("#optionList option:selected").text();
  $("#options").val(optText);
  $("#options_id").val(op_val[1]);
  bootbox.alert("Selected");
  $("#mod2").click();
}

function add_para_form(id, name) {
  var c = 1;
  var t = document.getElementsByClassName("p_id");
  for (var j = 0; j < t.length; j++) {
    if (t[j].value == id) {
      c = 0;
      break;
    }
  }

  if (c) {
    var par = document.getElementById("formula_tab");
    var tbody = document.createElement("tbody");
    var tr = document.createElement("tr");
    var td = document.createElement("td");
    var td1 = document.createElement("td");

    td.innerHTML = name + "<input type='hidden' value=" + id + " class='p_id' />";
    td1.setAttribute("class", "refr");

    tr.appendChild(td);
    tr.appendChild(td1);

    tbody.appendChild(tr);
    td.onclick = function() {
      $(tbody).remove();
      assign_refr()
    }

    par.appendChild(tbody);

    assign_refr();
  }
}

function assign_refr() {
  var ref = document.getElementsByClassName("refr");
  var pid = document.getElementsByClassName("p_id");

  for (var i = 0; i < ref.length; i++) {
    var j = i + 1;
    $(ref[i]).text(j);
    pid[i].id = "pid_" + j
  }

}
//~ function save_formula(id)
//~ {
//~ var all="";
//~ var pid=document.getElementsByClassName("p_id");
//~ for(var i=0;i<pid.length;i++)
//~ {
//~ all=all+"#"+pid[i].id+"@"+pid[i].value;
//~ }

//~ $.post("pages/parameter_save_formula.php",
//~ {
//~ id:id,
//~ all:all,
//~ formula:$("#forml").val()
//~ },
//~ function(data,status)
//~ {
//~ bootbox.alert("Formula Saved");
//~ })
//~ }
function sel_restype(val, id) {
  //var opt_click=$("#opt_tr").attr("onclick");
  //-------------Unit---------------
  if (val == 1) {
    document.getElementById("unit_tr").setAttribute("onclick", "load_more_info(1)");

    $("#deci_span").css({
      'display': 'block'
    })
  } else {
    document.getElementById("unit_tr").setAttribute("onclick", "");
    $("#unit").val("");
    $("#unit_id").val("");

    $("#deci_span").css({
      'display': 'none'
    })
  }

  //-------------Option---------------
  if (val == 2 || val == 11) {
    document.getElementById("opt_tr").setAttribute("onclick", "load_more_info(2," + id + ")");
  } else {
    document.getElementById("opt_tr").setAttribute("onclick", "");
    $("#options").val("");
    $("#options_id").val("");
  }

  //------------Formula---------------
  if (val == 1) {
    document.getElementById("form_tr").setAttribute("onclick", "load_more_info(4," + id + ")");

  } else {
    document.getElementById("form_tr").setAttribute("onclick", "");

  }


  if (val == 0) {
    document.getElementById("ref_tr").setAttribute("onclick", "");
  } else {
    document.getElementById("ref_tr").setAttribute("onclick", "load_more_info(3," + id + ")");
  }
}

function close_div() {
  var res1 = $("#results1").css('display');
  if (res1 == "block") {
    $("#results1").slideUp(500);
  } else {
    $('#results').slideUp(500);
    $("#back").fadeOut(100);
    $('#pinfo').fadeOut(200)
  }
}

function close_div1(id) {
  if (id) {
    $("#results2").slideUp(500);
  } else {
    $("#results1").fadeOut(500);
  }
}

function save_unit() {
  if ($("#n_unit").val() == "") {
    alert("Insert Unit Name");
    return false;
  }
  $.post("pages/parameter_save_unit.php", {
      unit: $("#n_unit").val()
    },
    function(data, status) {
      load_more_info(1);
    })
}

function normal_add(id) {
  $.post("pages/parameter_master_normal.php", {
      id: id
    },
    function(data, status) {
      $("#add_range").html(data);
      $("#display_range").slideUp(200);
      $("#add_range").slideDown(200);
      /*
      $("#results2").html(data);
      $(".modal-dialog").css({'width':'1200px'})
      $("#mod3").click();
      
      $("#results2").fadeIn(500);
      */
      /*
      $("#results2").css({'width':'auto','height':'auto'});
      
      var w=$("#results2").width()/2+25;
      var h=$("#results2").height()/2;
      document.getElementById("results2").style.cssText+="margin-left:-"+w+"px;margin-top:-"+h+"px";
      $("#results2").slideDown(500);
      */
    })
}

function normal_update_opt(sl, id, elem) {
  var val = $(elem).val();
  if (val == 1) {
    update_range(sl, id);
  } else if (val == 2) {
    update_norm_stat(sl, id);
  } else if (val == 3) {
    remove_range(sl, id);
  }
}


function normal_status_show(val) {
  if (val == "show_norm") {
    $(".norm_stat_1").show();
    $("#" + val + "").text("Hide Inactive Ranges");
    $("#" + val + "").prop("id", "hide_norm");
  } else {
    $(".norm_stat_1").hide();
    $("#" + val + "").text("Show Inactive Ranges");
    $("#" + val + "").prop("id", "show_norm");
  }

  //~ if($("#normal_stat_show").prop("checked")==false)
  //~ {
  //~ $(".norm_stat_1").show();
  //~ $("#normal_stat_show").prop("checked",true)
  //~ }
  //~ else if($("#normal_stat_show").prop("checked")==true)
  //~ {
  //~ $(".norm_stat_1").hide();
  //~ $("#normal_stat_show").prop("checked",false)
  //~ }
}

function update_norm_stat(sl, id) {

  bootbox.dialog({
    message: "<h5>Do you really want to change the Status?</h5>",
    buttons: {
      cancel: {
        label: '<i class="icon-remove"></i> No',
        className: "btn btn-inverse",
        callback: function() {
          bootbox.hideAll();
        }
      },
      confirm: {
        label: '<i class="icon-ok"></i> Yes',
        className: "btn btn-info",
        callback: function() {
          $.post("pages/parameter_master_normal_status.php", {
              slno: sl
            },
            function(data, status) {
              bootbox.hideAll();
              load_more_info('3', id);
            })
        }
      }
    }
  });
}


function update_range(sl, id) {
  $.post("pages/parameter_master_normal.php", {
      sl: sl,
      id: id
    },
    function(data, status) {
      $("#add_range").html(data);
      $("#display_range").slideUp(200);
      $("#add_range").slideDown(200);
    })
}

function normal_save(val) {
  $.post("pages/parameter_normal_save.php", {
      tid: val,
      dep: $("#dep_id").val(),
      sex: $("#sex").val(),
      a_from: $("#a_from").val(),
      a_from_typ: $("#a_from_typ").val(),
      a_to: $("#a_to").val(),
      a_to_typ: $("#a_to_typ").val(),
      val_f: $("#val_f").val(),
      val_t: $("#val_t").val(),
      n_range: $("#n_range").val(),
      type: 1

    },
    function(data, status) {
      bootbox.alert("Saved");
      $.post("pages/parameter_more_info.php", {
          val: 3,
          id: val
        },
        function(data, status) {
          $("#results1").html(data);
        })
    })
}

function normal_update(val, sl) {
  $.post("pages/parameter_normal_save.php", {
      tid: val,
      instrument_id: $("#instrument_id").val(),
      dep: $("#dep_id").val(),
      sex: $("#sex").val(),
      a_from: $("#a_from").val(),
      a_from_typ: $("#a_from_typ").val(),
      a_to: $("#a_to").val(),
      a_to_typ: $("#a_to_typ").val(),
      val_f: $("#val_f").val(),
      val_t: $("#val_t").val(),
      n_range: $("#n_range").val(),
      sl: sl,
      type: 2

    },
    function(data, status) {
      bootbox.alert("Updated");
      $.post("pages/parameter_more_info.php", {
          val: 3,
          id: val
        },
        function(data, status) {
          $("#results1").html(data);
        })
    })
}

function remove_range(slno, id) {
  bootbox.dialog({
    //title: "Patient Re-visit ?",
    message: "<h5>Are you sure want to delete the normal range ?</h5>",
    buttons: {
      cancel: {
        label: '<i class="icon-remove"></i> Cancel',
        className: "btn btn-inverse",
        callback: function() {
          bootbox.hideAll();
        }
      },
      confirm: {
        label: '<i class="icon-ok"></i> Delete',
        className: "btn btn-info",
        callback: function() {
          $.post("pages/parameter_master_normal_remove.php", {
              slno: slno
            },
            function(data, status) {
              load_more_info('3', id)
            })
        }
      }
    }
  });
}

function delete_para(id) {
  bootbox.dialog({
    //title: "Patient Re-visit ?",
    message: "<h5>Are you sure want to delete this Parameter ?</h5>",
    buttons: {
      cancel: {
        label: '<i class="icon-remove"></i> Cancel',
        className: "btn btn-inverse",
        callback: function() {
          bootbox.hideAll();
        }
      },
      confirm: {
        label: '<i class="icon-ok"></i> Delete',
        className: "btn btn-info",
        callback: function() {
          $.post("pages/parameter_more_info.php", {
              id: id,
              val: "5"
            },
            function(data, status) {
			//alert(data);
              bootbox.alert(data);

              $.post("pages/parameter_master_ajax.php", {
                val: $("#par").val()
              }, function(data, status) {
                $("#params").html(data);
              })
            })
        }
      }
    }
  });
}

//-----Formula-----//

function add_element(val) {
  $("button").attr("disabled", false);
  if (val == "add_p") {
    $("#" + val + "").attr("disabled", true);

    var b = $('#parm').clone().attr({
      "name": "param",
      "id": "",
      "class": "formula"
    }).val($("#parm").val());
    $("#formula_text").append(b);
    //$("#formula_text").append("<input type='text' value='"+$("#parm").val()+"' class='param' readonly />");
  } else if (val == "add_op") {
    $("#" + val + "").attr("disabled", true);
    $("#formula_text").append("<input type='text' value='" + $("#opr").val() +
      "' name='operator' class='formula span1' maxlength='1' size='1'/>");
  } else if (val == "add_num") {
    $("#" + val + "").attr("disabled", true);
    $("#formula_text").append("<input type='text' value='" + $("#num").val() +
      "' name='numeric' size='3' class='formula span1' onkeyup='check_num(this)'/>");
  }
  $('#formula_text').animate({scrollTop: $('#formula_text').prop("scrollHeight")}, 500);
}

function save_formula(id) {
  var formula = "";
  var elem = $("#formula_text .formula");
  for (var i = 0; i < elem.length; i++) {
    if ($(elem[i]).attr("name") == "param") {
      var param = $(elem[i]).val();
      formula += "p" + param;
    }
    if ($(elem[i]).attr("name") == "operator") {
      var opr = $(elem[i]).val();
      formula += "@" + opr + "@";
    }
    if ($(elem[i]).attr("name") == "numeric") {
      var num = $(elem[i]).val();
      formula += num;
    }

  }

  $.post("pages/parameter_save_formula.php", {
      id: id,
      formula: formula,
      dec: $("#dec").val(),
      type: 1
    },
    function(data, status) {
      alert("Saved");
    })
}

function delete_formula(id) {
  $.post("pages/parameter_save_formula.php", {
      id: id,
      type: 2
    },
    function(data, status) {
      alert("Deleted");
      $("#formula_text").html("");
      $('[value=Add]').attr('disabled', false);
    })
}

function check_num(val) {
  if ($(val).val() * 0 != 0) {
    $(val).val("");
  }
}

function check_opr() {
  var opr = ["+", "-", "%", "*", "/"];

}

function search(inputVal) {
  var table = $('#tblData');
  table.find('tr').each(function(index, row) {
    var allCells = $(row).find('td');
    if (allCells.length > 0) {
      var found = false;
      allCells.each(function(index, td) {
        var regExp = new RegExp(inputVal, 'i');
        if (regExp.test($(td).text())) {
          found = true;
          return false;
        }
      });
      if (found == true) {
        $("#no_record").text("");
        $(row).show();
      } else {
        $(row).hide();
        var n = $('tr:visible').length;
        if (n == 1) {
          $("#no_record").text("No matching records found");
        } else {
          $("#no_record").text("");
        }
      }
      //if(found == true)$(row).show();else $(row).hide();
    }
  });
}
</script>
<style>
#myModal {
  left: 28%;
  width: 90%;
}

.modal.fade.in {
  top: 1%;
}

.modal-body {
  max-height: 520px;
}

.norm_stat_1 {
  display: none;
}

.norm_stat_1 td {
  font-style: italic;
  color: #958E8E;
}
</style>


<?php
/*$tp=mysqli_query($link,"SELECT `TestId`,count(`TestId`) as tot FROM `Testparameter` group BY `TestId` having (count(`TestId`)=1)");
while($tt=mysqli_fetch_array($tp))
{
    $pp=mysqli_fetch_array(mysqli_query($link,"select * from Testparameter where TestId='$tt[TestId]'"));

    $samp=mysqli_fetch_array(mysqli_query($link,"select * from TestSample where TestId='$tt[TestId]'"));

    $vac=mysqli_fetch_array(mysqli_query($link,"select * from test_vaccu where testid='$tt[TestId]'"));

    mysqli_query($link,"update Parameter_old set sample='$samp[SampleId]',vaccu='$vac[vac_id]' where ID='$pp[ParamaterId]'");
}
*/

/*
$par=mysqli_query($link,"select * from Parameter_old where sample>0 and vaccu>0");
while($pp=mysqli_fetch_array($par))
{
    mysqli_query($link,"update Testparameter set sample='$pp[sample]',vaccu='$pp[vaccu]' where ParamaterId='$pp[ID]'");
}
*/
          ?>
