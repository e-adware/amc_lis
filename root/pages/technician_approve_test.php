<?php
include("../../includes/connection.php");
include("pathology_normal_range_new.php");
print_r($_GET);
$uhid=$_GET['uhid'];
$opd_id=$_GET['opd_id'];
$ipd_id=$_GET['ipd_id'];
$batch=$_GET['batch'];


$dep=$_GET['dep'];
$user=$_GET['user'];
$fdoc=$_GET['fdoc'];

if($opd_id!='')
{
	$pin=$opd_id;
}
else
{
	$pin=$ipd_id;
}


$d_name=mysqli_fetch_array(mysqli_query($link,"select name from test_department where id='$dep'"));
$doc=mysqli_fetch_array(mysqli_query($link,"select * from lab_doctor where id='$fdoc'"));
?>


<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link href="../../css/bootstrap.min.css" rel="stylesheet" type="text/css" media="all">
	
	<link href="../../css/loader.css" rel="stylesheet" type="text/css">
	<link href="../../plugins/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
	<link href="../../dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
	<link href="../../dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
	<link href="../../css/font-awesome.css" rel="stylesheet" type="text/css">
	<link href="../../css/ionicons.min.css" rel="stylesheet" type="text/css" />
	<link href="../../css/custom.css" rel="stylesheet" type="text/css" />
	<link href="../../css/animate.css" rel="stylesheet" type="text/css" />
	
	<link rel="shortcut icon" href="../ico/favicon.ico">
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
	
	<script src="../../js/jquery.uniform.js"></script>
	<link rel="stylesheet" href="../../css/uniform.css" type="text/css" />
	
	<script src="../../js/matrix.form_common.js"></script>
	
	<script>
			$(document).ajaxStart(function()
			{
				$("#loader").show();
			});
			
			$(document).ajaxStop(function()
			{
				$("#loader").hide();
				
			});
			
			function load_normal(uhid,param,val,no)
			{
				$("#loader").show();
				$.post("pathology_normal_range_new.php",
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
					$("#res_chk"+no).css({'font-weight':'bold','color':'red'});
				}
				})
			}
		
			function body_events(e)
			{
				
				var unicode=e.keyCode? e.keyCode : e.charCode;
				if(unicode==13)
				{
					var act_name=$("#"+document.activeElement.id+"").attr("name");
					if(act_name=="multi_27")
					{
						//------ DISABLE 'ENTER ON APPROVE' IN MULTILINE TEXT FOR NEW LINE------//
					}
					else
					{
					if($("#mod_chk").val()==0)
					{
						if(confirm("Do you want to approve all the result?"))
						{
						
						var len=$(".aprv_param:checkbox:not(:checked)");
						for(var i=0;i<len.length;i++)
						{
								if($(len[i]).length>0)
								{
									var res_val=$(len[i]).attr("name");
									
									if($("#res_chk_combo_"+res_val+"").length>0)
									{
										if($(len[i]).prop("checked")==false && $("#res_chk_combo_"+res_val+"").val().trim()!='')
										{
											$(len[i]).prop("checked",true);
											$(len[i]).click();
											$(len[i]).prop("checked",true);
										}
									}
									else
									{
										if($(len[i]).prop("checked")==false && $("#res_chk"+res_val+"").text().trim()!='')
										{
											$(len[i]).prop("checked",true);
											$(len[i]).click();
											$(len[i]).prop("checked",true);
										}
									}
								}
							
						}
												
						var len_cult=$(".aprv_param_cult:checkbox:not(:checked)");
						for(var j=0;j<len_cult.length;j++)
						{
							$(len_cult[j]).prop("checked",true);
							$(len_cult[j]).click();
							$(len_cult[j]).prop("checked",true);
						}
						
						var len_pad=$(".aprv_param_pad:checkbox:not(:checked)");
						for(var k=0;k<len_pad.length;k++)
						{
							$(len_pad[k]).prop("checked",true);
							$(len_pad[k]).click();
							$(len_pad[k]).prop("checked",true);
						}
						
						var len_wid=$(".aprv_param_wid:checkbox:not(:checked)");
						for(var l=0;l<len_wid.length;l++)
						{
							$(len_wid[l]).prop("checked",true);
							$(len_wid[l]).click();
							$(len_wid[l]).prop("checked",true);
						}
						
						window.opener.load_pat_ser();
						
						$(document).ajaxStop(function()
						{
							
							var pid=$("#uhid").val();
							var opd_id=$("#opd_id").val();
							var ipd_id=$("#ipd_id").val();
							var batch_no=$("#batch_no").val();
							var dep=$("#n_dep").val();
							var user=$("#user").val();
							var fdoc=$("#fdoc").val();
							var check=1;
							document.location="technician_approve_test.php?uhid="+pid+"&opd_id="+opd_id+"&ipd_id="+ipd_id+"&batch="+batch_no+"&dep="+dep+"&user="+user+"&fdoc="+fdoc+"&check="+check;
						})
						}
				}
				}
				}
				else if(unicode==27)
				{
					
					if($("#mod_chk").val()==1)
					{
						$("#mod_chk").val("0");
						$("#mod").click();
					}
					else
					{
						window.close();
					}
				}
				else
				{
					if(e.ctrlKey==1)
					{
						
						if(unicode==112 || unicode==80)
						{
							e.preventDefault();
							var tst="";
							var tst_all=$(".print_tst:checked");
							
							if(tst_all.length>0)
							{
								var uhid=$("#uhid").val();
								var opd_id=$("#opd_id").val();
								var ipd_id=$("#ipd_id").val();
								var batch=$("#batch_no").val();
								
								var user=$("#user").val();
								
								for(var i=0;i<tst_all.length;i++)
								{
									tst=tst+"@"+$(tst_all[i]).val();
								}
								
								var dep=$("#n_dep").val();
								
								var url="report_print_path_group.php?uhid="+uhid+"&opd_id="+opd_id+"&ipd_id="+ipd_id+"&batch="+batch+"&tests="+tst+"&hlt="+tst+"&user="+user+"&dep="+dep;
								var win=window.open(url,'','fullScreen=yes,scrollbars=yes,menubar=yes');
							}
							else
							{
								alert("NO TEST IS SELECTED TO PRINT");
							}
							
						}
						else if(unicode==65 || unicode==97)
						{
							e.preventDefault();
							$(".print_tst").prop("checked",true);
							
						}
					}
				}
			}

			function approve_param(val)
			{
				var aprv=0;
				if($("#aprv"+val+"").prop("checked"))
				{
					aprv=1;
				}
					
					var res=$("#res_chk"+val+"").text().trim();
					
					if($("#res_chk"+val+"").prop("name")=="multi_27")
					{
						res=$("#res_chk"+val+"").html().trim();
					}
					
					if(!res)
					{
						if($("#res_chk_combo_"+val+"").length>0)
						{
							res=$("#res_chk_combo_"+val+"").val().trim();
						}
					}
					if(res)
					{
						$.post("technician_approve_aprv.php",
						{
							uhid:$("#uhid").val(),
							opd_id:$("#opd_id").val(),
							ipd_id:$("#ipd_id").val(),
							batch_no:$("#batch_no").val(),
							test:$("#test_"+val+"").val(),
							param:$("#param_"+val+"").val(),
							chk_user:1,
							res:res,
							user:$("#user").val(),
							rep_doc:$("#rep_doc").val(),
							aprv:aprv,
							type:1
						},
						function(data,status)
						{
							//alert(data);
						})
					}
					else
					{
						$("#aprv"+val+"").attr("checked",false);
					}
					
			}
			
			function approve_culture(val)
			{
				var aprv=0;
				if($("#aprv_"+val+"").is(":checked"))
				{
					aprv=1;
				}
				
				$.post("technician_approve_aprv.php",
				{
					uhid:$("#uhid").val(),
					opd_id:$("#opd_id").val(),
					ipd_id:$("#ipd_id").val(),
					batch_no:$("#batch_no").val(),
					test:$("#test_"+val+"").val(),
					user:$("#user").val(),
					fdoc:$("#fdoc").val(),
					aprv:aprv,
					type:2
				},
				function(data,status)
				{
					//alert(data);
				})
			}
			
			function approve_pad(val)
			{
				var aprv=0;
				if($("#aprv_"+val+"").is(":checked"))
				{
					aprv=1;
				}
				
				$.post("technician_approve_aprv.php",
				{
					uhid:$("#uhid").val(),
					opd_id:$("#opd_id").val(),
					ipd_id:$("#ipd_id").val(),
					batch_no:$("#batch_no").val(),
					test:$("#test_"+val+"").val(),
					user:$("#user").val(),
					aprv:aprv,
					type:2
				},
				function(data,status)
				{
					//alert(data);
				})
			}
			
			function approve_wid(val)
			{
				var aprv=0;
				if($("#aprv_"+val+"").is(":checked"))
				{
					aprv=1;
				}
				$.post("technician_approve_aprv.php",
				{
					uhid:$("#uhid").val(),
					opd_id:$("#opd_id").val(),
					ipd_id:$("#ipd_id").val(),
					batch_no:$("#batch_no").val(),
					//test:$("#test_"+val+"").val(),
					user:$("#user").val(),
					fdoc:$("#fdoc").val(),
					aprv:aprv,
					type:3
				},
				function(data,status)
				{
					//alert(data);
				})
			}
			/*
			function approve_pad(val)
			{
				var aprv=0;
				if($("#aprv"+val+"").is(":checked"))
				{
					aprv=1;
				}
				$.post("technician_approve_aprv.php",
				{
					uhid:$("#uhid").val(),
					opd_id:$("#opd_id").val(),
					ipd_id:$("#ipd_id").val(),
					batch_no:$("#batch_no").val(),
					test:$("#test_"+val+"").val(),
					user:$("#user").val(),
					fdoc:$("#fdoc").val(),
					aprv:aprv,
					type:4
				},
				function(data,status)
				{
					
				})
				
			}
			*/
			function check_form(id,form,dec)
			{
				
				var sqr_chk=0;
				var form=form.split("@");
				var fr="";
				for(var i=0;i<form.length;i++)
				{
					var chk=form[i].split("p");
					if(chk[1]>0)
					{			
						if($("."+chk[1].trim()).length>0)
						{
							fr+=$("."+chk[1].trim()).text();
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
				
				if(dec==0)
				{
					var res=eval(fr);
				}
				else
				{
					var res=eval(fr);
					
				}
				
				res=res.toFixed(2);
						
				$("."+id+"").text(res);
			}
			
						
			function select_all_print()
			{
				
				if($("#print_all").is(":checked"))
				{
					//$(".print_tst:checkbox:not(:checked)").click();
					$(".print_tst").prop("checked",true);
				}
				else
				{
					$(".print_tst").prop("checked",false);
				}
			}
			function print_approved()
			{
				var tst="";
				var tst_all=$(".print_tst");
				
				if(tst_all.length>0)
				{
					var uhid=$("#uhid").val();
					var opd_id=$("#opd_id").val();
					var ipd_id=$("#ipd_id").val();
					var batch=$("#batch_no").val();
					
					var user=$("#user").val();
					
					for(var i=0;i<tst_all.length;i++)
					{
						tst=tst+"@"+$(tst_all[i]).val();
					}
					
					var dep=$("#n_dep").val();
					
					var url="report_print_path_group.php?uhid="+uhid+"&opd_id="+opd_id+"&ipd_id="+ipd_id+"&batch="+batch+"&tests="+tst+"&hlt="+tst+"&user="+user+"&dep="+dep;
					var win=window.open(url,'','fullScreen=yes,scrollbars=yes,menubar=yes');
					
					window.close();
				}
			}
			
			function load_note_sample(tid,btype)
			{
				$("#mod_chk").val("1");
				$.post("technician_approve_mis.php",
				{
					pid:$("#uhid").val(),
					opd:$("#opd_id").val(),
					ipd:$("#ipd_id").val(),
					batch:$("#batch_no").val(),
					tid:tid,
					btype:btype,
					type:1
				},
				function(data,status)
				{
					$("#results").html(data);
					$("#mod").click();
				})
			}
			function save_note_sample(tid,btype)
			{
				
				$.post("technician_approve_mis.php",
				{
					pid:$("#uhid").val(),
					opd:$("#opd_id").val(),
					ipd:$("#ipd_id").val(),
					batch:$("#batch_no").val(),
					tid:tid,
					tst_note:$("#tst_note").val(),
					tst_stat:$("#tst_sample_stat").val(),
					rep_dis:$("#rep_dis").val(),
					btype:btype,
					user:$("#user").val(),
					type:2
				},
				function(data,status)
				{
					alert(data);
					if(data.trim()=="Saved")
					{
						if(btype==1)
						{
							$("#note_"+tid+"").attr("class","btn btn-success btn-mini");
							$("#note_"+tid+"").text("View Note");
						}
						else if(btype==2)
						{
							$("#samp_stat_"+tid+"").attr("class","btn btn-success btn-mini");
							$("#samp_stat_"+tid+"").text("View Sample Status");
						}
					}
				})
			}
			
			function repeat_param(val)
			{
				$.post("technician_approve_aprv.php",
				{
					uhid:$("#uhid").val(),
					opd_id:$("#opd_id").val(),
					ipd_id:$("#ipd_id").val(),
					batch_no:$("#batch_no").val(),
					test:$("#test_"+val+"").val(),
					param:$("#param_"+val+"").val(),
					user:$("#user").val(),
					type:5
				},
				function(data,status)
				{
					location.reload();
				})
			}
			
			function view_repeat_param(val,sl)
			{
				$("#repeat_result").empty();
				$.post("technician_approve_aprv.php",
				{
					uhid:$("#uhid").val(),
					opd_id:$("#opd_id").val(),
					ipd_id:$("#ipd_id").val(),
					batch_no:$("#batch_no").val(),
					test:$("#test_"+val+"").val(),
					param:$("#param_"+val+"").val(),
					type:6
				},
				function(data,status)
				{
					if(sl==1)
					{
						$("#repeat_result").html(data);
						
						var cord=$("#rep_view"+val+"").offset();
						
						var left  = cord.left-650;
						var top  = cord.top-100;

						var div = document.getElementById("repeat_result");

						div.style.left = left + "px";
						div.style.top = top+ "px";
						$("#repeat_result").fadeIn(200);
					}
					else
					{
						$("#repeat_result").fadeOut(200);
					}
				})
				
			}
			//~ function view_repeat_param(e,val)
			//~ {
				
				
				//~ //return false;
				
			//~ }
			
			function load_pat_det()
			{
				$("#mod_chk").val("1");
				$.post("technician_approve_mis.php",
				{
					pid:$("#uhid").val(),
					opd:$("#opd_id").val(),
					ipd:$("#ipd_id").val(),
					batch:$("#batch_no").val(),
					type:3
				},
				function(data,status)
				{
					$("#results").html(data);
					$("#mod").click();
				})
			}
			function update_info()
			{
				$.post("technician_approve_mis.php",
				{
					pid:$("#uhid").val(),
					opd:$("#opd_id").val(),
					ipd:$("#ipd_id").val(),
					batch:$("#batch_no").val(),
					name:$("#name").val(),
					age:$("#age").val(),
					age_type:$("#age_type").val(),
					sex:$("#sex").val(),
					type:4
				},
				function(data,status)
				{
					if(data!='')
					{
						$("#pat_info").text(data);
						$('#mod').click();
						$("#mod_chk").val("0");
					}
				})
			}
			function change_up()
			{
				var name=$("#name").val();
				var n_name=name.toUpperCase().replace(/\./g, '').replace(/\,/g, '')
				$("#name").val(n_name);
			}
			
			function load_vaccu_note(vac)
			{
				$.post("technician_approve_mis.php",
				{
					pid:$("#uhid").val(),
					opd:$("#opd_id").val(),
					ipd:$("#ipd_id").val(),
					batch:$("#batch_no").val(),
					vac:vac,
					type:5
				},
				function(data,status)
				{
					$("#results").html(data);
					$("#mod").click();
					$('#mod_chk').val('1');
				})
			}
			function vac_wise_save(vac)
			{
				
				$.post("technician_approve_mis.php",
				{
					pid:$("#uhid").val(),
					opd:$("#opd_id").val(),
					ipd:$("#ipd_id").val(),
					batch:$("#batch_no").val(),
					vac:vac,
					stat:$("#vac_sample_stat").val(),
					note:$("#vac_note").val(),
					dis_res:$("#dis_res").val(),
					user:$("#user").val(),
					type:6
				},
				function(data,status)
				{
					if(data>0)
					{
						$("#vac_"+vac+"").attr("class","btn btn-success btn-mini");
						$('#mod_chk').val('0');$('#mod').click();
					}
					else
					{
						$("#vac_"+vac+"").attr("class","btn btn-info btn-mini");
						$('#mod_chk').val('0');$('#mod').click();
					}
				})
			}
			
			function save_disease(val)
			{
				$.post("technician_approve_mis.php",
				{
					pid:$("#uhid").val(),
					opd:$("#opd_id").val(),
					ipd:$("#ipd_id").val(),
					batch:$("#batch_no").val(),
					val:val,
					user:$("#user").val(),
					type:7
				},
				function(data,status)
				{
					
				})				
			}
			
			function flag_pat()
			{
				$.post("technician_approve_mis.php",
				{
					pid:$("#uhid").val(),
					opd:$("#opd_id").val(),
					ipd:$("#ipd_id").val(),
					batch:$("#batch_no").val(),
					dep:$("#n_dep").val(),
					type:8
				},
				function(data,status)
				{
					$("#results").html(data);
					$("#mod").click();
					$('#mod_chk').val('1');
				})				
			}
			
			function flag_save(val)
			{
				$.post("technician_approve_mis.php",
				{
					pid:$("#uhid").val(),
					opd:$("#opd_id").val(),
					ipd:$("#ipd_id").val(),
					batch:$("#batch_no").val(),
					flag_cause:$("#flag_cause").val(),
					flag_note:$("#flag_note").val(),
					user:$("#user").val(),
					dep:$("#n_dep").val(),
					val:val,
					type:9
				},
				function(data,status)
				{
					if(val==1)
					{
						$("#flag").attr("class","btn btn-danger");
						$("#flag").text("Patient Flagged");
						$('#mod_chk').val('0');$('#mod').click();
					}
					else
					{
						$("#flag").attr("class","btn btn-info");
						$("#flag").text("Flag This Patient");
						$('#mod_chk').val('0');$('#mod').click();
					}
					window.opener.load_search();

				})
			}
			
			function load_date_det(opd,uhid)
			{
				
				var pid=uhid;
				var opd_id=opd;
				var ipd_id=$("#ipd_id").val();
				var batch_no=$("#batch_no").val();
				var dep=$("#n_dep").val();
				var user=$("#user").val();
				var fdoc=$("#fdoc").val();
				var check=1;
				document.location="technician_approve_test.php?uhid="+pid+"&opd_id="+opd_id+"&dep="+dep+"&user="+user+"&fdoc="+fdoc;
				
			}
			function load_hosp_chk(hosp)
			{
				window.opener.load_hosp_det(hosp);
				window.close();
			}
			
			function multiline_txt(e,val)
			{
				if(e.which==13)
				{
					
				}
			}
			
			function load_instr()
			{
				$.post("technician_approve_mis.php",
				{
					pid:$("#uhid").val(),
					opd:$("#opd_id").val(),
					ipd:$("#ipd_id").val(),
					batch:$("#batch_no").val(),
					dep:$("#n_dep").val(),
					type:10
				},
				function(data,status)
				{
					$("#instr_name").html(data);
				})
			}
			
			function load_repeat_all_tst(tst)
			{
				if(confirm("Do you want to repeat all?"))
				{
					$.post("technician_approve_aprv.php",
					{
						uhid:$("#uhid").val(),
						opd_id:$("#opd_id").val(),
						ipd_id:$("#ipd_id").val(),
						batch_no:$("#batch_no").val(),
						test:tst,
						user:$("#user").val(),
						type:7
					},
					function(data,status)
					{
						location.reload();
					})
				}
			}
			function load_pbs_option(val)
			{
				var desc=$("#pbs_option_list").val();
				$("#res_chk"+val+" span").html(desc);
			}
			
			function load_hem_image()
			{
				$.post("technician_approve_mis.php",
				{
					uhid:$("#uhid").val(),
					opd_id:$("#opd_id").val(),
					ipd_id:$("#ipd_id").val(),
					batch:$("#batch_no").val(),
					dep:$("#n_dep").val(),
					type:11
				},
				function(data,status)
				{
					if(data.length>1)
					{
						$("#check_hem_image").html("<button class='btn btn-info' onclick='view_image()'><i class='icon-camera'></i> Graph</button>");
						$("#hem_image").html(data);
					}
				})
				
			}
			function view_image()
			{
				$("#hem_image").css({'top':'200px','right':'10px','border':'2px solid #CCC','border-radius':'5%'});
				$("#hem_image").fadeIn(300);
				
			}
			function image_close()
			{
				$("#hem_image").fadeOut(300);
			}
			
			function res_edit_check(val)
			{
				if($("#aprv"+val+"").prop("checked")==true)
				{
					$("#res_chk"+val+"").prop("contenteditable",false);
				}
				else if($("#aprv"+val+"").prop("checked")==false)
				{
					$("#res_chk"+val+"").prop("contenteditable",true);
				}
			}
			
			function load_sum_edit(tst)
			{
				$("#pad_"+tst+"").hide();
				$("#pad_edit_"+tst+"").show();
				if (CKEDITOR.instances["article-body-"+tst+""]) 
				{
					CKEDITOR.instances["article-body-"+tst+""].destroy(true);
				}
				CKEDITOR.replace("article-body-"+tst+"");
				CKEDITOR.config.enterMode=CKEDITOR.ENTER_BR;
				CKEDITOR.config.height = 300;
			}
			function load_sum_edit_hide(tst)
			{
				$("#pad_"+tst+"").show();
				$("#pad_edit_"+tst+"").hide();
			}
			function save_summary(tst)
			{
				$.post("technician_approve_aprv.php",
				{
					uhid:$("#uhid").val(),
					opd_id:$("#opd_id").val(),
					ipd_id:$("#ipd_id").val(),
					batch_no:$("#batch_no").val(),
					test:tst,
					result:$("#pad_edit_"+tst+" #rad_res").contents().find('body').html(),
					user:$("#user").val(),
					type:8
				},
				function(data,status)
				{
					alert("Saved");
					$("#pad_"+tst+"").html($("#pad_edit_"+tst+" #rad_res").contents().find('body').html()).show();
					$("#pad_edit_"+tst+"").hide();
					$("#summary_"+tst+"").val($("#pad_edit_"+tst+" #rad_res").contents().find('body').html());
				})
			}
	</script>
	<script type="text/javascript" src="../../ckeditor/ckeditor.js"></script>
	<style>
		input[type='text']{ height:30px !important;}
		#t_bold td{background-color:#ccc}
		.table td{ //height:40px;}
		.param_extra{ font-style:italic;font-weight:bold}
	</style>
</head>
<body onkeyup="body_events(event)">
	<div class="container-fluid">
	<?php
	$user_name=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$user'"));
	?>
	<div id="content-header">
		<div class="header_div"  style="border: 1px solid #F6F6F6;background-color: #F6F6F6;">
			<h4 style="display:inline;"> Technician Approval (<?php echo $user_name[name];?>) </h4>
		</div> <!--Tech Approve-->
	</div>
	<?php
		$patient_det=mysqli_fetch_array(mysqli_query($link,"select * from patient_info where patient_id='$uhid'"));
		$reg=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where patient_id='$uhid' and opd_id='$pin'"));
		$d_nm=mysqli_fetch_array(mysqli_query($link,"select ref_name from refbydoctor_master where refbydoctorid='$serial_no[refbydoctorid]'"));
		$reg_date=$serial_no[date];
	?>
	
	<select id="rep_doc" style="display:none" disabled >
		<option value="0">--Select--</option>
		<?php
			$rep_d=mysqli_query($link,"select * from lab_doctor order by name");
			while($rep=mysqli_fetch_array($rep_d))
			{
				if($user==$rep[id]){ $sel="Selected='selected'";}else{ $sel='';}
				echo "<option value='$rep[id]' $sel>$rep[name]</option>'";
			}
		?>
	</select>
	
	
	<table class="table table-bordered header-table">
		<tr id="t_bold">
			<td>BILL ID | Batch No</td><td>UHID NO.</td><td>Name / Age / Sex</td> <td>User</td>
		</tr>
		<tr>
			<td><?php echo $pin." | ".$batch;?></td>
			<td><?php echo $reg[patient_id];?></td>
			<td><?php echo $patient_det[name]." / ".$patient_det[age]." ".$patient_det[age_type]." / ".$patient_det[sex];?></td>
			
			<td>
				<?php
					$emp=mysqli_fetch_array(mysqli_query($link,"select a.name from employee a,uhid_and_opdid b where a.emp_id=b.user and b.patient_id='$uhid' and b.opd_id='$opd_id'"));
					echo $emp[name];
				?>
			</td>
		</tr>
		<!--
		<tr>
			<td colspan="2" style="display:none">
			<b>Add Vaccu Status and Note</b>: 
			<?php
			$vac=mysqli_query($link,"select distinct(a.vac_id),b.type from test_vaccu a,vaccu_master b,patient_test_details c where c.patient_id='$uhid' and c.opd_id='$opd_id' and c.testid=a.testid and a.vac_id=b.id");
			while($vc=mysqli_fetch_array($vac))
			{
				
				$v_class="btn btn-info btn-mini";
				$chk_vac=mysqli_num_rows(mysqli_query($link,"select * from testresults_sample_stat where patient_id='$uhid' and opd_id='$opd_id' and vac_id='$vc[vac_id]'"));
				if($chk_vac>0)
				{
					$v_class="btn btn-success btn-mini";	
				}
				?>
				<button id="vac_<?php echo $vc[vac_id];?>" class="<?php echo $v_class;?>" onclick="load_vaccu_note(<?php echo $vc[vac_id];?>)"> <?php echo $vc[type];?> </button>
				<?php
			}
			?>
			</td>
			<td colspan="2">
			<div id="instr_name"></div>	
			<div style="display:none">
				Select Disease:
				<select id="pat_dis" onchange="save_disease(this.value)">
					<option value="0">None</option>
					<?php
					$pat_dis=mysqli_fetch_array(mysqli_query($link,"select * from patient_disease_details where patient_id='$uhid' and opd_id='$opd_id'"));
					$dis=mysqli_query($link,"select * from disease_master order by name");
					while($ds=mysqli_fetch_array($dis))
					{
						if($pat_dis[disease_id]==$ds[id]) { $sel="Selected='selected'";}else{ $sel="";}
						echo "<option value='$ds[id]' $sel>$ds[name]</option>";
					}
					?>
				</select>
			</div>	
			</td>
		</tr>
		-->
	</table>
	<div id="check_hem_image" align="center">
		
	</div>
	<?php
	/*if($chk_prev>0)
	{
	echo "<div style='font-weight:bold'> VISITS:";
	?>
			<select id="hosp_no_date" onchange="load_date_det(this.value)">
			<?php
				$hosp_det=mysqli_query($link,"select * from uhid_and_opdid where hosp_no='$serial_no[hosp_no]' order by slno desc");
				while($hosp_date=mysqli_fetch_array($hosp_det))
				{
					echo "<option value='$hosp_date[opd_id]@koushik@$hosp_date[patient_id]@koushik@$hosp_date[date]'>".convert_date($hosp_date[date])."</option>";
				}
			?>
			</select>
	<?php
	echo "</div>";
	}*/
	
	if($chk_prev>0)
	{
		echo "<div class='btn-grp' style='border-bottom:1px solid #CCC'>";
		
		$hosp_det=mysqli_query($link,"select * from uhid_and_opdid where hosp_no='$serial_no[hosp_no]' order by slno desc");
		while($hosp_date=mysqli_fetch_array($hosp_det))
		{
			
			if($opd_id==$hosp_date[opd_id])
			{
				echo "<button class='btn btn-default' disabled>".convert_date($hosp_date[date])."</button>";	
			}
			else
			{
				?><button class='btn btn-primary' onclick="load_date_det('<?php echo $hosp_date[opd_id];?>','<?php echo $hosp_date[patient_id];?>')"><?php echo convert_date($hosp_date[date]);?></button><?php
			}
		}
		
		echo "</div><br/>";
	}
	?>
	
		
	</div>
	
	
	<div id="loader" style="position:fixed;display:none;z-index:1000"></div>

	<input type="hidden" id="uhid" value="<?php echo $uhid;?>"/>
	<input type="hidden" id="opd_id" value="<?php echo $opd_id;?>"/>
	<input type="hidden" id="ipd_id" value="<?php echo $ipd_id;?>"/>
	<input type="hidden" id="batch_no" value="<?php echo $batch;?>"/>
	<input type="hidden" id="user" value="<?php echo $user;?>"/>
	<input type="hidden" id="fdoc" value="<?php echo $fdoc;?>"/>
	<input type="hidden" id="n_dep" value="<?php echo $dep;?>"/>

	

	<div id="current">  <!----Current Div Ends---->

<?php

	$uname=mysqli_fetch_array(mysqli_query($link, "select name from employee where emp_id='$user'"));

	$id_pref=mysqli_fetch_array(mysqli_query($link,"select * from id_setup limit 1"));	


	function convert_date($date)
	{
		if($date)
		{
			$timestamp = strtotime($date); 
			$new_date = date('d-M-Y', $timestamp);
			return $new_date;
		}
	}
		
	$all_test="";
	$all_cult="";
	$all_pad="";
	$all_ptst="";
	$pos=0;
	$wid=0;
	$nbl_note=0;

	$j=1;
	
	$tsts=mysqli_query($link,"SELECT DISTINCT a.testid FROM patient_test_details a,testmaster b WHERE a.patient_id='$uhid' AND a.opd_id='$opd_id' and a.ipd_id='$ipd_id' and a.batch_no='$batch'  AND a.testid=b.testid and b.type_id='$dep'");
			
	while($tt=mysqli_fetch_array($tsts))
	{
		$tnm=mysqli_fetch_array(mysqli_query($link,"select testname from testmaster where testid='$tt[testid]'"));
		
		
		if (strpos($tnm['testname'],'culture') !== false) 
		{
			$pos=2;
		}
		elseif (strpos($tnm['testname'],'CULTURE') != false) 
		{
			$pos=2;
		}
		elseif (strpos($tnm['testname'],'Culture') != false) 
		{
			$pos=2;
		}
		else
		{
			$pos=0;
				
			//--------Check Pad----------//
			$chk_pad=mysqli_query($link,"select * from Testparameter where TestId='$tt[testid]'");
			if(mysqli_num_rows($chk_pad)==1)
			{
				$par_det=mysqli_fetch_array(mysqli_query($link,"select ResultType from Parameter_old where ID in(select ParamaterId from Testparameter where TestId='$tt[testid]')"));
				if($par_det[ResultType]==7)
				{
					$pos=4;
				}
			}
			//----------------------------//
			
			if($tt[testid]==1227)	//---Widal---//		 
			{
				$pos=5;
			} 
		}
		
		if($pos==2)
		{
			$all_cult.="@".$tt['testid'];
		}
		else if($pos==3)
		{
			
		}
		else if($pos==4)
		{
			$all_pad.="@".$tt['testid'];
		}
		else if($pos==5)
		{
			$wid=1;
		}
		else
		{	
			$all_test.="@".$tt['testid'];
		}
		
	}
		
	
			
			
			
	//$reg=mysqli_fetch_array(mysqli_query($link, "select * from patient_reg_details where patient_id='$uhid' and visit_no='$visit'"));

	$pinfo=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$uhid'"));

	$doc=mysqli_fetch_array(mysqli_query($link, "select refbydoctorid,ref_name from refbydoctor_master where refbydoctorid in(select refbydoctorid from uhid_and_opdid where `patient_id`='$uhid' AND `opd_id`='$pin' )"));

	$dname="Dr. ".$doc['ref_name'];

	$cname=mysqli_fetch_array(mysqli_query($link, "select centrename from centremaster where centreno in( SELECT `center_no` FROM `uhid_and_opdid` WHERE `patient_id`='$uhid' AND `opd_id`='$pin' and centreno!='C100')"));


	if($all_test)
	{
		$tech_n="";
		$test=explode("@",$all_test);
		
		$sam="";
		$sam1=" ";
		$micr=0;
		
		foreach($test as $ttst)
		{
			if($ttst)
			{
				$samp=mysqli_fetch_array(mysqli_query($link, "select Name from Sample where ID in(select SampleId from TestSample where TestId='$ttst' and SampleId!='1')"));
				$sam.=",".$samp['Name'];
			}
		}
		
		
		$sam=explode(",",$sam);
		$samp=array_unique($sam);
		
		foreach($samp as $samp1)
		{
		if($samp1)
		{
		$sam1.=$samp1.",";
		}
		}
		
		$rp_page=0;
		if($_GET['rp_page']>0)
		{
			$rp_page=$_GET['rp_page'];
		}
		$t_page=0;
		
		unset($l_user);
	?>
		
		<input type="hidden" id="chk_page" value="<?php echo $rp_page;?>"/>
		<div class="container-fluid">
			<div class="">
				<div class="">
					
				<div style="" id="test_param">
					<table class="table table-bordered table-condensed test-table">
						<tr id='t_bold'>
							<td width="35%">TEST</td>
							<td width="10%">RESULTS </td>
							<td width="10%">UHID</td>
							<td width="10%"><?php if(!$lab_no['result']){ echo "REF. RANGE";}?></td>
							<!--<td>Entry By</td>-->
							<td width="25%">Approve</td>
							<?php
							if($glob_barcode==1)
							{
							?>
								<td width="8%">Repeat</td>
							<?php
							}
							?>
							<td width="15%"></td>
							
						</tr>
						<?php
							$type="";
							$tech_n="";
							
							if($dep)
							{	
								$l=1;
								$t_p=0;
								foreach($test as $tst)
								{
									
									$num_tst=mysqli_num_rows(mysqli_query($link,"select * from approve_details where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$tst'"));									
									if($tst)
									{
										$usr="";
											
										$auth=mysqli_fetch_array(mysqli_query($link,"select * from approve_details where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$tst'"));
										
										
										$lab_doc=mysqli_fetch_array(mysqli_query($link,"select * from testresults where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$tst' limit 1"));
										$l_doc[]=$lab_doc['doc'];
										
										
										$lis=mysqli_num_rows(mysqli_query($link,"select * from test_sample_result where`patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$tst' and result>0"));
										
										if($lis>0)
										{
											$l_user[]="LIS";
										}
										else
										{
											$l_user[]=$lab_doc['tech'];
										}
										
										$tech=mysqli_fetch_array(mysqli_query($link, "select name from employee where emp_id='$lab_doc[main_tech]'"));
										$tech_n.=$tech['name'].",";
										
										$tname=mysqli_fetch_array(mysqli_query($link, "select * from testmaster where testid='$tst'"));
										$type="@".$tname['type_id'];
										
										$tot_par=mysqli_num_rows(mysqli_query($link,"select * from Testparameter where TestId='$tst' and sequence>0"));
										if($tot_par>1)
										{
											$t_p=0;
										?>
											<tr>
												<td colspan='5' style="padding-bottom:5px">
													<?php
											$nbl_test=mysqli_num_rows(mysqli_query($link, "select * from nabl_logo where testid='$tst' and paramid='0'"));
											 if($nbl_test>0)
											 {
												 $nbl_star="*";
												 $nbl_note_test=1;
											 }					
										?>
													<div class="row">
													<div class="span5">
													<b><?php echo $nbl_star.$tname['testname'];?></b>
													<br/>
													<?php
													$barc=mysqli_fetch_array(mysqli_query($link,"select distinct barcode_id from test_sample_result where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$tst'"));
													if($barc[barcode_id])
													{
														echo "(<i>Barcode ID: $barc[barcode_id])</i>";
													}
													?>
													</div>
												</td>
												<?php 
												if($glob_barcode==1)
												{
												?>	
													<td>
														<button class="btn btn-info btn-mini" id="repeat_test_<?php echo $tst;?>" onclick="load_repeat_all_tst(<?php echo $tst;?>)">Repeat All</button>
													</td>
												<?php } ?>
												<td>
													<div class="span4 text-right">
														<?php
														$note_cls="btn btn-info btn-mini";
														$note_tst="Add Note";
														$note_chk=mysqli_num_rows(mysqli_query($link,"select * from testresults_note where patient_id='$uhid' and opd_id='$opd_id' and testid='$tst'"));
														if($note_chk>0)
														{
															$note_cls="btn btn-success btn-mini";
															$note_tst="View Note";
														}
														
														
														?>
														<button class="<?php echo $note_cls;?>" id="note_<?php echo $tst;?>" onclick="load_note_sample(<?php echo $tst;?>,1)"><?php echo $note_tst;?></button>
														
													</div>
												</td>
												<td style="display:none">
													<input type="checkbox" class="print_tst" value="<?php echo $tst;?>" id="<?php echo $tst;?>_print" onclick="test_print_group(this.value)"/>
												</td>
											</tr>
									<?php
										}
										else
										{
											
											$t_p=1;
											$nbl_star_par="";
									?>
									<tr>
									<?php
										}
											$i=1;
											$param=mysqli_query($link, "select * from Testparameter where TestId='$tst' and sequence>0 order by sequence");
											while($p=mysqli_fetch_array($param))
											{
												$pn=mysqli_fetch_array(mysqli_query($link, "select * from Parameter_old where ID='$p[ParamaterId]'"));
												if($pn[ResultType]!=0)
												{
												$res=mysqli_query($link, "select * from testresults where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$tst' and paramid='$p[ParamaterId]'");
												$num=mysqli_num_rows($res);
												if($dep>0)
												{
													
													$pat_note=mysqli_fetch_array(mysqli_query($link, "select note from testresults_note where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$tst'"));
													if($pat_note['note'])
													{
														$note=$pat_note['note'];
													}
													
													
													if($tname['type_id']==33)
													{
														$micr=1;
													}
													
													$all_d++;
												
												$div_ht="111";
												?>
												
												<?php
												
												
											
											$p_unit=mysqli_fetch_array(mysqli_query($link, "select unit_name from Units where ID='$pn[UnitsID]'"));
											$meth=mysqli_fetch_array(mysqli_query($link, "select name from test_methods where id in(select method_id from parameter_method where param_id='$p[ParamaterId]')"));
											
											$meth_name=mysqli_fetch_array(mysqli_query($link, "select name from test_methods where id='$pn[method]'"));
											
											$t_res=mysqli_fetch_array($res);
											
											if($t_p>0)
											{
												$p_name=mysqli_fetch_array(mysqli_query($link,"select Name from Parameter_old where ID in(select ParamaterId from Testparameter where TestId='$tst' and sequence>0)"));
												?>
												<td><b><?php echo $nbl_star_par.$p_name['Name'];?></b>
												
												<br/>
												<?php
													$barc=mysqli_fetch_array(mysqli_query($link,"select distinct barcode_id from test_sample_result where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$tst'"));
													if($barc[barcode_id])
													{
														echo "(<i>Barcode ID: $barc[barcode_id])</i>";
													}
												?>
													
													
												
												</td>
										
												<?php
											}
											else
											{
												echo "<tr class='tr_test'>";
											}
											?>
										<?php
											$nres=$t_res['result'];
											if($p[ResultType]!=27)
											{
												
												if($t_p>0)
												{
												
												}
												else
												{
													$par_class="";
													if($pn['ResultType']==8)
													{
														$par_class="tname";
													}
													else
													{
														$par_class="";
													}
										?>
										<td class="<?php echo $par_class;?>" valign="top"><?php echo $nbl_star_par.$pn['Name'];?>
																			
										</td>
										<?php
												}
										
											$prm_res="";
											$entry_by="";
											$aprv_by="Approve";
											$chk_user="";
											if($t_res['result']!='')
											{
												$prm_res=nl2br($t_res['result']);
												$emp_name=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$t_res[tech]'"));
												if($t_res['main_tech'])
												{
													$aprv_name=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id='$t_res[main_tech]'"));
													$aprv_by=$aprv_name['name'];
												}
												$entry_by=$emp_name['name'];
												
												
												$chk_lis=mysqli_fetch_array(mysqli_query($link,"select * from test_sample_result where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$tst' and paramid='$p[ParamaterId]'"));
												if($chk_lis[result]!='')
												{
													$entry_by='LIIS';
												}
												
												$chk_user=1;
												
												$range=mysqli_fetch_array(mysqli_query($link,"select normal_range from parameter_normal_check where slno='$t_res[range_id]'"));
												$norm_range=$range[normal_range];
											}
											else
											{
												$prm_res="";
												$lis=mysqli_fetch_array(mysqli_query($link,"select result from test_sample_result where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$tst' and paramid='$p[ParamaterId]'"));
												$prm_res=$lis['result'];
												
												if($p['ParamaterId']==124 || $p['ParamaterId']==616 || $p['ParamaterId']==611 )
												{
													if($prm_res)
													{
														$prm_res=$prm_res*1000;
													}
												}
												else if($p['ParamaterId']==136)
												{
													if($prm_res)
													{
														$prm_res=round($prm_res/100,1);
													}
												}
												if($prm_res)
												{
													$entry_by="LIIS";
													$chk_user=2;
												}
												
												$nr=load_normal($uhid,$p['ParamaterId'],$prm_res,0);
												$nr1=explode("#",$nr);
												$norm_range=$nr1[0];
											}
											
											
										
											if($pn['ResultOptionID']>0)
											{
												$p_unit=mysqli_fetch_array(mysqli_query($link, "select unit_name from Units where ID='$pn[UnitsID]'"));
											?>
												<td valign="top" style="height:40px;"> <?php
												
												echo "<input type='text' id='res_chk_combo_$j' value='$prm_res' list='list$j' style='position:absolute;width:200px;border:1px solid;margin-left:-5px;margin-top:5px;'/>";
												echo "<datalist id='list$j'>";
												$sel=mysqli_query($link, "select * from ResultOptions where id='$pn[ResultOptionID]'");
												while($s=mysqli_fetch_array($sel))
												{
													$op=mysqli_fetch_array(mysqli_query($link, "select name from Options where id='$s[optionid]'"));
													echo "<option value='$op[name]'>".strtoupper($op[name])."</option>";
												}
												echo "</datalist>";
											}
											else
											{
												if($pn["ResultType"]==27)
												{
													?> 
													<td valign="top" id="res_chk<?php echo $j;?>" name="multi_27" style="border:1px solid;height:100px;" contenteditable="true" colspan="3" tabindex='1' onkeydown="multiline_txt(event)"> 
													<span class="<?php echo $p['ParamaterId'];?>">
														<?php 
														if(!$prm_res)
														{
															$fix_res=mysqli_fetch_array(mysqli_query($link,"select * from param_fix_result where paramid='$p[ParamaterId]'"));
															
															$fix_res["result"]=str_replace(array("\\r\\n", "\\r", "\\n"), "<br />", $fix_res["result"]);
															
															echo nl2br($fix_res["result"]);
														}
														else
														{
															$prm_res=str_replace(array("\\r\\n", "\\r", "\\n"), "<br />", $prm_res);
															
															echo nl2br($prm_res);
														}
														?>
													</span>
													<?php
													
												}
												else
												{
													$res_edit="true";
													if($t_res['main_tech'])
													{
														$res_edit="false";
													}
												
													?> <td valign="top" id="res_chk<?php echo $j;?>" style="border:1px solid" contenteditable="<?php echo $res_edit;?>"> <?php
													
													if($nr1[1]=="Error" || $t_res['range_status']==1)
													{
														?> <span style='font-weight:bold;color:red' class="<?php echo $p['ParamaterId'];?>" > <?php
													}
													else
													{
														?> <span class="<?php echo $p['ParamaterId'];?>"> <?php
													}
													echo $prm_res;
													echo "</span>";
												}
											}
											
											?>
										</td>
										<?php
										if($pn[ResultType]!=27)
										{
											if(!$prm_res)
											{
												$chk_form=mysqli_fetch_array(mysqli_query($link,"select * from parameter_formula where ParameterID='$p[ParamaterId]'"));
												if($chk_form['formula'])
												{
													?><script>check_form(<?php echo $p['ParamaterId'];?>,'<?php echo $chk_form['formula'];?>',<?php echo $chk_form['res_dec'];?>)</script><?php
												}
											}
											
											$unit_sty="left";
											if($pn['ResultOptionID']>0)
											{
												$unit_sty="right";
											}
											?>
											<td valign="top" style="text-align:<?php echo $unit_sty;?>"><?php echo $p_unit['unit_name'];?></td>
											<td id="norm_r<?php echo $j;?>">
												<?php echo $norm_range;?>
											</td>
										<?php } ?>
										<td>
												<?php
												$chkbx_chk="";
												if($t_res['main_tech']>0)
												{
													$chkbx_chk="Checked='checked'";
													if($user!==$t_res['main_tech'])
													{
														$chkbx_chk.="disabled";
													}
												}
												
												if($t_res['doc']>0)
												{
													//$chkbx_chk.="disabled";
												}
												$rep_chk="";
												if(!$prm_res)
												{
													$rep_chk="disabled";
												}
												?>
												<input type="hidden" id="test_<?php echo $j;?>" value="<?php echo $tst;?>"/>
												<input type="hidden" id="param_<?php echo $j;?>" value="<?php echo $p['ParamaterId'];?>"/>
												<input type="checkbox"  id="aprv<?php echo $j;?>" name="<?php echo $j;?>" class="aprv_param" onchange="res_edit_check(<?php echo $j;?>)" onclick="approve_param(<?php echo $j;?>)" <?php echo $chkbx_chk;?>/> (<?php echo $aprv_by;?>)
														
											</td>
											<?php
											if($glob_barcode==1)
											{
											?>
											<td>
												<button  id="rep<?php echo $j;?>" class="btn btn-info btn-mini" onclick="repeat_param(<?php echo $j;?>)" <?php echo $rep_chk;?>> Repeat</button>
												<?php
												$chk_rep=mysqli_num_rows(mysqli_query($link,"select * from test_sample_result_repeat where patient_id='$uhid' and opd_id='$opd_id' and testid='$tst' and paramid='$p[ParamaterId]'"));
												if($chk_rep>0)
												{
													?> <button  id="rep_view<?php echo $j;?>" class="btn btn-success btn-mini" onmouseover="view_repeat_param(<?php echo $j;?>,1)" onmouseout="view_repeat_param(<?php echo $j;?>,2)"> View</button> <?php
												}
												
												?>
											</td>
											<?php } ?>
											<td style="text-align:right">
												
														<?php
														$note_cls="btn btn-info btn-mini";
														$note_tst="Add Note";
														$note_chk=mysqli_num_rows(mysqli_query($link,"select * from testresults_note where patient_id='$uhid' and opd_id='$opd_id' and testid='$tst'"));
														if($note_chk>0)
														{
															$note_cls="btn btn-success btn-mini";
															$note_tst="View Note";
														}
														
														/*
														$stat_cls="btn btn-info btn-mini";
														$stat_tst="Add Sample Status";
														$stat_chk=mysqli_num_rows(mysqli_query($link,"select * from testresults_sample_stat where patient_id='$uhid' and opd_id='$opd_id' and paramid='$p[ParamaterId]'"));
														if($stat_chk>0)
														{
															$stat_cls="btn btn-success btn-mini";
															$stat_tst="View Sample Status";
														}
														*/
														
														if($t_p>0)
														{ ?> <button class="<?php echo $note_cls;?>" id="note_<?php echo $tst;?>" onclick="load_note_sample(<?php echo $tst;?>,1)"><?php echo $note_tst;?></button> <?php } ?>
														
														<!--<button class="<?php echo $stat_cls;?>" id="samp_stat_<?php echo $p[ParamaterId];?>" onclick="load_note_sample(<?php echo $p[ParamaterId];?>,2)"><?php echo $stat_tst;?></button>-->
											
											</td>
											<td style="display:none">
												<?php if($tot_par==1)
												{ ?>
												<input type="checkbox" class="print_tst" value="<?php echo $tst;?>" id="<?php echo $tst;?>_print" onclick="test_print_group(this.value)"/>
												<?php } ?>
											</td>
										<?php
											$j++;
											$l++;
											
											}
											else
											{
											?>
										
										
										<?php
											if($meth_name['name'])
											{
											?>
										<td width="12%" colspan="3" valign="top">
											<b><?php echo nl2br($t_res['result']);?></b>
										</td>
										<td>
											<?php echo $meth_name[name];?>
										</td>
										<?php
											}
											else
											{
											?>
										<td colspan="6" valign="top">
											<b><?php echo nl2br($t_res['result']);?></b>
										</td>
										<?php
											}
											$l++;
											}
											
											?>
											
										</tr>
										
										<?php
											}
											}
											else
											{
												echo "<tr class='tr_test'><td colspan='7'><b>$pn[Name]</b></td></tr>";
											}
										
										}
										
									
									//---Summary--//
									$pat_sum=mysqli_query($link, "select * from patient_test_summary where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$tst'");
									$num_pat=mysqli_num_rows($pat_sum);
									
									if($num_pat>0)
									{
										 $pat_s=mysqli_fetch_array($pat_sum);
										 if(strip_tags($pat_s[summary])!='')
										 {
											echo "<tr><td colspan='6'> $pat_s[summary] </td></tr>";
										 }
										 
									}
									else
									{
										 $chk_sum=mysqli_query($link, "select * from test_summary where testid='$tst'");
										 $num_sum=mysqli_num_rows($chk_sum);
										 if($num_sum>0)
										 {
											 $summ_all=mysqli_fetch_array($chk_sum);
											 echo "<tr><td colspan='6'> $summ_all[summary] </td></tr>";
										 }
									 
									}
									
									?>
										<tr>
											<td colspan="6" style="text-align:center;font-size:12px;border-bottom:2px solid">
												<div class="row">
												<?php
												$com_par=mysqli_query($link,"select * from Testparameter where TestId='$tst' and sequence='0' order by ParamaterId");
												while($cp=mysqli_fetch_array($com_par))
												{
													
													$bc=mysqli_fetch_array(mysqli_query($link,"select barcode_id from test_sample_result where patient_id='$uhid' and opd_id='$opd_id' and testid='$tst' and paramid='$cp[ParamaterId]'"));
													$res_p=mysqli_fetch_array(mysqli_query($link,"select * from test_sample_result where patient_id='$uhid' and opd_id='$opd_id' and barcode_id='$bc[barcode_id]'  and paramid='$cp[ParamaterId]'"));
													?> <div class="span4 param_extra"> <?php
													$p_name_n=mysqli_fetch_array(mysqli_query($link,"select Name from Parameter_old where ID='$cp[ParamaterId]'"));	
													echo $p_name_n[Name].": <u>".$res_p[result]."</u>";
													?> </div><?php
												}
												?>
												</div>
											</td>
										</tr>
										<?php
									
									
									
									}
									$j++;
													
								
								}
							}
							
							?>
					</table>
					
					
				</div>
				
				
			</div>
		</div>
	</div>
			
	


<?php
		$usr="";
		$techn="";
		$tech_n="";
	}
	
	
	
	if($all_ptst)
	{
		$al_pt=explode("@",$all_ptst);
		foreach($al_pt as $apt)
		{
			if($apt)
			{
					$j++;
					$samp=mysqli_fetch_array(mysqli_query($link,"select Name from Sample where ID in(select SampleId from TestSample where TestId='$apt')"));
					$tname=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$apt'"));
										?>
						<div class="container-fluid">
								<div class="row">
									<div class="col-md-12">
										
										<div>
											<table class="table borderless table-report">
											<?php
												$test_rs=mysqli_num_rows(mysqli_query($link, "select * from testresults where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$apt'"));
												if($test_rs>0)
												{
													$note="";
													$pat_note=mysqli_fetch_array(mysqli_query($link, "select note from testresults_note where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$apt'"));
													if($pat_note['note'])
													{
														$note=$pat_note['note'];
													}
													
														
												?>
											<tr id='t_bold'>
												<td width="27%">TEST</td>
												<td colspan="3">RESULTS</td>
												<td><?php if(!$lab_no['result']){ echo "REF. RANGE";}?></td>
												<td>METHOD</td>
											</tr>
											<?php
												//$nbl_note=0;
												$nbl_star_par="";
																		
											
												$tot_par=mysqli_num_rows(mysqli_query($link,"select * from Testparameter where TestId='$apt' and sequence>0"));
												if($tot_par>1)
												{
												?>
											<tr>
												<?php
													$aprv_by="Approve";
													$chked_pd="";
													$chk_pd_query=mysqli_query($link,"select * from testresults where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$apt' and main_tech>0");
													$chk_pd=mysqli_num_rows($chk_pd_query);
													if($chk_pd>0)
													{
														$chked_pd.="Checked='checked'";
														$aprv_tech=mysqli_fetch_array($chk_pd_query);
														$aprv_name=mysqli_fetch_array(mysqli_query($link,"select * from employee where emp_id='$aprv_tech[main_tech]'"));
														$aprv_by=$aprv_name['name'];
													}
													
													$chk_doc=0;
													$chk_doc=mysqli_num_rows(mysqli_query($link,"select * from testresults where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$apt' and doc>0"));
													if($chk_doc>0)
													{
														//$chked_pd.="disabled='disabled'";
													}
												?>
												<th colspan='5'><?php echo $nbl_star.$tname['testname'];?></th>
												<th>
												<input id="test_<?php echo $j;?>" type="hidden" value="<?php echo $apt;?>"></input>
												<input id="aprv<?php echo $j;?>" name="<?php echo $j;?>" class="aprv_param" type="checkbox" onclick="approve_pad(<?php echo $j;?>)" <?php echo $chked_pd;?>/> <?php echo $aprv_by;?>
												
												<input type="checkbox" class="print_tst" value="<?php echo $apt;?>" id="<?php echo $apt;?>_print" onclick="test_print_group(this.value)" style="display:none"/>
												 </th>
											</tr>
											<?php
												}
												else
												{
													/*
													$chked_pd="";
													$chk_pd=mysqli_num_rows(mysqli_query($link,"select * from testresults where patient_id='$uhid' and visit_no='$visit' and testid='$apt' and main_tech>0"));
													if($chk_pd>0)
													{
														$chked_pd="Checked='checked'";
													}
													*/
													
													$aprv_by="Approve";
													$chked_pd="";
													$chk_pd_query=mysqli_query($link,"select * from testresults where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$apt' and main_tech>0");
													$chk_pd=mysqli_num_rows($chk_pd_query);
													if($chk_pd>0)
													{
														$chked_pd.="Checked='checked'";
														$aprv_tech=mysqli_fetch_array($chk_pd_query);
														$aprv_name=mysqli_fetch_array(mysqli_query($link,"select * from employee where emp_id='$aprv_tech[main_tech]'"));
														$aprv_by=$aprv_name['name'];
													}
													$chk_doc=0;
													$chk_doc=mysqli_num_rows(mysqli_query($link,"select * from testresults where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$apt' and doc>0"));
													if($chk_doc>0)
													{
														//$chked_pd.="disabled='disabled'";
													}
													
													
													echo "<tr><th colspan='5'>$nbl_star $tname[testname]</th><th>";
													?>
													<input id="test_<?php echo $j;?>" type="hidden" value="<?php echo $apt;?>"></input>
													<input id="aprv<?php echo $j;?>" name="<?php echo $j;?>" class="aprv_param" type="checkbox" onclick="approve_pad(<?php echo $j;?>)" <?php echo $chked_pd;?>/> <?php echo $aprv_by;?>
													 <?php
													 echo "</th></tr>";
													
												}
												
												}
												else
												{
													$aprv_by="Approve";
													$chked_pd="";
													$chked_pd_query=mysqli_query($link,"select * from patient_test_summary where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$apt' and main_tech>'0'");
													$chk_pd=mysqli_num_rows($chked_pd_query);
													if($chk_pd>0)
													{
														$chked_pd="Checked='checked'";
														$aprv_tech=mysqli_fetch_array($chk_pd_query);
														$aprv_name=mysqli_fetch_array(mysqli_query($link,"select * from employee where emp_id='$aprv_tech[main_tech]'"));
														$aprv_by=$aprv_name['name'];
													}
													
													$chk_doc=mysqli_num_rows(mysqli_query($link,"select * from patient_test_summary where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$apt' and doc>0"));
													if($chk_doc>0)
													{
														//$chked_pd.="disabled='disabled'";
													}
													
													echo "<tr><th colspan='5'>$nbl_star $tname[testname]</th><th>";
												 ?>
													<input id="test_<?php echo $j;?>" type="hidden" value="<?php echo $apt;?>"></input>
													<input id="aprv<?php echo $j;?>" name="<?php echo $j;?>" class="aprv_param" type="checkbox" onclick="approve_pad(<?php echo $j;?>)" <?php echo $chked_pd;?>/> <?php echo $aprv_by;?>
												 <?php
												 echo "</th></tr>";
												}
												 
												 
												 
												 $i=1;
												 
												 $param=mysqli_query($link, "select * from Testparameter where TestId='$apt'  and sequence>0 order by sequence"); 
												 while($p=mysqli_fetch_array($param))
												 {
												 $pn=mysqli_fetch_array(mysqli_query($link, "select * from Parameter_old where ID='$p[ParamaterId]'"));
												 if($pn[ResultType]!=0)
												 {
												 $res=mysqli_query($link, "select * from testresults where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$apt' and paramid='$p[ParamaterId]'");
												 $num=mysqli_num_rows($res);
												 if($num>0)
												 {
													$p_unit=mysqli_fetch_array(mysqli_query($link, "select unit_name from Units where ID='$pn[UnitsID]'"));
													 $meth=mysqli_fetch_array(mysqli_query($link, "select name from test_methods where id in(select method_id from parameter_method where param_id='$p[ParamaterId]')"));
													 $t_res=mysqli_fetch_array($res);
													 
													 $meth_name=mysqli_fetch_array(mysqli_query($link, "select name from test_methods where id='$pn[method]'"));
													 ?>
												<?php
													$par_class="";
													if($pn['ResultType']==8)
													{
													$par_class="tname";
													}
													else
													{
													$par_class="";
													}
													?>
												<tr class="tr_test">
													<?php
														$nbl_star="";
														$nres=$t_res['result'];
														if(strlen($nres)<15)
														{
															if($nbl_note_test!=1)
															{
																$nbl_tst=mysqli_num_rows(mysqli_query($link,"select * from nabl_logo where paramid='$pn[ID]'"));
																if($nbl_tst==0)
																{
																	$nabl=mysqli_fetch_array(mysqli_query($link,"select * from nabl"));
																	if($nabl[nabl]==1)
																	{
																		$nbl_star="*";
																		$nbl_note=1;
																	}
																}
															}								
														?>
													<td class="<?php echo $par_class;?>" valign="top" contenteditable="true"><?php echo $nbl_star.$pn[Name];?></td>
													<td width="1%" valign="top" style="text-align:right"><b><span id="chk_res<?php echo $i;?>" contenteditable="true">&nbsp;</span></b></td>
													<td valign="top" contenteditable="true" id="result<?php echo $i;?>" width="10%"><?php echo nl2br($t_res[result]);?></td>
													<td width="15%"><?php echo $p_unit[unit_name];?></td>
													<td id="norm_r<?php echo $i;?>" style='text-align:left;' contenteditable='true'>
														<script>load_normal('<?php echo $uhid;?>','<?php echo $p[ParamaterId];?>','<?php echo $t_res[result];?>','<?php echo $i;?>')</script>
													</td>
													<td><?php echo $meth_name[name];?></td>
													<?php
														}
														else
														{
														
														$par_class="";
														if($pn[ResultType]==8)
														{
														$par_class="tname";
														}
														else
														{
														$par_class="";
														}
														
														?>
													<td width="7%" valign="top" class="<?php echo $par_class;?>">
														<?php echo $pn[Name];?>
													</td>
													<td width="1%" valign="top" style="text-align:right"><b><span id="chk_res<?php echo $i;?>" contenteditable="true">&nbsp;</span></b></td>
													<?php
														if($meth_name[name])
														{
														?>
													<td colspan="3" valign="top">
														<?php echo nl2br($t_res[result]);?>
													</td>
													<td>
														<?php echo $meth_name[name];?>
													</td>
													<?php
														}
														else
														{
														?>
													<td colspan="4" valign="top">
														<?php echo nl2br($t_res[result]);?>
													</td>
													<?php
														}
														
														}
														?>
												</tr>
												<?php
													$i++;
													$j++;
													}
													}
													else
													{
													 echo "<tr><td colspan='7' style='text-align:left;padding-left:20px !important' ><b>$pn[Name]</b></td></tr>";
													}
													}
													
													?>
													</table>
													<div class="table-modifier">
														
													<?php
														$pat_sum=mysqli_query($link, "select * from patient_test_summary where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$apt'");
														$num_pat=mysqli_num_rows($pat_sum);
														
														if($num_pat>0)
														{
														 $pat_s=mysqli_fetch_array($pat_sum);
														 echo $pat_s[summary];
														}
														else
														{
														 $chk_sum=mysqli_query($link, "select * from test_summary where testid='$apt'");
														 $num_sum=mysqli_num_rows($chk_sum);
														 if($num_sum>0)
														 {
														 $summ_all=mysqli_fetch_array($chk_sum);
														 echo $summ_all[summary];
														 }
														 
														}
														?>
												</div>
												<?php
													
												?>
												
										</div>
										
										
								</div>
							</div>
						</div>	
	<?php
	
		
				
			}
		}
	}
	
	
	
	if($all_cult)
	{
		$all_cultz=explode("@", $all_cult);
		foreach($all_cultz AS $all_cult)
		{
			if($all_cult)
			{
				$j++;
?>
	<div class="container-fluid">
			<div class="">
				<div class="col-md-12" style="border-top:1px solid;border-bottom:1px solid;">
					<?php
						$samp=mysqli_fetch_array(mysqli_query($link, "select Name from Sample where ID in(select SampleId from TestSample where TestId='$all_cult')"));
					
						$tname=mysqli_fetch_array(mysqli_query($link, "select testname from testmaster where testid='$all_cult'"));
						$spec1=explode(" ",$tname[testname]);
						$spec_s=sizeof($spec1);
						$spec=array_pop($spec1);
						
						$col=mysqli_fetch_array(mysqli_query($link, "select * from testresults where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$all_cult' and paramid='311'"));
						$num=mysqli_num_rows(mysqli_query($link, "select * from testresults where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$all_cult' and paramid='313'"));
						 
						?>
					<table class="table">
					<tr>
						<th><?php echo $tname[testname];?></th>
						<th style="text-align:center">
							
							<?php
							$cult_res=mysqli_fetch_array(mysqli_query($link,"select * from testresults where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$all_cult' limit 1"));
							if($cult_res[result])
							{
							$aprv_by="Approve";
							$cul_chk="";
							if($cult_res[main_tech]>0)
							{
								$cul_chk="Checked='checked'";
								$aprv_name=mysqli_fetch_array(mysqli_query($link,"select * from employee where emp_id='$cult_res[main_tech]'"));
								$aprv_by=$aprv_name['name'];
								
								if($cult_res['doc']>0)
								{
									$cul_chk.="disabled='disabled'";
								}
							}
							?>
							<input type="hidden" id="test_<?php echo $j;?>" value="<?php echo $all_cult;?>"/>
							<input type="checkbox" id="aprv_<?php echo $j;?>" name="<?php echo $j;?>" class="aprv_param_cult" onclick="approve_culture(<?php echo $j;?>)" <?php echo $cul_chk;?>/> <?php echo $aprv_by;?>
							
							<?php } ?>
							
						</th>
					</tr>
					<tr>
						<td colspan="2">
									
						<?php
						$cult_table="";
						$cs_res_ant=mysqli_query($link,"select a.*,b.* from testresults a,Parameter_old b where a.patient_id='$uhid' and a.opd_id='$opd_id' and a.ipd_id='$ipd_id' and a.batch_no='$batch' and a.testid='$all_cult' and a.paramid=b.ID and b.ResultOptionID='68' order by a.sequence");
						if(mysqli_num_rows($cs_res_ant)==0)
						{
							$cult_table="cult_table";
						}
						?>
											
						<table class="table table-bordered table-condensed">
						<tr>
							<th style="text-align:left;width:200px;" valign="top">TEST</th>
							<td style="width:5px;" valign="top">:</td>
							<td valign="top"><?php echo $tname[testname];?></td>
						</tr>	
						<?php
							
							$fung=mysqli_fetch_array(mysqli_query($link, "select * from testresults where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch'  and testid='$all_cult' and paramid='310'"));
							
							$cs_res=mysqli_query($link,"select a.*,b.* from testresults a,Parameter_old b where a.patient_id='$uhid' and a.opd_id='$opd_id' and a.ipd_id='$ipd_id' and a.batch_no='$batch' and a.testid='$all_cult' and a.paramid=b.ID and b.ID!='311' and b.ID!='312' and b.ResultOptionID!='68' order by a.sequence");	
							while($cs_r=mysqli_fetch_array($cs_res))
							{
								
								if($cs_r[ID]==311 || $cs_r[ID]==312)
								{
											
								}
								else
								{
								?>
									<tr>
										<th style="text-align:left" valign="top"><?php echo $cs_r[Name];?></th>
										<td style="width:5px;" valign="top">:</td>
										<td valign="top"><?php echo $cs_r[result];?></td>
									</tr>
								<?php								
								}
							}
							
							$col=mysqli_fetch_array(mysqli_query($link,"select * from testresults where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid='$all_cult' and paramid='311'"));	
							$pow=mysqli_fetch_array(mysqli_query($link,"select * from testresults where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch' and testid='$all_cult' and paramid='312'"));	
							if($col[result])
							{
								?>
									<tr>
										<th style="text-align:left" valign="top">COLONY COUNT</th>
										<td style="width:5px;" valign="top">:</td>
										<td valign="top"><?php echo $col[result]."<sup>".$pow[result]."</sup> CFU/ml of ".$sample_name;?></td>
									</tr>
								<?php
							}
							?>
							
						</table>
						
						<?php
						
						if(mysqli_num_rows($cs_res_ant)>0)
						{
						?>
							<table class="table table-condensed table-bordered">
								<tr>
									<th contentEditable="true" width="33%">SENSITIVE</th>
									<th contentEditable="true" width="33%">INTERMEDIATE</th>
									<th contentEditable="true" width="33%">RESISTANT</th>
								</tr>
								<tr>
									<td valign="top" style="border-right:1px solid #CCC">
										<?php
											$sen=mysqli_query($link, "select a.* from testresults a,Parameter_old b where a.patient_id='$uhid' AND a.opd_id='$opd_id' and a.ipd_id='$ipd_id' and a.batch_no='$batch' and a.testid='$all_cult' and (a.result like 'S' or a.result like 's%') and a.paramid=b.ID order by b.Name");
											while($s=mysqli_fetch_array($sen))
											{
												$mic_s=explode("#MICValue#",$s[result]);
												$pn=mysqli_fetch_array(mysqli_query($link, "select Name from Parameter_old where ID='$s[paramid]'"));
												echo "<div style='display:inline-block;min-width:160px'>".$pn[Name]."</div> <br/>";
											}
											?>
									</td>
									<td valign="top" style="border-right:1px solid #CCC">
										<?php
											$sen=mysqli_query($link, "select a.* from testresults a,Parameter_old b where a.patient_id='$uhid' AND a.opd_id='$opd_id' and a.ipd_id='$ipd_id' and a.batch_no='$batch' and a.testid='$all_cult' and (a.result like 'I%' or a.result like 'i%') and a.paramid=b.ID order by b.Name");
											while($s=mysqli_fetch_array($sen))
											{
												$mic_i=explode("#MICValue#",$s[result]);
												$pn=mysqli_fetch_array(mysqli_query($link, "select Name from Parameter_old where ID='$s[paramid]'"));
												echo "<div style='display:inline-block;width:160px'>".$pn[Name]."</div> <br/>";
											}
											?>
									</td>
									<td valign="top" style="border-right:1px solid #CCC">
										<?php
											$sen=mysqli_query($link, "select a.* from testresults a,Parameter_old b where a.patient_id='$uhid' AND a.opd_id='$opd_id' and a.ipd_id='$ipd_id' and a.batch_no='$batch'  and a.testid='$all_cult' and ( a.result like 'R%' or a.result like 'r%') and a.paramid=b.ID order by b.Name");
											while($s=mysqli_fetch_array($sen))
											{
												$mic_r=explode("#MICValue#",$s[result]);
												$pn=mysqli_fetch_array(mysqli_query($link, "select Name from Parameter_old where ID='$s[paramid]'"));
												echo "<div style='display:inline-block;width:160px'>".$pn[Name]."</div><br/>";											}
											?>
									</td>
								</tr>
							</table>
						<?php
							}
							?>
						</td>
					</tr>	
						
							<?php
								$pat_sum=mysqli_query($link, "select * from patient_test_summary where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch' and  testid='$all_cult' and main_tech>0");
								$num_pat=mysqli_num_rows($pat_sum);
								
								if($num_pat>0)
								{
									$pat_s=mysqli_fetch_array($pat_sum);
									echo "<tr><td colspan='2'>".$pat_s[summary]."</td></tr>";
								}
								else
								{
									$chk_sum=mysqli_query($link, "select * from test_summary where testid='$all_cult'");
									$num_sum=mysqli_num_rows($chk_sum);
									if($num_sum>0)
									{
										$summ_all=mysqli_fetch_array($chk_sum);
										//echo $summ_all[summary];
									}
								}
								?>
					</table>	
					</div>
			</div>
		</div>
<?php
				$all_d++;
			}
		}
	}
	
	if($all_pad)
	{
		$al_p=explode("@",$all_pad);
		foreach($al_p as $ap)
		{
			if($ap)
			{
				$j++;
				
				$tname=mysqli_fetch_array(mysqli_query($link, "select * from testmaster where testid='$ap'"));
				$dep=mysqli_fetch_array(mysqli_query($link, "select name from test_department where id='$tname[type_id]'"));
				
				$auth_p=mysqli_fetch_array(mysqli_query($link,"select * from approve_details where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$ap'"));

			?>
		<input type="hidden" id="test_<?php echo $j;?>" value="<?php echo $ap;?>"/>
		<div class="container-fluid">
			<div class="">
				<div class="">
					<?php
						$tname=mysqli_fetch_array(mysqli_query($link, "select * from testmaster where testid='$ap'"));
						 
						$user_pad=mysqli_fetch_array(mysqli_query($link,"select * from employee where emp_id in(select user from patient_test_summary where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$ap')"));
						$user_tech=mysqli_fetch_array(mysqli_query($link,"select * from employee where emp_id in(select main_tech from patient_test_summary where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$ap')"));
						$user_doc=mysqli_fetch_array(mysqli_query($link,"select * from employee where emp_id in(select doc from patient_test_summary where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$ap')"));
						
						$pat_sum=mysqli_query($link, "select * from patient_test_summary where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='$ap'");
						$num_pat=mysqli_num_rows($pat_sum);
						?>
					<table class="table">
					<tr>
						<th colspan="3"><?php echo $tname[testname];?></th>
						<th colspan="3">
							<?php
							if(!$user_doc[emp_id] && $num_pat>0)
							{
								if($user==$user_tech[main_tech] || !$user_tech[main_tech])
								{
									?> <span class="sum_click"  onclick="load_sum_edit(<?php echo $ap;?>)"><i><u>(Click to edit)</u></i></span> <?php
								}
							}
							?>
							
						</th>
						<th>
							<?php
							if($num_pat>0)
							{
							$aprv_pad_txt="Approve";
							$aprv_pad_chk="";
							if($user_tech[emp_id])
							{
								$aprv_pad_txt=$user_tech[name];
								$aprv_pad_chk="checked";
							}
							?> 
							<input type="checkbox" class="aprv_param_pad" id="aprv_<?php echo $j;?>" onclick="approve_pad(<?php echo $j;?>)" <?php echo $aprv_pad_chk;?> /> <?php echo $aprv_pad_txt;?>
							<?php } ?>
						</th>
					</tr>
					<tr>
						<td colspan="7">
							<div id="pad_<?php echo $ap;?>">
							<?php
							
																
								if($num_pat>0)
								{
									$pat_s=mysqli_fetch_array($pat_sum);
									echo $pat_s[summary];
									$res_sum_pad=$pat_s[summary];
								}
								else
								{
									
									$chk_sum=mysqli_query($link, "select * from test_summary where paramid in(select ParamaterId from Testparameter where testid='$ap')");
									$num_sum=mysqli_num_rows($chk_sum);
									if($num_sum>0)
									{
										$summ_all=mysqli_fetch_array($chk_sum);
										//echo $summ_all[summary];
										$res_sum_pad=$summ_all[summary];
									}
									
								}
								?>
							</div>
							
							<div id="pad_edit_<?php echo $ap;?>" style='display:none'>
								<textarea style='height:350px;width:1100px' name="article-body-<?php echo $ap;?>" id="summary_<?php echo $ap;?>">
									<?php echo $res_sum_pad;?>
								</textarea>
								<div align="center">
									<button class="btn btn-primary btn-mini" onclick="save_summary(<?php echo $ap;?>)">Save & Approve</button>
									<button class="btn btn-alert btn-mini" onclick="load_sum_edit_hide(<?php echo $ap;?>)">Cancel</button>
								</div>
							</div>
						</td>
					</tr>
					</table> 	
						
				</div>	
			</div>
		</div>

		<?php
		
		}
		}
		$all_d++;
	}
	
	if($wid>0)
	{
		$j++;
	?>	
		<div class="container-fluid">
			<div class="">
				<div class="col-md-12" style='border-bottom:1px solid !important;border-top:1px solid !important'>
					<?php
						
						$auth_w=mysqli_fetch_array(mysqli_query($link,"select * from approve_details where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='1227'"));
											
						$phl=mysqli_fetch_array(mysqli_query($link, "select time,date from phlebo_sample where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='1227'"));
						
						$res_time=mysqli_fetch_array(mysqli_query($link, "select time,date from widalresult where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  limit 1"));
						
						$samp=mysqli_fetch_array(mysqli_query($link, "select Name from Sample where ID in(select SampleId from TestSample where TestId='1227')"));
						
						$user_wid=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id in(select v_User from widalresult where  `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch' )"));
					
						$user_wid_t=mysqli_fetch_array(mysqli_query($link,"select name from employee where emp_id in(select main_tech from widalresult where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  )"));
					?>
					
					<?php
						$tname=mysqli_fetch_array(mysqli_query($link, "select testname from testmaster where testid='1227'"));
						$tst=1227;
					?>
					<table class="table">
					<tr>
						<th><?php echo $tname[testname];?></th>
						<th style="text-align:center">
							<?php
								$aprv_by="<i>Approve</i>";
								$wid_chk="";
								$wid_res=mysqli_fetch_array(mysqli_query($link,"select * from widalresult where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  limit 1"));
								if($wid_res['main_tech']>0)
								{
									$wid_chk="Checked";
									$aprv_name=mysqli_fetch_array(mysqli_query($link,"select * from employee where emp_id='$wid_res[main_tech]'"));
									
									$aprv_by=$aprv_name['name'];
									
									if($wid_res['doc']>0)
									{
										$wid_chk.=" disabled";
									}
								}
								?>
								<input type="hidden" id="test_<?php echo $j;?>" value="<?php echo $tst;?>"/>
								<input type="checkbox" id="aprv_<?php echo $j;?>" name="<?php echo $j;?>" class="aprv_param_wid" onclick="approve_wid(<?php echo $j;?>)" <?php echo $wid_chk;?>/> <?php echo $aprv_by;?>
						</th>
					</tr>
					<tr>
						<td colspan="2">
					
										
					
						<?php
							$w1=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and slno=1"));
							$w2=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and slno=2"));
							$w3=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and slno=3"));
							$w4=mysqli_fetch_array(mysqli_query($link, "select * from widalresult where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and slno=4"));
						?>
						<table class="table table-bordered">
							<tr class="tr_border">
								<td><b>Dilution:</b></td>
								<td><b>1:20</b></td>
								<td><b>1:40</b></td>
								<td><b>1:80</b></td>
								<td><b>1:160</b></td>
								<td><b>1:320</b></td>
								<td><b>1:640</b></td>
							</tr>
							<tr>
								<td><b>Antigen 'O'</b></td>
								<td><?php echo $w1[F1]?></td>
								<td><?php echo $w1[F2]?></td>
								<td><?php echo $w1[F3]?></td>
								<td><?php echo $w1[F4]?></td>
								<td><?php echo $w1[F5]?></td>
								<td><?php echo $w1[F6]?></td>
							</tr>
							<tr>
								<td><b>Antigen 'H'</b></td>
								<td><?php echo $w2[F1]?></td>
								<td><?php echo $w2[F2]?></td>
								<td><?php echo $w2[F3]?></td>
								<td><?php echo $w2[F4]?></td>
								<td><?php echo $w2[F5]?></td>
								<td><?php echo $w2[F6]?></td>
							</tr>
							<tr>
								<td><b>Antigen 'A(H)'</b></td>
								<td><?php echo $w3[F1]?></td>
								<td><?php echo $w3[F2]?></td>
								<td><?php echo $w3[F3]?></td>
								<td><?php echo $w3[F4]?></td>
								<td><?php echo $w3[F5]?></td>
								<td><?php echo $w3[F6]?></td>
							</tr>
							<tr>
								<td><b>Antigen 'B(H)'</b></td>
								<td><?php echo $w4[F1]?></td>
								<td><?php echo $w4[F2]?></td>
								<td><?php echo $w4[F3]?></td>
								<td><?php echo $w4[F4]?></td>
								<td><?php echo $w4[F5]?></td>
								<td><?php echo $w4[F6]?></td>
							</tr>
							<tr>
								<td><b>IMPRESSION</b></td>
								<td colspan="6"><?php echo nl2br($w4[DETAILS]);?></td>
							</tr>
							</table>
					</td>
					</tr>
					
						<?php
							$pat_sum=mysqli_query($link, "select * from patient_test_summary where `patient_id`='$uhid' AND `opd_id`='$opd_id' AND `ipd_id`='$ipd_id' AND `batch_no`='$batch'  and testid='1227'");
							$num_pat=mysqli_num_rows($pat_sum);
							
							if($num_pat>0)
							{
								$pat_s=mysqli_fetch_array($pat_sum);
								echo "<tr><td colspan='2'>".$pat_s['summary']."</td>";	
							}
							else
							{
								$chk_sum=mysqli_query($link, "select * from test_summary where testid='1227'");
								$num_sum=mysqli_num_rows($chk_sum);
								if($num_sum>0)
								{
									$summ_all=mysqli_fetch_array($chk_sum);
									echo "<tr><td colspan='2'>".$summ_all['summary']."</td>";
								}
								
							}
							?>
						
					</table>
				</div>
			</div>
		</div>
		<hr/>
		<?php
		
	}
	?>
			
<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"/>
<input type="hidden" value="0" id="mod_chk"/>
<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-keyboard="false">
<div class="modal-dialog">
<div class="modal-content">
  
  <div class="modal-body">
	<div id="results"> </div>
  </div>
 
</div>
</div>
</div>			
	<?php


if($_GET['check']==1)
{
	?>
	<script>
		//$(".aprv_param:checkbox:not(:checked)").click();
		var len=$(".aprv_param:checkbox:not(:checked)").length-1;
		$(".aprv_param:checkbox").each(function(i)
		{
			var res_val=$(this).attr("name");
			
			if($(this).prop("checked")==false && $("#res_chk"+res_val+"").text().trim()!='')
			{
				$(this).prop("checked",true);
				$(this).click();
				$(this).prop("checked",true);
			}
		});
						
		$(document).ajaxStop(function()
		{
			var pid=$("#uhid").val();
			var opd_id=$("#opd_id").val();
			var ipd_id=$("#ipd_id").val();
			var batch_no=$("#batch_no").val();
			var dep=$("#n_dep").val();
			var user=$("#user").val();
			var fdoc=$("#fdoc").val();
			var check=2;
			
			alert("technician_approve_test.php?uhid="+pid+"&opd_id="+opd_id+"&ipd_id="+ipd_id+"&batch_no="+batch_no+"&dep="+dep+"&user="+user+"&fdoc="+fdoc+"&check="+check;);
			
			document.location="technician_approve_test.php?uhid="+pid+"&opd_id="+opd_id+"&ipd_id="+ipd_id+"&batch="+batch_no+"&dep="+dep+"&user="+user+"&fdoc="+fdoc+"&check="+check;
		});
	
	
	
	</script>	
	<?php	
}

?>

<div align="center">
	
</div>

<div align="center">
		<?php
		$cls="btn btn-info";
		$txt_flag="Flag This Patient";
		$det=mysqli_fetch_array(mysqli_query($link,"select * from patient_flagged_details where patient_id='$uhid' and opd_id='$opd_id' and dept_id='$dep'"));
		if($det)
		{
			$cls="btn btn-danger";
			$txt_flag="Patient Flagged";
		}
		?>
		<!--<button class="<?php echo $cls;?>" onclick="flag_pat()" id="flag"> <?php echo $txt_flag;?></button>-->
		<button class="btn btn-danger" onclick="window.close()">Exit</button>
	</div>


<div id="repeat_result" style="position:absolute;display:none;"></div>

<div id="hem_image" style="position:absolute;display:none;">

</div>

</div> <!----Current Div Ends---->

<script>load_instr()</script>
<!----------------------Haem-Image-------------->
<?php
if($dep==29)
{?>
	<script>load_hem_image()</script>
<?php 
} 
?>
<!----------------------Haem-Image-------------->
</body>
</html>
<style>
h3
{
	margin: 2px 0;
}
select, .btn, .table-bordered
{
	border-radius: 0px;
}
.header-table
{
	font-size:14px;
}
.test-table
{
	font-size:13px;
}
.table-condensed th, .table-condensed td
{
	padding: 1px 5px;
}

.table div table td{ border:1px solid}

.sum_click{color:red;cursor:pointer;}
</style>
