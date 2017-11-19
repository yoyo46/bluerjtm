<?php 
		$id = !empty($id)?$id:false;
		$number = !empty($number)?$number:false;
		$class_wrapper_success =  sprintf('serial-number-fill-%s', $id);
		
		echo $this->Html->tag('div', __('%s No. Seri Terdaftar', $this->Html->tag('strong', $number)), array(
			'class' => sprintf('serial-number-set %s', $class_wrapper_success),
		));
?>