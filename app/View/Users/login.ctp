<?php
		echo $this->Form->create('User', array(
			'url' => array(
				'controller' => 'users', 
				'action' =>'login', 
				'admin'=> false
			), 
			'class'=> 'form-signin',
		));
		
		echo $this->Session->flash();
		// echo $this->Session->flash('auth');
		echo $this->Session->flash('success');
		echo $this->Session->flash('error');
		echo $this->Session->flash('info');
		echo $this->Session->flash('email');
?>
<h2>
    RJTM<small class="on-right">site</small>
</h2>
<?php
		echo $this->Form->input('User.username', array(
			'placeholder' => 'Username',
			'label' => false,
			'div' => false,				
			'required' => true,	
			'class' => 'form-control',
		));
		echo $this->Form->input('User.password', array(
			'type' => 'password',
			'placeholder' => 'Password',
			'class' => 'form-control',
			'label' => false,
			'div' => false,
			'required' => true,
		));
?>
<div class="form-group">
	<?php
		echo $this->Form->button(__('Sign in'), array(
			'type' => 'submit', 
			'class'=>'btn btn-lg btn-primary btn-block',
		));
	?>
</div>

<?php
	echo $this->Form->end();
?>