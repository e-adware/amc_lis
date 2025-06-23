<?php

// Age Calculator till date
function age_calculator_date($dob, $reg_date)
{
	$dob = date("Y-m-d", strtotime($dob));
	$reg_date = date("Y-m-d", strtotime($reg_date));

	$bday = new DateTime($dob);
	$today = new DateTime($reg_date);
	$diff = $today->diff($bday);

	//echo $diff->y."Y ".$diff->m."M ".$diff->d."D";

	$age_str = "";
	if ($diff->y > 0) {
		if ($diff->y == 1) {
			$age_str .= $diff->y . " Year ";
		} else {
			$age_str .= $diff->y . " Years ";
		}
	}
	if ($diff->m > 0) {
		if ($diff->m == 1) {
			$age_str .= $diff->m . " Month ";
		} else {
			$age_str .= $diff->m . " Months ";
		}
	}
	if ($diff->d > 0) {
		if ($diff->d == 1) {
			$age_str .= $diff->d . " Day";
		} else {
			$age_str .= $diff->d . " Days";
		}
	}

	if ($age_str == "") {
		$age_str .= "0 Day";
	}
	return $age_str;
}

function age_calculator_date_only($dob, $reg_date)
{
	$dob = date("Y-m-d", strtotime($dob));
	$reg_date = date("Y-m-d", strtotime($reg_date));

	$bday = new DateTime($dob);
	$today = new DateTime($reg_date);
	$diff = $today->diff($bday);

	//echo $diff->y."Y ".$diff->m."M ".$diff->d."D";

	$age_str = "";
	if ($diff->y > 0) {
		if ($diff->y == 1) {
			$age_str .= $diff->y . " Year ";
		} else {
			$age_str .= $diff->y . " Years ";
		}
	}
	if ($diff->m > 0 && $age_str == "") {
		if ($diff->m == 1) {
			$age_str .= $diff->m . " Month ";
		} else {
			$age_str .= $diff->m . " Months ";
		}
	}
	if ($diff->d > 0 && $age_str == "") {
		if ($diff->d == 1) {
			$age_str .= $diff->d . " Day";
		} else {
			$age_str .= $diff->d . " Days";
		}
	}

	if ($age_str == "") {
		$age_str .= "0 Day";
	}
	return $age_str;
}

