<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Supplier Rate</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span6" style="margin-left: 0;">
		<table class="table table-bordered table-condensed">
			<tr>
				<td>Select Supplier</td>
				<td>
					<select id="ind_supp" onchange="set_btn()" autofocus>
						<option value="0">Select</option>
						<?php
						$q=mysqli_query($link,"SELECT * FROM `inv_supplier_master` ORDER BY `name`");
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
				<td>Item</td>
				<td>
					<input type="text" id="iid" style="display:none;" />
					<input type="text" id="item" class="span4" placeholder="Item" readonly="readonly" />
				</td>
			</tr>
			<tr>
				<td>Rate</td>
				<td><input type="text" id="rate" class="span4" onkeyup="tab(this.id,event)" placeholder="Rate" /></td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center;">
					<input type="button" id="sav" class="btn btn-info" onclick="save()" value="Save" />
					<input type="button" id="" class="btn btn-danger" onclick="clrr();$('#ind_supp').val('0');" value="Reset" />
					<input type="button" id="vew" class="btn btn-success" onclick="view()" value="View" disabled="disabled" />
				</td>
			</tr>
		</table>
	</div>
	<div class="span5">
		<b>Search</b> <input type="text" id="srch" onkeyup="load_item()" class="span4" placeholder="Search..." />
		<div id="res" style="max-height:400px;overflow-y:scroll;">
		
		</div>
	</div>
	<!--modal-->
		<a href="#myAlert" data-toggle="modal" id="dl" class="btn" style="display:none;">A</a>
		<div id="myAlert" class="modal fade">
		  <div class="modal-body">
			  <input type="text" id="idl" style="display:none;" />
			<p>Are You Sure Want To Delete...?</p>
		  </div>
		  <div class="modal-footer">
			<a data-dismiss="modal" onclick="dell()" class="btn btn-primary" href="#">Confirm</a>
			<a data-dismiss="modal" onclick="clrr()" class="btn btn-info" href="#">Cancel</a>
		  </div>
		</div>
	  <!--modal end-->
</div>
<script>
	$(document).ready(function()
	{
		load_item();
	});
	
	function load_item()
	{
		$.post("pages/global_load_g.php",
		{
			srch:$("#srch").val(),
			type:"load_inv_items",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function sel(id,nm)
	{
		$("#iid").val(id);
		$("#item").val(nm);
		$("#item").css("border","");
		$("#rate").focus();
	}
	function save()
	{
		if($("#ind_supp").val()=="0")
		{
			$("#ind_supp").focus();
			$("#ind_supp").css("border","1px solid #e00");
		}
		else if($("#item").val()=="")
		{
			$("#item").css("border","1px solid #e00");
		}
		else if($("#rate").val()=="")
		{
			$("#rate").focus();
			$("#rate").css("border","1px solid #e00");
		}
		else
		{
			$.post("pages/global_insert_data_g.php",
			{
				ind_supp:$("#ind_supp").val(),
				iid:$("#iid").val(),
				rate:$("#rate").val(),
				type:"save_inv_supp_rate",
			},
			function(data,status)
			{
				clrr();
			})
		}
	}
	function tab(id,e)
	{
		if(e.keyCode==13)
		{
			if(id=="rate")
			$("#sav").focus();
		}
	}
	function set_btn()
	{
		if($("#ind_supp").val()!="0")
		{
			$("#vew").attr('disabled',false);
			$("#ind_supp").css("border","");
		}
		else
		$("#vew").attr('disabled',true);
	}
	function view()
	{
		var supp=btoa($("#ind_supp").val());
		url="pages/supplier_item_prev.php?supplier="+supp;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function clrr()
	{
		$("input").css("border","");
		$("select").css("border","");
		//$("#ind_supp").val('0');
		$("#iid").val('');
		$("#item").val('');
		$("#rate").val('');
		$("#srch").val('');
		$("#vew").attr('disabled',true);
		$("#ind_supp").focus();
		load_item();
		set_btn();
	}
</script>
<style>
	.nm:hover
	{
		color:#0000ff;
		cursor:pointer;
	}
</style>
