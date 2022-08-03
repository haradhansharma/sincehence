<?php
/*
  StorePickup Map
  Premium Extension
  
  Copyright (c) 2013 - 2019 Adikon.eu
  http://www.adikon.eu/
  
  You may not copy or reuse code within this file without written permission.
*/
?>
<script type="text/javascript"><!--
<?php if (version_compare(VERSION, '2.0', '<')) { ?>
$('select[name^="spm_"]').live('change', function(e) {
<?php } else { ?>
$(document).delegate('select[name^="spm_"]', 'change', function(e) {
<?php } ?>
	$('.spm_alert').remove();

	select_type = $(this).data('select-type');

	if (select_type == 'country' || select_type == 'zone') {
		if (this.value == 0 || this.value == '') {
			if (select_type == 'country') {
				$('select[name="spm_zone_id"] option:not(:first)').remove().end();
			}

			$('select[name="spm_city"] option:not(:first)').remove().end();

			getStores();
		} else {
			$.ajax({
				url: 'index.php?route=<?php echo $module_path; ?>/filter&filter_' + select_type + '=' + encodeURIComponent(this.value),
				dataType: 'json',
				beforeSend: function() {
					<?php if (version_compare(VERSION, '2.0', '<')) { ?>
					$('#spm_filter').after(' <span class="spm_wait"><img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
					<?php  } else { ?>
					$('#spm_filter').after(' <span class="spm_wait"><i class="fa fa-circle-o-notch fa-spin"></i></span>');
					<?php } ?>
				},
				complete: function() {
					$('.spm_wait').remove();
				},
				success: function(json) {
					$('.spm_wait').remove();

					html = '';

					if (select_type == 'country') {
						html += '<option value=""><?php echo $text_select_zone; ?></option>';
					}

					if (select_type == 'zone') {
						html += '<option value=""><?php echo $text_select_city; ?></option>';
					}

					if (json['item'] && json['item'] != '') {
						for (i = 0; i < json['item'].length; i++) {
							html += '<option value="' + json['item'][i]['id'] + '">' + json['item'][i]['name'] + '</option>';
						}
					} else {
						html += '<option value="0"><?php echo $text_none; ?></option>';
					}

					if (select_type == 'country') {
						$('select[name="spm_zone_id"]').html(html);

						$('select[name="spm_city"] option:not(:first)').remove().end();
					}

					if (select_type == 'zone') {
						$('select[name="spm_city"]').html(html);
					}

					getStores();
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	} else if (select_type == 'city') {
		getStores();
	}
});

var $storexhr;

function getStores() {
	if ($storexhr != null){ 
		$storexhr.abort();
		$storexhr = null;
	}

	$storexhr = $.ajax({
		url: 'index.php?route=<?php echo $module_path; ?>/stores&filter_country=' + ($('select[name="spm_country_id"]').length > 0 ? encodeURIComponent($('select[name="spm_country_id"] option:selected').val()) : '') + '&filter_zone=' + encodeURIComponent($('select[name="spm_zone_id"] option:selected').val()) + '&filter_city=' + encodeURIComponent($('select[name="spm_city"] option:selected').val()),
		dataType: 'json',
		beforeSend: function() {
			<?php if (version_compare(VERSION, '2.0', '<')) { ?>
			$('#spm_filter').after(' <span class="spm_wait"><img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');

			$('input[value^="storepickup_map"]').parents('tr').remove();
			<?php  } else { ?>
			$('#spm_filter').after(' <span class="spm_wait"><i class="fa fa-circle-o-notch fa-spin"></i></span>');

			$('input[value^="storepickup_map"]').parents('.radio').remove();
			$('input[value^="storepickup_map"]').parents('.ve-radio').remove();//Ajax Quick Checkout Fix
			<?php } ?>
		},
		complete: function() {
			$('.spm_wait').remove();
		},
		success: function(json) {
			$('.spm_wait').remove();

			html = '';

			if (json['store'] && json['store'] != '') {
				for (i = 0; i < json['store'].length; i++) {
					<?php if (version_compare(VERSION, '2.0', '<')) { ?>
					html += '<tr class="highlight">';
					html += '  <td>';

					if (i == 0) {
						html += '<input type="radio" name="shipping_method" value="' + json['store'][i]['code'] + '" id="' + json['store'][i]['code'] + '" checked="checked" />';
					} else {
						html += '<input type="radio" name="shipping_method" value="' + json['store'][i]['code'] + '" id="' + json['store'][i]['code'] + '" />';
					}

					html += '  </td>';
					html += '  <td><label for="' + json['store'][i]['code'] + '">' + json['store'][i]['title'] + '</label></td>';
					html += '  <td style="text-align: right;"><label for="' + json['store'][i]['code'] + '">';

					<?php if ($cost_status) { ?>
					html += json['store'][i]['cost'];
					<?php } ?>

					html += '  </label></td>';
					html += '</tr>';
					<?php  } else { ?>
					html += '<div class="radio">';
					html += '  <label>';

					if (i == 0) {
						html += '<input type="radio" name="shipping_method" value="' + json['store'][i]['code'] + '" id="' + json['store'][i]['code'] + '" checked="checked" /> ' + json['store'][i]['title'];
					} else {
						html += '<input type="radio" name="shipping_method" value="' + json['store'][i]['code'] + '" id="' + json['store'][i]['code'] + '" /> ' + json['store'][i]['title'];
					}

					<?php if ($cost_status) { ?>
					html += ' - ' + json['store'][i]['cost'];
					<?php } ?>

					html += '  </label>';
					html += '</div>';
					<?php } ?>
				}
			} else {
				<?php if (version_compare(VERSION, '2.0', '<')) { ?>
				html += '<tr class="highlight">';
				html += '  <td colspan="3">';
				html += '    <div class="warning spm_alert">' + json['error'] + '</div>';
				html += '  </td>';
				html += '</tr>';
				<?php  } else { ?>
				html += '<div class="alert alert-danger spm_alert">' + json['error'] + '</div>';
				<?php  } ?>
			}

			<?php if (version_compare(VERSION, '2.0', '<')) { ?>
			$('#spm_filter').closest('tr').after(html);
			<?php  } else { ?>
			if ($('#spm_filter').closest('p').length === 0) {
				$('#spm_filter').after(html);
			} else {
				$('#spm_filter').closest('p').after(html);
			}
			<?php  } ?>

			$('input[name="shipping_method"][value^="storepickup_map"]').first().trigger('click');
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
}
//--></script>
<?php if (version_compare(VERSION, '2.0', '<')) { ?>
<style>
.col-sm-3, .col-sm-5 { margin-top: 7px; margin-bottom: 5px; display: inline-block; }
</style>
<?php  } ?>
<?php if ($map_status) { ?>
<link rel="stylesheet" href="catalog/view/javascript/vendors/storepickup_map/colorbox/colorbox.css" />
<script src="catalog/view/javascript/vendors/storepickup_map/colorbox/jquery.colorbox.js"></script>
<script type="text/javascript"><!--
$(document).ready(function() {
	<?php if (version_compare(VERSION, '2.0', '<')) { ?>
	$('a#spm_linkmap').live('click', function(e) {
	<?php } else { ?>
	$(document).delegate('a#spm_linkmap', 'click', function(e) {
	<?php } ?>
		e.preventDefault();

		if (typeof($.colorbox) == 'function') {
			$('a#spm_linkmap').colorbox({
					html: '<div id="spm_map_canvas" style="position:relative;min-width:200px;width:100%;height:100%;"></div>',
					scrolling: false,
					open: true,
					width: "<?php echo $map_width; ?>",
					height: "<?php echo $map_width; ?>",
					onComplete: function(){ spm_loadScriptGeneral(); }
			});
		}

		return false;
	});

	<?php if (version_compare(VERSION, '2.0', '<')) { ?>
	$('a[id^="spm_pickup"]').live('click', function() {
	<?php } else { ?>
	$(document).delegate('a[id^="spm_pickup"]', 'click', function() {
	<?php } ?>
		if ($(this).data('pickup-code').length > 0 && $(this).data('pickup-title').length > 0) {
			<?php if (version_compare(VERSION, '2.0', '<')) { ?>
			$('input[name="shipping_method"][value^="storepickup_map"]').parents('tr').remove();

			html = '<tr class="highlight">';
			html += '  <td>';
			html += '    <input type="radio" name="shipping_method" value="' + $(this).data('pickup-code') + '" id="' + $(this).data('pickup-code') + '" data-refresh="" checked="checked" />';
			html += '  </td>';
			html += '  <td><label for="' + $(this).data('pickup-code') + '">' + $(this).data('pickup-title') + '</label></td>';
			html += '  <td style="text-align: right;"><label for="' + $(this).data('pickup-code') + '">';

			<?php if ($cost_status) { ?>
			html += $(this).data('pickup-cost');
			<?php } ?>

			html += '  </label></td>';
			html += '</tr>';

			$('#spm_linkmap').closest('tr').after(html);
			<?php } else { ?>
			$('input[name="shipping_method"][value^="storepickup_map"]').parents('.radio').remove();
			$('input[name="shipping_method"][value^="storepickup_map"]').parents('.ve-radio').remove();//Ajax Quick Checkout Fix

			html = '<div class="radio">';
			html += '  <label>';
			html += '    <input type="radio" name="shipping_method" value="' + $(this).data('pickup-code') + '" id="' + $(this).data('pickup-code') + '" data-refresh="" checked="checked" /> ' + $(this).data('pickup-title');

			<?php if ($cost_status) { ?>
			html += ' - ' + $(this).data('pickup-cost');
			<?php } ?>

			html += '  </label>';
			html += '</div>';

			if ($('#spm_filter').length > 0 && $('#spm_filter').closest('p').length === 0) {
				$('#spm_filter').after(html);
			} else {
				$('#spm_linkmap').closest('p').after(html);
			}
			<?php } ?>

			$.colorbox.close();

			$('input[name="shipping_method"][value="' + $(this).data('pickup-code') + '"]').trigger('click');
		}
	});
});

function spm_loadScriptGeneral() {
	if (typeof google === 'object' && typeof google.maps === 'object') {
		spm_initializeGeneral()
	} else {
		var script = document.createElement("script");
		script.type = "text/javascript";
		script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + "maps.googleapis.com/maps/api/js?key=<?php echo $apikey; ?>&callback=spm_initializeGeneral";
		document.body.appendChild(script);
	}
}

function spm_initializeGeneral() {
	<?php if ($stores) { ?>
	<?php $first_store = reset($stores); ?>
	var spm_map = new google.maps.Map(document.getElementById("spm_map_canvas"), {
		center: new google.maps.LatLng(<?php echo $first_store['customer_latitude'] > 0 ? $first_store['customer_latitude'] : $first_store['latitude']; ?>, <?php echo $first_store['customer_latitude'] > 0 ? $first_store['customer_longitude'] : $first_store['longitude']; ?>),
		zoom: 8,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	});

	var spm_infoWindow = new google.maps.InfoWindow;

	<?php foreach ($stores as $store) { ?>
	var spm_point = new google.maps.LatLng(<?php echo $store['latitude']; ?>, <?php echo $store['longitude']; ?>);
	var spm_html = "<b><?php echo $store['name']; ?></b><br/><?php echo $store['address']; ?> <?php echo $store['city']; ?><br/><?php echo $store['zone']; ?><br/><?php echo $store['country']; ?><br/><?php echo $store['telephone']; ?><br />[ <a id=\"spm_pickup<?php echo $store['storepickup_id']; ?>\" data-pickup-code=\"<?php echo $store['code']; ?>\" data-pickup-title=\"<?php echo $store['title']; ?>\" data-pickup-cost=\"<?php echo $store['cost']; ?>\"><?php echo $text_choose; ?></a> ]";
	var spm_marker = new google.maps.Marker({
		map: spm_map,
		position: spm_point,
		icon: "<?php echo $store['icon']; ?>",
		title: "<?php echo $store['name']; ?>"
	});

	spm_bindInfoWindow(spm_marker, spm_map, spm_infoWindow, spm_html);
	<?php } ?>
	<?php } ?>
}

function spm_bindInfoWindow(marker, map, infoWindow, html) {
	google.maps.event.addListener(marker, 'click', function() {
		infoWindow.setContent(html);
		infoWindow.open(map, marker);
	});
}
//--></script>
<?php } ?>