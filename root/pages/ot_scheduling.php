<?php
unset($_SESSION["schedule_id"]);
$uhid_str=base64_decode($_GET['uhid_str']);
$pin_str=base64_decode($_GET['pin_str']);
$fdate_str=base64_decode($_GET['fdate_str']);
$tdate_str=base64_decode($_GET['tdate_str']);
$name_str=base64_decode($_GET['name_str']);
$phone_str=base64_decode($_GET['phone_str']);
$param_str=base64_decode($_GET['param_str']);

?>
<!--header-->
<div id="content-header">
	<div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table">
         <tr>
             <th class="">Unit No.:</th>
            <td class="">
                <input list="browsrs" type="text" name="uhid" id="uhid" class="" style="width:100px;" onkeyup="search_patient_list()" value="<?php echo $uhid_str; ?>" autofocus />
                <datalist id="browsrs">
                <?php
                    //~ $pid = mysqli_query($link," SELECT DISTINCT `patient_id` FROM `uhid_and_opdid` WHERE `type`='3' order by `slno` DESC limit 0,100 ");
                    //~ while($pat_uid=mysqli_fetch_array($pid))
                    //~ {
                        //~ echo "<option value='$pat_uid[patient_id]'>";
                    //~ }
                ?>
                </datalist>
            </td>
             <th class="">Bill No.:</th>
            <td class="">
                <input list="browsr" type="text" name="ipd" id="ipd" class="" style="width:100px;" onkeyup="search_patient_list()" value="<?php echo $pin_str; ?>" />
                <datalist id="browsr">
                <?php
                    //~ $oid= mysqli_query($link," SELECT DISTINCT `opd_id` FROM `uhid_and_opdid` WHERE `type`='3' order by `slno` DESC limit 0,100 ");
                    //~ while($pat_oid=mysqli_fetch_array($oid))
                    //~ {
                        //~ echo "<option value='$pat_oid[opd_id]'>";
                    //~ }
                ?>
                </datalist>
            </td>
            <th class="">Patient Name:</th>
            <td class="">
                <input type="text" name="name" id="name" class="name" onkeyup="search_patient_list()" value="<?php echo $name_str; ?>" />
            </td>
            <th style="display:none;">Date:</th>
            <td style="display:none;">
                <input type="text" name="date" id="date" class="pin input-group datepicker span2" onChange="checkup_date()" value="<?php echo $fdate_str; ?>" />
            </td>
            <td>
				<button class="btn btn-search"  onclick="search_patient_list()"><i class="icon-search"></i> Search</button>
            </td>
          </tr>
    </table>
	<div id="load_all" class="ScrollStyle">
	
	</div>
	<input type="hidden" id="list_start" value="50">
</div>
<!-- Loader -->
<div id="loader" style="margin-top:0%;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	$(document).ready(function()
	{
		$("#loader").hide();
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
		});
		$('#load_all').on('scroll', function() {
			var div_height = $(this).get(0).scrollHeight;
			var div = $(this).get(0);
			
			if(div.scrollTop + div.clientHeight >= div.scrollHeight) {
				var list_start=$("#list_start").val().trim();
				list_start=parseInt(list_start)+50;
				$("#list_start").val(list_start);
				search_patient_list();
			}
		});
		search_patient_list();
	});
	function search_patient_list()
	{
		$("#loader").show();
		$.post("pages/ot_scheduling_data.php",
		{
			uhid:$("#uhid").val(),
			ipd:$("#ipd").val(),
			name:$("#name").val(),
			date:$("#date").val(),
			usr:$("#user").text().trim(),
			list_start:$("#list_start").val(),
			type:"search_patient_list_ipd",
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").html(data);
		})
	}
	
	function redirect_page(uhid,ipd)
	{
		var param_str="&param_str="+btoa($("#param_id").val())+"&fdate_str="+btoa($("#date").val())+"&tdate_str="+btoa($("#to").val())+"&uhid_str="+btoa($("#uhid").val())+"&pin_str="+btoa($("#ipd").val())+"&name_str="+btoa($("#name").val());
		window.location="?param="+btoa(214)+"&uhid="+btoa(uhid)+"&ipd="+btoa(ipd)+"&schedule_id="+btoa(0)+param_str;
	}
</script>
