<?php 
		echo $this->Form->input('from_city_id',array(
			'label'=> false, 
			'class'=>'form-control',
			'required' => false,
			'empty' => __('Dari Kota --'),
			'options' => $resultCity,
			'div' => false,
			'id' => 'from_city_id',
		));
?>