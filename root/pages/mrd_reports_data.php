<?php
session_start();

include("../../includes/connection.php");
include("../../includes/global.function.php");
include("../../includes/idgeneration.function.php");

$c_user = $_SESSION["emp_id"];
$ip_addr = $_SERVER["REMOTE_ADDR"];

$date = date("Y-m-d");
$time = date("H:i:s");

$c_user = trim($_SESSION['emp_id']);
$emp_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$c_user' "));

$branch_id = $emp_info["branch_id"];

$discount_element_disable = "";
if ($emp_info["discount_permission"] == 0) {
    $discount_element_disable = "readonly";
}

if ($_POST["type"] == "load_district_pat") {
    $val = $_POST["val"];
    $state_qry = mysqli_query($link, " SELECT * FROM `district` WHERE `state_id`='$val' ORDER BY `name` ");
    echo "<option value='0'>All</option>";
    while ($state = mysqli_fetch_array($state_qry)) {
        //if($state['state_id']=='4'){ $sel_state="selected"; }else{ $sel_state=""; }
        echo "<option value='$state[district_id]' $sel_state >$state[name]</option>";
    }
}
if ($_POST["type"] == "load_all_pat") {
    $pat_type = $_POST["pat_type"];
    $branch_id = $_POST["branch_id"];
    $fdate = $_POST["from"];
    $tdate = $_POST["to"];
    $pat_name = $_POST["pat_name"];
    $pat_uhid = $_POST["pat_uhid"];
    $pin = $_POST["pin"];
    $phone = $_POST["phone"];
    $state = $_POST["state"];
    $district = $_POST["district"];
    $ref_doc_id = $_POST["ref_doc_id"];
    $health_guide_id = $_POST["health_guide_id"];
    $list_start = $_POST["list_start"];

    $q = " SELECT * FROM `uhid_and_opdid` WHERE `slno`>0 ";

    $z = 0;

    if ($fdate && $tdate) {
        $q = " SELECT * FROM `uhid_and_opdid` WHERE `date` between '$fdate' and '$tdate' ";
        $z = 1;
    }
    if (strlen($pat_name) > 2) {
        $q .= " AND `patient_id` in ( SELECT `patient_id` FROM `patient_info` WHERE `name` like '%$pat_name%' ) ";
        $z = 1;
    }

    if ($state != '0' && $district != '0') {
        $q .= " AND `patient_id` in ( SELECT `patient_id` FROM `patient_info_rel` WHERE `state`='$state' AND `district`='$district' ) ";
        $z = 1;
    } else if ($state != '0') {
        $q .= " AND `patient_id` in ( SELECT `patient_id` FROM `patient_info_rel` WHERE `state`='$state' ) ";
        $z = 1;
    } else if ($district != 'null') {
        $q .= " AND `patient_id` in ( SELECT `patient_id` FROM `patient_info_rel` WHERE `district`='$district' ) ";
        $z = 1;
    }
    if (strlen($phone) > 3) {
        $q .= " AND `patient_id` in ( SELECT `patient_id` FROM `patient_info` WHERE `phone` like '$phone%' ) ";
        $z = 1;
    }
    if (strlen($pat_uhid) > 2) {
        $q .= " AND `patient_id` like '$pat_uhid%' ";
        $z = 1;
    }
    if (strlen($pin) > 2) {
        $q .= " AND `opd_id` like '$pin%' ";
        $z = 1;
    }

    if ($health_guide_id) {
        $q .= " AND `patient_id` in ( SELECT `patient_id` FROM `pat_health_guide` WHERE `hguide_id`='$health_guide_id' )";
        $z = 1;
    }

    if ($z == 0) {
        $q = " SELECT * FROM `uhid_and_opdid` WHERE `date`='$date' and `slno`>0 ";
    }

    if ($pat_type > 0) {
        $q .= " AND `type`='$pat_type' ";
    }

    //~ $q.=" AND `branch_id`='$branch_id' order by `slno` DESC limit ".$list_start;
    $q .= " order by `slno` DESC limit " . $list_start;

    //echo $q;

    $pat_reg_qry = mysqli_query($link, $q);

?>
<table class="table table-bordered text-center">
    <thead class="table_header_fix">
        <tr>
            <th>#</th>
            <th>UHID</th>
            <th>Bill No.</th>
            <th>Patient Name</th>
            <th>Age</th>
            <th>Sex</th>
            <th>Phone</th>
            <th>Date</th>
            <th>Type</th>
            <th>User</th>
        </tr>
    </thead>
    <?php
        $n = 1;
        while ($pat_reg = mysqli_fetch_array($pat_reg_qry)) {
            $user_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$pat_reg[user]' "));

            $pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$pat_reg[patient_id]' "));

            $reg_date = $pat_reg["date"];

            if ($pat_info["dob"] != "") {
                $age = age_calculator_date($pat_info["dob"], $reg_date);
            } else {
                $age = $pat_info["age"] . " " . $pat_info["age_type"];
            }

            $pat_typ_text = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));
            $pat_typ = $pat_typ_text['p_type'];

            $cashier_access_num = 1;

            $cancel_request = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `cancel_request` WHERE `patient_id`='$pat_reg[patient_id]' AND `opd_id`='$pat_reg[opd_id]' AND `type`='2' "));
            if ($cancel_request) {
                $td_function = "";
                $td_style = "";
                $tr_back_color = "style='background-color: #ff000021'";

                $emp_info_del = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `employee` WHERE `emp_id`='$cancel_request[user]' "));

                $tr_title = "title='Cancel request by $emp_info_del[name]' id='cancel_request_tr'";
            } else {
                $td_function = "onclick=\"redirect_page('$pat_reg[patient_id]','$pat_reg[opd_id]','$pat_reg[type]','$cashier_access_num')\"";
                $td_style = "style='cursor:pointer;'";
                $tr_back_color = "";
                $tr_title = "";
            }
        ?>
    <tr <?php echo $tr_back_color . " " . $tr_title; ?>>
        <td <?php echo $td_function; ?> <?php echo $td_style; ?>>
            <?php echo $n; ?>
        </td>
        <td <?php echo $td_function; ?> <?php echo $td_style; ?>>
            <?php echo $pat_info["patient_id"]; ?>
        </td>
        <td <?php echo $td_function; ?> <?php echo $td_style; ?>>
            <?php echo $pat_reg["opd_id"]; ?>
        </td>
        <td <?php echo $td_function; ?> <?php echo $td_style; ?>>
            <?php echo $pat_info["name"]; ?>
        </td>
        <td <?php echo $td_function; ?> <?php echo $td_style; ?>>
            <?php echo $age; ?>
        </td>
        <td <?php echo $td_function; ?> <?php echo $td_style; ?>>
            <?php echo $pat_info["sex"]; ?>
        </td>
        <td <?php echo $td_function; ?> <?php echo $td_style; ?>>
            <?php echo $pat_info["phone"]; ?>
        </td>
        <td <?php echo $td_function; ?> <?php echo $td_style; ?>>
            <?php echo convert_date($pat_reg["date"]); ?>
        </td>
        <td <?php echo $td_function; ?> <?php echo $td_style; ?>>
            <?php echo $pat_typ; ?>
        </td>
        <td>
            <?php echo $user_info["name"]; ?>
        </td>
    </tr>
    <?php
            $n++;
        }
        ?>
</table>

<?php
}
if ($_POST["type"] == "load_all_pat_mrd") {
    $patient_id = $_POST["uhid"];
    $ipd_id = $_POST["pin"];

    $pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));

    $pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_id' "));

    $reg_date = $pat_reg["date"];

    if ($pat_info["dob"] != "") {
        $age = age_calculator_date($pat_info["dob"], $reg_date);
    } else {
        $age = $pat_info["age"] . " " . $pat_info["age_type"];
    }
?>
<div>
    <table class="table table-bordered text-center">
        <thead class="table_header_fix">
            <tr>
                <th>UHID</th>
                <th>Bill No.</th>
                <th>Patient Name</th>
                <th>Age</th>
                <th>Sex</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tr>
            <td><?php echo $pat_info["patient_id"]; ?></td>
            <td><?php echo $pat_reg["opd_id"]; ?></td>
            <td><?php echo $pat_info["name"]; ?></td>
            <td><?php echo $age; ?></td>
            <td><?php echo $pat_info["sex"]; ?></td>
            <td><?php echo $pat_info["phone"]; ?></td>
        </tr>
    </table>
    <?php
        $pat_vital = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_vital` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' "));
        if ($pat_vital) {
        ?>
    <button class="btn btn-success"
        onclick="print_mrd('Patient Vital Record','<?php echo $pat_info["patient_id"]; ?>','<?php echo $pat_reg["opd_id"]; ?>')"
        style="margin: 10px 0px;"><i class="icon-print"></i> Vitals Record</button>
    <?php
        }
        ?>
    <?php
        $pat_insulin = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_insulin_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' "));
        if ($pat_insulin) {
        ?>
    <button class="btn btn-success"
        onclick="print_mrd('Patient Insuline Record','<?php echo $pat_info["patient_id"]; ?>','<?php echo $pat_reg["opd_id"]; ?>')"
        style="margin: 10px 0px;"><i class="icon-print"></i> Insuline Record</button>
    <?php
        }
        ?>
    <?php
        $pat_consultation = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_ip_consultation` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' "));
        if ($pat_consultation) {
        ?>
    <button class="btn btn-success"
        onclick="print_mrd('Doctor Consultation Record','<?php echo $pat_info["patient_id"]; ?>','<?php echo $pat_reg["opd_id"]; ?>')"
        style="margin: 10px 0px;"><i class="icon-print"></i> Doctor Consultation Record</button>
    <?php
        }
        ?>
    <?php
        $pat_diagnosis = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_diagnosis` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' "));
        if ($pat_diagnosis) {
        ?>
    <button class="btn btn-success"
        onclick="print_mrd('Patient Diagnosis Record','<?php echo $pat_info["patient_id"]; ?>','<?php echo $pat_reg["opd_id"]; ?>')"
        style="margin: 10px 0px;"><i class="icon-print"></i> Patient Diagnosis Record</button>
    <?php
        }
        ?>
    <?php
        $pat_examination = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_examination` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' "));
        if ($pat_examination) {
        ?>
    <button class="btn btn-success"
        onclick="print_mrd('Patient Examination Record','<?php echo $pat_info["patient_id"]; ?>','<?php echo $pat_reg["opd_id"]; ?>')"
        style="margin: 10px 0px;"><i class="icon-print"></i> Patient Examination Record</button>
    <?php
        }
        ?>
    <?php
        $pat_history = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `pat_examination` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' "));
        if ($pat_history) {
        ?>
    <button class="btn btn-success"
        onclick="print_mrd('Patient History Record','<?php echo $pat_info["patient_id"]; ?>','<?php echo $pat_reg["opd_id"]; ?>')"
        style="margin: 10px 0px;"><i class="icon-print"></i> Patient History Record</button>
    <?php
        }
        ?>
    <?php
        $pat_invest = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_test_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' and `type`>3 "));
        if ($pat_invest) {
        ?>
    <button class="btn btn-success"
        onclick="print_mrd('Patient Investigaiton Record','<?php echo $pat_info["patient_id"]; ?>','<?php echo $pat_reg["opd_id"]; ?>')"
        style="margin: 10px 0px;"><i class="icon-print"></i> Patient Investigation Record</button>
    <?php
        }
        ?>

    <?php
        $pat_complaint = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_pat_complaints` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' "));
        if ($pat_complaint) {
        ?>
    <button class="btn btn-success"
        onclick="print_mrd('Patient Complaint Record','<?php echo $pat_info["patient_id"]; ?>','<?php echo $pat_reg["opd_id"]; ?>')"
        style="margin: 10px 0px;"><i class="icon-print"></i> Patient Complaint Record</button>
    <?php
        }
        ?>

    <?php
        $pat_complaint = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_ot_schedule` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' "));
        if ($pat_complaint) {
        ?>
    <button class="btn btn-success"
        onclick="print_mrd('Patient OT Record','<?php echo $pat_info["patient_id"]; ?>','<?php echo $pat_reg["opd_id"]; ?>')"
        style="margin: 10px 0px;"><i class="icon-print"></i> Patient OT Record</button>
    <?php
        }
        ?>
    <?php
        $f1 = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `ipd_case_sheet_AB` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
        $f2 = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `ipd_case_sheet_CF` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
        $f3 = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `ipd_case_sheet_GL` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
        $f4 = mysqli_num_rows(mysqli_query($link, "SELECT * FROM `ipd_case_sheet_LL` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));

        $f_num = $f1 + $f2 + $f3 + $f4;
        if ($f_num > 0) {
        ?>
    <button class="btn btn-success"
        onclick="print_mrd('Patient Medical Case Sheet Record','<?php echo $pat_info["patient_id"]; ?>','<?php echo $pat_reg["opd_id"]; ?>')"
        style="margin: 10px 0px;"><i class="icon-print"></i> Patient Medical Case Sheet Record</button>
    <?php
        }
        ?>

    <button class="btn btn-success"
        onclick="print_mrd('Patient Medicine Administered Record','<?php echo $pat_info["patient_id"]; ?>','<?php echo $pat_reg["opd_id"]; ?>')"
        style="margin: 10px 0px;"><i class="icon-print"></i> Patient Medicine Administered Record</button>

    <button class="btn btn-success"
        onclick="print_mrd('Patient Discharge Summary','<?php echo $pat_info["patient_id"]; ?>','<?php echo $pat_reg["opd_id"]; ?>')"
        style="margin: 10px 0px;"><i class="icon-print"></i> Patient Discharge Summary</button>

    <?php
        $service_qry = mysqli_query($link, "SELECT DISTINCT `group_id` AS `g_id` FROM `ipd_pat_service_details` WHERE `ipd_id` = '$pat_reg[opd_id]'");

        while ($services = mysqli_fetch_array($service_qry)) {
            $grp = mysqli_fetch_array(mysqli_query($link, "SELECT `group_name` FROM `charge_group_master` WHERE `group_id` = '$services[g_id]'"));

            // echo "SELECT `group_name` FROM `charge_group_master` WHERE `group_id` = '$services[g_id]';";
        ?>
    <button class="btn btn-success"
        onclick="print_mrd('<?php echo $grp['group_name']; ?>', '<?php echo $pat_info['patient_id']; ?>', '<?php echo $pat_reg['opd_id'] ?>')"
        style="margin: 10px 0px;"><i class="icon-print"></i>
        <?php echo mb_convert_case($grp['group_name'], MB_CASE_TITLE); ?> Record</button>

    <?php



        }


        ?>
    <br>
    <br>
    <button style="display: none" class="btn btn-primary"><i class="icon-file"></i> Upload Files</button>
    <center>
        <button class="btn btn-inverse" onclick="view_all()"><i class="icon-backward"></i> Back</button>
    </center>
</div>
<?php
}

if ($_POST["type"] == "Patient Vital Record") {
    $patient_id = $_POST["uhid"];
    $ipd_id = $_POST["ipd_id"];
    $user = $_POST["user"];

    $pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));

    $pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_id' "));

    $reg_date = $pat_reg["date"];

    if ($pat_info["dob"] != "") {
        $age = age_calculator_date($pat_info["dob"], $reg_date);
    } else {
        $age = $pat_info["age"] . " " . $pat_info["age_type"];
    }

    $prefix_det = mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

    $pat_admit = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_id' ORDER BY `slno` DESC limit 0,1 "));

    $ward = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_admit[ward_id]' "));

    $bed = mysqli_fetch_array(mysqli_query($link, " SELECT `room_id`,`bed_no` FROM `bed_master` WHERE `bed_id`='$pat_admit[bed_id]' "));

    $room = mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' "));

    $discharge_time_str = "NA";
    $discharge_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
    if ($discharge_info) {
        $discharge_time_str = date("d-M-Y", strtotime($discharge_info["date"])) . " " . date("h:i A", strtotime($discharge_info["time"]));
    }
