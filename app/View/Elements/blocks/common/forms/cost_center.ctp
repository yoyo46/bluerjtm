<?php
		$disabled = Configure::read('__Site.config_cost_center_readonly');

		echo $this->Common->buildInputForm('cogs_id', __('Cost Center'), array(
			'label'=> __('Cost Center'), 
			'class'=>'form-control chosen-select',
			'empty' => __('Pilih Cost Center '),
			'options' => $cogs,
			'disabled' => $disabled,
		));
?>