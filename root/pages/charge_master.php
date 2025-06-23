<?php
$user_change_disabled="disabled";
if($p_info["levelid"]==1)
{
	$user_change_disabled="";
}
if($p_info["levelid"]==1 && $p_info["branch_id"]==2)
{
	$branch_str="";
	$branch_display="display:none;";
}
else
{
	$branch_str=" AND branch_id='$p_info[branch_id]'";
	$branch_display="display:none;";
}
$branch_display="display:none;";

$branch_id=$p_info["branch_id"];
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="row">
		<div class="span5">
			<table class="table table-striped table-bordered table-condensed">
				<tr style="<?php echo $branch_display; ?>">
				<th>Branch</th>
				<td>
					<select id="branch_id" class="" onchange="load_item()" style="<?php echo $branch_display; ?>">
					<?php
						$branch_qry=mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
						while($branch=mysqli_fetch_array($branch_qry))
						{
							if($branch_id==$branch["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
							echo "<option value='$branch[branch_id]' $branch_sel>$branch[name]</option>";
						}
					?>
					</select>
				</td>
			</tr>
				<tr>
					<th>Charge ID</th>
					<td>
						<label for="charge_id"></label>
						<input type="text" name="charge_id" id="charge_id" class="imp" readonly>
					</td>
				</tr>
				<tr>
					<th>Charge Name</th>
					<td>
						<label for="charge_name"></label>
						<input type="text" name="charge_name" id="charge_name" class="imp" onkeyup="charge_name(event)" autofocus />
						<span id="span_bed_no" style="display:none;"></span>
					</td>
				</tr>
				<tr>
					<th>Charge Group</th>
					<td>
						<label for="group_id"></label>
						<select id="group_id">
							<option value="0">Select</option>
						<?php
							$q_qry=mysqli_query($GLOBALS["___mysqli_ston"], " SELECT * FROM `charge_group_master` order by `group_name` ");
							while($q=mysqli_fetch_array($q_qry))
							{
								echo "<option value='$q[group_id]'>$q[group_name]</option>";
							}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<th>Amount</th>
					<td>
						<label for="amount"></label>
						<input type="text" name="amount" id="amount" class="" >
					</td>
				</tr>
				<tr>
					<th>Link with Doctor</th>
					<td>
						<label><input type="radio" name="client" class="doc_link0" id="doc_link" value="0" checked> No</label>
						<label><input type="radio" name="client" class="doc_link1" id="doc_link" value="1"> Yes</label>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center" >
						<input type="button" name="button" id="button" value="Save" onclick="Insert()" class="btn btn-info" />
						<input type="button" name="button2" id="button2" onclick="clearr()" value="Reset" class="btn btn-danger" />
						<input type="button" name="button3" id="button3" onclick="popitup('pages/charge_name_print.php')" value="View" class="btn btn-info" />
					</td>
				</tr>
			</table>
		</div>
		<div class="span6">
			Search <input type="text" id="txtdoc" size="35" onkeyup="sel_pr(this.value,event)"/>
			<select id="category" onchange="load_item()">
				<option value="0">All</option>
				<?php
				$grp=mysqli_query($link,"SELECT * FROM `charge_group_master` ORDER BY `group_name`");
				while($gr=mysqli_fetch_array($grp))
				{
				?>
				<option value="<?php echo $gr['group_id'];?>"><?php echo $gr['group_name'];?></option>
				<?php
				}
				?>
			</select>
			<div style="height:350px; overflow-x:hidden" id="laod_item">
				
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		get_id();
		load_item();
	});
	function charge_name(e)
	{
		var str=$("#charge_name").val();
		var nval=str.toUpperCase();
		$("#charge_name").val(nval);
		
		//~ str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
			//~ return letter.toUpperCase();
		//~ });
		//~ $("#charge_name").val(str);
	}
	var doc_v=1;
	var doc_sc=0;
	function sel_pr(val,e) ///for load patient
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			var chk=$("#chk").val();
			if(chk!="0")
			{
				var prod=document.getElementById("prod"+doc_v).innerHTML;
				val_load_new(prod);
			}
		}
		else if(unicode==40)
		{
			$("#chk").val("1");
			var chk=doc_v+1;
			var cc=document.getElementById("rad_test"+chk).innerHTML;
			if(cc)
			{
				doc_v=doc_v+1;
				$("#rad_test"+doc_v).css({'font-weight':'bold','color': 'green','transform':'scale(0.95)','transition':'all .2s'});
				var doc_v1=doc_v-1;
				$("#rad_test"+doc_v1).css({'font-weight':'normal','color': 'black','transform':'scale(1.0)','transition':'all .2s'});
				var z2=doc_v%3;
				if(z2==0)
				{
					$("#laod_item").scrollTop(doc_sc)
					doc_sc=doc_sc+90;
				}
			}
		}
		else if(unicode==38)
		{
			$("#chk").val("1");
			var chk=doc_v-1;
			var cc=document.getElementById("rad_test"+chk).innerHTML;
			if(cc)
			{
				doc_v=doc_v-1;
				$("#rad_test"+doc_v).css({'font-weight':'bold','color': 'green','transform':'scale(0.95)','transition':'all .2s'});
				var doc_v1=doc_v+1;
				$("#rad_test"+doc_v1).css({'font-weight':'normal','color': 'black','transform':'scale(1.0)','transition':'all .2s'});
				var z2=doc_v%3;
				if(z2==0)
				{
					doc_sc=doc_sc-90;					
					$("#laod_item").scrollTop(doc_sc)		
				}
			}
		}
		else
		{
			$.post("pages/charge_master_data.php",
			{
				val:val,
				branch_id:$("#branch_id").val(),
				cat:$("#category").val(),
				type:"charges_load",
			},
			function(data,status)
			{
				$("#laod_item").html(data);
			})
		}
	}
	function popitup(url)
	{
		var branch_id=$("#branch_id").val();
		var group_id=$("#category").val();
		url=url+"?bid="+btoa(branch_id)+"&gid="+btoa(group_id);
		newwindow=window.open(url,'window','left=10,top=10,height=800,witdh=600,menubar=1,resizeable=0,scrollbars=1');
	}
	function load_item()
	{
		$.post("pages/charge_master_data.php",
		{
			branch_id:$("#branch_id").val(),
			cat:$("#category").val(),
			type:"charges_load",
		},
		function(data,status)
		{
			$("#laod_item").html(data);
			clearr();
		})
	}
	function val_load_new(doid1)
	{
		$.post("pages/charge_master_data.php",
		{
			type:"charge_load",
			doid1:doid1,
		},
		function(data,status)
		{
			var val=data.split("@#$");
			document.getElementById("charge_id").value=val[0];
			document.getElementById("charge_name").value=val[1];
			document.getElementById("group_id").value=val[2];
			//document.getElementById("charge_type").value=val[3];
			document.getElementById("amount").value=val[4];
			if(val[6]==1)
			{
				$(".doc_link1").prop("checked", true);
			}else
			{
				$(".doc_link0").prop("checked", true);
			}
			
			if(val[5]=='141')
			{
				if(val[7])
				{
					$("#group_id").prop("disabled",true);
					$("#span_bed_no").html("<br><b>Bed No: "+val[7]+"</b>").fadeIn(500);
				}else
				{
					$("#group_id").prop("disabled",false);
					$("#span_bed_no").fadeOut(500);
				}
			}else
			{
				$("#group_id").prop("disabled",false);
				$("#span_bed_no").fadeOut(500);
			}
			
			document.getElementById("branch_id").value=val[8];
		 })
	}
	function clearr()
	{
		var a=document.getElementsByTagName("input");
		for(var i=0;i<a.length;i++)
		{
			if(a[i].type=="text")
			{
				if(i==0 )
				{
				}
				else
				{
					a[i].value="";
				}
			}
		}
		$("#charge_name").focus();
		$("#group_id").val('0');
		$("#charge_type").val('0');
		get_id();
	}
	function Insert() //For Insert
	{
		//alert($("#doc_link:checked").val());
		///////////////For Check blanj Field
		var jj=1;
		var chk=document.getElementsByClassName("imp")
		for(var i=0;i<chk.length;i++)
		{
			if(chk[i].value=="")
			{
				document.getElementById(chk[i].id).placeholder="Can not be blank";
				jj=0;	
			}
		}
		if(jj==1)
		{
			///////end ////////
			$.post("pages/charge_master_data.php",
			{
				type:"charges_save",
				branch_id:$("#branch_id").val(),
				charge_id:$("#charge_id").val(),
				charge_name:$("#charge_name").val(),
				group_id:$("#group_id").val(),
				amount:$("#amount").val(),
				user:$("#user").text().trim(),
				doc_link:$("#doc_link:checked").val(),
			},
			function(data,status)
			{
				bootbox.dialog({ message: "<H5>Saved</H5>"});
				setTimeout(function(){
					bootbox.hideAll();
					clearr();
					load_item();
				},1000);
			})
		}
	}
	function get_id()
	{
		$.post("pages/charge_master_data.php",
		{
			type:"charge_id",
		},
		function(data,status)
		{
			$("#charge_id").val(data);
		})
	}
	function delete_data(smplid)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to delete</h5>",
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
						$.post("pages/charge_master_data.php",
						{
							type:"charges_delete",
							smplid:smplid,
						},
						function(data,status)
						{
							bootbox.dialog({ message: "<h5>"+data+"</h5>"});
							setTimeout(function(){
								bootbox.hideAll();
								clearr();
								load_item();
							},1000);
						})
					}
				}
			}
		});
	}
</script>
