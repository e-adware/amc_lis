<!--header-->

<div id="content-header">
	<div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div>
		<b style="display:none;">Patient Type: </b>
		
	</div>
	<table class="table table-bordered text-center">
		<tr>
			<td style="text-align:center" colspan="2">
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
					<option value="opd_id">OPD</option>
					<option value="ipd_id">IPD</option>
				</select>
				<button class="btn btn-success" onClick="view_all()" style="margin-bottom: 10px;">View</button>
			</td>
		</tr>
		<tr>
			<td style="text-align:right">
				<b>Name</b>
				<input type="text" id="pat_name" class="src" placeholder="Enter to search" onKeyup="view_pat_event(this,event)">
			</td>
			<td style="text-align:left">
				<select class="span2" id="catagory">
					<option value="pin">BILL ID</option>
					<option value="uhid">UHID</option>
				</select>
				<input type="text" class="span2" id="var_id" class="src" placeholder="Enter to search" onKeyup="view_pat_event(this,event)" autofocus>
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		
	</div>
</div>
<div class="text-center" style="position: fixed; bottom: -20px; right: 0px; padding: 10px; background: #fff; color: #000; box-shadow: 0 0 10px rgba(0,0,0,0.5); z-index:100;">
	<table class="table table-bordered table-condensed">
		<tr>
			<td class=""><span class="btn_round_msg red"></span> Not Received</td>
			<td class=""><span class="btn_round_msg green"></span> Received</td>
			<td class=""><span class="btn_round_msg yellow"></span> Partially Received</td>
		</tr>
	</table>
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
<div id="loader" style="margin-top:-10%;"></div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<link rel="stylesheet" href="../css/animate.css" />
<script src="include/js/jquery-ui.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<!-- Select2 Plugin -->
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>

