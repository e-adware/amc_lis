<html>
<head>
</head>
<body>
<b><i>Close this page when printing is done</i> <a href="#" onclick="window.close()">Click to Close</a></b>
<?php
include("../../includes/connection.php");
$bill=$_GET['billno'];
$cnt=$_GET['counter'];


$cus=mysqli_fetch_array(mysqli_query($link,"select * from `ph_sell_master` where `bill_no`='$bill'"));
$qipd=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where opd_id='$cus[ipd_id]'"));

$date1=date('Y-m-d');
if($cus['bill_type_id']==2)
{
	$vmnyrcpt="\t\t\t\t\Return Item";
}
else
{
	$vmnyrcpt="\t\t\t\t\Return Item";
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
$pinfo.=$namegp;
$pinfo.="Bill No:".$bill;
$cname="Prescribed by     :".$vcabin."\t\t Cabin No:".$vcabin."\t       Return Date :".$date1;
$line="------------------------------------------------------------------------------";


$vgstamt1=0;
$vgstamt=0;

$q=mysqli_query($link,"select  a.mrp,b.return_qnt from ph_sell_details a,ph_item_return_master b where a.bill_no=b.bill_no and b.bill_no='$bill' and b.counter='$cnt' and a.item_code=b.item_code and a.batch_no=b.batch_no");
while($q1=mysqli_fetch_array($q))
{
	$vamt=$q1['mrp']*$q1['return_qnt'];
	$vamtgst1=$vamtgst1+$vamt;
}
$disct=($vamtgst1*$cus['discount_perchant'])/100;
$vamtgst=$vamtgst1-$disct;
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
$tot1="$vcgst.$cgstgp Sub Total        :".floor($vamtgst1).".00";
$tot2="$vcgst.$cgstgp Discount         :".floor($disct).".00";
$tot3="$vcgst.$cgstgp Net Amount       :".floor($vamtgst).".00";

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
	
	//$table=mysqli_query($link,"select a.mrp,a.expiry_date,b.item_name,b.hsn_code,c.return_qnt,c.batch_no from ph_sell_details a,ph_item_master b,ph_item_return_master c where a.item_code=b.item_code and a.item_code=c.item_code and a.bill_no=c.bill_no and  c.bill_no='$bill' and c.counter='$cnt' ");
	$table=mysqli_query($link,"select a.mrp,a.expiry_date,b.item_name,b.hsn_code,c.return_qnt,c.batch_no from ph_sell_details a,ph_item_master b,ph_item_return_master c where a.item_code=b.item_code and a.item_code=c.item_code and a.bill_no=c.bill_no and a.`batch_no`=c.`batch_no` and c.bill_no='$bill' and c.counter='$cnt'");
	$i=1;
	while($tab=mysqli_fetch_array($table))
	{
		$vitmttl=$r['mrp']*$r['return_qnt'];
		$vsbttl=$vsbttl+$vitmttl;
		$vcgst=0;
		$itmnetamt=number_format($tab['mrp']*$tab['return_qnt'],2);
		
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
				
		var name_b="<?php echo substr($tab[item_name],0,25);?>";		
		var n_len=name_b.length;
		var n_len2=25-n_len;
			
			var tgap2=""; 
			for(var i=0;i<n_len2;i++)
			{
				tgap2=tgap2+" ";
			}
	    
	    var hsn_code="<?php echo substr($tab[hsn_code],0,5);?>";		
		var hsnn_len=hsn_code.length;
		var hsnn_len1=5-hsnn_len;
			
			var hsn_gap=" "; 
			for(var i=0;i<hsnn_len1;i++)
			{
				hsn_gap=hsn_gap+" ";
			}
			
		
		var batch_no="<?php echo substr($tab[batch_no],0,6);?>";		
		var batch_len=batch_no.length;
		var batch_len1=7-batch_len;
			
			var batch_gap="  "; 
			for(var i=0;i<batch_len1;i++)
			{
				batch_gap=batch_gap+" ";
			}
				
		
		var exp_d="<?php echo substr($tab[expiry_date],2,5);?>";
		var x_len=exp_d.length;
		var x_len2=8-x_len;
		var x_gap=" ";
		for(var i=0;i<x_len2;i++)
		{
			x_gap=x_gap+" ";
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
		
		var mrp="<?php echo $tab[mrp];?>";
		var m_length=mrp.length;
		var n_len3=7-m_length;

		var tgap3=""; 
		for(var i=0;i<n_len3;i++)
		{
			tgap3=tgap3+" ";
		}
		
		var rate="<?php echo $tab[sale_price];?>";		
		var rate_len=rate.length;
		var rate_len1=7-rate_len;
			
			var rate_gap=""; 
			for(var i=0;i<rate_len1;i++)
			{
				rate_gap=rate_gap+" ";
			}
		
		var gst="<?php echo $vcgst;?>";		
		var gst_len=gst.length;
		var gst_len1=4-gst_len;
			
			var gst_gap=""; 
			for(var i=0;i<gst_len1;i++)
			{
				gst_gap=gst_gap+" ";
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
			 applet.append("#"+space1+"Item Name"+itm_gap+"HSN   "+"Batch   "+"Expiry  "+"Qnty  "+" MRP    "+"  Amount"+"\n");
			 applet.append("<?php echo $line;?>"+"\n");
			 <?php 
			  }
			  
          ?>
		   applet.append("<?php echo $i;?>"+space1+name_b+tgap2+hsn_code+hsn_gap+batch_no+batch_gap+exp_d+x_gap+"<?php echo $tab[return_qnt];?>"+qnt_gap+"<?php echo $tab[mrp];?>"+tgap3+amtgap+"<?php echo $itmnetamt;?>"+"\n");
		
			
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
	applet.append("<?php echo $tot2;?>"+"\n");
	applet.append("<?php echo $tot3;?>"+"\n");
	
	
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
