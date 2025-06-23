<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span5">
		<br>
		<b>Select Table</b>
		<select id="db_table" class="span4">
			<option value="0">Select</option>
			<?php
			$db_qry=mysqli_query($link," SELECT table_name, engine FROM information_schema.tables WHERE table_type = 'BASE TABLE' AND table_schema='$db_name' ORDER BY table_name ASC; ");
			while($db=mysqli_fetch_array($db_qry))
			{
			?>
			<option value="<?php echo $db['table_name'];?>"><?php echo $db['table_name'];?></option>
			<?php
			}
			?>
		</select>
		<div id="load_data" style="margin-top: 5%;"></div>
	</div>
	<div class="span6">
		<div id="load_tables"></div>
	</div>
</div>
<link rel="stylesheet" href="../css/select2.min.css" />
<script src="../js/select2.min.js"></script>
<script src="../js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
	$(document).ready(function()
	{
		load_table();
		$("#db_table").select2({ theme: "classic" });
		$("#db_table").on("select2:close",function(e)
		{
			//alert($("#db_table").val());
			//setTimeout(function(){$("#pat_typ").focus();},200);
			$.post("pages/data_backup_setup_data.php",
			{
				db_table:$("#db_table").val(),
				type:"load_save_field",
			},
			function(data,status)
			{
				var val=data.split("@");
				if(val[0]=='404')
				{
					//$("#load_data").html("<b>This table is already added</b>");
					edit_table(val[1]);
				}else
				{
					$("#load_data").html(data);
				}
			})
		});
	});
	function save_table()
	{
		//alert($("#table_name").text().trim());
		var chk=$("#date_check:checked").length;
		if(chk>0)
		{
			var date_check=1;
		}else
		{
			var date_check=0;
		}
		$.post("pages/data_backup_setup_data.php",
		{
			db_table:$("#table_name").text().trim(),
			date_check:date_check,
			type:"save_table",
		},
		function(data,status)
		{
			alert(data);
			$("#db_table").val("0").trigger("change");
			$("#load_data").html("");
			load_table();
		})
	}
	function load_table()
	{
		$.post("pages/data_backup_setup_data.php",
		{
			type:"load_table",
		},
		function(data,status)
		{
			$("#load_tables").html(data);
			$('.data-table').dataTable({
				"bJQueryUI": true,
				"sPaginationType": "full_numbers",
				"sDom": '<""l>t<"F"fp>'
			});
		})
	}
	function delete_table(slno)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to remove this table</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Remove',
					className: "btn btn-danger",
					callback: function() {
						$.post("pages/data_backup_setup_data.php",
						{
							slno:slno,
							type:"remove_table"
						},
						function(data,status)
						{
							load_table();
						})
					}
				}
			}
		});
	}
	function edit_table(slno)
	{
		$.post("pages/data_backup_setup_data.php",
		{
			slno:slno,
			type:"edit_table"
		},
		function(data,status)
		{
			$("#load_data").html(data);
		})
	}
	
	function print_page()
	{
		url="pages/data_backup_tables_print.php?v="+btoa(0);
		window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
</script>
<style>
.container-fluid
{
	padding-left: 0;
}
.dataTables_length
{
	display: none;
}
.dataTables_filter input
{
	padding:0;
}
</style>
