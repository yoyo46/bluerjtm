<?php
		$this->Html->addCrumb(__('Tipe Motor'), array(
            'action' => 'type_motors',
        ));
		$this->Html->addCrumb(__('Grup Motor'), array(
            'action' => 'group_motors',
        ));
        $this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('GroupMotor', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
			'class' => 'form-horizontal',
		));
	?>
    <div class="box-body">
        <?php 
				echo $this->Common->buildInputForm('name', __('Grup Motor *'), array(
					'type' => 'text',
					'labelClass' => 'col-sm-2 control-label',
					'divClass' => 'col-sm-4',
					'class' => 'form-control',
				));
				echo $this->Common->buildInputForm('converter', __('Converter UJ Extra'), array(
					'type' => 'text',
					'labelClass' => 'col-sm-2 control-label',
					'divClass' => 'col-sm-4',
					'textGroup' => __('Kali Lipat'),
					'class' => 'input_number form-control',
					'title' => __('Converter Uang Jalan'),
				));
				echo $this->element('blocks/common/forms/submit_action', array(
					'frameClass' => 'form-group action',
					'divClass' => 'col-sm-offset-2 col-sm-4',
					'urlBack' => array(
						'action' => 'group_motors', 
					),
				));
        ?>
    </div>
	<?php
		echo $this->Form->end();
	?>
</div>