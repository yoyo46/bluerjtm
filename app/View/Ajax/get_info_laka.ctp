<?php 
		// echo $this->Form->hidden('no_sim',array(
		// 	'id'=>'no_sim',
		// 	'value' => (!empty($data_ttuj['Ttuj']['no_sim'])) ? $truck['Driver']['no_sim'] : ''
		// ));
		// echo $this->Form->hidden('truck_id',array(
		// 	'id'=>'truck_id',
		// 	'value' => $truck_id
		// ));
?>
<!-- <div id="nopol-laka">
	<?php 
			// echo $this->Form->input('Laka.nopol',array(
			// 	'label'=> __('Nopol Armada *'), 
			// 	'class'=>'form-control',
			// 	'required' => false,
			// 	'type' => 'text',
			// 	'readonly' => true,
			// 	'value' => (!empty($data_ttuj['Ttuj']['nopol'])) ? $data_ttuj['Ttuj']['nopol'] : ''
			// ));
	?>
</div> -->
<div id="destination-laka">
	<div class="col-sm-6">
		<?php 
			echo $this->Form->input('Laka.from_city_id',array(
				'label'=> false, 
				'class'=>'form-control',
				'required' => false,
				'empty' => __('Dari Kota'),
				'options' => $fromCities,
				// 'readonly' => true,
				'value' => (!empty($data_ttuj['Ttuj']['from_city_id'])) ? $data_ttuj['Ttuj']['from_city_id'] : ''
			));
		?>
	</div>
	<div class="col-sm-6">
		<?php 
			echo $this->Form->input('Laka.to_city_id',array(
				'label'=> false, 
				'class'=>'form-control',
				'required' => false,
				'empty' => __('Ke Kota'),
				'options' => $toCities,
				// 'readonly' => true,
				'value' => (!empty($data_ttuj['Ttuj']['to_city_id'])) ? $data_ttuj['Ttuj']['to_city_id'] : ''
			));
		?>
	</div>
</div>