<?php
include("../../includes/connection.php");

$type = $_POST['type'];
if ($type == 1) {
    $val = $_POST['val'];
    if ($val == 1) {
        ?>
        <div id="tab<?php echo $val; ?>" class="tab-pane active">
            <table class="table table-bordered table-condensed">
                <tr>
                    <td>
                        <center>

                            <!-- <button class="btn btn-success" id="btn1" onClick="view_all(1)">Registration Summary</button> -->
                            <!-- <button class="btn btn-success" id="btn2" onClick="view_all(2)">Admissions by Department
                                (IPD)</button> -->
                            <!-- <button class="btn btn-success" id="btn3" onClick="view_all(3)">Admissions by Age Group
                                (IPD)</button> -->
                            <button class="btn btn-success" id="btn2" onClick="view_all_opd_report(2)">Registration By Age
                                Group</button>
                            <!-- <button class="btn btn-success" id="btn4" onClick="view_all(4)">Total Registration By Doctor
                                (IPD)</button> -->

                        </center>
                    </td>
                </tr>

            </table>
            <div id="load_data"></div>

        </div>
        <?php
    } else if ($val == 2) {

        ?>
            <div id="tab<?php echo $val; ?>" class="tab-pane active">
                <table class="table table-bordered table-condensed">
                    <tr>
                        <td style="text-align: center;">
                            <select id="free_patients" class="span2">
                                <option value="0">SELECT FREE TYPE</option>
                                <?php

                                $free_qry = mysqli_query($link, "SELECT * FROM `free_type_master` ORDER BY `name` ASC");
                                while ($free_pat = mysqli_fetch_array($free_qry)) {
                                    echo "<option value='$free_pat[code]'>$free_pat[name]</option>";
                                }
                                ?>
                            </select>

                            <button class="btn btn-success" id="btn1" onClick="view_all_scheme(1)">View</button>

                        </td>

                    </tr>
                    <!-- <tr>
                        <td>
                            <center>

                                <button class="btn btn-success" id="btn1" onClick="view_all_dsc(1)">View</button>


                            </center>
                        </td>
                    </tr> -->

                </table>
                <div id="load_data"></div>

            </div>
        <?php
    } else if ($val == 3) {
        ?>
                <div id="tab<?php echo $val; ?>" class="tab-pane active">
                    <table class="table table-bordered table-condensed">
                        <tr>
                            <td style="text-align: center;">
                                <select id="free_patients" class="span2">
                                    <option value="0">SELECT FREE TYPE</option>
                                <?php
                                // $free_qry = mysqli_query($link, "SELECT `freeType` FROM `uhid_and_opdid` ORDER BY `slno`");
                                // while ($free_pat = mysqli_fetch_array($free_qry)) {
                                //     echo "<option value='$free_pat[freeType]'>$free_pat[freeType]</option>";
                                // }
                        
                                $free_qry = mysqli_query($link, "SELECT * FROM `free_type_master` ORDER BY `name` ASC");
                                while ($free_pat = mysqli_fetch_array($free_qry)) {
                                    echo "<option value='$free_pat[code]'>$free_pat[name]</option>";
                                }
                                ?>
                                </select>
                                <button class="btn btn-success" id="btn2" onClick="view_all_dsc(2)">View</button>


                            </td>
                        </tr>


                    </table>
                    <div id="load_data"></div>

                </div>
        <?php
    } else if ($val == 4) {
        ?>
                    <div id="tab<?php echo $val; ?>" class="tab-pane active">
                        <table class="table table-bordered table-condensed">
                            <tr>
                                <td>
                                    <center>

                                        <!-- <button class="btn btn-success" id="btn1" onClick="view_inv_report(1)">Department Wise</button> -->
                                        <button class="btn btn-success" id="btn2" onClick="view_inv_report(2)">Test Wise</button>
                                        <!-- <button class="btn btn-success" id="btn3" onClick="view_all_opd_report(3)">Total Registration By Doctor (OPD)</button> -->


                                    </center>
                                </td>
                            </tr>

                        </table>
                        <div id="load_data"></div>

                    </div>
        <?php
    } else if ($val == 5) {
        ?>
                        <div id="tab<?php echo $val; ?>" class="tab-pane active">
                            <table class="table table-bordered table-condensed">
                                <tr>
                                    <td>
                                        <center>

                                            <button class="btn btn-success" id="btn1" onClick="ward_occ(1)">Ward Occupancy</button>
                                            <button class="btn btn-success" id="btn2" onClick="view_inv_report(2)">Test Wise</button>
                                            <button class="btn btn-success" id="btn3" onClick="view_all_opd_report(3)">Total Registration By
                                                Doctor (OPD)</button>


                                        </center>
                                    </td>
                                </tr>

                            </table>
                            <div id="load_data"></div>

                        </div>
        <?php
    } else if ($val == 6) {
        ?>
                            <div id="tab<?php echo $val; ?>" class="tab-pane active">
                                <table class="table table-bordered table-condensed">
                                    <tr>
                                        <td>
                                            <center>

                                                <button class="btn btn-success" id="btn1" onClick="audit_reports(1)">Patient Wise</button>
                                                <button class="btn btn-success" id="btn2" onClick="audit_reports(2)">Parameter Wise</button>
                                                <!-- <button class="btn btn-success" id="btn3" onClick="view_all_opd_report(3)">Total Registration By
                                                    Doctor (OPD)</button> -->


                                            </center>
                                        </td>
                                    </tr>

                                </table>
                                <div id="load_data"></div>

                            </div>
        <?php
    } else if ($val == 7) {
        ?>
                                <div id="tab<?php echo $val; ?>" class="tab-pane active">
                                    <table class="table table-bordered table-condensed">
                                        <tr>
                                            <td>
                                                <center>

                                                    <button class="btn btn-success" id="btn1" onClick="total_reg_reports(1)">View</button>


                                                </center>
                                            </td>
                                        </tr>

                                    </table>
                                    <div id="load_data"></div>

                                </div>
        <?php
    }
}
?>