<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header">Pharmacy Item Receive</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<div class="row">
		<div class="span11">
			<div id="msgg" style="display:none;font-size: 20px;text-align: center;position: fixed;top: 8%;left: 50%;padding: 10px;padding-right: 10px;padding-left: 10px;border-radius: 5px;padding-right: 15px;padding-left: 15px;background: #FFFFFF;"></div>
			<div id="res" style="max-height:400px;overflow-y:scroll;">
			
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function()
	{
		setTimeout(function()
		{
			load_items();
		},300);
	});
	
	function rcv_confirm(ord)
	{
		//alert(ord);
		$("#btn"+ord).attr("disabled",true);
		var itm=$(".itm"+ord);
		var bch=$(".bch"+ord);
		//alert(itm.length);
		var all="";
		for(var j=0; j<(itm.length); j++)
		{
			var vl = itm[j].value+"@@"+bch[j].value;
			all+=vl+"#@#";
		}
		//alert(all);
		
		$.post("pages/ph_item_confirm_ajax.php",
		{
			ord:ord,
			all:all,
			user:$("#user").text().trim(),
			type:2,
		},
		function(data,status)
		{
			//alert(data);
			if(data=="1")
			{
				$("#msgg").text("Received");
				$("#msgg").css({"color":"#1F821C","box-shadow":"1px 1px 10px 3px #1F821C"});
			}
			if(data=="2")
			{
				$("#msgg").text("Error");
				$("#msgg").css({"color":"#C2010A","box-shadow":"1px 1px 10px 3px #DA1F23"});
			}
			$("#msgg").show();
			setTimeout(function()
			{
				$("#msgg").fadeOut(300);
			},500);
			load_items();
		})
	}
	
	function chk_btnn(ord)
	{
		var chk=$(".chk"+ord+":checked");
		if(chk.length>0)
		{
			$("#btn"+ord).attr("disabled",false);
		}
		else
		{
			$("#btn"+ord).attr("disabled",true);
		}
	}
	
	function load_items()
	{
		$.post("pages/ph_item_confirm_ajax.php",
		{
			type:1,
		},
		function(data,status)
		{
			$("#res").html(data);
			var tr=$(".cnf");
			var tm=1000;
			for(var i=0; i<tr.length; i++)
			{
				$(".cnf:eq("+i+") td").slideDown(tm);
				tm+=300;
			}
		})
	}
</script>
