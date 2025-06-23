<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Radiology Reporting</span></div>
</div>
<!--End-header-->
<div class="container-fluid" tabindex='1' onkeyup="back_page(event)">
	<div style="padding-top:0px;text-align:center;font-weight:bold">
		<select id="category_id" onChange="category_change()">
			<option value="2">Radiology</option>
			<option value="3">Cardiology</option>
		</select>
	</div>
	<hr/>
<?php
$doc_sel="";	
$cur_user=$_SESSION[emp_id];
$chk_doc=mysqli_fetch_array(mysqli_query($link,"select * from lab_doctor where id='$cur_user' and category='2' and status='0'"));
if($chk_doc[id]>0)
{
	?>
	<h3 style='font-weight:normal;margin-bottom:-10px;color:black'>Reporting Doctor: <?php echo $chk_doc[name];?> </h3>
	<input type="hidden" id="d_id" value="<?php echo $chk_doc[id];?>"/>
	<?php
}
else
{
?>	
	Select Doctor:
	<select id="d_id" onchange="show_report()">
		<option value='0'>--Select--</option>
		<?php

		$r_doc=mysqli_query($link,"select * from lab_doctor where category='2' and status='0' order by name");
		while($rd=mysqli_fetch_array($r_doc))
		{
			if(mysqli_num_rows($r_doc)==1)
			{
				echo "<option value='$rd[id]' selected>$rd[name]</option>";
			}
			else
			{
				echo "<option value='$rd[id]'>$rd[name]</option>";
			}
		}
		?>
	</select>	
<?php
}
?>    
    <br/><br/>

	
	 <div id="search_rad">
			 <button class="btn btn-info" onclick="$('#rad_search').slideToggle(200)"><i class="icon-search"></i> Search Options</button>
			
			<br/><br/>
			<table class="table table-bordered" style="display:none" id="rad_search">
				 <tr>
					<td>
						<b>Bill No.:</b> <input type="text" class="span2" id="p_id" onKeyUp="load_pinfo_id(event)" autocomplete="off">
					</td>
					<td>
						<b>Enter Name:</b> <input type="text" class="span2" id="name" onKeyUp="load_pinfo_name(event)" autocomplete="off">
					</td>
					<td>
						<b>From</b> <input class="datepicker span2" type="text" name="fdate" id="fdate" value="<?php echo date('Y-m-d');?>">
						<b>To</b> <input class="datepicker span2" type="text" name="tdate" id="tdate" value="<?php echo date('Y-m-d');?>"><br>
						<center>
							<button type="button" id="ser" name="ser" class="btn btn-success" onClick="load_pinfo_date()">Search</button>
						</center>
					</td>
				</tr>
			</table>
	 
	 </div>
	 <div id="select_type">
		 <div class="row">
			 <div class="span6">
				<b>Display:</b>
				<select class="span2" id="dis" onChange="load_data_pat(this.value)">
					<option value="all">All</option>
					<option value="red">Pending</option>
					<option value="green">Reported</option>
					<option value="ultr">USG</option>
					<option value="xr">X-Ray</option>
					<option value="endo">Endoscopy</option>
					<option value="ct">CT</option>
					<option value="mri">MRI</option>
			<!--
					<option value="yellow">All Yellow</option>
					<option value="grey">All Gray</option>
					<option value="xr">X Ray</option>
					<option value="ultr">Ultrasound</option>
			-->
				</select>
			</div>
			<div class="span text-right">
				<button class="btn btn-info btn-mini" onclick="location.reload()"><i class="icon-refresh"></i> Refresh</button>
			</div>	
		</div>
    </div>
	
		
    
     <div id="radio_pat">
        
    </div>
   
     <div id="load_test">
        
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

<!--
<div class="text-center" style="position: fixed; bottom: -20px; right: 0px; padding: 10px; background: #fff; color: #000; box-shadow: 0 0 10px rgba(0,0,0,0.5); z-index:100;">
	<table class="table table-bordered table-condensed">
		<tr>
			<td class=""><span class="btn_round_msg redd"></span> Not Done</td>
			<td class=""><span class="btn_round_msg greenn"></span> Done</td>
			<td class=""><span class="btn_round_msg yelloww"></span> Partially Done</td>
			<!--<td class=""><span class="btn_round_msg grayy"></span> All Printed</td>
		</tr>
	</table>
</div>
-->

<div id="loader" style="margin-top:-7%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../jss/moment.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<script type="text/javascript" src="../ckeditor_rad/ckeditor.js"></script>

<link rel="stylesheet" href="../css/select2.min.css" />
<script src="../js/select2.min.js"></script>

