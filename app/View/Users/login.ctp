<h1>
    RJTM<small class="on-right"> ERP</small>
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
<div class="form-group">
	<?php
		echo $this->Form->input('User.username', array(
			'placeholder' => 'Username',
			'label' => false,
			'div' => false,				
			'required' => false,
			'class' => 'form-control input-lg',	
		));
	?>
</div>
<div class="form-group">
	<?php
		echo $this->Form->input('User.password', array(
			'type' => 'password',
			'placeholder' => 'Password',
			'class' => 'form-control input-lg',
			'label' => false,
			'div' => false,
			'required' => false,
		));
	?>
</div>
<div class="form-group">
<?php
	echo $this->Form->button(__('Sign in'), array(
		'type' => 'submit', 
		'class'=>'btn btn-primary btn-lg btn-block',
	));
?>
</div>
<?php
	echo $this->Form->end();
?>