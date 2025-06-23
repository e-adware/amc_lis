<div class="row">
<?php
				$doc_query=mysqli_query($link,"select * from lab_doctor where category='1'");
				$tot_doc=mysqli_num_rows($doc_query);
				$main_width=750;
				$doc_width=$main_width/$tot_doc;
				while($d_det=mysqli_fetch_array($doc_query))
				{
					?>
						<div style="display:inline-block;width:<?php echo $doc_width;?>px;text-align:center;font-size:11px;">
							<?php
							if($aprv_by==$d_det[id])
							{
								if (file_exists("../../sign/".$aprv_by.".jpg")) 
								{
								?> <img src='../../sign/<?php echo $aprv_by;?>.jpg'/>  <?php
								}
								else
								{
									echo "<span style='display: block; height: 45px; width: 20px;'></span>";
								}
							}
							else
							{
								echo "<span style='display: block; height: 45px; width: 20px;'></span>";
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
				
			<div class="span4 text-right">
				<div class='page_no'></div>
			</div>
</div>

<script>load_page_no()</script>			     
