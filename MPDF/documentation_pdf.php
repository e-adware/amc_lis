<?php
include("../includes/connection.php");

function convert_date_only_sm_year($date)
{
	$timestamp = strtotime($date); 
	$new_date = date('y', $timestamp);
	return $new_date;
}

$client_info=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `company_name` "));
if($client_info["i_date"])
{
	$i_date=$client_info["i_date"];
}else
{
	$reg=mysqli_fetch_array(mysqli_query($link," SELECT * FROM `uhid_and_opdid` ORDER BY `slno` ASC LIMIT 0,1 "));
	if($reg["date"])
	{
		$i_date=$reg["date"];
	}else
	{
		$i_date=date("Y-m-d");
	}
}

$date_str=explode("-", $i_date);
$dis_year=$date_str[0];
$dis_month=$date_str[1];
$dis_year_sm=convert_date_only_sm_year($i_date);

$version=$dis_year_sm.".".$dis_month;

include("mpdf60/mpdf.php");

$mpdf=new mPDF('c','A4'); 

$mpdf->mirrorMargins = 1;	// Use different Odd/Even headers and footers and mirror margins

$footer = '<div align="center" style="font-size:12px;">This document has been created by E-Adware Healthcare Informatics</div>';
$footerE ='<div align="center" style="font-size:12px;">This document has been created by E-Adware Healthcare Informatics</div>';

$logo="images/watermark.jpg";

$mpdf->SetHTMLFooter($footer);
$mpdf->SetHTMLFooter($footerE,'E');

$mpdf->SetDisplayMode('fullpage');
//$mpdf->SetWatermarkImage("images/watermark.jpg", 0.25, "F");
//$mpdf->showWatermarkImage = true;

$html="
	<p align='center' style='font-size: 50px;font-weight: bold;'>E-ADWARE</p>
    <p align='center'><img src=$logo width='400' height='400' /></p>
	<h2 align='center'><strong>Software Documentation</strong></h2>
	<h3 align='center'><strong>$client_info[name]</strong></h3>
	<p align='center'>Version : $version</p> 
	<pagebreak />";
	$html.="
	<h4>Contents</h4>
	<div style='text-align:left;'>
	<ol>
	";
		$header_no=1;
		$line_no=1;
		$header_qry=mysqli_query($link," SELECT * FROM `menu_header_master` WHERE `id` IN(SELECT `header` FROM `menu_master` WHERE `hidden`='0') ORDER BY `sequence` ");
		while($header=mysqli_fetch_array($header_qry))
		{
			$html .= "<li>".$header["name"]."</li>
			<ul>
			";
			
			$menu_qry=mysqli_query($link," SELECT * FROM `menu_master` WHERE `header`='$header[id]' AND `par_id`!='0' AND `hidden`='0' ORDER BY `sequence` ");
			$menu_no=1;
			while($menu=mysqli_fetch_array($menu_qry))
			{
				$html .= "<li>".$menu["par_name"]."</li>
				
				<ul>
				";
				$remarks_str=explode("@", $menu["remarks"]);
				foreach($remarks_str AS $remarks_str)
				{
					if($remarks_str)
					{
						$html .= "<li>".$remarks_str."</li>";
						
						$line_no++;	
					}
				}
				
				$html .= "</ul>";
				
				$menu_no++;
			}
			$html.="</ul>";
			$header_no++;
		}
	$html.="</ol>
	</div>
	";
	

// Document ends

// File Name
//$rfp_num=$_SESSION['counter_rfp_od'];
//$file_name="documents/RFP_for_".str_replace(" ", "_", $info['org_name'])."_trucolours".$user.$rfp_num.".pdf";
$file_name="documents/e-adware_documantation.pdf";

// LOAD a stylesheet
$stylesheet = file_get_contents('mpdfstyletables.css');
$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text	
//~ $mpdf->WriteHTML($html);
//~ $mpdf->Output($file_name,'F');
//~ exit;

//~ $mpdf=new mPDF('c'); 

//~ $mpdf->WriteHTML($html);
//~ $mpdf->Output();

$download_file_name="Penguin Documentation.pdf";

$payStub=new mPDF();
$payStub->WriteHTML($html);
$payStub->Output($download_file_name, 'I');

exit;

?>
