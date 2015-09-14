<?php
		$this->Html->addCrumb('Ijin Usaha Truk', array(
			'controller' => 'trucks',
			'action' => 'siup'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Siup', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
?>
<div class="row">
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Informasi Ijin Usaha'); ?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
        			<?php 
        					if( !empty($siup) && empty($siup['Siup']['status']) ) {
        						echo $this->Html->tag('label', __('Nopol Truk'));
        						echo $this->Html->tag('div', $siup['Siup']['no_pol']);
        					} else {
	        					$attrBrowse = array(
	                                'class' => 'ajaxModal visible-xs browse-docs',
	                                'escape' => false,
									'title' => __('Data Truk'),
									'data-action' => 'browse-form',
									'data-change' => 'truckID',
	                            );
	        					$urlBrowse = array(
	                                'controller'=> 'ajax', 
									'action' => 'getTrucks',
	                            );
								echo $this->Form->label('driver_id', __('Supir Truk ').$this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse));
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
										'data-action' => 'siup_add',
										'id' => 'truckID',
									));
							?>
		        		</div>
        				<div class="col-sm-2 hidden-xs">
	                        <?php 
        							$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
	                                echo $this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse);
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
								'label'=> __('Tgl Berakhir Ijin Usaha'), 
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
		        	<?php 
							echo $this->Form->label('price_estimate', __('Biaya Ijin Usaha')); 
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
									'placeholder' => __('Biaya Ijin Usaha'),
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
		        <h3 class="box-title"><?php echo __('Biaya Ijin Usaha'); ?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('tgl_siup', array(
								'label'=> __('Tanggal Perpanjang *'), 
								'class'=>'form-control custom-date',
								'type' => 'text',
								'required' => false,
								'value' => (!empty($this->request->data['Siup']['tgl_siup'])) ? $this->request->data['Siup']['tgl_siup'] : date('d/m/Y')
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
									'placeholder' => __('Biaya Ijin Usaha'),
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
			if( empty($siup) || !empty($siup['Siup']['status']) ) {
	            if( empty($siup['Siup']['paid']) && empty($siup['Siup']['rejected']) ){
		    		echo $this->Form->button(__('Simpan'), array(
						'div' => false, 
						'class'=> 'btn btn-success',
						'type' => 'submit',
					));
		    	}
		    }

    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'siup'
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>
<?php
		echo $this->Form->end();
?>