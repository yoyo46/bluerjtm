<?php
		$this->Html->addCrumb(__('Mutasi Truk'), array(
			'controller' => 'trucks',
			'action' => 'mutations'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('TruckMutation', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
			'type' => 'file'
		));
		$mutationDate = (!empty($this->request->data['TruckMutation']['mutation_date'])) ? $this->request->data['TruckMutation']['mutation_date'] : date('d/m/Y');
?>
<div class="row">
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Informasi Utama')?></h3>
		    </div>
		    <div class="box-body">
		    	<div class="form-group">
		        	<?php 
							echo $this->Form->input('no_doc',array(
								'type' => 'text',
								'label'=> __('No. Dokumen *'), 
								'class'=>'form-control',
								'required' => false,
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('mutation_date',array(
								'label'=> __('Tanggal Mutasi *'), 
								'class'=>'form-control custom-date',
								'required' => false,
								'type' => 'text',
								'value' => $mutationDate,
							));
					?>
		        </div>
			</div>    	
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Keterangan Mutasi')?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('description',array(
								'type' => 'textarea',
								'label'=> __('Keterangan *'), 
								'class'=>'form-control description-mutation',
								'required' => false,
							));
					?>
		        </div>
			</div>    	
		</div>
	</div>
	<div class="clear"></div>
	<div class="col-sm-6">
		<div class="box box-success">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Informasi Truk')?></h3>
		    </div>
		    <div class="box-body">
		    	<?php 
		    			echo $this->element('blocks/trucks/forms/mutation');
		    	?>
			</div>    	
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-success">
			<div class="box-header">
		        <h3 class="box-title"><?php echo __('Mutasi')?></h3>
		    </div>
		    <div class="box-body">
		    	<?php 
		    			echo $this->element('blocks/trucks/forms/mutation', array(
		    				'data_changes' => true,
	    				));
		    	?>
		   	</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-success">
			<div class="box-header">
		        <h3 class="box-title"><?php echo __('Alokasi Truk')?></h3>
		    </div>
		    <div class="box-body" id="truck_customers">
		    	<?php 
		    			if( !empty($id) ) {
			    			if( !empty($truckMutation['TruckMutationOldCustomer']) ) {
			    				echo $this->element('blocks/trucks/alokasi_truck', array(
									'truckCustomers' => $truckMutation['TruckMutationOldCustomer'],
								));
			    			} else {
			    				echo '-';
			    			}
			    		}else if(!empty($truckCustomers['TruckCustomer'])){
							echo $this->element('blocks/trucks/alokasi_truck', array(
								'truckCustomers' => $truckCustomers['TruckCustomer'],
							));
			            }
		    	?>
		    </div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-success">
			<div class="box-header">
		        <h3 class="box-title"><?php echo __('Mutasi Alokasi Truk')?></h3>
		    </div>
		    <div class="box-body">
		    	<?php 
		    			if( !empty($truckMutation['TruckMutationCustomer']) ) {
		    				echo $this->element('blocks/trucks/alokasi_truck', array(
								'truckCustomers' => $truckMutation['TruckMutationCustomer'],
							));
		    			} else {
		    	?>
		        <div class="form-group">
		        	<?php
			        		echo $this->Html->link('<i class="fa fa-plus"></i> '.__('Tambah Alokasi'), 'javascript:', array(
								'class' => 'add-custom-field btn btn-success btn-xs',
								'action_type' => 'alocation',
								'escape' => false,
							));
		        	?>
		        </div>
		        <div id="box-field-input">
		        	<div id="main-alocation">
		        		<div class="list-alocation">
			        		<?php 
									echo $this->Form->label('TruckCustomer.customer_id.',__('Alokasi')); 
			        		?>
			        		<div class="row">
			        			<div class="col-sm-11">
			        				<div class="form-group">
					        			<?php 
												echo $this->Form->input('TruckCustomer.customer_id.',array(
													'label'=> false, 
													'class'=> 'form-control',
													'required' => false,
													'empty' => __('Pilih'),
													'options' => $customers,
													'value' => (!empty($this->request->data['TruckCustomer']['customer_id'][0])) ? $this->request->data['TruckCustomer']['customer_id'][0] : ''
												));
										?>
							        </div>
			        			</div>
			        			<div class="col-sm-1 no-pleft">
							        <?php
											echo $this->Html->link('<i class="fa fa-times"></i>', 'javascript:', array(
												'class' => 'delete-custom-field has-danger',
												'action_type' => 'alocation',
												'escape' => false
											));
							        ?>
			        			</div>
			        		</div>
		        		</div>
		        	</div>
		        </div>
		        <div id="advance-box-field-input">
		        	<?php
			        		if(!empty($this->request->data['TruckCustomer']['customer_id'])){
			        			foreach ($this->request->data['TruckCustomer']['customer_id'] as $key => $value) {
			        				if($key != 0 && !empty($value)){
		        	?>
	        		<div class="list-alocation">
		        		<?php 
								echo $this->Form->label('TruckCustomer.customer_id.',__('Alokasi *')); 
		        		?>
		        		<div class="row">
		        			<div class="col-sm-11">
					        	<div class="form-group">
				        			<?php 
										echo $this->Form->input('TruckCustomer.customer_id.',array(
											'label'=> false, 
											'class'=> 'form-control',
											'required' => false,
											'empty' => __('Pilih'),
											'options' => $customers,
											'value' => $value
										));
									?>
						        </div>
		        			</div>
		        			<div class="col-sm-1 no-pleft">
						        <?php
										echo $this->Html->link('<i class="fa fa-times"></i>', 'javascript:', array(
											'class' => 'delete-custom-field has-danger',
											'action_type' => 'alocation',
											'escape' => false
										));
						        ?>
		        			</div>
		        		</div>
	        		</div>
		        	<?php
			        				}
			        			}
			        		}
		        	?>
		        </div>
		        <?php 
		        		}
		        ?>
		   	</div>
		</div>
	</div>
</div>
<div class="box-footer text-center action">
	<?php
			if( empty($id) ) {
	    		echo $this->Form->button(__('Simpan'), array(
					'div' => false, 
					'class'=> 'btn btn-success',
					'type' => 'submit',
				));
	    	}

    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'mutations', 
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>
<?php
	echo $this->Form->end();
?>