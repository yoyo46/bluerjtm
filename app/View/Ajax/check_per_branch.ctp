<?php
		if(!empty($branch_modules)){
			$list = '';
			foreach ($branch_modules as $key => $value) {
				$icon = '<i class="fa fa-times"></i>';
				if(!empty($data_auth[$value['BranchModule']['id']]) && $data_auth[$value['BranchModule']['id']] == $value['BranchModule']['id']){
					$icon = '<i class="fa fa-check"></i>';
				}

				$link_action = $this->Html->link(sprintf('%s %s', $icon, $value['BranchModule']['name']), 'javascript:', array(
					'action-id' => $value['BranchModule']['id'],
					'branch-id' => $group_branch_id,
					'class' => 'action-child-module',
					'escape' => false
				));

				$list .= $this->Html->tag('li', $link_action, array(
					'rel' => $value['BranchModule']['id']
				));
			}

			echo $this->Html->tag('ul', $list.'<div class="clear"></div>', array(
				'class' => 'list-auth-action'
			));
		}
?>