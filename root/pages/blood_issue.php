<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Blood Issue</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="" style="margin-left:0px;">
		<table class="table table-condensed">
			<tr>
				<td>UHID:<br/>
					<input type="text" list="browsrs" id="uhid" placeholder="Patient Number" autofocus />
					<datalist id="browsrs">
					<?php
						$pid = mysqli_query($link,"SELECT `uhid` FROM `patient_info` WHERE `patient_id` IN (SELECT DISTINCT `patient_id` FROM `blood_request` order by `request_id` DESC)");
						while($pat_uid=mysqli_fetch_array($pid))
						{
							echo "<option value='$pat_uid[uhid]'>";
						}
					?>
					</datalist>
					<input type="text" id="err" value="0" style="display:none;" />
					<input type="text" id="id" value="0" style="display:none;" />
					<input type="button" id="pid" class="btn btn-info" onclick="show_pat()" value="Search" />
				</td>
			</tr>
		</table>
	</div>
	<div id="pat_det">
	
	</div>
</div>
<script>
	$(document).ready(function()
	{
		check_exp_date();
	});
	function bar_field(vl,r,comp,e)
	{
		var s="";
		var i=1;
		for(;i<=vl;i++)
		{
			s+="<input type='text' class='span1 iss' id='"+r+i+"' onkeyup='check(this.value,this.id,"+comp+",event)' placeholder='Bar code' /> ";
		}
		$("#"+r).html(s);
	}
	function check(vl,id,comp,e)
	{
		$("#id").val(id);
		if(e.keyCode==13)
		{
			$.post("pages/global_load_g.php",
			{
				uhid:$("#uhid").val(),
				bar:vl,
				comp:comp,
				type:"blood_pat_issue_check",
			},
			function(data,status)
			{
				//alert(data);
				$("#err").val(data);
				if(data==1)
				$("#"+id).css("background","#B6FFB4");
				else if(data==2)
				$("#"+id).css("background","#F89D92");
				else if(data==3)
				$("#"+id).css("background","#FFF18D");
			})
		}
		if(($("#"+id).val()).length<2)
		$("#"+id).css("background","");
		$("#err").val('0');
	}
	function blood_issue()
	{
		var all="";
		var ff=1;
		var iss=$(".iss");
		var sel=$(".sel");
		//alert(iss.length);
		//alert(sel.length);
		for(var i=1;i<=sel.length;i++)
		{
			if($("#sel"+i).val()!=0)
			{
				var s=$("#sel"+i).val();
				var c=$("#tr"+i).val();
				for(var j=1;j<=s;j++)
				{
					if($("#bar"+i+j).val()=="")
					{
						ff=0;
						$("#bar"+i+j).focus();
					}
					else
					{
						ff=1;
						all+=c+"@"+$("#bar"+i+j).val()+"@"+$("#req"+i).val()+"@#";
					}
				}
			}
		}
		if($("#err").val()==0)
		{
			alert("Component Not Compared");
			$("#"+$("#id").val()).focus();
		}
		if($("#err").val()==2)
		{
			alert("Component Not Matched");
			$("#"+$("#id").val()).focus();
		}
		if($("#err").val()==3)
		{
			alert("Bar Code Not Found");
			$("#"+$("#id").val()).focus();
		}
		if(ff==1 && $("#err").val()==1)
		{
			//alert(all);
			$.post("pages/global_insert_data_g.php",
			{
				uhid:$("#uhid").val(),
				all:all,
				usr:$("#user").text().trim(),
				type:"blood_pat_issue_save",
			},
			function(data,status)
			{
				alert(data);
				show_pat();
			})
		}
	}
	function check_exp_date()
	{
		$.post("pages/global_load_g.php",
		{
			usr:$("#user").text().trim(),
			type:"check_exp_date",
		},
		function(data,status)
		{
			//alert(data);
		})
	}
	function show_pat()
	{
		$("#err").val('0');
		if($("#uhid").val()=="")
		{
			$("#uhid").focus();
		}
		else
		{
			$.post("pages/global_load_g.php",
			{
				uhid:$("#uhid").val(),
				type:"blood_pat_issue",
			},
			function(data,status)
			{
				$("#pat_det").html(data);
				$("#result").html('');
				//$("select:first").focus();
			})
		}
	}
</script>
