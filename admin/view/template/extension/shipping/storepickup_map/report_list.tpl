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
		<div class="row">
			<div class="col-md-3 col-md-push-9 col-sm-12 hidden-sm hidden-xs">
				<div class="panel panel-default" id="report-filter">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-calendar"></i> <?php echo $text_filter; ?></h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label class="control-label"><?php echo $entry_name; ?></label>
							<input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" class="form-control" />
						</div>
						<div class="form-group">
							<label class="control-label"><?php echo $entry_date_start; ?></label>
							<div class="input-group date">
								<input type="text" name="filter_date_start" value="<?php echo $filter_date_start; ?>" placeholder="<?php echo $entry_date_start; ?>" data-date-format="YYYY-MM-DD" class="form-control" />
								<span class="input-group-btn">
									<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
								</span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label"><?php echo $entry_date_end; ?></label>
							<div class="input-group date">
								<input type="text" name="filter_date_end" value="<?php echo $filter_date_end; ?>" placeholder="<?php echo $entry_date_end; ?>" data-date-format="YYYY-MM-DD" class="form-control" />
								<span class="input-group-btn">
									<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
								</span>
							</div>
						</div>
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
						<div class="form-group text-right">
							<button type="button" id="button-filter" class="btn btn-primary"><?php echo $button_filter; ?></button>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-9 col-md-pull-3 col-sm-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-4 col-md-4 col-sm-4">
								<div class="well well-sm"><b><?php echo $text_store; ?> (<?php echo $store_total; ?>)</b></div>
							</div>
							<div class="col-lg-4 col-md-4 col-sm-4">
								<div class="well well-sm"><i class="fa fa-shopping-cart text-info"></i> <span class="text-info"><b><?php echo $text_order; ?></b></span> <b><?php echo $order_total; ?></b></div>
							</div>
							<div class="col-lg-4 col-md-4 col-sm-4">
								<div class="well well-sm"><i class="fa fa-money text-info"></i> <span class="text-info"><b><?php echo $text_total; ?></b></span> <b><?php echo $total; ?></b></div>
							</div>
						</div>
						<div id="general"></div>
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
	filter = getFilter('#report-filter');

	location = 'index.php?route=<?php echo $module_path; ?>/report&<?php echo $token; ?>&' + filter;
});
//--></script>
<script type="text/javascript"><!--
filter = getFilter('#report-filter');

$('#general').delegate('.pagination a, .links a', 'click', function(e) {
	e.preventDefault();

	$('#general').load(this.href);
});

$('#general').load('index.php?route=<?php echo $module_path; ?>/report/store&<?php echo $token; ?>&format=raw&' + filter);
//--></script>
<script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script>
</div>
<?php echo $footer; ?>