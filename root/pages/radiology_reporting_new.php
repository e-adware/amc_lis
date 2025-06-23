<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div style="padding-top:20px;text-align:center;font-weight:bold">
		<select id="category_id" onChange="category_change()" style="display:none">
			<option value="2">Radiology</option>
			<option value="3">Cardiology</option>
		</select>
		<div style="height:80px">
			<div id="passw_div" style="padding:20px;text-align:center;font-weight:bold;">
					Select Doctor:
					<select id="d_id" onchange="show_report()">
						<option value='0'>--Select--</option>
					<?php
					$r_doc=mysqli_query($link,"select * from lab_doctor where category='2'");
					while($rd=mysqli_fetch_array($r_doc))
					{
						echo "<option value='$rd[id]'>$rd[name]</option>";
					}
					?>
					</select>
					<button class="btn btn-info" onclick="view_report()" id="doc_report" style="display:none">Report</button>
			</div>
		</div>
	</div>
	<hr/>
	<table class="table table-bordered">
		 <tr>
			<td>
				<b>Enter UHID:</b> <input type="text" class="span2" id="bill" onKeyUp="load_pat(event,this.value,'bill')" autocomplete="off">
			</td>
			<td>
				<b>Enter ID:</b> <input type="text" class="span2" id="p_id" onKeyUp="load_pat(event,this.value,'p_id')" autocomplete="off">
			</td>
			<td>
				<b>Enter Name:</b> <input type="text" class="span2" id="name" onKeyUp="load_pat(event,this.value,'name')" autocomplete="off">
			</td>
			<td>
				<b>From</b> <input class="datepicker span2" type="text" name="fdate" id="fdate" value="<?php echo date('Y-m-d');?>">
				<b>To</b> <input class="datepicker span2" type="text" name="tdate" id="tdate" value="<?php echo date('Y-m-d');?>"><br>
				<center>
					<button type="button" id="ser" name="ser" class="btn btn-success" onClick="load_pinfo_date()">Search</button>
					<!--<button type="button" id="res" name="res" class="btn btn-danger" onClick="load_pinfo('','')">Reset</button>-->
				</center>
			</td>
		</tr>
	</table>
	<b>Display:</b>
    <select class="span2" id="dis" onChange="load_data_pat(this.value)">
        <option value="all">All</option>
        <option value="red">All Red</option>
        <option value="green">All Green</option>
        <option value="yellow">All Yellow</option>
        <option value="grey">All Gray</option>
        <option value="xr">X Ray</option>
        <option value="ultr">Ultrasound</option>
    </select>
     <div id="radio_pat">
        
    </div>
</div>

<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"/>
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal_dial">
		<div class="modal-content">
			<div class="modal-body">
				<div id="results"> </div>
			</div>
		</div>
	</div>
</div>

<input type="button" data-toggle="modal" data-target="#myModal2" id="mod2" style="display:none"/>
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
	<div class="modal-dialog" id="modal_dial2">
		<div class="modal-content">
			<div class="modal-body">
				<div id="results1"></div>
			</div>
		</div>
	</div>
</div>

<div class="text-center" style="position: fixed; bottom: -20px; right: 0px; padding: 10px; background: #fff; color: #000; box-shadow: 0 0 10px rgba(0,0,0,0.5); z-index:100;">
	<table class="table table-bordered table-condensed">
		<tr>
			<td class=""><span class="btn_round_msg redd"></span> Not Done</td>
			<td class=""><span class="btn_round_msg greenn"></span> Done</td>
			<td class=""><span class="btn_round_msg yelloww"></span> Partially Done</td>
			<!--<td class=""><span class="btn_round_msg grayy"></span> All Printed</td>-->
		</tr>
	</table>
</div>

<div id="loader" style="margin-top:-7%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../jss/moment.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<script type="text/javascript" src="../ckeditor_rad/ckeditor.js"></script>
<script src="../nanospell/autoload.js"></script>
<link rel="stylesheet" href="../css/select2.min.css" />
<script src="../js/select2.min.js"></script>

