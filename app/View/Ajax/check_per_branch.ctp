<?php
		if(!empty($branch_modules)){
			$content_module = '';
			foreach ($branch_modules as $key => $value_module) {
				$content = $this->Html->tag('div', $this->Html->tag('label', $value_module['BranchModule']['name']), array(
					'class' => 'col-sm-3'
				) );

				if(!empty($value_module['BranchChild'])){
					$list_action = '';
					foreach ($value_module['BranchChild'] as $key => $value) {
						$icon = '<i class="fa fa-times"></i>';
						if($value['is_allow']){
							$icon = '<i class="fa fa-check"></i>';
						}

						$link_action = $this->Html->link(sprintf('%s %s', $icon, ucfirst($value['type'])), 'javascript:', array(
							'action-id' => $value['id'],
							'branch-id' => $group_branch_id,
							'class' => 'action-child-module',
							'escape' => false
						));

						$list_action .= $this->Html->tag('div', $link_action, array(
							'rel' => $value['id'],
							'class' => 'col-md-3 col-sm-3 col-xs-6 action-link-auth'
						));
					}

					$list_action = $this->Html->tag('div', $list_action, array(
						'class' => 'row'
					) );

					$content .= $this->Html->tag('div', $list_action, array(
						'class' => 'col-sm-9'
					) );

				}
				$content_module .= $this->Html->tag('div', $content, array(
					'class' => 'row list-auth-action'
				));
			}

			echo $this->Html->tag('div', $content_module, array(
				'class' => 'list-auth-action'
			));
		}
?>