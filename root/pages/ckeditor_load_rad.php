<?php
include("../../includes/connection.php");
include("../../includes/global.function.php");

$uhid=$_GET['uhid'];
$opd_id=$_GET['opd_id'];
$ipd_id=$_GET['ipd_id'];
$batch_no=$_GET['batch_no'];

$tst=$_GET['tstid'];

$user=$_GET['user'];

$rep_doc=$_GET['doc'];

$category_id=$_GET['category_id'];



if($opd_id)
{
	$reg_id=$opd_id;
}
if($ipd_id)
{
	$reg_id=$ipd_id;
}

$tname=mysqli_fetch_array(mysqli_query($link,"select * from testmaster where testid='$tst'"));
$pin=mysqli_fetch_array(mysqli_query($link,"select * from uhid_and_opdid where opd_id='$reg_id'"));

$pat_info=mysqli_fetch_array(mysqli_query($link, "select * from patient_info where patient_id='$uhid'"));

$obsrv=mysqli_fetch_array(mysqli_query($link, "select * from testresults_rad where patient_id='$uhid' and opd_id='$opd_id' and ipd_id='$ipd_id' and batch_no='$batch_no' and testid='$tst'"));

if(!$rep_doc || $rep_doc==0)
{
	$rep_doc=$obsrv["doc"];
}

$res_type=0;

if($obsrv['observ']!='')
{
	$res=$obsrv['observ'];	
	$tn=trim($obsrv[testname]);
	
	$res_type=1;
}
else if($obsrv[observ]=="<p><br></p>")
{
	$res=$obsrv[observ];
	if($obsrv[testname])
	{
		$tn=trim($obsrv[testname]);
	}
	else
	{
		$tn=$tname[testname];
	}
	$res_type=1;
}
else
{
	$doc_normal=1;
	$norm=mysqli_fetch_array(mysqli_query($link, "select normal from radiology_normal where testid='$tst' and doctor='$rep_doc'"));	
	if(!$norm[normal] || $norm[normal]=="<p><br></p>")
	{
		$doc_normal=0;
		$norm=mysqli_fetch_array(mysqli_query($link, "select normal from radiology_normal where testid='$tst' and doctor='0' "));	
	}
	$res=$norm[normal];
	//$tname=$tst[3];
	
	$res_type=2;
	
	$tn=$tname[testname];
}



?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Radiology Reporting</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="../../css/bootstrap.min.css" />
	<link rel="stylesheet" href="../../font-awesome/css/font-awesome.css" />
	<script src="../../js/jquery.min.js"></script>
	<script src="../../js/bootstrap.min.js"></script>
	<script src="../../js/matrix.js"></script>
	
	<script type="text/javascript" src="../../ckeditor_rad/ckeditor.js"></script>
	<script src="../../nanospell/autoload.js"></script>
</head>

