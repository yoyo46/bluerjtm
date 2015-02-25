<?php
		$module_title = sprintf(__('Uang Kuli %s'), ucwords($data_action));
		$this->Html->addCrumb($module_title, array(
			'controller' => 'settings',
			'action' => 'uang_kuli'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('UangKuli', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
			'id' => 'UangKuliForm'
		));
?>
<div class="row">
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo $module_title;?></h3>
		    </div>
		    <div class="box-body">
		    	<?php 
						echo $this->Html->tag('div', $this->Form->input('title',array(
							'label'=> __('Nama *'), 
							'class'=>'form-control',
							'required' => false,
						)), array(
							'class' => 'form-group'
						));

						echo $this->Html->tag('div', $this->Form->input('uang_kuli_type',array(
							'label'=> __('Tipe Uang Kuli *'), 
							'class'=>'form-control uang_kuli',
							'required' => false,
							'options' => array(
								'per_truck' => __('Per Truk'),
								'per_unit' => __('Per Unit'),
							),
							'empty' => __('Pilih Tipe Uang Kuli'),
						)), array(
							'class' => 'form-group'
						));

						if( $data_action == 'bongkar' ) {
							echo $this->Html->tag('div', $this->Form->input('customer_id',array(
								'label'=> __('Customer *'), 
								'class'=>'form-control',
								'required' => false,
								'empty' => __('Pilih Customer'),
							)), array(
								'class' => 'form-group'
							));
						}

						$titleLabel = ( $data_action == 'bongkar' )?__('Tujuan'):__('Dari');
						$titleEmpy = ( $data_action == 'bongkar' )?__('Pilih Kota Tujuan'):__('Pilih Kota Asal');

						echo $this->Html->tag('div', $this->Form->input('city_id',array(
							'label'=> $titleLabel, 
							'class'=>'form-control',
							'required' => false,
							'empty' => $titleEmpy,
							'options' => $cities,
						)), array(
							'class' => 'form-group'
						));
				?>
				<div class="form-group">
		    		<?php 
		    				echo $this->Form->label('uang_kuli', __('Uang Kuli'));
		    		?>
	                <div class="input-group">
				    	<?php 
				    			echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
				    				'class' => 'input-group-addon'
			    				));
								echo $this->Form->input('uang_kuli',array(
									'label'=> false, 
									'class'=>'form-control input_price',
									'required' => false,
									'empty' => __('Uang Kuli'),
									'type' => 'text',
								));
						?>
					</div>
				</div>
		    </div>
		</div>
	</div>
	<?php 
			$addClass = 'hide';

			if( !empty($this->request->data['UangKuli']['uang_kuli_type']) && $this->request->data['UangKuli']['uang_kuli_type'] == 'per_unit' ) {
				$addClass = '';
			}
	?>
	<div class="col-sm-6 <?php echo $addClass; ?> biaya-per-unit">
		<div class="box box-success">
			<div class="box-header">
		        <h3 class="box-title"><?php echo __('Biaya Per Group Motor <small>(* Semua biaya berlaku apabila per unit)</small>'); ?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
		            <?php
		                    echo $this->Html->link('<i class="fa fa-plus"></i> '.__('Tambah'), 'javascript:', array(
		                        'class' => 'add-custom-field btn btn-success btn-xs',
		                        'action_type' => 'uang_kuli',
		                        'escape' => false
		                    ));
		            ?>
		        </div>
		        <div id="box-uang-kuli">
		        	<?php 
		        			if( !empty($this->request->data['UangKuliGroupMotor']['group_motor_id']) ) {
								foreach ($this->request->data['UangKuliGroupMotor']['group_motor_id'] as $key => $group_motor_id) {
									echo $this->element('blocks/settings/list_uang_kuli', array(
										'idx' => $key,
										'model' => 'UangKuliGroupMotor',
									));
								}
							}
		        	?>
		        </div>
		   	</div>
		</div>
	</div>
	<?php
			$addClass = 'hide';

			if( !empty($this->request->data['UangKuli']['uang_kuli_type']) && $this->request->data['UangKuli']['uang_kuli_type'] == 'per_truck' ) {
				$addClass = '';
			}
	?>
	<div class="col-sm-6 <?php echo $addClass; ?> capacity_truck">
		<div class="box box-success">
			<div class="box-header">
		        <h3 class="box-title"><?php echo __('Biaya Per Kapasitas'); ?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
		            <?php
		                    echo $this->Html->link('<i class="fa fa-plus"></i> '.__('Tambah'), 'javascript:', array(
		                        'class' => 'add-custom-field btn btn-success btn-xs',
		                        'action_type' => 'uang_kuli_capacity',
		                        'escape' => false
		                    ));
		            ?>
		        </div>
		        <div id="box-uang-kuli-capacity">
		        	<?php 
		        			if( !empty($this->request->data['UangKuliCapacity']['capacity']) ) {
								foreach ($this->request->data['UangKuliCapacity']['capacity'] as $key => $capacity) {
									echo $this->element('blocks/settings/list_uang_kuli_capacities', array(
										'idx' => $key,
										'model' => 'UangKuliCapacity',
									));
								}
							}
		        	?>
		        </div>
		   	</div>
		</div>
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
				'action' => 'uang_kuli', 
				$data_action,
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>
<?php
		echo $this->Form->end();
?>
<div class="hide">
	<div id="group_motor">
		<?php
                echo $this->Form->input('group_motor_id', array(
                    'label'=> false, 
                    'class'=>'form-control',
                    'required' => false,
                    'empty' => false,
                    'empty' => __('Pilih Group Motor'),
               	 	'options' => $groupMotors,
                ));
        ?>
	</div>
</div>