<?php
include("../../includes/connection.php");

$uhid=$_POST['uhid'];
$opd_id=$_POST['opd_id'];
$ipd_id=$_POST['ipd_id'];
$batch_no=$_POST['batch_no'];
$tid=$_POST['tid'];

$img=mysqli_query($GLOBALS["___mysqli_ston"],"select * from image_temp where patient_id='$uhid' and `opd_id`='$opd_id' and `ipd_id`='$ipd_id' and `batch_no`='$batch_no' and `testid`='$tid' order by img_no");

if(mysqli_num_rows($img)>0)
{
	
	echo "<div id='uploaded_img' style='padding:10px'><h4>Uploaded Images:</h4>";
	
	while($im=mysqli_fetch_array($img))
	{
			echo "<div style='display:inline;' onclick='remove_pic($tid,$im[img_no])' id='img_par_$im[img_no]'><img style='border:2px solid;border-radius:5px;margin-left:10px' height='200' width='200' src='$im[Path]'/></div>";
	}	
	echo "</div>";
	
	$sty="display:none";
	
	$butt="<input type='button' id='a' value='Upload' class='btn btn-default' onclick='show_upload(this.id)'/>";
}

?>

<div id="image_upload">
<div id="fine-uploader" style="<?php echo $sty;?>">
    </div>

<?php

?>
    <!-- Fine Uploader -->
    

    <script type="text/template" id="qq-template">
        <div class="qq-uploader-selector qq-uploader" qq-drop-area-text="Drop files here">
            <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
                <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
            </div>
            <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
                <span class="qq-upload-drop-area-text-selector"></span>
            </div>
            <div class="qq-upload-button-selector qq-upload-button">
                <div>Upload a file</div>
            </div>
            <span class="qq-drop-processing-selector qq-drop-processing">
                <span>Processing dropped files...</span>
                <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
            </span>
            <ul class="qq-upload-list-selector qq-upload-list" aria-live="polite" aria-relevant="additions removals">
				
				<?php
				/*
				$i=0;
				$img=mysqli_query($GLOBALS["___mysqli_ston"],"select * from image_temp where patient_id='$uhid' and visit_no='$visit' order by img_no");
				while($im=mysqli_fetch_array($img))
				{
					echo "a";
					?>
					
						<li class="qq-file-id-<?php echo $i;?> qq-upload-success" qq-file-id="<?php echo $i;?>">
							<div class="qq-progress-bar-container-selector qq-hide">
								<div class="qq-progress-bar-selector qq-progress-bar" aria-valuemax="100" aria-valuemin="0" aria-valuenow="100" role="progressbar" style="width: 100%;"></div>
							</div>
							<span class="qq-upload-spinner-selector qq-upload-spinner qq-hide"></span>
							<img class="qq-thumbnail-selector" qq-server-scale="" qq-max-size="100" src="<?php echo $im[Path];?>"></img>
							<span class="qq-upload-file-selector qq-upload-file" title="">

								
							</span>
							<span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
							<input class="qq-edit-filename-selector qq-edit-filename" type="text" tabindex="0"></input>
							<span class="qq-upload-size-selector qq-upload-size">

								428.9kB

							</span>
							<button class="qq-btn qq-upload-cancel-selector qq-upload-cancel qq-hide" type="button">

								Cancel

							</button>
							<button class="qq-btn qq-upload-retry-selector qq-upload-retry qq-hide" type="button"> … </button>
							<!--

							  <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">Delete</button> 

							-->
							<span class="qq-upload-status-text-selector qq-upload-status-text" role="status"></span>
						</li>
				<?php
				$i++;
				
				}
				*/
				?>
				
                <li>
                    <div class="qq-progress-bar-container-selector">
                        <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
                    </div>
                    <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                    <img class="qq-thumbnail-selector" qq-max-size="100" qq-server-scale>
                    <span class="qq-upload-file-selector qq-upload-file"></span>
                    <span class="qq-edit-filename-icon-selector qq-edit-filename-icon" aria-label="Edit filename"></span>
                    <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                    <span class="qq-upload-size-selector qq-upload-size"></span>
                    <button type="button" class="qq-btn qq-upload-cancel-selector qq-upload-cancel">Cancel</button>
                    <button type="button" class="qq-btn qq-upload-retry-selector qq-upload-retry">Retry</button>
                  <!--  <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">Delete</button> -->
                    <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                </li>
            </ul>

            <dialog class="qq-alert-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">Close</button>
                </div>
            </dialog>

            <dialog class="qq-confirm-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">No</button>
                    <button type="button" class="qq-ok-button-selector">Yes</button>
                </div>
            </dialog>

            <dialog class="qq-prompt-dialog-selector">
                <div class="qq-dialog-message-selector"></div>
                <input type="text">
                <div class="qq-dialog-buttons">
                    <button type="button" class="qq-cancel-button-selector">Cancel</button>
                    <button type="button" class="qq-ok-button-selector">Ok</button>
                </div>
            </dialog>
        </div>
    </script>
	<div align="center">
			<!--<input type="button" value="Print" onclick="print_report_image(<?php echo $_POST[tid];?>)" class="btn btn-default"/>-->
			<input type="button" value="Close" onclick="$('#mod_chk2').val('0');$('#mod2').click()" class="btn btn-default"/>
			<?php echo $butt;?>
	</div>
</div>
