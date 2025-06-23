<div id="content-header">
    <div class="header_div"> <span class="header"> Approval by Doctor</span></div>
</div>
<div class="container-fluid">
			
			<input type="hidden" id="ser_typ" value="1"/>
			
			<table class="table table-bordered table-condensed text-center">
			<tr>
				<td>
					<center>
						<b>From</b>
						<input class="form-control datepicker" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" >
						<b>To</b>
						<input class="form-control datepicker" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" >
						
						<?php
						if($glob_patient_type==0)
						{
							$pat_typ="display:none";
						}
					?>
						
						<select id="pat_type" style="<?php echo $pat_typ;?>">
							<option value="0">--All(Type)--</option>
							<option value="opd_id">--OPD--</option>
							<option value="ipd_id">--IPD--</option>
						</select>
						<select id="dep" name="dep">
							<option value="0">--All(Dept)--</option>
						<?php
							$dep=mysqli_query($link,"select distinct type_id from testmaster where category_id='1' order by type_id");
							while($dp=mysqli_fetch_array($dep))
							{
								$dnm=mysqli_fetch_array(mysqli_query($link,"select * from test_department where id='$dp[type_id]'"));
								echo "<option value='$dp[type_id]'>$dnm[name]</option>";
							}
						?>
						</select>
						<button" id="ser" class="btn btn-search" onclick="load_pat_ser()" style="margin-bottom: 10px;"><i class="icon-search"></i> Search</button>
					</center>
				</td>
			</tr>
			<tr>
				<td colspan="4" style="text-align:center">
					<input type="text" id="uhid" placeholder="ENTER UHID NO" onkeyup="load_pat_list_event(event)"/>
					<input type="text" id="opd" placeholder="ENTER Bill No." onkeyup="load_pat_list_event(event)"/>
					<!--<input type="text" id="barcode" placeholder="ENTER BARCODE NO" onkeyup="load_pat_list_event(event)"/>-->
				</td>
			</tr>
		</table>
		
		<div style="position:absolute;right:20px;">
			<button class="btn btn-info" onclick="chose_dep()"><i class="icon-tag"></i> Choose Department</button>
		</div>
		
		
		<select id="rep_doc" style="display:none">
			<?php
			$rep_doc=mysqli_query($link,"select * from lab_doctor order by name");
			while($rp=mysqli_fetch_array($rep_doc))
			{
				if(trim($_SESSION["emp_id"])==$rp[id]){ echo $sel="Selected='selected'";}else{ $sel='';}
				echo "<option value='$rp[id]' $sel>$rp[name]</option>";
			}
			?>
		</select>
		
		
		


<div>
	<select id="disp" onchange="show_hide_tr(this.value)">
		<option value="all">--All(Display)--</option>
		<option value="success">Approved</option>
		<option value="warning">Partially Approved</option>
		<option value="danger">None Approved</option>
	</select>
</div>

<div id="data">  	



</div>





<div id="loader" style="position:fixed;display:none;z-index:5000"></div>

<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"/>

<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false">
<div class="modal-dialog">
<div class="modal-content">
  
  <div class="modal-body">
	<div id="results"> </div>
  </div>
 
</div>
</div>
</div>
	

<input type="hidden" id="aprv_but_val" value="1"/>	
	
<input type="button" data-toggle="modal" data-target="#myModal2" id="mod2" style="display:none"/>
<div class="modal fade" id="myModal2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false">
	<div class="modal-dialog" id="modal_dial">
	<div class="modal-content">
	  
	  <div class="modal-body">
		<div id="results1"></div>
	  </div>
	 
	</div>
	</div>
</div>	

<input type="button" data-toggle="modal" data-target="#myModal3" id="mod3" style="display:none"/>
<div class="modal fade" id="myModal3" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false">
	<div class="modal-dialog" id="modal_dial">
	<div class="modal-content">
	  
	  <div class="modal-body">
		<div id="results2"></div>
	  </div>
	 
	</div>
	</div>
</div>	

<div style="position:fixed;bottom:0;left:5;text-align:center;padding:5px;background-color:#CCC;opacity:0.9;display:none">
		<button class="btn btn-info">None Approved and not all entry has done</button>
		<button class="btn btn-danger">None Approved</button>
		<button class="btn btn-warning">Partially Approved</button>
		<button class="btn btn-success">Fully Approved</button>
</div>
	
</div>


