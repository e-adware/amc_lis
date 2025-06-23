<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Item Type Master</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span5" style="margin-left:0px;">
		<form id="form1" method="post">
			<table class="table table-condensed table-bordered" >
				<tr>
					<td>Id</td>
					<td>
						<input type="text" name="txtcid" id="txtcid" value="" readonly="readonly" class="imp intext"/>
					</td>
				</tr>
				<tr>
					<td>Name</td>
					<td>
						<input type="text" name="txtcntrname"  id="txtcntrname" value="" autocomplete="off" class="imp intext span4" autofocus />
					</td>
				</tr>
				<tr>
					<td colspan="4" style="text-align:center">
						<input type="button" name="button2" id="button2" value="Reset" onclick="clearr();" class="btn btn-danger" style="width:100px"/> 
						<input type="button" name="button" id="button" value= "Submit" onclick="insert_data()" class="btn btn-default btn-info" style="width:100px"/>
						<!--<input type="button" name="button1" id="button1" value= "View" onclick="popitup1('pages/item_type_rpt.php')" class="btn btn-success" style="width:100px" />-->
					</td>
				</tr>
			</table>
		</form>
	</div>
	<div class="span5">
		<table class="table table-condensed table-bordered">
			<tr>
				<td>Search</td>
				<td> <input type="text" name="txtcustnm" size="30" id="txtcustnm"  autocomplete="off" class="intext" onkeyup="sel_pr(this.value,event)" /></td>
			</tr>
		</table>
		<div id="load_materil"  class="vscrollbar" style="max-height:400px;overflow-y:scroll;" >
			
		</div>
	</div>
	<div id="back"></div>
	<div id="results"></div>
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
<style>
	.itmtype:hover
	{
		color:#0000FF;
	}
</style>
<script>
	$(document).ready(function()
	{
		get_id();
		load_item();
		
		$("#txtcntrname").keyup(function(e)
		{
			$(this).css("border","");
			$(this).attr("placeholder","Cannot blank");
			$(this).val($(this).val().toUpperCase());
			if(e.keyCode==13)
			{
				if($(this).val()=="")
				$(this).css("border","1px solid #f00");
				else
				{
					$(this).css("border","");
					$("#button").focus();
				}
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
					$("#load_materil").scrollTop(doc_sc)
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
					$("#load_materil").scrollTop(doc_sc)
					
				}
			}	
	
		}
		else
		{
			$.post("pages/global_load_g.php",
			{
				val:val,
				type:"loaditemtype",
			
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
		document.getElementById("button").value='Submit';
		$("#txtcntrname").css('border','');
		get_id();
	} 
	
	function load_item()
	{
		$.post("pages/global_load_g.php",
		{
			type:"loaditemtype",
		},
		function(data,status)
		{
			$("#load_materil").html(data);
		})
	}
	
	function val_load_new(rid)///for retrieve data against center
	{
	  $.post("pages/global_load_g.php",
		{
			type:"edititemtype",
			rid:rid,
		},
		function(data,status)
		 {
			var val=data.split("@");
			document.getElementById("txtcid").value=val[0];
			document.getElementById("txtcntrname").value=val[1];
			$("#txtcntrname").css('border','');
			document.getElementById("button").value='Update';
			document.getElementById("txtcntrname").focus();
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
			type:"delete_item_type",
			rid:$("#idl").val(),
		},
		function(data,status)
		{
			bootbox.dialog({ message: data});
			setTimeout(function()
			{
				bootbox.hideAll();
				clearr();
			}, 1000);
			get_id();
			load_item();
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
			document.getElementById(chk[i].id).placeholder="Cannot blank";
			document.getElementById(chk[i].id).focus();
		}
		//---------------end------------------//
		if(jj==1)
		{
			$.post("pages/global_insert_data_g.php",
			{
				type:"saveitemtype",
				csid:document.getElementById("txtcid").value,
				csname:document.getElementById("txtcntrname").value,
			},
			function(data,status)
			{
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					clearr();
				}, 1000);
				load_item();
			})
		}
	}
	
	function get_id() //For Get Id
	{
		$.post("pages/global_load_g.php",
		{
			type:"itemtypeid",
		},
		function(data,status)
		{
			$("#txtcid").val(data);
			$("#txtcntrname").focus();
		})
	}
</script>
