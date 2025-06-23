<?php
$uhid=base64_decode($_GET["uhid"]);
$uhid=trim($uhid);
$opd=base64_decode($_GET["opd"]);
$opd=trim($opd);
$pat=mysqli_fetch_array(mysqli_query($link,"select * from `patient_info` where `patient_id`='$uhid'"));
$d=mysqli_fetch_array(mysqli_query($link,"SELECT `consultantdoctorid` FROM `consultant_doctor_master` WHERE `emp_id`='$_SESSION[emp_id]'"));
$app=mysqli_num_rows(mysqli_query($link,"SELECT * FROM `appointment_book` WHERE `patient_id`='$uhid'"));
if($app>1)
$diss="";
else
$diss="disabled='disabled'";
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Doctor OPD Dashboard</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<input type="text" id="uhid" style="display:none;" value="<?php echo $uhid;?>" />
	<input type="text" id="pid" style="display:none;" value="<?php echo $pat['uhid'];?>" />
	<input type="text" id="opd" style="display:none;" value="<?php echo $opd;?>" />
	<input type="text" id="opd_crr" style="display:none;" value="<?php echo $opd;?>" />
	<input type="text" id="prev_rec" style="display:none;" value="1" />
	<input type="button" class="btn btn-info" onclick="window.location='processing.php?param=15'" style="float:right;" value="Back to queue" />
	<table class="table table-condensed table-bordered table-report" style="background:snow;">
		<tr>
			<th>UHID</th>
			<th>PIN</th>
			<th>Patient Name</th>
			<th>Age</th>
			<th>Sex</th>
			<th>Phone Number</th>
		</tr>
		<tr>
			<td><?php echo $uhid;?></td>
			<td><?php echo $opd;?></td>
			<td><?php echo $pat['name'];?></td>
			<td><?php if($pat['dob']){echo age_calculator($pat['dob'])." (".$pat['dob'].")";}else{echo $pat['age']." ".$pat['age_type'];}?></td>
			<td><?php echo $pat['sex'];?></td>
			<td><?php echo $pat['phone'];?></td>
		</tr>
	</table>
	<table class="tab-table" style="margin:0 auto;display:none;">
		<tr>
			<td><button class="btn btn-large btn-default" name="tab1" id="tab1" onclick="load_res(1)">Observations</button></td>
			<td><button class="btn btn-large btn-default" name="tab2" id="tab2" onclick="load_res(2)">Diagnosis</button></td>
			<td><button class="btn btn-large btn-default" name="tab3" id="tab3" onclick="load_res(3)">Investigations</button></td>
			<td><button class="btn btn-large btn-default" name="tab4" id="tab4" onclick="load_res(4)">Medications</button></td>
			<!--<td><button class="btn btn-large btn-default" name="tab5" id="tab5" onclick="load_res(5)">Consultation</button></td>-->
			<td><button class="btn btn-large btn-default" name="tab5" id="tab5" onclick="load_res(5)">Disposition</button></td>
			<td><button class="btn btn-large btn-default" name="tab6" id="tab6" onclick="load_res(6)" <?php echo $diss;?>>Previous Record</button></td>
			<!--<td><button class="btn btn-large btn-default" name="tab1" id="tab1" onclick="load_res(1)">Chief Complaints</button></td>
			<td><button class="btn btn-large btn-default" name="tab2" id="tab2" onclick="load_res(2)">Patient History</button></td>
			<td><button class="btn btn-large btn-default" name="tab3" id="tab3" onclick="load_res(3)">Physical Examination</button></td>
			<td><button class="btn btn-large btn-default" name="tab4" id="tab4" onclick="load_res(4)">Impression</button></td>
			<td><button class="btn btn-large btn-default" name="tab5" id="tab5" onclick="load_res(5)" <?php echo $diss;?>>Previous Record</button></td>-->
		</tr>
	</table>
	<div id="load_parts" class="" style="min-height:300px;margin:0 auto;margin-top:1%;display:none;">
		
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="float:right;margin-right:15px;">
		<button type="button" class="btn btn-primary" id="print" onClick="print_page()" style="display:none;" />Print</button>
		<button type="button" class="btn btn-primary" id="prev" onClick="prev_page()" style="display:none;" />&lt;&lt; Previous Page</button>
		<button type="button" class="btn btn-primary" id="save" onClick="insert_data()" style="display:none;" />Save</button>
		<button type="button" class="btn btn-primary" id="next" onClick="next_page()" style="display:none;" />Save &amp; Next Page &gt;&gt;</button>
	</div>
	<input type="hidden" id="chk_val1" value="0"/>
	<input type="hidden" id="chk_val2" value="0"/>
