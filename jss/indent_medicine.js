var med_tr=1;
var med_sc=0;
function load_ind_medi()
{
	//$("#ind_med_list").fadeIn(500);
	setTimeout(function(){$("#chk_val1").val(1)},1000);
	$("html,body").animate({scrollTop: '620px'},1000);
}
function load_ind_medi1(val,e)
{
	$("html,body").animate({scrollTop: '620px'},1000);
		setTimeout(function(){$("#ind_med_list").css('height','400px');},300);
		var unicode=e.keyCode? e.keyCode : e.charCode;
		if(unicode!=13)
		{
			if(unicode!=40 && unicode!=38)
			{
				$("#ind_med_list").html("<img src='../images/ajax-loader.gif' />");
				$("#ind_med_list").fadeIn(500);
				$.post("pages/nursing_load_g.php"	,
				{
					val:val,
					type:"medicine_list",
				},
				function(data,status)
				{
					$("#ind_med_list").html(data);	
					med_tr=1;
					med_sc=0;
				})	
			}
			else if(unicode==40)
			{
				var chk=med_tr+1;
				var cc=document.getElementById("ind"+chk).innerHTML;
				if(cc)
				{
					med_tr=med_tr+1;
					$("#ind"+med_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var med_tr1=med_tr-1;
					$("#ind"+med_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=med_tr%1;
					if(z3==0)
					{
						$("#ind_med_list").scrollTop(med_sc)
						med_sc=med_sc+30;
					}
				}
			}
			else if(unicode==38)
			{
				var chk=med_tr-1;
				var cc=document.getElementById("ind"+chk).innerHTML;
				if(cc)
				{
					med_tr=med_tr-1;
					$("#ind"+med_tr).css({'color': '#419641','transform':'scale(0.95)','font-weight':'bold','transition':'all .2s'});
					var med_tr1=med_tr+1;
					$("#ind"+med_tr1).css({'color': 'black','transform':'scale(1.0)','font-weight':'normal','transition':'all .2s'});
					var z3=med_tr%1;
					if(z3==0)
					{
						med_sc=med_sc-30;
						$("#ind_med_list").scrollTop(med_sc)
					}
				}
			}
			
		}
		else
		{
			var cen_chk1=document.getElementById("chk_val1").value;
			if(cen_chk1!=0)
			{
				var docs=document.getElementById("mdname"+med_tr).innerHTML.split("#");
				var doc_naam=docs[2].trim()
				$("#ind_med").val(doc_naam);
				select_med_ind(docs[1],docs[2]);
				$("#ind_med_list").fadeOut(300);
				$("#qnt").val('');
				var d_in=docs[5];
			}
		}
}
function select_med_ind(id,name)
{
	$("#ind_med").val(name.trim());
	$("#mediid").val(id);
	$("#ind_med_list").fadeOut(300);
	select_ind_data();
}
function select_ind_data()
{
	$("#ind_data").show();
	$("#qnt").focus();
}

function add_ind_data()
{
	var medid=$("#mediid").val();
	var medi=$("#ind_med").val();
	var qnt=$("#qnt").val();
	var med_add= $('#ind_tbl tr').length;
	if(qnt=="" || (parseInt(qnt))==0)
	{
		$("#qnt").focus();
	}
	else
	{
		if(med_add==0)
		{
			var tbl="<table class='table table-condensed table-bordered' style='style:none' id='ind_tbl'>";
			tbl+="<tr><th style='width:5%;background-color:#cccccc'>SN<span style='display:none;position:fixed;font-size:22px;top:30%;left:40%;color:#e00;' id='msgg'></span></th><th style='width:80%;background-color:#cccccc'>Drugs</th><th style='background-color:#cccccc'>Quantity</th><th style='width:5%;background-color:#cccccc'><i class='icon-trash icon-large'></i></span></th></tr>";
			tbl+="<tr id='tr0' class='"+medid+" ind'><td>1</td><td>"+medi+"<input type='hidden' value='"+medid+"' class='m_val'/></td><td>"+qnt+"<input type='hidden' value='"+qnt+"' class='m_val'/><td><span class='text-danger' onclick='$(this).parent().parent().remove()' style='cursor:pointer'><i class='icon-remove'></i></span></td></tr>";
			tbl+="</table>";
			
			$("#select_load").html(tbl);
			med_add++;
			setTimeout(function(){$("#ind_med").focus()},500);
			$("#ind_med").val('');
			$("#qnt").val('');
			$("#ind_med_list").fadeOut(300);
		}
		else
		{
			var medid=$("#mediid").val();
			var medi=$("#ind_med").val();
			var qnt=$("#qnt").val();
			var t_ch=0;
			var med_l=$("."+medid);
			if(med_l.length>0)
			{
				t_ch=1;
			}
			else
			{
				t_ch=0;
			}
			if(t_ch==1)
			{
				$("#ind_tbl").css({'opacity':'0.5'});
				$("#msgg").text("Already Selected");
				$("#msgg").fadeIn(500);
				setTimeout(function(){$("#msgg").fadeOut(500,function(){$("#ind_tbl").css({'opacity':'1.0'});$("#ind_med").focus()})},600);	
			}			
			else
			{
				var ln=med_add-1;
				var tr=document.createElement("tr");
				tr.setAttribute("id","tr"+ln);
				tr.setAttribute("class","ind "+medid);
				var td=document.createElement("td");
				var td1=document.createElement("td");
				var td2=document.createElement("td");
				var td3=document.createElement("td");
				var tbody=document.createElement("tbody");
				td.innerHTML=med_add;
				td1.innerHTML=medi+"<input type='hidden' value='"+medid+"' class=''/>";
				td2.innerHTML=qnt+"<input type='hidden' value='"+qnt+"' class=''/>";
				td3.innerHTML="<span class='text-danger' onclick='$(this).parent().parent().remove()' style='cursor:pointer'><i class='icon-remove'></i></span>";
				tr.appendChild(td);
				tr.appendChild(td1);
				tr.appendChild(td2);
				tr.appendChild(td3);
				tbody.appendChild(tr);
				//alert(tbody);
				
				document.getElementById("ind_tbl").appendChild(tbody);
				$("#ind_med").val('');
				$("#qnt").val('');
				setTimeout(function(){$("#ind_med").focus()},500);
				$("#ind_med_list").fadeOut(300);
			}
		}
		$('#ins_ind').show();
	}
}

function insert_final_ind()
{
	var tr= $('#ind_tbl tr.ind').length;
	var det="";
	//alert(tr);
	for(var i=0;i<tr;i++)
	{
		$("#tr"+i).find('td:first input:first').val()
		det+=$("#tr"+i).find('td:eq(1) input:first').val()+"@@"+$("#tr"+i).find('td:eq(2) input:first').val()+"@@#gg#";
	}
	//alert(det);
	$.post("pages/nursing_load_g.php",
	{
		uhid:$("#uhid").val(),
		ipd:$("#ipd").val(),
		det:det,
		usr:$("#user").text().trim(),
		type:"insert_final_ind",
	},
	function(data,status)
	{
		medicine_indent();
	})
}
