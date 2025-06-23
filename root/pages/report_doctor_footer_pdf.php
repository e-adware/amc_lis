<div class="row" style="margin-left:2px !important">

<?php
	$doc_query=mysqli_query($link,"select * from lab_doctor where category='1'");
	$tot_doc=mysqli_num_rows($doc_query);
	$main_width=720;
	$doc_width=$main_width/$tot_doc;
	while($d_det=mysqli_fetch_array($doc_query))
	{
		?>
			<div style="display:inline-block;width:<?php echo $doc_width;?>px;text-align:center;font-size:9px;float:left;margin-left:5px;">
				<?php
				if($aprv_by==$d_det[id])
				{
					if (file_exists("../../sign/".$aprv_by.".jpg")) 
					{
					?> <img src='../../sign/<?php echo $aprv_by;?>.jpg'/>  <?php
					}
					else
					{
						echo "<p style='display: block; height: 45px; width: 20px;'></p>";
					}
				}
				else
				{
					echo "<p style='display: block; height: 30px; width: 20px;'></p>";
				}
				?>	
				<br/>						
				<?php echo $d_det[name]." , ".$d_det[qual]."<br/>".$d_det[desig];?>
			</div>
		
		<?php
	}

?>
			
							
</div>
<div class="row">
				
			<div class="span10 text-right">
				
			</div>
</div>


