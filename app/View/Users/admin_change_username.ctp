<?php
	echo $this->Html->tag('h2', 'Change Username');
	echo $this->Form->create('User', array(
		'url'=> array(
			'controller'=> 'users', 
			'action'=> 'change_username',
			'admin' => true,
		),
		'class'=> 'form-horizontal'
	));
?>
<div class="form-group">
	<?php 
			$title = __('New Username *');
			echo $this->Form->label('username', $title, array(
				'class'=>'control-label col-sm-2'
			)); 
	?>
	<div class="col-sm-8">
	<?php 
			echo $this->Form->input('username',array(
				'label'=>false,
				'placeholder' => $title,
				'required' => false,
				'class' => 'form-control',
				'required' => false,
			)); 
	?>
	</div>
</div>

<div class="form-group">
	<?php 
			$title = __('Password *');
			echo $this->Form->label('old_password', $title, array(
				'class'=>'control-label col-sm-2'
			)); 
	?>
	<div class="col-sm-8">
		<?php
				echo $this->Form->input('old_password', array(
					'type' => 'password',
					'placeholder' => $title,
					'class' => 'form-control',
					'label' => false,
					'required' => false,
				));
		?>    
	</div>
</div>
<div class="form-group">
	<div class="col-sm-8 col-sm-offset-2">
		<?php 
				echo $this->Form->submit(__('Save'), array(
					'class'=> 'btn btn-success'
				)); 
		?>
	</div>
</div>
<?php
	echo $this->Form->end();
?>