// Age Calculator
function age_calculator($dob)
{
	//~ $from = new DateTime($dob);
	//~ $to   = new DateTime('today');
	//~ $year=$from->diff($to)->y;
	//~ $month=$from->diff($to)->m;
	//~ if($year==0)
	//~ {
	//~ //$month=$from->diff($to)->m;
	//~ if($month==0)
	//~ {
	//~ $day=$from->diff($to)->d;
	//~ return $day." Days";
	//~ }else
	//~ {
	//~ return $month." Months";
	//~ }
	//~ }else
	//~ {
	//~ return $year.".".$month." Years";
	//~ }

	$bday = new DateTime($dob);
	$today = new DateTime('today');
	$diff = $today->diff($bday);

	$age_str = "";
	if ($diff->y > 0) {
		$age_str = $diff->y . " Years";
	}
	if ($diff->m > 0) {
		$age_str .= " " . $diff->m . " Months";
	}
	if ($diff->d > 0) {
		$age_str .= " " . $diff->d . " Days";
	}

	return $age_str;

	//return $diff->y."Y ".$diff->m."M ".$diff->d."D";
}
function age_calculator_save($dob)
{
	$from = new DateTime($dob);
	$to = new DateTime('today');
	$year = $from->diff($to)->y;
	$month = $from->diff($to)->m;
	$day = $from->diff($to)->d;

	if ($year == 0) {
		if ($month == 0) {
			return $day . " Days";
		} else {
			return $month . " Months";
		}
	} else {
		return $year . " Years";
	}
}
function age_calculator_all($dob)
{
	$from = new DateTime($dob);
	$to = new DateTime('today');
	$year = $from->diff($to)->y;
	$month = $from->diff($to)->m;
	$day = $from->diff($to)->d;

	return $year . "@#@" . $month . "@#@" . $day;
}
function age_To_Birthday($age)
{
	//~ $days = (int)($age * 365); //convert age to days

	//~ $date = new DateTime("today -$days day");
	//~ $birthday = $date->format('Y-m-d');
	//~ return $birthday;

	return date('Y-m-d', strtotime($age . ' years ago'));
}
// Date format convert
function convert_date($date)
{
	if ($date) {
		$timestamp = strtotime($date);
		$new_date = date('d-M-y', $timestamp);
		return $new_date;
	}
}
// Date format convert
function convert_date_g($date)
{
	if ($date) {
		$timestamp = strtotime($date);
		$new_date = date('d-M-Y', $timestamp);
		return $new_date;
	}
}
// Date format convert
function convert_date_f($date)
{
	if ($date) {
		$timestamp = strtotime($date);
		$new_date = date('d F Y', $timestamp);
		return $new_date;
	}
}
// Date format convert
function convert_date_small($date)
{
	if ($date) {
		$timestamp = strtotime($date);
		$new_date = date('d-m-Y', $timestamp);
		return $new_date;
	}
}
// Time format convert
function convert_time($time)
{
	$time = date("g:i A", strtotime($time));
	return $time;
}
// Time format convert
function convert_time_g($time)
{
	$time = date("h:i A", strtotime($time));
	return $time;
}
// Convert Date to day number
function convert_date_to_day_num($date)
{
	$timestamp = strtotime($date);
	$new_date = date('D', $timestamp);
	$week = ["", "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
	for ($i = 0; $i < 8; $i++) {
		if ($week[$i] == $new_date) {
			$day_num = $i;
		}
	}
	return $day_num;
}
// Function that gives days between including two dates
function getDatesFromRange($start, $end, $format = 'd-m-Y')
{
	$array = array();
	$interval = new DateInterval('P1D');

	$realEnd = new DateTime($end);
	$realEnd->add($interval);

	$period = new DatePeriod(new DateTime($start), $interval, $realEnd);

	foreach ($period as $date) {
		$array[] = $date->format($format);
	}

	return $array;
}

function nextID($prefix, $table, $idno, $start = "100")
{
	$idRes = mysqli_query($GLOBALS["___mysqli_ston"], "Select max( cast( right(" . $idno . ",length(" . $idno . ")-" . strlen($prefix) . ") AS signed ) ) from " . $table);
	//SELECT max( cast( right( r_id, length( r_id ) -2 ) AS signed ) ) 

	//echo "Select max( cast( right(".$idno.",length(".$idno.")-".strlen($prefix).") AS signed ) ) from ".$table;
	$idArr = mysqli_fetch_array($idRes);
	if ($idArr[0] == null) {
		$NewId = $start;
	} else {
		$NewId = $idArr[0] + 1;
	}
	$NewId = $prefix . $NewId;
	return $NewId;
	//return "Select max( cast( right(".$idno.",length(".$idno.")-".strlen($prefix).") AS signed ) ) from ".$table."<br>SELECT max( cast( right( r_id, length( r_id ) -2 ) AS signed ) )";
}
function convert_number($number)
{
	if (($number < 0) || ($number > 999999999)) {
		throw new Exception("Number is out of range");
	}

	$Gn = floor($number / 100000);  /* Lakh (100 kilo) */
	$number -= $Gn * 100000;
	$kn = floor($number / 1000);     /* Thousands (kilo) */
	$number -= $kn * 1000;
	$Hn = floor($number / 100);      /* Hundreds (hecto) */
	$number -= $Hn * 100;
	$Dn = floor($number / 10);       /* Tens (deca) */
	$n = $number % 10;               /* Ones */

	$res = "";

	if ($Gn) {
		$res .= convert_number($Gn) . " Lakh";
	}

	if ($kn) {
		$res .= (empty($res) ? "" : " ") .
			convert_number($kn) . " Thousand";
	}

	if ($Hn) {
		$res .= (empty($res) ? "" : " ") .
			convert_number($Hn) . " Hundred";
	}

	$ones = array(
		"",
		"One",
		"Two",
		"Three",
		"Four",
		"Five",
		"Six",
		"Seven",
		"Eight",
		"Nine",
		"Ten",
		"Eleven",
		"Twelve",
		"Thirteen",
		"Fourteen",
		"Fifteen",
		"Sixteen",
		"Seventeen",
		"Eightteen",
		"Nineteen"
	);
	$tens = array(
		"",
		"",
		"Twenty",
		"Thirty",
		"Fourty",
		"Fifty",
		"Sixty",
		"Seventy",
		"Eigthy",
		"Ninety"
	);

	if ($Dn || $n) {
		if (!empty($res)) {
			$res .= " and ";
		}

		if ($Dn < 2) {
			$res .= $ones[$Dn * 10 + $n];
		} else {
			$res .= $tens[$Dn];

			if ($n) {
				$res .= "-" . $ones[$n];
			}
		}
	}

	if (empty($res)) {
		$res = "zero";
	}

	return $res;
}

// Indian Money format
function indian_currency_format($amount)
{
	setlocale(LC_MONETARY, 'en_IN');
	$amount = money_format('%!i', $amount);
	return $amount;
}

function text_query($txt, $file)
{
	$txt_file = "../log/" . $file;
	if (file_exists($txt_file)) {
		file_put_contents($txt_file, $txt . PHP_EOL, FILE_APPEND | LOCK_EX);
	} else {
		$fp = fopen($txt_file, 'w');
		file_put_contents($txt_file, $txt . PHP_EOL, FILE_APPEND | LOCK_EX);
	}
}

// Cookie Start
function save_cookie($cookie_name, $cookie_value, $cookie_time)
{
	setcookie($cookie_name, $cookie_value, time() + (86400 * 30 * $cookie_time), "/"); // 86400 = 1 day X $cookie_time
}

function delete_cookie($cookie_name)
{
	setcookie($cookie_name, "", time() - 3600);
}

// Cookie End

function generateRandomString($length)
{
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	// Generate random bytes
	$bytes = random_bytes($length);
	// Convert bytes to base64, then shuffle and return the desired length substring
	$randomString = substr(str_shuffle(str_repeat($characters, $length)), 0, $length);
	return $randomString;
}

function sex_full($sex)
{
	if ($sex == 'M' || $sex == 'm')
		return "Male";
	else if ($sex == 'f' || $sex == 'F')
		return "Female";
	else
		return "Others";
}
?>