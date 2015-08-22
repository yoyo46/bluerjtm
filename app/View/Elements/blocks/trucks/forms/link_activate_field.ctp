<?php 
		if( !empty($data_changes) ) {
			$fieldClass = !empty($fieldClass)?$fieldClass:false;
			$arrow = $this->Common->icon('angle-double-right');

			echo $this->Html->tag('div', $this->Html->link($arrow, 'javascript:', array(
				'class' => 'active-field',
				'data-active' => $fieldClass,
				'escape' => false,
			)), array(
				'class'=>'icon-mutasi',
			));
		}
?>