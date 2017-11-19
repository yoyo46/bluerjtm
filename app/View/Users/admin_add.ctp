<?php 
	$this->Html->addCrumb(__('Contact'), array(
		'controller' => 'users',
		'action' => 'contacts',
		'admin' => true
	));
	$this->Html->addCrumb(__('Add'));
	echo $this->element('blocks/users/form');
?>