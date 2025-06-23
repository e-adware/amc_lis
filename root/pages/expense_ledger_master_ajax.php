<?php
include('../../includes/connection.php');
$type=$_POST[type];

if($type==1)
{
	$val=$_POST[val];
	?>
	<table class="table table-bordered table-condesned ledger_list">
		<tr><th>#</th><th>Ledger Name</th><th></th></tr>
	<?php
	$i=1;
	if($val=='')
	{
		$qry=mysqli_query($link,"select * from ledger_master order by ledger_name");
	}
	else
	{
		$qry=mysqli_query($link,"select * from ledger_master where ledger_name like '%$val%' order by ledger_name");
	}
	while($q=mysqli_fetch_array($qry))
	{
		echo "<tr><td>$i</td><td>$q[ledger_name]</td><td><input type='button' class='btn btn-primary btn-mini' onclick='load_ledger($q[id])' value='Edit'/> <input type='button' class='btn btn-danger btn-mini' onclick='delete_ledger($q[id])' value='Delete' /></td></tr>";
		$i++;
	}
	?> </table> <?php
}
else if($type==2)
{
	$id=$_POST[id];
	$det=mysqli_fetch_array(mysqli_query($link,"select * from ledger_master where id='$id'"));
	
	echo $det[id]."@@".$det[ledger_name];
}
else if($type==3)
{
	$max=mysqli_fetch_array(mysqli_query($link,"select max(id) as tot from ledger_master"));
	$nid=$max[tot]+1;
	echo $nid;
}
else if($type==4)
{
	$id=$_POST[id];
	$name=$_POST[name];
	
	mysqli_query($link,"delete from ledger_master where id='$id'");
	mysqli_query($link,"insert into ledger_master(id,ledger_name) values('$id','$name')");
}
else if($type==5)
{
	$id=$_POST[id];
	mysqli_query($link,"delete from ledger_master where id='$id'");
}
?>
