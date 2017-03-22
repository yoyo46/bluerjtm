<?php 
		$vendor = !empty($vendor)?$vendor:false;
		$top = Common::hashEmptyField($vendor, 'Vendor.top');

		if( !empty($top) ) {
			$attributes = array(
				'value' => $top,
			);
		} else {
			$attributes = array();
		}
?>
<div class="wrapper-supplier">
	<?php 
			echo $this->Common->buildInputForm('Vendor.top', __('T.O.P'), array(
				'type' => 'text',
				'textGroup' => __('Hari'),
				'column' => 'col-sm-6',
				'attributes' => $attributes,
			));
	?>
</div>