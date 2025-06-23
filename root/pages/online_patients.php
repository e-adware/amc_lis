<!--header-->
<div id="content-header">
    <div class="header_div"><span class="header"><?= $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<input type="text" id="date" value="<?php echo date("Y-m-d");?>" readonly />
	<button type="button" class="btn" id="mybtn" onclick="col_dets()"><i class="icon-user"></i> View Patients</button>
	<div id="res">
		<table class="table table-condensed" id="mytable">
			<tr>
				<th>#</th>
				<th>Name</th>
				<th>Contact No</th>
				<th>Age / Sex</th>
				<th>Amount</th>
				<th>Tests</th>
				<th>Collection</th>
				<th>Register</th>
			</tr>
		</table>
	</div>
	<div id="loader" style="display:none;top:50%;position:fixed;z-index:99999;"></div>
</div>
<link rel="stylesheet" href="../css/loader.css" />
<!-- Time -->
<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<script>
	$(document).ready(function()
	{
		$("#date").datepicker({dateFormat: 'yy-mm-dd'});
	});
	function col_dets()
	{
		$("#mybtn").attr("disabled",true);
		$("#loader").show();
		var dt=$("#date").val().trim();
		$.get("https://us-central1-medicity2.cloudfunctions.net/medicity/labtests/collection/g1Z01N9bcl6YniicVlP1/"+dt,
		{
			
		},
		function(data,status)
		{
			//alert(data.success);
			$('#mytable tr:not(:first)').remove();
			if((data.success)=="1")
			{
				for(var i=0; i<(data.result.length); i++)
				{
					//alert(data.result[i].patPhone);
					var tr=document.createElement("tr");
					var td1=document.createElement("td");
					var td2=document.createElement("td");
					var td3=document.createElement("td");
					var td4=document.createElement("td");
					var td5=document.createElement("td");
					var td6=document.createElement("td");
					var td8=document.createElement("td");
					var td7=document.createElement("td");
					var td8=document.createElement("td");
					var tests="";
					
					td1.innerHTML=i+1;
					td2.innerHTML=data.result[i].patName;
					td3.innerHTML=data.result[i].patPhone;
					td4.innerHTML=data.result[i].patAge+" "+data.result[i].patSex;
					var test_all="";
					for(var j=0; j<(data.result[i].items.length); j++)
					{
						if(tests=="")
						{
							tests=(j+1)+". "+data.result[i].items[j].testName;
						}
						else
						{
							tests+="<br/>"+(j+1)+". "+data.result[i].items[j].testName;
						}
						test_all=test_all+"##"+data.result[i].items[j].testId+"@"+data.result[i].items[j].price+"@0";
					}
					//td5.innerHTML=(parseFloat(data.result[i].totalValue)).toFixed(2);
					td5.innerHTML=(data.result[i].totalValue).toFixed(2);
					td6.innerHTML=tests;
					td7.innerHTML=data.result[i].colCenterCode;
					var onclick="reg_pat('"+data.result[i].id+"','"+i+"')";
					td8.innerHTML="<input type='hidden' id='name"+i+"' value='"+data.result[i].patName+"' />";
					td8.innerHTML+="<input type='hidden' id='phone"+i+"' value='"+data.result[i].patPhone+"' />";
					td8.innerHTML+="<input type='hidden' id='address"+i+"' value='"+data.result[i].patAddrs+"' />";
					td8.innerHTML+="<input type='hidden' id='age"+i+"' value='"+data.result[i].patDob+"' />";
					td8.innerHTML+="<input type='hidden' id='sex"+i+"' value='"+data.result[i].patSex+"' />";
					td8.innerHTML+="<input type='hidden' id='c_no"+i+"' value='"+data.result[i].colCenterCode+"' />";
					td8.innerHTML+="<input type='hidden' id='tot"+i+"' value='"+data.result[i].totalValue+"' />";
					td8.innerHTML+="<input type='hidden' id='tests"+i+"' value='"+test_all+"' />";
					if(data.result[i].billNo==="")
					{
						td8.innerHTML+="<button type='button' class='btn btn-primary btn-mini' id='btn"+i+"' onclick="+onclick+">Register</button>";
					}
					else
					{
						td8.innerHTML+="<button type='button' class='btn btn-warning btn-mini' disabled>Registered</button>";
					}
					
					tr.appendChild(td1);
					tr.appendChild(td2);
					tr.appendChild(td3);
					tr.appendChild(td4);
					tr.appendChild(td5);
					tr.appendChild(td6);
					tr.appendChild(td7);
					tr.appendChild(td8);
					
					document.getElementById("mytable").appendChild(tr);
				}
			}
			$("#loader").hide();
			$("#mybtn").attr("disabled",false);
			//alert(JSON.stringify(data));
		});
	}
	function reg_pat(id,n)
	{
		//alert(id);
		bootbox.dialog({
			message: "<h5>Are you sure want to register this patient?</h5>",
			buttons: {
				cancel:
				{
					label: '<i class="icon-remove"></i> No',
					className: "btn btn-inverse",
					callback: function()
					{
					  bootbox.hideAll();
					}
				},
				confirm:
				{
					label: '<i class="icon-ok"></i> Yes',
					className: "btn btn-danger",
					callback: function()
					{
						//alert($("#name"+n).val());
						$("#btn"+n).attr("disabled",true);
						$("#loader").show();
						$.post("pages/new_lab_registration_data.php",
						{
							branch_id:"1",
							center_no:$("#c_no"+n).val(),
							p_type_id:"2",
							patient_id:"0",
							opd_id:"0",
							pat_reg_type:"0",
							name_title:"",
							pat_name:$("#name"+n).val().trim(),
							dob:$("#age"+n).val().trim(),
							sex:$("#sex"+n).val().trim(),
							phone:$("#phone"+n).val().trim(),
							address:$("#address"+n).val().trim(),
							test_all:$("#tests"+n).val().trim(),
							total:$("#tot"+n).val().trim(),
							opd_now_pay:"0",
							opd_now_payment_mode:"1",
							user:$("#user").text().trim(),
							type:"pat_save",
						},
						function(data,status)
						{
							$("#loader").hide();
							//alert(data);
							var res=data.split("@");
							$("#patient_id").val(res[0]);
							$("#opd_id").val(res[1]);
							bootbox.dialog({ message: res[3]});
							setTimeout(function()
							{
								bootbox.hideAll();
							}, 1000);
							$.post("https://us-central1-medicity2.cloudfunctions.net/medicity/labtests/collection/update",
							{
							"authKey": "g1Z01N9bcl6YniicVlP1",
							"data":
								{
									"id": id,
									"billNo": res[1],
									"billDate": new Date(),
									"billUrl": "https://penguinhis.in/penguinhis/root/pages/money_reciept_pdf.php?v=0&uhid="+btoa(res[0])+"&opdid="+btoa(res[1])+"&user="+btoa($("#user").text().trim())
								}
							},
							function(data,status)
							{
								//alert(JSON.stringify(data));
								col_dets();
							});
						})
					}
				}
			}
		});
	}
</script>
