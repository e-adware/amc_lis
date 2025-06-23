<?php
include("../../includes/connection.php");
$id=$_POST[id];

$pdata=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select * from Parameter_old where id='$id'"));
?>
<div id="pinfo" style="padding:10px;display:none" align="center">
  <!--<h4>Parameters</h4>-->
  <table class="table table-bordered table-condensed">
    <tr>
      <td>Parameter Name</td>
      <td><input type="text" name="pname" id="pname"
          value='<?php echo((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $pdata[Name]) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));?>' />
      </td>

      <td>Result Type</td>
      <td>
        <select id="rtype">
          <?php
            $rtyp=mysqli_query($GLOBALS["___mysqli_ston"], "select * from ResultType order by slno");
while($r=mysqli_fetch_array($rtyp)) {
    if($r[ResultTypeId]==$pdata[ResultType]) {
        $sel="Selected='selected'";
    } else {
        $sel="";

    }
    echo "<option value='$r[ResultTypeId]' $sel>$r[ResultType_name]</option>";
}

$sty="display:none";
$b="";
if($pdata[ResultType]==1) {
    //$sty="display:block";
    $b="selected='selected'";
}

?>
        </select>
        <span id="deci_span" style="<?php echo $sty;?>">
          <select id="deci">
            <option value="1" <?php echo $b;?>>Decimal</option>
            <option value="0" <?php if(!$b) {
                echo "selected='selected'";
            }?>>Non-Decimal</option>
          </select>
        </span>
      </td>
    </tr>
    <tr>
      <?php
    $unit=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select unit_name from Units where ID='$pdata[UnitsID]'"));
?>
      <td>Unit</td>
      <td onclick="load_more_info('1','')" id="unit_tr"><input type="text" id="unit" name="unit"
          value="<?php echo $unit[unit_name];?>" readonly /><input type="hidden" id="unit_id"
          value="<?php echo $pdata[UnitsID];?>" /></td>

      <?php
$opt=mysqli_fetch_array(mysqli_query($GLOBALS["___mysqli_ston"], "select name from ResultOption where id='$pdata[ResultOptionID]'"));
?>
      <td>Options</td>
      <td onclick="load_more_info('2','<?php echo $id?>')" id="opt_tr"><input type="text" id="options" name="options"
          value="<?php echo $opt[name];?>" readonly /><input type="hidden" id="options_id"
          value="<?php echo $pdata[ResultOptionID];?>" /></td>
    </tr>
    <tr>
      <td>Method</td>
      <td>
        <select id="meth">
          <option value='0'>--None-</option>
          <?php
    $meth=mysqli_query($GLOBALS["___mysqli_ston"], "select * from test_methods order by name");
while($mt=mysqli_fetch_array($meth)) {
    if($mt[id]==$pdata[method]) {
        $sel_m="Selected='selected'";
    } else {
        $sel_m="";
    }
    echo "<option value='$mt[id]' $sel_m>$mt[name]</option>";
}
?>
        </select>
      </td>

      <td>Entry Range:</td>
      <td>
        <?php
    $p_range=mysqli_fetch_array(mysqli_query($link, "select * from parameter_range where paramid='$id'"));

?>
        <input type="text" id="ent_range" value="<?php echo $p_range[e_range];?>" />
      </td>
    </tr>
    <tr>
      <td>Sample</td>
      <td>
        <select id="samp">
          <option value="0">--Select--</option>
          <?php
    $sam=mysqli_query($GLOBALS["___mysqli_ston"], "select * from  Sample order by Name");
while($s=mysqli_fetch_array($sam)) {
    if($pdata[sample]==$s[ID]) {
        echo $sel="Selected='selected'";
    } else {
        $sel="";
    }
    echo "<option value='$s[ID]' $sel>$s[Name]</option>";
}
?>
        </select>
      </td>

      <td>Vaccu</td>
      <td>
        <select id="vacc">
          <option value="0">--Select--</option>
          <?php
    $vac=mysqli_query($GLOBALS["___mysqli_ston"], "select * from vaccu_master order by type");
while($v=mysqli_fetch_array($vac)) {
    if($pdata[vaccu]==$v[id]) {
        echo $sel2="Selected='selected'";
    } else {
        $sel2="";
    }
    echo "<option value='$v[id]' $sel2>$v[type]</option>";
}
?>
        </select>
      </td>
    </tr>
    <?php
    if($id)
    {
		$trStyle="";
	}
	else
    {
		$trStyle="display:none;";
	}
    ?>
    <tr id="form_tr" style="<?php echo $trStyle;?>">
      <td>Formula</td>
      <td onclick="load_more_info('4','<?php echo $id?>')" style="cursor:pointer"><i>Click to set/view</i></td>

      <td>Referrence Range</td>
      <td onclick="load_more_info('3','<?php echo $id?>')" id="ref_tr" style="cursor:pointer"><i>Click to set/view</i>
      </td>
    </tr>
    <tr>
      <td colspan="4" style="text-align:center">
        <input type="button" id="save" name="save" value="Save" onclick="save_param(<?php echo $id;?>)" class="btn btn-info" />
        <input type="button" id="close" name="close" value="Close" class="btn btn-danger" onclick="$('#mod').click()" />
      </td>
    </tr>
  </table>
</div>