<html>
<head>
</head>
<body>
<b><i>Close this page when printing is done</i></b>
<?php
include("../../includes/connection.php");
$bill=$_GET['billno'];


$cus=mysqli_fetch_array(mysqli_query($link,"select * from `ph_sell_master` where `bill_no`='$bill'"));
$qipd=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where opd_id='$cus[ipd_id]'"));

$date1=date('Y-m-d');
if($cus['bill_type_id']==2)
{
	$vmnyrcpt="\t\t\t\t\Receipt Memo";
}
else
{
	$vmnyrcpt="\t\t\t\t\Receipt Memo";
}

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
$company_info=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `company_name` limit 0,1 "));
$cer=mysqli_fetch_array(mysqli_query($link, " SELECT `gst` FROM `company_documents` limit 0,1 "));
$signature="\t\t\t\t\tFor ".$company_info['name'];
$phon="";
if($company_info["phone1"])
$phon.=$company_info["phone1"];
if($company_info["phone2"])
$phon.=", ".$company_info["phone2"];
if($company_info["phone3"])
$phon.=", ".$company_info["phone3"];
$company_name="                         ".$company_info["name"];
$header2="                       ".$company_info["address"].", ".$company_info["city"].", ".$company_info["state"]."-".$company_info["pincode"];
$header3="                       Phone Number(s): ".$phon."Email: ".$company_info["email"];

$inv="Invoice No:".$bill;
$date="    Date:".$date1;
$vcabin="*";


$q=mysqli_fetch_array(mysqli_query($link,"select  receipt_no,amount,sl_no from ph_credit_payment_details  where bill_no='$bill' order by sl_no desc"));

//$info3="\t\tGSTIN :".$cer['gst']." , DL No: 00000 ";
$info3="\t\t\tGSTIN :".$cer['gst'];


$pinfo="Patient Name      :".$cus['customer_name'];
$namegp="";
$vsize=strlen($cus['customer_name']);
$nsize=36-$vsize;
for ($i=0;$i<$nsize;$i++)
{
	$namegp.=" ";
}
$vdoc=$cus['entry_date'];
$vdoclnght=strlen($vdoc);
$docsize=16-$vdoclnght;
for ($i=0;$i<$docsize;$i++)
{
	$docgap.=" ";
}
$vrno=$q['receipt_no'];
$vrnolnght=strlen($vrno);
$rnosize=6-$vrnolnght;
for ($i=0;$i<$rnosize;$i++)
{
	$rnogap.=" ";
}

$pinfo.=$namegp;
$pinfo.="Bill No:".$bill;
$cname="Entry Date        :".$cus['entry_date'].$docgap." Receipt No:".$q[receipt_no].$rnogap."  Receipt Date :".$date1;
$line="------------------------------------------------------------------------------";


$vgstamt1=0;
$vgstamt=0;


$vamtgst=$q['amount'];

//$table=mysql_query("select * from sell_master where bill_no='$vno' and 	FID='$fid'");

$vcgst="";
$vsgst="";

$vcgamtstsize=strlen($vcgst);
$nsize=51-$vcgamtstsize;
for ($i=0;$i<$nsize;$i++)
{
	$cgstgp.=" ";
}
$vaftrdis=$cus['total_amt']-$cus['discount_amt']-$cus['adjust_amt'];
//$tot1="\t\t\t\t\t\t\tSub Total   :".round($cus[total_amt]).".00";
$tot1="$vcgst.$cgstgp     Total        :".floor($vamtgst).".00";


$text_tot="Rupees ".no_to_words(round($vamtgst))."Only";
$text_note_wish="Wishing You a Speedy recovery.";