</div>
	<!--modal-->
	<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"/>
	<input type="text" id="modtxt" value="0" style="display:none"/>
	<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="border-radius:0;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<button type="button" class="bootbox-close-button close" data-dismiss="modal" aria-hidden="true"><b>x</b></button>
					<div id="results">
					
					</div>
				</div>
			</div>
		</div>
	</div>
	<!--modal end-->
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script src="../js/jquery.uniform.js"></script>
<link rel="stylesheet" href="../css/uniform.css" type="text/css" />
<script type='text/javascript'>
	var availableTags="";
	$(document).ready(function()
	{
		load_res(1);
		load_print();
		load_disp_texts();
	});
	function load_disp_texts()
	{
		//alert($("#disp_note").val());
		$.post("pages/global_load_g.php",
		{
			type:"load_disp_texts",
		},
		function(data,status)
		{
			availableTags=data.split("@");
			//alert(availableTags);
			//$("#disp_note").autocomplete({source:sourcehelp});
		})
	}
	function abcd()
	{	
		function split1( val )
		{
		  return val.split( /,\s*/ );
		}
		function extractLast( term )
		{
		  return split1( term ).pop();
		}
	 
		$( "#disp_note" )
		  // don't navigate away from the field on tab when selecting an item
		  .on( "keydown", function( event )
		  {
			if ( event.keyCode === $.ui.keyCode.TAB && $( this ).autocomplete( "instance" ).menu.active )
			{
			  event.preventDefault();
			}
		  })
		  .autocomplete({
			minLength: 0,
			source: function( request, response )
			{
			  // delegate back to autocomplete, but extract the last term
			  response( $.ui.autocomplete.filter(
				availableTags, extractLast( request.term ) ) );
			},
			focus: function()
			{
			  // prevent value inserted on focus
			  return false;
			},
			select: function( event, ui )
			{
			  var terms = split1( this.value );
			  // remove the current input
			  terms.pop();
			  // add the selected item
			  terms.push( ui.item.value );
			  // add placeholder to get the comma-and-space at the end
			  terms.push( "" );
			  this.value = terms.join( ", " );
			  return false;
			}
		  });
	  }
	function save_conf()
	{
		$.post("pages/global_insert_data_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			conf:$("#confident").val(),
			usr:$("#user").text().trim(),
			type:"save_conf",
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
				load_print();
				//view_disp();
				//$("#nexto").focus();
			}, 1000);
		})
	}
	function add_row(r)
	{
		if(r=="1")
		{
			var rr=document.getElementById("hist_table").rows.length;
			var k=jj=1;
			for(jj=1;jj<rr-2;jj++)
			{
				if($("#tr"+jj).find("input").val()=="")
				{
					$("#tr"+jj).find('td:first input:first').focus();
					k=0;
				}
				else if($("#tr"+jj).find("select:first").val()=="0")
				{
					$("#tr"+jj).find("select:first").focus();
					k=0;
				}
				else if($("#tr"+jj).find("select:last").val()=="0")
				{
					$("#tr"+jj).find("select:last").focus();
					k=0;
				}
			}
			if(k==1)
			{
				var i=1;
				var d="";
				for(i=1;i<=30;i++)
				{
					d+="<option value='"+i+"'>"+i+"</option>";
				}
				var s='<option value="Minutes">Minutes</option><option value="Hours">Hours</option><option value="Days">Days</option><option value="Week">Week</option><option value="Month">Month</option><option value="Year">Year</option>';
				var j=rr-2;
				$("#ref_doc").fadeIn(500);
				$.post("pages/global_load_g.php"	,
				{
					no:j,
					type:"complain_templates_list",
				},
				function(data,status)
				{
					$("#hh").closest("tr").before('<tr id="tr'+j+'"><th>Chief Complaints</th><td colspan="4"><input list="browsrs'+j+'" type="text" id="chief'+j+'" class="" onkeyup="sel_chief('+j+',event)" /><span id="com'+j+'"></span></td><td colspan="4"><b>for</b> <select id="cc'+j+'" class="span2" onkeyup="sel_chief('+j+',event)"><option value="0">--Select--</option>'+d+'</select> <select id="tim'+j+'" class="span2" onkeyup="sel_chief('+j+',event)"><option value="0">--Select--</option>'+s+'</select></td><td><span style="float:right"><button type="button" class="btn-mini btn-danger" onclick="$(this).parent().parent().parent().remove()"><span class="icon-remove"></span></button></span></td></tr>');
					$("#com"+j).html(data);
					$("#chief"+j).focus();
				})
			}
		}
		else if(r=="2")
		{
			var rr=document.getElementById("diag_table").rows.length;
			var jj=rr+1;
			var k=1;
			for(jj=1;jj<=rr;jj++)
			{
				if($("#tr"+jj).find("input").val()=="")
				{
					$("#tr"+jj).find('td:first input:first').focus();
					k=0;
				}
				else if($("#tr"+jj).find("select:first").val()=="0")
				{
					$("#tr"+jj).find("select:first").focus();
					k=0;
				}
				else if($("#tr"+jj).find("select:last").val()=="0")
				{
					$("#tr"+jj).find("select:last").focus();
					k=0;
				}
			}
			if(k==1)
			{
				$.post("pages/global_load_g.php"	,
				{
					no:jj,
					type:"diagnosis_templates_list",
				},
				function(data,status)
				{
					$('#diag_table').append('<tr id="tr'+jj+'"><th>Diagnosis</th><td><input list="brows'+jj+'" type="text" name="diagnosis'+jj+'" class="span4" onkeyup="diagtab('+jj+',event)" id="diagnosis'+jj+'" /><span id="diagnos'+jj+'"></span></td><th>Order</th><td><select id="order'+jj+'" onkeyup="diagtab('+jj+',event)" class="span2"><option value="0">--Select--</option><option value="Primary">Primary</option><option value="Secondary">Secondary</option></select></td><th>Certainity</th><td><select id="cert'+jj+'" onkeyup="diagtab('+jj+',event)" class="span2"><option value="0">--Select--</option><option value="Confirmed">Confirmed</option><option value="Presumed">Presumed</option></select></td><td><span style="float:right"><button type="button" class="btn-mini btn-danger" onclick="$(this).parent().parent().parent().remove()"><span class="icon-remove"></span></button></span></td></tr>');
					$("#diagnos"+jj).html(data);
					$("#diagnosis"+jj).focus();
				})
			}
		}
	}
	function show_icon(i)
	{
		$(".hidden_div").fadeOut();
		$(".iconp").show();
		$(".iconm").hide();
		$(".icon-arrow-down").show();
		$(".icon-arrow-up").hide();
		if($('#cl'+i+':visible').length)
		{
			$("#cl"+i).fadeOut('slow');
			$("#plus_sign"+i).show();
			$("#minus_sign"+i).hide();
			$("#ard"+i).show();
			$("#aru"+i).hide();
		}
		else
		{
			$("#cl"+i).fadeIn('slow');
			$("#plus_sign"+i).hide();
			$("#minus_sign"+i).show();
			$("#ard"+i).hide();
			$("#aru"+i).show();
			if(i==1)
			{
				view_chief();
				view_his();
				setTimeout(function(){$("#chief1").focus();},500);
			}
			else if(i==2)
			{
				load_vital_data();
				setTimeout(function(){$("#systolic").focus();},500);
			}
		}
	}
	function print_page()
	{
		var uhid=$("#uhid").val();
		var opd=$("#opd").val();
		url="pages/prescription.php?uhid="+uhid+"&opd="+opd;
		wind=window.open(url,'Window','scrollbars=1,toolbar=0,menubar=1,height=670,width=1050');
	}
	//-----------------------------------------Load Test List Onfocus---------------------------------||
	function load_test_list()
	{
		//$("html,body").animate({scrollTop: '300px'},500);
		$("#ref_doc").fadeIn(500);
		$("#test").select();
		setTimeout(function(){ $("#chk_val2").val(1)},1000);
	}
	//-----------------------------------------Load Medicine List Onfocus-----------------------------||
	function load_medi_list()
	{
		$("html,body").animate({scrollTop: '500px'},500);
		$("#med_div").fadeIn(500);
		$("#medi").select();
		setTimeout(function(){ $("#chk_val1").val(1)},1000);
	}
	//----------------------------------Load/Save/Choose Doctor Onkeyup/Enter-------------------------||
	var doc_tr=1;
	var doc_sc=0;
	function load_test_list1(val,e)
	{
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(unicode!=13)
			{
				if(unicode!=40 && unicode!=38)
				{
					$("#ref_doc").html("<img src='../images/ajax-loader.gif' />");
					$("#ref_doc").fadeIn(500);
					$.post("pages/load_test.php"	,
					{
						val:val,
					},
					function(data,status)
					{
						$("#ref_doc").html(data);	
						doc_tr=1;
						doc_sc=0;
					})
				}
				else if(unicode==40)
				{
					var chk=doc_tr+1;
					var cc=document.getElementById("doc"+chk).innerHTML;
					if(cc)
					{
						doc_tr=doc_tr+1;
						$("#doc"+doc_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
						var doc_tr1=doc_tr-1;
						$("#doc"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
						var z3=doc_tr%1;
						if(z3==0)
						{
							$("#ref_doc").scrollTop(doc_sc)
							doc_sc=doc_sc+30;
						}
					}
				}
				else if(unicode==38)
				{
					var chk=doc_tr-1;
					var cc=document.getElementById("doc"+chk).innerHTML;
					if(cc)
					{
						doc_tr=doc_tr-1;
						$("#doc"+doc_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
						var doc_tr1=doc_tr+1;
						$("#doc"+doc_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
						var z3=doc_tr%1;
						if(z3==0)
						{
							doc_sc=doc_sc-30;
							$("#ref_doc").scrollTop(doc_sc)
						}
					}
				}
				
			}
			else
			{
				var cen_chk1=document.getElementById("chk_val2").value;
				if(cen_chk1!=0)
				{
					var docs=document.getElementById("dvdoc"+doc_tr).innerHTML.split("#");
					var doc_naam=docs[2].trim();
					var rate=docs[3].trim();
					$("#test").val(doc_naam);
					$("#testid").val(docs[1]);
					$("#rate").val(rate);
					var d_in=docs[4];
					//$("#doc_mark").val(docs[5]);
					$("#doc_info").html(d_in);
					$("#doc_info").fadeIn(500);
					select_test();
					if($("#focus_chk").val()!="")
					{
						//$("html, body").animate({ scrollTop: 350 })	
						$("#test").focus();
					}
					else
					{
						$("#test").focus();	
					}
				}
			}
	}
	var med_tr=1;
	var med_sc=0;
	function load_medi_list1(val,e)
	{
			$("#med_dos").hide();
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(unicode!=13)
			{
				if(unicode!=40 && unicode!=38)
				{
					$("#med_div").html("<img src='../images/ajax-loader.gif' />");
					$("#med_div").fadeIn(500);
					$.post("pages/load_medi.php"	,
					{
						val:val,
					},
					function(data,status)
					{
						$("#med_div").html(data);	
						med_tr=1;
						med_sc=0;
					})	
				}
				else if(unicode==40)
				{
					var chk=med_tr+1;
					var cc=document.getElementById("med"+chk).innerHTML;
					if(cc)
					{
						med_tr=med_tr+1;
						$("#med"+med_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
						var med_tr1=med_tr-1;
						$("#med"+med_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
						var z3=med_tr%1;
						if(z3==0)
						{
							$("#med_div").scrollTop(med_sc)
							med_sc=med_sc+30;
						}
					}
				}
				else if(unicode==38)
				{
					var chk=med_tr-1;
					var cc=document.getElementById("med"+chk).innerHTML;
					if(cc)
					{
						med_tr=med_tr-1;
						$("#med"+med_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
						var med_tr1=med_tr+1;
						$("#med"+med_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
						var z3=med_tr%1;
						if(z3==0)
						{
							med_sc=med_sc-30;
							$("#med_div").scrollTop(med_sc)
						}
					}
				}
				
			}
			else
			{
				var cen_chk1=document.getElementById("chk_val1").value
				if(cen_chk1!=0)
				{
					var docs=document.getElementById("mdname"+med_tr).innerHTML.split("#");
					var doc_naam=docs[2].trim()
					$("#medi").val(doc_naam);
					$("#medid").val(docs[1]);
					$("#unit").val(docs[3]);
					var d_in=docs[5];
					//$("#doc_mark").val(docs[5]);
					$("#med_info").html(d_in);
					$("#med_info").fadeIn(500);
					$("#g_name").show();
					select_medi();
					if($("#focus_chk").val()!="")
					{
						//$("html, body").animate({ scrollTop: 350 })	
						$("#dos").focus();
					}
					else
					{
						$("#dos").focus();	
					}
					load_generic(docs[4]);
				}
			}
	}
	function doc_load(id,name,rate)
	{
		$("#test").val(name);
		$("#testid").val(id);
		$("#rate").val(rate);
		$("#doc_info").html("");
		$("#ref_doc").fadeOut(500);
		$("#test").focus();
		select_test();
	}
	function select_med(id,name,typ,gen)
	{
		$("#medi").val(name);
		$("#medid").val(id);
		$("#med_info").html("");
		$("#med_div").fadeOut(500);
		$("#unit").val(typ);
		select_medi();
		load_generic(gen);
	}
	function load_generic(id)
	{
		$.post("pages/global_load_g.php",
		{
			id:id,
			type:"load_generic",
		},
		function(data,status)
		{
			$("#generic").val(data);
		})
	}
	function meditab(id,e)
	{
		if(e.keyCode==13)
		{
			if(id=="dos" && $("#"+id).val().trim()!="")
			$("#ph_quantity").focus();
			if(id=="new_medi" && $("#"+id).val()!="")
			$("#dos").focus();
			if(id=="inst")
			$("#add_medi").focus();
			if(id=="inst")
			$("#add_new_medi").focus();
			if(id=="ph_quantity")
			$("#inst").focus();
		}
	}
	function diagtab(i,e)
	{
		if(e.keyCode==13)
		{
			if($("#diagnosis"+i).val()!="")
			$("#order"+i).focus();
			if($("#order"+i).val()!="0")
			$("#cert"+i).focus();
			if($("#cert"+i).val()!="0")
			$("#addmore").focus();
		}
		else if(e.keyCode==27)
		$("#sav").focus();
	}
	function tab(id,e)
	{
		if(e.keyCode==13)
		{
			if(id=="mid_cum")
			$("#hd_cum").focus();
			if(id=="hd_cum")
			$("#spo").focus();
			if(id=="spo")
			$("#pulse").focus();
			if(id=="pulse")
			$("#temp").focus();
			if(id=="temp")
			$("#pr").focus();
			if(id=="pr")
			$("#rr").focus();
			if(id=="rr")
			$("#systolic").focus();
			if(id=="systolic")
			$("#diastolic").focus();
			if(id=="diastolic")
			$("#vit_note").focus();
			if(id=="vit_note")
			$("#next").focus();
			if(id=="cft")
			$("#ofc").focus();
			if(id=="ofc")
			$("#sav_vit").focus();
			
			
			//~ $("#systolic").focus();
			//~ if(id=="systolic")
			//~ $("#diastolic").focus();
			//~ if(id=="diastolic")
			//~ $("#pulse").focus();
			//~ if(id=="pulse")
			//~ $("#weight").focus();
			if(id=="confident")
			$("#disp_note").focus();
		}
		if(e.keyCode==27)
		{
			if(id=="con_note")
			$("#sav").focus();
			if(id=="history")
			$("#exam").focus();
			if(id=="exam")
			//$("#sav").focus();
			$("#weight").focus();
			if(id=="disp_note")
			$("#sav_dsp").focus();
		}
	}
	function sel_chief(n,e)
	{
		if(e.keyCode==13)
		{
			if($("#chief"+n).val()!="")
			$("#cc"+n).focus();
			if($("#cc"+n).val()!="0")
			$("#tim"+n).focus();
			if($("#tim"+n).val()!="0")
			$("#addmore").focus();
		}
		else if(e.keyCode==27)
		{
			$("#history").focus();
		}
	}
	function physical(val,e)
	{
		var ht=$("#height").val();
		if(ht!='' && val!='')
		{
			var ht=ht/100;
			var bmi=(val/(ht*ht));
			var bmi = bmi.toFixed(2);
			var bmi = bmi.split(".");
			$("#bmi1").val(bmi[0]);
			$("#bmi2").val(bmi[1]);
		}else
		{
			$("#bmi1").val("");
			$("#bmi2").val("");
		}
		
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			$("#height").focus();	
		}
	}
	function physical1(val,e)
	{
		var wt=$("#weight").val();
		if(wt!='' && val!='')
		{
			var val=val/100;
			var bmi=(wt/(val*val));
			var bmi = bmi.toFixed(2);
			var bmi = bmi.split(".");
			$("#bmi1").val(bmi[0]);
			$("#bmi2").val(bmi[1]);
		}else
		{
			$("#bmi1").val("");
			$("#bmi2").val("");
		}
		
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			$("#spo").focus();	
		}
	}
	function next_only()
	{
		var p=parseInt($("#val").val());
		load_res(p+1);
	}
	function next_page()
	{
		var curr_div=$("#val").val().trim();
		var prev_rec=$("#prev_rec").val().trim();
		var p=parseInt($("#val").val());
		//alert(curr_div);
		if(prev_rec=="1")
		{
			if(curr_div=="1")
			{
				save_exam();
				setTimeout(function(){load_res(p+1);},1000);
			}
			else if(curr_div=="2")
			{
				save_diag()
				setTimeout(function(){load_res(p+1);},1000);
			}
			else if(curr_div=="5")
			{
				save_disp();
				setTimeout(function(){load_res(p+1);},1000);
			}
			else
			{
				load_res(p+1);
			}
		}
		else
		{
			load_res(p+1);
		}
	}
	function prev_page()
	{
		var p=parseInt($("#val").val());
		load_res(p-1);
	}
	function insert_data()
	{
		save_disp();
	}
	function load_refer()
	{
		var vl=$("#discom").val();
		//alert(vl);
		if(vl=="2")
		{
			$("#ref_tr").slideDown();
			$("#ref_to").focus();
		}
		else
		{
			$("#ref_tr").hide();
		}
	}
	function view_presc(uhid,opd)
	{
		//url="pages/prescription_doc.php?uhid="+uhid+"&opd="+opd;
		//wind=window.open(url,'Window','scrollbars=1,toolbar=0,menubar=1,height=670,width=1000');
		$.post("pages/global_load_g.php",
		{
			uhid:uhid,
			opd:opd,
			type:"view_presc_doc",
		},
		function(data,status)
		{
			$("#results").html(data);
			$("#mod").click();
		})
	}
	function save_diag()
	{
		var diag="";
		var rr=document.getElementById("diag_table").rows.length;
		for(var j=1;j<=rr;j++)
		{
			if($("#tr"+j).find('td:first input:first').val() && $("#tr"+j).find('td:eq(1) select:first').val()!="0" && $("#tr"+j).find('td:eq(2) select:first').val()!="0")
			diag+=$("#tr"+j).find('td:first input:first').val()+"@"+$("#tr"+j).find('td:eq(1) select:first').val()+"@"+$("#tr"+j).find('td:eq(2) select:first').val()+"#g#";
		}
		$.post("pages/global_insert_data_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			diag:diag,
			usr:$("#user").text(),
			type:"save_diag",
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
				load_print();
				//load_res(2);
				$("#next").focus();
			}, 1000);
		})
	}
	function save_disp()
	{
		/*
		if($("#discom").val()==2 && $("#ref_doc_to").val()==0)
		{
			$("#ref_doc_to").focus();
		}
		else if($("#ref_doc_to").val()=="other" && $("#other_doctor").val()=="")
		{
			$("#other_doctor").focus();
		}
		else
		{
			$("#sav_dsp").attr("disabled",true);
			save_conf();
			$.post("pages/global_insert_data_g.php",
			{
				uhid:$("#uhid").val(),
				opd:$("#opd").val(),
				disp:$("#discom").val(),
				ref_to:$("#ref_doc_to").val(),
				oth_doc:$("#other_doctor").val(),
				disp_note:$("#disp_note").val().trim(),
				usr:$("#user").text(),
				type:"save_disp",
			},
			function(data,status)
			{
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					$("#sav_dsp").attr("disabled",false);
					load_print();
					view_disp();
					$("#nexto").focus();
				}, 1000);
			})
		}
		*/
		//=========================
		$.post("pages/global_insert_data_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			disp:$("#discom").val(),
			ref_to:$("#ref_to").val(),
			usr:$("#user").text(),
			type:"save_disp",
		},
		function(data,status)
		{
			save_conf();
		})
	}
	function save_note()
	{
		$.post("pages/global_insert_data_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			con_note:$("#con_note").val().trim(),
			usr:$("#user").text(),
			type:"save_note",
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
				load_print();
				load_note();
				$("#next").focus();
			}, 1000);
		})
	}
	function save_exam()
	{
		var chief="";
		var rr=document.getElementById("hist_table").rows.length;
		for(var j=1;j<=rr-3;j++)
		{
			if($("#tr"+j).find("input").val() && $("#tr"+j).find("select:first").val()!="0" && $("#tr"+j).find("select:last").val()!="0")
			chief+=$("#tr"+j).find("input").val()+"@"+$("#tr"+j).find("select:first").val()+"@"+$("#tr"+j).find("select:last").val()+"#g#";
		}
		$.post("pages/global_insert_data_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			chief:chief,
			history:$("#history").val().trim(),
			exam:$("#exam").val().trim(),
			usr:$("#user").text(),
			type:"save_exam",
		},
		function(data,status)
		{
			//view_chief();
			//view_his();
			//load_print();
			save_vital();
		})
	}
	function save_vital()
	{
		$.post("pages/global_insert_data_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			weight:$("#weight").val(),
			height:$("#height").val(),
			mid_cum:$("#mid_cum").val(),
			bmi1:$("#bmi1").val(),
			bmi2:$("#bmi2").val(),
			hd_cum:$("#hd_cum").val(),
			pr:$("#pr").val(),
			rr:$("#rr").val(),
			spo:$("#spo").val(),
			pulse:$("#pulse").val(),
			temp:$("#temp").val(),
			systolic:$("#systolic").val(),
			diastolic:$("#diastolic").val(),
			vit_note:$("#vit_note").val(),
			usr:$("#user").text(),
			type:"save_vital",
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
				load_print();
				$("#next").focus();
			}, 1000);
		})
	}
	function calc_totday()
	{
		var tot=0;
		var freq=$("#freq").val();
		var unit=$("#unit_day").val();
		var dur=parseInt($("#dur").val());
		var dos=parseInt($("#dos").val());
		if(unit=="Days")
		tot=(dur*dos*1);
		else if(unit=="Weeks")
		tot=(dur*dos*7);
		else if(unit=="Months")
		tot=(dur*dos*30);
		if(freq=="1")
		tot=tot*1;
		else if(freq=="2")
		tot=tot*1;
		else if(freq=="3")
		tot=tot*2;
		else if(freq=="4")
		tot=tot*3;
		else if(freq=="5")
		tot=tot*4;
		else if(freq=="6")
		tot=tot*5;
		else if(freq=="7")
		tot=tot*24;
		else if(freq=="8")
		tot=tot*12;
		else if(freq=="9")
		tot=tot*8;
		else if(freq=="10")
		tot=tot*6;
		else if(freq=="11")
		tot=tot*5;
		else if(freq=="12")
		tot=tot*4;
		else if(freq=="13")
		tot=tot*3;
		else if(freq=="14")
		tot=tot*3;
		else if(freq=="15")
		tot=tot*2;
		else if(freq=="16")
		tot=tot*2;
		else if(freq=="17")
		tot=tot*(1/2);
		else if(freq=="18")
		tot=tot*(1/7);
		else if(freq=="19")
		tot=tot*(2/7);
		else if(freq=="20")
		tot=tot*(3/7);
		else if(freq=="21")
		tot=tot*(1/14);
		else if(freq=="22")
		tot=tot*(1/21);
		else if(freq=="23")
		tot=tot*(1/30);
		$("#totl").val(tot);
	}
	function view_chief()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			prev_rec:$("#prev_rec").val(),
			usr:$("#user").text(),
			type:"view_chief",
		},
		function(data,status)
		{
			$("#cl1").html(data);
			view_his();
		})
	}
	function view_disp()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			prev_rec:$("#prev_rec").val(),
			usr:$("#user").text(),
			type:"view_disp",
		},
		function(data,status)
		{
			//alert(data);
			$("#disp_res").html(data);
			if($("#discom").val()==2)
			{
				$("#ref_det").slideDown();
				if($("#ref_doc_to").val()=="other")
				{
					$("#inp").show();
				}
				else
				{
					$("#inp").hide();
				}
			}
			else if($("#discom").val()!=2)
			{
				$("#ref_det").hide();
			}
			$("#discom").focus();
			load_disp_texts();
		})
	}
	function load_print()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			type:"load_print",
		},
		function(data,status)
		{
			if(data==1)
			{
				$("#print").show();
			}
			else if(data==0)
			{
				$("#print").hide();
			}
		})
	}
	function load_note()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			usr:$("#user").text(),
			type:"load_note",
		},
		function(data,status)
		{
			$("#con_note").val(data);
		})
	}
	function view_his()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			usr:$("#user").text(),
			type:"view_his",
		},
		function(data,status)
		{
			if(data)
			{
				var vl=data.split("#govinda#");
				if(vl[0]!="")
				$("#history").val(vl[0]);
				if(vl[1]!="")
				$("#exam").val(vl[1]);
			}
		})
	}
	function load_diag()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			type:"load_diag",
		},
		function(data,status)
		{
			$("#diag_res").html(data);
		})
	}
	function load_vital()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			type:"load_vital",
		},
		function(data,status)
		{
			$("#vital_res").html(data);
		})
	}
	function load_comp()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			type:"load_comp",
		},
		function(data,status)
		{
			$("#comp_res").html(data);
		})
	}
	function load_vital_data()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			usr:$("#user").text(),
			type:"load_vital_data",
		},
		function(data,status)
		{
			var vl=data.split("#govinda#");
			$("#weight").val(vl[0]);
			$("#height").val(vl[1]);
			$("#mid_cum").val(vl[2]);
			$("#bmi1").val(vl[3]);
			$("#bmi2").val(vl[4]);
			$("#spo").val(vl[5]);
			$("#pulse").val(vl[6]);
			$("#hd_cum").val(vl[7]);
			$("#pr").val(vl[8]);
			$("#rr").val(vl[9]);
			$("#bp").val(vl[10]);
			$("#temp").val(vl[11]);
			$("#systolic").val(vl[12]);
			$("#diastolic").val(vl[13]);
			$("#vit_note").val(vl[14]);
		})
	}
	function load_diag_data()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			prev_rec:$("#prev_rec").val(),
			usr:$("#user").text(),
			type:"load_diag_data",
		},
		function(data,status)
		{
			$("#diag_res_all").html(data);
		})
	}
	function select_test()
	{
		$.post("pages/global_insert_data_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			test:$("#testid").val(),
			rate:$("#rate").val(),
			usr:$("#user").text(),
			type:"select_test",
		},
		function(data,status)
		{
			$("#ref_doc").fadeOut(500);
			$("#test").val('');
			$("#testid").val('');
			$("#rate").val('');
			load_test();
			//$("html,body").animate({scrollTop: '300px'},500);
			//setTimeout(function(){$('#medi').select2('open');},600);
		})
	}
	function load_test()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			type:"load_test",
		},
		function(data,status)
		{
			$("#test_list").show();
			$("#test_list_data").html(data);
		})
	}
	function select_medi()
	{
		$("#med_dos").show();
		$("#g_name").show();
		$("#dos").focus();
	}
	function insert_medi()
	{
		if($("#dos").val().trim()=="")
		{
			$("#dos").focus();
		}
		// else if($("#freq").val()=="0")
		// {
		// 	$("#freq").focus();
		// }
		// else if($("#st_date").val()=="")
		// {
		// 	$("#st_date").focus();
		// }
		// else if($("#dur").val()=="0")
		// {
		// 	$("#dur").focus();
		// }
		// else if($("#unit_day").val()=="0")
		// {
		// 	$("#unit_day").focus();
		// }
		else
		{
			$.post("pages/global_insert_data_g.php",
			{
				uhid:$("#uhid").val(),
				opd:$("#opd").val(),
				medi:$("#medid").val(),
				dos:$("#dos").val(),
				ph_quantity:$("#ph_quantity").val(),
				inst:$("#inst").val(),
				usr:$("#user").text(),
				type:"insert_medi",
			},
			function(data,status)
			{
				$("#dos").val('');
				$("#inst").val('1');
				$("#med_dos").hide();
				load_medi();
				setTimeout(function(){$('#medi').val('');$("#medi").focus();$("#med_div").fadeOut(10);},100);
			})
		}
	}
	function load_medi()
	{
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			prev_rec:$("#prev_rec").val(),
			type:"load_medi",
		},
		function(data,status)
		{
			$("#medi_list").show();
			$("#medi_list_data").html(data);
			$("#medi").focus();
			load_dos_list();
		})
	}
	function load_dos_list()
	{
		$.post("pages/global_load_g.php",
		{
			type:"load_dos_list",
		},
		function(data,status)
		{
			$("#dos_list").html(data);
			//setTimeout(function(){$("#medi").focus();},400);
		})
	}
	function new_medi()
	{
		$("#medi").hide();
		$("#med_div").hide();
		$("#new_medi").show();
		$("#med_dos").show();
		$("#add_medi").hide();
		$("#add_new_medi").show();
		$("#new_medi").focus();
	}
	function save_new_medi()
	{
		if($("#new_medi").val().trim()=="")
		{
			$("#new_medi").focus();
		}
		else if($("#dos").val().trim()=="")
		{
			$("#dos").focus();
		}
		else
		{
			$("#add_new_medi").attr("disabled",true);
			$.post("pages/global_insert_data_g.php",
			{
				uhid:$("#uhid").val(),
				opd:$("#opd").val(),
				new_medi:$("#new_medi").val(),
				dos:$("#dos").val(),
				inst:$("#inst").val(),
				usr:$("#user").text(),
				type:"save_new_medicine",
			},
			function(data,status)
			{
				//alert(data);
				$("#new_medi").val('');
				$("#dos").val('');
				load_res(4);
			})
		}
	}
	function ref_doc_list()
	{
		if($("#discom").val()==2)
		{
			$("#ref_det").slideDown();
			$("#ref_doc_to").focus();
		}
		else if($("#discom").val()!=2)
		{
			$("#ref_det").hide();
			$("#confident").focus();
		}
	}
	function refer_doc(id,e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			if(id=="discom" && $("#"+id).val()==2)
			{
				$("#ref_det").slideDown();
				$("#ref_doc_to").focus();
			}
			else if(id=="discom" && $("#"+id).val()!=2)
			{
				$("#ref_det").hide();
				$("#confident").focus();
			}
			if(id=="ref_doc_to" && $("#"+id).val()!=0 && $("#"+id).val()!="other")
			{
				$("#ref_doc_to").focus();
			}
			else if(id=="ref_doc_to" && $("#"+id).val()!=0 && $("#"+id).val()=="other")
			{
				$("#other_doctor").focus();
			}
		}
	}
	function load_others()
	{
		if($("#ref_doc_to").val()=="other")
		{
			$("#inp").show();
			$("#other_doctor").focus();
		}
		else
		{
			$("#inp").hide();
			$("#sav_dsp").focus();
		}
	}
	function del_test(t)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to delete this test?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function()
					{
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Delete',
					className: "btn btn-info",
					callback: function()
					{
						$.post("pages/global_delete_g.php",
						{
							uhid:$("#uhid").val(),
							opd:$("#opd").val(),
							test:t,
							type:"delete_test",
						},
						function(data,status)
						{
							load_test();
						})
					}
				}
			}
		});
		
	}
	function rep_pop(uhid,opd,ipd,batch,testid,category_id)
	{
		if(category_id==1)
		{
			$.post("pages/nurs_report_patho.php",
			{
				uhid:uhid,
				opd:opd,
				ipd:ipd,
				batch:batch,
				testid:testid,
			},function(data,status)
			{
				$("#results").html(data);
				$("#mod").click();
			});
		}
		if(category_id==2)
		{
			$.post("pages/nurs_report_rad.php",
			{
				uhid:uhid,
				opd:opd,
				ipd:ipd,
				batch:batch,
				testid:testid,
			},function(data,status)
			{
				$("#results").html(data);
				$("#mod").click();
			});
		}
		if(category_id==3)
		{
			$.post("pages/nurs_report_card.php",
			{
				uhid:uhid,
				opd:opd,
				ipd:ipd,
				batch:batch,
				testid:testid,
			},function(data,status)
			{
				$("#results").html(data);
				$("#mod").click();
			});
		}
	}
	function del_medicine(m)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to delete this medicine?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function()
					{
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Delete',
					className: "btn btn-info",
					callback: function()
					{
						$.post("pages/global_delete_g.php",
						{
							uhid:$("#uhid").val(),
							opd:$("#opd").val(),
							medi:m,
							type:"delete_medi",
						},
						function(data,status)
						{
							load_medi();
						})
					}
				}
			}
		});
	}
	function del_comp(sl)
	{
		$.post("pages/global_delete_g.php",
		{
			sl:sl,
			type:"del_comp",
		},
		function(data,status)
		{
			view_chief();
		})
	}
	function del_diag(sl)
	{
		$.post("pages/global_delete_g.php",
		{
			sl:sl,
			type:"del_diag",
		},
		function(data,status)
		{
			load_diag_data();
		})
	}
	function del_disp(sl)
	{
		$.post("pages/global_delete_g.php",
		{
			sl:sl,
			type:"del_disp",
		},
		function(data,status)
		{
			view_disp();
		})
	}
	function pre_record()
	{
		$("#print").hide();
		$.post("pages/global_load_g.php",
		{
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			opd_crr:$("#opd_crr").val(),
			usr:$("#user").text().trim(),
			type:"opd_prev_record",
		},
		function(data,status)
		{
			$("#disp_res").html(data);
		})
	}
	function no_action()
	{
		bootbox.dialog({ message: "<h4>No Permission to access</h4>"});
		setTimeout(function()
		{
			bootbox.hideAll();
			pre_record();
		}, 1000);
	}
	
	function caps(id,e)
	{
		var vl=$("#"+id).val().toUpperCase();
		$("#"+id).val(vl);

		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			if($("#"+id).val()!="")
			$("#confident").focus();
		}
	}
	function prev_view(opd,pr)
	{
		$("#opd").val(opd);
		$("#prev_rec").val(pr);
		load_print();
		load_res(1);
	}
	function load_res(v)
	{
		//alert(v);
		for(var j=1; j<=6; j++)
		$("#tab"+j).removeClass("active");
		$("#tab"+v).addClass("active");
		$.post("pages/global_load_g.php",
		{
			val:v,
			uhid:$("#uhid").val(),
			opd:$("#opd").val(),
			prev_rec:$("#prev_rec").val(),
			usr:$("#user").text(),
			type:"doc_queue",
		},
		function(data,status)
		{
			$("#load_parts").show();
			$("#load_parts").html(data);
			if(v==1)
			{
				//load_complaint();
				load_print();
				$("#next").show();
				$("#prev").hide();
				$("#save").hide();
				$("#collps1").click();
			}
			else if(v==2)
			{
				load_diag_data();
				setTimeout(function(){$("#diagnosis1").focus();},200);
				load_print();
				$("#next").show();
				$("#prev").show();
				$("#save").hide();
			}
			else if(v==3)
			{
				//$("select").select2({ theme: "classic" });
				$(".datepicker").datepicker(
				{
					dateFormat: 'yy-mm-dd',
					minDate: '0',
				});
				load_test();
				//load_medi();
				//load_impression();
				load_print();
				$("#next").show();
				$("#prev").show();
				$("#save").hide();
				//$("#save").show();
			}
			else if(v==4)
			{
				load_medi();
				load_print();
				$("#next").show();
				$("#prev").show();
				$("#save").hide();
			}
			//~ else if(v==5)
			//~ {
				//~ load_diag();
				//~ load_vital();
				//~ load_comp();
				//~ load_note();
				//~ $("html,body").animate({scrollTop: '200px'},800);
				//~ setTimeout(function(){$("#con_note").focus();},200);
				//~ $("#nexto").show();
				//~ $("#next").hide();
				//~ $("#prev").show();
				//~ $("#save").hide();
			//~ }
			else if(v==5)
			{
				view_disp();
				load_print();
				$("#next").hide();
				$("#prev").show();
				$("#save").hide();
			}
			else if(v==6)
			{
				pre_record();
				$("#next").hide();
				$("#prev").show();
				$("#save").hide();
			}
		})
	}
</script>

<style>
#myModal
{
	left: 30%;
	width:80%;
}
.modal.fade.in
{
	top: 3%;
}
.modal-body
{
	max-height: 540px;
}
#ui-id-1
{
	width:48%;
	max-height:200px;
	overflow-y:scroll;
}
#breadcrumb a.current:hover
{
	cursor:default;
	text-decoration:none;
}
#breadcrumb a:hover
{
	//margin-left:5px;
	//margin-right:-5px;
	//box-shadow: 1px 1px 1px 1px #999999;
	//border-radius: 2px 60px 60px 2px;
	//transition: 0.6s;
	cursor:pointer;
	text-decoration:underline;
}
</style>