?>
<table class="table table-condensed">
    <tr>
        <th style="width: 100px;">UHID</th>
        <td style="width: auto;">: <?php echo $pat_info["patient_id"]; ?></td>
        <th style="width: 95px;"><?php echo $prefix_det["prefix"]; ?></th>
        <td>: <?php echo $pat_reg["opd_id"]; ?></td>
    </tr>
    <tr>
        <th>Registration Time</th>
        <td>: <?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?>
            <?php echo date("h:i A", strtotime($pat_reg["time"])); ?></td>
        <th>Discharged Time</th>
        <td>: <?php echo $discharge_time_str; ?></td>
    </tr>
    <tr>
        <th>Patient Name</th>
        <td>: <?php echo $pat_info["name"]; ?></td>
        <th>Patient Age</th>
        <td>: <?php echo $age; ?></td>
    </tr>
    <tr>
        <th>Patient Gender</th>
        <td>: <?php echo $pat_info["sex"]; ?></td>
        <th>Room Category</th>
        <td>: <?php echo $ward["name"]; ?>, <span><?php echo $room["room_no"]; ?></span>, <span>Bed No:
                <?php echo $bed["bed_no"]; ?></span></td>
    </tr>
</table>
<table class="table table-condensed table-bordered table-hover text-center">
    <thead class="table_header_fix">
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Pulse</th>
            <th>B.P.</th>
            <th>Temperature(<sup>0</sup>C)</th>
            <th>SPO<sub>2</sub>(%)</th>
            <th>Intake Output</th>
        </tr>
    </thead>
    <?php
        $dist_date_qry = mysqli_query($link, " SELECT DISTINCT `record_date` FROM `ipd_pat_vital` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' ORDER BY `record_date` ASC, `record_time` ASC");
        while ($dist_date = mysqli_fetch_array($dist_date_qry)) {
            $data_qry = mysqli_query($link, " SELECT * FROM `ipd_pat_vital` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' AND `record_date`='$dist_date[record_date]' ORDER BY `record_date` ASC, `record_time` ASC ");
            $data_num = mysqli_num_rows($data_qry);
            $i = 1;
            while ($data = mysqli_fetch_array($data_qry)) {
        ?>
    <tr>
        <?php if ($i == 1) { ?>
        <td rowspan="<?php echo $data_num; ?>"><?php echo date("d-M-Y", strtotime($data["record_date"])); ?></td>
        <?php } ?>
        <td><?php echo date("h:i A", strtotime($data["record_time"])); ?></td>
        <td><?php echo $data["pulse"]; ?></td>
        <td><?php echo $data["systolic"]; ?>/<?php echo $data["diastolic"]; ?></td>
        <td><?php echo $data["temp"]; ?></td>
        <td><?php echo $data["spo2"]; ?></td>
        <td><?php echo $data["intake_output_record"]; ?></td>
    </tr>
    <?php
                $i++;
            }
        }
        ?>
</table>
<?php
}

if ($_POST["type"] == "Patient Insuline Record") {
    $patient_id = $_POST["uhid"];
    $ipd_id = $_POST["ipd_id"];
    $user = $_POST["user"];

    $pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));

    $pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_id' "));

    $reg_date = $pat_reg["date"];

    if ($pat_info["dob"] != "") {
        $age = age_calculator_date($pat_info["dob"], $reg_date);
    } else {
        $age = $pat_info["age"] . " " . $pat_info["age_type"];
    }

    $prefix_det = mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

    $pat_admit = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_id' ORDER BY `slno` DESC limit 0,1 "));

    $ward = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_admit[ward_id]' "));

    $bed = mysqli_fetch_array(mysqli_query($link, " SELECT `room_id`,`bed_no` FROM `bed_master` WHERE `bed_id`='$pat_admit[bed_id]' "));

    $room = mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' "));

    $discharge_time_str = "NA";
    $discharge_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
    if ($discharge_info) {
        $discharge_time_str = date("d-M-Y", strtotime($discharge_info["date"])) . " " . date("h:i A", strtotime($discharge_info["time"]));
    }
?>
<table class="table table-condensed">
    <tr>
        <th style="width: 100px;">UHID</th>
        <td style="width: auto;">: <?php echo $pat_info["patient_id"]; ?></td>
        <th style="width: 95px;"><?php echo $prefix_det["prefix"]; ?></th>
        <td>: <?php echo $pat_reg["opd_id"]; ?></td>
    </tr>
    <tr>
        <th>Registration Time</th>
        <td>: <?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?>
            <?php echo date("h:i A", strtotime($pat_reg["time"])); ?></td>
        <th>Discharged Time</th>
        <td>: <?php echo $discharge_time_str; ?></td>
    </tr>
    <tr>
        <th>Patient Name</th>
        <td>: <?php echo $pat_info["name"]; ?></td>
        <th>Patient Age</th>
        <td>: <?php echo $age; ?></td>
    </tr>
    <tr>
        <th>Patient Gender</th>
        <td>: <?php echo $pat_info["sex"]; ?></td>
        <th>Room Category</th>
        <td>: <?php echo $ward["name"]; ?>, <span><?php echo $room["room_no"]; ?></span>, <span>Bed No:
                <?php echo $bed["bed_no"]; ?></span></td>
    </tr>
</table>
<table class="table table-condensed table-bordered table-hover text-center">
    <thead class="table_header_fix">
        <tr>
            <th>Date</th>
            <th>Time</th>
            <?php
                $insuline_qry = mysqli_query($link, " SELECT `insulin_id`, `name` FROM `insulin_type_master` WHERE `insulin_id` IN(SELECT DISTINCT `insulin_id` FROM `ipd_pat_insulin_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id') ");
                while ($insuline_info = mysqli_fetch_array($insuline_qry)) {
                    echo "<th>$insuline_info[name]</th>";
                }
                ?>
        </tr>
    </thead>
    <?php
        $dist_date_qry = mysqli_query($link, " SELECT DISTINCT `given_date` FROM `ipd_pat_insulin_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' ORDER BY `given_date` ASC, `given_time` ASC");
        while ($dist_date = mysqli_fetch_array($dist_date_qry)) {
            $data_qry = mysqli_query($link, " SELECT * FROM `ipd_pat_insulin_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' AND `given_date`='$dist_date[given_date]' ORDER BY `given_date` ASC, `given_time` ASC ");
            $data_num = mysqli_num_rows($data_qry);
            $i = 1;
            while ($data = mysqli_fetch_array($data_qry)) {
        ?>
    <tr>
        <?php if ($i == 1) { ?>
        <td rowspan="<?php echo $data_num; ?>"><?php echo date("d-M-Y", strtotime($data["given_date"])); ?></td>
        <?php } ?>
        <td><?php echo date("h:i A", strtotime($data["given_time"])); ?></td>
        <?php
                    $insuline_qry = mysqli_query($link, " SELECT `insulin_id`, `name` FROM `insulin_type_master` WHERE `insulin_id` IN(SELECT DISTINCT `insulin_id` FROM `ipd_pat_insulin_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id') ");
                    while ($insuline_info = mysqli_fetch_array($insuline_qry)) {
                        if ($insuline_info["insulin_id"] == $data["insulin_id"]) {
                            echo "<td>$data[dosage]</td>";
                        } else {
                            echo "<td></td>";
                        }
                    }
                    $i++;
                    ?>
    </tr>
    <?php
            }
        }
        ?>
</table>
<?php
}

if ($_POST["type"] == "Patient Medicine Administered Record") {
    $patient_id = $_POST["uhid"];
    $ipd_id = $_POST["ipd_id"];
    $user = $_POST["user"];

    $pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));

    $pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_id' "));

    $reg_date = $pat_reg["date"];

    if ($pat_info["dob"] != "") {
        $age = age_calculator_date($pat_info["dob"], $reg_date);
    } else {
        $age = $pat_info["age"] . " " . $pat_info["age_type"];
    }

    $prefix_det = mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

    $pat_admit = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_id' ORDER BY `slno` DESC limit 0,1 "));

    $ward = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_admit[ward_id]' "));

    $bed = mysqli_fetch_array(mysqli_query($link, " SELECT `room_id`,`bed_no` FROM `bed_master` WHERE `bed_id`='$pat_admit[bed_id]' "));

    $room = mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' "));

    $discharge_time_str = "NA";
    $discharge_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
    if ($discharge_info) {
        $discharge_time_str = date("d-M-Y", strtotime($discharge_info["date"])) . " " . date("h:i A", strtotime($discharge_info["time"]));
    }
?>
<table class="table table-condensed">
    <tr>
        <th style="width: 100px;">UHID</th>
        <td style="width: auto;">: <?php echo $pat_info["patient_id"]; ?></td>
        <th style="width: 95px;"><?php echo $prefix_det["prefix"]; ?></th>
        <td>: <?php echo $pat_reg["opd_id"]; ?></td>
    </tr>
    <tr>
        <th>Registration Time</th>
        <td>: <?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?>
            <?php echo date("h:i A", strtotime($pat_reg["time"])); ?></td>
        <th>Discharged Time</th>
        <td>: <?php echo $discharge_time_str; ?></td>
    </tr>
    <tr>
        <th>Patient Name</th>
        <td>: <?php echo $pat_info["name"]; ?></td>
        <th>Patient Age</th>
        <td>: <?php echo $age; ?></td>
    </tr>
    <tr>
        <th>Patient Gender</th>
        <td>: <?php echo $pat_info["sex"]; ?></td>
        <th>Room Category</th>
        <td>: <?php echo $ward["name"]; ?>, <span><?php echo $room["room_no"]; ?></span>, <span>Bed No:
                <?php echo $bed["bed_no"]; ?></span></td>
    </tr>
</table>
<table class="table table-condensed table-bordered table-hover text-center">
    <thead class="table_header_fix">
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Medicine</th>
            <th>Dosage</th>
            <th>Given By</th>
        </tr>
    </thead>
    <tr>
        <td rowspan="2">23-March-2021</td>
        <td>7:30 AM</td>
        <td>CAP PAN D</td>
        <td>1 Tab</td>
        <td>M PATGIRI</td>
    </tr>
    <tr>
        <td>8:00 PM</td>
        <td>CAP PAN D</td>
        <td>1 Tab</td>
        <td>M PATGIRI</td>
    </tr>
    <tr>
        <td rowspan="2">23-March-2021</td>
        <td>7:30 AM</td>
        <td>TOTALAX NF SYP</td>
        <td>5ml</td>
        <td>CHANDAN DAS</td>
    </tr>
    <tr>
        <td>8:00 PM</td>
        <td>TAB MEROSURE O</td>
        <td>1 Tab</td>
        <td>M PATGIRI</td>
    </tr>
    <tr>
        <td rowspan="2">23-March-2021</td>
        <td>7:30 AM</td>
        <td>TOTALAX NF SYP</td>
        <td>5ml</td>
        <td>CHANDAN DAS</td>
    </tr>
    <tr>
        <td>8:00 PM</td>
        <td>TAB MEROSURE O</td>
        <td>1 Tab</td>
        <td>M PATGIRI</td>
    </tr>
    <tr>
        <td rowspan="2">24-March-2021</td>
        <td>7:30 AM</td>
        <td>CAP PAN D</td>
        <td>1 Tab</td>
        <td>CHANDAN DAS</td>
    </tr>
    <tr>
        <td>8:00 PM</td>
        <td>CAP PAN D</td>
        <td>1 Tab</td>
        <td>M PATGIRI</td>
    </tr>
    <tr>
        <td rowspan="2">24-March-2021</td>
        <td>7:30 AM</td>
        <td>TOTALAX NF SYP</td>
        <td>5ml</td>
        <td>CHANDAN DAS</td>
    </tr>
    <tr>
        <td>8:00 PM</td>
        <td>TAB MEROSURE O</td>
        <td>1 Tab</td>
        <td>M PATGIRI</td>
    </tr>
    <tr>
        <td rowspan="2">24-March-2021</td>
        <td>7:30 AM</td>
        <td>TOTALAX NF SYP</td>
        <td>5ml</td>
        <td>CHANDAN DAS</td>
    </tr>
    <tr>
        <td>8:00 PM</td>
        <td>TAB MEROSURE O</td>
        <td>1 Tab</td>
        <td>M PATGIRI</td>
    </tr>
    <tr>
        <td rowspan="2">25-March-2021</td>
        <td>7:30 AM</td>
        <td>CAP PAN D</td>
        <td>1 Tab</td>
        <td>CHANDAN DAS</td>
    </tr>
    <tr>
        <td>8:00 PM</td>
        <td>CAP PAN D</td>
        <td>1 Tab</td>
        <td>M PATGIRI</td>
    </tr>
    <tr>
        <td rowspan="2">25-March-2021</td>
        <td>7:30 AM</td>
        <td>TOTALAX NF SYP</td>
        <td>5ml</td>
        <td>CHANDAN DAS</td>
    </tr>
    <tr>
        <td>8:00 PM</td>
        <td>TAB MEROSURE O</td>
        <td>1 Tab</td>
        <td>M PATGIRI</td>
    </tr>
    <tr>
        <td rowspan="2">25-March-2021</td>
        <td>7:30 AM</td>
        <td>TOTALAX NF SYP</td>
        <td>5ml</td>
        <td>CHANDAN DAS</td>
    </tr>
    <tr>
        <td>8:00 PM</td>
        <td>TAB MEROSURE O</td>
        <td>1 Tab</td>
        <td>M PATGIRI</td>
    </tr>
</table>
<?php
}

if ($_POST["type"] == "Doctor Consultation Record") {
    $patient_id = $_POST["uhid"];
    $ipd_id = $_POST["ipd_id"];
    $user = $_POST["user"];

    $pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));

    $pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_id' "));

    $reg_date = $pat_reg["date"];

    if ($pat_info["dob"] != "") {
        $age = age_calculator_date($pat_info["dob"], $reg_date);
    } else {
        $age = $pat_info["age"] . " " . $pat_info["age_type"];
    }

    $prefix_det = mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

    $pat_admit = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_id' ORDER BY `slno` DESC limit 0,1 "));

    $ward = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_admit[ward_id]' "));

    $bed = mysqli_fetch_array(mysqli_query($link, " SELECT `room_id`,`bed_no` FROM `bed_master` WHERE `bed_id`='$pat_admit[bed_id]' "));

    $room = mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' "));

    $discharge_time_str = "NA";
    $discharge_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
    if ($discharge_info) {
        $discharge_time_str = date("d-M-Y", strtotime($discharge_info["date"])) . " " . date("h:i A", strtotime($discharge_info["time"]));
    }
