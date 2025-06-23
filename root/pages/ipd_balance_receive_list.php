<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> Balance Patient List</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<b>Search Patient</b>
	<!--<input type="text" id="search_data" onkeyup="search(this.value)" placeholder="Search Patient">-->
	<input type="text" id="search_data" onkeyup="view_all()" placeholder="Search Patient Bill No">
	<div id="load_all" class="ScrollStyle">
		
	</div>
</div>
<script>
	$(document).ready(function(){
		view_all();
	});
	function view_all()
	{
		$.post("pages/ipd_balance_receive_data.php",
		{
			type:"load_all_pat",
			search_data:$("#search_data").val(),
		},
		function(data,status)
		{
			$("#load_all").html(data);
			setTimeout(function(){
				//view_all();
			},2000);
		})
	}
	function redirect_ipd_balance(uhid,ipd)
	{
		//alert(uhid+" "+pin);
		//bootbox.dialog({ message: "<b>Redirecting to IPD balance receive</b> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/> <img src='../images/loading.gif' height='10' width='30'/>"});
		window.location="processing.php?param=115&uhid="+uhid+"&ipd="+ipd;
	}
	function search(inputVal)
	{
		var table = $('#tblData');
		table.find('tr').each(function(index, row)
		{
			var allCells = $(row).find('td');
			if(allCells.length > 0)
			{
				var found = false;
				allCells.each(function(index, td)
				{
					var regExp = new RegExp(inputVal, 'i');
					if(regExp.test($(td).text()))
					{
						found = true;
						return false;
					}
				});
				if(found == true)
				{
					$("#no_record").text("");
					$(row).show();
				}else{
					$(row).hide();
					var n = $('tr:visible').length;
					if(n==1)
					{
						$("#no_record").text("No matching records found");
					}else
					{
						$("#no_record").text("");
					}
				}
				//if(found == true)$(row).show();else $(row).hide();
			}
		});
	}
</script>
<style>
.ScrollStyle
{
    max-height: 400px;
    overflow-y: scroll;
}
</style>
