<?php
$mkid=$_POST['selectmk'];
if($_POST['enter'])
{
	$sel=$_POST['sel'];
	$num=count($sel);
	if($num==0)
	{
	 
	}
	else
	{
		for($i=0;$i<$num;$i++)
		{
			$sel[$i];
			mysqli_query($GLOBALS["___mysqli_ston"], "insert into ResultOptions values('$mkid','$sel[$i]')");
		}
	 }
}

if($_POST['remove'])
{
	$sel1=$_POST['sel1'];
	$num1=count($sel1);
	if($num1==0)
	{
	 
	}
	else
	{
		for($i=0;$i<$num1;$i++)
		{
			$sel1[$i];
			mysqli_query($GLOBALS["___mysqli_ston"], "delete from ResultOptions where optionid='$sel1[$i]'");
		}
	};
}
		
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Options Link Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="row">
		<form id="form1" name="form1" method="post" action="">
			<div class="span11">
				<table class="table table-bordered table-condensed">
					<tr>
						<td align="center">
							<b>Select Result Option</b>
							<select class="span5" name="selectmk" id="selectmk" onChange="lod_groupdoc();">
								<option value="0">Select</option>
								<?php
									$qrmkt=mysqli_query($GLOBALS["___mysqli_ston"], "select id,name from ResultOption  order by name ");
									while($qrmkt1=mysqli_fetch_array($qrmkt)){if($_POST['selectmk']==$qrmkt1['id']){$ssel="Selected='selected'";} else { $ssel=" ";}  
									?>
								<option value="<?php echo $qrmkt1['id'];?>" <?php echo $ssel;?>><?php echo $qrmkt1['name'];?></option>
								<?php
									;}?>
							</select>
						</td>
					 </tr>
				</table>
			</div>
			<div class="span4">
				<table class="table table-bordered table-condensed">
					<tr>
						<td colspan="3"><b>Name</b> <input class="span3" type="text" id="txtdcname" size="35" autocomplete="off" onKeyUp="sel_pr(this.value,event)"/></td>
					</tr>
				</table>
				<div style="height:300px; overflow:auto; overflow-x:hidden;" id="ungroup">
					
				</div>
			</div>
			<div class="span2">
				<br/><br/><br/><br/><br/><br/><br/><br/>
				<span><input type="submit" name="enter" id="enter" value="&raquo;" class="btn btn-default"/></span><br/><br/>
				<span><input type="submit" name="remove" id="remove" value="&laquo;" class="btn btn-default"/></span> 
			</div>
			<div class="span5">
				<table class="table table-bordered table-condensed">
						<tr>
							<td>ID</td>
							<td>Name </td>
							<td></td>
						</tr>
				</table>
				<div style="height:300px; overflow:auto; overflow-x:hidden; " id="laod_doctor">
					
				</div>
			</div>
		</form>
	</div>
</div>
<script>
	$(document).ready(function(){
		ungroupdoc_load();
		lod_groupdoc();
	});
	var doc_v=1;
	var doc_sc=0;
	function sel_pr(val,e) ///for load patient
	{	    
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			var chk=$("#chk").val();
			if(chk!="0")
			{
			var prod=document.getElementById("prod"+doc_v).innerHTML;
			val_load_new(prod);
			
			}
		}
		else if(unicode==40)
		{
			$("#chk").val("1");
			var chk=doc_v+1;
			var cc=document.getElementById("rad_test"+chk).innerHTML;
			if(cc)
			{
				doc_v=doc_v+1;
				$("#rad_test"+doc_v).css({'font-weight':'bold','color': 'red','transform':'scale(0.95)','transition':'all .2s'});
				var doc_v1=doc_v-1;
				$("#rad_test"+doc_v1).css({'font-weight':'normal','color': 'black','transform':'scale(1.0)','transition':'all .2s'});
				var z2=doc_v%3;
				if(z2==0)
				{
					$("#ungroup").scrollTop(doc_sc)
					doc_sc=doc_sc+90;
				}
			}
		}
		else if(unicode==38)
		{
			$("#chk").val("1");
			var chk=doc_v-1;
			var cc=document.getElementById("rad_test"+chk).innerHTML;
			if(cc)
			{
				doc_v=doc_v-1;
				$("#rad_test"+doc_v).css({'font-weight':'bold','color': 'red','transform':'scale(0.95)','transition':'all .2s'});
				var doc_v1=doc_v+1;
				$("#rad_test"+doc_v1).css({'font-weight':'normal','color': 'black','transform':'scale(1.0)','transition':'all .2s'});
				var z2=doc_v%3;
				if(z2==0)
				{
					doc_sc=doc_sc-90;					
					$("#ungroup").scrollTop(doc_sc);		
				}
			}
		}
		else
		{
			$.post("pages/global_load.php",
			{
				val:val,
				type:"ungrpoptionlist",
			},
			function(data,status)
			{
				$("#ungroup").html(data);
			})
		}
	}
	
	/*function popupit(url)
	{
		var txtfrom=0;
		var txtto=0;
		var vmkid=document.getElementById("selectmk").value;
		if(vmkid==0)
		{
			alert("Please select a Executive name..");
		}
		else
		{
		  url=url+"?date1="+txtfrom+"&date2="+txtto+"&vmkid="+vmkid;
		  newwindow=window.open(url,'window','left=10,top=10,height=600,witdh=600,menubar=1,resizeable=0,scrollbars=1');
		}
	}*/
	
	function ungroupdoc_load() //For load ungroup Doctor
	{
		$.post("pages/global_load.php",
		{
			//hid:document.getElementById("selecthd").value,
			type:"ungrpoptionlist",
		},
		function(data,status)
		{
			$("#ungroup").html(data);
		})
	}
	
	function lod_groupdoc()
	{
		$.post("pages/global_load.php",
		{
			type:"grpoptionlist",
			mkid:document.getElementById("selectmk").value,
		},
		function(data,status)
		{
			$("#laod_doctor").html(data);
		})
	}
	
</script>
<style>
	/*.reference{
	width:700px;}
	.reference td{
	padding:5px;
	text-align:center;
	height:5px;
	width:auto;
	min-width:100px;}
	*/
	.reference td img{
	margin:0;}
</style>