$text_company="\t\t\t\t\tFor ".$company_info["name"];
?>
<script>
function printt()
{
	
	
	var applet=document.jzebra;
	
	applet.append("<?php echo $vmnyrcpt;?>"+"\n");
	
	applet.append("\x1B\x45");
	applet.append("<?php echo $company_name;?>"+"\n");
	applet.append("\x1B\x40");
	
	applet.append("<?php echo $header2;?>"+"\n");
	applet.append("<?php echo $header3;?>"+"\n");
	
	applet.append("<?php echo $info3;?>"+"\n");
	applet.append("<?php echo $line;?>"+"\n");
	
    applet.append("<?php echo $pinfo;?>"+"\n");
	applet.append("<?php echo $cname;?>"+"\n");
	
	//applet.append("<?php echo $t_head;?>"+"\n");
	applet.append("<?php echo $line;?>"+"\n");
	
	<?php
	
	$table=mysqli_query($link,"select * from ph_credit_payment_details  where bill_no='$bill' order by order by sl_no desc ");
	$i=1;
	while($tab=mysqli_fetch_array($table))
	{
		$vitmttl=$tab['amount'];
		$vsbttl=$vsbttl+$vitmttl;
		$vcgst=0;
		$itmnetamt=number_format($tab['amount'],2);
		
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
		var amtgap="     ";
				
		
	    
	    var hsn_code="<?php echo $q[receipt_no];?>";		
		var hsnn_len=hsn_code.length;
		var hsnn_len1=20-hsnn_len;
			
			var rcptno_gap=" "; 
			for(var i=0;i<hsnn_len1;i++)
			{
				rcptno_gap=rcptno_gap+" ";
			}
			
		
		
				
		
		var exp_d="<?php echo $cus[entry_date];?>";
		var x_len=exp_d.length;
		var x_len2=12-x_len;
		var dt_gap=" ";
		for(var i=0;i<x_len2;i++)
		{
			dt_gap=dt_gap+" ";
		}

        
        var qntgap=4;
		<?php
		if($tab[sale_qnt]>9)
		{
			?>
					qntgap=3;
			<?php	
		}
		?>
		var qnt_gap=""; 
		for(var i=0;i<qntgap;i++)
		{
			qnt_gap=qnt_gap+" ";
		}
			

		var space2="";
		for(var i=0;i<=tgap3;i++)
		{
			space2=space2+" ";
		}
		
				
		<?php
          if($i==1)
          {
			  ?>
			  
				var itm_gap=""; 
				for(var i=0;i<17;i++)
				{
				itm_gap=itm_gap+" ";
				}
			 applet.append("#"+space1+"Entry Date    "+itm_gap+"Receipt No                                 "+"  Amount"+"\n");
			 applet.append("<?php echo $line;?>"+"\n");
			 <?php 
			  }
			  
          ?>
		   applet.append("<?php echo $i;?>"+space1+"<?php echo $cus[entry_date];?>"+dt_gap+"<?php echo $q[receipt_no];?>"+rcptno_gap+exp_d+x_gap+qnt_gap+tgap3+amtgap+"<?php echo $itmnetamt;?>"+"\n");
		
			
	<?php
	$i++;
	}
	if($i<=9)
	{
		$new_line=9-$i;
		for($j=0;$j<$new_line;$j++)
		{
			?>
			applet.append("\n");
			<?php
		}
	}
	
	
	
	?>
	
	 
	applet.append("<?php echo $line;?>"+"\n");
	applet.append("<?php echo $tot1;?>"+"\n");
	
	
	//applet.append("<?php echo $text_tot;?>"+"\n");
	applet.append("<?php echo $line;?>"+"\n");
	applet.append("\n\n");
	applet.append("<?php echo $signature;?>"+"\n");
	
	applet.append("\x1B\x40");
	//applet.append("\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n");
	applet.append("\n\n\n\n\n\n\n");
	applet.append("\n\n\n\n");
	applet.print();
	window.close();
}
</script>

<applet name="jzebra" code="jzebra.PrintApplet.class" archive="./jzebra.jar" width="50px" height="50px">
      


      <param name="printer" value="zebra">
	
      
   </applet>
   
   <script>printt()</script>

</body>
</html>
