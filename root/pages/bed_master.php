<?php
$user_change_disabled="disabled";
if($p_info["levelid"]==1)
{
	$user_change_disabled="";
}
if($p_info["levelid"]==1)
{
	$branch_str="";
	$branch_display="display:none;";
}
else
{
	$branch_str=" AND branch_id='$p_info[branch_id]'";
	$branch_display="display:none;";
}
$branch_id=$p_info["branch_id"];
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="row">
		<div class="span11" >
			<table class="table table-bordered table-condensed">
				<tr>
					<td colspan="6">
						<center>
							<b>Bed No</b>
							<input type="text" id="bed_no_sr" onkeyup="load_emp(this.value,event)" placeholder="Search Bed" />
							
							<select id="branch_id" class="span2" onchange="load_ward()" style="<?php echo $branch_display; ?>">
							<?php
								$branch_qry=mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
								while($branch=mysqli_fetch_array($branch_qry))
								{
									if($branch_id==$branch["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
									echo "<option value='$branch[branch_id]' $branch_sel>$branch[name]</option>";
								}
							?>
							</select>
						</center>
					</td>
				</tr>
				<tr>
					<td colspan="6">
						<div id="emp_list" style="max-height:450px;overflow-y:scroll;">
							
						</div>
					</td>
				</tr>
				<tr>
					<th>
						Select Ward
					</th>
					<td>
						<select id="ward" onchange="load_room_list(0)" autofocus>
							<option value="0">--Select--</option>
						</select>
					</td>
					<th>
						Room No
					</th>
					<td id="room_list">
						<select id="room" onChange="ward_change()">
							<option value="0">--Select--</option>
						</select>
					</td>
					<th>
						Bed No
					</th>
					<td>
						<input type="text" id="bed" onkeyup="bed_no(this,event)" onblur="bed_no_blur()" placeholder="Bed No" />
						<div style="display:none;">
							<br>
							<label> <input type="checkbox" id="private_bed"> Private Bed</label>
							<label> <input type="checkbox" id="share_bed" onchange="share_bed_change()"> Share Bed</label>
							<div id="main_bed_div" style="display:none;">
								<select id="main_bed_id">
									<option>Select Main Bed</option>
								</select>
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<th>
						Charges
					</th>
					<td>
						<input type="text" id="charge" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" placeholder="Charges per day" />
					</td>
					<th colspan="3">
						<span class="text-right">Bed ID</span>
					</th>
					<td>
						<input type="text" id="bed_id" readonly>
					</td>
				</tr>
				<tr>
					<th>
						Other Charges
					</th>
					<td colspan="5">
						<div id="other_charge_table"></div>
						<select id="othr_chrg">
							<option value="0">Select</option>
					<?PHP
						$q_qry=mysqli_query($link, " SELECT `charge_id`,`charge_name` FROM `charge_master` WHERE `group_id`='148' order by `charge_id` ");
						while($q=mysqli_fetch_array($q_qry))
						{
							echo "<option value='$q[charge_id]'>$q[charge_name]</option>";
							//echo "<label><input type='checkbox' id='othr$q[charge_id]' class='chk' value='$q[charge_id]' $chk/> $q[charge_name]</label>";
						}
					?>
						</select>
						<button class="btn btn-success" onClick="add_other_charge()">Add</button>
						<input type="hidden" id="sel_othr_chrg_id">
					</td>
				</tr>
				<tr style="display:none;">
					<th>Housekeeping</th>
					<td colspan="5">
						<div id="area_charge_table"></div>
						<select class="" id="item_id" name="item_id">
							<option value="0">Select Item</option>
							<?php
								$item_qry=mysqli_query($link," SELECT * FROM `cleaning_item_master` order by `item_id` ");
								while($item=mysqli_fetch_array($item_qry))
								{		
									echo "<option value='$item[item_id]'>$item[item_name]</option>";
								}
								?>
						</select>
						<select class="" id="item_mat_id" name="item_mat_id">
							<option value="0">Select Material</option>
							<?php
								$item_mat_qry=mysqli_query($link," SELECT * FROM `cleaning_material_master` order by `item_mat_id` ");
								while($item_mat=mysqli_fetch_array($item_mat_qry))
								{		
									echo "<option value='$item_mat[item_mat_id]'>$item_mat[item_mat_name]</option>";
								}
								?>
						</select>
						<select class="" id="frequency" name="frequency">
							<option value="0">Select Frequency</option>
							<option value="1">Once a day</option>
							<option value="2">Twice a day</option>
							<option value="3">Thice a day</option>
							<option value="7">Once a week</option>
							<option value="30">Once a month</option>
						</select>
						<button class="btn btn-success" onClick="add_area_info()">Add</button>
						<input type="hidden" id="sel_area_chrg">
					</td>
				</tr>
				<tr>
					<td colspan="6" style="text-align:center;">
						<input type="button" id="sav" class="btn btn-info" onclick="save()" value="Save" />
						<input type="button" id="" class="btn btn-warning" onclick="clrr()" value="Reset" />
						<input type="button" name="button3" id="button3" onclick="popitup('pages/bed_master_print.php')" value="View" class="btn btn-info" />
						<!--<input type="button" id="" class="btn btn-success" onclick="vew()" value="View" />-->
						<input type="button" id="bed_del" class="btn btn-danger" onclick="delete_bed()" value="Delete" style="display:none;">
					</td>
				</tr>
			</table>
		</div>
		<!--<div class="span5" style="">
			<div id="res" style="max-height:300px;overflow-y:scroll;">
			
			</div>
		</div>-->
	</div>
	<!--<script>load_bed();</script>-->
	<!--modal-->
		<a href="#myAlert" data-toggle="modal" id="dl" class="btn" style="display:none;">A</a>
		<div id="myAlert" class="modal fade">
		  <div class="modal-body">
			  <div id="bed_list" style="max-height:400px;overflow-y:scroll;">
			  
			  </div>
		  </div>
		  <div class="modal-footer">
<!--
			<a data-dismiss="modal" onclick="delete_bed()" class="btn btn-primary" href="#">Confirm</a>
-->
			<a data-dismiss="modal" class="btn btn-danger" href="#">Close</a>
		  </div>
		</div>
	  <!--modal end-->
</div>
<script>
	$(document).ready(function()
	{
		load_id();
		load_ward();
		$("#ward").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				if($(this).val()=="0")
				{
					$(this).css("border","1px solid #f00");
				}
				else
				{
					$(this).css("border","");
					$("#room").focus();
				}
			}
		});
		$("#bed").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				{
					$(this).css("border","1px solid #f00");
				}
				else
				{
					$(this).css("border","");
					//$("#charge").focus();
				}
			}
		});
		$("#charge").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				{
					$(this).css("border","1px solid #f00");
				}
				else
				{
					$(this).css("border","");
					$("#sav").focus();
				}
			}
		});
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
	function popitup(url)
	{
		var branch_id=$("#branch_id").val();
		url=url+"?bid="+btoa(branch_id);
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function vew()
	{
		$.post("pages/bed_master_data.php",
		{
			ward:$("#ward").val(),
			type:"view_bed_det",
		},
		function(data,status)
		{
			$("#dl").click();
			$("#bed_list").html(data);
		})
	}
	function load_ward()
	{
		$.post("pages/bed_master_data.php",
		{
			type:"load_ward",
			branch_id:$("#branch_id").val(),
		},
		function(data,status)
		{
			$("#ward").html(data);
		})
	}
	function load_id()
	{
		$.post("pages/bed_master_data.php",
		{
			type:"bed_master_bed_id",
		},
		function(data,status)
		{
			$("#bed_id").val(data);
			$("#sav").val('Save');
			$("#bed_del").fadeOut(200);
		})
	}
	function ward_change()
	{
		var ward=$("#ward").val();
		var room=$("#room").val();
		var bed=$("#bed").val();
		if(ward!='0' && room!='0' && bed!='')
		{
			//load_bed_details(ward,room,bed);
		}
		load_main_bed(ward,room);
	}
	function load_main_bed(ward,room)
	{
		$.post("pages/bed_master_data.php",
		{
			type:"load_main_bed",
			ward:ward,
			room:room,
		},
		function(data,status)
		{
			$("#main_bed_id").html(data);
		})
	}
	function room_change()
	{
		var ward=$("#ward").val();
		var room=$("#room").val();
		var bed=$("#bed").val();
		if(ward!='0' && room!='0' && bed!='')
		{
			load_bed_details(ward,room,bed);
		}
	}
	function share_bed_change()
	{
		if($("#share_bed:checked").length)
		{
			$("#main_bed_div").show();
		}
		else
		{
			$("#main_bed_div").hide();
		}
	}
	function bed_no(www,e)
	{
		//if (/\D/g.test(www.value)) www.value = www.value.replace(/\D/g,'');
		var ward=$("#ward").val();
		var room=$("#room").val();
		var bed=$("#bed").val();
		if(ward!='0' && room!='0' && bed!='')
		{
			if(e.keyCode==13)
			{
				load_bed_details(ward,room,bed);
			}
		}
	}
	function bed_no_blur()
	{
		var ward=$("#ward").val();
		var room=$("#room").val();
		var bed=$("#bed").val();
		if(ward!='0' && room!='0' && bed!='')
		{
			//load_bed_details(ward,room,bed);
		}
	}
	function load_bed_details(ward,room,bed)
	{
		$.post("pages/bed_master_data.php",
		{
			type:"load_bed_detail",
			ward:ward,
			room:room,
			bed:bed,
		},
		function(data,status)
		{
			if(data==0)
			{
				$("#charge").focus();
				$("#sel_othr_chrg_id").val('');
				load_sel_othr_charge('');
				
				$("#sel_area_chrg").val('');
				load_sel_area_charge('');
				
				load_id();
			}else
			{
				var vl=data.split("@govin@");
				$("#bed_id").val(vl[0]);
				$("#ward").val(vl[1]);
				load_room_list(vl[2]);
				//$("#room").val(vl[2]);
				$("#bed").val(vl[3]);
				
				setTimeout(function(){
					$("#main_bed_id").val(vl[4]);
				},100);
				
				if(vl[5]==1)
				{
					$("#share_bed").prop("checked",true);
					
					$("#main_bed_div").show();
				}
				else
				{
					$("#share_bed").prop("checked",false);
					$("#main_bed_div").hide();
				}
				if(vl[6]==1)
				{
					$("#private_bed").prop("checked",true);
				}
				else
				{
					$("#private_bed").prop("checked",false);
				}
				$("#charge").val(vl[7]);
				$("#sav").val('Update');
				$("#ward").css("border","");
				$("#room").css("border","");
				//$("#bed").focus();
				
				$("#sel_othr_chrg_id").val(vl[8]);
				load_sel_othr_charge(vl[9]);
				
				$("#sel_area_chrg").val(vl[9]);
				load_sel_area_charge(vl[9]);
				
				$("#bed_del").fadeIn(200);
				
			}
		})
	}
	
	function save()
	{
		var share_bed=$("#share_bed:checked").length;
		if(!share_bed){ share_bed=0; }
		
		var private_bed=$("#private_bed:checked").length;
		if(!private_bed){ private_bed=0; }
		
		if($("#ward").val()=="0")
		{
			$("#ward").focus();
		}
		else if($("#room").val()=="0")
		{
			$("#room").focus();
		}
		else if($("#bed").val()=="")
		{
			$("#bed").focus();
		}
		else if($("#charge").val()=="")
		{
			$("#charge").focus();
		}
		else
		{
			if(share_bed==1 && $("#main_bed_id").val()==0)
			{
				$("#main_bed_id").focus();
				return false;
			}
			
			//alert();
			$.post("pages/bed_master_data.php",
			{
				type:"save_bed",
				bed_id:$("#bed_id").val(),
				ward:$("#ward").val(),
				room:$("#room").val(),
				bed:$("#bed").val(),
				main_bed_id:$("#main_bed_id").val(),
				share_bed:share_bed,
				private_bed:private_bed,
				charge:$("#charge").val(),
				othr_chrge:$("#sel_othr_chrg_id").val(),
				sel_area_chrg:$("#sel_area_chrg").val(),
				user:$("#user").text().trim(),
			},
			function(data,status)
			{
				var val=data.split("@");
				bootbox.dialog({ message: "<h5>"+val[0]+"</h5>"});
				setTimeout(function()
				{
					bootbox.hideAll();
					if(val[1]==1)
					{
						clrr();
						window.location.reload(true);
					}
				}, 2000);
			})
		}
	}
	function load_room_list(v)
	{
		$("#ward").css("border","");
		$.post("pages/bed_master_data.php",
		{
			ward:$("#ward").val(),
			type:"load_room_list",
		},
		function(data,status)
		{
			$("#room_list").html(data);
			$("#room").val(v);
		})
	}
	
	function clrr()
	{
		$("#ward").val('0');
		$("#room").val('0');
		$("#bed").val('');
		$("#charge").val('');
		$("#sav").val('Save');
		$("#ward").css("border","");
		$("#room").css("border","");
		$("#ward").focus();
		$("#sel_othr_chrg_id").val('');
		load_sel_othr_charge('');
		$("#sel_area_chrg").val('');
		load_sel_area_charge('');
		load_id();
	}
	function add_other_charge()
	{
		var id=$("#othr_chrg").val();
		var ww=$("#sel_othr_chrg_id").val();
		
		if (ww.indexOf(id) > -1)
		{
			bootbox.alert("Already added");
			return true;
		}
		
		var tid=ww+'@@'+id;
		$("#sel_othr_chrg_id").val(tid);
		load_sel_othr_charge(tid);
		$("#othr_chrg").val('0');
	}
	function load_sel_othr_charge(val)
	{
		$.post("pages/bed_master_data.php",
		{
			sval:val,
			type:"bed_add_other_charge",
		},
		function(data,status)
		{
			$("#other_charge_table").html(data);
		})
	}
	function delete_data(id)
	{
		//alert(id);
		var qq=$("#sel_othr_chrg_id").val();
		qq = qq.replace(id,'');
		$("#sel_othr_chrg_id").val(qq);
		load_sel_othr_charge(qq);
	}
	function add_area_info()
	{
		var itm=$("#item_id").val();
		var itm_mat=$("#item_mat_id").val();
		var frq=$("#frequency").val();
		var qq=$("#sel_area_chrg").val();
		
		if(itm!=0 && itm_mat!=0 && frq!=0)
		{
			var ww=$("#sel_area_chrg").val();
			var str="i"+itm+"@@m"+itm_mat;
			
			if (ww.indexOf(str) > -1)
			{
				bootbox.alert("Already added");
				return true;
			}
			
			var qq=qq+"###i"+itm+'@@m'+itm_mat+'@@f'+frq;
			$("#sel_area_chrg").val(qq);
			
			load_sel_area_charge(qq);
			$("#item_id").val('0');
			$("#item_mat_id").val('0');
			$("#frequency").val('0');
		}
	}
	function load_sel_area_charge(val)
	{
		$.post("pages/bed_master_data.php",
		{
			sval:val,
			type:"area_add_other_charge",
		},
		function(data,status)
		{
			$("#area_charge_table").html(data);
		})
	}
	function delete_sel_area(itm,itm_mat,frq)
	{
		var qq="###i"+itm+'@@m'+itm_mat+'@@f'+frq;
		var ww=$("#sel_area_chrg").val();
		ww = ww.replace(qq,'');
		$("#sel_area_chrg").val(ww);
		load_sel_area_charge(ww);
	}
	
	
	
	
	
	/*
	function load_bed()
	{
		$.post("pages/bed_master_data.php",
		{
			type:"load_bed",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	
	function edt(id)
	{
		$.post("pages/global_load_g.php",
		{
			id:id,
			type:"edit_bed",
		},
		function(data,status)
		{
			var vl=data.split("@govin@");
			$("#id").val(vl[0]);
			$("#ward").val(vl[1]);
			load_room_list(vl[2]);
			//$("#room").val(vl[2]);
			$("#bed").val(vl[3]);
			$("#charge").val(vl[4]);
			$("#sav").val('Update');
			$("#ward").css("border","");
			$("#room").css("border","");
			$("#bed").focus();
			
			$("#sel_othr_chrg_id").val(vl[5]);
			load_sel_othr_charge(vl[5]);
		})
	}
	function del(id)
	{
		$("#dl").click();
		$("#idl").val(id);
	}
	function delete_bed()
	{
		$.post("pages/global_delete_g.php",
		{
			id:$("#idl").val(),
			type:"delete_bed",
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
				clrr();
			}, 1000);
		})
	}
	*/
	var emp_d=1;
	var emp_div=0;
	function load_emp(val,e)
	{
		$("#emp_list").fadeIn(500);
		
		var unicode=e.keyCode? e.keyCode : e.charCode;
		
		if(unicode==13)
		{
			var eid=$("#e_id"+emp_d+"").val();
			eid=eid.split("@@");
			var tst=$("#testt").val();
			load_emp_details(eid[0],eid[1],eid[2]);
		}
		else if(unicode==38)
		{
			var chk=emp_d-1;
			var cc=$("#row_id"+chk+"").html();
			if(cc)
			{
				$(".all_pat_row").css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				
				emp_d=emp_d-1;
				$("#row_id"+emp_d).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
				var emp_d1=emp_d+1;
				$("#row_id"+emp_d1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				var z2=emp_d%1;
				if(z2==0)
				{
					emp_div=emp_div-30;
					$("#emp_list").scrollTop(emp_div)
					
				}
			}
		}
		else if(unicode==40)
		{
			var chk=emp_d+1;
			var cc=$("#row_id"+chk+"").html();
			if(cc)
			{
				$(".all_pat_row").css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				
				emp_d=emp_d+1;
				$("#row_id"+emp_d).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
				var emp_d1=emp_d-1;
				$("#row_id"+emp_d1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
				var z2=emp_d%1;
				if(z2==0)
				{
					$("#emp_list").scrollTop(emp_div)
					emp_div=emp_div+30;
				}
			}
		}
		else
		{
			if(val.length>0)
			{
				$.post("pages/bed_master_data.php",
				{
					val:val,
					type:"search_load_bed",
					branch_id:$("#branch_id").val(),
				},
				function(data,status)
				{
					$("#emp_list").html(data);
				})
			}else if(val.length==0)
			{
				$("#emp_list").html("");
			}
		}
	}
	function load_emp_details(ward,room,bed)
	{
		//alert(ward+' '+room+' '+bed);
		load_bed_details(ward,room,bed);
		load_main_bed(ward,room);
		$("#emp_list").slideUp(500);
	}
	function delete_bed()
	{
		//alert($("#bed_id").val());
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to delete this bed</h5>",
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
					className: "btn btn-danger",
					callback: function() {
						$.post("pages/bed_master_data.php",
						{
							type:"delete_bed",
							bed_id:$("#bed_id").val(),
						},
						function(data,status)
						{
							bootbox.dialog({ message: "<b>"+data+"</b>"});
							setTimeout(function(){
								bootbox.hideAll();
								clrr();
							 }, 2000);
						})
					}
				}
			}
		});
	}
</script>
<style>
	#myAlert
	{
		width: 1000px !important;
		left: 30%;
	}
	#b_det tr:hover
	{
		background:none;
	}
	
</style>
