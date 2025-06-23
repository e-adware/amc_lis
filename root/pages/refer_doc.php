<script>
	$(document).on('keyup', ".capital", function () {
		$(this).val(function (_, val) {
			return val.toUpperCase();
		});
	});
	$(document).ready(function()
	{
		$("#name").keyup(function(e)
		{
			$(this).val($(this).val().toUpperCase());
			if($(this).val()=="" && e.keyCode==13)
			{
				$(this).css("border","1px solid #f00");
			}
			else
			{
				$(this).css("border","");
				if(e.keyCode==13)
				$("#quali").focus();
			}
		});
		$("#quali").keyup(function(e)
		{
			if(e.keyCode==13)
			$("#add").focus();
		});
		$("#add").keyup(function(e)
		{
			if(e.keyCode==13)
			$("#contact").focus();
		});
		$("#contact").keyup(function(e)
		{
			if(e.keyCode==13)
			$("#email").focus();
		});
		$("#email").keyup(function(e)
		{
			if(e.keyCode==13)
			$("#sav").focus();
		});
	});
	function ValidateEmail(email)
    {
        var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
        return expr.test(email);
	}
	function save()
	{
		if($("#name").val()=="")
		{
			$("#name").focus();
		}
		else
		{
			$("#save_tr").hide();
			$.post("pages/refer_doc_data.php",
			{
				id:$("#id").val().trim(),
				name:$("#name").val().trim(),
				quali:$("#quali").val().trim(),
				add:$("#add").val().trim(),
				contact:$("#contact").val().trim(),
				discount_prchnt:$("#discount_prchnt").val().trim(),
				executive_id:$("#executive_id").val().trim(),
				lcd_rate:$("#lcd_rate:checked").length,
				full_rate:$("#full_rate:checked").length,
				not_required:$("#not_required:checked").length,
				usr:$("#user").text().trim(),
				type:"save_refer_doc",
			},
			function(data,status)
			{
				bootbox.dialog({ message: "<h4>"+data+"</h4>"});
				setTimeout(function()
				{
					$("#save_tr").show();
					bootbox.hideAll();
					clrr();
				}, 1000);
			})
		}
	}
	function edt(i)
	{
		$.post("pages/refer_doc_data.php",
		{
			id:i,
			type:"edit_refer_doc",
		},
		function(data,status)
		{
			var vl=data.split("@govin@");
			$("#id").val(vl[0]);
			$("#name").val(vl[1]);
			$("#quali").val(vl[2]);
			$("#add").val(vl[3]);
			$("#contact").val(vl[4]);
			if(vl[5]==1)
			{
				$("#lcd_rate").prop("checked",true);
			}
			else
			{
				$("#lcd_rate").prop("checked",false);
			}
			if(vl[6]==1)
			{
				$("#full_rate").prop("checked",true);
			}
			else
			{
				$("#full_rate").prop("checked",false);
			}
			//$("#en_type").val(vl[7]);
			$("#discount_prchnt").val(vl[8]);
			if(vl[9]==1)
			{
				$("#not_required").prop("checked",true);
			}
			else
			{
				$("#not_required").prop("checked",false);
			}
			$("#sav").val('Update');
			$("#name").focus();
		})
	}
	function del(i)
	{
		$("#dl").click();
		$("#idl").val(i);
	}
	function delete_doc()
	{
		$.post("pages/refer_doc_data.php",
		{
			id:$("#idl").val(),
			type:"delete_refer_doc",
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
	function load_doc()
	{
		$("#loader").show();
		$.post("pages/refer_doc_data.php",
		{
			type:"load_refer_doc",
			srch:$("#srch").val(),
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})
	}
	function srch()
	{
		$("#loader").show();
		$.post("pages/refer_doc_data.php",
		{
			srch:$("#srch").val(),
			type:"load_refer_doc",
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})
	}
	function clrr()
	{
		$("#id").val('');
		$("#name").val('');
		$("#quali").val('');
		$("#add").val('');
		$("#contact").val('');
		$("#discount_prchnt").val('');
		$("#executive_id").val('0');
		$("#lcd_rate").prop("checked",false);
		$("#full_rate").prop("checked",false);
		$("#not_required").prop("checked",false);
		$("#srch").val('');
		$("#sav").val('Save');
		$("#name").focus();
		load_doc();
	}
	
	function merge_doctor_div()
	{
		$.post("pages/refer_doc_data.php",
		{
			type:"merge_doctor_div",
		},
		function(data,status)
		{
			$("#merge_doctor_btn").click();
			$("#load_data").html(data);
			$("#main_doc").select2({ theme: "classic" });
		})
	}
	function main_doc_change()
	{
		$.post("pages/refer_doc_data.php",
		{
			type:"load_duplicate_doc",
			main_doc:$("#main_doc").val(),
		},
		function(data,status)
		{
			$("#duplicate_doc").html(data);
			$("#duplicate_doc").select2({ theme: "classic" });
		})
	}
	function save_merge()
	{
		if($("#main_doc").val()==0)
		{
			alert("Select Main Doctor");
			$("#main_doc").focus();
			return false;
		}
		if(!$("#duplicate_doc").val())
		{
			alert("Select Duplicate Doctor(s)");
			$("#duplicate_doc").focus();
			return false;
		}
		
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to merge ?</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-primary",
					callback: function() {
						//alert("K");
						$.post("pages/refer_doc_data.php",
						{
							type:"save_merge",
							main_doc:$("#main_doc").val(),
							duplicate_doc:$("#duplicate_doc").val(),
						},
						function(data,status)
						{
							alert(data);
							$("#modal_close_btn").click();
							load_doc();
						})
					}
				}
			}
		});
	}
	function print_list()
	{
		var user = $("#user").text().trim();
		var srch=$("#srch").val();
		url = "pages/refer_doc_print.php?user="+btoa(user)+"&srch="+btoa(srch);
		
		wind = window.open(url, 'Window', 'scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
		
	}
</script>
<style>
.modal.fade.in
{
	top: 0%;
}
.modal
{
    z-index: 999 !important;
}
.modal-backdrop
{
	z-index: 990 !important;
}
.span3
{
	width: 30%;
}
.span6
{
	width: 60%;
}
</style>
<!-- Select2 Plugin -->
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span><br/>
    <b>Note:</b> ( <b style="color:#f00;">*</b> ) Required fields
    </div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span3" style="margin-left: 0;">
		<table class="table table-bordered table-condensed">
			<tr>
				<td>
					Name
					<input type="text" id="id" class="capital" style="display:none;" />
				</td>
				<td><input type="text" id="name" class="span3 capital" placeholder="Doctor Name" autofocus /> <b style="color:#f00;">*</b></td>
			</tr>
			<tr>
				<td>Qualification</td>
				<td><input type="text" id="quali" class="span3 capital" placeholder="Qualification" /></td>
			</tr>
			<tr>
				<td>Address</td>
				<td><input type="text" id="add" class="span3 capital" placeholder="Address" /></td>
			</tr>
			<tr>
				<td>Contact Number</td>
				<td><input type="text" id="contact" class="span3" maxlength="10" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" placeholder="Contact Number" /></td>
			</tr>
			<tr>
				<td>Discount</td>
				<td><input type="text" id="discount_prchnt" class="span3" maxlength="3" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" placeholder="Discount %" />%</td>
			</tr>
			<tr>
				<td>Select Executive</td>
				<td>
					<select id="executive_id">
						<option value="0">Select</option>
				<?php
					$qry=mysqli_query($link, "SELECT `emp_id`,`name` FROM `employee` WHERE `levelid`=31");
					while($data=mysqli_fetch_array($qry))
					{
						echo "<option value='$data[emp_id]'>$data[name]</option>";
					}
				?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<label name="lcd_rate" style="display:inline;"><input type="checkbox" id="lcd_rate"> LCD Rate</label>
					<label name="full_rate" style="display:inline;"><input type="checkbox" id="full_rate"> Full Rate</label>
					<label name="not_required" style="display:inline;"><input type="checkbox" id="not_required"> Not Require</label>
				</td>
			</tr>
			<tr id="save_tr">
				<td colspan="2" style="text-align:center;">
					<button class="btn btn-save" id="sav" onclick="save()" value="Save"><i class="icon-save"></i> Save</button>
					<button class="btn btn-reset" id="reset"  onclick="clrr()" value="Reset"><i class="icon-refresh"></i> Reset</button>
					<button class="btn btn-new" onclick="merge_doctor_div()"><i class="icon-group"></i> Merge Doctor</button>
					<button class="btn btn-print" onclick="print_list()"><i class="icon-print"></i> Print</button>
				</td>
			</tr>
		</table>
	</div>
	<div class="span6">
		<b>Search</b> <input type="text" id="srch" onkeyup="srch()" class="span4" placeholder="Search..." />
		<div id="res" style="max-height:400px;overflow-y:scroll;">
		
		</div>
	</div>
	<script>load_doc();</script>
	<!--modal-->
		<a href="#myAlert" data-toggle="modal" id="dl" class="btn" style="display:none;">A</a>
		<div id="myAlert" class="modal hide">
		  <div class="modal-body">
			  <input type="text" id="idl" style="display:none;" />
			<p>Are You Sure Want To Delete...?</p>
		  </div>
		  <div class="modal-footer">
			  <a data-dismiss="modal" onclick="clrr()" class="btn btn-back" href="#"><i class="icon-remove"></i> Cancel</a>
			<a data-dismiss="modal" onclick="delete_doc()" class="btn btn-delete" href="#"><i class="icon-ok"></i> Confirm</a>
		  </div>
		</div>
		
		<button type="button" class="btn btn-info" id="merge_doctor_btn" data-toggle="modal" data-target="#myModal" style="display:none;">Open Modal</button>
		<div id="myModal" class="modal fade" role="dialog">
			<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Merge Duplicate Doctors</h4>
					</div>
					<div class="modal-body" id="load_data">
					</div>
					<div class="modal-footer" style="display:none;">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
	  <!--modal end-->
	  
	  <!-- Loader -->
	<div id="loader" style="margin-top:-10%;"></div>
	<link rel="stylesheet" href="../css/loader.css" />
</div>