<script>
	$(document).ready(function(){
		$("#loader").hide();
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
		});
		view_all('date');
	});
	function hid_div(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==27)
		{
			//$("#mod").click();
			$('.modal').modal('hide');
		}
	}
	function hid_mod()
	{
		$("#mod").click();
	}
	
	function view_all()
	{
		$("#loader").show();
		$.post("pages/lab_sample_pat.php",
		{
			pat_type:$("#pat_type").val(),
			from:$("#from").val(),
			to:$("#to").val(),
			pat_name:$("#pat_name").val(),
			catagory:$("#catagory").val(),
			var_id:$("#var_id").val(),
			user:$("#user").text(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		})
	}
	
	function view_pat_event(elem,e)
	{
		if(e.which==13)
		{
			//$(".src:not("+elem+"").val("");
			view_all();
		}
	}
	
	function check_vac_err(name)
	{
		bootbox.dialog({ message: "<b style='color:red'>"+name+" is already processed. Can not be removed</b>"});
		setTimeout(function()
		{
			bootbox.hideAll();
		},2500)
	}
	
	function load_sample(uhid,opd,ipd,batch_no)
	{
		$.post("pages/lab_load_sample.php",
		{
			uhid:uhid,
			opd:opd,
			ipd:ipd,
			batch_no:batch_no,
			lavel:$("#lavel_id").val(),
			user:$("#user").text().trim(),
		},
		function(data,status)
		{
			$("#results").html(data);
			$("#mod").click();
			$("#results").fadeIn(500,function(){ load_vaccu(); });
			$(".disease_id").select2({ theme: "classic" });
		})
	}
	
	function select_sample(id)
	 {
		if($("#"+id).is(":checked"))
		{
			$("."+id).click();
		}
		else
		{
			if($("#val_"+id).val()=="0")
			{
				$("."+id).attr('checked',false);
			}
			else
			{
				$("#"+id).attr('checked',true);
				bootbox.alert("Sample already accepted by Lab. Action cannot be completed");
				
			}
		}
		
		load_vaccu();
	 }
	 
	 function load_vaccu()
	 {
		var tst="";
		var samp=$(".samp:checked")
		for(var i=0;i<samp.length;i++)
		{
			var test=$("."+$(samp[i]).attr("id"));
			for(var j=0;j<test.length;j++)
			{
				tst=tst+"@"+$(test[j]).val();
			}
		}
		
		$(".vac").attr("checked",false);
		$.post("pages/phlebo_load_vaccu.php",
		{
			tst:tst
		},
		function(data,status)
		{
			var vc=data.split("@");
			for(var k=0;k<vc.length;k++)
			{
				if(vc[k])
				{
					$("#vac_"+vc[k]+"").click();
				}
			}
		})
	}
	 function print_barcode(val)
	 {
		 var pid=patient_id=$('#h_no').text().trim();
		 var opd_id=$('#opd_id').text().trim();
		 var ipd_id=$('#ipd_id').text().trim();
		 var batch_no=$('#batch_no').text().trim();
		 var user=$("#user").text().trim();
		 
		 var test=val.split("_");
		 
		 var url="pages/barcode_generate_single.php?pid="+pid+"&opd_id="+opd_id+"&ipd_id="+ipd_id+"&batch_no="+batch_no+"&user="+user+"&tid="+test[0];
		 window.open(url,'','fullscreen=yes,scrollbars=yes');
	 }
	function note(a)
	{
		$.post("pages/update_note.php",
		{
			test_id:a,
			patient_id:$('#h_no').text().trim(),
			opd_id:$('#opd_id').text().trim(),
			ipd_id:$('#ipd_id').text().trim(),
			batch_no:$('#batch_no').text().trim(),
			user:$('#user').text().trim(),
		},
		function(data,status)
		{
			bootbox.dialog({
			  message: "Note:<input type='text' value='"+data+"' id='note' autofocus />",
			  title: "Note",
			  buttons: {
				main: {
				  label: "Save",
				  className: "btn-primary",
				  callback: function() {
					if($('#note').val()!='')
					{
						$.post("pages/sample_notes.php",
						{
							test_id:a,
							patient_id:$("#h_no").text(),
							opd_id:$('#opd_id').text(),
							ipd_id:$('#ipd_id').text(),
							batch_no:$('#batch_no').text(),
							note:$('#note').val(),
							user:$('#user').text(),
						},
						function(data,status)
						{
							bootbox.alert(data);
						})
					}else
					{
						bootbox.alert("Note cannot blank");
					}
					
				  }
				}
			  }
			});
		})
	}
	
	function sample_accept(pid,opd,ipd,batch_no)
	{
		bootbox.dialog({ message: "<p id='phlb_msg'><b>Saving...</b></p>"});
		
		var glob_barcode=$("#glob_barcode").val();
		
		var vac="";
		var vac_l=$(".icon-check");
		for(var i=0;i<vac_l.length;i++)
		{
			if($(vac_l[i]).prop("id"))
			{
				vac+="@@"+$(vac_l[i]).prop("id");
			}
		}
		
		var vac_n="";
		var vac_l_n=$(".icon-check-empty");
		for(var j=0;j<vac_l_n.length;j++)
		{
			if($(vac_l_n[j]).prop("id"))
			{
				vac_n+="@@"+$(vac_l_n[j]).prop("id");
			}
		}
		
		var tst_vac="";
		var tst_vac_n=$(".tst_vac:checked");
		for(var j=0;j<tst_vac_n.length;j++)
		{
			if($(tst_vac_n[j]).val())
			{
				if(j==0)
				{
					tst_vac=$(tst_vac_n[j]).val();
				}
				else
				{
					tst_vac+=","+$(tst_vac_n[j]).val();
				}
			}
		}
		
		$.post("pages/lab_save_sample.php",
		{
			pid:pid,
			opd_id:opd,
			ipd_id:ipd,
			batch_no:batch_no,
			vac:vac,
			vac_n:vac_n,
			tst_vac:tst_vac,
			user:$("#user").text()
		},
		function(data,status)
		{
			$("#phlb_msg").html("<b>Saved</b>");
			setTimeout(function()
			{
				view_all();
				bootbox.hideAll();
			},1000);
		})	
	}
	
	function check_vac(elem,val)
	{
		if($("#"+val+"").prop("class")=="icon-check")
		{
			$("#"+val+"").prop("class","icon-check-empty")
			$("#smp_td_"+elem+"").css({'background-color':'rgb(234, 164, 130)'});
			
			$(".test_vac_cls"+val).prop("checked", false);
		}
		else
		{
			$("#"+val+"").prop("class","icon-check")
			$("#smp_td_"+elem+"").css({'background-color':'rgb(146, 217, 146)'});
			
			$(".test_vac_cls"+val).prop("checked", true);
		}
	}
	function barcode_single(pid,opd,ipd,batch_no,vc)
	{
		
		var tst_vac="";
		var tst_vac_n=$(".tst_vac:checked");
		for(var j=0;j<tst_vac_n.length;j++)
		{
			if($(tst_vac_n[j]).val())
			{
				if(j==0)
				{
					tst_vac=$(tst_vac_n[j]).val();
				}
				else
				{
					tst_vac+=","+$(tst_vac_n[j]).val();
				}
			}
		}
		
		var user=$("#user").text();
		var url="pages/barcode_generate.php?pid="+pid+"&opd_id="+opd+"&ipd_id="+ipd+"&batch_no="+batch_no+"&user="+user+"&vac="+vc+"&tst_vac="+tst_vac+"&sing="+1;
		window.open(url,'','fullscreen=yes,scrollbars=yes');
	}
	
	function select_all()
	{
		if($("#sel_all").val()=="Select All")
		{
			$(".icon-check-empty").prop("class","icon-check");	
			$("#sel_all").val("De-Select All");
			$("#sel_all").html("<i class='icon-list-ul'></i> De-Select All");
			$(".icon-check").parent().css({'background-color':'rgb(146, 217, 146)'});
			
			$(".tst_vac").prop("checked", true);
		}
		else if($("#sel_all").val()=="De-Select All")
		{
			$(".icon-check:not('[name=vacc_done]')").prop("class","icon-check-empty");
			$("#sel_all").val("Select All");
			$("#sel_all").html("<i class='icon-list-ul'></i> Select All");
			$(".icon-check-empty").parent().css({'background-color':'rgb(234, 164, 130)'});
			
			$(".tst_vac").prop("checked", false);
		}
	}
	
	function vac_note(pid,opd,ipd,batch_no,vac,vname)
	{
		bootbox.dialog({
			title: 'Add Note For '+vname,
			message: "<p><input type='text' id='note_text_"+vac+"' style='width:90%'/></p>",
			size: 'large',
			buttons: {
				save: {
					label: "<i class='icon-save'></i> Save",
					className: 'btn-success',
					callback: function(){
						var note=$("#note_text_"+vac+"").val();
						$.post("pages/phlebo_sample_note.php",
						{
							pid:pid,
							opd_id:opd,
							ipd_id:ipd,
							batch_no:batch_no,
							vac:vac,
							note:note,
							user:$("#user").text()
						},
						function(data,status)
						{
							if(data==1)
							{
								$("#vac_saved_note_"+vac+"").val(note);
								$("#note_"+vac+"").prop("class","btn btn-success");
								$("#note_"+vac+"").val("view");
							}
							else
							{
								$("#note_"+vac+"").prop("class","btn btn-info");
								$("#note_"+vac+"").val("note");
								$("#vac_saved_note_"+vac+"").val("");
							}
						})
					}
				},
				close: {
					label: "<i class='icon-off'></i> Close",
					className: 'btn-danger',
					callback: function(){
						console.log('Custom button clicked');
						
					}
				}
			}
		});
		
		var nt=$("#vac_saved_note_"+vac+"").val();
		$("#note_text_"+vac+"").val(nt).focus();
		
		setTimeout(function(){ $("#note_text_"+vac+"").focus();},500);
		
	}
	
	
	function save_des(pid,opd,ipd,batch_no)
	{ 
		$.post("pages/lab_load_sample_data.php",
         {
              pid:pid,
              opd_id:opd,
              ipd:ipd,
              batch_no:batch_no,
              medication:$("#medication").val(),
              disease_id:$("#disease_id").val(),
              user:$("#user").text()
          },
         function(data,status)
         {
			 alert(data);
			 
          			//$("#results").html(data);
          			
          			

         });

	
	}
	
	
	/*
	function sample_accept(pid,opd,ipd,batch_no)
	{
		var all="";
		var samp=$(".samp");
		for(var i=0;i<samp.length;i++)
		{
			if($(samp[i]).is(":checked"))
			{
				all=all+"#"+samp[i].id+"$";
				var tst=$("."+samp[i].id);
				
				for(var j=0;j<tst.length;j++)
				{
					if(tst[j].checked)
					{		
						all=all+"@"+tst[j].value;
					}
					
				}
			}		
		}
		$.post("pages/lab_load_sample_data.php",
		{
			pid:pid,
			opd_id:opd,
			ipd_id:ipd,
			batch_no:batch_no,
			medication:medication,
			disease_id:disease_id,
			user:$("#user").text()
		},
		function(data,status)
		{
			bootbox.dialog({ message: "Saved"});
			setTimeout(function(){
				bootbox.hideAll();
				var stype=$("#search_type").val();			
				if(stype=="date")
				{
					view_all('date');
				}else if(stype=="name")
				{
					view_all('name');
				}else if(stype=="ids")
				{
					view_all('ids');
				}
				$("#mod").click();
				
				var samp_b=$(".samp:checked");
         		if(samp_b.length>0)
         		{
					var sid="";
					
					for(var j=0;j<samp_b.length;j++)
					{
						sid=sid+"@"+samp_b[j].id;
					}
					var user=$("#user").text();
					var url="pages/barcode_generate.php?pid="+pid+"&opd_id="+opd+"&ipd_id="+ipd+"&batch_no="+batch_no+"&user="+user+"&sid="+sid;
					window.open(url,'','fullscreen=yes,scrollbars=yes');
					
				}
				
			},1000);
		})
	}
	*/
	/*
	function select_all()
	{
		if($("#sel_all").val()=="Select All")
		{
			$(".samp:checkbox").each(function(i)
			{
				if($(this).prop("checked")==false)
				{
					$(this).prop("checked",true);
					$(this).click();
					$(this).prop("checked",true);
					
				}
				//load_vaccu();
			});
			$("#sel_all").val("De-select All");
		}
		else if($("#sel_all").val()=="De-select All")
		{
			$(".samp:checkbox").each(function(i)
			{
				if($(this).prop("checked")==true)
				{
					$(this).prop("checked",false);
					$(this).click();
					$(this).prop("checked",false);
					
				}
				
			});
			$("#sel_all").val("Select All");
			
		}
		setTimeout(function(){load_vaccu();},100);
	}
	*/
	
	function print_trf(uhid,opd_id,ipd_id,batch_no)
	{
		var tst_vac="";
		var tst_vac_n=$(".tst_vac:checked");
		for(var j=0;j<tst_vac_n.length;j++)
		{
			if($(tst_vac_n[j]).val())
			{
				if(j==0)
				{
					tst_vac=$(tst_vac_n[j]).val();
				}
				else
				{
					tst_vac+=","+$(tst_vac_n[j]).val();
				}
			}
		}
		
		url="pages/phlebo_gen_req.php?patient_id="+uhid+"&opd_id="+opd_id+"&ipd_id="+ipd_id+"&batch_no="+batch_no+"&dep_id=0&tst_vac="+tst_vac;
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function checked_test(elem,vac,testid)
	{
		var vaccu_test_num=$(".test_vac_cls"+vac+"").length;
		var vaccu_test_chk_num=$(".test_vac_cls"+vac+":checked").length;
		
		if(vaccu_test_chk_num>0)
		{
			$("#"+vac+"").prop("class","icon-check")
			$("#smp_td_"+elem+"").css({'background-color':'rgb(146, 217, 146)'});
		}
		else
		{
			$("#"+vac+"").prop("class","icon-check-empty")
			$("#smp_td_"+elem+"").css({'background-color':'rgb(234, 164, 130)'});
		}
	}
</script>
<style>
#myModal
{
	left: 23%;
	width:95%;
	height: 600px;
}

.ScrollStyle
{
    max-height: 550px;
    overflow-y: scroll;
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
.red
{
	background-color: #d59a9a;
}
.green
{
	background-color:#9dcf8a;
}
.yellow
{
	background-color:#f6e8a8;
}
input[type="checkbox"]:not(old) + label, input[type="radio"]:not(old) + label
{
	display: inline-block;
	margin-left:0;
	line-height: 1.5em;
}
.modal.fade.in {
    top: 1%;
}
.modal-body
{
	max-height: 550px;
}

.tests_phlebo
{ display:inline-block; border-bottom:1px solid #CCC;width:100%;}

table td .icon-check,table td .icon-check-empty{ display:block !important;transform:scale(1.5) !important;margin-top:10px  !important;}

#samp_det_table td{ cursor:pointer;}

</style>
