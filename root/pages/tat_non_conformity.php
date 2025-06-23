<?php
$emp_id=trim($_SESSION["emp_id"]);
$branch_display="display:none;";
if($p_info["levelid"]==1)
{
	$branch_str="";
	
	$branch_display="display:none;";
	$branch_num=mysqli_num_rows(mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 "));
	if($branch_num>1)
	{
		$branch_display="display:;";
	}
	
	$dept_sel_dis="";
}
else
{
	$branch_str=" AND branch_id='$p_info[branch_id]'";
	$branch_display="display:none;";
	
	$dept_sel_dis="disabled";
}

$branch_id=$p_info["branch_id"];
?>
<!--header-->
<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">
	<table class="table table-bordered text-center">
		<tr>
			<td>
				<center>
					<b>From</b>
					<input class="form-control datepicker span2" type="text" name="from" id="from" value="<?php echo date("Y-m-d"); ?>" readonly>
					<b>To</b>
					<input class="form-control datepicker span2" type="text" name="to" id="to" value="<?php echo date("Y-m-d"); ?>" readonly>
					<select class="span3" id="testid" onchange="">
						<option value="">Select Test</option>
						<?php
						$q=mysqli_query($link,"SELECT `testid`,`testname` FROM `testmaster` ORDER BY `testname`");
						while($r=mysqli_fetch_array($q))
						{
							echo "<option value='$r[testid]'>$r[testname]</option>";
						}
						?>
					</select>
					<select class="span2" id="head_id" style="display:none;">
						<!--<option value="0">Select Department</option>-->
					<?php
						//$head_qry=mysqli_query($link, " select distinct type_id,type_name from testmaster where type_name!='' and type_name!='0' order by type_name ");
						$head_qry=mysqli_query($link, " select distinct a.type_id,a.type_name from testmaster a, patient_test_details b where a.testid=b.testid and a.type_name!='' and a.type_name!='0' order by a.type_name ");
						while($head=mysqli_fetch_array($head_qry))
						{
							echo "<option value='$head[type_id]'>$head[type_name]</option>";
						}
					?>
					</select>
					<select id="branch_id" class="span2" style="<?php echo $branch_display; ?>" onChange="view_all('cat_test_detail')">
					<?php
						$branch_qry=mysqli_query($link, " SELECT `branch_id`,`name` FROM `company_name` WHERE `branch_id`>0 $branch_str ORDER BY `branch_id` ASC ");
						while($branch=mysqli_fetch_array($branch_qry))
						{
							if($branch_id==$branch["branch_id"]){ $branch_sel="selected"; }else{ $branch_sel=""; }
							echo "<option value='$branch[branch_id]' $branch_sel>$branch[name]</option>";
						}
					?>
					</select>
					<select id="encounter" class="span2" style="display:none;">
						<!--<option value="0">All Department</option>-->
						<option value="2">Laboratory</option>
						<!--<option value="3">IPD</option>-->
						<option value="10">Radiology</option>
					<?php
						//~ $qq_qry=mysqli_query($link, " SELECT * FROM `patient_type_master` WHERE `p_type_id`=2 OR `p_type_id`=3 ORDER BY `p_type_id` ");
						//~ while($qq=mysqli_fetch_array($qq_qry))
						//~ {
							//~ echo "<option value='$qq[p_type_id]'>$qq[p_type]</option>";
						//~ }
					?>
					</select>
					<br>
					<!--<button class="btn btn-success" onClick="view_all('head_wise_detail_pat')"><i class="icon-search"></i> Patient wise Details</button>-->
					<button class="btn btn-success" onClick="view_all('tat_non_confirmity_test_detail')"><i class="icon-search"></i> TAT Non Conformity Details</button>
				</center>
			</td>
		</tr>
	</table>
	<div id="load_all" class="ScrollStyle" style="display:none;">
		
	</div>
	<!--<input type="button" value="Print" onClick="print_doc_pat('load_all')" class="btn btn-success">-->
	
	<!--modal-->
    <input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none" />
    <input type="text" id="modtxt" value="0" style="display:none" />
    <div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
        style="border-radius:0;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <!--<button type="button" class="btn btn-mini btn-danger" style="float:right;" data-dismiss="modal" aria-hidden="true"><b>Close</b></button>-->
                    <div id="results">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--modal end-->
	
</div>
<div id="loader" style="margin-top:-10%;"></div>

<style>
#myModal {
    left: 33%;
    width: 75%;
}
</style>

<link rel="stylesheet" href="include/css/jquery-ui.css" />
<script src="include/js/jquery-ui.js"></script>
<script src="../jss/moment.js"></script>
<!-- Time -->
<link rel="stylesheet" href="include/ui-1.10.0/ui-lightness/jquery-ui-1.10.0.custom.min.css" type="text/css" />
<!-- Loader -->
<link rel="stylesheet" href="../css/loader.css" />
<script>
	$(document).ready(function(){
		$("#loader").hide();
		//view_all('head_wise_detail');
		//view_all('head_wise_detail_pat');
		$(".datepicker").datepicker({
			changeMonth:true,
			changeYear:true,
			dateFormat: 'yy-mm-dd',
			maxDate: '0',
			yearRange: "-150:+0",
		});
	});
	function view_all(typ,dep)
	{
		$("#loader").show();
		$.post("pages/headwise_test_data.php",
		{
			type:typ,
			date1:$("#from").val(),
			date2:$("#to").val(),
			branch_id:$("#branch_id").val(),
			head_id:$("#head_id").val(),
			testid:$("#testid").val(),
			encounter:$("#encounter").val(),
			user:$("#user").text().trim()
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#load_all").show().html(data);
		})
	}
	function print_page(val,date1,date2,head_id,encounter,branch_id,testid)
	{
		//~ if(val=="head_wise_detail_pat")
		//~ {
			//~ url="pages/head_wise_detail_pat_print.php?date1="+date1+"&date2="+date2+"&head_id="+head_id+"&encounter="+encounter+"&val="+val;
		//~ }
		
		url="pages/head_wise_detail_pat_print.php?date1="+date1+"&date2="+date2+"&head_id="+head_id+"&encounter="+encounter+"&branch_id="+branch_id+"&val="+val+"&testid="+testid;
		
		wind=window.open(url,'Window','scrollbars=1,menubar=1,toolbar=0,height=670,width=1000');
	}
	function action_cnf(pid,opd,tid)
	{
		$("#loader").show();
		$.post("pages/headwise_test_data.php",
		{
			pid:pid,
			opd:opd,
			tid:tid,
			user:$("#user").text().trim(),
			type:"action_cnf"
		},
		function(data,status)
		{
			$("#loader").hide();
			$("#results").html(data);
			$("#mod").click();
		});
	}
	function non_tat_action_save(pid,opd,tid)
	{
		//~ if($("#delayNote").val().trim()=="")
		//~ {
			//~ $("#delayNote").focus();
		//~ }
		//~ else
		{
			$("#btnSave").attr("disabled",true);
			$("#loader").show();
			$.post("pages/headwise_test_data.php",
			{
				pid:pid,
				opd:opd,
				tid:tid,
				delayNote:$("#delayNote").val().trim(),
				correctiveNote:$("#correctiveNote").val().trim(),
				user:$("#user").text().trim(),
				type:"non_tat_action_save"
			},
			function(data,status)
			{
				$("#loader").hide();
				//alert(data);
				$("#btnClose").click();
			});
		}
	}
	function print_doc_pat(el)
	{
		var restorepage = $('body').html();
		var printcontent = $('#' + el).clone();
		$('body').empty().html(printcontent);
		window.print();
		$('body').html(restorepage);
	}
</script>

