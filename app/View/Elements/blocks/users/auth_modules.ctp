<?php
		if(!empty($branch_modules)){
			foreach ($branch_modules as $key => $value_module_parent) {
				$link = $this->Html->tag('span', $value_module_parent['BranchParentModule']['name'], array(
					'parent-id' => $value_module_parent['BranchParentModule']['name'],
					'class' => 'parent-auth'
				));

				$link_check = $this->Html->link('checkall', 'javascript:', array(
					'class' => 'branch-module-check',
					'type-action' => 'checkall',
					'parent-id' => $value_module_parent['BranchParentModule']['id'],
				));

				$link_uncheck = $this->Html->link('uncheckall', 'javascript:', array(
					'class' => 'branch-module-check',
					'type-action' => 'uncheckall',
					'parent-id' => $value_module_parent['BranchParentModule']['id']
				));

				$content_check = $this->Html->tag('div', sprintf('(%s / %s)', $link_check, $link_uncheck), array(
					'class' => 'branch-module-action'
				));

				$link = sprintf('%s %s <div class="clear"></div>', $link, $content_check);

				$content_module = '';

				if(!empty($value_module_parent['child'])){
					foreach ($value_module_parent['child'] as $key => $value_module) {
						$content = $this->Html->tag('div', $this->Html->tag('label', $value_module['BranchModule']['name']), array(
							'class' => 'col-sm-3'
						) );

						if(!empty($value_module['BranchChild'])){
							$list_action = '';
							foreach ($value_module['BranchChild'] as $key => $value) {
								$icon = '<i class="fa fa-times"></i>';
								if(!empty($value['is_allow']) || !empty($data_auth[$value['id']])){
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
				}

				$content_module = $this->Html->tag('div', $content_module,array(
					'class' => 'box-act'
				));

				echo $this->Html->tag('div', $link.$content_module, array(
					'class' => 'box-action-auth-module'
				));
			}
		}
?>