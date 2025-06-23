<?php
include("../../includes/connection.php");
$id=$_POST[id];
$name=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select Name from Parameter_old where ID='$id'"));

$slno=$_POST[sl];
$sl=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from parameter_normal_check where slno='$slno'"));

$instrument_str="display:none;";
if($instrument_wise_normal_range==1)
{
	$instrument_str="";
}

?>

<div style="padding:-10px">
  <table class="table table-bordered table-condensed">
	  <tr style="<?php echo $instrument_str; ?>">
		<th>Instrument</th>
		<td>
			<select id="instrument_id">
			<?php
				$qry=mysqli_query($link, "SELECT `id`, `name` FROM `lab_instrument_master` WHERE `status`=0 ORDER BY `id` ASC");
				while($data=mysqli_fetch_array($qry))
				{
					if($sl["instrument_id"]==$data["id"]){ $sel="selected"; }else{ $sel=""; }
					echo "<option value='$data[id]' $sel>$data[name]</option>";
				}
			?>
			</select>
		</td>
	  </tr>
    <tr>
      <td>Parameter Name</td>
      <td><input type="text" id="pname" name="pname" value="<?php echo $name[Name];?>" /></td>
    </tr>
    <tr>
      <td>Depends On</td>
      <td>
        <select id="dep_id">
          <?php
                    $dep=mysqli_query($GLOBALS["___mysqli_ston"], "select * from DependentType");
while($d=mysqli_fetch_array($dep)) {
    if($sl[dep_id]==$d[id]) {
        $sel="Selected='selected'";
    } else {
        $sel="";
    }
    echo "<option value='$d[id]' $sel>$d[name]</option>";
}
?>
        </select>
      </td>
    </tr>
    <tr>
      <td>Sex</td>
      <td>
        <select id="sex">
          <option value="0">--Select--</option>
          <option <?php if($sl[sex]=="MALE") {
              echo "Selected='selected'";
          }?>>MALE</option>
          <option <?php if($sl[sex]=="FEMALE") {
              echo "Selected='selected'";
          }?>>FEMALE</option>
        </select>
      </td>
    </tr>
    <?php
        if($sl[age_from]>0) {
            if($sl[age_from]>30 && $sl[age_from]<365) {
                $age_from=$sl[age_from]/30;
                $mon_f="Selected='selected'";
            } elseif($sl[age_from]>0 && $sl[age_from]<30) {
                $age_from=$sl[age_from];
                $day_f="Selected='selected'";
            } else {
                $age_from=$sl[age_from]/365;
                $years_f="Selected='selected'";
            }
        }
?>
    <tr>
      <td>Age From</td>
      <td>
        <input type="text" id="a_from" value="<?php echo round($age_from);?>" />
        <select id="a_from_typ">
          <option value="1" <?php echo $day_f;?>>Days</option>
          <option value="30" <?php echo $mon_f;?>>Months</option>
          <option value="365" <?php echo $years_f;?>>Years</option>
        </select>
      </td>
    </tr>
    <?php
    if($sl[age_to]>0) {
        if($sl[age_to]>30 && $sl[age_to]<365) {
            $age_to=$sl[age_to]/30;
            $mon_t="Selected='selected'";
        } elseif($sl[age_to]>0 && $sl[age_to]<30) {
            $age_to=$sl[age_to];
            $day_t="Selected='selected'";
        } else {
            $age_to=$sl[age_to]/365;
            $years_t="Selected='selected'";
        }
    }
?>
    <tr>
      <td>Age to</td>
      <td>
        <input type="text" id="a_to" value="<?php echo round($age_to);?>" />
        <select id="a_to_typ">
          <option value="1" <?php echo $day_t;?>>Days</option>
          <option value="30" <?php echo $mon_t;?>>Months</option>
          <option value="365" <?php echo $years_t;?>>Years</option>
        </select>
      </td>
    </tr>
    <tr>
      <td>Value from</td>
      <td>
        <input type="text" id="val_f" value="<?php echo $sl[value_from];?>" />
      </td>
    </tr>
    <tr>
      <td>Value to</td>
      <td>
        <input type="text" id="val_t" value="<?php echo $sl[value_to];?>" />
      </td>
    </tr>
    <tr>
      <td>Normal Range</td>
      <td>
        <textarea rows='2' cols='50' id="n_range"><?php echo $sl[normal_range];?></textarea>
      </td>
    </tr>
    <tr>
      <td colspan="2" style="text-align:center">
        <?php
        if($slno) {
            ?>
        <input type="button" id="upd" name="upd" class="btn btn-info" value="Update"
          onclick="normal_update(<?php echo $id;?>,<?php echo $slno;?>)" />
        <?php

        } else {
            ?>
        <input type="button" id="save" name="save" class="btn btn-info" value="Save"
          onclick="normal_save(<?php echo $id;?>)" />
        <?php
        }
?>
        <input type="button" id="close" name="close" class="btn btn-info" value="Close"
          onclick="$('#display_range').slideDown(200);$('#add_range').slideUp(200);" />
      </td>
    </tr>
  </table>
</div>