?>
<table class="table table-condensed">
    <tr>
        <th style="width: 100px;">UHID</th>
        <td style="width: auto;">: <?php echo $pat_info["patient_id"]; ?></td>
        <th style="width: 95px;"><?php echo $prefix_det["prefix"]; ?></th>
        <td>: <?php echo $pat_reg["opd_id"]; ?></td>
    </tr>
    <tr>
        <th>Registration Time</th>
        <td>: <?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?>
            <?php echo date("h:i A", strtotime($pat_reg["time"])); ?></td>
        <th>Discharged Time</th>
        <td>: <?php echo $discharge_time_str; ?></td>
    </tr>
    <tr>
        <th>Patient Name</th>
        <td>: <?php echo $pat_info["name"]; ?></td>
        <th>Patient Age</th>
        <td>: <?php echo $age; ?></td>
    </tr>
    <tr>
        <th>Patient Gender</th>
        <td>: <?php echo $pat_info["sex"]; ?></td>
        <th>Room Category</th>
        <td>: <?php echo $ward["name"]; ?>, <span><?php echo $room["room_no"]; ?></span>, <span>Bed No:
                <?php echo $bed["bed_no"]; ?></span></td>
    </tr>
</table>
<table class="table table-condensed table-bordered table-hover text-center">
    <thead class="table_header_fix">
        <tr>
            <th>Date</th>
            <th>Treatment</th>
            <th>Doctor</th>
        </tr>
    </thead>
    <?php
        $dist_date_qry = mysqli_query($link, " SELECT DISTINCT `note_date` FROM `ipd_ip_consultation` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' ORDER BY `note_date` ASC, `id` ASC");
        while ($dist_date = mysqli_fetch_array($dist_date_qry)) {
            $data_qry = mysqli_query($link, " SELECT * FROM `ipd_ip_consultation` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' AND `note_date`='$dist_date[note_date]' ORDER BY `note_date` ASC, `id` ASC ");
            $data_num = mysqli_num_rows($data_qry);
            $i = 1;
            while ($data = mysqli_fetch_array($data_qry)) {
                $doc_info = mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$data[consultantdoctorid]'"));
        ?>
    <tr>
        <?php if ($i == 1) { ?>
        <td rowspan="<?php echo $data_num; ?>"><?php echo date("d-M-Y", strtotime($data["note_date"])); ?></td>
        <?php } ?>
        <td><?php echo $data["note"]; ?></td>
        <td><?php echo $doc_info["Name"]; ?></td>
    </tr>
    <?php
                $i++;
            }
        }
        ?>
</table>
<?php
}

if ($_POST["type"] == "Patient Diagnosis Record") {
    $patient_id = $_POST["uhid"];
    $ipd_id = $_POST["ipd_id"];
    $user = $_POST["user"];

    $pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));

    $pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_id' "));

    $reg_date = $pat_reg["date"];

    if ($pat_info["dob"] != "") {
        $age = age_calculator_date($pat_info["dob"], $reg_date);
    } else {
        $age = $pat_info["age"] . " " . $pat_info["age_type"];
    }

    $prefix_det = mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

    $pat_admit = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_id' ORDER BY `slno` DESC limit 0,1 "));

    $ward = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_admit[ward_id]' "));

    $bed = mysqli_fetch_array(mysqli_query($link, " SELECT `room_id`,`bed_no` FROM `bed_master` WHERE `bed_id`='$pat_admit[bed_id]' "));

    $room = mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' "));

    $discharge_time_str = "NA";
    $discharge_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
    if ($discharge_info) {
        $discharge_time_str = date("d-M-Y", strtotime($discharge_info["date"])) . " " . date("h:i A", strtotime($discharge_info["time"]));
    }
?>
<table class="table table-condensed">
    <tr>
        <th style="width: 100px;">UHID</th>
        <td style="width: auto;">: <?php echo $pat_info["patient_id"]; ?></td>
        <th style="width: 95px;"><?php echo $prefix_det["prefix"]; ?></th>
        <td>: <?php echo $pat_reg["opd_id"]; ?></td>
    </tr>
    <tr>
        <th>Registration Time</th>
        <td>: <?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?>
            <?php echo date("h:i A", strtotime($pat_reg["time"])); ?></td>
        <th>Discharged Time</th>
        <td>: <?php echo $discharge_time_str; ?></td>
    </tr>
    <tr>
        <th>Patient Name</th>
        <td>: <?php echo $pat_info["name"]; ?></td>
        <th>Patient Age</th>
        <td>: <?php echo $age; ?></td>
    </tr>
    <tr>
        <th>Patient Gender</th>
        <td>: <?php echo $pat_info["sex"]; ?></td>
        <th>Room Category</th>
        <td>: <?php echo $ward["name"]; ?>, <span><?php echo $room["room_no"]; ?></span>, <span>Bed No:
                <?php echo $bed["bed_no"]; ?></span></td>
    </tr>
</table>
<table class="table table-condensed table-bordered table-hover text-center">
    <thead class="table_header_fix">
        <tr>
            <th>Date</th>
            <th>Diagnosis</th>
            <th>Order</th>
            <th>Certainity</th>
            <th>Diagnosed By</th>
        </tr>
    </thead>
    <?php
        $dist_date_qry = mysqli_query($link, " SELECT DISTINCT `date` FROM `ipd_pat_diagnosis` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' ORDER BY `date` ASC");
        while ($dist_date = mysqli_fetch_array($dist_date_qry)) {
            $data_qry = mysqli_query($link, " SELECT * FROM `ipd_pat_diagnosis` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' AND `date`='$dist_date[date]' ORDER BY `date` ASC ");
            $data_num = mysqli_num_rows($data_qry);
            $i = 1;
            while ($data = mysqli_fetch_array($data_qry)) {
                $doc_info = mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$data[consultantdoctorid]'"));
        ?>
    <tr>
        <?php if ($i == 1) { ?>
        <td rowspan="<?php echo $data_num; ?>"><?php echo date("d-M-Y", strtotime($data["date"])); ?></td>
        <?php } ?>
        <td><?php echo $data["diagnosis"]; ?></td>
        <td><?php echo $data["order"]; ?></td>
        <td><?php echo $data["certainity"]; ?></td>
        <td><?php echo $doc_info["Name"]; ?></td>
    </tr>
    <?php
                $i++;
            }
        }
        ?>
</table>
<?php
}

if ($_POST["type"] == "Patient Examination Record") {
    $patient_id = $_POST["uhid"];
    $ipd_id = $_POST["ipd_id"];
    $user = $_POST["user"];

    $pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));

    $pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_id' "));

    $reg_date = $pat_reg["date"];

    if ($pat_info["dob"] != "") {
        $age = age_calculator_date($pat_info["dob"], $reg_date);
    } else {
        $age = $pat_info["age"] . " " . $pat_info["age_type"];
    }

    $prefix_det = mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

    $pat_admit = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_id' ORDER BY `slno` DESC limit 0,1 "));

    $ward = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_admit[ward_id]' "));

    $bed = mysqli_fetch_array(mysqli_query($link, " SELECT `room_id`,`bed_no` FROM `bed_master` WHERE `bed_id`='$pat_admit[bed_id]' "));

    $room = mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' "));

    $discharge_time_str = "NA";
    $discharge_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
    if ($discharge_info) {
        $discharge_time_str = date("d-M-Y", strtotime($discharge_info["date"])) . " " . date("h:i A", strtotime($discharge_info["time"]));
    }
?>
<table class="table table-condensed">
    <tr>
        <th style="width: 100px;">UHID</th>
        <td style="width: auto;">: <?php echo $pat_info["patient_id"]; ?></td>
        <th style="width: 95px;"><?php echo $prefix_det["prefix"]; ?></th>
        <td>: <?php echo $pat_reg["opd_id"]; ?></td>
    </tr>
    <tr>
        <th>Registration Time</th>
        <td>: <?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?>
            <?php echo date("h:i A", strtotime($pat_reg["time"])); ?></td>
        <th>Discharged Time</th>
        <td>: <?php echo $discharge_time_str; ?></td>
    </tr>
    <tr>
        <th>Patient Name</th>
        <td>: <?php echo $pat_info["name"]; ?></td>
        <th>Patient Age</th>
        <td>: <?php echo $age; ?></td>
    </tr>
    <tr>
        <th>Patient Gender</th>
        <td>: <?php echo $pat_info["sex"]; ?></td>
        <th>Room Category</th>
        <td>: <?php echo $ward["name"]; ?>, <span><?php echo $room["room_no"]; ?></span>, <span>Bed No:
                <?php echo $bed["bed_no"]; ?></span></td>
    </tr>
</table>
<table class="table table-condensed table-bordered table-hover text-center">
    <thead class="table_header_fix">
        <tr>
            <th>Date</th>
            <th>Examination</th>
        </tr>
    </thead>
    <?php
        $dist_date_qry = mysqli_query($link, " SELECT DISTINCT `date` FROM `ipd_pat_examination` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' ORDER BY `date` ASC");
        while ($dist_date = mysqli_fetch_array($dist_date_qry)) {
            $data_qry = mysqli_query($link, " SELECT * FROM `ipd_pat_examination` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' AND `date`='$dist_date[date]' ORDER BY `date` ASC ");
            $data_num = mysqli_num_rows($data_qry);
            $i = 1;
            while ($data = mysqli_fetch_array($data_qry)) {
        ?>
    <tr>
        <?php if ($i == 1) { ?>
        <td rowspan="<?php echo $data_num; ?>"><?php echo date("d-M-Y", strtotime($data["date"])); ?></td>
        <?php } ?>
        <td><?php echo $data["examination"]; ?></td>
    </tr>
    <?php
                $i++;
            }
        }
        ?>
</table>
<?php
}
if ($_POST["type"] == "Patient History Record") {
    $patient_id = $_POST["uhid"];
    $ipd_id = $_POST["ipd_id"];
    $user = $_POST["user"];

    $pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));

    $pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_id' "));

    $reg_date = $pat_reg["date"];

    if ($pat_info["dob"] != "") {
        $age = age_calculator_date($pat_info["dob"], $reg_date);
    } else {
        $age = $pat_info["age"] . " " . $pat_info["age_type"];
    }

    $prefix_det = mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

    $pat_admit = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_id' ORDER BY `slno` DESC limit 0,1 "));

    $ward = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_admit[ward_id]' "));

    $bed = mysqli_fetch_array(mysqli_query($link, " SELECT `room_id`,`bed_no` FROM `bed_master` WHERE `bed_id`='$pat_admit[bed_id]' "));

    $room = mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' "));

    $discharge_time_str = "NA";
    $discharge_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
    if ($discharge_info) {
        $discharge_time_str = date("d-M-Y", strtotime($discharge_info["date"])) . " " . date("h:i A", strtotime($discharge_info["time"]));
    }
?>
<table class="table table-condensed">
    <tr>
        <th style="width: 100px;">UHID</th>
        <td style="width: auto;">: <?php echo $pat_info["patient_id"]; ?></td>
        <th style="width: 95px;"><?php echo $prefix_det["prefix"]; ?></th>
        <td>: <?php echo $pat_reg["opd_id"]; ?></td>
    </tr>
    <tr>
        <th>Registration Time</th>
        <td>: <?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?>
            <?php echo date("h:i A", strtotime($pat_reg["time"])); ?></td>
        <th>Discharged Time</th>
        <td>: <?php echo $discharge_time_str; ?></td>
    </tr>
    <tr>
        <th>Patient Name</th>
        <td>: <?php echo $pat_info["name"]; ?></td>
        <th>Patient Age</th>
        <td>: <?php echo $age; ?></td>
    </tr>
    <tr>
        <th>Patient Gender</th>
        <td>: <?php echo $pat_info["sex"]; ?></td>
        <th>Room Category</th>
        <td>: <?php echo $ward["name"]; ?>, <span><?php echo $room["room_no"]; ?></span>, <span>Bed No:
                <?php echo $bed["bed_no"]; ?></span></td>
    </tr>
</table>
<table class="table table-condensed table-bordered table-hover text-center">
    <thead class="table_header_fix">
        <tr>
            <th>Date</th>
            <th>History</th>
        </tr>
    </thead>
    <?php
        $dist_date_qry = mysqli_query($link, " SELECT DISTINCT `date` FROM `pat_examination` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' ORDER BY `date` ASC");
        while ($dist_date = mysqli_fetch_array($dist_date_qry)) {
            $data_qry = mysqli_query($link, " SELECT * FROM `pat_examination` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' AND `date`='$dist_date[date]' ORDER BY `date` ASC ");
            $data_num = mysqli_num_rows($data_qry);
            $i = 1;
            while ($data = mysqli_fetch_array($data_qry)) {
        ?>
    <tr>
        <?php if ($i == 1) { ?>
        <td rowspan="<?php echo $data_num; ?>"><?php echo date("d-M-Y", strtotime($data["date"])); ?></td>
        <?php } ?>
        <td><?php echo $data["history"]; ?></td>
    </tr>
    <?php
                $i++;
            }
        }
        ?>
</table>
<?php
}
if ($_POST["type"] == "Patient Complaint Record") {
    $patient_id = $_POST["uhid"];
    $ipd_id = $_POST["ipd_id"];
    $user = $_POST["user"];

    $pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));

    $pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_id' "));

    $reg_date = $pat_reg["date"];

    if ($pat_info["dob"] != "") {
        $age = age_calculator_date($pat_info["dob"], $reg_date);
    } else {
        $age = $pat_info["age"] . " " . $pat_info["age_type"];
    }

    $prefix_det = mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

    $pat_admit = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_id' ORDER BY `slno` DESC limit 0,1 "));

    $ward = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_admit[ward_id]' "));

    $bed = mysqli_fetch_array(mysqli_query($link, " SELECT `room_id`,`bed_no` FROM `bed_master` WHERE `bed_id`='$pat_admit[bed_id]' "));

    $room = mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' "));

    $discharge_time_str = "NA";
    $discharge_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
    if ($discharge_info) {
        $discharge_time_str = date("d-M-Y", strtotime($discharge_info["date"])) . " " . date("h:i A", strtotime($discharge_info["time"]));
    }
?>
<table class="table table-condensed">
    <tr>
        <th style="width: 100px;">UHID</th>
        <td style="width: auto;">: <?php echo $pat_info["patient_id"]; ?></td>
        <th style="width: 95px;"><?php echo $prefix_det["prefix"]; ?></th>
        <td>: <?php echo $pat_reg["opd_id"]; ?></td>
    </tr>
    <tr>
        <th>Registration Time</th>
        <td>: <?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?>
            <?php echo date("h:i A", strtotime($pat_reg["time"])); ?></td>
        <th>Discharged Time</th>
        <td>: <?php echo $discharge_time_str; ?></td>
    </tr>
    <tr>
        <th>Patient Name</th>
        <td>: <?php echo $pat_info["name"]; ?></td>
        <th>Patient Age</th>
        <td>: <?php echo $age; ?></td>
    </tr>
    <tr>
        <th>Patient Gender</th>
        <td>: <?php echo $pat_info["sex"]; ?></td>
        <th>Room Category</th>
        <td>: <?php echo $ward["name"]; ?>, <span><?php echo $room["room_no"]; ?></span>, <span>Bed No:
                <?php echo $bed["bed_no"]; ?></span></td>
    </tr>
</table>
<table class="table table-condensed table-bordered table-hover text-center">
    <thead class="table_header_fix">
        <tr>
            <th>Date</th>
            <th>Complaint</th>
        </tr>
    </thead>
    <?php
        $dist_date_qry = mysqli_query($link, " SELECT DISTINCT `date` FROM `ipd_pat_complaints` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' ORDER BY `date` ASC");
        while ($dist_date = mysqli_fetch_array($dist_date_qry)) {
            $data_qry = mysqli_query($link, " SELECT * FROM `ipd_pat_complaints` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' AND `date`='$dist_date[date]' ORDER BY `date` ASC ");
            $data_num = mysqli_num_rows($data_qry);
            $i = 1;
            while ($data = mysqli_fetch_array($data_qry)) {
        ?>
    <tr>
        <?php if ($i == 1) { ?>
        <td rowspan="<?php echo $data_num; ?>"><?php echo date("d-M-Y", strtotime($data["date"])); ?></td>
        <?php } ?>
        <td><?php echo $data["comp_one"]; ?> for <?php echo $data["comp_two"]; ?> <?php echo $data["comp_three"]; ?>
        </td>
    </tr>
    <?php
                $i++;
            }
        }
        ?>
