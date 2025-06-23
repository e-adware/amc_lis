<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Purchase Order Report</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	
	<div class="" style="text-align:center;">
		<b>Select </b>
		<select id="supplier" autofocus>
			<option value="0"> --Al Supplier--</option>
			<?php
			$qq=mysqli_query($link,"SELECT id, `name` FROM `inv_supplier_master` order by `name`");
			while($r=mysqli_fetch_array($qq))
			{
			?>
			<option value="<?php echo $r['id']; ?>"><?php echo $r['name']; ?></option>
			<?php
			}
			?>
		</select>
		&nbsp;	
			<b>Order No </b>

			<input list="browsrs" type="text" name="txtorderno"  id="txtorderno"  autocomplete="off" class="intext span2"/>
				<datalist id="browsrs">
				<?php
				$tstid=0; 
				$pid = mysqli_query($link," SELECT 	order_no,order_no FROM `inv_purchase_order_master` where del=0 order by `order_no` ");
				while($pat1=mysqli_fetch_array($pid))
				{
				  echo "<option value='$pat1[order_no]'>$pat1[order_no]";

				  
				}
				?>
			</datalist>
				
			&nbsp;	
			<b>From</b>
			<input class="form-control" type="text" name="fdate" id="fdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
			<b>To</b>
			<input class="form-control" type="text" name="tdate" id="tdate" style="text-align:center;" value="<?php echo date('Y-m-d');?>" />
			
			
    </div>
    
   
	
	<div class="" style="margin-left:0px;">
		<table class="table table-condensed table-bordered" style="width:100%;margin:0 auto;" >
			
			<tr>
				<td colspan="2" style="text-align:center">
					<button type="button" class="btn btn-info" onclick="srch()" style="width:130px" >Search <i class="icon-search"></i></button>
					
					
				</td>
			</tr>
		</table>
	</div>
	
	<div id="res" style="margin-left:0px;max-height:400px;overflow-y:scroll;">
		
	</div>
	
	<!--modal-->
		<a href="#myAlert" data-toggle="modal" id="dl" class="btn" style="display:none;">A</a>
		<div id="myAlert" class="modal hide">
		  <div class="modal-body">
			  <input type="text" id="idl" style="display:none;" />
			<p>Are You Sure Want To Delete...?</p>
		  </div>
		  <div class="modal-footer">
			<a data-dismiss="modal" onclick="del()" class="btn btn-primary" href="#">Confirm</a>
			<a data-dismiss="modal" onclick="clearr()" class="btn btn-info" href="#">Cancel</a>
		  </div>
		</div>
	  <!--modal end-->
	  
</div>
<div id="loader" style="display:none;top:50%;position:fixed;"></div>
<link rel="stylesheet" href="../css/loader.css" />
<script>
	$(document).ready(function()
	{
		$("#txtorderno").keyup(function(e)
		{
			if(e.keyCode==13)
			srch();
		});
		
		
		$("#fdate").datepicker({dateFormat: 'yy-mm-dd'});
		$("#tdate").datepicker({dateFormat: 'yy-mm-dd'});
	});
	
	function srch()
	{
		$("#loader").show();
		$.post("pages/inv_purchase_order_ajax.php",
		{
			fdate:$("#fdate").val(),
			tdate:$("#tdate").val(),
			spplrid:$("#supplier").val(),
			orderno:$("#txtorderno").val(),
			type:3,
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#res").html(data);
		})
	}
	
function delete_data(id)
{
$("#dl").click();
$("#idl").val(id);
}
	
	function del()
	{
		$.post("pages/inv_load_delete.php",
		{
			type:"purchaseorderdel",
			rid:$("#idl").val(),
		},
		function(data,status)
		{
			
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
				clearr();
				get_id();
				load_item();
			}, 1000);
		})
	}
	
	
	
	function rcv_rep_exp(ord)
	{
		var url="pages/purchase_receive_rep_xls.php?orderno="+ord;
		document.location=url;
	}
	function inv_order_print(ord)
	{
		url="pages/inv_purchase_ordr_rpt.php?orderno="+btoa(ord);
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	
	function redirect_sale_frm(orderno)
	{
		
		bootbox.dialog({ message: "<b>Redirecting to Order Update</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> "});
		setTimeout(function(){
			window.location="processing.php?param=163&orderno="+orderno;
		 }, 2000);
	}
	
</script>
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<style>
tr:hover
{
	background:none;
}
</style>
