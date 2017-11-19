<?php 
	echo $this->Form->create('Contact', array(
		'url'=> $this->Html->url( null, true ), 
		'role' => 'form',
		'inputDefaults' => array('div' => false),
		'type' => 'file',
		'class' => 'form-horizontal'
	));
?>
<div class="form-group">
		<?php
				echo $this->Form->label('excel_read', __('File Excel'), );
				echo $this->Form->input('excel_read', array(
					'label'=> __('Kelamin'), 
					'options' => array(
						'male' => 'Pria',
						'female' => 'Wanita'
					),
					'class'=>'col-sm-12 form-control',
					'required' => false
				));
		?>
</div>
<div class="form-group btn-submit">
		<?php
				echo $this->Form->button(__('Submit'), array(
					'div' => false, 
					'class'=> 'btn btn-success btn-lg',
					'type' => 'submit',
				)).'&nbsp;';
				echo $this->Html->link(__('Back'), array(
					'controller' => 'users', 
					'action' => 'contacts',
					'admin' => true
				), array(
					'class'=> 'btn btn-warning btn-lg'
				));
		?>
</div>
<?php
	echo $this->Form->end();
?>