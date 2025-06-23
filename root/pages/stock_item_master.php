<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span>
    <span style="float:right;">
	<input type="button" id="new" value="Create New" class="btn btn-success" onClick="load_item_info('0')"/>
    <button class="btn btn-primary" onClick="show_test_list()">Print Item List</button>
    <button class="btn btn-info" onClick="export_test_list()">Export Item List</button>
    </span>
</div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered table-condensed">
		<tr>
			<!--<td>
				Search:<input type="text" id="tname" name="tname" onKeyUp="load_test(this.value,event)" />
			</td>-->
			<td>
				<select id="category_id" onchange="load_all_item()">
					<option value="0">Select Category</option>
					<?php
						$stock_category_qry=mysqli_query($link, " SELECT * FROM `stock_category_master` ORDER BY `category_name` ");
						while($stock_category=mysqli_fetch_array($stock_category_qry))
						{
							echo "<option value='$stock_category[category_id]'>$stock_category[category_name]</option>";								
						}
					?>
				</select>
			</td>
			<td>
				<select id="sub_category_id" onchange="load_all_item()">
					<option value="0">Select Sub Category</option>
					<?php
						$stock_sub_category_qry=mysqli_query($link, " SELECT * FROM `stock_sub_category_master` ORDER BY `sub_category_name` ");
						while($stock_sub_category=mysqli_fetch_array($stock_sub_category_qry))
						{
							echo "<option value='$stock_sub_category[sub_category_id]'>$stock_sub_category[sub_category_name]</option>";								
						}
					?>
				</select>
			</td>
			<td>
				<select id="item_type_id" onchange="load_all_item()">
					<option value="0">Select Product Type</option>
					<?php
						$item_type_qry=mysqli_query($link, " SELECT * FROM `item_type_master` ORDER BY `item_type_name` ");
						while($item_type=mysqli_fetch_array($item_type_qry))
						{
							echo "<option value='$item_type[item_type_id]'>$item_type[item_type_name]</option>";								
						}
					?>
				</select>
			</td>
			<td>
				<select id="manufacturer_id" onchange="load_all_item()">
					<option value="0">Select Product Type</option>
					<?php
						$manufacturer_qry=mysqli_query($link, " SELECT * FROM `manufacturer_company` ORDER BY `manufacturer_name` ");
						while($manufacturer=mysqli_fetch_array($manufacturer_qry))
						{
							echo "<option value='$manufacturer[manufacturer_id]'>$manufacturer[manufacturer_name]</option>";								
						}
					?>
				</select>
			</td>
		</tr>
	</table>
	<span class="text-right">
		<input type="text" id="search_item" placeholder="Search Item" onkeyup="load_all_item(this.value)">
	</span>
	<div id="load_all_data">
		
	</div>
	
	<div id="back" onClick="$('#results').slideUp(500);$(this).fadeOut(100);$('#pinfo').fadeOut(200)"></div>
		
	<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none"/>
	
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div id="results"> </div>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="loader" style="margin-top:-10%;"></div>
<script src="../js/jquery.dataTables.min_all.js"></script>
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<script>
	$(document).ready(function(){
		load_all_item('');
	});
	
	function load_all_item(val)
	{
		$("#loader").show();
		$.post("pages/stock_item_master_data.php",
		{
			type:"load_all_item",
			category_id:$("#category_id").val(),
			sub_category_id:$("#sub_category_id").val(),
			item_type_id:$("#item_type_id").val(),
			manufacturer_id:$("#manufacturer_id").val(),
			user:$("#user").text().trim(),
			level:$("#lavel_id").val(),
			val:val,
		},
		function(data,status)
		{
			$("#load_all_data").html(data);
			$('.data-table').dataTable({
				"bJQueryUI": true,
				"sPaginationType": "full_numbers",
				"sDom": '<""l>t<"F"fp>'
			});
			$("#loader").hide();
			$(".dataTables_filter").hide();
		})
	}
	
	function load_item_info(item_id)
	{
		$.post("pages/stock_item_master_data.php",
		{
			type:"load_item",
			item_id:item_id,
		},
		function(data,status)
		{
			$("#results").html(data);
			$("#mod").click();
			$("#results").fadeIn(500,function(){ $("#results").animate({scrollTop:0}, '500'); })
		})
	}
	function save_item(item_id)
	{
		if($("#item_require").prop("checked"))
		{
			var item_require=1;
		}else
		{
			var item_require=0;
		}
		var error=0;
		if($("#item_name").val()=="")
		{
			$("#item_name").focus();
			return false;
			error=1;
		}
		if($("#category_id_modal").val()=="0")
		{
			error=1;
			$("#category_id_modal").focus();
			return false;
		}
		if($("#sub_category_id_modal").val()=="0")
		{
			error=1;
			$("#sub_category_id_modal").focus();
			return false;
		}
		if($("#gst").val()=="")
		{
			$("#gst").focus();
			return false;
			error=1;
		}
		
		if(error==0)
		{
			$("#sav").attr("disabled",true);
			bootbox.dialog({ message: "<b>Saving...</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> "});
			$.post("pages/stock_item_master_data.php",
			{
				type:"save_item",
				item_id:item_id,
				item_name:$("#item_name").val(),
				short_name:$("#short_name").val(),
				generic_name:$("#generic_name").val(),
				category_id:$("#category_id_modal").val(),
				sub_category_id:$("#sub_category_id_modal").val(),
				item_type_id:$("#item_type_id_modal").val(),
				manufacturer_id:$("#manufacturer_id_modal").val(),
				mrp:$("#mrp").val(),
				gst:$("#gst").val(),
				strength:$("#strength").val(),
				strip_quantity:$("#strip_quantity").val(),
				unit:$("#unit").val(),
				re_order:$("#re_order").val(),
				no_of_test:$("#no_of_test").val().trim(),
				rack_no:$("#rack_no").val(),
				specific_type:$("#specific_type").val(),
				hsn_code:$("#hsn_code").val(),
				user:$("#user").text().trim(),
				item_require:item_require,
			},
			function(data,status)
			{
				//alert(data);
				//bootbox.hideAll();
				//bootbox.dialog({ message: data});
				setTimeout(function(){
					bootbox.hideAll();
					$("#modal_btn_close").click();
				},1000);
				setTimeout(function(){
					bootbox.hideAll();
					load_all_item();
				},2000);
			})
		}else
		{
			bootbox.dialog({ message: "Error ! Please Try Again Later."});
			setTimeout(function(){
				bootbox.hideAll();
			},1000);
		}
	}
	function category_change(category_id)
	{
		$.post("pages/stock_item_master_data.php",
		{
			type:"load_sub_category",
			category_id:category_id,
		},
		function(data,status)
		{
			$("#sub_category_id_modal").html(data);
		})
	}
	function delete_item(item_id)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to delete this Item ?</h5>",
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
						bootbox.dialog({ message: "<b>Deleting...</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> "});
						$.post("pages/stock_item_master_data.php",
						{
							type:"delete_item",
							item_id:item_id,
						},
						function(data,status)
						{
							setTimeout(function(){
								bootbox.hideAll();
							},1000);
							setTimeout(function(){
								bootbox.hideAll();
								load_all_item();
							},2000);
						})
					}
				}
			}
		});
	}
	function caps_it(id,val)
	{
		var nval=val.toUpperCase();
		$("#"+id).val(nval);
	}
	function export_test_list()
	{
		window.location="pages/stock_item_list_xls.php";
	}
	function show_test_list()
	{
		var category_id=$("#category_id").val();
		var sub_category_id=$("#sub_category_id").val();
		var item_type_id=$("#item_type_id").val();
		var manufacturer_id=$("#manufacturer_id").val();
		var user=$("#user").text().trim();
		
		url="pages/stock_item_list_print.php?cat="+category_id+"&sub_cat="+sub_category_id+"&item_type="+item_type_id+"&manufacturer="+manufacturer_id+"&user="+user;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function chk_num(id,val)
	{
		if(/\D/g.test(val))
		{
			val=val.replace(/\D/g,'');
			$("#"+id).val(val);
		}
	}
</script>
<style>
#results
{
	max-height:540px;
	overflow-y:scroll;
}
#myModal
{
	left: 23%;
	width:95%;
}
.modal.fade.in
{
	top: 3%;
}
.modal-body
{
	max-height: 540px;
}
.dataTables_filter {
    color: #878787;
    font-size: 11px;
    right: 1%;
    top: 15%;
    margin: 4px 8px 2px 10px;
    position: absolute;
    text-align: left;
}
.dataTables_filter
{
	top: 21%;
}
#DataTables_Table_0_filter
{
	display:none;
}
</style>