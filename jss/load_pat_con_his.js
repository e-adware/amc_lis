/*
function dept_sel(qq)
{
	$.post("pages/load_pat_con_his.php",
	{
		type:"load_dept_doc",
		dept_id:$("#dept_id").val(),
		uhid:$("#uhid").text().trim(),
	},
	function(data,status)
	{
		$("#load_all_form").hide().html(data).fadeIn('slow');
		$({myScrollTop:window.pageYOffset}).animate({myScrollTop:300}, {
			duration: 1000,
			easing: 'swing',
			step: function(val){
				window.scrollTo(0, val);
			}
		});
		$(".datepicker").datepicker({
			dateFormat: 'yy-mm-dd',
			minDate: '0',
		});
	})
}
*/
