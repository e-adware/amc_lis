<?php
$ord="";
if($_GET['orderno'])
{
	$ord=base64_decode($_GET['orderno']);
}
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"><?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div style="margin-left:0px;">
		<input type="hidden" id="ord_no" value="<?php echo $ord;?>" />
		<table class="table table-bordered table-condensed">
			<tr>
				<td colspan="2" style="text-align:center;">
					&nbsp;<span id="msgg" style="display:none;font-size:20px;color:#AD0C0C"></span>
				</td>
			</tr>
			<tr style="display:none;">
				<th>Order Date</th>
				<td>
					<input type="text" id="fid" name="fid" value="<?php echo $fid['maxfid'];?>" style="display:none;" />
					<input type="text" id="txtorddate" name="txtorddate" value="<?php echo date('Y-m-d');?>" onfocus="jsdate('txtorddate')"/>
				</td>
			</tr>
			<tr>
				<th>Select Sub Store</th>
				<td>
					<div id="first">
						<select id="sub_store">
							<option value="0">Select Sub Store</option>
							<?php
							$emp=$_SESSION['emp_id']; 
							$qsplr=mysqli_query($link,"select substore_id,substore_name from inv_sub_store order by substore_name");
							//$qsplr=mysqli_query($link,"select b.`substore_id`,b.`substore_name` FROM `inv_sub_dept_access` a, `inv_sub_store` b WHERE a.`substore_id`=b.`substore_id` AND a.`emp_id`='$emp' order by b.`substore_name`");
							while($qsplr1=mysqli_fetch_array($qsplr))
							{
							?>
							<option value="<?php echo $qsplr1['substore_id'];?>"><?php echo $qsplr1['substore_name'];?></option>
							<?php
							}
							?>
						</select>
					</div>
				</td>
			</tr>
			<tr>
			   <th>Item Name </th>
			   <td colspan="2">
				<select id="item" class="span4">
					<option value="0">Select</option>
					<?php
						$pid = mysqli_query($link," SELECT 	item_id,item_name FROM `item_master` order by `item_name` ");
						while($pat1=mysqli_fetch_array($pid))
						{
					?>
						<option value="<?php echo $pat1['item_id'];?>" mytag="<?php echo $pat1['item_name'];?>"><?php echo $pat1['item_name'];?></option>
					<?php
						}
					?>
				</select>
				</td>
			</tr>
			
			<tr>
				<th>Quantity</th>
				<td>
					<input type="text" id="qnt" autocomplete="off" class="span2" onkeyup="chk_num(this,event)" placeholder="Quantity" />
				</td>
			</tr>
			
			<tr>
				<td colspan="4" style="text-align:center">
					<!--<input type="button" id="button2" value="Reset" onclick="reset_all();" class="btn btn-danger" /> -->
					<input type="button" id="button" value="Add" onclick="add_item()" class="btn btn-info" />
					<input type="button" id="button4" value="Done" onclick="save()" class="btn btn-success" />
					<input type="button" id="button5" value="Update" onclick="update()" class="btn btn-success" style="display:none;" />
					<input type="button" id="button6" value="New Order" onclick="new_order()" class="btn btn-primary" />
					<!--<input type="button" name="button5" id="button5" value= "Print" onclick="popitup1('pages/inv_indent_order_rpt.php')" class="btn btn-success" disabled />-->
					<?php
					if($_GET['orderno'])
					{
					?>
					<input type="button" id="button7" value="Go Back" onclick="back_to_order()" class="btn btn-inverse" />
					<?php
					}
					?>
				</td>
			</tr>
			
		</table>
	</div>
	
	
	<div>
		<div id="load_select" class="vscrollbar" style="max-height:250px;overflow-y:scroll;" >
			
		</div>
	</div>
	<input type="hidden" id="set_i_name" />
