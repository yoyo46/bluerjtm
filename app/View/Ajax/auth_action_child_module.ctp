<?php
		if(!empty($branch_modules)){
			$icon = '<i class="fa fa-times"></i>';
			if(!empty($data_auth['BranchActionModule']['is_allow'])){
				$icon = '<i class="fa fa-check"></i>';
			}

			echo $this->Html->link(sprintf('%s %s', $icon, ucfirst($branch_modules['BranchModule']['type'])), 'javascript:', array(
				'action-id' => $branch_modules['BranchModule']['id'],
				'branch-id' => $group_branch_id,
				'class' => 'action-child-module',
				'escape' => false
			));
		}
?>