<script type='text/javascript'>
	$(document).ready(function(){
		$("#loader").hide();
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
		});
		
		load_pat('','','');
		$('#openBtn').click(function(){
			$('#myModal').modal({show:true})
		});
		$('.modal').on('hidden.bs.modal', function( event ){
			$(this).removeClass( 'fv-modal-stack' );
			$('body').data( 'fv_open_modals', $('body').data( 'fv_open_modals' ) - 1 );
		});
		$( '.modal' ).on( 'shown.bs.modal', function ( event ) {
		   // keep track of the number of open modals
		   if ( typeof( $('body').data( 'fv_open_modals' ) ) == 'undefined' )
		   {
			 $('body').data( 'fv_open_modals', 0 );
		   }				 
		   // if the z-index of this modal has been set, ignore.		
			if ( $(this).hasClass( 'fv-modal-stack' ) )
			{
				return;
			}
			   
			$(this).addClass( 'fv-modal-stack' );

			$('body').data( 'fv_open_modals', $('body').data( 'fv_open_modals' ) + 1 );

			$(this).css('z-index', 1040 + (10 * $('body').data( 'fv_open_modals' )));

			$( '.modal-backdrop' ).not( '.fv-modal-stack' ).css( 'z-index', 1039 + (10 * $('body').data( 'fv_open_modals' )));
			$( '.modal-backdrop' ).not( 'fv-modal-stack' ).addClass( 'fv-modal-stack' ); 
		 });
		 
		 $("#dis").val("ultr");
	});
	/*
	function load_pat_print()
	{
		var s_by=$("#search_by").val();	
		if(s_by=="name")
		{
			var name=$("#name").val();
			load_pat(name,s_by);
		}
		else if(s_by=="bill")
		{
			var bill=$("#bill").val();
			load_pat(name,s_by);
		}
		else if(s_by=="date")
		{
			load_pinfo_date();
		}
		else
		{
			load_pat();	
		}
	}
	*/
	function category_change()
	{
		var text="Enter Password: <input type='password' id='passw' onkeyup='chk_pass(this.value,event)' class='form-control'/>";
		$("#passw_div").html(text);
		$("#passw").focus();
		load_pat('','','');
	}
	function load_pat(e,val, id)
	{
		var unicode = e.keyCode ? e.keyCode : e.charCode;
		if(unicode==13)
		{
			load_test_info(1);
		}
		else
		{
			$.post("pages/radiology_reporting_pat.php", 
			{
				val: val,
				id: id,
				category_id:$("#category_id").val(),
			},
			function(data, status)
			{	
				$("#radio_pat").html(data);
				var dis_val = $("#dis").val();
				load_data_pat(dis_val);
			})
		}
	}
	function load_test_info(val) 
	{
		//~ var did=$("#d_id").val();
		//~ if(did>0)	
		//~ {
			var pat = $("#path_pat" + val).text();
			var info = pat.split("@");
			$.post("pages/patient_test_info_radio.php", 
			{
				uhid: info[1],
				opd_id: info[2],
				ipd_id: info[3],
				batch_no: info[4],
				category_id:$("#category_id").val(),
			},
			function(data, status) 
			{
				$("#results").html(data);
				$("#modal_dial").css({'width':'1000px'});
				$("#mod").click();
				$("#results").fadeIn(500);
				
				/*
				$("#results").css({
					'height': 'auto',
					'width': '85%',
					'max-height': '600px'
				});
				var x = $("#results").height() / 2 + 120;
				var y = $("#results").width() / 2 + 50;
				document.getElementById("results").style.cssText += "margin-left:-" + y + "px;margin-top:-" + x + "px";
				$("#results").slideDown(500);
				*/
			})
		//~ }
		//~ else
		//~ {
			//~ $("#passw").css({'border':'2px solid red'}).focus();	
		//~ }
	}
	function load_pinfo_date()
	{
		$.post("pages/radiology_reporting_pat.php",
		{
			user:$("#user").text(),
			type:"radiodate",
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			category_id:$("#category_id").val(),
		},
		function(data,status)
		{
			$("#radio_pat").html(data);
		})
	}
   
	function load_test_info2(val) 
	{
		var info = val.split("@");

		$.post("pages/patient_test_info_radio.php",
		{
			uhid: info[1],
			opd_id: info[2],
			ipd_id: info[3],
			batch_no: info[4],
			category_id:$("#category_id").val(),
		},
		function(data, status)
		{
			$("#results").html(data);
			$("#modal_dial").css({'width':'1000px'});
			$("#results").fadeIn(500);
		})
	}

	function rad_select_test(val)
	{
		var tinfo = $("#test_dis" + val).text();
		load_test_param(tinfo);
	}

	function load_test_param(tinfo) 
	{
		$.post("pages/radio_pad_new.php",
		{
			tinfo: tinfo,
			did:$("#d_id").val(),
			category_id:$("#category_id").val(),
		},
		function(data, status) 
		{
			$("#results1").html(data);
			$("#results1").css({'display':'block'})
			$("#modal_dial2").css({"width":"1300px"});
			$("#mod2").click();
			
			
			//$(".select2").css({"z-index": "19001"});

			add();
			/*
			$("#results1").css({
				'height': '600px',
				'width': '85%'
			});
			var x = $("#results1").height() / 2;
			var y = $("#results1").width() / 2 + 50;
			document.getElementById("results1").style.cssText += "margin-left:-" + y + "px;margin-top:-" + x + "px";

			$("#back").fadeIn(100, function() {
				$("#results1").slideDown(500, function() {
					$("[name='t_par1']").focus();
				});
			})
			*/
		})
	}

	function save_data(uhid,opd,ipd,batch_no) 
	{
		$.post("pages/radiology_result_save.php", 
		{
			uhid:uhid,
			opd_id:opd,
			ipd_id:ipd,
			batch_no:batch_no,
			tstid: $("#tstid").val(),
			detail: $(".rad_res").contents().find('body').html(),
			doc: $("#d_id").val(),
			category_id:$("#category_id").val(),
			sl:$("#film_no").val(),
            test_name:$("#rad_testname").text()
		},
		function(data, status) {
			alert("Saved");
			load_pat();
		})
	}

	function hid_div(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==27)
		{
			$('.modal').modal('hide');
			/*if($("#results").css("display")=="block")
			{
				if($("#results1").css("display")=="block")
				{
					$("#results1").fadeOut(200);
					$("#mod2").click();					
				}
				else
				{
					$("#results").fadeOut(200);
					$("#mod").click();
				}
			}*/
			$("#msg").fadeOut(200);
		}else if(e=="close")
		{
			$('.modal').modal('hide');
		}
	}

	function print_report(tid,uhid,opd,ipd,batch_no)
	{
		var tstid = tid;
		var uhid = uhid;
		var opd_id = opd;
		var ipd_id = ipd;
		var category_id=$("#category_id").val();
		if(category_id==2)
		{
			var url = "pages/report_print_rad.php?uhid=" + uhid + "&opd_id=" + opd_id+ "&ipd_id=" + ipd_id+ "&batch_no=" + batch_no + "&tstid=" + tid;
			var win = window.open(url, '', 'fullScreen=yes,scrollbars=yes,menubar=1');
		}
		if(category_id==3)
		{
			var url = "pages/report_print_card.php?uhid=" + uhid + "&opd_id=" + opd_id+ "&ipd_id=" + ipd_id +"&batch_no=" + batch_no + "&tstid=" + tid;
			var win = window.open(url, '', 'fullScreen=yes,scrollbars=yes,menubar=1');
		}
	}
	
	
	function chk_pass(val, e) 
	{
		var unicode = e.keyCode ? e.keyCode : e.charCode;
		if (unicode == 13) 
		{
			$.post("pages/radio_chk_pass.php",
			{
				val:val,
				category_id:$("#category_id").val(),
				
			},
			function(data,status)
			{
				if(data==1)
				{
					$("#passw").css({'border':'2px solid red'}).focus();	
				}
				else
				{
					var info=data.split("#");
					var text="<span class='btn btn-primary'  onclick='change_pass()'><input type='hidden' value="+info[0]+" id='d_id' /><b><i>Reporting Doctor: "+info[1]+"</i></b></span> <span class='btn btn-danger btn-sm' onclick='update_password("+info[0]+")'>Change Password</span> <span class='btn btn-success btn-sm' onclick='view_report("+info[0]+")'>View Report</span>";
					
					$("#passw_div").html(text);
					$("html, body").animate({ scrollTop: 250 },"slow")
				}
			})
		}
	}
	
	function change_pass()
	{
		var text="Enter Password: <input type='password' id='passw' onkeyup='chk_pass(this.value,event)' class='form-control'/>";
		$("#passw_div").html(text);
	}
	
	function update_password(val)
	{
		 $.post("pages/radio_update_pass.php", {
			   val:val,
			   type:1
			},
			function(data, status) {

				$("#results").html(data);
				//$("#modal_dial").css({'width':'600px'});
				$("#mod").click();
				$("#results").fadeIn(500);

			})
	}
	function save_pass(val)
	{
		 $.post("pages/radio_update_pass.php", {
			   val:val,
			   n_pass:$("#n_pass").val(),
			   o_pass:$("#o_pass").val(),
			   type:2
			},
			function(data, status) {

				if(data!=1)
				{
					$("#o_pass").val("").attr("placeholder","Wrong password").css({'border':'1px solid red'});
				}
				else
				{
					alert("Updated");
					$("#mod").click();
					change_pass();
				}
			})
	}
	function add() 
	{
		if (CKEDITOR.instances['article-body']) {
			CKEDITOR.instances['article-body'].destroy(true);
		}
		
		CKEDITOR.replace('article-body');
		CKEDITOR.config.enterMode=CKEDITOR.ENTER_BR;
		CKEDITOR.config.height = 300;
		//CKEDITOR.config.toolbar=[{ name:'lineheight',items:['1','2','3'] }];
		
		//CKEDITOR.config.bodyId = 'rad_res';
		CKEDITOR.config.extraPlugins = 'lineheight';
		//CKEDITOR.config.line_height="1.0mm;2.0mm;3.0mm;4.0mm;5.0mm;6.0mm;7.0mm;8.0mm;9.0mm;10.0mm;" ;
		CKEDITOR.config.line_height="1.0 em;1.5em;2.0em;2.5em;3.0em;3.5em;4.0em;4.5em;5.0em;" ;
	  
		
		

		
		nanospell.ckeditor('all',{server : "php"}); 
	}

	function load_data_pat(val)
	{
		if (val != "all")
		{
			$("#radio_pat tr").hide();
			$("#radio_pat tr:first, #tr_head").show();
			$("#radio_pat ." + val + "").show();
		}else
		{
			$("#radio_pat tr").show();
		}
	}

	function sync_list() {
		$("#sync").val("Syncing...");
		load_pat();
	}

	function load_normal(id,tst)
	{
		$.post("pages/radiology_normal_info.php",
		{
			id: tst,
			doctor: id,
			category_id:$("#category_id").val(),
		},
		function(data, status) {
			
			
			$(".rad_res").contents().find('body').html(data);
			
		})
	}
	
	function view_report()
	{
		var val=$("#d_id").val();
		var url="pages/radio_doctor_report.php?doc="+val;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1250,fullscreen=1');	
	}
	
	function load_findings(id)
	{
		
		if(id>0)
		{
			$.post("pages/radiology_normal_finding_ajax.php",
			{
				id:id,
				type:"load"
			},
			function(data,status)
			{
				$(".rad_res").contents().find('body').html(data);
				
			})
		}
		else
		{
			load_normal(0,$("#tstid").val());
		}	
	}
	
	function show_report()
	{
		var doc=$("#d_id").val();
		if(doc>0)
		{
			$("#doc_report").fadeIn(100);
		}
		else
		{
			$("#doc_report").fadeOut(100);
		}
	}
</script>
<style>
.cke_textarea_inline
{
	padding: 10px;
	height: 380px;
	overflow: auto;
	border: 1px solid gray;
	-webkit-appearance: textfield;
}
hr
{
	margin:0;
}
.btn_round_msg
{
	color:#000;
	padding:2px;
	border-radius: 7em;
	padding-right:10px;
	padding-left:10px;
	box-shadow: inset 1px 1px 0 rgba(0,0,0,0.6);
	transition: all ease-in-out 0.2s;
}
.redd
{
	background-color: #d59a9a;
}
.greenn
{
	background-color:#9dcf8a;
}
.yelloww
{
	background-color:#f6e8a8;
}
.grayy
{
	background-color:#666666;
}
#myModal
{
	left: 33%;
	width:75%;
}
#myModal2
{
	left: 23%;
	width:95%;
}
.modal.fade.in
{
	top: 5%;
}
.modal-body
{
	max-height: 500px;
}
#path_tr1, #test_tr1
{
  color:#666;
  font-weight:inherit;
}
.modal
{
    z-index: 999 !important;
}
.modal-backdrop
{
	z-index: 990 !important;
}
</style>
