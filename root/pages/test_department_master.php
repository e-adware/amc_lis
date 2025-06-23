<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="container-fluid">
	<div class="row">
		<div class="span7">
			<table class="table   table-bordered table-condensed">
				<tr>
					<th>Department Category</th>
					<td>
						<select class="form-control" name="cat_id" id="cat_id" required>
						<?php
							$cat_qry=mysqli_query($link, "SELECT `category_id`, `name` FROM `test_category_master` WHERE `status`='0' ORDER BY `category_id` ASC");
							while($cat=mysqli_fetch_array($cat_qry))
							{
								echo "<option value='$cat[category_id]'>$cat[name]</option>";
							}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<th>Department Name</th>
					<td>
						<input type="hidden" class="form-control" name="dept_id" id="dept_id"/>
						<input type="text" class="form-control span4" name="dept_name" id="dept_name" autofocus />
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center" >
						<input type="button" class="btn btn-info" name="save" id="save" value="Save" onclick="save()" />
						<input type="button" class="btn btn-warning" name="save" id="save" value="Reset" onclick="reset()" />
					</td>
				</tr>
			</table>
		</div>
		<div class="span4">
			<table  class="table   table-bordered table-condensed"  >
				<tr>
				   <td>Name</td>
				   <td colspan="2"><input type="text" id="txtdoc" size="35" onkeyup="sel_pr(this.value,event)"/></td>
				</tr>
			</table>
			<div style="height:350px; overflow-x:hidden" id="load_data"> 
			</div> 
		</div>
	</div>
</div>
</div>
<script>
	$(document).ready(function(){
		load_data();
	});
	function caps_it(val,id,e)
	{
		var nval=val.toUpperCase();
		$("#"+id).val(nval);
		var n=val.length;
		if(n>0)
		{
			var numex=/^[A-Za-z0-9 ]+$/;
			if(val[n-1].match(numex))
			{
				
			}
			else
			{
				val=val.slice(0,n-1);
				$("#"+id).val(val);
			}
		}
		
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			if(id=="dept_name")
			{
				$("#prefix").focus();
			}
			if(id=="prefix")
			{
				$("#save").focus();
			}
		}
	}
	function reset()
	{
		$("#dept_id").val("0");
		$("#dept_name").val("").focus();
		$("#cat_id").val("0");
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
				$("#rad_test"+doc_v).css({'font-weight':'bold','color': 'red','transform':'scale(0.95)','transition':'all .2s'});
				var doc_v1=doc_v-1;
				$("#rad_test"+doc_v1).css({'font-weight':'normal','color': 'black','transform':'scale(1.0)','transition':'all .2s'});
				var z2=doc_v%3;
				if(z2==0)
				{
					$("#load_data").scrollTop(doc_sc)
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
				$("#rad_test"+doc_v).css({'font-weight':'bold','color': 'red','transform':'scale(0.95)','transition':'all .2s'});
				var doc_v1=doc_v+1;
				$("#rad_test"+doc_v1).css({'font-weight':'normal','color': 'black','transform':'scale(1.0)','transition':'all .2s'});
				var z2=doc_v%3;
				if(z2==0)
				{
					doc_sc=doc_sc-90;					
					$("#load_data").scrollTop(doc_sc);
				}
			}
		}
		else
		{
			$.post("pages/test_department_master_data.php",
			{
				val:val,
				type:"load_all_types",
			},
			function(data,status)
			{
				$("#load_data").html(data);
			})
		}
	}
	
	function load_data()
	{
		$.post("pages/test_department_master_data.php",
		{
			type:"load_all_types",
		},
		function(data,status)
		{
			$("#load_data").html(data)
			reset();
		})
	}
	function save()
	{
		if($("#dept_name").val()=="")
		{
			$("#dept_name").focus();
			return false;
		}
		if($("#cat_id").val()=="")
		{
			$("#cat_id").focus();
			alert("Please Select A Department");
			return false;
		}
		$.post("pages/test_department_master_data.php",
		{
			type:"save_type",
			cat_id:$("#cat_id").val(),
			dept_name:$("#dept_name").val(),
			dept_id:$("#dept_id").val(),
		},
		function(data,status)
		{
			alert(data);
			load_data();
		})
	}
	function val_load_new(deptid)
	{
		$.post("pages/test_department_master_data.php",
		{
			type:"load_single_type",
			deptid:deptid,
		},
		function(data,status)
		{
			var val=data.split("#");
			$("#dept_id").val(val[0]);
			$("#dept_name").val(val[1]).focus();
			$("#cat_id").val(val[2]);
		})
	}

	function delete_data(subp)//for delete
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to delete this</h5>",
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
						$.post("pages/doctor_department_master_data.php",
						{
							subp:subp,
							type:"delete_type",
						},
						function(data,status)
						{
							load_data();
						})
					}
				}
			}
		});
	}

</script>
<style>
/*.reference{
	width:700px;}
.reference td{
	padding:5px;
	text-align:center;
	height:5px;
	width:auto;
	min-width:100px;}
	*/
.reference td img{
	margin:0;}
</style>
