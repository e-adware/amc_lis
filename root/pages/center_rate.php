<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered table-condensed">
		<tr>
			<th>Select Center</th>
			<td><label for="select"></label>
			<select name="selectmk" id="selectmk" >
				<option value="0">Select</option>
				<?php
				$qrmkt=mysqli_query($link,"select centreno,centrename from centremaster order by centrename ");
				while($qrmkt1=mysqli_fetch_array($qrmkt))
				{
					if($_POST['selectmk']==$qrmkt1['centreno'])
					{
						$ssel="Selected='selected'";
					}else
					{
						$ssel=" ";
					}  
				?>
				<option value="<?php echo $qrmkt1['centreno'];?>" <?php echo $ssel;?>><?php echo $qrmkt1['centrename'];?></option>
				<?php
				}?>
			</select>
			<td>
			<td> <input type="button" name="button2" id="button2" class="btn btn-default" value="Show" onclick="popupit('pages/center_test_list_rpt.php')" /></td>
		</tr>
	</table>
	<table class="table   table-bordered table-condensed">
		<tr>
			<th>Test :</th>
			<td><input type="text" id="txtname" name="txtname" class="imp" onkeyup="sel_pr(this.value,event)"/></td>
			<td><input type="text" id="txtid" name="txtid" readonly="readonly" class="imp" /></td>
		</tr>
	</table>
	<div style="height:200px; overflow:auto; overflow-x:hidden;" id="loadtest">
	</div> 
	<br/>
	<table class="table table-bordered table-condensed">
		<tr>
			<th style="text-align:center">Test Rate : <input type="text" id="txtrate" onkeyup="numentry('txtrate')" class="imp" /></th>
		</tr>
		<tr>
			<td style="text-align:center">
				<input type="button" id="button" class="btn btn-info" value="Save" onclick="Insert();" style="width:70px"/>
				<input type="button" id="button" class="btn btn-warning" value="Refresh" onclick="clearr();"/>
			</td>
		</tr>
	</table>
</div>

<script>
	$(document).ready(function(){
		//test_load();
	});
	function popupit(url)  // for pop up
	{ 
		var mkid=document.getElementById("selectmk").value;
		url=url+"?mkid="+mkid;
		newwindow=window.open(url,'window','left=10,top=10,height=600,witdh=1200,menubar=1,resizeable=0,scrollbars=1');	
	}
	var doc_v=1;
	var doc_sc=0;
	function sel_pr(val,e)
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
				$("#rad_test"+doc_v).css({'font-weight':'bold','color': '#419641','transform':'scale(0.95)','transition':'all .2s'});
				var doc_v1=doc_v-1;
				$("#rad_test"+doc_v1).css({'font-weight':'normal','color': 'black','transform':'scale(1.0)','transition':'all .2s'});
				var z2=doc_v%3;
				if(z2==0)
				{
					$("#loadtest").scrollTop(doc_sc)
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
				$("#rad_test"+doc_v).css({'font-weight':'bold','color': '#419641','transform':'scale(0.95)','transition':'all .2s'});
				var doc_v1=doc_v+1;
				$("#rad_test"+doc_v1).css({'font-weight':'normal','color': 'black','transform':'scale(1.0)','transition':'all .2s'});
				var z2=doc_v%3;
				if(z2==0)
				{
					doc_sc=doc_sc-90;					
					$("#loadtest").scrollTop(doc_sc);
				}
			}
		}
		else
		{
			$.post("pages/global_load.php",
			{
				center:$("#selectmk").val(),
				val:val,
				type:"testtarget",
			},
			function(data,status)
			{
				$("#loadtest").html(data);
			})
		}
	}


	function clearr() //For Clear Field
	{
		var b=document.getElementsByClassName("imp");
		for(var j=0;j<b.length;j++)
		{
			b[j].value="";
		}
		Get_id();
	}

	function numentry(id) ///only numeric value entry
	{
		num=document.getElementById(id);
		numx=/^[0-9]+$/;
		if(!num.value.match(numx))
		{
			num.value="";
		}
	}
	function numentry(id) //for Numeric value support in the text field
	{
		var num=document.getElementById(id);
		var numex=/^[0-9]+$/;
		//var nume=/a-z/
		if(!num.value.match(numex))
		{
			num.value="";
		}
	}

	function test_load() //For load test
	{
		$.post("pages/global_load.php",
		{
			type:"testtarget",
		},
		function(data,status)
		{
			$("#loadtest").html(data);
		})
	}
	function val_load_new(docid)  ///For load  Details
	{
		$.post("pages/global_load.php",
		{
			docid:docid,
			type:"spltest",
		},
		function(data,status)
		{
			var val=data.split("@");
			document.getElementById("txtid").value=val[0];
			document.getElementById("txtname").value=val[1];
			$("#txtrate").html="";
			$("#txtrate").focus();
		})
	}

	function Insert() //For Insert
	{
		///////For Check Blank fields//
		var jj=1;
		var b=document.getElementsByClassName("imp");
		for(var i=0;i<b.length;i++)
		if(b[i].value=="")
		{
			jj=0;
			document.getElementById(b[i].id).placeholder="Can not be blank";
		}
		var mkid=document.getElementById("selectmk").value;
		if(mkid==0)
		{
			alert("Please select a center name...");
			jj=0;
		}
		if(jj==1)
		{   
			///////end ////////
			$.post("pages/global_insert_data.php",
			{
				type:"centertest",
				mkid:document.getElementById("selectmk").value,
				testid:document.getElementById("txtid").value,
				rate:document.getElementById("txtrate").value,
			},
			function(data,status)
			{
				alert("Data Saved");
				clearr();
				$("#txtname").focus();
			})
		}
	}
</script>
