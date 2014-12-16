<?php 
		echo $this->Form->input('to_city_id',array(
			'label'=> false, 
			'class'=>'form-control',
			'required' => false,
			'empty' => __('Kota Tujuan --'),
			'options' => $resultCity,
			'div' => false,
			'id' => 'to_city_id',
		));
?>