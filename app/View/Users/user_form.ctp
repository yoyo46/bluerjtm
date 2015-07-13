<?php
		$this->Html->addCrumb(__('User'), array(
            'action' => 'list_user',
        ));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <?php
		echo $this->Form->create('User', array(
			'url'=> $this->Html->url(null, false),
			'class'=> 'form-horizontal'
		));
	?>
	<div class="box-body">
		<div class="form-group">
			<?php 
					$title = __('Cabang *');
					echo $this->Form->label('branch_id', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('branch_id',array(
						'label'=>false,
						'empty' => __('Pilih Cabang --'),
						'required' => false,
						'class' => 'form-control',
						'options' => $branches
					)); 
			?>
			</div>
		</div>
		<div class="form-group">
			<?php 
					$title = __('Karyawan *');
					echo $this->Form->label('employe_id', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('employe_id',array(
						'label'=>false,
						'empty' => __('Pilih Karyawan'),
						'required' => false,
						'class' => 'form-control employe-field',
						'options' => $employes
					)); 
			?>
			</div>
		</div>
		<div class="form-group">
			<?php 
					$title = __('Email *');
					echo $this->Form->label('email', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('email',array(
						'label'=>false,
						'placeholder' => $title,
						'required' => false,
						'class' => 'form-control',
					)); 
			?>
			</div>
		</div>
		<?php 
                if( empty($id) ) {
		?>
		<div class="form-group">
			<?php 
					$title = __('Password *');
					echo $this->Form->label('password', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('password',array(
						'label'=>false,
						'placeholder' => $title,
						'required' => false,
						'class' => 'form-control',
					)); 
			?>
			</div>
		</div>
		<div class="form-group">
			<?php 
					$title = __('Confirm Password *');
					echo $this->Form->label('password_confirmation', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('password_confirmation',array(
						'label'=>false,
						'placeholder' => $title,
						'required' => false,
						'class' => 'form-control',
						'type' => 'password',
					)); 
			?>
			</div>
		</div>
		<?php 
				}
		?>
		<div class="form-group">
			<?php 
					$title = __('Group *');
					echo $this->Form->label('group_id', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('group_id',array(
						'label'=>false,
						'required' => false,
						'class' => 'form-control',
						'empty' => __('Pilih Group'),
						'options' => $groups
					)); 
			?>
			</div>
		</div>
		<div class="form-group">
			<?php 
					$title = __('Nama Depan *');
					echo $this->Form->label('first_name', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('first_name',array(
						'label'=>false,
						'placeholder' => $title,
						'required' => false,
						'class' => 'form-control first-name-box',
					)); 
			?>
			</div>
		</div>
		<div class="form-group">
			<?php 
					$title = __('Nama Belakang');
					echo $this->Form->label('last_name', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('last_name',array(
						'label'=>false,
						'placeholder' => $title,
						'required' => false,
						'class' => 'form-control last-name-box',
					)); 
			?>
			</div>
		</div>
		<div class="form-group">
			<?php 
					$title = __('Telepon *');
					echo $this->Form->label('phone', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('phone',array(
						'label'=>false,
						'placeholder' => $title,
						'required' => false,
						'class' => 'form-control',
					)); 
			?>
			</div>
		</div>
		<div class="form-group">
			<?php 
					$title = __('Jenis Kelamin *');
					echo $this->Form->label('gender', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('gender',array(
						'label'=>false,
						'required' => false,
						'class' => 'form-control',
						'empty' => __('Pilih Jenis Kelamin'),
						'options' => array(
							'male' => __('Pria'),
							'female' => __('Wanita'),
						)
					)); 
			?>
			</div>
		</div>
		<div class="form-group">
			<?php 
					$title = __('Tangal Lahir *');
					echo $this->Form->label('birthdate', $title, array(
						'class'=>'control-label col-sm-2'
					)); 
			?>
			<div class="col-sm-8">
			<?php 
					echo $this->Form->input('birthdate',array(
						'type' => 'text',
						'label'=>false,
						'placeholder' => $title,
						'required' => false,
						'class' => 'form-control custom-date',
					)); 
			?>
			</div>
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