<div id="alr" style="display:none;top:10%;left:40%;position:fixed;font-size:22pt;font-weight:bold;color:#009900"></div>
<div id="loader" style="display:none;top:50%;position:fixed;"></div>
</div>
<link rel="stylesheet" href="../css/select2.min.css" />
<link rel="stylesheet" href="../css/loader.css" />
<script src="../js/select2.min.js"></script>
<script>
	$(document).ready(function()
	{
		//$("#sub_store").select2({ theme : "classic" });
		$("#item").select2({ theme : "classic" });
		//$("#sub_store").select2("focus");
		
		if($("#ord_no").val().trim()!="")
		{
			load_order();
		}
		else
		{
			$("#sub_store").focus();
		}
		$("#sub_store").on("select2:close",function(e)
		{
			//~ if($("#sub_store").val()!="0")
			//~ {
				//~ $("#loader").show();
				//~ $.post("pages/inv_ajax.php",
				//~ {
					//~ type:24,
					//~ sub_store:$("#sub_store").val(),
				//~ },
				//~ function(data,status)
				//~ {
					//~ $("#loader").hide();
					//~ //alert(data);
					//~ $('#item').children('option:not(:first)').remove();
					//~ $("#item").append(data);
					//~ setTimeout(function(){$("#item").select2("focus");},300);
				//~ })
			//~ }
			//~ else
			//~ {
				//~ $('#item').children('option:not(:first)').remove();
			//~ }
			setTimeout(function(){$("#item").select2("focus");},300);
        });
        
		$("#item").on("select2:close",function(e)
		{
            if($("#item").val()!="0")
            {
				var option = $('option:selected', this).attr('mytag');
				$('#set_i_name').val(option);
				setTimeout(function(){$("#qnt").focus();},300);
			}
        });
        
        $("#sub_store").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				if($("#sub_store").val().trim()!="0")
				{
					setTimeout(function(){$("#item").select2("focus");},200);
				}
			}
		});
		
        $("#qnt").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				if($("#qnt").val().trim()!="" && parseInt($("#qnt").val())>0)
				{
					$("#button").focus();
				}
			}
		});
	});
	
	function load_order()
	{
		$("#loader").show();
		$.post("pages/inv_indent_order_ajax.php",
		{
			ord_no:$("#ord_no").val().trim(),
			user:$("#user").text().trim(),
			type:2,
		},
		function(data,status)
		{
			$("#loader").hide();
			//alert(data);
			$("#sub_store").val(data).attr("disabled",true);
			load_order_det();
		})
	}
	
	function load_order_det()
	{
		$("#loader").show();
		$.post("pages/inv_indent_order_ajax.php",
		{
			ord_no:$("#ord_no").val().trim(),
			user:$("#user").text().trim(),
			type:3,
		},
		function(data,status)
		{
			$("#loader").hide();
			//alert(data);
			$("#load_select").html(data);
			$("#button4").hide();
			$("#button5").show();
			$("#item").select2("focus");
		})
	}
	
	function add_item()
	{
		if($("#sub_store").val()==0)
		{
			$("#sub_store").select2("focus");
		}
		else if($("#item").val()==0)
		{
			$("#item").select2("focus");
		}
		else if($("#qnt").val()=="")
		{
			$("#qnt").focus();
		}
		else if(parseInt($("#qnt").val())==0)
		{
			$("#qnt").focus();
		}
		else if(parseInt($("#qnt").val())<0)
		{
			$("#qnt").focus();
		}
		else
		{
			add_item_temp($("#item").val(),$('#set_i_name').val().trim(),$("#qnt").val().trim());
			$('#sub_store').attr('disabled',true);
			$('#set_i_name').val('');
		}
	}
	function add_item_temp(id,name,qnt)
	{
		var tr_len=$('#mytable tr').length;
		if(tr_len==0)
		{
			var test_add="<table class='table table-condensed table-bordered table-report' id='mytable'>";
			test_add+="<tr><th style='width:5%;'>#</th><th>Description</th><th style='width:8%;'>Quantity</th><th style='width:5%;'>Remove</th></tr>";
			test_add+="<tr class='all_tr'>";
			test_add+="<td>1</td>";
			test_add+="<td>"+name+"<input type='hidden' value='"+id+"' class='test_id'/></td>";
			test_add+="<td>"+qnt+"<input type='hidden' value='"+qnt+"' /></td>";
			test_add+="<td style='text-align:center;'><span onclick='$(this).parent().parent().remove();set_slno()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span></td>";
			test_add+="</tr>";
			test_add+="</table>";
			
			$("#load_select").html(test_add);
			tr_len++;
		}
		else
		{
			var t_ch=0;
			var test_l=document.getElementsByClassName("test_id");
			
			for(var i=0;i<test_l.length;i++)
			{
				if(test_l[i].value==id)
				{
					t_ch=1;
				}
			}
			if(t_ch)
			{

				$("#load_select").css({'opacity':'0.5'});
				$("#msgg").text("Already Selected same item.");
				$("#msgg").fadeIn(500);
				setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#load_select").css({'opacity':'1.0'});})},800);
				
			}
			else
			{
				var tr=document.createElement("tr");
				tr.setAttribute("class","all_tr");
				var td=document.createElement("td");
				var td1=document.createElement("td");
				var td2=document.createElement("td");
				var td3=document.createElement("td");
				
				td.innerHTML=tr_len;
				td1.innerHTML=name+"<input type='hidden' value='"+id+"' class='test_id'/>";
				td2.innerHTML=qnt+"<input type='hidden' value='"+qnt+"' />";
				td3.innerHTML="<span onclick='$(this).parent().parent().remove();set_slno()' style='cursor:pointer;color:#c00;'><i class='icon-remove icon-large text-danger'></i></span>";
				td3.setAttribute("style","text-align:center;");
				
				tr.appendChild(td);
				tr.appendChild(td1);
				tr.appendChild(td2);
				tr.appendChild(td3);
				document.getElementById("mytable").appendChild(tr);
			}
		}
		var tot_ts=document.getElementsByClassName("all_rate");
		for(var i=0;i<tot_ts.length;i++)
		{
			$(".all_tr:eq("+i+")").find('td:eq(0)').text(i+1);
		}
		
		$("#item").val("0").trigger("change");
		$("#item").select2("focus");
		$("#qnt").val("");
	}
	function set_slno()
	{
		var tot_ts=document.getElementsByClassName("all_tr");
		for(var i=0;i<tot_ts.length;i++)
		{
			$(".all_tr:eq("+i+")").find('td:eq(0)').text(i+1);
		}
	}
	function chk_num(ths,e)
	{
		var val=ths.value;
		if(/\D/g.test(val))
		{
			val=val.replace(/\D/g,'');
			$(ths).val(val);
		}
	}
	function update()
	{
		var all="";
		var tr=$(".all_tr");
		//alert(tr.length);
		if($("#sub_store").val()==0)
		{
			alrr("Select sub store.","sub_store");
		}
		else if(tr.length==0)
		{
			alrr("No items selected.","item");
		}
		else
		{
			$("#button").attr("disabled",true);
			$("#button4").attr("disabled",true);
			$("#button5").attr("disabled",true);
			$("#loader").show();
			for(var j=0; j<tr.length; j++)
			{
				all+=$(".all_tr:eq("+j+")").find('td:eq(1) input:first').val()+"@"+$(".all_tr:eq("+j+")").find('td:eq(2) input:first').val()+"#%#";
			}
			//alert(all);
			$.post("pages/inv_indent_order_ajax.php",
			{
				sub_store:$("#sub_store").val(),
				ord_no:$("#ord_no").val().trim(),
				all:all,
				user:$("#user").text().trim(),
				type:4,
			},
			function(data,status)
			{
				$("#loader").hide();
				//alert(data);
				if(data=="1")
				{
					$("#button").attr("onclick","");
					$("#button4").attr("onclick","");
					for(var j=0; j<tr.length; j++)
					{
						$(".all_tr:eq("+j+")").find('td:eq(3)').text("");
					}
					alert("Updated");
				}
				if(data=="2")
				{
					alert("Error");
				}
				if(data=="3")
				{
					alert("Partially Received");
				}
				if(data=="4")
				{
					alert("Already Received");
				}
			})
		}
	}
	function save()
	{
		var all="";
		var tr=$(".all_tr");
		//alert(tr.length);
		if($("#sub_store").val()==0)
		{
			//alrr("Select sub store.","sub_store");
			$("#sub_store").focus();
		}
		else if(tr.length==0)
		{
			alrr("No items selected.","item");
		}
		else
		{
			$("#button").attr("disabled",true);
			$("#button4").attr("disabled",true);
			$("#loader").show();
			for(var j=0; j<tr.length; j++)
			{
				all+=$(".all_tr:eq("+j+")").find('td:eq(1) input:first').val()+"@"+$(".all_tr:eq("+j+")").find('td:eq(2) input:first').val()+"#%#";
			}
			//alert(all);
			$.post("pages/inv_indent_order_ajax.php",
			{
				sub_store:$("#sub_store").val(),
				all:all,
				user:$("#user").text().trim(),
				type:1,
			},
			function(data,status)
			{
				$("#loader").hide();
				//alert(data);
				if(data=="1")
				{
					$("#button").attr("onclick","");
					$("#button4").attr("onclick","");
					for(var j=0; j<tr.length; j++)
					{
						$(".all_tr:eq("+j+")").find('td:eq(3)').text("");
					}
					alert("Saved");
				}
				if(data=="2")
				{
					alert("Error");
				}
			})
		}
	}
	function alrr(txt,sel)
	{		
		$("#msgg").text(txt);
		$("#msgg").fadeIn(500);
		setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#"+sel).select2("focus");})},800);
	}
	function back_to_order()
	{
		$(".btn").attr("disabled",true);
		bootbox.dialog({ message: "<b>Please wait while redirecting...</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> "});
		setTimeout(function()
		{
			window.location="index.php?param="+btoa(155);
		}, 1000);
	}
	function new_order()
	{
		//location.reload(true);
		window.location="index.php?param="+btoa(153);
	}
</script>
<style>
	.table-report
	{
		background:#FFFFFF;
	}
</style>
