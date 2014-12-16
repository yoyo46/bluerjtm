<h1>
    RJTM<small class="on-right">site</small>
</h1>
<?php
	echo $this->Form->create('User', array(
		'url' => array(
			'controller' => 'users', 
			'action' =>'login', 
			'admin'=> false
		), 
		'id'=> 'form-signin',
		'class' => 'form-horizontal'
	));
	
	echo $this->Session->flash();
	// echo $this->Session->flash('auth');
	echo $this->Session->flash('success');
	echo $this->Session->flash('error');
	echo $this->Session->flash('info');
	echo $this->Session->flash('email');
?>
<div class="input-control text" data-role="input-control">
	<?php
		echo $this->Form->input('User.username', array(
			'placeholder' => 'Username',
			'label' => false,
			'div' => false,				
			'required' => true,			
		));
	?>
</div>
<div class="input-control password" data-role="input-control">
	<?php
		echo $this->Form->input('User.password', array(
			'type' => 'password',
			'placeholder' => 'Password',
			'class' => 'form-control',
			'label' => false,
			'div' => false,
			'required' => true,
		));
	?>
</div>
<div class="input-control checkbox" data-role="input-control">
	<label>
		<?php
			echo $this->Form->input('User.remember_me', array(
				'type' => 'checkbox',
				'label' => false,
				'div' => false,
				'required' => false,
			));
		?>
		<span class="check"></span>
		Remember me
	</label>
</div>
<br>
<?php
	echo $this->Html->link('Lupa password?', array(
		'controller' => 'users',
		'action' => 'forgot',
		'admin' => false
	));
?>
<br><br>
<?php
	echo $this->Form->button(__('Sign in'), array(
		'type' => 'submit', 
		'class'=>'btn large default',
	));

	echo $this->Form->end();
?>