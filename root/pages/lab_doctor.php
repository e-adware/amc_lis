<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="row">
		<div class="span5">
			<table class="table table-striped table-condensed table-bordered">
				<tr>
					<th>Category</th>
					<td>
						<select name="selectcat" id="selectcat" class="span3">
					<?php
						$qry=mysqli_query($link, "SELECT `category_id`, `name` FROM `test_category_master` WHERE `category_id`>0 ORDER BY `category_id` ASC");
						while($data=mysqli_fetch_array($qry))
						{
							echo "<option value='$data[category_id]'>$data[name]</option>";
						}
					?>
						</select>
					</td>
				</tr>
				<tr>
					<th>ID</th>
					<td><input type="text" name="txtid" id="txtid" readonly="readonly" class="imp imp1 span3"  /></td>
				</tr>
				<tr>
					<th>Sequence</th>
					<td><input type="text" name="txtseq" id="txtseq" class="imp imp1 span3"  /></td>
				</tr>
				<tr>
					<th>Doctor Name</th>
					<td><input type="text" name="txtname" id="txtname" class="imp imp1 span3" autocomplete="off" disabled  />
					</td>
				</tr>
				<tr>
					<th>Designation</th>
					<td><input type="text" name="txtdesig" id="txtdesig" class="imp imp1 span3" autocomplete="off" disabled />
					</td>
				</tr>
				<tr>
					<th>Qualification</th>
					<td><input type="text" name="txtqlfction" id="txtqlfction" class="imp imp1 span3" autocomplete="off" disabled />
					</td>
				</tr>
				<tr>
					<th>Phone</th>
					<td><input type="text" name="txtphone" id="txtphone" class="span3" value="" autocomplete="off" disabled />
					</td>
				</tr>
				<tr>
					<th>Password</th>
					<td><input type="password" id="txtpswrd" name="txtpswrd" class="imp imp1 span3" /></td>
				</tr>
				<tr>
					<td colspan="2" class="text-center">
						<center>
							<input type="button" name="button" id="button" value="Submit" onclick="Insert();" class="btn btn-info" />
							<input type="button" name="button2" id="button2" value="Refresh" onclick="clearr();" class="btn btn-warning" />
						</center>
					</td>
				</tr>
			</table>
		</div>
		<div class="span6">
			<div class="fancyScroll" id="loaddoc"></div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		Get_id();
		doc_load();
	});
	function clearr()
	{
		var b=document.getElementsByClassName("imp1");
		for(var j=0;j<b.length;j++)
		{
			b[j].value="";
		}
		Get_id();
	}
	function Get_id()///  for get Id
	{
		$.post("pages/load_id.php",
		{
			type:"labdoctor",
		},
		function(data,status)
		{
			$("#txtid").val(data);
		})
	}
	function doc_load() //For load Doctor
	{
		$.post("pages/global_load.php",
		{
			type:"labdoctor",
		},
		function(data,status)
		{
			$("#loaddoc").html(data);
		})
	}
	function Insert() //For Insert
	{
		///////For Check Blank fields//
		var jj=1;
		var b=$(".imp");
		for(var i=0;i<b.length;i++)
		{
			if(b[i].value=="")
			{
				jj=0;
				document.getElementById(b[i].id).placeholder="Can not be blank";
				b[i].focus();
				exit;
			}
		}
		if(jj==1)
		{
		///////end ////////
			$.post("pages/global_insert_data.php",
			{
				type:"labdoctor",
				cat:$("#selectcat").val(),
				docid:$("#txtid").val(),
				seq:$("#txtseq").val(),
				name:$("#txtname").val(),
				desig:$("#txtdesig").val(),
				qlfction:$("#txtqlfction").val(),
				phn:$("#txtphone").val(),
				paswrd:$("#txtpswrd").val(),
				user:$("#user").text(),
			},
			function(data,status)
			{
				bootbox.alert("Data Saved");
				clearr();
				doc_load();
			})
		}
	}
	function val_load_new(docid)  ///For load Consultant doctor Details
	{
		$.post("pages/global_load.php",
		{
			docid:docid,
			type:"labdoctor_load",
		},
		function(data,status)
		{
			var val=data.split("@");
			document.getElementById("txtid").value=val[0];
			document.getElementById("txtseq").value=val[1];
			document.getElementById("selectcat").value=val[2];
			document.getElementById("txtname").value=val[3];
			document.getElementById("txtdesig").value=val[4];
			document.getElementById("txtqlfction").value=val[5];
			document.getElementById("txtphone").value=val[6];			
			document.getElementById("txtpswrd").value=val[7];			
		})
	}
	function delete_data(subp)//For Delete
	{
		$.post("pages/global_delete.php",
		{
			subp:subp,
			type:"labdoctor_del",
		},
		function(data,status)
		{
			doc_load();
			clearr();	
		})
	}
</script>
