<html>
<head>
</head>
<body>
<b><i>Close this page when printing is done</i></b>
<?php
include("../../includes/connection.php");
$vno=$_GET['blno'];
$fid=$_GET['fid'];
$date1=$_GET['date1'];

//echo "select a.*,b.item_name from sell_details a,item_master b where a.item_code=b.item_code and a.bill_no='$vno' and a.FID='$fid'";
function no_to_words($no)
{
    $words = array('0'=> '' ,'1'=> 'one' ,'2'=> 'two' ,'3' => 'three','4' => 'four','5' => 'five','6' => 'six','7' => 'seven','8' => 'eight','9' => 'nine','10' => 'ten','11' => 'eleven','12' => 'twelve','13' => 'thirteen','14' => 'fourteen','15' => 'fifteen','16' => 'sixteen','17' => 'seventeen','18' => 'eighteen','19' => 'nineteen','20' => 'twenty','30' => 'thirty','40' => 'fourty','50' => 'fifty','60' => 'sixty','70' => 'seventy','80' => 'eighty','90' => 'ninty','100' => 'hundred ','1000' => 'thousand','100000' => 'lakh','10000000' => 'crore');
    if($no == 0)
    return ' ';
    else {
    $novalue='';
    $highno=$no;
    $remainno=0;
    $value=100;
    $value1=1000;
    while($no>=100) {
    if(($value <= $no) &&($no < $value1)) {
    $novalue=$words["$value"];
    $highno = (int)($no/$value);
    $remainno = $no % $value;
    break;
    }
    $value= $value1;
    $value1 = $value * 100;
    }
    if(array_key_exists("$highno",$words))
    return $words["$highno"]." ".$novalue." ".no_to_words($remainno);
    else {
    $unit=$highno%10;
    $ten =(int)($highno/10)*10;
    return $words["$ten"]." ".$words["$unit"]." ".$novalue." ".no_to_words($remainno);
    }
    }
}

$inv="Invoice No:".$vno;
$date="    Date:".$date1;

$info="\t\t\tM/S SURAKSHA MEDICARE PVT LTD";
$info1="\t\t\t  C/O Midland Hospital";
$info2="\t\t\tSreenagar,Near Bora Market,GHY-6";
$info3="\t\tDL No: KMP 14514/15 , TIN No :18170210978";

$ccname=mysql_query("select * from sell_master where bill_no='$vno' and FID='$fid'");
$ccname1=mysql_fetch_array($ccname);


$cname="Name:".$ccname1['customer_name']."\t\t Cabin No:".$ccname1['room_no'];
$line="------------------------------------------------------------------------------";
$t_head="# | Item Name                          | Exp.date | Qnty |   MRP | Total Amount";

//$table=mysql_query("select * from sell_master where bill_no='$vno' and 	FID='$fid'");


$tot=mysql_fetch_array(mysql_query("select * from sell_master where bill_no='$vno' and 	FID='$fid'"));
$vaftrdis=$tot['total_amt']-$tot['discount_amt'];
$tot1="\t\t\t\t\t\t\tTotal       :".round($tot[total_amt]).".00";
$dis="\t\t\t\t\t\t\tDiscount     :".$tot[discount_amt];
$gtot="\t\t\t\t\t\t\tGrand Total :".round($vaftrdis).".00";
$text_tot="\t\tRupees ".no_to_words(round($vaftrdis))."Only";

?>
<script>
function printt()
{
	
	var applet=document.jzebra;
	
	
	applet.append("<?php echo $info;?>"+"\n");
	applet.append("<?php echo $info1;?>"+"\n");
	applet.append("<?php echo $info2;?>"+"\n");
	applet.append("<?php echo $info3;?>"+"\n");

        applet.append("\n"+"<?php echo $inv;?>"+"\t\t\t\t\t\t");
	applet.append("<?php echo $date;?>"+"\n");

	applet.append("<?php echo $cname;?>"+"\n");
	applet.append("<?php echo $line;?>"+"\n");
	applet.append("<?php echo $t_head;?>"+"\n");
	applet.append("<?php echo $line;?>"+"\n");
	
	<?php
	$table=mysql_query("select a.*,b.item_name from sell_details a,item_master b where a.item_code=b.item_code and a.bill_no='$vno' and a.FID='$fid'");
	$i=1;
	while($tab=mysql_fetch_array($table))
	{
		?>
				var tgap=2;
				<?php
				if($i>9)
				{
					?>
							tgap=1;
					<?php	
				}
				?>	
		var space1=" ";
		for(var i=0;i<=tgap;i++)
		{
			space1=space1+" ";
		}
		
				
		var name_b="<?php echo $tab[item_name];?>";		
		var n_len=name_b.length;
		var n_len2=37-n_len;
			
			var tgap2=" "; 
			for(var i=0;i<n_len2;i++)
			{
				tgap2=tgap2+" ";
			}
	
		var tgap3=5;
		<?php
		if($tab[sale_qnt]>9)
		{
			?>
					tgap3=4;
			<?php	
		}
		?>

		var exp_d="<?php echo $tab[expiry_date];?>";
		var x_len=exp_d.length;
		var x_len2=8-x_len;
		var x_gap=" ";
		for(var i=0;i<x_len2;i++)
		{
			x_gap=x_gap+" ";
		}



		var space2=" ";
		for(var i=0;i<=tgap3;i++)
		{
			space2=space2+" ";
		}
		
		var mrp="<?php echo $tab[mrp];?>";
		var m_length=mrp.length;
		var n_len3=12-m_length;

		var tgap3=" "; 
		for(var i=0;i<n_len3;i++)
		{
			tgap3=tgap3+" ";
		}



		applet.append("<?php echo $i;?>"+space1+name_b+tgap2+exp_d+x_gap+"<?php echo $tab[sale_qnt];?>"+tgap3+"<?php echo $tab[mrp];?>"+space2+"<?php echo $tab[total_amount];?>"+"\n");
			
	<?php
	$i++;
	}	
	?>
	
	
	applet.append("<?php echo $line;?>"+"\n");
	applet.append("<?php echo $tot1;?>"+"\n");
	applet.append("<?php echo $dis;?>"+"\n");
	applet.append("<?php echo $gtot;?>"+"\n");
	applet.append("<?php echo $text_tot;?>"+"\n");
	
	applet.append("\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n");
	applet.append("\n\n\n\n\n\n\n\n\n\n\n\n");
	
	
	applet.print();
	
}
</script>

<applet name="jzebra" code="jzebra.PrintApplet.class" archive="./jzebra.jar" width="10px" height="10px">
      


      <param name="printer" value="zebra">
	
      
   </applet>
   
   <script>printt()</script>

</body>
</html>
