<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Item Query</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	
	
	
	 <div class="" style="text-align:center;">
			
			 <b>Item Name</b>
			  
				<input list="browsrs" type="hidden" name="txtcntrname"  id="txtcntrname"  autocomplete="off" class="intext span4"/>
				<datalist id="browsrs">
				<?php
				$tstid=0; 
				$pid = mysqli_query($link," SELECT 	item_id,item_name FROM `item_master` order by `item_name` ");
				while($pat1=mysqli_fetch_array($pid))
				{
				  echo "<option value='$pat1[item_name]-#$pat1[item_id]'>$pat1[item_name]";

				  
				}
				?>
				</datalist>
				<select id="itm" class="span4">
					<option value="0">Select</option>
					<?php
					$pid=mysqli_query($link,"SELECT `item_id`,`item_name` FROM `item_master` ORDER BY `item_name`");
					while($p=mysqli_fetch_assoc($pid))
					{
					?><option value="<?php echo $p['item_id'];?>"><?php echo $p['item_name'];?></option><?php
					}
					?>
				</select>
    </div>
    
    
    
     
     
	<div class="" style="text-align:center;">
		<b>From</b>
		<input class="form-control" type="text" name="fdate" id="fdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
		<b>To</b>
		<input class="form-control" type="text" name="tdate" id="tdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
	</div>
	<div class="" style="margin-bottom:10px;text-align:center;">
		<button type="button" class="btn btn-info" onclick="srch()"><b class="icon-search"></b> Search</button>
		
	</div>
	<div id="res" style="margin-left:0px;max-height:400px;overflow-y:scroll;">
		
	</div>
</div>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<style>
tr:hover
{
	background:none;
}
</style>
<script>
	$(document).ready(function()
	{
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd'});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd'});
		
		$("#itm").select2({ theme: "classic" });
	});
	
	function srch()
	{
		var jj=1;
		if($("#itm").val()=="0")
		{
			//alert("Please select a item");
			$("#itm").focus();
			jj=0;
			
		}
		
		if(jj==1)
		{
		$.post("pages/ph_item_query_ajax.php",
		{
			itm:$("#itm").val(),
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			type:"item_query",
		},
		function(data,status)
		{
			
			$("#res").html(data);
		})
	 }
	}
	
	
	
	
	
	function report_xls(g,f,t)
	{
		var url="pages/gst_rep_xls.php?fdate="+f+"&tdate="+t;
		document.location=url;
	}
	
	
	
	function report_print_return(g,splr)
	{
		
		fdate=$("#fdate").val();
		tdate=$("#tdate").val();
		url="pages/inv_item_rtn_to_splr_rpt.php?fdate="+fdate+"&tdate="+tdate+"&splirid="+splr+"&billno="+g;
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	
</script>
