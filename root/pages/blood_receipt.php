<?php
$id=base64_decode($_GET['uhid']);
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Blood receipt</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<span class="text-right"><input type="button" class="btn btn-primary" onclick="go()" value="Back to list" /></span>
	<div class="span6" style="margin-left:0px;">
		<table class="table table-bordered table-condensed" >
			<tr>
				<td>Donor</td>
				<td>
					<select id="donor" disabled="disabled">
						<option value="0">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT `donor_id`,`name` FROM `blood_donor_reg` ORDER BY `name`");
						while($r=mysqli_fetch_array($q))
						{
						?>
						<option value="<?php echo $r['donor_id'];?>" <?php if($id==$r['donor_id']){echo "selected='selected'";}?>><?php echo $r['name'];?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Bag type</td>
				<td>
					<select id="bag" autofocus>
						<option value="0">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT * FROM `blood_pack_master`");
						while($r=mysqli_fetch_array($q))
						{
						?>
						<option value="<?php echo $r['pack_id'];?>"><?php echo $r['name'];?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>volume</td>
				<td>
					<input type="text" class="form-control" id="vol" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" autocomplete="off" />
				</td>
			</tr>
			<tr>
				<td>Barcode No</td>
				<td>
					<input type="text" class="form-control" id="bar" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" autocomplete="off" />
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center">
					<input type="button" name="button2" id="button2" value="Reset" onclick="clrr()" class="btn btn-danger" style="width:100px"/> 
					<input type="button" name="intext7" id="button" value= "Submit" onclick="save()" class="btn btn-default btn-info" style="width:100px"/>
				</td>
			</tr>
		</table>
	</div>
</div>
<script>
	$(document).ready(function()
	{
		$("#donor").keyup(function(e)
		{
			$(this).css("border","");
			//$(this).val($(this).val().toUpperCase());
			if(e.keyCode==13)
			{
				if($(this).val()=="0")
				$(this).css("border","1px solid #f00");
				else
				{
					$(this).css("border","");
					$("#bag").focus();
				}
			}
		});
		$("#bag").keyup(function(e)
		{
			$(this).css("border","");
			//$(this).val($(this).val().toUpperCase());
			if(e.keyCode==13)
			{
				if($(this).val()=="0")
				$(this).css("border","1px solid #f00");
				else
				{
					$(this).css("border","");
					$("#vol").focus();
				}
			}
		});
		$("#vol").keyup(function(e)
		{
			$(this).css("border","");
			//$(this).val($(this).val().toUpperCase());
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				$(this).css("border","1px solid #f00");
				else
				{
					$(this).css("border","");
					$("#bar").focus();
				}
			}
		});
		$("#bar").keyup(function(e)
		{
			$(this).css("border","");
			//$(this).val($(this).val().toUpperCase());
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				$(this).css("border","1px solid #f00");
				else
				{
					$(this).css("border","");
					$("#button").focus();
				}
			}
		});
	});
	function save()
	{
		if($("#donor").val()=="0")
		{
			$("#donor").focus();
		}
		else if($("#bag").val()=="0")
		{
			$("#bag").focus();
		}
		else if($("#vol").val()=="")
		{
			$("#vol").focus();
		}
		else if($("#bar").val()=="")
		{
			$("#bar").focus();
		}
		else
		{
			$.post("pages/global_insert_data_g.php",
			{
				donor:$("#donor").val(),
				bag:$("#bag").val(),
				vol:$("#vol").val(),
				bar:$("#bar").val(),
				usr:$("#user").text().trim(),
				type:"blood_receipt_save",
			},
			function(data,status)
			{
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					window.location="processing.php?param=195";
				}, 1000);
			})
		}
	}
	function go()
	{
		window.location="processing.php?param=195";
	}
	function clrr()
	{
		$("#donor").val('0');
		$("#bag").val('0');
		$("#vol").val('');
		$("#bar").val('');
		$("#donor").focus();
	}
</script>
