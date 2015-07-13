<?php
		$this->Html->addCrumb(__('Group'), array(
			'controller' => 'users',
			'action' => 'groups'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
		echo $this->Form->create('GroupBranch', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
	?>
	<div id="auth-box">
		<?php
			if(empty($GroupBranches)){
		?>
    	<div class="box-body" rel="1">
	    	<?php 
	    		echo $this->Form->label('city_id.', __('Cabang 1'));
	    		echo $this->Html->link('<i class="fa fa-times"></i>', 'javascript:', array(
	    			'class' => 'delete_custom_field pull-right',
	    			'action_type' => 'delete-auth-branch',
	    			'escape' => false
	    		));

				$form = $this->Form->input('city_id.', array(
					'label'=> false, 
					'class'=>'form-control auth-form-open',
					'required' => false,
					'options' => $branches,
					'empty' => __('Pilih Cabang')
				));

				echo $this->Html->tag('div', $form, array(
					'class' => 'form-group'
				));

				echo $this->Html->tag('div', '', array(
					'class' => 'auth-action-box'
				));
			?>
	    </div>
	    <?php
	    	}else{
	    		$i = 1;
	    		foreach ($GroupBranches as $key => $value) {
	  	?>
	  	<div class="box-body" rel="<?php echo $i;?>">
	  		<?php
	  				$group_branch_id = $value['GroupBranch']['id'];

	  				echo $this->Form->label('city_id.', sprintf(__('Cabang %s'), $i));
		    		echo $this->Html->link('<i class="fa fa-times"></i>', 'javascript:', array(
		    			'class' => 'delete-custom-field pull-right',
		    			'action_type' => 'delete-auth-branch',
		    			'group-branch-id' => $group_branch_id,
		    			'escape' => false
		    		));

	  				$form = $this->Form->input('city_id.',array(
						'label'=> false, 
						'class'=>'form-control auth-form-open',
						'required' => false,
						'options' => $branches,
						'empty' => __('Pilih Cabang'),
						'value' => $value['GroupBranch']['city_id']
					));

					echo $this->Html->tag('div', $form, array(
						'class' => 'form-group'
					));

					$list_module = $this->element('blocks/users/auth_modules', array(
						'data_auth' => $value['BranchActionModule'],
						'group_branch_id' => $group_branch_id
					));

					echo $this->Html->tag('div', $list_module, array(
						'class' => 'auth-action-box'
					));

	  				$i++;
		  	?>
	  	</div>
	  	<?php
	    		}
	    	}
	    ?>
	</div>
</div>
<?php
	echo $this->Form->end();

	echo $this->Html->link('<i class="fa fa-plus-square"></i> '.__('Tambah Otorisasi'), 'javascript:', array(
		'class' => 'add-custom-field btn bg-maroon',
		'action_type' => 'add_auth',
		'escape' => false
	));
?>
<div class="hide" id="temp-auth">
    <?php 
		$form = $this->Form->input('city_id.',array(
			'label'=> false, 
			'class'=>'form-control auth-form-open',
			'required' => false,
			'options' => $branches,
			'empty' => __('Pilih Cabang')
		));

		echo $this->Html->tag('div', $form, array(
			'class' => 'form-group'
		));

		echo $this->Html->tag('div', '', array(
			'class' => 'auth-action-box'
		));
	?>
</div>
<div class="hide">
    <?php 
		echo $this->Form->hidden('group_id',array(
			'value'=> $group_id, 
			'id' => 'group-id'
		));
	?>
</div>