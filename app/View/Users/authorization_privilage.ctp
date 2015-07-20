<?php
		$this->Html->addCrumb(__('Group'), array(
			'controller' => 'users',
			'action' => 'groups'
		));
		$this->Html->addCrumb($sub_module_title);
?>
	<div id="auth-box">
		<?php
				if(empty($GroupBranches)){
		?>
		<div class="box" rel="1">
		    <div class="box-header">
		        <div class="box-title">
		        	<?php
		        		echo $this->Form->input('city_id.', array(
							'label'=> false, 
							'class'=>'form-control auth-form-open',
							'required' => false,
							'options' => $branches,
							'empty' => __('Pilih Cabang'),
							'div' => false
						));
		        	?>
		        </div>
		        <div class="box-tools pull-right">
		            <button class="btn btn-default btn-sm trigger-collapse" rel="plus"><i class="fa fa-plus"></i></button>
		            <?php
			            	echo $this->Html->link('<i class="fa fa-times"></i>', 'javascript:', array(
				    			'class' => 'btn btn-default btn-sm delete-custom-field',
				    			'action_type' => 'delete-auth-branch',
				    			'escape' => false,
				    			'group-branch-id' => 0,
				    		));
		            ?>
		        </div>
		    </div>
		    <div class="box-body auth-action-box" style="display: none;"></div>
		</div>
	    <?php
		    	}else{
		    		$i = 1;
		    		foreach ($GroupBranches as $key => $value) {
		    			$group_branch_id = $value['GroupBranch']['id'];
	  	?>
	  	<div class="box" rel="<?php echo $i;?>">
		    <div class="box-header">
		        <div class="box-title">
		        	<?php
		        		echo $this->Form->input('city_id.',array(
							'label'=> false, 
							'class'=>'form-control auth-form-open',
							'required' => false,
							'options' => $branches,
							'empty' => __('Pilih Cabang'),
							'value' => $value['GroupBranch']['city_id'],
							'div' => false
						));
		        	?>
		        </div>
		        <div class="box-tools pull-right">
		            <button class="btn btn-default btn-sm trigger-collapse" rel="plus"><i class="fa fa-plus"></i></button>
		            <?php
		            	echo $this->Html->link('<i class="fa fa-times"></i>', 'javascript:', array(
			    			'class' => 'btn btn-default btn-sm delete-custom-field',
			    			'action_type' => 'delete-auth-branch',
			    			'escape' => false,
			    			'group-branch-id' => $group_branch_id,
			    		));
		            ?>
		        </div>
		    </div>
		    <div class="box-body auth-action-box" style="display: none;">
		    	<?php
		    		echo $this->element('blocks/users/auth_modules', array(
						'data_auth' => $value['BranchActionModule'],
						'group_branch_id' => $group_branch_id
					));
		    	?>
		    </div>
		</div>
	  	<?php
	  				$i++;

	    		}
	    	}
	    ?>
	</div>
<?php
	echo $this->Html->link('<i class="fa fa-plus-square"></i> '.__('Tambah Otorisasi'), 'javascript:', array(
		'class' => 'add-custom-field btn bg-maroon',
		'action_type' => 'add_auth',
		'escape' => false
	));
?>
<div class="hide" id="temp-auth">
    <?php 
		echo $this->Form->input('city_id.',array(
			'label'=> false, 
			'class'=>'form-control auth-form-open',
			'required' => false,
			'options' => $branches,
			'empty' => __('Pilih Cabang'),
			'div' => false
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