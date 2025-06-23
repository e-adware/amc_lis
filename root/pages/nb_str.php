<?php
	$patho=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `nb_text` WHERE `id`='1' "));
	$radio=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `nb_text` WHERE `id`='2' "));
	$crdio=mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `nb_text` WHERE `id`='3' "));

?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> NB Text</span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<label class="control-label" ><b>For Pathology</b></label>
	<textarea rows="2" id="nb_text1"><?php echo $patho["nb_text"]; ?></textarea>
	<button class="btn btn-info" onClick="save_nb_text(1)">Update</button>
	<hr>
	<label class="control-label" ><b>For Radiology</b></label>
	<textarea rows="2" id="nb_text2"><?php echo $radio["nb_text"]; ?></textarea>
	<button class="btn btn-info" onClick="save_nb_text(2)">Update</button>
	<hr>
	<label class="control-label" ><b>For Cardiology</b></label>
	<textarea rows="2" id="nb_text3"><?php echo $crdio["nb_text"]; ?></textarea>
	<button class="btn btn-info" onClick="save_nb_text(3)">Update</button>
</div>
<script>
	function save_nb_text(id)
	{
		$.post("pages/nb_str_data.php",
		{
			id:id,
			nb_text:$("#nb_text"+id).val(),
		},
		function(data,status)
		{
			bootbox.dialog({ message: "<h5>Saved</h5>"});
			setTimeout(function(){
				window.location.reload(true);
			 }, 2000);
		})
	}
</script>
<style>
textarea
{
	width: 100%;
}
</style>