<body onkeydown="close_window(event)">
	<div class="container-fluid" tabindex="1">
	
	<input type="hidden" value="<?php echo $tst;?>" id="tstid"/>
	<input type="hidden" value="<?php echo $uhid;?>" id="uhid"/>
	<input type="hidden" value="<?php echo $opd_id;?>" id="opd_id"/>
	<input type="hidden" value="<?php echo $ipd_id;?>" id="ipd_id"/>
	<input type="hidden" value="<?php echo $reg_id;?>" id="reg_id"/>
	<input type="hidden" value="<?php echo $batch_no;?>" id="batch"/>
	<input type="hidden" value="<?php echo $tname[category_id];?>" id="category_id"/>
	<input type="hidden" value="<?php echo $user;?>" id="user"/>
	
	<div class="span6" style="padding:10px;font-size:15px;background-color:rgba(50, 115, 520, 0.05);height:95%;border-radius:2%;margin-left:-15px;">
		<br/><br/><br/>
		
		<span style="font-size:20px;">
		<b>TEST <input type="text" id="test_name" value="<?php echo $tn;?>" style="height:40px;width:100%"/></b>
		</span>
		<br/><br/><br/>
		<b>
		<?php
		if($rep_doc>0)
		{
			$rep_name=mysqli_fetch_array(mysqli_query($link,"select name from lab_doctor where id='$rep_doc'"));
			?>
			<input type="hidden" id="rep_doc" value="<?php echo $rep_doc;?>" />
			Reporting Doctor: <br/>
			<?php echo "<div style='font-weight:normal;margin-top:10px;font-size:18px;'>".$rep_name["name"]."</div>";
		}
		else
		{
		?>
		Reporting Doctor:
		<select id="rep_doc" style="width:100%" onchange="change_doc(this.value)">
			<option value="0">--Select--</option>
			<?php
			$doc=mysqli_query($link,"select * from lab_doctor where category='$category_id' order by name");	
			while($rd=mysqli_fetch_array($doc))
			{
				if($rd[id]==$rep_doc){ $doc_s="Selected='selected'";} else { $doc_s="";}
				echo "<option value='$rd[id]' $doc_s>$rd[name]</option>";
			}
			?>
		</select> 
		<?php } ?>
		</b>
		Digital Sign Doctor:
		<select id="sign_doc_id" style="width:100%">
			<option value="0">--Select--</option>
			<?php
			$qry=mysqli_query($link,"select * from lab_doctor where category='$category_id' and sign_name!='' order by name");
			while($data=mysqli_fetch_array($qry))
			{
				echo "<option value='$data[id]' $doc_s>$data[name]</option>";
			}
			?>
		</select> 
		<br/><br/> <br/>
		<table class="table table-bordered table-report" style="font-size:13px;">
			<tr>
				<th colspan="2"> Patient Info</th>
			</tr>
			<tr>
				<th>Reg ID</th> <th> <?php echo $pin['opd_id']; ?> </th>
			</tr>
			<tr>
				<th>Name</th> <th> <?php echo $pat_info["name"]; ?> </th>
			</tr>
			<tr>
				<th>Age / Sex</th> <th> <?php echo $pat_info["age"]; ?> <?php echo $pat_info["age_type"]; ?> / <?php echo $pat_info["sex"]; ?></th>
			</tr>
			<tr>
				<th>Phone</th> <th> <?php echo $pat_info["phone"]; ?></th>
			</tr>
		</table>
	</div>	
	<div class="span9">
		<select id="findid" onchange="load_findings(this.value)">
			<option value="0">--Default(Select Finding)--</option>
			<?php
				$find=mysqli_query($link,"select id,name from radiology_normal_finding order by name");
				while($f=mysqli_fetch_array($find))
				{
					echo "<option value='$f[id]'>$f[name]</option>";
				}
			?>
		</select>
		
		<select id="tst_form" onchange="load_normal(this.value)">
			<option value="0">Other Normal Formats</option>
			<?php
			$tst_f=mysqli_query($link,"select * from testmaster where category_id='2' order by testname");
			while($tf=mysqli_fetch_array($tst_f))
			{
				echo "<option value='$tf[testid]'>$tf[testname]</option>";
			}
			?>
		</select>
		
		<textarea style="height:350px;width:1000px" name="article-body" id="txtdetail">
			<?php echo $res;?>
		</textarea>
		
		<?php
		$print_dis="disabled";
		if($res_type==1 && $obsrv[doc]>0)
		{
			$print_dis="";
			
		}
		$sav_val="";
		if($rep_doc==0)
		{
			$sav_val="disabled";
		}
		?>
		
		<div style="position:fixed;bottom:5;" class="btn-group">
			<button class="btn btn-warning" id="save" onclick="save_res()"><i class="icon-save"></i> Save</button>
			<button class="btn btn-success" id="validate" onclick="res_valid()" disabled><i class="icon-save"></i> Validate</button>
			<button class="btn btn-primary" id="save_valid" onclick="save_valid()" <?php echo $sav_val;?> ><i class="icon-file" ></i> Save & Validate</button>
			<button class="btn btn-info" id="print" onclick="print_report()" <?php echo $print_dis;?> ><i class="icon-print"></i> Print</button>
			<button class="btn btn-danger" onclick="window.close()" id="close"><i class="icon-off"></i> Exit</button>
		</div>
	</div>	
		
		
<?php
	$bq=mysqli_query($link,"SELECT * FROM `bed_master`");
?>
		
	</div>