<script type='text/javascript'>
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
		
		load_pinfo_date();
		
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
		 
		// $("#dis").val("ultr");
		
		load_pinfo_date();
	});
	
	function category_change()
	{
		var text="Enter Password: <input type='password' id='passw' onkeyup='chk_pass(this.value,event)' class='form-control'/>";
		$("#passw_div").html(text);
		$("#passw").focus();
		load_lab_doc();
		load_pinfo_date();
	}
	function load_lab_doc()
	{
		$.post("pages/radiology_reporting_data.php",
		{
			type:"load_cat_doc",
			category_id:$("#category_id").val(),
		},
		function(data,status)
		{
			$("#d_id").empty();
			$("#d_id").append(data);
		})
	}
	function load_pinfo_date()
	{
		$("#loader").show();
		$.post("pages/radiology_reporting_data.php",
		{
			user:$("#user").text().trim(),
			type:"load_all_pat",
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			category_id:$("#category_id").val(),
			p_id:$("#p_id").val(),
			name:$("#name").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			
			$("#load_test").slideUp(300);
			$("#search_rad").slideDown(300);
			$("#select_type").slideDown(300);
			$("#rad_search").slideUp(300);
			$("#radio_pat").slideDown(300).html(data);
			
			$(".datepicker").datepicker({
				dateFormat: 'yy-mm-dd',
				maxDate: '0',
			});
			//$("#radio_pat").html(data);
			load_data_pat($("#dis").val());
		})
	}
	
	function load_test_info(uhid,opd_id,ipd_id,batch_no,val,tst) 
	{
		var pat = $("#path_pat" + val).text();
		var info = pat.split("@");
		$.post("pages/radiology_reporting_data.php", 
		{
			type:"load_all_test",
			uhid: uhid,
			opd_id: opd_id,
			ipd_id: ipd_id,
			batch_no: batch_no,
			category_id:$("#category_id").val(),
			tst:tst
		},
		function(data, status) 
		{
			$("#radio_pat").slideUp(300);
			$("#search_rad").slideUp(300);
			$("#select_type").slideUp(300);
			$("#load_test").slideDown(300).html(data);
		})
	}
	
	function load_test_info_rad(val) 
	{
		var info=val.split("@@");
		$.post("pages/radiology_reporting_data.php", 
		{
			type:"load_all_test",
			uhid: info[1],
			opd_id: info[2],
			ipd_id: info[3],
			batch_no: info[4],
			tst:info[5],
			category_id:$("#category_id").val(),
		},
		function(data, status) 
		{
			$("#radio_pat").slideUp(300);
			$("#load_test").slideDown(300).html(data);		
		})
	}
	
	function load_test_param(tinfo) 
	{
		var url="pages/ckeditor_load_rad.php?tinfo="+tinfo+"&did="+$("#d_id").val();
		var win_chk=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=4000,width=4000');
		$("#back_div").prop("disabled",true);
		
	}
	//~ function load_res(tst)
	//~ {
		//~ var uhid=$("#uhid").val();
		//~ var pat_id=$("#pat_id").val();
		//~ var user=$("#user").text().trim();
		
		//~ var url="pages/ckeditor_load_rad.php?uhid="+uhid+"&pat_id="+pat_id+"&tst="+tst+"&rep_doc="+$("#d_id").val()+"&user="+user;
		//~ window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=4000,width=4000');
	//~ }
	
	function load_res(uhid,opd,ipd,batch_no,tid)
	{
		var tstid = tid;
		var uhid = uhid;
		var opd_id = opd;
		var ipd_id = ipd;
		var category_id=$("#category_id").val();
		var doc=$("#d_id").val();
		var user=$("#user").text().trim();
		
		var url = "pages/ckeditor_load_rad.php?uhid=" + uhid + "&opd_id=" + opd_id+ "&ipd_id=" + ipd_id+ "&batch_no=" + batch_no + "&tstid=" + tid + "&user=" + user + "&doc=" + doc + "&category_id=" + category_id;
		var win = window.open(url, '', 'fullScreen=yes,scrollbars=yes,menubar=1');
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
				typ:"load_all_pat",
				val: val,
				id: id,
				category_id:$("#category_id").val(),
			},
			function(data, status)
			{
				$("#loader").hide();
				$("#load_test").slideUp(300);
				$("#radio_pat").slideDown(300).html(data);
				
				//$("#radio_pat").html(data);
				var dis_val = $("#dis").val();
				load_data_pat(dis_val);
			})
		}
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
		var tinfo = $("#test_dis" + val).text().trim();
		load_test_param(tinfo);
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

	function print_report(uhid,opd,ipd,batch_no,tid)
	{
		var uhid = uhid;
		var opd_id = opd;
		var ipd_id = ipd;
		var batch = batch_no;
		var tst = tid;
		var category_id=$("#category_id").val();
		
		var user=$("#user").val();
		var view=0;
		
		var url = "pages/radiology_report_print.php?uhid=" + btoa(uhid) + "&opd_id=" + btoa(opd_id) + "&ipd_id=" + btoa(ipd_id)+ "&batch_no=" + btoa(batch) + "&tstid=" + btoa(tst) + "&category_id=" + btoa(category_id) + "&user=" + btoa(user) + "&view=" + btoa(view);
		
		var win = window.open(url, '', 'fullScreen=yes,scrollbars=yes,menubar=1');
		
		//~ if(category_id==2)
		//~ {
			//~ var url = "pages/report_print_rad.php?uhid=" + uhid + "&opd_id=" + opd_id+ "&ipd_id=" + ipd_id+ "&batch_no=" + batch_no + "&tstid=" + tid;
			//~ var win = window.open(url, '', 'fullScreen=yes,scrollbars=yes,menubar=1');
		//~ }
		//~ if(category_id==3)
		//~ {
			//~ var url = "pages/report_print_card.php?uhid=" + uhid + "&opd_id=" + opd_id+ "&ipd_id=" + ipd_id +"&batch_no=" + batch_no + "&tstid=" + tid;
			//~ var win = window.open(url, '', 'fullScreen=yes,scrollbars=yes,menubar=1');
		//~ }
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
		//~ if (CKEDITOR.instances['article-body']) {
			//~ CKEDITOR.instances['article-body'].destroy(true);
		//~ }
		
		//~ CKEDITOR.replace('article-body');
		//~ CKEDITOR.config.enterMode=CKEDITOR.ENTER_BR;
		//~ CKEDITOR.config.height = 300;
		//~ //CKEDITOR.config.toolbar=[{ name:'lineheight',items:['1','2','3'] }];
		
		//~ //CKEDITOR.config.bodyId = 'rad_res';
		//~ CKEDITOR.config.extraPlugins = 'lineheight';
		//~ //CKEDITOR.config.line_height="1.0mm;2.0mm;3.0mm;4.0mm;5.0mm;6.0mm;7.0mm;8.0mm;9.0mm;10.0mm;" ;
		//~ CKEDITOR.config.line_height="1.0 em;1.5em;2.0em;2.5em;3.0em;3.5em;4.0em;4.5em;5.0em;" ;
	  
		if (CKEDITOR.instances['article-body'])
		{
			CKEDITOR.instances['article-body'].destroy(true);
		}
		CKEDITOR.replace('article-body');
		CKEDITOR.config.enterMode=CKEDITOR.ENTER_BR;
		CKEDITOR.config.extraPlugins = 'lineheight';
		//CKEDITOR.config.width = 700;		
		CKEDITOR.config.height = 300;		
		CKEDITOR.config.line_height="1.0em;1.5em;2.0em;2.5em;3.0em;3.5em;4.0em;4.5em;5.0em;" ;

		
		//nanospell.ckeditor('all',{server : "php"}); 
	}

	function load_data_pat(val)
	{
		if (val != "all")
		{
			$("#radio_pat tr").hide();
			$("#radio_pat tr:first, #tr_head").show();
			$("#radio_pat ." + val + "").show();
			
			if(val=="ultr" || val=="xr" || val=="ct" || val=="mri")
			{
				$(".pat_test_but .btn[class*="+val+"]").slideDown(100);
				$(".pat_test_but .btn:not([class*="+val+"])").slideUp(100);
			}
			
		}else
		{
			$("#radio_pat tr").show();
			$(".pat_test_but .btn").slideDown(100);
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
	
	function back_div()
	{
		$("#load_test").slideUp(500);
		//$("#radio_pat").slideDown(500);
		load_pinfo_date();
	}
	function back_page(e)
	{
		if(e.which==27)
		{
			if($("#back_div:visible").length>0)
			{
				$("#back_div").click();
			}
		}
	}
	function load_pinfo_id(e)
	{	
		if(e.which==13)
		{
			load_pinfo_date();
		}
	}
	function load_pinfo_name(e)
	{	
		if(e.which==13)
		{
			load_pinfo_date();
		}
	}
	//~ function print_report(uhid,reg_id,batch,tst)
	//~ {
		//~ var url = "pages/report_print_rad.php?uhid=" + uhid + "&reg_id=" + reg_id+ "&batch_no=" + batch + "&tstid=" + tst;
		//~ var win = window.open(url, '', 'fullScreen=yes,scrollbars=yes,menubar=1');
	//~ }
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

.ScrollStyle
{
    max-height: 290px;
    overflow-y: scroll;
    box-shadow: 0px 0px 2px 0px #000;;
}
.table
{
	margin-bottom: 0px;
}
.table-report {
    background: #FFFFFF;
}
</style>
