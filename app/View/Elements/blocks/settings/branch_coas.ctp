<div id="auth-box" class="branch-coas-form">
  	<div class="box">
	    <div class="box-header">
	        <h3 class="box-title block">
		        <?php
		        		echo __('Akses COA');

		        		$link_check = $this->Html->link('checkall', array(
							'controller' => 'settings',
							'action' => 'branch_coa_checkall',
							$id,
							'admin' => false,
						), array(
							'class' => 'branch-check',
							'class' => 'ajax-link',
							'data-wrapper-write' => '#wrapper-branch-form',
						), __('Anda yakin ingin menambahkan semua COA pada cabang ini?'));
						$link_uncheck = $this->Html->link('uncheckall', array(
							'controller' => 'settings',
							'action' => 'branch_coa_uncheckall',
							$id,
							'admin' => false,
						), array(
							'class' => 'branch-check',
							'class' => 'ajax-link',
							'data-wrapper-write' => '#wrapper-branch-form',
						), __('Anda yakin ingin menghapus semua COA pada cabang ini?'));

						echo $this->Html->tag('div', sprintf('%s / %s', $link_check, $link_uncheck), array(
							'class' => 'branch-action pull-right'
						));
	        	?>
	        </h3>
	    </div>
    	<?php
	    		echo  $this->Html->tag('div', $this->element('blocks/settings/branch_coa_items'), array(
	    			'class' => 'box-body auth-action-box',
					'id' => 'wrapper-branch-form',
	    		));
    	?>
	</div>
</div>