<style>
		.green{ background-color:#CCFF99;color:black }
		.red{ background-color:#700000 ;color:white }
		.yellow{ background-color:#CCCC00;color:black }
		
		#test_display { font-size:15px}
		#t_bold td { border-bottom:0px}
		
		//.aprv_dne{color:white}
		.display_full_text{ display:none;position:absolute;bottom:80px;width:750px;border:2px solid;background-color:#ccc;padding:10px;left:100px;}
		
		#myModal
		{
			width: 95%;
			left: 23%;
			top:3%;
			height:600px;
			display:none;
		}
		
		#myModal2
		{
			width: 55%;
			top:10%;
			left: 45%;
			display:none;
		}
		
		#myModal .modal-body
		{
			max-height: 561px;
		}
		
	</style>
	<link rel="stylesheet" href="include/css/jquery-ui.css" />
	<link rel="stylesheet" href="../css/animate.css" />
	<script src="include/js/jquery-ui.js"></script>
	<!-- Time -->
	<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />

	<script>
		
		$(document).ajaxStart(function()
		{
			$("#loader").show();
		});
		$(document).ajaxStop(function()
		{
			$("#loader").hide();
		});
		
		$(document).ready(function(){
			
			$(".datepicker").datepicker({
				dateFormat: 'yy-mm-dd',
				maxDate: '0',
			});
			$("#loader").hide();
			load_pat_ser()
		})
		
		function load_pat_ser()
		{
			$("#loader").show();
			$.post("pages/chk_printed_rpt_doc.php",
			{
				pat_type:$("#pat_type").val(),
				fdate:$("#from").val(),
				tdate:$("#to").val(),
				dep:$("#dep").val(),
				uhid:$("#uhid").val(),
				opd:$("#opd").val(),
				type:2
			},
			function(data,status)
			{
				$("#data").html(data);
				
				var dis_val=$("#disp").val();
				show_hide_tr(dis_val);
			})	
		}
		
		
				
		function load_page1()
		{
			var fdate=$("#from").val();
			var tdate=$("#to").val();
			var dep=$("#dep").val();
			
			/*
			url="pages/chk_printed_rpt.php"+"?fdate="+fdate+"&tdate="+tdate+"&dep="+dep;
			wind=window.open(url,'Window','scrollbars=1,toolbar=0,height=670,width=1050');		
			*/
			
			$.post("pages/chk_printed_rpt_doc.php",
			{
				fdate:fdate,
				tdate:tdate,
				dep:dep
			},
			function(data,status)
			{
				$("#data").html(data);
				
				var dis_val=$("#disp").val();
				show_hide_tr(dis_val);
			})
		}
		
		function approve_win(val,dep)
		{
				
				$.post("pages/tech_sample_pat_doc_all.php",
				{
					pid:$("#pid_"+val).val(),
					visit:$("#vis_"+val).val(),
					val:val,
					dep:dep,
					user:$("#user").text(),
					type:"1"
				},
				function(data,status)
				{
					$("#results").html(data);
					//$("#myModal").css({'top':'5% !important'});
					$("#mod").click();
					$("#results").fadeIn(500,function(){ $('#results').animate({ scrollTop: 0}, "slow"); $("#sam_aprv_div").focus();});
					
					/*
					$("#results").css({'width':'1200px','height':'400px'});
					var w=$("#results").width()/2;
					var h=$("#results").height()/2;
					document.getElementById("results").style.cssText+="margin-left:-"+w+"px;margin-top:-"+h+"px";
					$("#back").fadeIn(100);
					$("#results").html(data);
					$("#results").slideDown(500,function(){ $("#tinfo").fadeIn(300);$("#tinfo").focus()});
					*/
				})
    	}
    	
    	function hid_div(e)
		{
			
			var scroll = $(window).scrollTop();
			var unicode=e.keyCode? e.keyCode : e.charCode;
			if(unicode==27)
			{
				//~ if($("#results").css("display")=="block")
				//~ {
					//~ if($("#results1").css("display")=="block")
					//~ {
						//~ $("#mod2").click();
						//~ setTimeout(function(){ $("#results1").css({'display':'none'}) },200);
					//~ }
					//~ else
					//~ {
						//~ $("#mod").click();
						//~ setTimeout(function(){ $("#results").css({'display':'none'}) },200);
						
					//~ }
				//~ }
				
				if($('#myModal2').hasClass('in'))
				{
					$('#myModal2').modal('hide');	
				}
				else if($('#myModal').hasClass('in'))
				{
					$('#myModal').modal('hide');	
				}
				
				load_pat_ser();
				//$("html, body").animate({ scrollTop: scroll })
				
				
			}
		}
		function win_exit()
		{
			if($("#results").css("display")=="block")
				{
					if($("#results1").css("display")=="block")
					{
						$("#mod2").click();
						setTimeout(function(){ $("#results1").css({'display':'none'}) },500);
					}
					else
					{
						$("#mod").click();
						setTimeout(function(){ $("#results").css({'display':'none'}) },500);
					}
				}
				
				if($("#ser_typ").val()==1)
				{
					load_patient('','');
				}
				else if($("#ser_typ").val()==2)
				{
					$("#reg_no").focus();
					load_patient($("#reg_no").val(),e);
				}
				//$("html, body").animate({ scrollTop: scroll })
		}
		
		function check_upd_test(tst)
		{
			$("#test_upd_"+tst+"").val("1");
		}
		function approve(val,typ,iso_no)
		{
			
			var aprv=0;
			if($("#aprv_"+val).is(":checked"))
			{
				aprv=1;
			}
			
				var tst=$("#test_id_"+val).val();
				var upd_v=$("#test_upd_"+tst+"").val();
				var err=0;	
				/*var chk_dlc=0;
				var err=0;
				var a=b=c=d=e=0;
				
				var chk_tst=$("[name=test_"+tst+"]");
				for(var i=0;i<chk_tst.length;i++)
				{
					if($(chk_tst[i]).attr("class")=="par_126")
					{
						if(parseInt($(".par_126").val()))
						{
							a=parseInt($(".par_126").val());
						}
						else { a=0;}
					}
					if($(chk_tst[i]).attr("class")=="par_127")
					{
						if(parseInt($(".par_127").val()))
						{
							b=parseInt($(".par_127").val());
						}
						else { b=0;}
					}
					if($(chk_tst[i]).attr("class")=="par_128")
					{
						if(parseInt($(".par_128").val()))
						{
							c=parseInt($(".par_128").val());
						}
						else { c=0;}
					}
					if($(chk_tst[i]).attr("class")=="par_129")
					{
						if(parseInt($(".par_129").val()))
						{
							d=parseInt($(".par_129").val());
						}
						else { d=0;}
					}
					if($(chk_tst[i]).attr("class")=="par_142")
					{
						if(parseInt($(".par_142").val()))
						{
							e=parseInt($(".par_142").val());
						}
						else { e=0;}
					}	
				}
								
				if(a>0 || b>0 || c>0 || d>0 || e>0)
				{
					chk_dlc=a+b+c+d+e;
					if(chk_dlc!=100)
					{
						
						if($(".par_285").length>0)
						{
							if($(".par_285").val()=="" || $(".par_285").val()=="Nil" || $(".par_285").val()=="NIL")
							{ 
								alert("DLC is "+chk_dlc+".Must be equal to 100");
								err=1;
							}
							else
							{
								err=0;
							}
						}
						else
						{
							alert("DLC is "+chk_dlc+".Must be equal to 100");
							err=1;
						}
					}
					else
					{
						
					}
				}
				*/
			//~ if(upd_v>0)
			//~ {
				//~ bootbox.alert("Change detected in results.Please update before approving");	
				//~ $("#aprv_"+val+"").prop("checked",false);			
			//~ }
			
			var check_save=0;
			$(".check_save_"+tst+"").each(function(){
					if($(this).val()==1)
					{
						check_save++; 
					}
				})
			
						
			if(check_save>0)
			{
				alert("Few results must be saved before approving");
				err=1;
			}
			var upd_v=0;
			if(err==0 && upd_v==0)
			{
				
				$.post("pages/tech_chk_approve_doc.php",
				{
					pin:$("#pin").val(),
					batch:$("#batch").val(),
					test:tst,
					aprv:aprv,
					user:$("#rep_doc").val(),
					typ:typ,
					iso_no:iso_no,
					type:"aprv"
				},
				function(data,status)
				{
					//alert(data);
					/*
					$.post("pages/tech_chk_approve_doc.php",
					{
						pid:$("#patient_id").val(),
						visit:$("#visit_no").val(),
						test:$("#test_id_"+val).val(),
						
					},
					function(data,status)
					{
						
					})
					*/
				})
			}
			else
			{
				setTimeout(function(){ $("#aprv_"+val+"").prop("checked",false); },200);
				
			}
			
		}
		
		var apr=0;
		function aprv_onkey(e)
		{
			var unicode=e.keyCode? e.keyCode : e.charCode;
			alert(unicode);
			if(unicode==40)
			{
				/*
				var chk_ap=apr+1;
				if($("#aprv_"+chk_ap).attr("id"))
				{
					apr=apr+1;
					$("#aprv_"+apr).focus();	
				}
				*/
			}
			else if(unicode==38)
			{
				/*
				var chk_ap=apr-1;
				if($("#aprv_"+chk_ap).attr("id"))
				{
					apr=apr-1;
					$("#aprv_"+apr).focus();	
				}
				*/
			}
			else if(unicode==13)
			{
				
					if(confirm("Do you want to approve all the result?"))
					{
						//$(".aprv_check:checkbox:not(:checked)").click();	
					}
				
			}
			else if(unicode==27)
			{
				apr=0;	
			}
		}
		function chk_value(i,val)
		{
			var res=$("#res_"+i).val();
			if(res)
			{
				$("#aprv_"+i).attr("disabled",false);
			}
			else
			{
				$("#aprv_"+i).attr("disabled",true);
			}			
		}
		function load_normal(uhid,param,val,no)
			{
				
				$.post("pages/pathology_normal_range_new.php",
				{
					uhid:uhid,
					param:param,
					val:val
				},
				function(data,satus)
				{
					
					
				})
			}
			
			function load_normal(uhid,param,val,no)
			{
				$.post("pages/pathology_normal_range_new.php",
				{
					uhid:uhid,
					param:param,
					val:val
				},
				function(data,satus)
				{
					
					var data=data.split("#");
					
					$("#norm_r"+no).html(data[0]);
					
								
								
					if(data[1]=="Error")
					{
						$("#result"+no).css({'color':'red'});
					}
				})
			}
			function load_reg_test(val)
			{
				
			}
			function load_patient(reg,e)
			{
				
				var unicode=e.keyCode? e.keyCode : e.charCode;
				if(unicode==13)
				{
					$("#app1").click();
				}	
				else
				{
					
					var scroll = $(window).scrollTop();
					$.post("pages/chk_printed_rpt_doc.php",
					{
						reg:reg,
						dep:$("#dep").val(),
						fdate:$("#from").val(),
						tdate:$("#to").val(),
						user:$("#user").text(),
						pat_type:$("#pat_type").val(),
						type:"2"
					},
					function(data,status)
					{
						
						$("#data").html(data);
						
						
						$("#ser_typ").val("1");
						if(reg)
						{
							$("#ser_typ").val("2");
							$("#reg_no").focus();
						}
						//$("html, body").animate({ scrollTop: scroll })
						
						var dis_val=$("#disp").val();
						show_hide_tr(dis_val);
						
						/*var but_id=$("#aprv_but_val").val();
						$("[name="+but_id+"]").focus();*/
					})
					
				}
			}
			
			function approve_win_doc(val)
			{
					var dep=$("#dep").val();
					var b_nm=$("#app"+val+"").attr("name");
					$("#aprv_but_val").val(b_nm);
					$.post("pages/tech_sample_pat_doc_all.php",
					{
						pin:$("#opd_"+val+"").val(),
						batch:$("#batch_"+val+"").val(),
						dep:dep,
						user:$("#user").text(),
						type:"2"
					},
					function(data,status)
					{
						$("#results").html(data);
						$("#mod").click();
						$("#sam_aprv_div").focus();
						$("#results").fadeIn(500,function(){ $("#tinfo").fadeIn(300);if(dep>0){ $("[class='name_"+dep+"']")[0].click();} $('#results').animate({ scrollTop: 0}, "slow"); $("#sam_aprv_div").focus()});
						setTimeout(function(){ $("#sam_aprv_div").focus(); }, 1000);
					})
			}
			var tab=1;
			function approve_key(e)
			{
				var unicode=e.keyCode? e.keyCode : e.charCode;
				if(unicode==37)
				{
					var ntab=tab-1;
					
					if($("#tab_pentry"+ntab+"").length>0)
					{
						$("#tab_pentry"+ntab+"").click();
						tab=ntab;
					}
				}
				else if(unicode==39)
				{
					var ntab=tab+1;
					
					if($("#tab_pentry"+ntab+"").length>0)
					{
						$("#option_a"+ntab+"").click();
						tab=ntab;
					}
				}
				else if(unicode==13)
				{
					var chk=0;
					$("[class*=active] .aprv_check:checkbox:not(:checked)").each(function()
					{ 
						$(this).prop("checked",true);
						$(this).click();
						$(this).prop("checked",true);
						//var vals=$(this).attr("name"); 
						//var vars=vals.split("#@koushik@#");
						//approve(vars[0],vars[1]);
					});
				}
			}
			
			function approve_key_all(e)
			{
				if(e.which==13)
				{
					$("#sam_aprv_div .aprv_check:checkbox:not(:checked)").each(function()
					{ 
						$(this).prop("checked",true);
						$(this).click();
						$(this).prop("checked",true);
					});
				}
			}
			
			function update_test_res(val,i,pin,tst,prm)
			{
				bootbox.dialog({ message: "<b id='upd_res_"+i+"'>Updating</b>"});
				
				var res="";
				if($("#result_res"+i+"").length>0)
				{
					res=$("#result_res"+i+"").val();
				}
				else if($("#result_"+i+"").length>0)
				{
					res=$("#result_"+i+"").text();
				}
				
				$.post("pages/tech_chk_approve_doc.php",
				{
					type:"update",
					pin:pin,
					batch:$("#batch").val(),
					tst:tst,
					prm:prm,
					res:res,
					user:$("#rep_doc").val()
						
				},
				function(data,status)
				{
					$("#upd_res_"+i+"").text("Updated");
					setTimeout(function()
					{
						bootbox.hideAll();
						$("#button_"+i+"").val("Updated");
						$("#test_upd_"+tst+"").val("0");
						$("#check_save_"+tst+"_"+prm+"").val("0");
					},1000)
					
					$.post("pages/tech_sample_pat_doc_all.php",
					{
						pin:$("#pin").val(),
						batch:$("#batch").val(),
						dep:$("#dep").val(),
						user:$("#user").text(),
						type:"2"
					},
					function(data,status)
					{
						$("#results").html(data);
					})
					
				})
			}
			function update_all(pin,tst)
			{
				var test=$("[name=test_"+tst+"]");
				var upd_all="";
				for(var i=0;i<test.length;i++)
				{
					
						var param_id=$(test[i]).attr("class").split("_");
						var res=$(test[i]).val().trim();
						upd_all=upd_all+"@@"+param_id[1]+"###koushik###"+res;
					
				}
				
				$.post("pages/tech_chk_approve_doc.php",
				{
					type:"update_all",
					pin:pin,
					batch:$("#batch").val(),
					tst:tst,
					upd_all:upd_all,
					user:$("#rep_doc").val()
				},
				function(data,status)
				{
					$("#test_upd_"+tst+"").val("0");
					$(".check_save_"+tst+"").each(function()
					{
						$(this).val("0");
					})
					bootbox.alert("Updated");
				})
			}
			function update_result(val,vl)
			{
				if(vl=="Edit")
				{
					$("#summary_res_"+val+"").attr("contentEditable",true).focus();
					$("#summ_res_bt_"+val+"").val("Update");
					$("#test_upd_"+$("#test_id_"+val).val()+"").val("1");
				}
				else if(vl=="Update")
				{
					
					$.post("pages/tech_chk_approve_doc.php",
					{
						pin:$("#pin").val(),
						batch:$("#batch").val(),
						test:$("#test_id_"+val).val(),
						result:$("#summary_res_"+val+"").html(),
						user:$("#rep_doc").val(),
						type:"update_res_pad"
					},
					function(data,status)
					{
						$("#aprv_"+val+":checkbox").prop("checked",true);
						$("#summary_res_"+val+"").attr("contentEditable",false);
						$("#summ_res_bt_"+val+"").val("Edit");
						$("#test_upd_"+$("#test_id_"+val).val()+"").val("0");
					})
				}
			}
			
			
			function load_note(val)
			{
				$.post("pages/pathology_add_note.php",
				{
					pin:$("#pin").val(),
					batch:$("#batch").val(),
					tid:$("#test_id_"+val).val(),
					val:val
				},
				function(data,status)
				{
					$("#results1").html(data);
					$("#mod2").click();
					$("#results1").fadeIn(300,function(){ $("#test_note").focus(); });
				})
			}
			
			function load_verify(val)
			{
				$.post("pages/pathology_test_reverify.php",
				{
					uhid:$("#patient_id").val(),
					visit:$("#visit_no").val(),
					tid:$("#test_id_"+val).val(),
					user:$("#user").text(),
					val:val,
					type:1
				},
				function(data,status)
				{
					$("#results1").html(data);
					if($("#results1").css("display")!="block")
					{
						$("#modal_dial").css({"width":"800px"});
						$("#mod2").click();
						$("#results1").fadeIn(300,function(){ $("#test_note").focus(); });
					}
				})
			}
			
			function reverify_test(val)
			{
				$.post("pages/pathology_test_reverify.php",
				{
					uhid:$("#patient_id").val(),
					visit:$("#visit_no").val(),
					req_doc:$("#req_doc").val(),
					remark:$("#ver_rem").val(),
					tid:$("#test_id_"+val).val(),
					user:$("#user").text(),
					val:val,
					type:2
				},
				function(data,status)
				{
					if(!$("#aprv_"+val+"").is(':checked'))
					{
						$("#aprv_"+val+"").click();
					}
					
					$("#verify_"+val+"").prop("class","btn btn-danger btn-xs");
					$("#verify_"+val+"").html("<b>Reverification Pending</b>");
					load_verify(val);
				})
			}
			
			function test_verify(val)
			{
				$.post("pages/pathology_test_reverify.php",
				{
					uhid:$("#patient_id").val(),
					visit:$("#visit_no").val(),
					tid:$("#test_id_"+val).val(),
					user:$("#user").text(),
					val:val,
					type:3
				},
				function(data,status)
				{
					$("#verify_"+val+"").prop("class","btn btn-success btn-xs");
					$("#verify_"+val+"").prop("onclick","");
					$("#verify_"+val+"").html("<b>Verified By "+data+"</b>");
				})
			}
			
			function cancel_verify(val)
			{
				$.post("pages/pathology_test_reverify.php",
				{
					uhid:$("#patient_id").val(),
					visit:$("#visit_no").val(),
					tid:$("#test_id_"+val).val(),
					user:$("#user").text(),
					val:val,
					type:4
				},
				function(data,status)
				{
					$("#verify_"+val+"").prop("class","btn btn-primary btn-xs");
					$("#verify_"+val+"").click(function() { load_verify(val) });
					$("#verify_"+val+"").html("<b>Request to Verify</b>");
					load_verify(val);
				})
			}
					
			function save_note(tid,val)
			{
				bootbox.dialog({ message: "<b id='upd_note'>Saving..</b>"});
				$.post("pages/pathology_save_note.php",
				{
					pin:$("#pin").val(),
					batch:$("#batch").val(),
					tid:tid,
					note:$("#test_note").val(),
					user:$("#user").text(),
				},
				function(data,status)
				{
					$("#upd_note").text("Saved");
					setTimeout(function()
					{
						bootbox.hideAll();
					},1000)
					
					if($("#note_"+val+"").length>0)
					{
						$("#note_"+val+"").html("<b><i><u>View Note</u></i></b>");
					}
					else
					{
						$.post("pages/tech_sample_pat_doc_all.php",
						{
							pin:$("#pin").val(),
							batch:$("#batch").val(),
							dep:$("#dep").val(),
							user:$("#user").text(),
							type:"2"
						},
						function(data,status)
						{
							$("#results").html(data);
						})
					}
				})
			}
			function load_patient_date()
			{
				$.post("pages/chk_printed_rpt_doc.php",
				{
					fdate:$("#from").val(),
					tdate:$("#to").val(),
					aprv:$("#appr").val(),
					user:$("#user").text(),
					type:1
				},
				function(data,status)
				{
					$("#data").html(data);
					$("#ser_typ").val("2");
				})	
			}
			function load_pat_dep()
			{
				$.post("pages/chk_printed_rpt_doc.php",
				{
					fdate:$("#from").val(),
					tdate:$("#to").val(),
					dep:$("#dep").val(),
					user:$("#user").text(),
					type:1
				},
				function(data,status)
				{
					
				})				
			}
		    function check_formula(fid)
		    {
				var chk_form=fid.split("result_res");
				if($("#"+chk_form[1]+"").attr("class")!="formula")
				{
					var form=$(".formula");
					for(var i=0;i<form.length;i++)
					{
						var val=$(form[i]).val();
						var id="result_res"+$(form[i]).attr("id");
						
						var dec=$(form[i]).attr("name");
						if(fid!=id)
						{
							check_form(id,val,dec);
						}
					}
				}
			}
			function check_form(id,form,dec)
			{
				var sqr_chk=0;
				var form=form.split("@");
				var fr="";
				var nv=0;
				for(var i=0;i<form.length;i++)
				{
					var chk=form[i].split("p");
					if(chk[1]>0)
					{			
						if($(".par_"+chk[1]).length>0)
						{
							if($(".par_"+chk[1]).val()!='')
							{
								fr+=$(".par_"+chk[1]).val();
							}
							else
							{
								fr+="0";
								nv++;
							}
						}
						else
						{
							break;
						}
						
					}
					else
					{
						if(form[i]=="sqr_root")
						{
							fr+="Math.sqrt(";
							var sqr_chk=1;
						}
						else
						{
							fr+=form[i];
							if(sqr_chk==1)
							{
								fr+=")";
								sqr_chk=0;
							}
							
							
						}
					}
				}
				
				
				
				var res=eval(fr).toFixed(dec);
				
				
				if(nv==0)
				{		
					$("#"+id+"").val(res);
				}
				else
				{
					$("#"+id+"").val("");
				}
		}
		
		function display_text(typ,val)
		{
			if(typ==1)
			{
				if($("#display_text"+val+"").css("display")=="none")
				{
					$("#display_text"+val+"").html($("#result_res"+val+"").val()).fadeIn(500);
				}
			}
			else
			{
				if($("#display_text"+val+"").css("display")=="block")
				{
					$("#display_text"+val+"").fadeOut(500,function(){ $("#display_text"+val+"").html(""); });
				}
			}
		}
		
		function load_pat_list_event(e)
		{
			if(e.which==13)
			{
				load_pat_ser();	
			}
		}
		
		function show_hide_tr(val)
		{
			if(val != "all")
			{
				$("#pat_table tr").hide();
				$("#pat_table tr:first, #tr_head").show();
				$("[class*='" + val + "']").parent().parent().show();
			}else
			{
				$("#pat_table tr").show();
			}
			
		}
		function tst_display_summ(val)
		{
			if($("#btn_tst_summ_"+val+"").val()=="hide")
			{
				$("#tst_summ_txt_"+val+"").slideDown(200,function(){ $("#btn_tst_summ_"+val+"").val("show");$("#btn_tst_summ_"+val+"").text("Hide Summary");});
			}
			else
			{
				$("#tst_summ_txt_"+val+"").slideUp(200,function(){ $("#btn_tst_summ_"+val+"").val("hide");$("#btn_tst_summ_"+val+"").text("Show Summary");});
			}
		}
		
		function load_sum_edit(tst)
		{
			$("#test_sum_"+tst+"").hide();
			$("#test_sum_edit_"+tst+"").show();
			if (CKEDITOR.instances['article-body']) 
			{
				CKEDITOR.instances['article-body'].destroy(true);
			}
			CKEDITOR.replace('article-body');
			CKEDITOR.config.enterMode=CKEDITOR.ENTER_BR;
			CKEDITOR.config.height = 300;
		}
		function load_sum_edit_hide(tst)
		{
			$("#test_sum_edit_"+tst+"").hide();
			$("#test_sum_"+tst+"").show();
		}
		
		function save_summary(tst)
		{
			bootbox.dialog({ message: "<b id='upd_sum_"+tst+"'>Updating</b>"});
			$.post("pages/tech_chk_approve_doc.php",
			{
				pin:$("#pin").val(),
				batch:$("#batch").val(),
				test:tst,
				user:$("#rep_doc").val(),
				result:$("#test_sum_edit_"+tst+" #rad_res").contents().find('body').html(),
				type:"update_res_pad"
			},
			function(data,status)
			{
				$("#upd_sum_"+tst+"").text("Updated");
				setTimeout(function()
				{
					bootbox.hideAll();
				},1000)
				
				$.post("pages/tech_sample_pat_doc_all.php",
				{
					pin:$("#pin").val(),
					batch:$("#batch").val(),
					dep:$("#dep").val(),
					user:$("#user").text(),
					type:"2"
				},
				function(data,status)
				{
					$("#results").html(data);
				})
			})
			
		}
		
		function chose_dep()
		{
			$.post("pages/tech_chk_approve_doc.php",
			{
				user:$("#user").text(),
				type:"doc_dept"
			},
			function(data,status)
			{
				$("#results1").html(data);
				$("#mod2").click();
				$("#results1").fadeIn(300);
			})
		}
		function save_doc_dept()
		{
			bootbox.dialog({ message: "<b id='doc_dep_save'>Saving..</b>"});
			var doc=$("#user").text();
			
			var aprv="";
			var aprv_lst=$(".app_dep:checked")
			for(var i=0;i<aprv_lst.length;i++)
			{
				aprv+="@@"+$(aprv_lst[i]).val();
			}
				
			var disp="";
			var dis_lst=$(".dis_dep:checked");
			for(var j=0;j<dis_lst.length;j++)
			{
				disp+="@@"+$(dis_lst[j]).val();
			}	
			
			$.post("pages/tech_chk_approve_doc.php",
			{
				doc:doc,
				aprv:aprv,
				disp:disp,
				type:"doc_dept_save"
			},
			function(data,status)
			{
				$("#doc_dep_save").text("Saved");
				setTimeout(function()
				{
					bootbox.hideAll();
					$("#mod2").click();
				},1000)
			})
		}
		function check_approve(dep)
		{
			if($("#apr_"+dep+"").prop("checked"))
			{
				$("#dis_"+dep+"").prop("checked",true)
			}
		}
		function check_display(dep)
		{
			if($("#dis_"+dep+"").prop("checked")==false)
			{
				$("#apr_"+dep+"").prop("checked",false)
			}
		}
		
		function check_all(elem,val)
		{
			if(val==1)
			{
				if($(elem).prop("checked"))
				{
					var apr="";
					var apr_lst=$(".app_dep:not(:checked)");
					for(var i=0;i<apr_lst.length;i++)
					{
						$(apr_lst[i]).prop("checked",true);
						var dep=$(apr_lst[i]).val();
						$("#dis_"+dep+"").prop("checked",true);
					}
				}
				else
				{
					$(".app_dep:checked").prop("checked",false);
				}
			}
			else
			{
				if($(elem).prop("checked"))
				{
					$(".dis_dep:not(:checked)").prop("checked",true);
				}
				else
				{
					var dis="";
					var dis_lst=$(".dis_dep:checked");
					for(var i=0;i<dis_lst.length;i++)
					{
						$(dis_lst[i]).prop("checked",false);
						var dep=$(dis_lst[i]).val();
						$("#apr_"+dep+"").prop("checked",false);
					}
				}
			}
		}
		
		function show_dept(elem)
		{
			
		}
		
		function group_view_test(uhid,opd_id,ipd_id,batch_no)
		{
			var user = $("#user").text().trim();
			var tst="";
			
			var url = "pages/pathology_report_print.php?uhid=" + btoa(uhid) + "&opd_id=" + btoa(opd_id) + "&ipd_id=" + btoa(ipd_id) + "&batch_no=" + btoa(batch_no) + "&tests=" + btoa(tst) + "&hlt=" + btoa(tst) + "&user=" + btoa(user) +"&sel_doc=" + btoa(0) + "&view=" + btoa(0) + "&doc_view=" + btoa(1);
			
			var win = window.open(url, '', 'fullScreen=yes,scrollbars=yes,menubar=yes');
			
		}
	</script>
	<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="../fine-uploader/fine-uploader.min.js"></script>
<style>
.test_res { border-top:1px solid;border-bottom:1px solid;padding-top:10px;padding-bottom:10px;}	
.test_res_par { padding-top:5px;padding-bottom:5px;}	
.test_res div{ font-size:13px; font-weight:bold;}
.test_res_par div:first-child{  }
.res_error{color:red;}
.upd_res_div{ font-size:12px !important;font-style:italic;color:red;font-weight:normal !important}
.tst_note{ font-size:12px !important;margin-top:5px;font-weight:normal !important}
.par_name{ margin-left:15px;font-size:11px;font-weight:550 !important}

.test_res { border-top:1px solid;border-bottom:1px solid;padding-top:10px;padding-bottom:10px;}	
.test_res_sum { border-top:1px solid;border-bottom:1px solid;padding-top:10px;padding-bottom:10px;}	
.test_res_sum .span6,.test_res_sum .span4 {font-weight:bold;}
.test_sum{margin-left:20px;}

.sum_edit{ color:red;font-style:italic;cursor:pointer;}
</style>
