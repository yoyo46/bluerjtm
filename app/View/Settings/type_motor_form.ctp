<?php
		$this->Html->addCrumb(__('Tipe Motor'), array(
            'action' => 'type_motors',
        ));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary box-solid">
    <?php 
			echo $this->Html->tag('div', $this->Html->tag('div', $sub_module_title, array(
				'class' => 'box-title',
			)), array(
				'class' => 'box-header',
			));

			echo $this->Form->create('TipeMotor', array(
				'class' => 'form-horizontal',
			));
	?>
    <div class="box-body">
    	<?php
				echo $this->Common->buildInputForm('code_motor', __('Kode *'), array(
					'labelClass' => 'col-sm-2 control-label',
					'divClass' => 'col-sm-4',
				));
				echo $this->Common->buildInputForm('name', __('Tipe Motor *'), array(
					'labelClass' => 'col-sm-2 control-label',
					'divClass' => 'col-sm-4',
				));
				echo $this->Common->buildInputForm('group_motor_id', __('Grup Motor *'), array(
					'labelClass' => 'col-sm-2 control-label',
					'divClass' => 'col-sm-4',
					'empty' => __('Pilih Grup Motor'),
					'options' => $group_motors
				));
				// echo $this->Common->buildInputForm('converter', __('Converter UJ Extra'), array(
				// 	'type' => 'text',
				// 	'labelClass' => 'col-sm-2 control-label',
				// 	'divClass' => 'col-sm-4',
				// 	'textGroup' => __('Kali Lipat'),
				// 	'class' => 'input_number form-control',
				// 	'title' => __('Converter Uang Jalan'),
				// ));
				echo $this->element('blocks/common/forms/submit_action', array(
					'frameClass' => 'form-group action',
					'divClass' => 'col-sm-offset-2 col-sm-4',
					'urlBack' => array(
						'action' => 'type_motors', 
					),
				));
		?>
    </div>
	<?php

			echo $this->Form->end();
	?>
</div>