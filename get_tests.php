<?php
include("includes/connection.php");

$tname=mysqli_real_escape_string($link,$_GET['tname']);

if($tname)
{
	$qry="SELECT * FROM `testmaster` WHERE `testname`!='' AND `testname` like '%$tname%' ORDER BY `testname` LIMIT 0,20";
}
else
{
	$qry="SELECT * FROM `testmaster` WHERE `testname`!='' ORDER BY `testname` LIMIT 0,20";
}

$mainarr=array();
$temp=["authKey"=>"g1Z01N9bcl6YniicVlP1"];
array_push($mainarr, $temp);

$val=array();

$q=mysqli_query($link,$qry);
while($r=mysqli_fetch_assoc($q))
{
	$temp=['testid'=>$r['testid'],'testname'=>$r['testname'], 'price'=>$r['rate']];
	array_push($val, $temp);
}
array_push($mainarr, $val);

echo json_encode($mainarr);

/*

{
  "authKey": "g1Z01N9bcl6YniicVlP1",
  "data": {
    "localId": 1,
    "testName": "Some Blood Test Name",
    "textDescription": "No description found about the test",
    "price": 1500.00
  }
}

*/

//~ echo "<br/><br/><br/><br/>";

//~ $arr = array (
  
      //~ // Every array will be converted
      //~ // to an object
      //~ array(
          //~ "name" => "Pankaj Singh",
          //~ "age" => "20"
      //~ ),
      //~ array(
          //~ "name" => "Arun Yadav",
          //~ "age" => "21"
      //~ ),
      //~ array(
          //~ "name" => "Apeksha Jaiswal",
          //~ "age" => "20"
      //~ )
  //~ );
  
  //~ // Function to convert array into JSON
  //~ echo json_encode($arr);
?>
