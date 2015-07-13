<div id="box-action-auth">
<?php
		echo $this->element('blocks/users/auth_modules');
?>
</div>
<?php
		echo $this->Html->tag('div', $group_branch_id, array(
			'id' => 'group_branch_id',
			'class' => 'hide'
		));
?>