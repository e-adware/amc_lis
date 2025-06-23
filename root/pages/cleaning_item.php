<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Cleaning Item</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="row">
		<div class="span5">
			<table class="table table-striped table-bordered table-condensed">
				<tr>
					<th>Item ID</th>
					<td>
						<label for="item_id"></label>
						<input type="text" name="item_id" id="item_id" class="imp" readonly>
					</td>
				</tr>
				<tr>
					<th>Item Name</th>
					<td>
						<label for="item_name"></label>
						<input type="text" name="item_name" id="item_name" class="imp" size="35"  autocomplete="off" autofocus />
					</td>
				</tr>
				<tr>
					<td colspan="2" style="text-align:center" >
						<input type="button" name="button" id="button" value="Submit" onclick="Insert()" class="btn btn-info" />
						<input type="button" name="button2" id="button2" onclick="clearr()" value="Reset" class="btn btn-danger" />
						<input type="button" name="button3" id="button3" onclick="popitup('pages/cleaning_item_print.php')" value="View" class="btn btn-info" />
					</td>
				</tr>
			</table>
		</div>
		<div class="span6">
			<table class="table table-striped table-bordered table-condensed">
				<tr>
					<th>Item Name</th>
					<td colspan="2"><input type="text" id="txtdoc" size="35" onkeyup="sel_pr(this.value,event)"/></td>
				</tr>
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Delete</th>
				</tr>
			</table>
			<div style="height:350px; overflow-x:hidden" id="laod_item">
				
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		get_id();
		load_item();
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
				$("#rad_test"+doc_v).css({'font-weight':'bold','color': 'green','transform':'scale(0.95)','transition':'all .2s'});
				var doc_v1=doc_v-1;
				$("#rad_test"+doc_v1).css({'font-weight':'normal','color': 'black','transform':'scale(1.0)','transition':'all .2s'});
				var z2=doc_v%3;
				if(z2==0)
				{
					$("#laod_item").scrollTop(doc_sc)
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
				$("#rad_test"+doc_v).css({'font-weight':'bold','color': 'green','transform':'scale(0.95)','transition':'all .2s'});
				var doc_v1=doc_v+1;
				$("#rad_test"+doc_v1).css({'font-weight':'normal','color': 'black','transform':'scale(1.0)','transition':'all .2s'});
				var z2=doc_v%3;
				if(z2==0)
				{
					doc_sc=doc_sc-90;					
					$("#laod_item").scrollTop(doc_sc)		
				}
			}
		}
		else
		{
			$.post("pages/global_load.php",
			{
				val:val,
				type:"cleaning_item_load",
			},
			function(data,status)
			{
				$("#laod_item").html(data);
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
	function load_item()
	{
		$.post("pages/global_load.php",
		{
			type:"cleaning_item",
		},
		function(data,status)
		{
			$("#laod_item").html(data);
			clearr();
		})
	}
	function val_load_new(doid1)
	{
		$.post("pages/global_load.php",
		{
			type:"cleaning_item_load",
			doid1:doid1,
		},
		function(data,status)
		{		
			var val=data.split("@");
			document.getElementById("item_id").value=val[0];
			document.getElementById("item_name").value=val[1];
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
		$("#item_name").focus();
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
				type:"cleaning_item",
				item_id:$("#item_id").val(),
				item_name:$("#item_name").val(),
			},
			function(data,status)
			{
				bootbox.dialog({ message: "<h5>Saved</h5>"});
				setTimeout(function(){
					bootbox.hideAll();
					clearr();
					load_item();
				},1000);
			})
		}
	}
	function get_id()
	{
		$.post("pages/global_load.php",
		{
			type:"cleaning_item_id",
		},
		function(data,status)
		{
			$("#item_id").val(data);
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
					className: "btn btn-danger",
					callback: function() {
						$.post("pages/global_delete.php",
						{
							type:"cleaning_item",
							smplid:smplid,
						},
						function(data,status)
						{
							bootbox.dialog({ message: "<h5>Deleted</h5>"});
							setTimeout(function(){
								bootbox.hideAll();
								clearr();
								load_item();
							},1000);
						})
					}
				}
			}
		});
	}

</script>
