<?php
	echo $this->Html->tag('h2', 'Change Password');
	echo $this->Form->create('User', array(
		'url'=> array(
			'controller'=> 'users', 
			'action'=> 'authorization',
			'admin' => true,
		),
		'class'=> 'form-horizontal'
	));
?>
<div class="form-group">
	<?php 
			$title = __('Old Password *');
			echo $this->Form->label('current_password', $title, array(
				'class'=>'control-label col-sm-2'
			)); 
	?>
	<div class="col-sm-8">
	<?php 
			echo $this->Form->input('current_password',array(
				'type' => 'password',
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
			$title = __('New Password *');
			echo $this->Form->label('password', $title, array(
				'class'=>'control-label col-sm-2'
			)); 
	?>
	<div class="col-sm-8">
		<?php
				echo $this->Form->input('password', array(
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
	<?php 
			$title = __('Password Confirmation *');
			echo $this->Form->label('password_confirmation', $title, array(
				'class'=>'control-label col-sm-2'
			)); 
	?>
	<div class="col-sm-8">
		<?php
				echo $this->Form->input('password_confirmation', array(
					'type' => 'password',
					'placeholder' => $title,
					'class' => 'form-control',
					'label' => false,
					'required' => false,
					'error' => array(
						'wrap' => 'span', 
						'class' => 'error error_text',
						'notempty' => __('Mohon konfirmasikan password Anda'),
						'minLength' => __('Panjang password minimal 6 karakter'),
						'notMatch' => __('Mohon konfirmasikan password Anda'),
					),
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