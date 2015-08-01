<?php
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php 
			echo $this->Form->create('User');
	?>
    <div class="box-body">
		<div class="form-group">
			<?php
					echo $this->Form->input('new_password', array(
						'type' => 'password',
						'class' => 'form-control',
						'label' => __('Password baru *'),
						'required' => false,
					));
			?>
		</div>

		<div class="form-group">
			<?php
					echo $this->Form->input('new_password_confirmation', array(
						'type' => 'password',
						'class' => 'form-control',
						'label' => __('Password Confirmation *'),
						'required' => false,
						'error' => array(
							'wrap' => 'span', 
							'class' => 'error error_text',
							'notempty' => __('Mohon konfirmasikan password Anda'),
							'minLength' => __('Panjang password minimal 6 karakter'),
							'notMatch' => __('Mohon konfirmasikan password Anda'),
						),
					));
			?>
		</div>
		<div class="box-footer text-center action">
	    	<?php
		    		echo $this->Form->button(__('Simpan'), array(
						'div' => false, 
						'class'=> 'btn btn-success',
						'type' => 'submit',
					));
		    		echo $this->Html->link(__('Kembali'), array(
		    			'controller' => 'users',
		    			'action' => 'list_user'
	    			), array(
						'div' => false, 
						'class'=> 'btn btn-default',
						'type' => 'submit',
					));
	    	?>
	    </div>
    </div>
    <?php
		echo $this->Form->end();
	?>
</div>
