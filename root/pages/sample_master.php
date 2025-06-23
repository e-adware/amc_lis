<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Sample Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="row">
		<div class="span5">
			<table class="table table-striped table-bordered table-condensed">
				<tr>
				  <td>Sample Id</td>
				  <td><label for="txtid"></label>
					<input type="text" name="txtid" id="txtid" class="imp"  readonly="readonly"  /></td>
				</tr>
				<tr>
				  <td> Name</td>
				  <td><label for="txtname"></label>
					<input type="text" name="txtname" id="txtname" class="imp" size="35" value="" autocomplete="off" /></td>
				</tr>
				<tr>
				  <td colspan="2" style="text-align:center" >
					  <input type="button" name="button" id="button" value="Submit" onclick="Insert();" class="btn btn-info" />
					  <input type="button" name="button2" id="button2" onclick="clearr();" value="Reset" class="btn btn-danger" />
					  <input type="button" name="button3" id="button3" onclick="popitup('pages/sample_master_print.php');" value="View" class="btn btn-info" />
				  </td>
				</tr>
			</table>
		</div>
		<div class="span6">
			<table class="table table-striped table-bordered table-condensed">
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
		get_id();
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
			$.post("pages/global_load.php",
			{
				val:val,
				type:"samplemastr",
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
	function lod_refraldoctor()
	{
		$.post("pages/global_load.php",
		{
			type:"samplemastr",
		},
		function(data,status)
		{
			$("#laod_doctor").html(data);
			clearr();
		})
	}
	function val_load_new(doid1)
	{
		$.post("pages/global_load.php",
		{
			type:"samplemastr_load",
			doid1:doid1,
		},
		function(data,status)
		{
			var val=data.split("@");
			document.getElementById("txtid").value=val[0];
			document.getElementById("txtname").value=val[1];
		 })
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
		//document.getElementById("txtid").value=document.getElementById("docid").value
	   get_id();
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
		if(jj==1)
		{
			///////end ////////
			
			$.post("pages/global_insert_data.php",
			{
				type:"samplemastr",
				smpid:document.getElementById("txtid").value,
				smpname:document.getElementById("txtname").value,
			},
			function(data,status)
			{
				bootbox.alert("Data Saved");
				clearr();
				lod_refraldoctor();
			})
		}
	}

	function get_id()
	{
		$.post("pages/global_load.php",
		{
			type:"samplemastr_load_id",
		},
		function(data,status)
		{
			$("#txtid").val(data);
		})
	}
	function delete_data(smplid)
	{
		bootbox.dialog({
			//title: "Patient Re-visit ?",
			message: "<h5>Are you sure want to delete</h5>",
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
					className: "btn btn-info",
					callback: function() {
						$.post("pages/global_delete.php",
						{
							type:"samplemastr",
							smplid:smplid,
						},
						function(data,status)
						{
							bootbox.alert("Data Delete");
							lod_refraldoctor();
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