</table>
<?php
}

if ($_POST["type"] == "Patient OT Record") {
    $patient_id = $_POST["uhid"];
    $ipd_id = $_POST["ipd_id"];
    $user = $_POST["user"];
    $schedule_id_qry = mysqli_fetch_array(mysqli_query($link, "SELECT `schedule_id` FROM `patient_ot_schedule` WHERE `patient_id` = '$patient_id' AND `ipd_id` = '$ipd_id'"));
    $schedule_id = $schedule_id_qry['schedule_id'];



    $pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));

    $pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_id' "));

    $reg_date = $pat_reg["date"];

    if ($pat_info["dob"] != "") {
        $age = age_calculator_date($pat_info["dob"], $reg_date);
    } else {
        $age = $pat_info["age"] . " " . $pat_info["age_type"];
    }

    $prefix_det = mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

    $pat_admit = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_id' ORDER BY `slno` DESC limit 0,1 "));

    $ward = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_admit[ward_id]' "));

    $bed = mysqli_fetch_array(mysqli_query($link, " SELECT `room_id`,`bed_no` FROM `bed_master` WHERE `bed_id`='$pat_admit[bed_id]' "));

    $room = mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' "));




    $patient_ot_schedule = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `patient_ot_schedule` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' AND `schedule_id`='$schedule_id'"));
    if (!$patient_ot_schedule) {
        echo "<center><h3>Error !</h3></center>";
        echo "SELECT * FROM `patient_ot_schedule` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' AND `schedule_id`='$schedule_id'";
        exit;
    }

    $request_doc_info = mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$patient_ot_schedule[request_doc_id]' "));
    $ot_area = mysqli_fetch_array(mysqli_query($link, "SELECT `ot_area_name` FROM `ot_area_master` WHERE `ot_area_id`='$patient_ot_schedule[ot_area_id]'"));
    $ot_dept = mysqli_fetch_array(mysqli_query($link, "SELECT `ot_dept_name` FROM `ot_dept_master` WHERE `ot_dept_id`='$patient_ot_schedule[ot_dept_id]'"));







    $discharge_time_str = "NA";
    $discharge_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
    if ($discharge_info) {
        $discharge_time_str = date("d-M-Y", strtotime($discharge_info["date"])) . " " . date("h:i A", strtotime($discharge_info["time"]));
    }
?>
<table class="table table-condensed">
    <tr>
        <th style="width: 100px;">UHID</th>
        <td style="width: auto;">: <?php echo $pat_info["patient_id"]; ?></td>
        <th style="width: 95px;"><?php echo $prefix_det["prefix"]; ?></th>
        <td>: <?php echo $pat_reg["opd_id"]; ?></td>
    </tr>
    <tr>
        <th>Registration Time</th>
        <td>: <?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?>
            <?php echo date("h:i A", strtotime($pat_reg["time"])); ?></td>
        <th>Discharged Time</th>
        <td>: <?php echo $discharge_time_str; ?></td>
    </tr>
    <tr>
        <th>Patient Name</th>
        <td>: <?php echo $pat_info["name"]; ?></td>
        <th>Patient Age</th>
        <td>: <?php echo $age; ?></td>
    </tr>
    <tr>
        <th>Patient Gender</th>
        <td>: <?php echo $pat_info["sex"]; ?></td>
        <th>Room Category</th>
        <td>: <?php echo $ward["name"]; ?>, <span><?php echo $room["room_no"]; ?></span>, <span>Bed No:
                <?php echo $bed["bed_no"]; ?></span></td>
    </tr>
</table>
<table class="table table-condensed table-no-top-border">
    <?php
        if ($patient_ot_schedule["ot_date"]) {
        ?>
    <tr>
        <td colspan="2">
            <table class="table table-condensed">
                <tr>
                    <th>OT Date</th>
                    <td><b>: </b><?php echo date("d-m-Y", strtotime($patient_ot_schedule["ot_date"])); ?>
                    </td>
                    <th>OT Start Time</th>
                    <td><b>: </b><?php echo date("h:i A", strtotime($patient_ot_schedule["start_time"])); ?>
                    </td>
                    <th>OT End Time</th>
                    <td><b>: </b><?php echo date("h:i A", strtotime($patient_ot_schedule["end_time"])); ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <?php
            $slno++;
        }
        if ($patient_ot_schedule["procedure_id"]) {
            $procedure_info = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `ot_clinical_procedure` WHERE `procedure_id`='$patient_ot_schedule[procedure_id]'"));
        ?>
    <tr>
        <td colspan="2">
            <b>Procedure :</b><br>

            <div class="results">
                <?php echo nl2br($procedure_info["name"]); ?>
            </div>
        </td>
    </tr>
    <?php
            $slno++;
        }
        if ($patient_ot_schedule["anesthesia_id"]) {
            $anesthesia_info = mysqli_fetch_array(mysqli_query($link, "SELECT `name` FROM `ot_anesthesia_types` WHERE `anesthesia_id`='$patient_ot_schedule[anesthesia_id]'"));
        ?>
    <tr>
        <td colspan="2">
            <b>Anesthesia :</b><br>

            <div class="results">
                <?php echo nl2br($anesthesia_info["name"]); ?>
            </div>
        </td>
    </tr>
    <?php
            $slno++;
        }
        if ($patient_ot_schedule["diagnosis"]) {
        ?>
    <tr>
        <td colspan="2">
            <b>Diagnosis :</b><br>
            <div class="results">
                <?php echo nl2br($patient_ot_schedule["diagnosis"]); ?>
            </div>
        </td>
    </tr>
    <?php
            $slno++;
        }
        if ($patient_ot_schedule["ot_note"]) {
        ?>
    <tr>
        <td colspan="2">
            <b>OT Note :</b><br>
            <div class="results">
                <?php echo nl2br($patient_ot_schedule["ot_note"]); ?>
            </div>
        </td>
    </tr>
    <?php
            $slno++;
        }
        $patient_ot_resources_qry = mysqli_query($link, "SELECT a.*,b.`resource_name` FROM `patient_ot_resources` a, `ot_resource_master` b WHERE a.`resource_id`=b.`resource_id` AND a.`patient_id`='$patient_id' AND a.`ipd_id`='$ipd_id' AND a.`schedule_id`='$schedule_id' ORDER BY b.`sequence` ASC");

        $patient_ot_resources_num = mysqli_num_rows($patient_ot_resources_qry);
        if ($patient_ot_resources_num > 0) {
        ?>
    <tr>
        <td colspan="2">
            <b>OT Resources :</b><br>
            <div class="results">
                <table class="table table-condensed">
                    <?php
                            while ($patient_ot_resources = mysqli_fetch_array($patient_ot_resources_qry)) {
                                $resource_id = $patient_ot_resources["resource_id"];
                                $ot_staff_id = $patient_ot_resources["emp_id"];

                                $staff_info = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `employee` WHERE `emp_id`='$ot_staff_id' "));
                            ?>
                    <tr>
                        <th style="width: 200px;"><?php echo $patient_ot_resources["resource_name"]; ?>
                            <span style="float:right;">:</span>
                        </th>
                        <td><?php echo $staff_info["name"]; ?></td>
                    </tr>
                    <?php
                            }
                        }
                        ?>
                </table>
            </div>
        </td>
    </tr>
</table>
<?php
}



if ($_POST["type"] == "Patient OT Record Old") {
    $patient_id = $_POST["uhid"];
    $ipd_id = $_POST["ipd_id"];
    $user = $_POST["user"];

    $pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));

    $pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND
`opd_id`='$ipd_id' "));

    $reg_date = $pat_reg["date"];

    if ($pat_info["dob"] != "") {
        $age = age_calculator_date($pat_info["dob"], $reg_date);
    } else {
        $age = $pat_info["age"] . " " . $pat_info["age_type"];
    }

    $prefix_det = mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE
`p_type_id`='$pat_reg[type]' "));

    $pat_admit = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE
`patient_id`='$patient_id' and `ipd_id`='$ipd_id' ORDER BY `slno` DESC limit 0,1 "));

    $ward = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_admit[ward_id]'
