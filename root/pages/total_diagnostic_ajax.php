<?php
include ("../../includes/connection.php");
include ("../../includes/global.function.php");

$type = $_POST['type'];
$date = date("Y-m-d");
$time = date("H:i:s");

if ($type == 1) {
    $fdate = $_POST['fdate'];
    $tdate = $_POST['tdate'];
    $branch_id = $_POST['branch_id'];  
    ?>
    <table class="table table-bordered table-condensed table-report table-white" id="pat">
        <tr>
            <td>#</td>
            <td>Department</td>
            <td>Total Count</td>
        </tr>
        <?php
        $i = 1;
        $total_count = 0;

		// SELECT DISTINCT a.type_id FROM `testmaster` a, `patient_test_details` b, `uhid_and_opdid` c WHERE a.testid=b.testid AND b.patient_id=c.patient_id AND (b.opd_id=c.opd_id OR b.ipd_id=c.opd_id) AND c.date BETWEEN '2024-06-01' AND '2024-08-01' AND c.branch_id=1

        $diag_count = mysqli_query($link, "SELECT a.type_id, COUNT(a.type_id) as count 
                                           FROM testmaster a 
                                           JOIN patient_test_details b ON a.testid = b.testid 
                                           JOIN uhid_and_opdid c ON b.patient_id = c.patient_id 
                                           WHERE (b.opd_id = c.opd_id OR b.ipd_id = c.opd_id) 
                                             AND c.date BETWEEN '$fdate' AND '$tdate' 
                                             AND c.branch_id = '$branch_id'
                                           GROUP BY a.type_id");

        while ($tot_diag_count = mysqli_fetch_array($diag_count)) {
            $type_id = $tot_diag_count['type_id'];
            $count = $tot_diag_count['count'];
            $total_count += $count;

            $dept_diag = mysqli_query($link, "SELECT name FROM test_department WHERE id = '$type_id'");
            $dept_nm = mysqli_fetch_array($dept_diag);
            ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $dept_nm["name"]; ?></td>
                <td><?php echo $count; ?></td>
            </tr>
            <?php
            $i++;
        }
        ?>
        <tr>
            <td colspan="2" style="text-align:right">Total :</td>
            <td><?php echo $total_count; ?></td>
        </tr>
    </table>
    <?php
}
?>

</table>