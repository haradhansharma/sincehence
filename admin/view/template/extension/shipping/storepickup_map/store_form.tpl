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
				<button type="submit" form="form-store" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
				<?php if ($storepickup_id) { ?>
				<a href="<?php echo $delete; ?>" id="button-delete" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i></a>
				<?php } ?>
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>
			<h1><?php echo $heading_title; ?></h1>
		</div>
	</div>
	<div class="container-fluid">
		<?php if ($error_warning) { ?>
		<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
			</div>
			<div class="panel-body">
				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-store" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_name; ?></label>
						<div class="col-sm-10">
							<input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" class="form-control" />
							<?php if ($error_name) { ?>
							<div class="text-danger"><?php echo $error_name; ?></div>
							<?php } ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_email; ?></label>
						<div class="col-sm-10">
							<input type="text" name="email" value="<?php echo $email; ?>" placeholder="<?php echo $entry_email; ?>" class="form-control" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_address; ?></label>
						<div class="col-sm-10">
							<input type="text" name="address" value="<?php echo $address; ?>" placeholder="<?php echo $entry_address; ?>" class="form-control" />
							<?php if ($error_address) { ?>
							<div class="text-danger"><?php echo $error_address; ?></div>
							<?php } ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_city; ?></label>
						<div class="col-sm-10">
							<input type="text" name="city" value="<?php echo $city; ?>" placeholder="<?php echo $entry_city; ?>" class="form-control" />
							<?php if ($error_city) { ?>
							<div class="text-danger"><?php echo $error_city; ?></div>
							<?php } ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_country; ?></label>
						<div class="col-sm-10">
							<select name="country_id" class="form-control">
								<?php foreach ($countries as $country) { ?>
								<?php if ($country['country_id'] == $country_id) { ?>
								<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
								<?php } else { ?>
								<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
								<?php } ?>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_zone; ?></label>
						<div class="col-sm-10">
							<select name="zone_id" class="form-control"></select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_telephone; ?></label>
						<div class="col-sm-10">
							<input type="text" name="telephone" value="<?php echo $telephone; ?>" placeholder="<?php echo $entry_telephone; ?>" class="form-control" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_cost; ?></label>
						<div class="col-sm-10">
							<input type="text" name="cost" value="<?php echo $cost; ?>" placeholder="<?php echo $entry_cost; ?>" class="form-control" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_icon; ?></label>
						<div class="col-sm-10">
							<a id="thumb-icon" data-toggle="image" class="img-thumbnail" <?php if (version_compare(VERSION, '2.0', '<')) { ?>onclick="image_upload('input-icon', 'thumb-icon');<?php } ?>"><img src="<?php echo $thumb ? $thumb : $placeholder; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" style="max-width:40px;" /></a>
							<input type="hidden" name="icon" value="<?php echo $icon; ?>" id="input-icon" />
							<?php if (version_compare(VERSION, '2.0', '<')) { ?><a onclick="$('#thumb-icon img').attr('src', '<?php echo $placeholder; ?>'); $('#input-icon').attr('value', '');">Clear</a><?php } ?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_store; ?></label>
						<div class="col-sm-10">
							<select name="store_id" class="form-control">
								<?php foreach ($stores as $store) { ?>
								<?php if ($store['store_id'] == $store_id) { ?>
								<option value="<?php echo $store['store_id']; ?>" selected="selected"><?php echo $store['name']; ?></option>
								<?php } else { ?>
								<option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
								<?php } ?>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_sort_order; ?></label>
						<div class="col-sm-10">
							<input type="text" name="sort_order" value="<?php echo $sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" class="form-control" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_status; ?></label>
						<div class="col-sm-10">
							<select name="status" class="form-control">
								<?php if ($status) { ?>
								<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
								<option value="0"><?php echo $text_disabled; ?></option>
								<?php } else { ?>
								<option value="1"><?php echo $text_enabled; ?></option>
								<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					////////
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_central; ?></label>
						<div class="col-sm-10">
							<select name="central" class="form-control">
								<?php if ($central) { ?>
								<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
								<option value="0"><?php echo $text_disabled; ?></option>
								<?php } else { ?>
								<option value="1"><?php echo $text_enabled; ?></option>
								<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					////////
					<div class="form-group">
						<label class="col-sm-2 control-label"><?php echo $entry_coordinate; ?></label>
						<div class="col-sm-4">
							<div class="input-group">
								<span class="input-group-addon"><?php echo $text_latitude; ?></span>
								<input type="text" name="latitude" value="<?php echo $latitude; ?>" placeholder="<?php echo $text_latitude; ?>" class="form-control" />
							</div>
						</div>
						<div class="col-sm-4">
							<div class="input-group">
								<span class="input-group-addon"><?php echo $text_longitude; ?></span>
								<input type="text" name="longitude" value="<?php echo $longitude; ?>" placeholder="<?php echo $text_longitude; ?>" class="form-control" />
							</div>
						</div>
						<div class="col-sm-2">
							<a id="button-coordinate" class="btn btn-warning"><?php echo $button_coordinate; ?></a>
						</div>
					</div>
					<div class="googlemaps" id="googlemaps"></div>
				</form>
			</div>
		</div>
		<div class="col-sm-12 text-center">
			<br />Adikon.eu, All Rights Reserved.
		</div>
	</div>
<style>
.googlemaps { margin-top:10px; position:relative; width:100%; max-width:100%; height:350px; }
</style>
<script type="text/javascript"><!--
$(document).delegate('#button-delete', 'click', function(e) {
	e.stopPropagation();

	if (confirm('<?php echo $text_confirm; ?>')) {
		return true;
	}

	return false;
});
//--></script>
<script type="text/javascript"><!--
function getMap(latitude, longitude) {
	var latlng = new google.maps.LatLng(latitude, longitude);

	var myOptions = {
		zoom: 9,
		center: latlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		streetViewControl: false
	};

	var map = new google.maps.Map(document.getElementById('googlemaps'), myOptions);

	if (latitude && longitude) {
		map.setZoom(12);
		map.setCenter(latlng);

		var marker = new google.maps.Marker({map: map, position: latlng});
	} else {
		map.setZoom(3);
	}
}

getMap('<?php echo $latitude; ?>', '<?php echo $longitude; ?>');

$(document).delegate('#button-coordinate', 'click', function(e) {
	address = $('select[name="country_id"] option:selected').text() + ' ' +  $('select[name="zone_id"] option:selected').text() + ' ' + $('input[name="city"]').val() + ' ' + $('input[name="address"]').val();

	geocoder = new google.maps.Geocoder();

	geocoder.geocode({'address': address, 'region': ''}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {
			$('input[name="latitude"]').val(results[0].geometry.location.lat());
			$('input[name="longitude"]').val(results[0].geometry.location.lng());

			getMap(results[0].geometry.location.lat(), results[0].geometry.location.lng());
		} else {
			alert('Geocoder failed to retrieve address: ' + status);
		}
	});

	return false;
});

$('select[name="country_id"]').on('change', function() {
	$.ajax({
		url: 'index.php?route=<?php echo $module_path; ?>/store/country&<?php echo $token; ?>&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name="country_id"]').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
		},
		complete: function() {
			$('.fa-spin').remove();
		},
		success: function(json) {
			$('.fa-spin').remove();

			html = '';

			if (typeof json['zone'] != 'undefined') {
				for (i = 0; i < json['zone'].length; i++) {
					html += '<option value="' + json['zone'][i]['zone_id'] + '"';

					if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
						html += ' selected="selected"';
					}

					html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
			}

			$('select[name="zone_id"]').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('select[name="country_id"]').trigger('change');
//--></script>
<?php if (version_compare(VERSION, '2.0', '<')) { ?>
<script type="text/javascript"><!--
function image_upload(field, thumb) {
	$('#dialog').remove();

	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/filemanager&<?php echo $token_name; ?>=<?php echo $token_value; ?>&field=' + encodeURIComponent(field) + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');

	$('#dialog').dialog({
		title: 'Image Manager',
		close: function (event, ui) {
			if ($('#' + field).attr('value')) {
				$.ajax({
					url: 'index.php?route=common/filemanager/image&<?php echo $token; ?>&image=' + encodeURIComponent($('#' + field).val()),
					dataType: 'text',
					success: function(data) {
						$('#' + thumb + ' img').replaceWith('<img src="' + data + '" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" />');
					}
				});
			}
		},
		bgiframe: false,
		width: 800,
		height: 400,
		resizable: false,
		modal: false
	});
};
//--></script>
<?php } ?>
</div>
<?php echo $footer; ?>