</body>
</html>
<script>
	$(document).ready(function(){
	
		//$("#test_name").focus();
		add();
	});
	
	function add()
	{
		if (CKEDITOR.instances['article-body'])
		{
			CKEDITOR.instances['article-body'].destroy(true);
		}
		CKEDITOR.replace('article-body', {toolbarStartupExpanded : false});
		CKEDITOR.config.enterMode=CKEDITOR.ENTER_BR;
		CKEDITOR.config.extraPlugins = 'lineheight';
		//CKEDITOR.config.width = 700;		
		CKEDITOR.config.height = 550;		
		CKEDITOR.config.toolbarCanCollapse = true;
		CKEDITOR.config.line_height="1.0em;1.5em;2.0em;2.5em;3.0em;3.5em;4.0em;4.5em;5.0em;" ;	
		
		
		//~ CKEDITOR.replace( 'article-body',
		//~ {
		//~ on:
			//~ {
			//~ 'instanceReady' : function( evt )
			//~ {
		
			//~ CKEDITOR.instances['article-body'].focus();
			//~ }
		//~ }
		//~ })
		
	}
	
	function close_window(e)
	{
		var unicode=e.keyCode? e.keyCode : e.charCode;

		if(unicode==27)
		{
			setTimeout(function(){ window.close();},400);
			
		}
	}
	function load_findings(id)
	{
		
		if(id>0)
		{
			$.post("radiology_normal_finding_ajax.php",
			{
				id:id,
				type:"load"
			},
			function(data,status)
			{
				$(".rad_res").contents().find('body').html(data);
				
			})
		}
		else
		{
			load_normal($("#tstid").val());
		}	
	}
	function load_normal(tst)
	{
		$.post("radiology_normal_info.php",
		{
			id: tst,
			doctor: $("#rep_doc").val(),
			category_id:$("#category_id").val(),
		},
		function(data, status) 
		{
			$(".rad_res").contents().find('body').html(data);
		})
	}
	function change_doc(val)
	{
		if(val>0)
		{
			$("#save_valid").prop("disabled",false);
			$("#validate").prop("disabled",false);
		}
		else
		{
			$("#save_valid").prop("disabled",true);
			$("#validate").prop("disabled",true);
		}
	}
	function save_res()
	{
		bootbox.dialog({ message: "<h5 id='b_msg'>Saving..</h5>"});
		$.post("radiology_reporting_data.php",
		{
			uhid:$("#uhid").val(),
			opd_id:$("#opd_id").val(),
			ipd_id:$("#ipd_id").val(),
			batch:$("#batch").val(),
			tst:$("#tstid").val(),
			tst_name:$("#test_name").val().trim(),
			res: $(".rad_res").contents().find('body').html(),
			user:$("#user").val(),
			type:"save_result"
		},
		function(data,status)
		{
			$("#b_msg").text("Saved");
			setTimeout(function(){
				bootbox.hideAll();
				if($("#rep_doc").val()>0)
				{
					$("#validate").prop("disabled",false);
					$("#save_valid").prop("disabled",false);
				}
				else
				{
					$("#validate").prop("disabled",true);
					$("#save_valid").prop("disabled",true);
				}
				$("#print").prop("disabled",true);
				load_test_data();
			},1000);
			return true;
			
		})
	}
	function res_valid()
	{
		bootbox.dialog({ message: "<h5 id='b_msg'>Validating..</h5>"});
		$.post("radiology_reporting_data.php",
		{
			uhid:$("#uhid").val(),
			opd_id:$("#opd_id").val(),
			ipd_id:$("#ipd_id").val(),
			tst:$("#tstid").val(),
			batch:$("#batch").val(),
			rep_doc:$("#rep_doc").val(),
			type:"valid_result"
		},
		function(data,status)
		{
			if(data=="valid")
			{
				$("#b_msg").text("Validated");
				setTimeout(function(){
					bootbox.hideAll();
					$("#print").prop("disabled",false);
					load_test_data();
				},1000);
				return true;
			}
			else
			{
				$("#b_msg").text("ERROR");
				setTimeout(function(){
					bootbox.hideAll();
				},1000);
			}
			
			
		})
	}
	
	function print_report()
	{
		var uhid=$("#uhid").val();
		var opd_id=$("#opd_id").val();
		var ipd_id=$("#ipd_id").val();
		var tst=$("#tstid").val();
		var batch=$("#batch").val();
		
		var category_id=$("#category_id").val();
		var sign_doc_id=$("#sign_doc_id").val();
		var user=$("#user").val();
		var view=0;
		
		var url = "radiology_report_print.php?uhid=" + btoa(uhid) + "&opd_id=" + btoa(opd_id) + "&ipd_id=" + btoa(ipd_id)+ "&batch_no=" + btoa(batch) + "&tstid=" + btoa(tst) + "&category_id=" + btoa(category_id) + "&sign_doc_id=" + btoa(sign_doc_id) + "&user=" + btoa(user) + "&view=" + btoa(view);
		var win = window.open(url, '', 'fullScreen=yes,scrollbars=yes,menubar=1');
		
		load_test_data();
	}
	
	function save_valid()
	{
		save_res();
		res_valid();
	}
	
	function load_test_data()
	{
		
		var uhid=$("#uhid").val();
		var opd_id=$("#opd_id").val();
		var ipd_id=$("#ipd_id").val();
		var batch=$("#batch").val();
		var tst=$("#tstid").val();
		
		var nval="@@"+uhid+"@@"+opd_id+"@@"+ipd_id+"@@"+batch+"@@"+tst;
		
		
		window.opener.load_test_info_rad(nval);
	}
	
	//~ $(window).on("beforeunload", function() 
	//~ { 
		//~ return confirm("Do you really want to close?"); 
	//~ })
</script>
<script src="../../jss/bootbox.min.js"></script> 
<style>

</style>
