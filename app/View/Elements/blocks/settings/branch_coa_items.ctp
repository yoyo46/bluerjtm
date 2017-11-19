<?php
		$id = !empty($id)?$id:false;

		if(!empty($list_coas)){
			foreach ($list_coas as $key => $value) {
				$content_module = '';
				$parent_id = $this->Common->filterEmptyField($value, 'Coa', 'parent_id');
				$coa_id = $this->Common->filterEmptyField($value, 'Coa', 'id');
				$name = $this->Common->filterEmptyField($value, 'Coa', 'coa_name');
				$child = $this->Common->filterEmptyField($value, 'children');

				$link = $this->Html->tag('span', $name, array(
					'parent-id' => $name,
					'class' => 'parent-auth',
				));
				$link_check = $this->Html->link('checkall', array(
					'controller' => 'settings',
					'action' => 'branch_coa_checkall',
					$id,
					$coa_id,
					'admin' => false,
				), array(
					'class' => 'branch-module-check',
					'class' => 'ajax-link',
					'data-wrapper-write' => sprintf('#wrapper-branch-form-%s', $coa_id),
				), __('Anda yakin ingin menambahkan semua COA pada cabang ini?'));
				$link_uncheck = $this->Html->link('uncheckall', array(
					'controller' => 'settings',
					'action' => 'branch_coa_uncheckall',
					$id,
					$coa_id,
					'admin' => false,
				), array(
					'class' => 'branch-module-check',
					'class' => 'ajax-link',
					'data-wrapper-write' => sprintf('#wrapper-branch-form-%s', $coa_id),
				), __('Anda yakin ingin menghapus semua COA pada cabang ini?'));
				$content_check = $this->Html->tag('div', sprintf('(%s / %s)', $link_check, $link_uncheck), array(
					'class' => 'branch-module-action'
				));
				$link = sprintf('%s %s <div class="clear"></div>', $link, $content_check);

				if( !empty($child) ) {
					$content_module = $this->element('blocks/settings/branch_coa_item_child', array(
						'child' => $child,
						'parent_id' => $coa_id,
					));
				}

				echo $this->Html->tag('div', $link.$content_module, array(
					'class' => 'box-action-auth-module',
				));
			}
		}
?>