"));

    $bed = mysqli_fetch_array(mysqli_query($link, " SELECT `room_id`,`bed_no` FROM `bed_master` WHERE
`bed_id`='$pat_admit[bed_id]' "));

    $room = mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]'
"));

    $discharge_time_str = "NA";
    $discharge_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_discharge_details` WHERE
`patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
    if ($discharge_info) {
        $discharge_time_str = date("d-M-Y", strtotime($discharge_info["date"])) . " " . date(
            "h:i A",
            strtotime($discharge_info["time"])
        );
    }
    $qry = mysqli_query($link, "SELECT * FROM `ot_notes` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id' ");
    $num = mysqli_num_rows($qry);
    if ($num > 0) {
        $nt = mysqli_fetch_array($qry);
        $asa = $nt['asa'];
        $asa_stat = $nt['asa_stat'];
        $ident = $nt['identify'];
        $consent = $nt['consent'];
        $oral = $nt['pre_oprative_oral'];
        $pr = $nt['pr'];
        $bp = $nt['bp'];
        $heart = $nt['heart'];
        $anaes_type = $nt['anaes_type'];
        $ecg = $nt['ecg'];
        $spo = $nt['spo'];
        $nibp = $nt['nibp'];
        $temp = $nt['temp'];
        $proc = $nt['procedure_perform'];
        $pos = $nt['patient_pos'];
        $incision = $nt['incision'];
    } else {
        $asa = "";
        $asa_stat = "";
        $ident = "";
        $consent = "";
        $oral = "";
        $pr = "";
        $bp = "";
        $heart = "";
        $anaes_type = "";
        $ecg = "";
        $spo = "";
        $nibp = "";
        $temp = "";
        $proc = "";
        $pos = "";
        $incision = "";
    }
?>
<table class="table table-condensed">
    <tr>
        <th style="width: 100px;">UHID</th>
        <td style="width: auto;">: <?php echo $pat_info["patient_id"]; ?></td>
        <th style="width: 95px;"><?php echo $prefix_det["prefix"]; ?></th>
        <td>: <?php echo $pat_reg["opd_id"]; ?></td>
    </tr>
    <tr>
        <th>Registration Time</th>
        <td>: <?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?>
            <?php echo date("h:i A", strtotime($pat_reg["time"])); ?></td>
        <th>Discharged Time</th>
        <td>: <?php echo $discharge_time_str; ?></td>
    </tr>
    <tr>
        <th>Patient Name</th>
        <td>: <?php echo $pat_info["name"]; ?></td>
        <th>Patient Age</th>
        <td>: <?php echo $age; ?></td>
    </tr>
    <tr>
        <th>Patient Gender</th>
        <td>: <?php echo $pat_info["sex"]; ?></td>
        <th>Room Category</th>
        <td>: <?php echo $ward["name"]; ?>, <span><?php echo $room["room_no"]; ?></span>, <span>Bed No:
                <?php echo $bed["bed_no"]; ?></span></td>
    </tr>
</table>
<?php
    $det = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM `ot_schedule` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
    ?>
<table class="table table-condensed table-bordered">
    <tr>
        <th colspan="4" style="text-align:center;background:#dddddd;">OT Details</th>
    </tr>
    <tr>
        <th>OT Type</th>
        <td>
            <?php if ($det['ot_type'] == "1") {
                    echo "Minor";
                } ?>
            <?php if ($det['ot_type'] == "2") {
                    echo "Major";
                } ?>
        </td>
        <th>Department</th>
        <td>
            <?php
                $q = mysqli_query($link, "SELECT * FROM `ot_dept_master` WHERE `ot_dept_id`='$det[ot_dept_id]' ORDER BY `ot_dept_name`");
                ($r = mysqli_fetch_array($q));
                echo $r['ot_dept_name'];
                ?>
        </td>
    </tr>
    <tr>
        <th>Procedure</th>
        <td>
            <?php
                $dept = $det['ot_dept_id'];
                $qry = "SELECT `procedure_id`,`name` FROM `ot_clinical_procedure` WHERE `ot_dept_id`='$dept' AND `procedure_id`='$det[procedure_id]'";
                $qry .= "ORDER BY `name`";
                $qr = mysqli_query($link, $qry);
                ($rr = mysqli_fetch_array($qr));

                echo $rr['name']; ?>
        </td>
        <th>Refer Doctor</th>
        <td>
            <?php
                $qry = mysqli_query($link, "SELECT `consultantdoctorid`,`Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$det[requesting_doc]' ORDER BY `Name`");
                ($rrr = mysqli_fetch_array($qry));
                ?>
            <?php echo $rrr['Name']; ?>
        </td>
    </tr>
    <tr>
        <th>OT Date</th>
        <td>
            <?php echo $det['ot_date']; ?>
        </td>
        <th>Anesthesia type</th>
        <td>
            <?php
                $qq = mysqli_query($link, "SELECT * FROM `ot_anesthesia_types` WHERE `anesthesia_id`='$det[anesthesia_id]' ORDER BY `name`");
                ($cc = mysqli_fetch_array($qq));
                ?>
            <?php echo $cc['name']; ?>
        </td>
    </tr>
    <?php
        if ($det['start_time'] != "00:00:00") {
            $s_tim = explode(":", $det['start_time']);
            $st_tim = $s_tim[0] . ":" . $s_tim[1];
        } else {
            $st_tim = "";
        }
        if ($det['end_time'] != "00:00:00") {
            $e_tim = explode(":", $det['end_time']);
            $en_tim = $e_tim[0] . ":" . $e_tim[1];
        } else {
            $en_tim = "";
        }
        ?>
    <tr>
        <th>Start Time</th>
        <td><?php echo $st_tim; ?></td>
        <th>End Time</th>
        <td><?php echo $en_tim; ?></td>
    </tr>
    <?php
        $j = 1;
        $resourse_qry = mysqli_query($link, "SELECT * FROM `ot_resource` WHERE `schedule_id`='$shed' AND `emp_id`!='0'");
        while ($res = mysqli_fetch_assoc($resourse_qry)) {
            $ot_type = mysqli_fetch_assoc(mysqli_query($link, "SELECT `type` FROM `ot_type_master` WHERE `type_id`='$res[resourse_id]'"));
        ?>
    <tr class="sel_type">
        <th>
            <?php echo $ot_type['type']; ?>
        </th>
        <td colspan="2">
            <?php
                    $inp_val = "";
                    $typ_qry = mysqli_query($link, "SELECT * FROM `ot_resource_link` WHERE `type_id`='$res[resourse_id]'");
                    while ($typs = mysqli_fetch_assoc($typ_qry)) {
                        $emp = mysqli_fetch_assoc(mysqli_query($link, "SELECT `name` FROM `employee` WHERE `emp_id`='$typs[emp_id]'"));
                        $ot_emp = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM `ot_resource` WHERE `schedule_id`='$shed' AND `resourse_id`='$res[resourse_id]' AND `emp_id`='$typs[emp_id]'"));
                        if ($ot_emp) {
                            $sel = "selected='selected'";
                            $inp_val = $typs['emp_id'];
                        } else {
                            $sel = "";
                        }
                    ?>
            <?php echo $emp['name']; ?>
            <?php
                    }
                    ?>
        </td>
        <td>
            <input type="hidden" id="rs<?php echo $j; ?>" disabled value="<?php echo $res['resourse_id']; ?>" />
            <input type="hidden" id="inp<?php echo $j; ?>" disabled value="<?php echo $inp_val; ?>" />
        </td>
    </tr>
    <?php
            $j++;
        }
        ?>
    <tr>
        <th>Diagnosis</th>
        <th colspan="3"><?php echo $det['diagnosis']; ?></th>
    </tr>
    <tr>
        <th>Remarks</th>
        <th colspan="3"><?php echo $det['remarks']; ?></th>
    </tr>
</table>
<?php
    $qry = mysqli_query($link, "SELECT * FROM `ot_post_surgery` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'");
    $n = mysqli_num_rows($qry);
    if ($n > 0) {
        $f = mysqli_fetch_array($qry);
        $req_no = $f['req_no'];
        $surgery = $f['surgery'];
        $notes = $f['notes'];
        $template = $f['template'];

        $air = $f['airway'];
        $hyp = $f['hypopharyngeal'];
        $sat = $f['saturation'];
        $pul = $f['pulmonary'];
        $vit = $f['vital'];
        $consc = $f['consciousness'];
        $ori = $f['orientation'];
        $mot = $f['motor'];
        $card = $f['cardiovascular'];
        $sur = $f['surgical'];
        $hemo = $f['hemorrhage'];
        $pain = $f['pain'];
        $urine = $f['urine'];
        $others = $f['others'];
    } else {
        $req_no = "";
        $surgery = "";
        $notes = "";
        $template = "";

        $air = "";
        $hyp = "";
        $sat = "";
        $pul = "";
        $vit = "";
        $consc = "";
        $ori = "";
        $mot = "";
        $card = "";
        $sur = "";
        $hemo = "";
        $pain = "";
        $urine = "";
        $others = "";
    }
    ?>
<table class="table table-condensed table-bordered">
    <tr>
        <th colspan="2" style="text-align:center;background:#dddddd;">Anaesthesia Record</th>
    </tr>
    <tr>
        <th>Physical Status(ASA)</th>
        <th>
            <?php if ($asa == "1") {
                    echo "Normal Healthy Patient(ASA-I)";
                } ?>
            <?php if ($asa == "2") {
                    echo "Mild Systemic Disease(ASA-II)";
                } ?>
            <?php if ($asa == "3") {
                    echo "Serve Systemic Disease(ASA-III)";
                } ?>
            <?php if ($asa == "4") {
                    echo "Serve Systemic Disease that is treat to life(ASA-IV)";
                } ?>
            <?php if ($asa == "5") {
                    echo "Morbit Patient not expected to survive the operation(ASA-V)";
                } ?>
            <?php if ($asa == "6") {
                    echo "Declared being dead(ASA-VI)";
                } ?>
            <?php if ($asa_stat == "emergency") {
                    echo "Emergency";
                } ?>
            <?php if ($asa_stat == "elective") {
                    echo "Elective";
                } ?>
        </th>
    </tr>
    <tr>
        <th>Patient Identified</th>
        <th>
            <?php if ($ident == "yes") {
                    echo "Yes";
                } ?>
            <?php if ($ident == "no") {
                    echo "No";
                } ?>
        </th>
    </tr>
    <tr>
        <th>Consent Taken</th>
        <th>
            <?php if ($consent == "yes") {
                    echo "Yes";
                } ?>
            <?php if ($consent == "no") {
                    echo "No";
                } ?>
        </th>
    </tr>
    <tr>
        <th>Last Pre-Operative Oral Intake</th>
        <th><?php echo $oral; ?></th>
    </tr>
    <tr>
        <th>Pre-Operative Vitals</th>
        <th>
            PR/HR : <?php echo $pr; ?>
            BP : <?php echo $bp; ?>
            Heart and Lungs : <?php echo $heart; ?>
        </th>
    </tr>
    <tr>
        <th>Type of Anaesthesia</th>
        <th>
            <?php if (strpos($anaes_type, '1') !== false) {
                    echo "General";
                } ?>
            <?php if (strpos($anaes_type, '2') !== false) {
                    echo "Regional";
                } ?>
            <?php if (strpos($anaes_type, '3') !== false) {
                    echo "MAC";
                } ?>
        </th>
    </tr>
    <tr>
        <th colspan="2" style="text-align:center;background:#dddddd;">Monitors Connected</th>
    </tr>
    <tr>
        <th>ECG</th>
        <th>
            <?php if (strpos($ecg, '3lead') !== false) {
                    echo "3 LEAD";
                } ?>
            <?php if (strpos($ecg, '5lead') !== false) {
                    echo "5 LEAD";
                } ?>
        </th>
    </tr>
    <tr>
        <th>SPO2</th>
        <th>
            <?php if ($spo == "yes") {
                    echo "Yes";
                } ?>
            <?php if ($spo == "no") {
                    echo "No";
                } ?>
        </th>
    </tr>
    <tr>
        <th>NIBP or Manual BP</th>
        <th>
            <?php if ($nibp == "yes") {
                    echo "Yes";
                } ?>
            <?php if ($nibp == "no") {
                    echo "No";
                } ?>
        </th>
    </tr>
    <tr>
        <th>Temperature</th>
        <th>
            <?php if ($temp == "central") {
                    echo "Central";
                } ?>
            <?php if ($temp == "perip") {
                    echo "Peripheral";
                } ?>
        </th>
    </tr>
    <tr>
        <th colspan="2" style="text-align:center;background:#dddddd;">Surgery Notes</th>
    </tr>
    <tr>
        <th>Name of the procedure performed</th>
        <th><?php echo $proc; ?></th>
    </tr>
    <tr>
        <th>Position of patient</th>
        <th><?php echo $pos; ?></th>
    </tr>
    <tr>
        <th>Incision</th>
        <th><?php echo $incision; ?></th>
    </tr>
</table>
<table class="table table-condensed table-bordered">
    <tr>
        <th colspan="6" style="text-align:center;background:#dddddd;">Post Surgery Record</th>
    </tr>
    <tr>
        <th>Request No</th>
        <th><?php echo $req_no; ?></th>
        <th>Surgery</th>
        <th><?php echo $surgery; ?></th>
        <th>Notes</th>
        <th><?php echo $notes; ?></th>
    </tr>
    <tr>
        <th>Template</th>
        <th><?php echo $template; ?></th>
        <th>Doctor/Nurse</th>
        <th><?php echo $doc['Name']; ?></th>
        <th>Ward No/Bed No</th>
        <th><?php echo $w['name'] . " / " . $b['bed_no']; ?></th>
    </tr>
</table>
<table class="table table-condensed table-bordered">
    <tr>
        <th colspan="2" style="background:#dddddd;">Patient Assessment</th>
    </tr>
    <tr>
        <th>Patient Airway</th>
        <th>
            <?php if ($air == "yes") {
                    echo "Yes";
                } ?>
            <?php if ($air == "no") {
                    echo "No";
                } ?>
        </th>
    </tr>
    <tr>
        <th>Hypopharyngeal Obstruction</th>
        <th>
            <?php if ($hyp == "absent") {
                    echo "Absent";
                } ?>
            <?php if ($hyp == "present") {
                    echo "Present";
                } ?>
        </th>
    </tr>
    <tr>
        <th>O2 Saturation Scores</th>
        <th>
            <?php if ($sat == "adeq") {
                    echo "Adequate";
                } ?>
            <?php if ($sat == "inadeq") {
                    echo "Inadequate";
                } ?>
            <?php if ($sat == "na") {
                    echo "N/A";
                } ?>
        </th>
    </tr>
    <tr>
        <th>Pulmonary Functions</th>
        <th>
            <?php if ($pul == "uncomprised") {
                    echo "Uncompromised";
                } ?>
            <?php if ($pul == "noisy") {
                    echo "Irregular Respirations";
                } ?>
            <?php if ($pul == "cyanotic") {
                    echo "Cyanotic";
                } ?>
            <?php if ($pul == "compromised") {
                    echo "Compromised";
                } ?>
            <?php if ($pul == "breath") {
                    echo "Non Breathing";
                } ?>
        </th>
    </tr>
    <tr>
        <th>Vital Signs</th>
        <th>
            <?php if ($vit == "stable") {
                    echo "Stable";
                } ?>
            <?php if ($vit == "unstable") {
                    echo "Unstable";
                } ?>
            <?php if ($vit == "not") {
                    echo "Not Recordable";
                } ?>
        </th>
    </tr>
    <tr>
        <th>Consciousness Level</th>
        <th>
            <?php if ($consc == "consc") {
                    echo "Conscious";
                } ?>
            <?php if ($consc == "semiconsc") {
                    echo "Semiconscious";
                } ?>
            <?php if ($consc == "unconsc") {
                    echo "Unconscious";
                } ?>
            <?php if ($consc == "unknown") {
                    echo "Unknown";
                } ?>
        </th>
    </tr>
    <tr>
        <th>Orientation</th>
        <th>
            <?php if ($ori == "orien") {
                    echo "Oriented";
                } ?>
            <?php if ($ori == "disorien") {
                    echo "Disoriented";
                } ?>
            <?php if ($ori == "not") {
                    echo "Not Responding";
                } ?>
        </th>
    </tr>
    <tr>
        <th>Motor and Sensory Function</th>
        <th>
            <?php if ($mot == "resumed") {
                    echo "Resumed";
                } ?>
            <?php if ($mot == "not") {
                    echo "Not yet to be resumed";
                } ?>
            <?php if ($mot == "unknown") {
                    echo "Unknown";
                } ?>
        </th>
    </tr>
    <tr>
        <th>Cardiovascular Function</th>
        <th>
            <?php if ($card == "normal") {
                    echo "Normal";
                } ?>
            <?php if ($card == "abnormal") {
                    echo "Abnormal";
                } ?>
            <?php if ($card == "absent") {
                    echo "Absent";
                } ?>
        </th>
    </tr>
    <tr>
        <th>Condition of the surgical site</th>
        <th>
            <?php if ($sur == "normal") {
                    echo "Normal";
                } ?>
            <?php if ($sur == "abnormal") {
                    echo "Abnormal";
                } ?>
            <?php if ($sur == "urgent") {
                    echo "Needs Urgent Attention";
                } ?>
        </th>
    </tr>
    <tr>
        <th>Hemorrhage</th>
        <th>
            <?php if ($hemo == "none") {
                    echo "None";
                } ?>
            <?php if ($hemo == "oozing") {
                    echo "Oozing";
                } ?>
            <?php if ($hemo == "bleed") {
                    echo "Profuse Bleeding";
                } ?>
        </th>
    </tr>
    <tr>
        <th>Pain</th>
        <th>
            <?php if ($pain == "none") {
                    echo "None";
                } ?>
            <?php if ($pain == "mild") {
                    echo "Mild";
                } ?>
            <?php if ($pain == "moderate") {
                    echo "Moderate";
                } ?>
            <?php if ($pain == "severe") {
                    echo "Severe";
                } ?>
        </th>
    </tr>
    <tr>
        <th>Urine Output at least 30/hr</th>
        <th>
            <?php if ($urine == "adeq") {
                    echo "Adequate";
                } ?>
            <?php if ($urine == "inadeq") {
                    echo "Inadequate";
                } ?>
        </th>
    </tr>
    <tr>
        <th>Others</th>
        <th>
            <?php echo $others; ?>
        </th>
    </tr>
</table>
<?php
}
if ($_POST["type"] == "Patient Medical Case Sheet Record") {
    $uhid = $patient_id = $_POST["uhid"];
    $ipd = $ipd_id = $_POST["ipd_id"];
    $user = $_POST["user"];

    $pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));

    $pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_id' "));

    $reg_date = $pat_reg["date"];

    if ($pat_info["dob"] != "") {
        $age = age_calculator_date($pat_info["dob"], $reg_date);
    } else {
        $age = $pat_info["age"] . " " . $pat_info["age_type"];
    }

    $prefix_det = mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

    $pat_admit = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_id' ORDER BY `slno` DESC limit 0,1 "));

    $ward = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_admit[ward_id]' "));

    $bed = mysqli_fetch_array(mysqli_query($link, " SELECT `room_id`,`bed_no` FROM `bed_master` WHERE `bed_id`='$pat_admit[bed_id]' "));

    $room = mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' "));

    $discharge_time_str = "NA";
    $discharge_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
    if ($discharge_info) {
        $discharge_time_str = date("d-M-Y", strtotime($discharge_info["date"])) . " " . date("h:i A", strtotime($discharge_info["time"]));
    }
?>
<table class="table table-condensed">
    <tr>
        <th style="width: 100px;">UHID</th>
        <td style="width: auto;">: <?php echo $pat_info["patient_id"]; ?></td>
        <th style="width: 95px;"><?php echo $prefix_det["prefix"]; ?></th>
        <td>: <?php echo $pat_reg["opd_id"]; ?></td>
    </tr>
    <tr>
        <th>Registration Time</th>
        <td>: <?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?>
            <?php echo date("h:i A", strtotime($pat_reg["time"])); ?></td>
        <th>Discharged Time</th>
        <td>: <?php echo $discharge_time_str; ?></td>
    </tr>
    <tr>
        <th>Patient Name</th>
        <td>: <?php echo $pat_info["name"]; ?></td>
        <th>Patient Age</th>
        <td>: <?php echo $age; ?></td>
    </tr>
    <tr>
        <th>Patient Gender</th>
        <td>: <?php echo $pat_info["sex"]; ?></td>
        <th>Room Category</th>
        <td>: <?php echo $ward["name"]; ?>, <span><?php echo $room["room_no"]; ?></span>, <span>Bed No:
                <?php echo $bed["bed_no"]; ?></span></td>
    </tr>
</table>
<?php
    $f1 = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_case_sheet_AB` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
    $f2 = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_case_sheet_CF` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
    $f3 = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_case_sheet_GL` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
    $f4 = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_case_sheet_LL` WHERE `patient_id`='$uhid' AND `ipd_id`='$ipd'"));
    ?>
