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