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
				<button type="submit" form="form-setting" id="button-save" class="btn btn-primary"><?php echo $button_save; ?></button>
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
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
		<div class="panel panel-default panel-nav-tabs">
			<div class="panel-heading">
				<div class="pull-right">
					<select onChange="location.href = this.value">
						<?php foreach ($stores as $store) { ?>
						<?php if ($store['store_id'] == $filter_store_id) { ?>
						<option value="<?php echo $store['filter']; ?>" selected="selected"><?php echo $store['name']; ?></option>
						<?php } else { ?>
						<option value="<?php echo $store['filter']; ?>"><?php echo $store['name']; ?></option>
						<?php } ?>
						<?php } ?>
					</select>
				</div>
				<ul class="nav nav-tabs" id="general-tabs">
					<li class="active"><a href="#tab-setting" data-toggle="tab"><?php echo $tab_setting; ?></a></li>
					<li><a href="#tab-support" data-toggle="tab">Support</a></li>
				</ul>
			</div>
			<div class="panel-body">
				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-setting" class="form-horizontal">
					<div class="tab-content">
						<div class="tab-pane active in" id="tab-setting">
							<div class="setting-name"><?php echo $caption_general; ?></div>
							<div class="form-group">
								<label class="col-sm-2 control-label"><?php echo $entry_module_status; ?></label>
								<div class="col-sm-10">
									<select name="<?php echo $module_name; ?>[status]" class="form-control">
										<?php if ($status == 1) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
										<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label"><?php echo $entry_name; ?></label>
								<div class="col-sm-10">
									<div class="input-group language-dropdown">
										<span class="input-group-addon"></span>
										<?php foreach ($languages as $language) { ?>
										<input type="text" name="<?php echo $module_name; ?>[name][<?php echo $language['language_id']; ?>]" value="<?php echo isset($name[$language['language_id']]) ? $name[$language['language_id']] : ''; ?>" data-language="<?php echo $language['language_id']; ?>" placeholder="<?php echo $entry_name; ?>" class="form-control" />
										<?php } ?>
										<div class="input-group-btn">
											<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span></button>
											<ul class="dropdown-menu dropdown-menu-right">
												<?php foreach ($languages as $language) { ?>
												<li><a data-language="<?php echo $language['language_id']; ?>" data-image="<?php echo $language['image']; ?>"><img src="<?php echo $language['image']; ?>" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
												<?php } ?>
											</ul>
										</div>
									</div>
									<?php if (isset($error['name'])) { ?>
									<div class="text-danger"><?php echo $error['name']; ?></div>
									<?php } ?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label s_help"><?php echo $entry_apikey; ?><i><?php echo $help_apikey; ?></i></label>
								<div class="col-sm-10">
									<input type="text" name="<?php echo $module_name; ?>[apikey]" value="<?php echo $apikey; ?>" placeholder="<?php echo $entry_apikey; ?>" class="form-control" />
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label s_help"><?php echo $entry_category; ?><i><?php echo $help_category; ?></i></label>
								<div class="col-sm-10">
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-search"></i></span>
										<input type="text" name="category" value="" placeholder="<?php echo $entry_category; ?>" class="form-control" />
									</div>
									<div id="category" class="well well-sm" style="height: 150px; overflow: auto;">
										<?php if ($categories) { ?>
										<?php foreach ($categories as $category) { ?>
										<div id="category<?php echo $category['category_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $category['name']; ?><input type="hidden" name="<?php echo $module_name; ?>[categories][]" value="<?php echo $category['category_id']; ?>" /></div>
										<?php } ?>
										<?php } ?>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label s_help"><?php echo $entry_notify_status; ?><i><?php echo $help_notify_status; ?></i></label>
								<div class="col-sm-10">
									<select name="<?php echo $module_name; ?>[notify_status]" class="form-control">
										<?php if ($notify_status == 1) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
										<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label"><?php echo $entry_tax_class; ?></label>
								<div class="col-sm-10">
									<select name="<?php echo $module_name; ?>[tax_class_id]" class="form-control">
										<option value="0"><?php echo $text_none; ?></option>
										<?php foreach ($tax_classes as $tax_class) { ?>
										<?php if ($tax_class['tax_class_id'] == $tax_class_id) { ?>
										<option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
										<?php } else { ?>
										<option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label"><?php echo $entry_geo_zone; ?></label>
								<div class="col-sm-10">
									<select name="<?php echo $module_name; ?>[geo_zone_id]" class="form-control">
										<option value="0"><?php echo $text_none; ?></option>
										<?php foreach ($geo_zones as $geo_zone) { ?>
										<?php if ($geo_zone['geo_zone_id'] == $geo_zone_id) { ?>
										<option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
										<?php } else { ?>
										<option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
										<?php } ?>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label"><?php echo $entry_sort_order; ?></label>
								<div class="col-sm-10">
									<input type="text" name="<?php echo $module_name; ?>[sort_order]" value="<?php echo $sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" />
								</div>
							</div>
							<div class="setting-name"><?php echo $caption_appearance; ?></div>
							<div class="form-group">
								<label class="col-sm-2 control-label"><?php echo $entry_cost_status; ?></label>
								<div class="col-sm-10">
									<select name="<?php echo $module_name; ?>[cost_status]" class="form-control">
										<?php if ($cost_status == 1) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
										<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label s_help"><?php echo $entry_distance_status; ?><i><?php echo $help_distance_status; ?></i></label>
								<div class="col-sm-7">
									<select name="<?php echo $module_name; ?>[distance_status]" class="form-control">
										<?php if ($distance_status == 1) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
										<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
										<?php } ?>
									</select>
								</div>
								<div class="col-sm-3">
									<select name="<?php echo $module_name; ?>[distance_unit]" class="form-control">
										<?php if ($distance_unit == 'k') { ?>
										<option value="k" selected="selected">km</option>
										<?php } else { ?>
										<option value="k">km</option>
										<?php } ?>
										<?php if ($distance_unit == 'm') { ?>
										<option value="m" selected="selected">miles</option>
										<?php } else { ?>
										<option value="m">miles</option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label s_help"><?php echo $entry_coordinate_status; ?><i><?php echo $help_coordinate_status; ?></i></label>
								<div class="col-sm-10">
									<select name="<?php echo $module_name; ?>[coordinate_status]" class="form-control">
										<?php if ($coordinate_status == 1) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
										<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label s_help"><?php echo $entry_filter_status; ?><i><?php echo $help_filter_status; ?></i></label>
								<div class="col-sm-10">
									<select name="<?php echo $module_name; ?>[filter_status]" class="form-control">
										<?php if ($filter_status == 1) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
										<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label s_help"><?php echo $entry_map_status; ?><i><?php echo $help_map_status; ?></i></label>
								<div class="col-sm-10">
									<div class="row">
										<div class="col-sm-6 col-md-4">
											<div class="form-group">
												<div class="col-sm-12">
													<select name="<?php echo $module_name; ?>[map_status]" class="form-control">
														<?php if ($map_status == 1) { ?>
														<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
														<option value="0"><?php echo $text_disabled; ?></option>
														<?php } else { ?>
														<option value="1"><?php echo $text_enabled; ?></option>
														<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
														<?php } ?>
													</select>
												</div>
											</div>
										</div>
										<div class="col-sm-6 col-md-4">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="input-group">
														<span class="input-group-addon"><i class="fa fa-arrows-h"></i></span>
														<input type="text" name="<?php echo $module_name; ?>[map_width]" value="<?php echo $map_width; ?>" placeholder="<?php echo $entry_map_width; ?>" class="form-control" />
													</div>
												</div>
											</div>
										</div>
										<div class="col-sm-12 col-md-4">
											<div class="form-group">
												<div class="col-sm-12">
													<div class="input-group">
														<span class="input-group-addon"><i class="fa fa-arrows-v"></i></span>
														<input type="text" name="<?php echo $module_name; ?>[map_height]" value="<?php echo $map_height; ?>" placeholder="<?php echo $entry_map_height; ?>" class="form-control" />
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label s_help"><?php echo $entry_pickup_list_status; ?><i><?php echo $help_pickup_list_status; ?></i></label>
								<div class="col-sm-10">
									<select name="<?php echo $module_name; ?>[pickup_list_status]" class="form-control">
										<?php if ($pickup_list_status == 1) { ?>
										<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
										<option value="0"><?php echo $text_disabled; ?></option>
										<?php } else { ?>
										<option value="1"><?php echo $text_enabled; ?></option>
										<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label s_help"><?php echo $entry_limit; ?><i><?php echo $help_limit; ?></i></label>
								<div class="col-sm-10">
									<input type="text" name="<?php echo $module_name; ?>[limit]" value="<?php echo $limit; ?>" placeholder="<?php echo $entry_limit; ?>" class="form-control" />
								</div>
							</div>
						</div>
						<div class="tab-pane" id="tab-support">
							<div class="row">
								<div class="col-sm-8 col-md-8">
									<div class="row">
										<div class="col-sm-8">
											<h4><b>You need help?</b></h4>
											<p>If you have any questions, idea or need help please feel free to contact us via ticket system</p>
										</div>
										<div class="col-sm-4 text-right">
											<a onclick="window.open('https://www.adikon.eu/login')" class="btn btn-warning btn-lg">Submit Ticket</a>
										</div>
									</div>
									<hr />
									<div class="row">
										<div class="col-sm-8">
											<h4><b>Need something customized?</b></h4>
											<p>Custom services, installations, custom theme integrations & updates and resolving conflicts with other third party extensions</p>
										</div>
										<div class="col-sm-4 text-right">
											<a onclick="window.open('http://www.adikon.eu/contact')" class="btn btn-info btn-lg">Get a Quote</a>
										</div>
									</div>
								</div>
								<div class="col-sm-4 col-md-4">
									<div class="panel-default">
										<div class="panel-body">
											<p><a onclick="window.open('http://www.adikon.eu')" class="btn-link">Official Website</a></p>
											<p><a onclick="window.open('http://www.opencart.com/index.php?route=marketplace/extension&filter_member=adikon')" class="btn-link">Our Modules</a></p>
											<p><a onclick="window.open('http://www.adikon.eu/support-i8/storepickup-map-google-maps-i39')" class="btn-link">Documentation</a></p>
										</div>
									</div>
								</div>
							</div>
							<script type="text/javascript">
							var mod_id = '7123';
							var domain = '<?php echo $bGljZW5zZV9kb21haW4; ?>';
							</script>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class="col-sm-12 text-center">
			<br />Adikon.eu, All Rights Reserved.
		</div>
	</div>
<script type="text/javascript"><!--
$('input[name="category"]').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/category/autocomplete&<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request) + '&format=raw',
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['category_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$(this).val('');

		$('#category' + item['value']).remove();

		$('#category').append('<div id="category' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="<?php echo $module_name; ?>[categories][]" value="' + item['value'] + '" /></div>');
	}
});

$('.well').delegate('.fa-minus-circle', 'click', function() {
	$(this).parent().remove();
});
//--></script>
<script type="text/javascript"><!--
$('.language-dropdown .dropdown-menu a').on('click', function(e) {
	e.preventDefault();

	$(this).parent().parent().find('li').removeClass('active');
	$(this).parent().addClass('active');

	var element = $(this).parents('.language-dropdown');

	element.find('span:first').html('<img src="' + $(this).data('image') + '" title="" />');
	element.find('input').hide();
	element.find('input[data-language="' + $(this).data('language') + '"]').css('display', 'table-cell');
});

$('.language-dropdown').each(function(index) {
	$(this).find('.dropdown-menu a:first').trigger('click');
});
//--></script>
<script type="text/javascript"><!--
<?php if ($filter_tab_show) { ?>
$('#general-tabs a[href="#tab-<?php echo $filter_tab_show; ?>"]').tab('show');
<?php } ?>

$('#button-save').on('click', function(e) {
	$('#form-setting').submit();
});
//--></script>
</div>
<?php echo $footer; ?>