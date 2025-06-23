<?php
//~ $q=mysqli_query($link,"SELECT * FROM `TABLE1`");
//~ while($r=mysqli_fetch_array($q))
//~ {
	//~ //mysqli_query($link,"UPDATE `ph_item_master` SET `item_mrp`='$r[item_mrp]' WHERE `item_code`='$r[item_code]'");
	//~ //mysqli_query($link,"INSERT INTO `ph_item_master`(`item_code`, `item_name`) VALUES ('$vid','$r[item_name]')");
//~ }
?>

<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Item Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span6" style="margin-left:0px;">
		<form id="form1" method="post">
			<table class="table table-bordered table-condensed" >
				<tr>
					<td>Id</td>
					<td>
						<input type="text" name="txtcid" id="txtcid" value="" readonly="readonly" class="intext"/>
					</td>
				</tr>
				
				<tr>
					<td>Name</td>
					<td>
						<input type="text" name="intext1"  id="txtcntrname" value="" autocomplete="off" class="intext span4"/>
					</td>
				</tr>
				
				<tr>
					<td>HSN Code</td>
					<td>
						<input list="browsrs" type="text" name="intext2"  id="txthsn" value="" autocomplete="off" class="intext span4"/>
						<datalist id="browsrs">
								<?php
								
									$pid = mysqli_query($link," SELECT `hsn_code` FROM `hsn_master` order by `hsn_code` DESC");
									while($pat_uid=mysqli_fetch_array($pid))
									{
										echo "<option value='$pat_uid[hsn_code]'>";
									}
								?>
								</datalist>
					</td>
				</tr>
				
				
				
				
				<tr>
					<td>Generic Name</td>
					<td>
						<select id="gen_name">
						  
							<option value="0">--Select--</option>
							<?php
							$qr=mysqli_query($link,"SELECT * FROM `generic` ORDER BY `name`");
							while($r=mysqli_fetch_array($qr))
							{
							?>
							<option value="<?php echo $r['id'];?>"><?php echo $r['name'];?></option>
							<?php
							}?>
						</select>
					</td>
				</tr>
				
				
				<tr>
					<td>Item Type </td>
					<td>
						<select name="intext2" id="selecttype" class="intext">
							<option value="0">--Select Type--</option>
							<?php
								$qitmtype=mysqli_query($link,"select * from ph_item_type_master order by item_type");
								while($qitmtype1=mysqli_fetch_array($qitmtype)){if($_POST['selecttype']==$qitmtype1['item_type_id']){$ssel="Selected='selected'";} else { $ssel=" ";}  
								?>  
							<option value="<?php echo $qitmtype1['item_type_id'];?>"><?php echo $qitmtype1['item_type'];?></option>
							<?php
								}?>
						</select>
					</td>
				</tr>
				
				<tr>
					<td>MRP  </td>
					<td>
						<input type="text" name="intext3" id="txtmrp" autocomplete="off" class="intext" />
					</td>
				</tr>
				<tr>
					<td>Streangth  </td>
					<td>
						<input type="text" name="intext4" id="txtstrength" autocomplete="off" class="intext" />
					</td>
				</tr>
				<tr>
					<td>CGST+SGST  </td>
					<td>
						<input type="text" name="intext5" id="txtgst" autocomplete="off" class="intext" /> %
					</td>
				</tr>
				<tr>
					<td>Cost Price  </td>
					<td>
						<input type="text" name="intext6" id="txtcostprice" autocomplete="off" class="intext" />
					</td>
				</tr>
				<tr>
					<td>Strip Qnty.  </td>
					<td>
						<input type="text" name="intext7" id="stripqnty" autocomplete="off" class="intext" />
					</td>
				</tr>
				
				<tr>
					<td colspan="4" style="text-align:center">
						<input type="button" name="intext8" id="button" value= "Submit" onclick="insert_data()" class="btn btn-default btn-info" style="width:100px"/>
						<input type="button" name="button2" id="button2" value="Reset" onclick="clearr();" class="btn btn-danger" style="width:100px"/> 
						
						<!--<input type="button" name="button1" id="button1" value= "View" onclick="popitup1('pages/itemlist_rpt.php')" class="btn btn-success" style="width:100px" />-->
					</td>
				</tr>
			</table>
		</div>
		<div class="span5">
			<table class="table table-condensed table-bordered">
				<tr>
					<td>Search</td>
					<td> <input type="text" name="txtcustnm" id="txtcustnm"  autocomplete="off" class="intext span4" onkeyup="sel_pr(this.value,event)" /></td>
				</tr>
			</table>
			<div id="load_materil" class="vscrollbar" style="max-height:400px;overflow-y:scroll;" >
				
			</div>
		<div id="back"></div>
		<div id="results"></div>
	</form>
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
</div>
<script src="../js/select2.min.js"></script>
<link rel="stylesheet" href="../css/select2.css" type="text/css" />
<style>
	.itname:hover
	{
		color:#0000FF;
	}
