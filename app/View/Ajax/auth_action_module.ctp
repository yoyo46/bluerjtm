<?php
		echo $this->Html->tag('div', $this->element('blocks/users/auth_modules'), array(
			'id' => 'box-action-auth',
		));
		echo $this->Html->tag('div', $group_branch_id, array(
			'id' => 'group_branch_id',
			'class' => 'hide'
		));
?>