<script>
	function load_head(h)
	{
		if(h==0)
		{
			$("#upd").slideUp();
		}
		else
		{
			$("#upd").stop().slideUp(100);
			$.post("pages/global_load_g.php",
			{
				type:"load_header",
				hid:h,
			},
			function(data,status)
			{
				$("#upd").slideDown();
				var vl=data.split("@gov@");
				$("#hid").val(vl[0]);
				$("#uhd").val(vl[1]);
				$("#useq").val(vl[2]);
			})
		}
	}
	function updat()
	{
		$.post("pages/global_insert_data_g.php",
		{
			type:"update_header",
			hid:$("#hid").val(),
			seq:$("#useq").val(),
			hname:$("#uhd").val(),
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
	function save_head()
	{
		if($("#hname").val()=="")
		{
			$("#hname").focus();
		}
		else if($("#seq").val()=="")
		{
			$("#seq").focus();
		}
		else
		{
			$.post("pages/global_insert_data_g.php",
			{
				type:"save_header",
				seq:$("#seq").val(),
				hname:$("#hname").val(),
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
	function hide_div()
	{
		$("#u_head").val('0');
		$("#upd").slideUp(500);
	}
</script>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Header Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="widget-box">
          <div class="widget-title">
            <ul class="nav nav-tabs">
              <li class="active" onclick="hide_div()"><a data-toggle="tab" href="#tab1">New Header</a></li>
              <li><a data-toggle="tab" href="#tab2">Update Header</a></li>
            </ul>
          </div>
          <div class="widget-content tab-content">
            <div id="tab1" class="tab-pane active">
              <table class="table table-bordered table-condensed">
					<tr>
						<th class="span2">Header Name</th>
						<td><input type="text" id="hname" name="hname" /></td>
					</tr>
					<tr>
						<th>Sequence</th>
						<td><input type="text" id="seq" name="seq"/></td>
					</tr>
					<tr>
						<th colspan="2" style="text-align:center">
						<input type="button" id="save" name="save" class="btn btn-info" value="Save" onclick="save_head()"/>
						</th>
					</tr>
				</table>
            </div>
            <div id="tab2" class="tab-pane">
              Select Header: 
				<select id="u_head" onchange="load_head(this.value)">
				<option value="0">-Select-</option>
				<?php
				$hd=mysqli_query($link, "select * from menu_header_master order by name");
				while($h=mysqli_fetch_array($hd))
				{
				echo "<option value='$h[id]'>$h[name]</option>";	
				}
				?>
				</select>
			  <div id="upd" style="display:none;">
				<table class="table table-condensed" style="max-width:50%;">
					<tr>
						<td>Name <input type="text" id="hid" style="display:none;" /></td>
						<td><input type="text" id="uhd" class="form-control" /></td>
					</tr>
					<tr>
						<td>Sequence</td>
						<td><input type="text" id="useq" class="form-control" /></td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center"><input type="button" id="upd" class="btn btn-info" onclick="updat()" value="Update" /></td>
					</tr>
				</table>
			  </div>
            </div>
          </div>
        </div>
</div>
