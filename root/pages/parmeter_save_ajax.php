<?php

include("../../includes/connection.php");

$id=$_POST[id];

$name=$_POST[name];
$rtype=$_POST[rtype];
if(!$rtype) {
    $rtype = 0;
}
$unit=$_POST[unit];
if(!$unit) {
    $unit = 0;
}
$option=$_POST[option];
if(!$option) {
    $option = 0;
}
$meth=$_POST[meth];
if(!$meth) {
    $meth = 0;
}
$samp=$_POST[samp];
if(!$samp) {
    $samp = 0;
}
$vaccu=$_POST[vaccu];
if(!$vaccu) {
    $vaccu = 0;
}

if($id) {

    $range=mysqli_real_escape_string($link, $_POST[e_range]);

    //$deci=$_POST[deci];

    mysqli_query($GLOBALS["___mysqli_ston"], "update Parameter_old set Name='$name',ResultType='$rtype',ResultOptionID='$option',UnitsID='$unit',method='$meth',sample='$samp',vaccu='$vaccu' where ID='$id'");
    
    if($samp)
    {
		mysqli_query($link,"update Testparameter set sample='$samp' where ParamaterId='$id'");
	}
    if($vaccu)
    {
		mysqli_query($link,"update Testparameter set vaccu='$vaccu' where ParamaterId='$id'");
	}


    if(trim($range)!="") {
        mysqli_query($link, "delete from parameter_range where paramid='$id'");
        mysqli_query($link, "insert into parameter_range(paramid,e_range) values('$id','$range')");
    }

} elseif ($id == '0') {
    $pid=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select max(ID) as m from Parameter_old"));
    if($pid[m]) {
        $npid=$pid[m]+1;
        mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO `Parameter_old`(`ID`, `ResultType`, `Name`, `ResultOptionID`, `UnitsID`, `sample`, `vaccu`, `method`, `deci_val`,`sub_title`, `instrument`) VALUES ('$npid','$rtype','$name','$option','$unit','$samp','$vaccu','$meth','0','0','0')");
    }

}