<table class="table table-condensed">
    <tr>
        <th colspan="2" style="text-align:center;background:#dddddd;">Clinical notes &amp; Case summary</th>
    </tr>
    <tr>
        <th colspan="2" style="text-align:center;">History</th>
    </tr>
    <tr>
        <td colspan="2">
            <b>A. Complaints with duration / Illness or injury</b><br />
            <?php $ill = str_replace("\n", "<br/>", $f1['illness']);
                echo $ill; ?>
        </td>
    </tr>
    <tr>
        <th colspan="2">B. Accident
            <?php if ($f1['accident'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
        </th>
    </tr>
    <?php
        if ($f1['accident'] > 0) {
            $tr_accid = "";
        } else {
            $tr_accid = "display:none;";
        }
        ?>
    <tr class="tr_accid" style="<?php echo $tr_accid; ?>">
        <td>
            <b>Date of injury : </b>
            <?php if ($f1['inj_dt'] != '0000-00-00') {
                    echo convert_date_g($f1['inj_dt']);
                } ?>
        </td>
        <td>
            <b>Time of injury : </b>
            <?php if ($f1['inj_tm'] != '00:00:00') {
                    echo convert_time($f1['inj_tm']);
                } ?>
        </td>
    </tr>
    <tr class="tr_accid" style="<?php echo $tr_accid; ?>">
        <th colspan="2">
            Type of injury :&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php if ($f1['blunt'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Blunt
            <?php if ($f1['penet'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Penetrating
            <?php if ($f1['burns'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Burns
            <?php if ($f1['inhal'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Inhalation Injury
            <?php if ($f1['inj_oth'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Others
        </th>
    </tr>
    <?php
        if ($f1['accident'] > 0) {
            if ($f1['blunt'] > 0 || $f1['penet'] > 0 || $f1['burns'] > 0 || $f1['inhal'] > 0 || $f1['inj_oth'] > 0) {
                $tr_injury = "";
            } else {
                $tr_injury = "display:none;";
            }
        } else {
            $tr_injury = "display:none;";
        }
        ?>
    <tr id="tr_injury" style="<?php echo $tr_injury; ?>">
        <td colspan="2">
            <?php $injury_hist = str_replace("\n", "<br/>", $f1['injury_hist']);
                echo $injury_hist; ?>
        </td>
    </tr>
    <tr class="tr_accid" style="<?php echo $tr_accid; ?>">
        <td colspan="2">
            <b>Place of Occurence :</b><br />
            <?php $occ = str_replace("\n", "<br/>", $f1['occur']);
                echo $occ; ?>
        </td>
    </tr>
    <tr class="tr_accid" style="<?php echo $tr_accid; ?>">
        <td colspan="2">
            <b>Mechanism of Injury :</b><br />
            <?php $inj = str_replace("\n", "<br/>", $f1['mechan']);
                echo $inj; ?>
        </td>
    </tr>
    <tr class="tr_accid" style="<?php echo $tr_accid; ?>">
        <td colspan="2">
            <b>Pre-Hospital Care :</b><br />
            <?php $car = str_replace("\n", "<br/>", $f1['care']);
                echo $car; ?>
        </td>
    </tr>
    <tr>
        <th colspan="2">C. Past Medical History with Duration</th>
    </tr>
    <tr>
        <td colspan="2">
            <?php if ($f2['no_past'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            No past history
            <?php if ($f2['copd'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            COPD or Lung Disorder
            <?php if ($f2['cva'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            CVA / Stroke
            <?php if ($f2['hyper'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Hypertension
            <?php if ($f2['unknown'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Unknown<br />
            <?php if ($f2['heart'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Heart Condition
            <?php if ($f2['cancer'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Cancer
            <?php if ($f2['diabetes'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Diabetes
            <?php if ($f2['seizure'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Seizures
            <?php if ($f2['past_oth'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Others
        </td>
    </tr>
    <?php
        if ($f2['no_past'] > 0 || $f2['copd'] > 0 || $f2['cva'] > 0 || $f2['hyper'] > 0 || $f2['unknown'] > 0 || $f2['heart'] > 0 || $f2['cancer'] > 0 || $f2['diabetes'] > 0 || $f2['seizure'] > 0 || $f2['past_oth'] > 0) {
            $tr_dur = "";
        } else {
            $tr_dur = "display:none;";
        }
        ?>
    <tr id="tr_dur" style="<?php echo $tr_dur; ?>">
        <td colspan="2">
            <?php $dur_hist = str_replace("\n", "<br/>", $f2['dur_hist']);
                echo $dur_hist; ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <b>D. Any Operation :</b><br />
            <?php $oper = str_replace("\n", "<br/>", $f2['operation']);
                echo $oper; ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <b>E. Drug History :</b><br />
            <?php if ($f2['steroid'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Steroids
            <?php if ($f2['hormone'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Hormones
            <?php if ($f2['drug'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Thyroid Drugs
            <?php if ($f2['pills'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Contraceptive Pills
            <?php if ($f2['analgesic'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Analgesics
        </td>
    </tr>
    <?php
        if ($f2['steroid'] > 0 || $f2['hormone'] > 0 || $f2['drug'] > 0 || $f2['pills'] > 0 || $f2['analgesic'] > 0) {
            $tr_drug = "";
        } else {
            $tr_drug = "display:none;";
        }
        ?>
    <tr id="tr_drug" style="<?php echo $tr_drug; ?>">
        <td colspan="2">
            <?php $drug_hist = str_replace("\n", "<br/>", $f2['drug_hist']);
                echo $drug_hist; ?>
        </td>
    </tr>
    <?php
        if ($pat['sex'] == "Male") {
            $sex_disb = "text-decoration:line-through;";
        }
        if ($pat['sex'] == "Female") {
            $sex_disb = "";
        }
        ?>
    <tr>
        <td colspan="2">
            <b>F. <span style="<?php echo $sex_disb; ?>">Menstrual
                    :</span></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <?php
                if ($pat['sex'] == "Female") {
                    if ($f2['regular'] == 1) {
                        echo "<b class='icon-check icon-large'></b> ";
                    } else {
                        echo "<b class='icon-check-empty icon-large'></b>";
                    } ?>
            Regular
            <?php if ($f2['irregular'] == 1) {
                        echo "<b class='icon-check icon-large'></b> ";
                    } else {
                        echo "<b class='icon-check-empty icon-large'></b>";
                    } ?>
            Irregular<br />
            <b>History : </b>
            <?php echo $f2['mens_hist'];
                }
                ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <b>G. Transfussion History :</b><br />
            <?php $tran = str_replace("\n", "<br/>", $f3['tran_hist']);
                echo $tran; ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <b>H. Known Allergy (if any) :</b><br />
            <?php $aller = str_replace("\n", "<br/>", $f3['allergy']);
                echo $aller; ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <b>I. Personal History :</b><br />
            <?php if ($f3['alcohol'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Alcohol
            <?php if ($f3['smoking'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Smoking
            <?php if ($f3['oth_addict'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Other Addiction
        </td>
    </tr>
    <?php
        if ($f3['alcohol'] > 0 || $f3['smoking'] > 0 || $f3['oth_addict'] > 0) {
            $tr_per = "";
        } else {
            $tr_per = "display:none;";
        }
        ?>
    <tr id="tr_per" style="<?php echo $tr_per; ?>">
        <td colspan="2">
            <?php $per_hist = str_replace("\n", "<br/>", $f2['per_hist']);
                echo $per_hist; ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <b>J. Family History :</b><br />
            <?php if ($f3['htn'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            HTN
            <?php if ($f3['dm'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            DM
            <?php if ($f3['cva'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            CVA
            <?php if ($f3['ihd'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            CA / IHD
            <?php if ($f3['f_cancer'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Cancer
            <?php if ($f3['asthma'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Br.Asthma
            <?php if ($f3['f_oth'] == 1) {
                    echo "<b class='icon-check icon-large'></b> ";
                } else {
                    echo "<b class='icon-check-empty icon-large'></b>";
                } ?>
            Others
        </td>
    </tr>
    <?php
        if ($f3['htn'] > 0 || $f3['dm'] > 0 || $f3['cva'] > 0 || $f3['ihd'] > 0 || $f3['f_cancer'] > 0 || $f3['asthma'] > 0 || $f3['f_oth'] > 0) {
            $tr_family = "";
        } else {
            $tr_family = "display:none;";
        }
        ?>
    <tr id="tr_family" style="<?php echo $tr_family; ?>">
        <td colspan="2">
            <?php $fam_hist = str_replace("\n", "<br/>", $f2['fam_hist']);
                echo $fam_hist; ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <b>K. Treatment History :</b><br />
            <?php $t_hist = str_replace("\n", "<br/>", $f3['treat_hist']);
                echo $t_hist; ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <b>L. Past Investigation :</b><br />
            <?php $p_hist = str_replace("\n", "<br/>", $f3['past_inv']);
                echo $p_hist; ?>
        </td>
    </tr>
    <tr>
        <th colspan="2" style="text-align:center;">Physical Examination</th>
    </tr>
    <tr>
        <th>General Examination</th>
        <th>Local Examination</th>
    </tr>
    <tr>
        <td>Height (cm)</td>
        <td><?php echo $f4['height']; ?></td>
    </tr>
    <tr>
        <td>Weight (kg)</td>
        <td><?php echo $f4['weight']; ?></td>
    </tr>
    <tr>
        <td>Respiratory Rate/min : </td>
        <td><?php echo $f4['rr']; ?></td>
    </tr>
    <tr>
        <td>Blood Pressure : </td>
        <td><?php echo $f4['bp']; ?></td>
    </tr>
    <tr>
        <td>Pulse/min : </td>
        <td><?php echo $f4['pulse']; ?></td>
    </tr>
    <tr>
        <td>Temperature : </td>
        <td><?php echo $f4['temp']; ?></td>
    </tr>
    <tr>
        <td>Pallor : </td>
        <td><?php echo $f4['pallor']; ?></td>
    </tr>
    <tr>
        <td>Cyanosis : </td>
        <td><?php echo $f4['cyanosis']; ?></td>
    </tr>
    <tr>
        <td>Clubbing : </td>
        <td><?php echo $f4['club']; ?></td>
    </tr>
    <tr>
        <th colspan="2">Others</th>
    </tr>
    <tr>
        <td>O2 Saturation : </td>
        <td><?php echo $f4['saturation']; ?></td>
    </tr>
    <tr>
        <td>APVU : </td>
        <td><?php echo $f4['apvu']; ?></td>
    </tr>
    <tr>
        <td>GCS : </td>
        <td><?php echo $f4['gcs']; ?></td>
    </tr>
    <tr>
        <th colspan="2">Systemic Examination :</th>
    </tr>
    <tr>
        <td colspan="2">
            <b>CENTRAL NERVOUS SYSTEM :</b><br />
            <?php $nerv = str_replace("\n", "<br/>", $f4['nervous']);
                echo $nerv; ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <b>RESPIRATORY SYSTEM :</b><br />
            <?php $resp = str_replace("\n", "<br/>", $f4['respiratory']);
                echo $resp; ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <b>CARDIO VASCULAR SYSTEM :</b><br />
            <?php $vasc = str_replace("\n", "<br/>", $f4['vascular']);
                echo $vasc; ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <b>ABDOMEN AND GENITALIA :</b><br />
            <?php $adob = str_replace("\n", "<br/>", $f4['abdomen']);
                echo $adob; ?>
        </td>
    </tr>
    <tr>
        <th colspan="2">Provisional Diagnosis :</th>
    </tr>
    <tr>
        <td rowspan="2">Case history recorded by :<br />
            <?php
                $doct = mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$f4[rec_doc]'"));
                echo $doct['Name'];
                ?>
        </td>
        <td>
            History Informant : Patient/Attendant<br />
            <?php $infom = str_replace("\n", "<br/>", $f4['informant']);
                echo $infom; ?>
        </td>
    </tr>
    <tr>
        <td>
            Name of the patient or attendand<br />
            <?php echo $f4['patient']; ?>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            Relation with patient (in case of attendant)<br />
            <?php echo $f4['rel']; ?>
        </td>
    </tr>
</table>
<?php
}
if ($_POST["type"] == "Patient Investigaiton Record") {
    $uhid = $patient_id = $_POST["uhid"];
    $ipd = $ipd_id = $_POST["ipd_id"];
    $pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));

    $pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_id' "));

    $reg_date = $pat_reg["date"];

    if ($pat_info["dob"] != "") {
        $age = age_calculator_date($pat_info["dob"], $reg_date);
    } else {
        $age = $pat_info["age"] . " " . $pat_info["age_type"];
    }

    $prefix_det = mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

    $pat_admit = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_id' ORDER BY `slno` DESC limit 0,1 "));

    $ward = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_admit[ward_id]' "));

    $bed = mysqli_fetch_array(mysqli_query($link, " SELECT `room_id`,`bed_no` FROM `bed_master` WHERE `bed_id`='$pat_admit[bed_id]' "));

    $room = mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' "));

    $discharge_time_str = "NA";
    $discharge_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
    if ($discharge_info) {
        $discharge_time_str = date("d-M-Y", strtotime($discharge_info["date"])) . " " . date("h:i A", strtotime($discharge_info["time"]));
    }
?>
<table class="table table-condensed">
    <tr>
        <th style="width: 100px;">UHID</th>
        <td style="width: auto;">: <?php echo $pat_info["patient_id"]; ?></td>
        <th style="width: 95px;"><?php echo $prefix_det["prefix"]; ?></th>
        <td>: <?php echo $pat_reg["opd_id"]; ?></td>
    </tr>
    <tr>
        <th>Registration Time</th>
        <td>: <?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?>
            <?php echo date("h:i A", strtotime($pat_reg["time"])); ?></td>
        <th>Discharged Time</th>
        <td>: <?php echo $discharge_time_str; ?></td>
    </tr>
    <tr>
        <th>Patient Name</th>
        <td>: <?php echo $pat_info["name"]; ?></td>
        <th>Patient Age</th>
        <td>: <?php echo $age; ?></td>
    </tr>
    <tr>
        <th>Patient Gender</th>
        <td>: <?php echo $pat_info["sex"]; ?></td>
        <th>Room Category</th>
        <td>: <?php echo $ward["name"]; ?>, <span><?php echo $room["room_no"]; ?></span>, <span>Bed No:
                <?php echo $bed["bed_no"]; ?></span></td>
    </tr>
</table>
<hr />
<div class="row" style="margin-left:0px;">

    <?php
        $max_j = 40;
        $max_j_page = 80;
        $j = 1;
        $i = 0;
        $dates = mysqli_query($link, "select distinct date from patient_test_details where patient_id='$uhid' and ipd_id='$ipd' and type>3 and testid in(select testid from testmaster where category_id='1') order by date");
        while ($dt = mysqli_fetch_array($dates)) {
            if ($j == 1) {
        ?>
    <div class="span5" style="margin-left:0px;">
        <table class="table table-bordered table-condensed">
            <?php
                }
                    ?>
            <tr>
                <th colspan="2" style="background-color:#CCC;text-align:center">
                    <?php echo convert_date($dt[date]); ?>
                </th>
            </tr>
            <?php
                    $j++;
                    $test = mysqli_query($link, "select * from patient_test_details where patient_id='$uhid' and ipd_id='$ipd' and type>3 and date='$dt[date]' and testid in(select testid from testmaster where category_id='1')");
                    while ($tst = mysqli_fetch_array($test)) {
                        $tname = mysqli_fetch_array(mysqli_query($link, "select testname from testmaster where testid='$tst[testid]'"));
                        $chk_p = mysqli_num_rows(mysqli_query($link, "select * from Testparameter where TestId='$tst[testid]' and sequence>0"));
                        if ($chk_p > 1) {
                    ?>
            <tr>
                <td colspan="2"><b><?php echo $tname[testname]; ?></b></td>
            </tr>
            <?php
                            $j++;
                            $param = mysqli_query($link, "select * from testresults where testid='$tst[testid]' and patient_id='$uhid' and ipd_id='$ipd' and batch_no='$tst[batch_no]' order by sequence");
                            while ($par = mysqli_fetch_array($param)) {
                                $unit = mysqli_fetch_array(mysqli_query($link, "select a.unit_name,b.Name from Units a,Parameter_old b where b.ID='$par[paramid]' and b.UnitsID=a.ID"));
                                $pname = mysqli_fetch_array(mysqli_query($link, "select * from Parameter_old where ID='$par[paramid]'"));
                            ?>
            <tr>
                <td><span style="margin-left:20px"><i><?php echo $pname[Name]; ?></i></span></td>
                <td><?php echo $par[result] . " " . $unit[unit_name]; ?></td>
            </tr>
            <?php
                                $j++;
                                if ($j > $max_j && $i == 0) {
                                ?>
        </table>
    </div>
    <div class="span5" style="margin-left:2px;">
        <table class="table table-bordered table-condensed">
            <?php
                                    $i = 1;
                                }
                                if ($j == $max_j_page) {
                    ?>
        </table>
    </div>
</div>

<div class="page-break"></div>
<?php include('page_header.php'); ?>
<hr>
<center>
    <h4><?php echo $_POST["type"]; ?></h4>
</center>
<table class="table table-condensed">
    <tr>
        <th style="width: 100px;">UHID</th>
        <td style="width: auto;">: <?php echo $pat_info["patient_id"]; ?></td>
        <th style="width: 95px;"><?php echo $prefix_det["prefix"]; ?></th>
        <td>: <?php echo $pat_reg["opd_id"]; ?></td>
    </tr>
    <tr>
        <th>Registration Time</th>
        <td>: <?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?>
            <?php echo date("h:i A", strtotime($pat_reg["time"])); ?></td>
        <th>Discharged Time</th>
        <td>: <?php echo $discharge_time_str; ?></td>
    </tr>
    <tr>
        <th>Patient Name</th>
        <td>: <?php echo $pat_info["name"]; ?></td>
        <th>Patient Age</th>
        <td>: <?php echo $age; ?></td>
    </tr>
    <tr>
        <th>Patient Gender</th>
        <td>: <?php echo $pat_info["sex"]; ?></td>
        <th>Room Category</th>
        <td>: <?php echo $ward["name"]; ?>, <span><?php echo $room["room_no"]; ?></span>, <span>Bed No:
                <?php echo $bed["bed_no"]; ?></span></td>
    </tr>
</table>
<hr />

<div class="row">
    <div class="span5" style="margin-left:0px;">
        <table class="table table-bordered table-condensed">
            <?php
                                    $j = 2;
                                    $i = 0;
                                }
                            }
                        } else {
                            $unit = mysqli_fetch_array(mysqli_query($link, "select a.unit_name from Units a,Testparameter b,Parameter_old c where b.TestId='$tst[testid]' and b.ParamaterId=c.ID and c.UnitsID=a.ID"));
                            $parm = mysqli_fetch_array(mysqli_query($link, "select * from Testparameter where TestId='$tst[testid]'"));
                            $res = mysqli_fetch_array(mysqli_query($link, "select * from testresults where testid='$tst[testid]' and patient_id='$uhid' and ipd_id='$ipd' and batch_no='$tst[batch_no]' and paramid='$parm[ParamaterId]'"));
        ?>
            <tr>
                <td><b><?php echo $tname[testname]; ?></b></td>
                <td><?php echo $res[result] . " " . $unit[unit_name]; ?></td>
            </tr>
            <?php
                            $j++;

                            if ($j > $max_j && $i == 0) {
        ?>
        </table>
    </div>
    <div class="span5" style="margin-left:10px !important;">
        <table class="table table-bordered table-condensed">
            <?php
                                $i = 1;
                            }
                            if ($j >= $max_j_page) {
            ?>
        </table>
    </div>
</div>
<div class="page-break"></div>
<?php include('page_header.php'); ?>
<hr>
<center>
    <h4><?php echo $_POST["type"]; ?></h4>
</center>
<table class="table table-condensed">
    <tr>
        <th style="width: 100px;">UHID</th>
        <td style="width: auto;">: <?php echo $pat_info["patient_id"]; ?></td>
        <th style="width: 95px;"><?php echo $prefix_det["prefix"]; ?></th>
        <td>: <?php echo $pat_reg["opd_id"]; ?></td>
    </tr>
    <tr>
        <th>Registration Time</th>
        <td>: <?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?>
            <?php echo date("h:i A", strtotime($pat_reg["time"])); ?></td>
        <th>Discharged Time</th>
        <td>: <?php echo $discharge_time_str; ?></td>
    </tr>
    <tr>
        <th>Patient Name</th>
        <td>: <?php echo $pat_info["name"]; ?></td>
        <th>Patient Age</th>
        <td>: <?php echo $age; ?></td>
    </tr>
    <tr>
        <th>Patient Gender</th>
        <td>: <?php echo $pat_info["sex"]; ?></td>
        <th>Room Category</th>
        <td>: <?php echo $ward["name"]; ?>, <span><?php echo $room["room_no"]; ?></span>, <span>Bed No:
                <?php echo $bed["bed_no"]; ?></span></td>
    </tr>
</table>
<hr />
<div class="row">
    <div class="span5" style="margin-left:0px;">
        <table class="table table-bordered table-condensed">
            <?php
                                $j = 2;
                                $i = 0;
                            }
                        }
                    }
    ?>

            <?php
    ?>
    </div> <?php
            }
                ?>
    </table>
</div>
</div>
<?php
}

if ($_POST["type"] == "BED CHARGE") {
    $patient_id = $_POST["uhid"];
    $ipd_id = $_POST["ipd_id"];
    $user = $_POST["user"];
    $schedule_id_qry = mysqli_fetch_array(mysqli_query($link, "SELECT `schedule_id` FROM `patient_ot_schedule` WHERE `patient_id` = '$patient_id' AND `ipd_id` = '$ipd_id'"));
    $schedule_id = $schedule_id_qry['schedule_id'];

    $pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));

    $pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_id' "));

    $reg_date = $pat_reg["date"];

    if ($pat_info["dob"] != "") {
        $age = age_calculator_date($pat_info["dob"], $reg_date);
    } else {
        $age = $pat_info["age"] . " " . $pat_info["age_type"];
    }

    $prefix_det = mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

    $pat_admit = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_id' ORDER BY `slno` DESC limit 0,1 "));

    $ward = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_admit[ward_id]' "));

    $bed = mysqli_fetch_array(mysqli_query($link, " SELECT `room_id`,`bed_no` FROM `bed_master` WHERE `bed_id`='$pat_admit[bed_id]' "));

    $room = mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' "));
    $request_doc_info = mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$patient_ot_schedule[request_doc_id]' "));
    $ot_area = mysqli_fetch_array(mysqli_query($link, "SELECT `ot_area_name` FROM `ot_area_master` WHERE `ot_area_id`='$patient_ot_schedule[ot_area_id]'"));
    $ot_dept = mysqli_fetch_array(mysqli_query($link, "SELECT `ot_dept_name` FROM `ot_dept_master` WHERE `ot_dept_id`='$patient_ot_schedule[ot_dept_id]'"));

    $discharge_time_str = "NA";
    $discharge_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
    if ($discharge_info) {
        $discharge_time_str = date("d-M-Y", strtotime($discharge_info["date"])) . " " . date("h:i A", strtotime($discharge_info["time"]));
    }
?>
<table class="table table-condensed">
    <tr>
        <th style="width: 100px;">UHID</th>
        <td style="width: auto;">: <?php echo $pat_info["patient_id"]; ?></td>
        <th style="width: 95px;"><?php echo $prefix_det["prefix"]; ?></th>
        <td>: <?php echo $pat_reg["opd_id"]; ?></td>
    </tr>
    <tr>
        <th>Registration Time</th>
        <td>: <?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?>
            <?php echo date("h:i A", strtotime($pat_reg["time"])); ?></td>
        <th>Discharged Time</th>
        <td>: <?php echo $discharge_time_str; ?></td>
    </tr>
    <tr>
        <th>Patient Name</th>
        <td>: <?php echo $pat_info["name"]; ?></td>
        <th>Patient Age</th>
        <td>: <?php echo $age; ?></td>
    </tr>
    <tr>
        <th>Patient Gender</th>
        <td>: <?php echo $pat_info["sex"]; ?></td>
        <th>Room Category</th>
        <td>: <?php echo $ward["name"]; ?>, <span><?php echo $room["room_no"]; ?></span>, <span>Bed No:
                <?php echo $bed["bed_no"]; ?></span></td>
    </tr>
</table>
<table class="table table-condensed table-bordered table-hover text-center">
    <thead class="table_header_fix">
        <tr>
            <th>#</th>
            <th>Service Name</th>
            <th>Quantity</th>
            <th>Rate</th>
            <th>Amount</th>
            <th>Service Date</th>
        </tr>
    </thead>
    <?php
        $bed_qry = mysqli_query($link, "SELECT * FROM `ipd_pat_service_details` WHERE `patient_id` = '$patient_id' AND `ipd_id` = '$ipd_id' AND `group_id` = '141'");
        $n = 1;
        while ($bed = mysqli_fetch_array($bed_qry)) {

        ?>
    <tr>
        <td><?php echo $n; ?></td>
        <td><?php echo $bed['service_text']; ?></td>
        <td><?php echo $bed['ser_quantity']; ?></td>
        <td><?php echo $bed['rate']; ?></td>
        <td><?php echo $bed['amount']; ?></td>

        <td><?php echo $bed['date']; ?></td>
    </tr>
    <?php
            $n++;
        } ?>

</table>
<?php
}

if ($_POST["type"] == "BED CHARGE PLUS") {
    $patient_id = $_POST["uhid"];
    $ipd_id = $_POST["ipd_id"];
    $user = $_POST["user"];
    $schedule_id_qry = mysqli_fetch_array(mysqli_query($link, "SELECT `schedule_id` FROM `patient_ot_schedule` WHERE `patient_id` = '$patient_id' AND `ipd_id` = '$ipd_id'"));
    $schedule_id = $schedule_id_qry['schedule_id'];

    $pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));

    $pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_id' "));

    $reg_date = $pat_reg["date"];

    if ($pat_info["dob"] != "") {
        $age = age_calculator_date($pat_info["dob"], $reg_date);
    } else {
        $age = $pat_info["age"] . " " . $pat_info["age_type"];
    }

    $prefix_det = mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

    $pat_admit = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_id' ORDER BY `slno` DESC limit 0,1 "));

    $ward = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_admit[ward_id]' "));

    $bed = mysqli_fetch_array(mysqli_query($link, " SELECT `room_id`,`bed_no` FROM `bed_master` WHERE `bed_id`='$pat_admit[bed_id]' "));

    $room = mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' "));
    $request_doc_info = mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$patient_ot_schedule[request_doc_id]' "));
    $ot_area = mysqli_fetch_array(mysqli_query($link, "SELECT `ot_area_name` FROM `ot_area_master` WHERE `ot_area_id`='$patient_ot_schedule[ot_area_id]'"));
    $ot_dept = mysqli_fetch_array(mysqli_query($link, "SELECT `ot_dept_name` FROM `ot_dept_master` WHERE `ot_dept_id`='$patient_ot_schedule[ot_dept_id]'"));

    $discharge_time_str = "NA";
    $discharge_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
    if ($discharge_info) {
        $discharge_time_str = date("d-M-Y", strtotime($discharge_info["date"])) . " " . date("h:i A", strtotime($discharge_info["time"]));
    }
?>
<table class="table table-condensed">
    <tr>
        <th style="width: 100px;">UHID</th>
        <td style="width: auto;">: <?php echo $pat_info["patient_id"]; ?></td>
        <th style="width: 95px;"><?php echo $prefix_det["prefix"]; ?></th>
        <td>: <?php echo $pat_reg["opd_id"]; ?></td>
    </tr>
    <tr>
        <th>Registration Time</th>
        <td>: <?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?>
            <?php echo date("h:i A", strtotime($pat_reg["time"])); ?></td>
        <th>Discharged Time</th>
        <td>: <?php echo $discharge_time_str; ?></td>
    </tr>
    <tr>
        <th>Patient Name</th>
        <td>: <?php echo $pat_info["name"]; ?></td>
        <th>Patient Age</th>
        <td>: <?php echo $age; ?></td>
    </tr>
    <tr>
        <th>Patient Gender</th>
        <td>: <?php echo $pat_info["sex"]; ?></td>
        <th>Room Category</th>
        <td>: <?php echo $ward["name"]; ?>, <span><?php echo $room["room_no"]; ?></span>, <span>Bed No:
                <?php echo $bed["bed_no"]; ?></span></td>
    </tr>
</table>
<table class="table table-condensed table-bordered table-hover text-center">
    <thead class="table_header_fix">
        <tr>
            <th>#</th>
            <th>Service Name</th>
            <th>Quantity</th>
            <th>Rate</th>
            <th>Amount</th>
            <th>Service Date</th>
        </tr>
    </thead>
    <?php
        $bed_qry = mysqli_query($link, "SELECT * FROM `ipd_pat_service_details` WHERE `patient_id` = '$patient_id' AND `ipd_id` = '$ipd_id' AND `group_id` = '148'");
        $n = 1;
        while ($bed = mysqli_fetch_array($bed_qry)) {

        ?>
    <tr>
        <td><?php echo $n; ?></td>
        <td><?php echo $bed['service_text']; ?></td>
        <td><?php echo $bed['ser_quantity']; ?></td>
        <td><?php echo $bed['rate']; ?></td>
        <td><?php echo $bed['amount']; ?></td>

        <td><?php echo $bed['date']; ?></td>
    </tr>
    <?php
            $n++;
        } ?>

</table>
<?php
}

if ($_POST["type"] == "ANAESTHESIA") {
    $patient_id = $_POST["uhid"];
    $ipd_id = $_POST["ipd_id"];
    $user = $_POST["user"];
    $schedule_id_qry = mysqli_fetch_array(mysqli_query($link, "SELECT `schedule_id` FROM `patient_ot_schedule` WHERE `patient_id` = '$patient_id' AND `ipd_id` = '$ipd_id'"));
    $schedule_id = $schedule_id_qry['schedule_id'];

    $pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));

    $pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_id' "));

    $reg_date = $pat_reg["date"];

    if ($pat_info["dob"] != "") {
        $age = age_calculator_date($pat_info["dob"], $reg_date);
    } else {
        $age = $pat_info["age"] . " " . $pat_info["age_type"];
    }

    $prefix_det = mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

    $pat_admit = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_id' ORDER BY `slno` DESC limit 0,1 "));

    $ward = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_admit[ward_id]' "));

    $bed = mysqli_fetch_array(mysqli_query($link, " SELECT `room_id`,`bed_no` FROM `bed_master` WHERE `bed_id`='$pat_admit[bed_id]' "));

    $room = mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' "));
    $request_doc_info = mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$patient_ot_schedule[request_doc_id]' "));
    $ot_area = mysqli_fetch_array(mysqli_query($link, "SELECT `ot_area_name` FROM `ot_area_master` WHERE `ot_area_id`='$patient_ot_schedule[ot_area_id]'"));
    $ot_dept = mysqli_fetch_array(mysqli_query($link, "SELECT `ot_dept_name` FROM `ot_dept_master` WHERE `ot_dept_id`='$patient_ot_schedule[ot_dept_id]'"));

    $discharge_time_str = "NA";
    $discharge_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
    if ($discharge_info) {
        $discharge_time_str = date("d-M-Y", strtotime($discharge_info["date"])) . " " . date("h:i A", strtotime($discharge_info["time"]));
    }
?>
<table class="table table-condensed">
    <tr>
        <th style="width: 100px;">UHID</th>
        <td style="width: auto;">: <?php echo $pat_info["patient_id"]; ?></td>
        <th style="width: 95px;"><?php echo $prefix_det["prefix"]; ?></th>
        <td>: <?php echo $pat_reg["opd_id"]; ?></td>
    </tr>
    <tr>
        <th>Registration Time</th>
        <td>: <?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?>
            <?php echo date("h:i A", strtotime($pat_reg["time"])); ?></td>
        <th>Discharged Time</th>
        <td>: <?php echo $discharge_time_str; ?></td>
    </tr>
    <tr>
        <th>Patient Name</th>
        <td>: <?php echo $pat_info["name"]; ?></td>
        <th>Patient Age</th>
        <td>: <?php echo $age; ?></td>
    </tr>
    <tr>
        <th>Patient Gender</th>
        <td>: <?php echo $pat_info["sex"]; ?></td>
        <th>Room Category</th>
        <td>: <?php echo $ward["name"]; ?>, <span><?php echo $room["room_no"]; ?></span>, <span>Bed No:
                <?php echo $bed["bed_no"]; ?></span></td>
    </tr>
</table>
<table class="table table-condensed table-bordered table-hover text-center">
    <thead class="table_header_fix">
        <tr>
            <th>#</th>
            <th>Service Name</th>
            <th>Quantity</th>
            <th>Rate</th>
            <th>Amount</th>
            <th>Service Date</th>
        </tr>
    </thead>
    <?php
        $bed_qry = mysqli_query($link, "SELECT * FROM `ipd_pat_service_details` WHERE `patient_id` = '$patient_id' AND `ipd_id` = '$ipd_id' AND `group_id` = '168'");
        $n = 1;
        while ($bed = mysqli_fetch_array($bed_qry)) {

        ?>
    <tr>
        <td><?php echo $n; ?></td>
        <td><?php echo $bed['service_text']; ?></td>
        <td><?php echo $bed['ser_quantity']; ?></td>
        <td><?php echo $bed['rate']; ?></td>
        <td><?php echo $bed['amount']; ?></td>

        <td><?php echo $bed['date']; ?></td>
    </tr>
    <?php
            $n++;
        } ?>

</table>
<?php
}

if ($_POST["type"] == "DAY CARE") {
    $patient_id = $_POST["uhid"];
    $ipd_id = $_POST["ipd_id"];
    $user = $_POST["user"];
    $schedule_id_qry = mysqli_fetch_array(mysqli_query($link, "SELECT `schedule_id` FROM `patient_ot_schedule` WHERE `patient_id` = '$patient_id' AND `ipd_id` = '$ipd_id'"));
    $schedule_id = $schedule_id_qry['schedule_id'];

    $pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));

    $pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_id' "));

    $reg_date = $pat_reg["date"];

    if ($pat_info["dob"] != "") {
        $age = age_calculator_date($pat_info["dob"], $reg_date);
    } else {
        $age = $pat_info["age"] . " " . $pat_info["age_type"];
    }

    $prefix_det = mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

    $pat_admit = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_id' ORDER BY `slno` DESC limit 0,1 "));

    $ward = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_admit[ward_id]' "));

    $bed = mysqli_fetch_array(mysqli_query($link, " SELECT `room_id`,`bed_no` FROM `bed_master` WHERE `bed_id`='$pat_admit[bed_id]' "));

    $room = mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' "));
    $request_doc_info = mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$patient_ot_schedule[request_doc_id]' "));
    $ot_area = mysqli_fetch_array(mysqli_query($link, "SELECT `ot_area_name` FROM `ot_area_master` WHERE `ot_area_id`='$patient_ot_schedule[ot_area_id]'"));
    $ot_dept = mysqli_fetch_array(mysqli_query($link, "SELECT `ot_dept_name` FROM `ot_dept_master` WHERE `ot_dept_id`='$patient_ot_schedule[ot_dept_id]'"));

    $discharge_time_str = "NA";
    $discharge_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
    if ($discharge_info) {
        $discharge_time_str = date("d-M-Y", strtotime($discharge_info["date"])) . " " . date("h:i A", strtotime($discharge_info["time"]));
    }
?>
<table class="table table-condensed">
    <tr>
        <th style="width: 100px;">UHID</th>
        <td style="width: auto;">: <?php echo $pat_info["patient_id"]; ?></td>
        <th style="width: 95px;"><?php echo $prefix_det["prefix"]; ?></th>
        <td>: <?php echo $pat_reg["opd_id"]; ?></td>
    </tr>
    <tr>
        <th>Registration Time</th>
        <td>: <?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?>
            <?php echo date("h:i A", strtotime($pat_reg["time"])); ?></td>
        <th>Discharged Time</th>
        <td>: <?php echo $discharge_time_str; ?></td>
    </tr>
    <tr>
        <th>Patient Name</th>
        <td>: <?php echo $pat_info["name"]; ?></td>
        <th>Patient Age</th>
        <td>: <?php echo $age; ?></td>
    </tr>
    <tr>
        <th>Patient Gender</th>
        <td>: <?php echo $pat_info["sex"]; ?></td>
        <th>Room Category</th>
        <td>: <?php echo $ward["name"]; ?>, <span><?php echo $room["room_no"]; ?></span>, <span>Bed No:
                <?php echo $bed["bed_no"]; ?></span></td>
    </tr>
</table>
<table class="table table-condensed table-bordered table-hover text-center">
    <thead class="table_header_fix">
        <tr>
            <th>#</th>
            <th>Service Name</th>
            <th>Quantity</th>
            <th>Rate</th>
            <th>Amount</th>
            <th>Service Date</th>
        </tr>
    </thead>
    <?php
        $bed_qry = mysqli_query($link, "SELECT * FROM `ipd_pat_service_details` WHERE `patient_id` = '$patient_id' AND `ipd_id` = '$ipd_id' AND `group_id` = '167'");
        $n = 1;
        while ($bed = mysqli_fetch_array($bed_qry)) {


        ?>
    <tr>
        <td><?php echo $n; ?></td>
        <td><?php echo $bed['service_text']; ?></td>
        <td><?php echo $bed['ser_quantity']; ?></td>
        <td><?php echo $bed['rate']; ?></td>
        <td><?php echo $bed['amount']; ?></td>

        <td><?php echo $bed['date']; ?></td>
    </tr>
    <?php
            $n++;
        } ?>

</table>
<?php
}

if ($_POST["type"] == "DOCTOR VISIT CHARGES") {
    $patient_id = $_POST["uhid"];
    $ipd_id = $_POST["ipd_id"];
    $user = $_POST["user"];
    $schedule_id_qry = mysqli_fetch_array(mysqli_query($link, "SELECT `schedule_id` FROM `patient_ot_schedule` WHERE `patient_id` = '$patient_id' AND `ipd_id` = '$ipd_id'"));
    $schedule_id = $schedule_id_qry['schedule_id'];

    $pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));

    $pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_id' "));

    $reg_date = $pat_reg["date"];

    if ($pat_info["dob"] != "") {
        $age = age_calculator_date($pat_info["dob"], $reg_date);
    } else {
        $age = $pat_info["age"] . " " . $pat_info["age_type"];
    }

    $prefix_det = mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

    $pat_admit = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_id' ORDER BY `slno` DESC limit 0,1 "));

    $ward = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_admit[ward_id]' "));

    $bed = mysqli_fetch_array(mysqli_query($link, " SELECT `room_id`,`bed_no` FROM `bed_master` WHERE `bed_id`='$pat_admit[bed_id]' "));

    $room = mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' "));
    $request_doc_info = mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$patient_ot_schedule[request_doc_id]' "));
    $ot_area = mysqli_fetch_array(mysqli_query($link, "SELECT `ot_area_name` FROM `ot_area_master` WHERE `ot_area_id`='$patient_ot_schedule[ot_area_id]'"));
    $ot_dept = mysqli_fetch_array(mysqli_query($link, "SELECT `ot_dept_name` FROM `ot_dept_master` WHERE `ot_dept_id`='$patient_ot_schedule[ot_dept_id]'"));

    $discharge_time_str = "NA";
    $discharge_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
    if ($discharge_info) {
        $discharge_time_str = date("d-M-Y", strtotime($discharge_info["date"])) . " " . date("h:i A", strtotime($discharge_info["time"]));
    }
?>
<table class="table table-condensed">
    <tr>
        <th style="width: 100px;">UHID</th>
        <td style="width: auto;">: <?php echo $pat_info["patient_id"]; ?></td>
        <th style="width: 95px;"><?php echo $prefix_det["prefix"]; ?></th>
        <td>: <?php echo $pat_reg["opd_id"]; ?></td>
    </tr>
    <tr>
        <th>Registration Time</th>
        <td>: <?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?>
            <?php echo date("h:i A", strtotime($pat_reg["time"])); ?></td>
        <th>Discharged Time</th>
        <td>: <?php echo $discharge_time_str; ?></td>
    </tr>
    <tr>
        <th>Patient Name</th>
        <td>: <?php echo $pat_info["name"]; ?></td>
        <th>Patient Age</th>
        <td>: <?php echo $age; ?></td>
    </tr>
    <tr>
        <th>Patient Gender</th>
        <td>: <?php echo $pat_info["sex"]; ?></td>
        <th>Room Category</th>
        <td>: <?php echo $ward["name"]; ?>, <span><?php echo $room["room_no"]; ?></span>, <span>Bed No:
                <?php echo $bed["bed_no"]; ?></span></td>
    </tr>
</table>
<table class="table table-condensed table-bordered table-hover text-center">
    <thead class="table_header_fix">
        <tr>
            <th>#</th>
            <th>Service Name</th>
            <th>Doctor Name</th>
            <th>Quantity</th>
            <th>Rate</th>
            <th>Amount</th>
            <th>Service Date</th>
        </tr>
    </thead>
    <?php
        $bed_qry = mysqli_query($link, "SELECT * FROM `ipd_pat_service_details` WHERE `patient_id` = '$patient_id' AND `ipd_id` = '$ipd_id' AND `group_id` = '142'");
        $n = 1;
        while ($bed = mysqli_fetch_array($bed_qry)) {
            $doc_name = mysqli_fetch_array(mysqli_query($link, "SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid` = '$bed[doc_id]'"));
        ?>
    <tr>
        <td><?php echo $n; ?></td>
        <td><?php echo $bed['service_text']; ?></td>
        <td><?php echo $doc_name['Name']; ?></td>

        <td><?php echo $bed['ser_quantity']; ?></td>
        <td><?php echo $bed['rate']; ?></td>
        <td><?php echo $bed['amount']; ?></td>

        <td><?php echo $bed['date']; ?></td>
    </tr>
    <?php
            $n++;
        } ?>

</table>
<?php
}

if ($_POST["type"] == "LABORATORY CHARGES") {
    $patient_id = $_POST["uhid"];
    $ipd_id = $_POST["ipd_id"];
    $user = $_POST["user"];
    $schedule_id_qry = mysqli_fetch_array(mysqli_query($link, "SELECT `schedule_id` FROM `patient_ot_schedule` WHERE `patient_id` = '$patient_id' AND `ipd_id` = '$ipd_id'"));
    $schedule_id = $schedule_id_qry['schedule_id'];

    $pat_info = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `patient_info` WHERE `patient_id`='$patient_id' "));

    $pat_reg = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `uhid_and_opdid` WHERE `patient_id`='$patient_id' AND `opd_id`='$ipd_id' "));

    $reg_date = $pat_reg["date"];

    if ($pat_info["dob"] != "") {
        $age = age_calculator_date($pat_info["dob"], $reg_date);
    } else {
        $age = $pat_info["age"] . " " . $pat_info["age_type"];
    }

    $prefix_det = mysqli_fetch_array(mysqli_query($link, " SELECT `prefix` FROM `patient_type_master` WHERE `p_type_id`='$pat_reg[type]' "));

    $pat_admit = mysqli_fetch_array(mysqli_query($link, " SELECT * FROM `ipd_bed_alloc_details` WHERE `patient_id`='$patient_id' and `ipd_id`='$ipd_id' ORDER BY `slno` DESC limit 0,1 "));

    $ward = mysqli_fetch_array(mysqli_query($link, " SELECT `name` FROM `ward_master` WHERE `ward_id`='$pat_admit[ward_id]' "));

    $bed = mysqli_fetch_array(mysqli_query($link, " SELECT `room_id`,`bed_no` FROM `bed_master` WHERE `bed_id`='$pat_admit[bed_id]' "));

    $room = mysqli_fetch_array(mysqli_query($link, " SELECT `room_no` FROM `room_master` WHERE `room_id`='$bed[room_id]' "));
    $request_doc_info = mysqli_fetch_array(mysqli_query($link, " SELECT `Name` FROM `consultant_doctor_master` WHERE `consultantdoctorid`='$patient_ot_schedule[request_doc_id]' "));
    $ot_area = mysqli_fetch_array(mysqli_query($link, "SELECT `ot_area_name` FROM `ot_area_master` WHERE `ot_area_id`='$patient_ot_schedule[ot_area_id]'"));
    $ot_dept = mysqli_fetch_array(mysqli_query($link, "SELECT `ot_dept_name` FROM `ot_dept_master` WHERE `ot_dept_id`='$patient_ot_schedule[ot_dept_id]'"));

    $discharge_time_str = "NA";
    $discharge_info = mysqli_fetch_array(mysqli_query($link, "SELECT * FROM `ipd_pat_discharge_details` WHERE `patient_id`='$patient_id' AND `ipd_id`='$ipd_id'"));
    if ($discharge_info) {
        $discharge_time_str = date("d-M-Y", strtotime($discharge_info["date"])) . " " . date("h:i A", strtotime($discharge_info["time"]));
    }
?>
<table class="table table-condensed">
    <tr>
        <th style="width: 100px;">UHID</th>
        <td style="width: auto;">: <?php echo $pat_info["patient_id"]; ?></td>
        <th style="width: 95px;"><?php echo $prefix_det["prefix"]; ?></th>
        <td>: <?php echo $pat_reg["opd_id"]; ?></td>
    </tr>
    <tr>
        <th>Registration Time</th>
        <td>: <?php echo date("d-M-Y", strtotime($pat_reg["date"])); ?>
            <?php echo date("h:i A", strtotime($pat_reg["time"])); ?></td>
        <th>Discharged Time</th>
        <td>: <?php echo $discharge_time_str; ?></td>
    </tr>
    <tr>
        <th>Patient Name</th>
        <td>: <?php echo $pat_info["name"]; ?></td>
        <th>Patient Age</th>
        <td>: <?php echo $age; ?></td>
    </tr>
    <tr>
        <th>Patient Gender</th>
        <td>: <?php echo $pat_info["sex"]; ?></td>
        <th>Room Category</th>
        <td>: <?php echo $ward["name"]; ?>, <span><?php echo $room["room_no"]; ?></span>, <span>Bed No:
                <?php echo $bed["bed_no"]; ?></span></td>
    </tr>
</table>
<table class="table table-condensed table-bordered table-hover text-center">
    <thead class="table_header_fix">
        <tr>
            <th>#</th>
            <th>Test Name</th>
            <th>Quantity</th>
            <th>Rate</th>
            <th>Amount</th>
            <th>Test Date</th>
        </tr>
    </thead>
    <?php
        $bed_qry = mysqli_query($link, "SELECT * FROM `ipd_pat_service_details` WHERE `patient_id` = '$patient_id' AND `ipd_id` = '$ipd_id' AND `group_id` = '104'");
        $n = 1;
        while ($bed = mysqli_fetch_array($bed_qry)) {
        ?>
    <tr>
        <td><?php echo $n; ?></td>
        <td><?php echo $bed['service_text']; ?></td>

        <td><?php echo $bed['ser_quantity']; ?></td>
        <td><?php echo $bed['rate']; ?></td>
        <td><?php echo $bed['amount']; ?></td>

        <td><?php echo $bed['date']; ?></td>
    </tr>
    <?php
            $n++;
        } ?>

</table>
<?php
}
?>