<?php
/*
  StorePickup Map
  Premium Extension
  
  Copyright (c) 2013 - 2019 Adikon.eu
  http://www.adikon.eu/
  
  You may not copy or reuse code within this file without written permission.
*/
?>
<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
				<a href="<?php echo $export; ?>" data-toggle="tooltip" title="<?php echo $button_export; ?>" class="btn btn-info"><i class="fa fa-download"></i></a>
				<button type="button" id="button-import" data-loading-text="<?php echo $text_loading; ?>" data-toggle="tooltip" title="<?php echo $button_import; ?>" class="btn btn-info"><i class="fa fa-upload"></i></button>
			</div>
			<?php if ($links) { ?>
			<div class="btn-group manage-link">
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></button>
				<ul class="dropdown-menu">
					<?php foreach ($links as $manage_link) { ?>
					<?php if ($manage_link['name']) { ?>
					<li><a href="<?php echo $manage_link['href']; ?>"><?php echo $manage_link['name']; ?></a></li>
					<?php } else { ?>
					<li class="divider"></li>
					<?php } ?>
					<?php } ?>
				</ul>
			</div>
			<?php } ?>
			<h1><?php echo $heading_title; ?></h1>
		</div>
	</div>
	<div class="container-fluid">
		<?php if ($error_warning) { ?>
		<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<?php if ($success) { ?>
		<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<?php if ($process) { ?>
		<div class="alert alert-info"><i class="fa fa-check-circle"></i> <?php echo $process; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="btn-group pull-right">
					<button type="button" data-toggle="modal" data-target="#storeFilterModal" class="btn btn-warning btn-sm"><span data-toggle="tooltip" title="<?php echo $button_filter; ?>"><i class="fa fa-search fa-fw"></i></span></button>
					<button type="button" data-toggle="tooltip" title="<?php echo $button_reset; ?>" class="btn btn-default btn-sm" onclick="location = 'index.php?route=<?php echo $module_path; ?>/store&<?php echo $token; ?>';"><i class="fa fa-times fa-fw"></i></button>
				</div>
				<h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
			</div>
			<div class="panel-body">
				<form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-store" class="form-horizontal">
					<div class="table-responsive">
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
									<td style="width: 1px;" class="text-center"><button type="button" form="form-store" formaction="<?php echo $delete; ?>" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger btn-xs" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-store').submit() : false;"><i class="fa fa-trash-o"></i></button></td>
									<td style="width: 20px;" class="text-center"><a href="<?php echo $sort_id; ?>" class="<?php echo ($sort == 's.storepickup_id') ? strtolower($order) : ''; ?>"><?php echo $column_id; ?></a></td>
									<td class="text-left"><a href="<?php echo $sort_name; ?>" class="<?php echo ($sort == 's.name') ? strtolower($order) : ''; ?>"><?php echo $column_name; ?></a></td>
									<td class="text-left"><?php echo $column_address; ?></td>
									<td class="text-left"><a href="<?php echo $sort_city; ?>" class="<?php echo ($sort == 's.city') ? strtolower($order) : ''; ?>"><?php echo $column_city; ?></a></td>
									<td class="text-right"><a href="<?php echo $sort_cost; ?>" class="<?php echo ($sort == 's.cost') ? strtolower($order) : ''; ?>"><?php echo $column_cost; ?></a></td>
									<td class="text-right"><a href="<?php echo $sort_sort_order; ?>" class="<?php echo ($sort == 's.sort_order') ? strtolower($order) : ''; ?>"><?php echo $column_sort_order; ?></a></td>
									<td class="text-left"><a href="<?php echo $sort_status; ?>" class="<?php echo ($sort == 's.status') ? strtolower($order) : ''; ?>"><?php echo $column_status; ?></a><a id="button-status" class="text-danger pull-right">[<?php echo $button_save; ?>]</i></a></td>
									///////////      
                                    <td class="text-left"><a href="<?php echo $sort_central; ?>" class="<?php echo ($sort == 's.central') ? strtolower($order) : ''; ?>"><?php echo $column_central; ?></a><a id="button-central" class="text-danger pull-right">[<?php echo $button_save; ?>]</i></a></td>
                                     //////////
									<td class="text-right"><a href="<?php echo $sort_date_added; ?>" class="<?php echo ($sort == 's.date_added') ? strtolower($order) : ''; ?>"><?php echo $column_date_added; ?></a></td>
									<td class="text-right"></td>
								</tr>
							</thead>
							<tbody>
								<?php if ($locations) { ?>
								<?php foreach ($locations as $location) { ?>
								<tr data-store-id="<?php echo $location['storepickup_id']; ?>">
									<td class="text-center"><input type="checkbox" name="selected[]" value="<?php echo $location['storepickup_id']; ?>" /></td>
									<td class="text-center"><?php echo $location['storepickup_id']; ?></td>
									<td class="text-left"><?php echo $location['name']; ?></td>
									<td class="text-left"><?php echo $location['address']; ?></td>
									<td class="text-left"><?php echo $location['city']; ?></td>
									<td class="text-right"><?php echo $location['cost']; ?></td>
									<td class="text-right"><?php echo $location['sort_order']; ?></td>
									<td class="text-left"><select name="status[<?php echo $location['storepickup_id']; ?>]" class="form-control">
										<?php if ($location['status'] == '1') { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
										<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
										<?php } ?>
									</select></td>
									///////
									<td class="text-left"><select name="central[<?php echo $location['storepickup_id']; ?>]" class="form-control">
										<?php if ($location['central'] == '1') { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
										<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
										<?php } ?>
									</select></td>
									//////
									<td class="text-right"><?php echo $location['date_added']; ?></td>
									<td class="text-right"><a href="<?php echo $location['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
								</tr>
								<?php } ?>
								<?php } else { ?>
								<tr>
									<td class="text-center" colspan="10"><?php echo $text_no_results; ?></td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</form>
				<div class="row">
					<div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
					<div class="col-sm-6 text-right"><?php echo $results; ?></div>
				</div>
				<div class="modal fade" id="storeFilterModal" tabindex="-1" role="dialog">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-body">
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<label class="control-label"><?php echo $entry_name; ?></label>
											<input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" class="form-control" />
										</div>
										<div class="form-group">
											<label class="control-label"><?php echo $entry_address; ?></label>
											<input type="text" name="filter_address" value="<?php echo $filter_address; ?>" placeholder="<?php echo $entry_address; ?>" class="form-control" />
										</div>
										<div class="form-group">
											<label class="control-label"><?php echo $entry_country; ?></label>
											<select name="filter_country_id" class="form-control">
												<option value="*"></option>
												<?php foreach ($countries as $country) { ?>
												<?php if ($country['country_id'] == $filter_country_id) { ?>
												<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
												<?php } else { ?>
												<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
												<?php } ?>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<label class="control-label"><?php echo $entry_store; ?></label>
											<select name="filter_store_id" class="form-control">
												<option value="*"></option>
												<?php foreach ($stores as $store) { ?>
												<?php if ($store['store_id'] == $filter_store_id) { ?>
												<option value="<?php echo $store['store_id']; ?>" selected="selected"><?php echo $store['name']; ?></option>
												<?php } else { ?>
												<option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
												<?php } ?>
												<?php } ?>
											</select>
										</div>
										<div class="form-group">
											<label class="control-label"><?php echo $entry_date_added; ?></label>
											<div class="input-group date">
												<input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="<?php echo $entry_date_added; ?>" data-date-format="YYYY-MM-DD" class="form-control" />
												<span class="input-group-btn">
													<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
												</span>
											</div>
										</div>
										<div class="form-group">
											<label class="control-label"><?php echo $entry_status; ?></label>
											<select name="filter_status" class="form-control">
												<option value="*"></option>
												<?php if ($filter_status == '1') { ?>
												<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
												<?php } else { ?>
												<option value="1"><?php echo $text_enabled; ?></option>
												<?php } ?>
												<?php if ($filter_status == '0') { ?>
												<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
												<?php } else { ?>
												<option value="0"><?php echo $text_disabled; ?></option>
												<?php } ?>
											</select>
										</div>
										///////
										<div class="form-group">
											<label class="control-label"><?php echo $entry_central; ?></label>
											<select name="filter_central" class="form-control">
												<option value="*"></option>
												<?php if ($filter_central == '1') { ?>
												<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
												<?php } else { ?>
												<option value="1"><?php echo $text_enabled; ?></option>
												<?php } ?>
												<?php if ($filter_central == '0') { ?>
												<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
												<?php } else { ?>
												<option value="0"><?php echo $text_disabled; ?></option>
												<?php } ?>
											</select>
										</div>
										//////
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn btn-link" data-dismiss="modal"><?php echo $button_close; ?></button>
								<button type="button" id="button-filter" class="btn btn-primary"><?php echo $button_filter; ?></button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-12 text-center">
			<br />Adikon.eu, All Rights Reserved.
		</div>
	</div>
<script type="text/javascript"><!--
function getFilter(id) {
	filter_param = [];

	$(id + ' :input').each(function() {
		var $name = $(this).attr('name') + '';
		var $value = $(this).val();

		if ($name.indexOf('filter_') !== -1) {
			if (this.type == 'select-one') {
				if ($value != '*') {
					filter_param.push($name + '=' + encodeURIComponent($value));
				}
			} else if (this.type == 'checkbox' || this.type == 'radio') {
				if ($(this).is(':checked')) {
					filter_param.push($name + '=' + encodeURIComponent($value));
				}
			} else {
				if ($value) {
					filter_param.push($name + '=' + encodeURIComponent($value));
				}
			}
		}
	});

	return filter_param.join('&');
}

$('#button-filter').on('click', function() {
	filter = getFilter('#storeFilterModal');

	location = 'index.php?route=<?php echo $module_path; ?>/store&<?php echo $token; ?>&' + filter;
});
//--></script>
<script type="text/javascript"><!--
$(document).delegate('#button-status', 'click', function() {
	$.ajax({
		url: 'index.php?route=<?php echo $module_path; ?>/store/status&<?php echo $token; ?>&format=raw',
		type: 'post',
		dataType: 'json',
		data: $('#form-store select'),
		beforeSend: function() {
			$('#button-status').html('<i class="fa fa-spinner fa-spin"></i>');
		},
		complete: function() {
			$('#button-status').html('[<?php echo $button_save; ?>]');
		},
		success: function(json) {
			$('.alert').remove();

			if (json['error']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
			}

			if (json['success']) {
				$('#content > .container-fluid').prepend('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$(document).delegate('#button-process', 'click', function() {
	var skip = prompt("<?php echo $text_skip; ?>", "0");

	if (skip != null) {
		$(this).html('<i class="fa fa-spinner fa-spin"></i>');

		$.ajax({
			url: 'index.php?route=<?php echo $module_path; ?>/store/import&<?php echo $token; ?>&skip=' + skip + '&format=raw',
			type: 'post',
			dataType: 'json',
			success: function(json) {
				$('.alert-info').remove();

				if (json['error']) {
					alert(json['error']);
				}

				if (json['success']) {
					alert(json['success']);
				}
			}
		});
	}
});
//--></script>
<script type="text/javascript"><!--
$('#button-import').on('click', function() {
	var skip = prompt("<?php echo $text_skip; ?>", "0");

	if (skip != null) {
		$('#form-import').remove();

		$('body').prepend('<form enctype="multipart/form-data" id="form-import" style="display: none;"><input type="file" name="file" /></form>');

		$('#form-import input[name=\'file\']').trigger('click');

		if (typeof timer != 'undefined') {
			clearInterval(timer);
		}

		timer = setInterval(function() {
			if ($('#form-import input[name=\'file\']').val() != '') {
				clearInterval(timer);

				$.ajax({
					url: 'index.php?route=<?php echo $module_path; ?>/store/import&<?php echo $token; ?>&reset=1&skip=' + skip,
					type: 'post',
					dataType: 'json',
					data: new FormData($('#form-import')[0]),
					cache: false,
					contentType: false,
					processData: false,
					beforeSend: function() {
						$('#button-import').button('loading');
					},
					complete: function() {
						$('#button-import').button('reset');
					},
					success: function(json) {
						$('.alert').remove();

						if (json['error']) {
							alert(json['error']);
						}

						if (json['success']) {
							filter = getFilter('#storeFilterModal');

							location = 'index.php?route=<?php echo $module_path; ?>/store&<?php echo $token; ?>&' + filter;
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			}
		}, 500);
	}
});
//--></script>
<script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script>
</div>
<?php echo $footer; ?>