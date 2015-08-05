<?php
		$this->Html->addCrumb('STNK Truk', array(
			'controller' => 'trucks',
			'action' => 'stnk'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Stnk', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
?>
<div class="row">
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo $sub_module_title?></h3>
		    </div>
		    <div class="box-body">
		    	<div class="form-group">
        			<?php 
        					if( !empty($stnk) && empty($stnk['Stnk']['status']) ) {
        						echo $this->Html->tag('label', __('Nopol Truk'));
        						echo $this->Html->tag('div', $stnk['Stnk']['no_pol']);
        					} else {
	        					$attrTruck = array(
									'class' => 'ajaxModal visible-xs',
									'escape' => false,
									'title' => __('Data Truk'),
									'data-action' => 'browse-form',
									'data-change' => 'truckID',
								);
	        					$urlTruck = array(
									'controller'=> 'ajax', 
									'action' => 'getTrucks',
								);
								echo $this->Form->label('truck_id', __('Truk * ').$this->Html->link('<i class="fa fa-plus-square"></i>', $urlTruck, $attrTruck));
					?>
		        	<div class="row">
		        		<div class="col-sm-10">
				        	<?php 
									echo $this->Form->input('truck_id', array(
										'label'=> false, 
										'class'=>'form-control submit-change',
										'required' => false,
										'empty' => __('Pilih Truk'),
										'options' => $trucks,
										'action_type' => 'nopol',
										'data-action' => 'stnk_add',
										'id' => 'truckID',
									));
							?>
		        		</div>
		        		<div class="col-sm-2 hidden-xs">
		        			<?php 
		        					$attrTruck['class'] = 'btn bg-maroon ajaxModal';
									echo $this->Html->link('<i class="fa fa-plus-square"></i>', $urlTruck, $attrTruck);
							?>
		        		</div>
		        	</div>
		        	<?php 
		        			}
		        	?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('from_date', array(
								'label'=> __('Tgl Berakhir STNK'), 
								'class'=>'form-control',
								'type' => 'text',
								'required' => false,
								'readonly' => true,
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('to_date', array(
								'label'=> __('Berlaku Sampai'), 
								'class'=>'form-control',
								'type' => 'text',
								'required' => false,
								'readonly' => true,
							));
					?>
		        </div>
		        <div class="form-group">
			        <div class="checkbox aset-handling">
		                <label>
		                    <?php 
									echo $this->Form->checkbox('is_change_plat',array(
										'label'=> false, 
										'required' => false,
										'class' => 'change-plat',
									)).__('pergantian Plat Nomor Truk?');
							?>
		                </label>
		            </div>
		        </div>
		        <div class="content-plat <?php echo !empty($stnk['Stnk']['is_change_plat'])?'':'hide'; ?>">
		        	<div class="form-group">
			        	<?php 
								echo $this->Form->input('plat_from_date', array(
									'label'=> __('Tgl Ganti Plat'), 
									'class'=>'form-control',
									'type' => 'text',
									'required' => false,
									'readonly' => true,
								));
						?>
			        </div>
			        <div class="form-group">
			        	<?php 
								echo $this->Form->input('plat_to_date', array(
									'label'=> __('Plat Berlaku Sampai'), 
									'class'=>'form-control',
									'type' => 'text',
									'required' => false,
									'readonly' => true,
								));
						?>
			        </div>
		        </div>
		    </div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Biaya Perpanjang'); ?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
		        	<?php 
							echo $this->Form->label('price_estimate', __('Biaya STNK')); 
					?>
					<div class="input-group">
						<?php 
								echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
				    				'class' => 'input-group-addon'
			    				));
								echo $this->Form->input('price_estimate', array(
									'type' => 'text',
									'label'=> false, 
									'class'=>'form-control input_price',
									'required' => false,
									'placeholder' => __('Biaya STNK'),
									'readonly' => true,
								));
						?>
					</div>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('tgl_bayar', array(
								'label'=> __('Tgl Perpanjang *'), 
								'class'=>'form-control custom-date',
								'type' => 'text',
								'required' => false,
								'value' => (!empty($this->request->data['Stnk']['tgl_bayar'])) ? $this->request->data['Stnk']['tgl_bayar'] : date('d/m/Y')
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->label('price', __('Biaya Yg Dibayar *')); 
					?>
					<div class="input-group">
						<?php 
								echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
				    				'class' => 'input-group-addon'
			    				));
								echo $this->Form->input('price', array(
									'type' => 'text',
									'label'=> false, 
									'class'=>'form-control input_price',
									'required' => false,
									'placeholder' => __('Biaya STNK'),
								));
						?>
					</div>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->label('denda', __('Denda')); 
					?>
					<div class="input-group">
						<?php 
								echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
				    				'class' => 'input-group-addon'
			    				));
								echo $this->Form->input('denda', array(
									'type' => 'text',
									'label'=> false, 
									'class'=>'form-control input_price',
									'required' => false,
									'placeholder' => __('Denda'),
								));
						?>
					</div>
		        </div>
		    </div>
		</div>
	</div>
</div>
<div class="box-footer text-center action">
	<?php
			if( empty($stnk) || !empty($stnk['Stnk']['status']) ) {
	            if( empty($stnk['Stnk']['paid']) && empty($stnk['Stnk']['rejected']) ){
		    		echo $this->Form->button(__('Simpan'), array(
						'div' => false, 
						'class'=> 'btn btn-success',
						'type' => 'submit',
					));
		    	}
		    }

    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'stnk',
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>
<?php
		echo $this->Form->end();
?>