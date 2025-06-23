<script>
	function load_emp()
	{
		$.post("pages/user_list_ajax.php",
		{
			srch:$("#srch").val(),
			doc_speclty:$("#doc_speclty").val(),
			type:"load_doctor",
		},
		function(data,status)
		{
			$("#res").html(data);
		})
	}
	function goedit(id)
	{
		var levelid=$("#lavel_id").val();
		if(levelid)
		{
			window.location="processing.php?param=6&user="+id;
		}
	}
	function print_page()
	{
		url="pages/print_doctor_list.php";
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function clrr()
	{
		$("#id").val('');
		$("#name").val('');
		$("#dob").val('');
		$("#sex").val('0').change();
		$("#email").val('');
		$("#special").val('');
		$("#quali").val('');
		$("#desig").val('');
		$("#reg").val('');
		$("#sav").val('Update');
		$("#srch").val('');
		$("#name").focus();
		$("#sav").val('Save');
		load_emp();
	}
</script>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div>
		<b>Search</b> <input type="text" id="srch" onkeyup="load_emp()" class="span4" placeholder="Search..." autofocus />
		<b>Speciality</b>
		<select id="doc_speclty" onChange="load_emp()">
			<option value="0">Select</option>
			<?php
				$spclt_qry=mysqli_query($link," SELECT * FROM `doctor_specialist_list` order by `name` ");
				while($spclt=mysqli_fetch_array($spclt_qry))
				{
					echo "<option value='$spclt[speciality_id]'>$spclt[name]</option>";
				}
			?>
		</select>
		<div id="res" style="max-height:450px;overflow-y:scroll;">
		
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
				<a data-dismiss="modal" onclick="delete_level()" class="btn btn-primary" href="#">Confirm</a>
				<a data-dismiss="modal" onclick="clrr()" class="btn btn-info" href="#">Cancel</a>
			  </div>
            </div>
          <!--modal end-->
	<script>
		load_emp();
	</script>
</div>
