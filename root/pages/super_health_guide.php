<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Executive Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="row">
		<div class="span7">
			<table class="table   table-bordered table-condensed">
				<tr>
					<td> ID</td>
					<td ><input type="text" name="txtid" id="txtid" class="imp"  readonly="readonly"  /></td>
					<input type="hidden" name="txtlgid" id="txtlgid" value="" autocomplete="off" />
				</tr>
				<tr>
					<td >Name</td>
					<td ><input type="text" name="intext1" id="txtname" class="intext" size="35" value="" autocomplete="off" onKeyup="caps_it(this.value,this.id,event)" /></td>
				</tr>
				<tr>
					<td >Address</td>
					<td ><input type="text" name="intext2" id="txtaddress" class="intext" size="35" value="" autocomplete="off" onKeyup="caps_it(this.value,this.id,event)" /></td>
				</tr>							
				<tr>
					<td >Phone</td>
					<td ><input type="text" name="intext3" id="txtphon" size="35" class="intext" autocomplete="off" onKeyup="phone_check(this.value,event)" /></td>
				</tr>
				<tr>
					<td >Email</td>
					<td ><input type="text" name="intext4" id="txtemail" size="35" class="intext" autocomplete="off" onKeyup="email(this.value,event)" /></td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center" >
						<input type="button" name="intext18" id="button" value="Submit" onclick="Insert();" class="btn btn-info" />
						<input type="button" name="button2" id="button2" onclick="clearr();" value="Reset" class="btn btn-danger" />
						<input type="button" name="button3" id="button3" onclick="popitup('pages/health_guide_list.php');" value="View" class="btn btn-success" />
					</td>
				</tr>
			</table>                 
		</div>
		<div class="span4">
			<table  class="table   table-bordered table-condensed"  >
				<tr>
				   <td>Name</td>
				   <td colspan="2"><input type="text" id="txtdoc" size="35" onkeyup="sel_pr(this.value,event)"/></td>
				</tr>
				<tr>
					<td>ID</td>
					<td>Name</td>
					<td>Delete</td>
				</tr>
			</table>
			<div style="height:350px; overflow-x:hidden" id="laod_doctor"> 
			</div> 
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		lod_refraldoctor();
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
					$("#laod_doctor").scrollTop(doc_sc)
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
					$("#laod_doctor").scrollTop(doc_sc);
				}
			}
		}
		else
		{
			$.post("pages/health_guide_data.php",
			{
				val:val,
				type:"super_cntermaster",
			},
			function(data,status)
			{
				$("#laod_doctor").html(data);
			})
		}
	}

	function popitup(url)
	{
		var txtfrom=0;
		var txtto=0;
		url=url+"?date1="+txtfrom+"&date2="+txtto+"&super=1";
		window.open(url,'window','left=10,top=10,height=600,witdh=600,menubar=1,resizeable=0,scrollbars=1');
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

	function lod_refraldoctor()
	{
		$.post("pages/health_guide_data.php",
		{
			type:"super_cntermaster",
		},
		function(data,status)
		{
			$("#laod_doctor").html(data)
			get_id();
			clearr();
		})
	}
	function get_focus()
	{
		$("#txtname").focus();
	}

	function get_id()//For get refdoctor Id
	{
		$.post("pages/health_guide_data.php",
		{
		  type:"super_cntermaster_id",	
		},
		function(data,status)
		{
			$("#txtid").val(data);
			get_focus();
		})

	}

	function val_load_new(doid1)
	{
		$.post("pages/health_guide_data.php",
		{
			type:"super_cntermaster_load",
			doid1:doid1,
		},
		function(data,status)
		{
			var val=data.split("#");
			document.getElementById("txtid").value=val[0];
			document.getElementById("txtname").value=val[1];
			document.getElementById("txtaddress").value=val[2];
			document.getElementById("txtphon").value=val[3];
			document.getElementById("txtemail").value=val[4];
		}
	)
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

	function clearr()
	{
		$(".intext").val("");
		$("#txtname").focus();
	}

	function Insert() //For Insert
	{
		///////////////For Check blanj Field
		var jj=1;
		var chk=document.getElementsByClassName("imp")
		for(var i=0;i<chk.length;i++)
		{
			if(chk[i].value=="")
			{
				document.getElementById(chk[i].id).placeholder="Can not be blank";
				jj=0;	
			}
			
		}
		
	   var vname=document.getElementById("txtname").value;
	   if(vname=="")
	   {
		   jj=0;
		   $("#txtname").focus();
	   }
	   
		
		if(jj==1)
		{
			///////end ////////
			
			$.post("pages/health_guide_data.php",
			{
				type:"super_cntermaster_insert",
				cid:$("#txtid").val(),
				cname:$("#txtname").val(),
				address:$("#txtaddress").val(),
				phone:$("#txtphon").val(),
				email:$("#txtemail").val(),
			},
			function(data,status)
			{
				bootbox.dialog({ message: "<b>Saved</b>"});
				setTimeout(function(){
					bootbox.hideAll();
					lod_refraldoctor();
				 }, 2000);
			})
		}
	}


	function delete_data(subp)//for delete
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to delete this test</h5>",
			buttons: {
				cancel: {
					label: '<i class="icon-remove"></i> Cancel',
					className: "btn btn-inverse",
					callback: function() {
					  bootbox.hideAll();
					}
				},
				confirm: {
					label: '<i class="icon-ok"></i> Delete',
					className: "btn btn-danger",
					callback: function() {
						$.post("pages/health_guide_data.php",
						{
							subp:subp,
							type:"super_cntermaster_delete",
						},
						function(data,status)
						{
							lod_refraldoctor();
						})
					}
				}
			}
		});
	}
	function caps_it(val,id,e)
	{
		$("#"+id).css({'border-color': 'rgba(82,168,236,0.8)'});
		var nval=val.toUpperCase();
		$("#"+id).val(nval);
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			if(id=="txtname")
			{
				$("#txtaddress").focus();
			}
			if(id=="txtaddress")
			{
				$("#txtphon").focus();
			}
		}
		var n=val.length;
		var numex=/^[A-Za-z0-9 ]+$/;
		if(val[n-1].match(numex))
		{
			
		}
		else
		{
			val=val.slice(0,n-1);
			document.getElementById(id).value=val;
		}
	}
	function phone_check(a,e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			$("#txtemail").focus();
		}
		var n=a.length;
		var numex=/^[0-9]+$/;
		if(a[n-1].match(numex))
		{
			
		}
		else
		{
			a=a.slice(0,n-1);
			document.getElementById("txtphon").value=a;
		}
	}
	function email(val,e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode==13)
		{
			Insert();
		}
		var n=val.length;
		var numex=/^[A-Za-z0-9.@]+$/;
		if(val[n-1].match(numex))
		{
			
		}
		else
		{
			val=val.slice(0,n-1);
			$("#txtemail").val(val);
		}
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

