<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Screening Details</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="span6" style="margin-left:0px;">
		<table class="table table-bordered table-condensed">
			<tr>
				<th>Enter Barcode</th>
				<td>
					<input type="text" id="bar" onkeyup="if(/\D/g.test(this.value))this.value=this.value.replace(/\D/g,'')" placeholder="Bar Code Number" autofocus />
					<input type="button" id="srch" class="btn btn-info" onclick="show_donor()" value="Search" />
				</td>
			</tr>
			<tr>
				<th>Donor Name</th>
				<td>
					<input type="text" id="did" style="display:none;" />
					<input type="text" id="donor" placeholder="Donor Name" disabled="disabled" />
				</td>
			</tr>
			<tr>
				<th>ABO</th>
				<td>
					<select id="abo">
						<option value="0">Select</option>
						<option value="A">A</option>
						<option value="B">B</option>
						<option value="AB">AB</option>
						<option value="O">O</option>
					</select>
				</td>
			</tr>
			<tr>
				<th>Rh</th>
				<td>
					<select id="rh">
						<option value="0">Select</option>
						<option value="positive">Positive</option>
						<option value="negative">Negative</option>
					</select>
				</td>
			</tr>
			<tr>
				<th>HIV</th>
				<td>
					<select id="hiv">
						<option value="0">Select</option>
						<option value="positive">Positive</option>
						<option value="negative">Negative</option>
					</select>
				</td>
			</tr>
			<tr>
				<th>Hep-B</th>
				<td>
					<select id="hepb">
						<option value="0">Select</option>
						<option value="positive">Positive</option>
						<option value="negative">Negative</option>
					</select>
				</td>
			</tr>
			<tr>
				<th>Hep-C</th>
				<td>
					<select id="hepc">
						<option value="0">Select</option>
						<option value="positive">Positive</option>
						<option value="negative">Negative</option>
					</select>
				</td>
			</tr>
			<tr>
				<th>Mp</th>
				<td>
					<select id="mp">
						<option value="0">Select</option>
						<option value="positive">Positive</option>
						<option value="negative">Negative</option>
					</select>
				</td>
			</tr>
			<tr>
				<th>VDRL</th>
				<td>
					<select id="vdrl">
						<option value="0">Select</option>
						<option value="positive">Positive</option>
						<option value="negative">Negative</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center">
					<input type="button" class="btn btn-danger" value="Reset" onclick="clrr()" />
					<input type="button" class="btn btn-info" value="Save" onclick="save()" />
				</td>
			</tr>
		</table>
	</div>
</div>
<style>
	select{border-radius:15px;border:1px solid #999999;}
</style>
<script>
	$(document).ready(function()
	{
		$("#bar").keyup(function(e)
		{
			$("#did").val('');
			$("#donor").val('');
			$("#donor").css("border","");
			$("#donor").attr("placeholder","Donor Name");
			if(e.keyCode==13)
			{
				if($(this).val()!="")
				$("#srch").focus();
			}
		});
	});
	function show_donor()
	{
		$.post("pages/global_load_g.php",
		{
			did:$("#did").val(),
			bar:$("#bar").val(),
			type:"show_donor_screw",
		},
		function(data,status)
		{
			var vl=data.split("#@#");
			$("#did").val(vl[0]);
			$("#donor").val(vl[1]);
			$("#abo").val(vl[2]);
			$("#rh").val(vl[3]);
			$("#hiv").val(vl[4]);
			$("#hepb").val(vl[5]);
			$("#hepc").val(vl[6]);
			$("#mp").val(vl[7]);
			$("#vdrl").val(vl[8]);
			$("#abo").focus();
		})
	}
	function save()
	{
		if($("#did").val()=="")
		{
			$("#donor").css("border","1px solid #ff0000");
			$("#donor").attr("placeholder","Cannot Blank");
			$("#bar").focus();
		}
		else if($("#abo").val()=="0")
		{
			$("#abo").focus();
		}
		else if($("#rh").val()=="0")
		{
			$("#rh").focus();
		}
		else if($("#hiv").val()=="0")
		{
			$("#hiv").focus();
		}
		else if($("#hepb").val()=="0")
		{
			$("#hepb").focus();
		}
		else if($("#hepc").val()=="0")
		{
			$("#hepc").focus();
		}
		else if($("#mp").val()=="0")
		{
			$("#mp").focus();
		}
		else if($("#vdrl").val()=="0")
		{
			$("#vdrl").focus();
		}
		else
		{
			$.post("pages/global_insert_data_g.php",
			{
				did:$("#did").val(),
				bar:$("#bar").val(),
				abo:$("#abo").val(),
				rh:$("#rh").val(),
				hiv:$("#hiv").val(),
				hepb:$("#hepb").val(),
				hepc:$("#hepc").val(),
				mp:$("#mp").val(),
				vdrl:$("#vdrl").val(),
				usr:$("#user").text().trim(),
				type:"save_donor_screw",
			},
			function(data,status)
			{
				bootbox.dialog({ message: data});
				setTimeout(function()
				{
					bootbox.hideAll();
					clrr();
				}, 1000);
			})
		}
	}
	function clrr()
	{
		$("#did").val('');
		$("#donor").val('');
		$("#bar").val('');
		$("#abo").val('0');
		$("#rh").val('0');
		$("#hiv").val('0');
		$("#hepb").val('0');
		$("#hepc").val('0');
		$("#mp").val('0');
		$("#vdrl").val('0');
		$("#bar").focus();
	}
</script>