</style>
<script>
	$(document).ready(function()
	{
		// //$(this).val($this.val().replace(/[^\d.]/g, ''))
		//$("#gen_name").select2();
		get_id();
		load_item();
		
		$("#txtcntrname").keyup(function(e)
		{
			$(this).css("border","");
			$(this).val($(this).val().toUpperCase());
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				$(this).css("border","1px solid #f00");
				else
				{
					$(this).css("border","");
					$("#txthsn").focus();
					//$('#selectcategory').select2('focus');
				}
			}
		});
		
		
		$("#txthsn").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				$("#gen_name").focus();
			}
		});
		
		
		
			$("#txthsn").keyup(function(e)
			{
				if(e.keyCode==13)
				{
					if(($(this).val().length)>0)
					{
						$.post("pages/ph_load_display_ajax.php",
						{
							uhid:$(this).val(),
							type:"loadhsnno",
						},
						function(data,status)
						{
							var val=data.split("@");
							$("#txtgst").val(val[1]);
						})
					}
				}
			});
				
		
		$("#selectcategory").keyup(function(e)
		{
			$(this).css("border","");
			if(e.keyCode==13)
			{
				$("#gen_name").focus();
			}
		});
		
		$("#gen_name").keyup(function(e)
		{
			$(this).css("border","");
			if(e.keyCode==13)
			{
				$("#selecttype").focus();
			}
		});
		
		$("#selecttype").keyup(function(e)
		{
			$(this).css("border","");
			if(e.keyCode==13)
			{
				$("#txtmrp").focus();
			}
		});
		
		$("#txtmrp").keyup(function(e)
		{
			$(this).css("border","");
			var val = $(this).val();
			if(isNaN(val))
			{
				val = val.replace(/[^0-9\.]/g,'');
				if(val.split('.').length>2) 
				val =val.replace(/\.+$/,"");
			}
			$(this).val(val);
			if(e.keyCode==13)
			{
				if($(this).val().trim()=="" || $(this).val()=="." || parseFloat($(this).val())==0)
				$(this).css("border","1px solid #f00");
				else
				{
					$(this).css("border","");
					$("#txtstrength").focus();
				}
			}
		});
		
		$("#txtstrength").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				$("#txtgst").focus();
			}
		});
		
		$("#txtgst").keyup(function(e)
		{
			
			if(e.keyCode==13)
			{
				$("#txtcostprice").focus();
			}
		});
		
		
		$("#txtcostprice").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				$("#stripqnty").focus();
			}
		});
		$("#stripqnty").keyup(function(e)
		{
			if(e.keyCode==13)
			{
				$("#button").focus();
			}
		});
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
						$("#load_materil").scrollTop(doc_sc);
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
						$("#load_materil").scrollTop(doc_sc);
					}
				}	
		
			}
			else
			{
				$.post("pages/global_load_g.php",
				{
					val:val,
					type:"load_item_master",
				},
				function(data,status)
				{
					$("#load_materil").html(data);
				})
			}
	}
	
	
	function popitup1(url)
	{
		var custid=0;
		
		url=url+"?custid="+custid;
		newwindow=window.open(url,'window','left=10,top=10,height=600,width=1000,menubar=1,resizeable=0,scrollbars=1');
	}
	
	function clearr()//For Clear the text fields
	{
		var b=document.getElementsByClassName("intext");
		for(var j=0;j<b.length;j++)
		{
			b[j].value="";
		} 
		$("#selecttype").val('0');
		$("#txtcntrname").css('border','');
		$("#selecttype").css('border','');
		$("#txtmrp").css('border','');
		$("#button").val('Submit');
		$("#gen_name").val("0").trigger("change");
		get_id();
	} 
	
	function setFocus()
	{
		document.getElementById("txtcntrname").focus();
	}
	
	function load_item()
	{
		
		$.post("pages/global_load_g.php",
		{
			type:"load_item_master",
		},
		function(data,status)
		{
			$("#load_materil").html(data);
		})
	}
	
	
	function val_load_new(rid)///for retrieve data against center
	{
	  $.post("pages/ph_load_display_ajax.php",
		{
			type:"edit_item_master",
			rid:rid,
		},
		function(data,status)
		 {
			
			var val=data.split("@");
			$("#txtcntrname").css('border','');
			$("#selecttype").css('border','');
			$("#txtmrp").css('border','');
			document.getElementById("txtcid").value=val[0];
			document.getElementById("txtcntrname").value=val[1];
			$("#gen_name").val(val[2]).trigger("change");
			document.getElementById("txtstrength").value=val[3];
			document.getElementById("txtmrp").value=val[4];
			document.getElementById("txtcostprice").value=val[5];	
			document.getElementById("txtgst").value=val[6];
			document.getElementById("selecttype").value=val[7];
			document.getElementById("stripqnty").value=val[8];
			document.getElementById("txthsn").value=val[10];
			$("#txtcntrname").focus();
			$("#button").val("Update");
		 })
	}
	
	function delete_data(id)
	{
		$("#dl").click();
		$("#idl").val(id);
	}
	
	function del()
	{
		$.post("pages/global_delete_g.php",
		{
			type:"item_master_delete",
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
	
	function insert_data()
	{
		///////For Check Blank fields//
		var jj=1;
		var chk=document.getElementsByClassName("imp");
		for(var i=0;i<chk.length;i++)
		if(chk[i].value=="")
		{
			jj=0;
			document.getElementById(chk[i].id).placeholder="Can not be Blank";
			document.getElementById(chk[i].id).focus();
		}
		
		if($("#txtcntrname").val()=="")
		  {
			document.getElementById("txtcntrname").placeholder="Cannot blank";
			  $("#txtcntrname").focus();
			  jj=0;
		  }
		  /*
			else if($("#txthsn").val()=="")
			{
			alert("Please Enter the HSN Code....");
			$("#txthsn").focus();
			jj=0;
			}
		
		else if($("#selecttype").val()=="0")
		  {
			  $("#selecttype").focus();
			  jj=0;
		  }
		  */
		else if($("#txtmrp").val()=="")
		  {
			  document.getElementById("txtmrp").placeholder="Cannot blank";
			  $("#txtmrp").focus();
			  jj=0;
		  }
		   
		if(jj==1)
		{   
		///////end ////////
		 $.post("pages/ph_insert_data.php",
		  {
			  type:"insert_item_master",
			  csid:document.getElementById("txtcid").value,
			  csname:document.getElementById("txtcntrname").value,
			  catid:0,
			  gen_name:document.getElementById("gen_name").value,
			  vtpeye:document.getElementById("selecttype").value,
			  vmrp:document.getElementById("txtmrp").value,
			  vstrngth:document.getElementById("txtstrength").value,
			  vvat:0,
			  costprice:document.getElementById("txtcostprice").value,
			  gst:document.getElementById("txtgst").value,
			  strpqnty:document.getElementById("stripqnty").value,
			  hsncode:document.getElementById("txthsn").value,
		  },
		  function(data,status)
		   {
			   bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					clearr();
					load_item();
				}, 1000);
		   }
		
		 )
	}}
	
	function get_id() //For Get Id
	{
		$.post("pages/global_load_g.php",
		{
			type:"item_master_id",
		},
		function(data,status)
		{
			$("#txtcid").val(data);
		})
	  setFocus()
	}
	function tab_next(e) 
	{ 	
		var unicode=e.keyCode? e.keyCode : e.charCode; 	
		if(unicode==13) 
		{ 		
			var act=document.activeElement.id; 		
			if(!act) 		
			{ 			
			document.getElementById("info1").focus();	 		
			} 	
				else 
			{    
			   var clsn=$("#"+act).attr("class");    
			   var nam=$("#"+act).attr("name"); 
			   var val=nam.replace( /^\D+/g, ''); 
			   val=parseInt(val)+1; 
			   document.getElementsByName(clsn+val)[0].focus(); 
			}
		}
	}
</script>
