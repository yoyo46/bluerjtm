<div id="destination-laka">
	<div class="col-sm-6">
		<?php 
				echo $this->Form->input('Laka.from_city_name',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'readonly' => true,
					'value' => (!empty($data_ttuj['Ttuj']['from_city_name'])) ? $data_ttuj['Ttuj']['from_city_name'] : ''
				));
				echo $this->Form->hidden('Laka.from_city_id', array(
					'value' => (!empty($data_ttuj['Ttuj']['from_city_id'])) ? $data_ttuj['Ttuj']['from_city_id'] : ''
				));
		?>
	</div>
	<div class="col-sm-6">
		<?php 
				echo $this->Form->input('Laka.to_city_name',array(
					'label'=> false, 
					'class'=>'form-control',
					'required' => false,
					'readonly' => true,
					'value' => (!empty($data_ttuj['Ttuj']['to_city_name'])) ? $data_ttuj['Ttuj']['to_city_name'] : ''
				));
				echo $this->Form->hidden('Laka.to_city_id', array(
					'value' => (!empty($data_ttuj['Ttuj']['to_city_id'])) ? $data_ttuj['Ttuj']['to_city_id'] : ''
				));
		?>
	</div>
</div>
<div id="data-supir-pengganti">
	<?php
		echo $this->Form->hidden('Laka.change_driver_id',array(
			'id' => 'laka-driver-change-id',
			'readonly' => true,
			'value' => !empty($data_ttuj['Ttuj']['driver_pengganti_id']) ? $data_ttuj['Ttuj']['driver_pengganti_id'] : 0
		));

		echo $this->Form->input('Laka.change_driver_name',array(
			'type' => 'text',
			'label'=> __('Nama Supir Pengganti'), 
			'class'=>'form-control',
			'required' => false,
			'id' => 'laka-driver-change-name',
			'value' => $this->Common->filterEmptyField($data_ttuj, 'DriverPengganti', 'driver_name'),
			'readonly' => true
		));
	?>
</div>