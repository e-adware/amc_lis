<div id="content-header">
	<div class="header_div"> <span class="header"> <?php echo $menu_info["par_name"]; ?></span></div>
</div>
<!--End-header-->
<div class="container-fluid">

	<div class="widget-title">
		<ul class="nav nav-tabs">
			<li onclick="tab_data(1)"><a data-toggle="tab" href="#tab1">Parameter Fix Value</a></li>
			<li onclick="tab_data(2)"><a data-toggle="tab" href="#tab2">Sample Master</a></li>
			<li onclick="tab_data(3)"><a data-toggle="tab" href="#tab3">Vaccu Master</a></li>
			<li onclick="tab_data(4)"><a data-toggle="tab" href="#tab4">Method Master</a></li>
			<li onclick="tab_data(6)"><a data-toggle="tab" href="#tab6">Option List Master</a></li>
			<li onclick="tab_data(5)"><a data-toggle="tab" href="#tab5">List Of Choice Master</a></li>
			<li onclick="tab_data(7)"><a data-toggle="tab" href="#tab7">Culture Set Up</a></li>
			<li onclick="tab_data(8)"><a data-toggle="tab" href="#tab8">Test Summary</a></li>
			<li onclick="tab_data(9)"><a data-toggle="tab" href="#tab8">Barcode Suffix</a></li>


		</ul>
	</div>


	<div class="widget-content tab-content" id="tab_data">

	</div>

	<input type="button" data-toggle="modal" data-target="#myModal" id="mod" style="display:none" />
	<div class="modal fade" id="myModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"
		style="border-radius:0;display:none">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
					<div id="results"> </div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>

	function tab_data(val) {
		$.post("pages/master_set_page_ajax.php",
			{
				val: val,
				type: 1
			},
			function (data, status) {
				$("#tab_data").html(data);
			})
	}

	function load_all_param() {
		$.post("pages/master_set_page_ajax.php",
			{
				type: 2,
				testid: $("#testid").val(),
			},
			function (data, status) {
				$("#param").html(data);
				$("#load_data").html("");

			})
	}

	function load_fix_param() {
		$.post("pages/master_set_page_ajax.php",
			{
				type: 3,
				testid: $("#testid").val(),
				param: $("#param").val(),
			},
			function (data, status) {
				$("#load_data").html(data);
			})
	}

	function save_param_fix_val(testid, param) {
		bootbox.dialog({ message: "<h5 id='alert_msg'>Saving</h5>" });

		if ($("#range_check").prop("checked")) {
			var range_check = 1;
		} else {
			var range_check = 0;
		}

		if ($("#must_save").prop("checked")) {
			var must_save = 1;
		} else {
			var must_save = 0;
		}

		$.post("pages/master_set_page_ajax.php",
			{
				type: 4,
				testid: testid,
				param: param,
				fix_param_val: $("#fix_param_val").val(),
				range_check: range_check,
				must_save: must_save,
			},
			function (data, status) {
				$("#alert_msg").text("Saved");
				setTimeout(function () {
					bootbox.hideAll();
				}, 1000);
			})
	}

	function search_sample(elem, e) {
		if (e.which == 13) {
			$.post("pages/master_set_page_ajax.php",
				{
					sname: $(elem).val(),
					val: 2,
					type: 1
				},
				function (data, status) {
					$("#tab_data").html(data);
				})
		}
	}

	function add_new_sample(val, sname) {
		var title = "ADD NEW SAMPLE"
		var btn_name = "Save";
		var alert_msg = "Saving";

		if (val > 0) {
			title = "UPDATE SAMPLE: " + sname;
			btn_name = "Update";
			alert_msg = "Updating";
		}

		bootbox.dialog({
			title: title,
			message: "<p><input type='text' id='new_samp" + val + "' value='" + sname + "' style='width:90%' placeholder='Enter Sample Name Here'/></p>",
			size: 'large',
			buttons: {
				save: {
					label: "<i class='icon-save'></i> " + btn_name,
					className: 'btn-success',
					callback: function () {
						bootbox.dialog({ message: "<h5 id='alert_msg'>" + alert_msg + "</h5>" });
						$.post("pages/master_set_page_ajax.php",
							{
								sid: val,
								name: $("#new_samp" + val + "").val(),
								type: 5
							},
							function (data, status) {
								$("#alert_msg").text(data);
								setTimeout(function () {
									bootbox.hideAll();

									$.post("pages/master_set_page_ajax.php",
										{
											sname: $("#s_name").val(),
											val: 2,
											type: 1
										},
										function (data, status) {
											$("#tab_data").html(data);
										})

								}, 1000);
							})
					}
				},
				close: {
					label: "<i class='icon-off'></i> Close",
					className: 'btn-danger',
					callback: function () {
						console.log('Custom button clicked');

					}
				}
			}
		})

		setTimeout(function () { $("#new_samp" + val + "").focus(); }, 500);
	}

	function delete_sample(val, name) {
		bootbox.dialog({
			message: "<h5>Do you really want to delete '" + name + "'?</h5>",
			size: 'large',
			buttons: {
				save: {
					label: "<i class='icon-ok'></i> Yes",
					className: 'btn-success',
					callback: function () {
						bootbox.dialog({ message: "<h5 id='alert_msg'>Deleting</h5>" });
						$.post("pages/master_set_page_ajax.php",
							{
								sid: val,
								type: 6
							},
							function (data, status) {
								$("#alert_msg").text(data);
								setTimeout(function () {
									bootbox.hideAll();

									$.post("pages/master_set_page_ajax.php",
										{
											sname: $("#s_name").val(),
											val: 2,
											type: 1
										},
										function (data, status) {
											$("#tab_data").html(data);
										})

								}, 1000);
							})
					}
				},
				close: {
					label: "<i class='icon-remove'></i> No",
					className: 'btn-danger',
					callback: function () {
						console.log('Custom button clicked');

					}
				}
			}
		})
	}

	function search_vaccu(elem, e) {
		if (e.which == 13) {
			$.post("pages/master_set_page_ajax.php",
				{
					vname: $(elem).val(),
					val: 3,
					type: 1
				},
				function (data, status) {
					$("#tab_data").html(data);
				})
		}
	}

	function add_new_vaccu(val, vname, suff) {
		var title = "ADD NEW VACCU"
		var btn_name = "Save";
		var alert_msg = "Saving";

		if (val > 0) {
			title = "UPDATE VACCU: " + vname;
			btn_name = "Update";
			alert_msg = "Updating";
		}

		bootbox.dialog({
			title: title,
			message: "<p><input type='text' id='new_vac" + val + "' value='" + vname + "' style='width:40%' placeholder='Enter Vaccu Name Here'/> <input type='text' id='vac_suff" + val + "' value='" + suff + "' style='width:40%' placeholder='Enter Barcode Suffix'/></p>",
			size: 'large',
			buttons: {
				save: {
					label: "<i class='icon-save'></i> " + btn_name,
					className: 'btn-success',
					callback: function () {
						bootbox.dialog({ message: "<h5 id='alert_msg'>" + alert_msg + "</h5>" });
						$.post("pages/master_set_page_ajax.php",
							{
								vid: val,
								name: $("#new_vac" + val + "").val(),
								suff: $("#vac_suff" + val + "").val(),
								type: 7
							},
							function (data, status) {
								$("#alert_msg").text(data);
								setTimeout(function () {
									bootbox.hideAll();

									$.post("pages/master_set_page_ajax.php",
										{
											vname: $("#v_name").val(),
											val: 3,
											type: 1
										},
										function (data, status) {
											$("#tab_data").html(data);
										})

								}, 1000);
							})
					}
				},
				close: {
					label: "<i class='icon-off'></i> Close",
					className: 'btn-danger',
					callback: function () {
						console.log('Custom button clicked');

					}
				}
			}
		})

		setTimeout(function () { $("#new_vac" + val + "").focus(); }, 500);
	}

	function delete_vaccu(val, name) {
		bootbox.dialog({
			message: "<h5>Do you really want to delete '" + name + "'?</h5>",
			size: 'large',
			buttons: {
				save: {
					label: "<i class='icon-ok'></i> Yes",
					className: 'btn-success',
					callback: function () {
						bootbox.dialog({ message: "<h5 id='alert_msg'>Deleting</h5>" });
						$.post("pages/master_set_page_ajax.php",
							{
								vid: val,
								type: 8
							},
							function (data, status) {
								$("#alert_msg").text(data);
								setTimeout(function () {
									bootbox.hideAll();

									$.post("pages/master_set_page_ajax.php",
										{
											vname: $("#v_name").val(),
											val: 3,
											type: 1
										},
										function (data, status) {
											$("#tab_data").html(data);
										})

								}, 1000);
							})
					}
				},
				close: {
					label: "<i class='icon-remove'></i> No",
					className: 'btn-danger',
					callback: function () {
						console.log('Custom button clicked');

					}
				}
			}
		})
	}

	function search_method(elem, e) {
		if (e.which == 13) {
			$.post("pages/master_set_page_ajax.php",
				{
					mname: $(elem).val(),
					val: 4,
					type: 1
				},
				function (data, status) {
					$("#tab_data").html(data);
				})
		}
	}

	function add_new_method(val, mname) {
		var title = "ADD NEW METHOD"
		var btn_name = "Save";
		var alert_msg = "Saving";

		if (val > 0) {
			title = "UPDATE METHOD: " + mname;
			btn_name = "Update";
			alert_msg = "Updating";
		}

		bootbox.dialog({
			title: title,
			message: "<p><input type='text' id='new_meth" + val + "' value='" + mname + "' style='width:90%' placeholder='Enter Method Name Here'/></p>",
			size: 'large',
			buttons: {
				save: {
					label: "<i class='icon-save'></i> " + btn_name,
					className: 'btn-success',
					callback: function () {
						bootbox.dialog({ message: "<h5 id='alert_msg'>" + alert_msg + "</h5>" });
						$.post("pages/master_set_page_ajax.php",
							{
								mid: val,
								name: $("#new_meth" + val + "").val(),
								type: 9
							},
							function (data, status) {
								$("#alert_msg").text(data);
								setTimeout(function () {
									bootbox.hideAll();

									$.post("pages/master_set_page_ajax.php",
										{
											mname: $("#m_name").val(),
											val: 4,
											type: 1
										},
										function (data, status) {
											$("#tab_data").html(data);
										})

								}, 1000);
							})
					}
				},
				close: {
					label: "<i class='icon-off'></i> Close",
					className: 'btn-danger',
					callback: function () {
						console.log('Custom button clicked');

					}
				}
			}
		})

		setTimeout(function () { $("#new_meth" + val + "").focus(); }, 500);
	}

	function delete_method(val, name) {
		bootbox.dialog({
			message: "<h5>Do you really want to delete '" + name + "'?</h5>",
			size: 'large',
			buttons: {
				save: {
					label: "<i class='icon-ok'></i> Yes",
					className: 'btn-success',
					callback: function () {
						bootbox.dialog({ message: "<h5 id='alert_msg'>Deleting</h5>" });
						$.post("pages/master_set_page_ajax.php",
							{
								mid: val,
								type: 10
							},
							function (data, status) {
								$("#alert_msg").text(data);
								setTimeout(function () {
									bootbox.hideAll();

									$.post("pages/master_set_page_ajax.php",
										{
											sname: $("#s_name").val(),
											val: 4,
											type: 1
										},
										function (data, status) {
											$("#tab_data").html(data);
										})

								}, 1000);
							})
					}
				},
				close: {
					label: "<i class='icon-remove'></i> No",
					className: 'btn-danger',
					callback: function () {
						console.log('Custom button clicked');

					}
				}
			}
		})
	}

	function search_res_op(elem, e) {
		if (e.which == 13) {
			$.post("pages/master_set_page_ajax.php",
				{
					rname: $(elem).val(),
					val: 5,
					type: 1
				},
				function (data, status) {
					$("#tab_data").html(data);
				})
		}
	}

	function add_new_res_op(val, rname) {

		var title = "ADD NEW LIST"
		var btn_name1 = "Save & Link Option";
		var btn_name2 = "Save & Exit";
		var alert_msg = "Saving";

		if (val > 0) {
			title = "UPDATE LIST: " + rname;
			btn_name1 = "Update & Link Option";
			btn_name2 = "Update & Exit";
			alert_msg = "Updating";
		}

		bootbox.dialog({
			title: title,
			message: "<p><input type='text' id='new_list" + val + "' value='" + rname + "' style='width:90%' placeholder='Enter List Name Here'/></p>",
			size: 'large',
			buttons: {
				save: {
					label: "<i class='icon-cogs'></i> " + btn_name1,
					className: 'btn-success',
					callback: function () {
						bootbox.dialog({ message: "<h5 id='alert_msg'>" + alert_msg + "</h5>" });
						save_res_op(1, val);
					}
				},
				save_ex: {
					label: "<i class='icon-save'></i> " + btn_name2,
					className: 'btn-primary',
					callback: function () {
						bootbox.dialog({ message: "<h5 id='alert_msg'>" + alert_msg + "</h5>" });

						save_res_op(2, val);
					}
				},
				close: {
					label: "<i class='icon-off'></i> Close",
					className: 'btn-danger',
					callback: function () {
						console.log('Custom button clicked');

					}
				}
			}
		})

		setTimeout(function () { $("#new_list" + val + "").focus(); }, 500);
	}

	function save_res_op(typ, val) {
		$.post("pages/master_set_page_ajax.php",
			{
				rid: val,
				name: $("#new_list" + val + "").val(),
				type: 11
			},
			function (data, status) {
				var ndata = data.split("#koushik#");
				$("#alert_msg").text(ndata[1]);
				setTimeout(function () {
					bootbox.hideAll();

					$.post("pages/master_set_page_ajax.php",
						{
							rname: $("#r_name").val(),
							val: 5,
							type: 1
						},
						function (data, status) {
							$("#tab_data").html(data);
							if (typ == 1) {
								add_res_option(ndata[0]);
							}

						})

				}, 1000);
			})
	}

	function add_res_option(rid) {
		$.post("pages/master_set_page_ajax.php",
			{
				rid: rid,
				type: 12
			},
			function (data, status) {
				$("#results").html(data);
				$("#option").select2({ theme: "classic" });
				if (!$('#myModal').hasClass('in')) {
					$("#mod").click();
					$("#results").fadeIn(500);
				}
			})
	}

	function link_option() {
		$.post("pages/master_set_page_ajax.php",
			{
				rid: $("#list_id").val(),
				opt: $("#option").val(),
				type: 14
			},
			function (data, status) {
				add_res_option($("#list_id").val());

				$.post("pages/master_set_page_ajax.php",
					{
						rname: $("#r_name").val(),
						val: 5,
						type: 1
					},
					function (data, status) {
						$("#tab_data").html(data);
					})
			})
	}

	function save_new_option() {
		$.post("pages/master_set_page_ajax.php",
			{
				rid: $("#list_id").val(),
				opt: $("#new_option").val(),
				type: 15
			},
			function (data, status) {
				add_res_option($("#list_id").val());

				$.post("pages/master_set_page_ajax.php",
					{
						rname: $("#r_name").val(),
						val: 5,
						type: 1
					},
					function (data, status) {
						$("#tab_data").html(data);
					})
			})
	}

	function remove_option(opt) {
		$.post("pages/master_set_page_ajax.php",
			{
				rid: $("#list_id").val(),
				opt: opt,
				type: 16
			},
			function (data, status) {
				add_res_option($("#list_id").val());

				$.post("pages/master_set_page_ajax.php",
					{
						rname: $("#r_name").val(),
						val: 5,
						type: 1
					},
					function (data, status) {
						$("#tab_data").html(data);
					})
			})
	}


	function delete_res_op(val, name) {
		bootbox.dialog({
			message: "<h5>Do you really want to delete '" + name + "'?</h5>",
			size: 'large',
			buttons: {
				save: {
					label: "<i class='icon-ok'></i> Yes",
					className: 'btn-success',
					callback: function () {
						bootbox.dialog({ message: "<h5 id='alert_msg'>Deleting</h5>" });
						$.post("pages/master_set_page_ajax.php",
							{
								rid: val,
								type: 13
							},
							function (data, status) {
								$("#alert_msg").text(data);
								setTimeout(function () {
									bootbox.hideAll();
									$.post("pages/master_set_page_ajax.php",
										{
											rname: $("#r_name").val(),
											val: 5,
											type: 1
										},
										function (data, status) {
											$("#tab_data").html(data);
										})

								}, 1000);
							})
					}
				},
				close: {
					label: "<i class='icon-remove'></i> No",
					className: 'btn-danger',
					callback: function () {
						console.log('Custom button clicked');

					}
				}
			}
		})
	}

	function search_option(elem, e) {
		if (e.which == 13) {
			$.post("pages/master_set_page_ajax.php",
				{
					opname: $(elem).val(),
					val: 6,
					type: 1
				},
				function (data, status) {
					$("#tab_data").html(data);
				})
		}
	}

	function add_new_option_new(val, oname) {
		var title = "ADD NEW OPTION"
		var btn_name = "Save";
		var alert_msg = "Saving";

		if (val > 0) {
			title = "UPDATE OPTION: " + oname;
			btn_name = "Update";
			alert_msg = "Updating";
		}

		bootbox.dialog({
			title: title,
			message: "<p><input type='text' id='new_opt" + val + "' value='" + oname + "' style='width:90%' placeholder='Enter Option Name Here'/></p>",
			size: 'large',
			buttons: {
				save: {
					label: "<i class='icon-save'></i> " + btn_name,
					className: 'btn-success',
					callback: function () {
						bootbox.dialog({ message: "<h5 id='alert_msg'>" + alert_msg + "</h5>" });
						$.post("pages/master_set_page_ajax.php",
							{
								oid: val,
								name: $("#new_opt" + val + "").val(),
								type: 17
							},
							function (data, status) {
								$("#alert_msg").text(data);
								setTimeout(function () {
									bootbox.hideAll();

									$.post("pages/master_set_page_ajax.php",
										{
											opname: $("#op_name").val(),
											val: 6,
											type: 1
										},
										function (data, status) {
											$("#tab_data").html(data);
										})

								}, 1000);
							})
					}
				},
				close: {
					label: "<i class='icon-off'></i> Close",
					className: 'btn-danger',
					callback: function () {
						console.log('Custom button clicked');

					}
				}
			}
		})

		setTimeout(function () { $("#new_opt" + val + "").focus(); }, 500);
	}

	function delete_option_new(val, name) {
		bootbox.dialog({
			message: "<h5>Do you really want to delete '" + name + "'?</h5>",
			size: 'large',
			buttons: {
				save: {
					label: "<i class='icon-ok'></i> Yes",
					className: 'btn-success',
					callback: function () {
						bootbox.dialog({ message: "<h5 id='alert_msg'>Deleting</h5>" });
						$.post("pages/master_set_page_ajax.php",
							{
								oid: val,
								type: 18
							},
							function (data, status) {
								$("#alert_msg").text(data);
								setTimeout(function () {
									bootbox.hideAll();

									$.post("pages/master_set_page_ajax.php",
										{
											opname: $("#op_name").val(),
											val: 6,
											type: 1
										},
										function (data, status) {
											$("#tab_data").html(data);
										})

								}, 1000);
							})
					}
				},
				close: {
					label: "<i class='icon-remove'></i> No",
					className: 'btn-danger',
					callback: function () {
						console.log('Custom button clicked');

					}
				}
			}
		})
	}

	function load_antibio() {
		$.post("pages/master_set_page_ajax.php",
			{
				type: 19
			},
			function (data, status) {
				$("#list_bio").html(data);
				$("#anti_bio").val('');
			})
	}
	function save_antibio() {
		if ($("#anti_bio").val().trim() != '') {
			$.post("pages/master_set_page_ajax.php",
				{
					anti_bio_id: $("#anti_bio_id").val(),
					bio: $("#anti_bio").val(),
					type: 20
				},
				function (data, status) {
					alert("Saved");
					$("#anti_bio_id").val(0);
					load_antibio();
				})
		}
		else {
			$("#anti_bio").css({ 'border': '1px solid red' });
		}
	}
	function search_anti(val) {
		$.post("pages/master_set_page_ajax.php",
			{
				bio: val,
				type: 21
			},
			function (data, status) {
				$("#list_bio").html(data);
			})
	}
	function load_spec() {
		$.post("pages/master_set_page_ajax.php",
			{
				type: 22
			},
			function (data, status) {
				$("#list_spec").html(data);
				$("#spec").val('');
			})
	}
	function save_spec() {
		if ($("#spec").val().trim() != '') {
			$.post("pages/master_set_page_ajax.php",
				{
					spec_id: $("#spec_id").val(),
					spec: $("#spec").val(),
					type: 23
				},
				function (data, status) {
					alert("Saved");
					$("#spec_id").val(0);
					load_spec();
				})
		}
		else {
			$("#spec").css({ 'border': '1px solid red' });
		}
	}
	function load_org() {
		$.post("pages/master_set_page_ajax.php",
			{
				type: 24
			},
			function (data, status) {
				$("#list_org").html(data);
				$("#org").val('');
			})
	}
	function save_org() {
		if ($("#org").val().trim() != '') {
			$.post("pages/master_set_page_ajax.php",
				{
					org_id: $("#org_id").val(),
					org: $("#org").val(),
					type: 25
				},
				function (data, status) {
					alert("Saved");
					$("#org_id").val(0);
					load_org();
				})
		}
		else {
			$("#org").css({ 'border': '1px solid red' });
		}
	}
	function search_spec(val) {
		$.post("pages/master_set_page_ajax.php",
			{
				spec: val,
				type: 26
			},
			function (data, status) {
				$("#list_spec").html(data);
			})
	}
	function search_org(val) {
		$.post("pages/master_set_page_ajax.php",
			{
				org: val,
				type: 27
			},
			function (data, status) {
				$("#list_org").html(data);
			})
	}

	function add_editor() {
		if (CKEDITOR.instances['article-body']) {
			CKEDITOR.instances['article-body'].destroy(true);
		}
		CKEDITOR.replace('article-body');
		CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
		CKEDITOR.config.height = 300;


		// Remove text formatting while pasting
		CKEDITOR.config.extraPlugins = (CKEDITOR.config.extraPlugins ? CKEDITOR.config.extraPlugins + ',pastefromword' : 'pastefromword');
		CKEDITOR.config.pasteFromWordRemoveFontStyles = true;
		CKEDITOR.config.pasteFromWordRemoveStyles = true;

		// Optionally remove inline styles and classes
		CKEDITOR.config.forcePasteAsPlainText = true;

		CKEDITOR.config.removeFormatTags = 'b,strong,em,i,font,u,strike,sub,sup';
		CKEDITOR.config.removeFormatAttributes = 'style,span,class,width,height,align,hspace,valign';

		CKEDITOR.on('instanceReady', function (evt) {
			evt.editor.on('paste', function (event) {
				var data = event.data.dataValue;
				// Remove all HTML tags except <br>
				data = data.replace(/<(?!br\s*\/?)[^>]+>/gi, '');
				event.data.dataValue = data;
			});
		});
	}

	function load_normal(id, typ) {
		if (typ == "test") {
			$("#param_pad").val("0").trigger("change.select2");
		}
		else if (typ == "param") {
			//$("#testid").val("0").trigger("change.select2");
		}
		$.post("pages/master_set_page_ajax.php",
			{
				id: id,
				typ: typ,
				type: 28
			},
			function (data, status) {
				$("#rad_res").contents().find('body').html(data);

			})
	}


	function save_normal() {
		bootbox.dialog({ message: "<h5 id='alert_msg'>Saving</h5>" });
		$.post("pages/master_set_page_ajax.php",
			{
				testid: $("#testid").val(),
				param: $("#param_pad").val(),
				summ: $("#rad_res").contents().find('body').html(),
				type: 29
			},
			function (data, status) {
				$("#alert_msg").text("Saved");
				setTimeout(function () {
					bootbox.hideAll();
				}, 1000);
			})
	}

	function populateFields(testid, suffix) {
				$("#testid").val(testid).trigger('change');
				$("#suffix_text").val(suffix);
				$("#current_testid").val(testid);
				$("#original_suffix").val(suffix);
			}

			function save_suffix_name() {
				let testid = $("#testid").val();
				let suffix = $("#suffix_text").val();
				let original_suffix = $("#original_suffix").val();

				if (testid === "0") {
					alert("Please select a test.");
					return;
				}

				if (suffix !== original_suffix) {
					bootbox.dialog({ message: "<h5 id='alert_msg'>Saving</h5>" });
					$.post("pages/master_set_page_ajax.php",
						{
							testid: testid,
							suffix: suffix,
							type: "save_suffix"
						},
						function (data, status) {
							$("#alert_msg").text("Saved");
							setTimeout(function () {
								bootbox.hideAll();
							}, 1000);
							tab_data(9);
						}
					);
				} else {
					alert("No changes detected.");
				}
			}

			function delete_suffix(testid) {
				bootbox.confirm({
					message: "Are you sure you want to delete this entry?",
					buttons: {
						confirm: {
							label: 'Yes',
							className: 'btn-danger'
						},
						cancel: {
							label: 'No',
							className: 'btn-secondary'
						}
					},
					callback: function (result) {
						if (result) {
							$.post("pages/master_set_page_ajax.php",
								{
									testid: testid,
									type: "delete_suffix"
								},
								function (data, status) {
									alert(data);
									tab_data(9);
								}
							);
						}
					}
				});
			}

	function load_paramm(id, name) {
		$("#anti_bio_id").val(id);
		$("#anti_bio").val(name);
	}
	function load_specimenn(id, name) {
		$("#spec_id").val(id);
		$("#spec").val(name);
	}
	function load_organismm(id, name) {
		$("#org_id").val(id);
		$("#org").val(name);
	}


	
</script>
<script type="text/javascript" src="../ckeditor/ckeditor.js"></script>
<link href="../css/select2.min.css" rel="stylesheet" />
<script src="../js/select2.min.js"></script>
<style>
	.pspan {
		display: block;
		width: 150px;
		margin-left: -2px;
	}

	.tab_list_table {
		max-height: 400px;
		overflow: scroll;
		overflow-x: hidden
	}

	#myModal {
		left: 30%;
		width: 80%;
		height: 400px;
	}

	.select2-container--open {
		z-index: 9999999
	}

	.div_size {
		display: inline-block;
		margin-top: 0px;
		width: 430px;
		margin-left: -10px;
	}

	.div_size .btn {
		margin-bottom: 10px;
	}

	.cke_textarea_inline {
		padding: 10px;
		height: 380px;
		overflow: auto;
		border: 1px solid gray;
		-webkit-appearance: textfield;
	}
	
</style>