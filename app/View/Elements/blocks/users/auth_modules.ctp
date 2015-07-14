<?php
		if(!empty($branch_modules)){
			foreach ($branch_modules as $key => $value_module) {
				$list = '';
				$link = $this->Html->tag('span', $value_module['BranchModule']['name'], array(
					'parent-id' => $value_module['BranchModule']['name'],
					'class' => 'parent-auth'
				));

				if(!empty($value_module['BranchChild'])){
					foreach ($value_module['BranchChild'] as $key => $value) {
						$icon = '<i class="fa fa-times"></i>';
						if(!empty($data_auth[$value['id']]) && $data_auth[$value['id']] == $value['id']){
							$icon = '<i class="fa fa-check"></i>';
						}

						$link_action = $this->Html->link(sprintf('%s %s', $icon, $value['name']), 'javascript:', array(
							'action-id' => $value['id'],
							'branch-id' => $group_branch_id,
							'class' => 'action-child-module',
							'escape' => false
						));

						$list .= $this->Html->tag('li', $link_action, array(
							'rel' => $value['id']
						));
					}
				}

				$content_module = $this->Html->tag('div', $this->Html->tag('ul', $list.'<div class="clear"></div>', array(
					'class' => 'list-auth-action'
				)),array(
				'class' => 'box-act'
				));

				echo $this->Html->tag('div', $link.$content_module, array(
					'class' => 'box-action-auth-module'
				));
			}
		}
?>