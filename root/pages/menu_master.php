<script>
	function load_menu(h)
	{
		if(h==0)
		{
			$("#upd").fadeOut();
		}
		else
		{
			$.post("pages/menu_master_data.php",
			{
				type:"load_menu",
				head:h,
			},
			function(data,status)
			{
				$("#upd").fadeIn();
				$("#upd").html(data);
			})
		}
	}
	function save_menu()
	{
		if($("#hd").val()=="0")
		{
			$("#hd").focus();
		}
		else if($("#menu").val()=="")
		{
			$("#menu").focus();
		}
		else if($("#par").val()=="")
		{
			$("#par").focus();
		}
		else
		{
			$.post("pages/menu_master_data.php",
			{
				type:"save_menu",
				menu:$("#menu").val(),
				par:$("#par").val(),
				head:$("#hd").val(),
				seq:$("#seq").val(),
				access_to:$("#access_to").val(),
				menu_remarks_new:$("#menu_remarks_new").val(),
			},
			function(data,status)
			{
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					location.reload();
				}, 1000);
			})
		}
	}
	function edit(i)
	{
		$.post("pages/menu_master_data.php",
		{
			id:i,
			type:"edit_menu",
		},
		function(data,status)
		{
			$("#modle").click();
			var vl=data.split("@gov@");
			$("#pid").val(vl[0]);
			$("#pname").val(vl[1]);
			$("#phd").val(vl[2]);
			$("#seqn").val(vl[3]);
			setTimeout(function(){ $("#pname").focus(); }, 500);
		})
	}
	function updatee()
	{
		$.post("pages/menu_master_data.php",
		{
			type:"update_menu",
			pid:$("#pid").val(),
			pname:$("#pname").val(),
			phead:$("#phd").val(),
			seq:$("#seqn").val(),
		},
		function(data,status)
		{
			$("#modle").click();
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 1000);
			load_menu($("#u_head").val());
		})
	}
	function del(i)
	{
		$("#dl").click();
		$("#idl").val(i);
	}
	function del_menu()
	{
		$.post("pages/menu_master_data.php",
		{
			pid:$("#idl").val(),
			type:"del_menu",
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 1000);
			load_menu($("#u_head").val());
		})
	}
	function hide_div()
	{
		$("#u_head").val('0');
		$("#upd").slideUp(500);
	}
	function menu_hidden(val,pid)
	{
		$.post("pages/menu_master_data.php",
		{
			pid:pid,
			val:val,
			type:"hidden_menu",
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
			}, 1000);
			load_menu($("#u_head").val());
		})
	}
	function menu_remarks_up(e,val,par_id)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			$.post("pages/menu_master_data.php",
			{
				par_id:par_id,
				val:val,
				type:"save_menu_remarks",
			},
			function(data,status)
			{
				$("#menu_remarks"+par_id).css({'border-color': '#28B779'});
			})
		}
	}
</script>
<script src="../js/jquery.uniform.js"></script>
<link rel="stylesheet" href="../css/uniform.css" type="text/css" />
<!-- Select2 Plugin -->
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Menu Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="widget-box">
          <div class="widget-title">
            <ul class="nav nav-tabs">
              <li class="active" onclick="hide_div()"><a data-toggle="tab" href="#tab1">New Menu</a></li>
              <li><a data-toggle="tab" href="#tab2">Update Menu</a></li>
            </ul>
          </div>
          <div class="widget-content tab-content">
            <div id="tab1" class="tab-pane active">
              <table class="table table-bordered table-condensed">
					<tr>
						<th>Select Header</th>
						<td>
							<select id="hd">
								<option value="0">--Select--</option>
								<?php
								$q=mysqli_query($link,"SELECT * FROM `menu_header_master` ORDER BY `name`");
								while($r=mysqli_fetch_array($q))
								{
								?>
								<option value="<?php echo $r['id'];?>"><?php echo $r['name'];?></option>
								<?php
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<th class="span2">Menu Name</th>
						<td><input type="text" id="menu" name="menu" /></td>
					</tr>
					<tr>
						<th class="span2">Parameter Id</th>
						<td><input type="text" id="par" name="par" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" /></td>
					</tr>
					<tr>
						<th class="span2">Remarks</th>
						<td>
							<input type="text" id="menu_remarks_new" name="menu_remarks_new" />
						</td>
					</tr>
					<tr>
						<th class="span2">Sequence</th>
						<td><input type="text" id="seq" name="seq" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" /></td>
					</tr>
					<tr>
						<th class="span2">Access To</th>
						<td>
							<select multiple id="access_to">
								<?php
									$lv=mysqli_query($link,"select * from level_master");
									while($l=mysqli_fetch_array($lv))
									{
										echo "<option value='$l[levelid]' $sel>$l[name]</option>";
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<th colspan="2" style="text-align:center">
							<input type="button" id="save" name="save" class="btn btn-info" value="Save" onclick="save_menu()"/>
						</th>
					</tr>
				</table>
            </div>
            <div id="tab2" class="tab-pane">
              Select Header: 
				<select id="u_head" onchange="load_menu(this.value)">
				<option value="0">-Select-</option>
				<?php
				$hd=mysqli_query($link, "select * from menu_header_master order by `name`");
				while($h=mysqli_fetch_array($hd))
				{
				echo "<option value='$h[id]'>$h[name]</option>";	
				}
				?>
				</select>
			  <div id="upd" style="display:none;">
				  
			  </div>
            </div>
          </div>
        </div>
		  <input type="button" data-toggle="modal" data-target="#myModale" id="modle" style="display:none"/>
			<input type="text" id="mod" style="display:none;" />
				<div class="modal fade" id="myModale" role="dialog">
					<div class="modal-dialog">
						<div class="modal-content">
							<!--<div class="modal-header">
							</div>-->
							<div class="modal-body">
								<div id="res">
									<table class="table table-condensed" style="">
										<tr>
											<th>Header</th><th>Parameter Name</th><th>Sequence</th>
										</tr>
										<tr>
											<td>
												<select id="phd">
													<?php
													$sel=mysqli_query($link,"SELECT * FROM `menu_header_master` ORDER BY `name`");
													while($s=mysqli_fetch_array($sel))
													{
													?>
													<option value="<?php echo $s['id'];?>"><?php echo $s['name'];?></option>
													<?php
													}
													?>
												</select>
											</td>
											<td><input type="text" id="pid" style="display:none;" /><input type="text" id="pname" class="span2" /></td>
											<td><input type="text" id="seqn" class="span1" /></td>
										</tr>
									</table>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-info" onclick="updatee()">Submit</button>
								<button type="button" class="btn btn-danger" onclick="$('#mod').val('0')" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div>
				
		<!--modal-->
			<a href="#myAlert" data-toggle="modal" id="dl" class="btn" style="display:none;">A</a>
            <div id="myAlert" class="modal hide">
              <div class="modal-body">
				  <input type="text" id="idl" style="display:none;" />
                <p>Are You Sure Want To Delete...?</p>
              </div>
              <div class="modal-footer">
				<a data-dismiss="modal" onclick="del_menu()" class="btn btn-primary" href="#">Confirm</a>
				<a data-dismiss="modal" onclick="" class="btn btn-info" href="#">Cancel</a>
			  </div>
            </div>
          <!--modal end-->
</div>
<style>
.modal
{
    z-index: 999 !important;
}
.modal-backdrop
{
	z-index: 990 !important;
}
</style>
<?php
	//mysqli_query($link," INSERT INTO `test_insert`(`data`) VALUES ('$_SERVER[REMOTE_ADDR]') ");
?>
