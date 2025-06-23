<div id="content-header">
    <div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">

	<div class="span11"  onkeypress="body_event(event)" tabindex='1'>
		
		<div id="level_1">
			<table class="table table-bordered">
				<tr>
					<th>Total Bed</th><th>Bed Available</th><th>Closed/Unavailable Beds</th><th>Occupied Beds</th><th>Temporary Holding Beds</th>
				</tr>
				<?php
					$branch_id=$p_info['branch_id'];
					
					$tot_bed=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from bed_master"));
					
					$tot_avail=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from bed_master where bed_id not in(select bed_id from ipd_pat_bed_details)"));
					
					$tot_cls=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from bed_master where status='1'"));
					
					$tot_occ=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from ipd_pat_bed_details"));
					
					$tot_temp=mysqli_fetch_array(mysqli_query($link,"select count(*) as tot from ipd_bed_details_temp"));
					
					$avail=$tot_avail[tot]-$tot_temp[tot];
					
					
					$bq=mysqli_query($link,"SELECT b.* FROM `bed_master` b, `ward_master` c WHERE b.`ward_id`=c.`ward_id` AND c.`branch_id`='$branch_id' AND b.`share_bed`=0 ");
					$b_num=mysqli_num_rows($bq);
					$bed_occu=mysqli_num_rows(mysqli_query($link,"SELECT a.* FROM `ipd_pat_bed_details` a, `bed_master` b, `ward_master` c WHERE a.`bed_id`=b.`bed_id` AND b.`ward_id`=c.`ward_id` AND c.`branch_id`='$branch_id' AND b.`share_bed`=0 "));
					$bed_bloc=mysqli_num_rows(mysqli_query($link,"SELECT b.* FROM `bed_master` b, `ward_master` c WHERE b.`ward_id`=c.`ward_id` AND c.`branch_id`='$branch_id' AND b.`share_bed`=0 AND b.`status`='1' "));
					$bed_avail=$b_num-$bed_occu-$bed_bloc;
					
					
					
				?>
				<tr onclick="load_ward(0)">
					<td><?php echo $b_num;?></td>
					<td><?php echo $bed_avail;?></td>
					<td><?php echo $bed_bloc;?></td>
					<td><?php echo $bed_occu;?></td>
					<td><?php echo $tot_temp[tot];?></td>
				</tr>
			</table>
		</div>	
		
		<div id="level_2"></div>
		
		<div id="level_3"></div>
		
		<div id="level_4"></div>
		
		<div id="butts" style="display:none">
			  <span class="icon-circle-arrow-left btn btn-danger butts" onclick="load_prev_data()"></span>
		</div>
	</div>
</div>
<input type="button" data-toggle="modal" data-target="#myModal1" id="mod" style="display:none"/>
<input type="hidden" id="mod_chk" value="0"/>
<div class="modal fade" id="myModal1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<div id="results">
				</div>
				<!--
				
				-->
			</div>
		</div>
	</div>
</div>



<script>
function load_ward(chk)
{
	$.post("pages/bed_details_ajax.php",
	{
		type:2
	},
	function(data,status)
	{
		var val=parseInt($("[id^=level]:visible").attr("id").split("_")[1]);
		if(chk==1)
		{
			var nval=val-1;
		}
		else
		{
			var nval=val+1;
		}
		
		
		$("#level_"+nval+"").html(data);
		
		$("#level_"+val+"").slideUp(200,function()
		{
			$("#level_"+nval+"").slideDown(200,function(){ $("#butts").fadeIn(200);});
			
		})
	})
}

function load_room(ward,chk)
{
	$.post("pages/bed_details_ajax.php",
	{
		type:3,
		ward:ward
	},
	function(data,status)
	{
		var lev=parseInt($("[id^=level]:visible").attr("id").split("_")[1]);
		if(chk==1)
		{
			var nlev=lev-1;
		}
		else
		{
			var nlev=lev+1;
		}
		
		$("#level_"+nlev+"").html(data);
		
		$("#level_"+lev+"").slideUp(200,function()
		{
			$("#level_"+nlev+"").slideDown(200,function(){ });
			
		})
	})	
}

function load_prev_data()
{
	
	var val=$("[id^=level]:visible").attr("id").split("_")[1];
	
	
	if(val==3)
	{
		load_ward(1);
	}
	else if(val==2)
	{
		load_ward(1)		
	}
	else if(val==4)
	{
		var ward=$("#ward_id").val();
		load_room(ward,1);
		$("html, body").animate({ scrollTop: 0 },"slow")
	}
}

function load_room_details(ward,room)
{
	$.post("pages/bed_details_ajax.php",
	{
		type:4,
		ward:ward,
		room:room,
		
	},
	function(data,status)
	{
		var lev=parseInt($("[id^=level]:visible").attr("id").split("_")[1]);
		var nlev=lev+1;
		
		$("#level_"+nlev+"").html(data);
		
		$("#level_"+lev+"").slideUp(200,function()
		{
			$("#level_"+nlev+"").slideDown(200,function(){ $("html, body").animate({ scrollTop: 90 },"slow") });
			
		})
	})
}

function body_event(e)
{
	var unicode=e.keyCode? e.keyCode : e.charCode;
	if(unicode==27)
	{
		$(".butts").click();	
	}
}

function change_status(val)
{
	var bed_id=$("#bed_"+val+"").val();
	
	$.post("pages/bed_details_ajax.php",
	{
		type:5,
		bed:bed_id
	},
	function(data,status)
	{
		bootbox.dialog(
		{
            message: data,
            buttons: 
            {
                success: {
                    label: "Save",
                    className: "btn-success",
                    callback:function(){ save_bed_stat();}
                },
                cancel: {
                    label: "Cancel",
                    className: "btn-danger",
                    callback:function(){ $(".span11").focus();}
                }
            }
            
        });
	})
}
function save_bed_stat()
{
	$.post("pages/bed_details_ajax.php",
	{
		bed:$("#bd_st").val(),
		stat:$("#status").val(),
		reas:$("#reason").val(),
		ad_info:$("#info").val(),
		type:6
	},
	function(data,status)
	{
		var ward=$("#ward_id").val();
		var room=$("#room_id").val();
		
		$.post("pages/bed_details_ajax.php",
		{
			type:4,
			ward:ward,
			room:room,
			
		},
		function(data,status)
		{
			$("#level_4").html(data);
		})
	})
	
}

function load_blck_reas(val)
{
	if(val==1)
	{
		$("#blck_reason, #add_info").slideDown(1000);
	}
	else
	{
		$("#blck_reason,#add_info").slideUp(200);
	}
}
</script>
<style>

.table tr:not(:first-child) td
{
	cursor:pointer;
}

.bed_box
{
	display:inline-block;
	width:150px;
	height:150px;
	border-radius:10%;
	border:3px solid;
	background-color:white;
	margin:20px 0px 0px 25px;
}

.bed_box:hover
{
	
}
.occupy
{
	background-color:#00C851;
	color:black;
}

.other_selected
{
	background-color:#ff4444;
	color:white;
}

.blocked
{
	background-color:#ffbb33;
	color:black;
}

.btn {padding:-10px !important;}

.text_big{ font-size:14px; font-weight:bold;position:relative;top:5px}
.text_med{ font-size:11px; }
.text_small{ font-size:9px;}
</style>
