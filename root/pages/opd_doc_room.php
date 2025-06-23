<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="row">
		<div class="span7">
			<table class="table   table-bordered table-condensed">
				<tr>
					<td> Room ID</td>
					<td ><input type="text" name="txtid" id="txtid" class="imp"  readonly="readonly"  /></td>
					<input type="hidden" name="txtlgid" id="txtlgid" value="" autocomplete="off" />
				</tr>
				<tr>
					<td >Room Name</td>
					<td ><input type="text" name="intext1" id="txtname" class="intext" size="35" value="" autocomplete="off" onKeyup="room_name(this.value)" /></td>
				</tr>
					<td colspan="2" style="text-align:center" >
						<input type="button" name="intext18" id="button" value="Submit" onclick="Insert();" class="btn btn-info" />
						<input type="button" name="button2" id="button2" onclick="clearr();" value="Reset" class="btn btn-danger" />
						<input type="button" name="button3" id="button3" onclick="popitup('pages/opd_room_list_rpt.php');" value="View" class="btn btn-success" />
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
					<td>Room Name</td>
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
		get_id();
	});
	function room_name(val)
	{
		var nval=val.toUpperCase();
		$("#txtname").val(nval);
		var n=val.length;
		if(n>0)
		{
			var numex=/^[A-Za-z0-9 ]+$/;
			if(val[n-1].match(numex))
			{
				
			}
			else
			{
				val=val.slice(0,n-1);
				document.getElementById('txtname').value=val;
			}
		}
	}
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
			$.post("pages/global_load.php",
			{
				val:val,
				type:"opd_room",
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
		url=url+"?date1="+txtfrom+"&date2="+txtto;
		newwindow=window.open(url,'window','left=10,top=10,height=600,witdh=600,menubar=1,resizeable=0,scrollbars=1');
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
		$.post("pages/global_load.php",
		{
			type:"opd_room",
		},
		function(data,status)
		{
			$("#laod_doctor").html(data)
			
		})
	}
	function get_focus()
	{
		$("#txtname").focus();
	}

	function get_id()//For get refdoctor Id
	{
		$.post("pages/global_load.php",
		{
		  type:"opd_room_id",	
		},
		function(data,status)
		{
			$("#txtid").val(data);
			get_focus();
		})

	}

	function val_load_new(doid1)
	{
		$.post("pages/global_load.php",
		{
			type:"opd_room_load",
			doid1:doid1,
		},
		function(data,status)
		{
			var val=data.split("#");
			document.getElementById("txtid").value=val[0];
			document.getElementById("txtname").value=val[1];
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
	var a=document.getElementsByTagName("input");
	for(var i=0;i<a.length;i++)
	{
		if(a[i].type=="text")
		{
			if(i==0 )
			{
			}
			else
			{
			a[i].value=""; 
			
			}
		}
	}

	   get_id();
	  
	  
	   
	}

	function Insert() //For Insert
	{
		///////////////For Check blank Field
		var jj=1;
		
	   var vname=document.getElementById("txtname").value;
	   if(vname=="")
	   {
		   alert("Please enter the Room Name..");
		   jj=0;
		   $("#txtname").focus();
	   }
	   
		
		if(jj==1)
		{
			///////end ////////
			
			$.post("pages/global_insert_data.php",
			{
				type:"opd_room",
				cid:$("#txtid").val(),
				cname:$("#txtname").val(),
			},
		 function(data,status)
		  {
					
			alert("Data Saved");
			clearr();
			lod_refraldoctor();
		  }
	   )
	 }
	}


	function delete_data(subp)//for delete
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to delete this</h5>",
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
						$.post("pages/global_delete.php",
						{
							subp:subp,
							type:"opd_room",
						},
						function(data,status)
						{
							lod_refraldoctor();
							get_id();
							clearr();
						})
					}
				}
			}
		});
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
