<div id="nopol-laka">
	<?php 
			echo $this->Form->input('Laka.nopol',array(
				'label'=> __('Nopol Armada *'), 
				'class'=>'form-control',
				'required' => false,
				'type' => 'text',
				'readonly' => true,
				'value' => (!empty($data_ttuj['Ttuj']['nopol'])) ? $data_ttuj['Ttuj']['nopol'] : ''
			));
	?>
</div>
<div id="destination-laka">
	<div class="col-sm-6">
		<?php 
				echo $this->Form->input('Laka.from_city_name',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Dari Kota'),
					'readonly' => true,
					'value' => (!empty($data_ttuj['Ttuj']['from_city_name'])) ? $data_ttuj['Ttuj']['from_city_name'] : ''
				));
		?>
	</div>
	<div class="col-sm-6">
		<?php 
				echo $this->Form->input('Laka.to_city_name',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'placeholder' => __('Ke Kota'),
					'readonly' => true,
					'value' => (!empty($data_ttuj['Ttuj']['to_city_name'])) ? $data_ttuj['Ttuj']['to_city_name'] : ''
				));
		?>
	</div>
</div>