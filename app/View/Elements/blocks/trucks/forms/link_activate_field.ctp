<?php 
		if( !empty($data_changes) ) {
			$fieldClass = !empty($fieldClass)?$fieldClass:false;
			$fieldIcon = !empty($fieldIcon)?$fieldIcon:'angle-double-right';

			$arrow = $this->Common->icon($fieldIcon);

			echo $this->Html->tag('div', $this->Html->link($arrow, 'javascript:', array(
				'class' => 'active-field',
				'data-active' => $fieldClass,
				'escape' => false,
			)), array(
				'class'=>'icon-mutasi',
			));
		}
?>