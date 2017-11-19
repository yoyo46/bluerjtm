<?php 
		if(!empty($child)){
			$content_module = '';
			$parentClass = '';
			$id = !empty($id)?$id:false;
			$parent_id = !empty($parent_id)?$parent_id:false;
			$branchCoas = !empty($branchCoas)?$branchCoas:array();

			foreach ($child as $key => $value) {
				$coa_id = $this->Common->filterEmptyField($value, 'Coa', 'id');
				$level = $this->Common->filterEmptyField($value, 'Coa', 'level');
				$name = $this->Common->filterEmptyField($value, 'Coa', 'coa_name');
				$child = $this->Common->filterEmptyField($value, 'children');
				$addClass = '';

				if( $level == 4 ){
					$icon = $this->Common->icon('times');
					$addClass = 'col-sm-6';

					if( in_array($coa_id, $branchCoas) ){
						$icon = $this->Common->icon('check');
					}

					$link_action = $this->Html->link(sprintf('%s %s', $icon, $this->Html->tag('label', $name)), array(
						'controller' => 'settings',
						'action' => 'branch_coa',
						$id,
						$coa_id,
						'admin' => false,
					), array(
						'class' => 'ajax-link',
						'data-wrapper-write' => sprintf('#wrapper-branch-form-%s', $parent_id),
						'escape' => false
					));
					$content = $this->Html->tag('div', $link_action, array(
						'rel' => $coa_id,
						'class' => 'action-link-auth'
					));
				} else {
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
					$content = sprintf('%s %s %s', $link, $content_check, $this->Html->tag('div', '', array(
						'class' => 'clearfix',
					)));
				}

				$content_module .= $this->Html->tag('div', $content, array(
					'class' => !empty($addClass)?$addClass:'list-auth-action',
				));

				if( !empty($child) ) {
					$content_module .= $this->element('blocks/settings/branch_coa_item_child', array(
						'child' => $child,
						'parent_id' => $coa_id,
					));
				}
			}

			if( !empty($addClass) ) {
				$parentClass = 'row list-coas';
			} else {
				$parentClass = 'box-act';
			}

			echo $this->Html->tag('div', $content_module,array(
				'class' => $parentClass,
				'id' => sprintf('wrapper-branch-form-%s', $parent_id),
			));
		}
?>