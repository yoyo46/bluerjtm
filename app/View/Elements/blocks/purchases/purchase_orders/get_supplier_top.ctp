<?php 
		$vendor = !empty($vendor)?$vendor:false;
		$model_name = !empty($model_name)?$model_name:false;
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
			echo $this->Common->buildInputForm($model_name.'.top', __('T.O.P'), array(
				'type' => 'text',
				'textGroup' => __('Hari'),
				'column' => 'col-sm-6',
				'attributes' => $attributes,
			));
	?>
</div>