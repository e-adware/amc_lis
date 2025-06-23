<?php

function check_user($user)
{
	$emp_name=mysqli_fetch_array(mysqli_query($link, "select Name from Employee where ID='$user'"));	
	return $emp_name["Name"];
}


function load_normal($uhid,$param,$val,$instrument_id)
{
	global $link;
	
	if(!$instrument_id)
	{
		$instrument_id=0;
	}
	
	$pat=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$uhid'"));

	//----Convert Patient Age in Days
	$date1 = new DateTime($pat["dob"]);
	$date2 = new DateTime('today');
	$diff = $date2->diff($date1);
	
	$npat_age=0;
	$npat_age=$npat_age+($diff->y*365);
	$npat_age=$npat_age+($diff->m*30);
	$npat_age=$npat_age+($diff->d);
	//----------------------------------//
	


	if($pat[sex]=="Male")
	{
		$npat_sex="MALE";
	}
	else
	{
		$npat_sex="FEMALE";
	}

	$val=str_replace(",","",$val);
	
	$unit=mysqli_fetch_array(mysqli_query($link, "select unit_name from Units where ID in(select UnitsID from Parameter_old where ID='$param')"));
	
	$par=mysqli_query($link, "select * from parameter_normal_check where parameter_id='$param' and status='0' and instrument_id='$instrument_id'");
	$par_num=mysqli_num_rows($par);
	if($par_num==false)
	{
		$par=mysqli_query($link, "select * from parameter_normal_check where parameter_id='$param' and status='0'");
	}
	while($p=mysqli_fetch_array($par))
	{
		$n_r="";
		if($p["dep_id"]=="1") // AGE
		{
			$n_r="";
			if($npat_age>=$p["age_from"])
			{
				if($p["age_to"]>0)
				{
					if($npat_age<=$p["age_to"])
					{
						//return $p["normal_range"]."#";
						if(!$p["value_to"])
						{
							$nr=str_replace(" ","&nbsp;",$p["normal_range"]);
							$n_r.=nl2br($nr)."#";
						}
						else if(!$p["value_from"])
						{
							$nr=str_replace(" ","&nbsp;",$p["normal_range"]);
							$n_r.=nl2br($nr)."#";
						}
						else
						{
							$nr=str_replace(" ","&nbsp;",$p["normal_range"]);
							$n_r.=nl2br($nr)."#";
						}
						if(($val<$p["value_from"] && $p["value_from"]!="") || ($val>$p["value_to"] && $p["value_to"]!=""))
						{
							//~ if($val<$p["value_from"])
							//~ {
								//~ $n_r.="Low";
							//~ }
							//~ else if($val>$p["value_from"])
							//~ {
								//~ $n_r.="High";
							//~ }
							$n_r.="Error";
							
						}
					}
				}
				else
				{
					
					if(!$p["value_to"])
					{
						$nr=str_replace(" ","&nbsp;",$p["normal_range"]);
						$n_r.=nl2br($nr)."#";
					}
					else if(!$p["value_from"])
					{
						$nr=str_replace(" ","&nbsp;",$p["normal_range"]);
						$n_r.=nl2br($nr)."#";
					}
					else
					{
						$nr=str_replace(" ","&nbsp;",$p["normal_range"]);
						$n_r.=nl2br($nr)."#";
					}
					
					if(($val<$p["value_from"] && $p["value_from"]!="") || ($val>$p["value_to"] && $p["value_to"]!=""))
					{
						//~ if($val<$p["value_from"])
						//~ {
							//~ $n_r.="Low";
						//~ }
						//~ else if($val>$p["value_from"])
						//~ {
							//~ $n_r.="High";
						//~ }
						$n_r.="Error";
						//break;
					}
				}
			}
			
			if($n_r!="")
			{
				return $n_r."#".$p["slno"];
			}
		}
		else if($p["dep_id"]=="2") // AGE AND SEX
		{
			$n_r="";
			if($npat_sex==$p[sex])
			{
				
				if($npat_age>=$p["age_from"])
				{
					
					if($p["age_to"]>0)
					{
						
						if($npat_age<=$p["age_to"])
						{
							
							if(!$p["value_to"])
							{
								$nr=str_replace(" ","&nbsp;",$p["normal_range"]);
								$n_r.=nl2br($nr)."#";
							}
							else if(!$p["value_from"])
							{
								$nr=str_replace(" ","&nbsp;",$p["normal_range"]);
								$n_r.=nl2br($nr)."#";
							}
							else
							{
								$nr=str_replace(" ","&nbsp;",$p["normal_range"]);
								$n_r.=nl2br($nr)."#";
							}
							
							if(($val<$p["value_from"] && $p["value_from"]!="") || ($val>$p["value_to"] && $p["value_to"]!=""))
							{
									//~ if($val<$p["value_from"])
									//~ {
										//~ $n_r.="Low";
									//~ }
									//~ else if($val>$p["value_from"])
									//~ {
										//~ $n_r.="High";
									//~ }
									$n_r.="Error";
									//break;
							}
						}
					}
					else
					{
						
						//return $p["normal_range"]."#";
						if(!$p["value_to"])
						{
							$nr=str_replace(" ","&nbsp;",$p["normal_range"]);
							$n_r.=nl2br($nr)."#";
						}
						else if(!$p["value_from"])
						{
							$nr=str_replace(" ","&nbsp;",$p["normal_range"]);
							$n_r.=nl2br($nr)."#";
						}
						else
						{
							$nr=str_replace(" ","&nbsp;",$p["normal_range"]);
							$n_r.=nl2br($nr)."#";
						}
						if(($val<$p["value_from"] && $p["value_from"]!="") || ($val>$p["value_to"] && $p["value_to"]!=""))
						{
							//~ if($val<$p["value_from"])
							//~ {
								//~ $n_r.="Low";
							//~ }
							//~ else if($val>$p["value_from"])
							//~ {
								//~ $n_r.="High";
							//~ }
							$n_r.="Error";
							//break;
						}
					}
				}
			}
			
			if($n_r!="")
			{
				return $n_r."#".$p["slno"];
			}
		}
		else if($p["dep_id"]=="3") // SEX
		{
			if($npat_sex==$p[sex])
			{
				$n_r="";
				//return $p["normal_range"]."#";
				if(!$p["value_to"])
				{
					$nr=str_replace(" ","&nbsp;",$p["normal_range"]);
					$n_r.=nl2br($nr)."#";
				}
				else if(!$p["value_from"])
				{
					$nr=str_replace(" ","&nbsp;",$p["normal_range"]);
					$n_r.=nl2br($nr)."#";
				}
				else
				{
					$nr=str_replace(" ","&nbsp;",$p["normal_range"]);
					$n_r.=nl2br($nr)."#";
				}
				if(($val<$p["value_from"] && $p["value_from"]!="") || ($val>$p["value_to"] && $p["value_to"]!=""))
				{
					//~ if($val<$p["value_from"])
					//~ {
						//~ $n_r.="Low";
					//~ }
					//~ else if($val>$p["value_from"])
					//~ {
						//~ $n_r.="High";
					//~ }
					$n_r.="Error";
					//break;
				}
				
				if($n_r!="")
				{
					return $n_r."#".$p["slno"];
				}
			}
		}
		else if($p["dep_id"]=="7") // NOCONDITION
		{
			$n_r="";
			if(!$p["value_to"])
			{
				if(!$p["value_from"])
				{
					$nr=str_replace(" ","&nbsp;",$p["normal_range"]);
					$n_r.=nl2br($nr)."#";
				}
				else
				{
					$nr=str_replace(" ","&nbsp;",$p["normal_range"]);
					$n_r.=nl2br($nr)."#";
				}
			}
			else if(!$p["value_from"])
			{
				if(!$p["value_to"])
				{
					$nr=str_replace(" ","&nbsp;",$p["normal_range"]);
					$n_r.=nl2br($nr)."#";
				}
				else
				{
					$nr=str_replace(" ","&nbsp;",$p["normal_range"]);
					$n_r.=nl2br($nr)."#";
				}
			}
			else
			{
				$nr=str_replace(" ","&nbsp;",$p["normal_range"]);
				$n_r.=nl2br($nr)."#";
			}
			
			if(is_numeric($val))
			{
				if($p["value_from"]!='' && $p["value_to"]!='')
				{
					if(($val<$p["value_from"] && $p["value_from"]!="") || ($val>$p["value_to"] && $p["value_to"]!=""))
					{
						//~ if($val<$p["value_from"])
						//~ {
							//~ $n_r.="Low";
						//~ }
						//~ else if($val>$p["value_from"])
						//~ {
							//~ $n_r.="High";
						//~ }
						$n_r.="Error";
					}
					//break;
				}
			}
			if($n_r!="")
			{
				return $n_r."#".$p["slno"];
			}
		}
		if($n_r!="")
		{
			break;
		}
	}
}
?>
