
		<style>
			
			#contenitore{
			position: relative;
			width: 85%;
			margin: 20px auto;
			text-align:center;
			overflow:hidden;
			font: 14px 'Trebuchet MS', sans serif;
			}
			.left{
			float:left;
			width:100%;
			}
			#grafico{
			position:relative;
			height:300px;
			border-left:2px solid #000000;
			border-bottom: 2px solid #000000;
			width:100%;
			margin-top:40px;
			margin-bottom:30px;
			margin-left:90px;
			}
			.riga{
			position:absolute;
			left: -20;
			height: 1px;
			margin-left:-8%;
			background-color:gray;
			width: 100%;
			color:#222222;
			}
			.riga div{
			float:left;
			margin:0px;
			}
			.canc{
			clear:both;
			}
			#graph_tbl{
			display: none;
			width:0%;
			background-color: white;
			color: #000;
			margin:  auto;
				
			}
			#graph_tbl caption{
			background-color: #D8EED8;
			color: #004156;
			text-align: left;
			}
			#graph_tbl tr:nth-child(2n){
			background-color: #D8EED8;
			}
			#graph_tbl tr:nth-child(2n+1){
			background-color: #BFDFFF;
			}
			#graph_tbl td{
			text-align:center;
			border-bottom: 1px solid #CDCDCD;
			padding: 6px;
			}
			.column{
			position:absolute;
			width: 10%;
			bottom: 0;
			background-color: #003366;
			color:#222222;
			//margin-left:5px;
			}
			div.button {
				margin: 0 auto;
				text-align: center;
				width: 100px;
				background-color:#003366;
				border: 1px solid #003366;
				border-radius: 5px;
				padding: 8px;
				color: #E1E2CF;
				cursor: pointer;
			}
			.column div{
			margin-top:-20px;
			height:20px;
			}
			.amt
			{
				left: 3%;
				position: absolute;
				bottom: 5px;
				font-size: 12px;
				display:none;
				color: #fff;
			}
			.bl
			{
				position: absolute;
				bottom: -20px;
				font-size: 12px;
				color:#222222;
			}
		</style>
		<div id="contenitore">
			<div class="">
				<table id="graph_tbl">
					<caption>Data Table</caption>
					<tbody>
					<tr class="tr_data"><td>A</td><td></td><td></td><td style="background-color:#185405">&nbsp;</td></tr>
					<tr class="tr_data"><td>B</td><td></td><td></td><td style="background-color:#185405">&nbsp;</td></tr>
					<tr class="tr_data"><td>C</td><td></td><td></td><td style="background-color:#185405">&nbsp;</td></tr>
					<tr class="tr_data"><td>D</td><td></td><td></td><td style="background-color:#185405">&nbsp;</td></tr>
					<tr class="tr_data"><td>E</td><td></td><td></td><td style="background-color:#185405">&nbsp;</td></tr>
					<tr class="tr_data"><td>F</td><td></td><td></td><td style="background-color:#185405">&nbsp;</td></tr>
					<tr class="tr_data"><td>G</td><td></td><td></td><td style="background-color:#185405">&nbsp;</td></tr>
					</tbody>
				</table>
			</div>
			<div class="">
				<?php
				function divide_number($max,$part)
				{
					$max_numm=$max;
					$temp=$max_numm;
					$parts=$part;
					$counts=($max_numm/$parts);
					//$counts=number_format($counts,0);
					$arr=array();
					for($loops=1; $loops<=$parts; $loops++)
					{
						$temp=$temp-$counts;
						if($loops==$parts)
						{
							$counts+=$temp;
						}
						array_push($arr,$temp);
					}
					return $arr;
					//print_r($arr);
				}
				
				$grph_val=mysqli_fetch_assoc(mysqli_query($link,"SELECT `levels`,`max_value`,`colors` FROM `ph_graph_set_up` ORDER BY `slno` DESC"));
				if($grph_val)
				{
					$max_val=$grph_val['max_value'];
				}
				else
				{
					$max_val=100000;
				}
				?>
				<div id="grafico">
					<div class="riga" style="top:0%;"><div><?php echo number_format($max_val,0);?></div></div>
					<?php
					if($grph_val)
					{
						$levels=$grph_val['levels'];
						$rr=divide_number($max_val,$levels);
						$top=array("","","","","","");
						$rrr=array_reverse($rr,true);
						$sz=sizeof($rrr);
						$kk=1;
						foreach($rrr as $r)
						{
							$tp=($r/$max_val)*100;
							$top[$sz-$kk]=$tp;
							$kk++;
						}
						$top=array_filter($top);
						$top=array_reverse($top,true);
						$k=0;
						foreach($rrr as $r)
						{
							if($r!=0)
							{
							?>
							<div class="riga" style="top:<?php echo $top[$k];?>%;"><div><?php echo number_format($r,0);?></div></div>
							<?php
							$k++;
							}
						}
					}
					else
					{
					?>
					<div class="riga" style="top:20%;"><div>80,000</div></div>
					<div class="riga" style="top:40%;"><div>60,000</div></div>
					<div class="riga" style="top:60%;"><div>40,000</div></div>
					<div class="riga" style="top:80%;"><div>20,000</div></div>
					<?php
					}
					$all_color=array("#185405","#185405","#185405","#185405","#185405","#185405","#185405");
					if($grph_val)
					{
						$all_color=array();
						$cl=explode("@@",$grph_val['colors']);
						foreach($cl as $c)
						{
							array_push($all_color,$c);
						}
					}
					?>
					<div id="col0" style="left:2%; background-color:<?php echo $all_color[0];?>;" class="column"></div>
					<div id="col1" style="left:14%; background-color:<?php echo $all_color[1];?>;" class="column"></div>
					<div id="col2" style="left:27%; background-color:<?php echo $all_color[2];?>;" class="column"></div>
					<div id="col3" style="left:41%; background-color:<?php echo $all_color[3];?>;" class="column"></div>
					<div id="col4" style="left:55%; background-color:<?php echo $all_color[4];?>;" class="column"></div>
					<div id="col5" style="left:68%; background-color:<?php echo $all_color[5];?>;" class="column"></div>
					<div id="col6" style="left:80%; background-color:<?php echo $all_color[6];?>;" class="column"></div>
					
					<div class="amt" id="amt0" style="left:3%;position:absolute;"></div>
					<div class="amt" id="amt1" style="left:15%;position:absolute;"></div>
					<div class="amt" id="amt2" style="left:28%;position:absolute;"></div>
					<div class="amt" id="amt3" style="left:42%;position:absolute;"></div>
					<div class="amt" id="amt4" style="left:56%;position:absolute;"></div>
					<div class="amt" id="amt5" style="left:69%;position:absolute;"></div>
					<div class="amt" id="amt6" style="left:81%;position:absolute;"></div>
					
					<div class="bl" id="vdt0" style="left:2%;position:absolute;">=====</div>
					<div class="bl" id="vdt1" style="left:14%;position:absolute;">=====</div>
					<div class="bl" id="vdt2" style="left:27%;position:absolute;">======</div>
					<div class="bl" id="vdt3" style="left:41%;position:absolute;">=====</div>
					<div class="bl" id="vdt4" style="left:55%;position:absolute;">=====</div>
					<div class="bl" id="vdt5" style="left:68%;position:absolute;">=====</div>
					<div class="bl" id="vdt6" style="left:80%;position:absolute;">=====</div>
				</div>
			</div>
			<div class="canc"></div>
		</div>

		<script>
			function load_prev_data()
			{
				$.post("pages/sales_graph_ajax.php"	,
				{
					type:"load_prev_data",
				},
				function(data,status)
				{
					var vl=data.split("#@#");
					for(var j=0; j<vl.length; j++)
					{
						var val=(vl[j]).split("@");
						var amt=val[0];
						var per=val[1];
						var dt=val[2];
						if(amt)
						{
							//alert(j);
							$(".tr_data:eq("+j+")").find('td:eq(1)').html(per);
							$(".tr_data:eq("+j+")").find('td:eq(2)').html(amt);
							$(".bl:eq("+j+")").html(dt);
						}
					}
					viewGraph();
					//$("#res").html(data);
				})
			}
			function viewGraph()
			{
				$('.column').css('height','0');
				$('#graph_tbl tr').each(function(index)
				{
					var ha = $(this).children('td').eq(1).text();
					var amt = $(this).children('td').eq(2).text();
					$('#col'+index).animate({height: ha}, 1500).html("<div>"+amt+"</div>");
					$('#amt'+index).html(amt);
				});
				$(".column div").fadeIn(1500);
				$(".amt").fadeIn(1500);
			}
			$(document).ready(function()
			{
				load_prev_data();
				//viewGraph();
			});
		</script>
