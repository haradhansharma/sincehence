<?php
/*
  StorePickup Map
  Premium Extension
  
  Copyright (c) 2013 - 2019 Adikon.eu
  http://www.adikon.eu/
  
  You may not copy or reuse code within this file without written permission.
*/
?>
<div class="table-responsive">
	<table class="table table-bordered">
		<thead>
			<tr>
				<td class="text-left"><?php echo $column_name; ?></td>
				<td class="text-right"><?php echo $column_orders; ?></td>
				<td class="text-right"><?php echo $column_products; ?></td>
				<td class="text-right"><?php echo $column_total; ?></td>
				<td class="text-right"><?php echo $column_last_used; ?></td>
			</tr>
		</thead>
		<tbody>
			<?php if ($stores) { ?>
			<?php foreach ($stores as $store) { ?>
			<tr>
				<td class="text-left"><?php echo $store['name']; ?></td>
				<td class="text-right"><?php echo $store['orders']; ?></td>
				<td class="text-right"><?php echo $store['products']; ?></td>
				<td class="text-right"><?php echo $store['total']; ?></td>
				<td class="text-right"><?php echo $store['last_used']; ?></td>
			</tr>
			<?php } ?>
			<?php } else { ?>
			<tr>
				<td class="text-center" colspan="5"><?php echo $text_no_results; ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<div class="row">
	<div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
	<div class="col-sm-6 text-right"><?php echo $results; ?></div>
</div>