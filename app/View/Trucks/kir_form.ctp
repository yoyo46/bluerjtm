<?php
		$this->Html->addCrumb('KIR Truk', array(
			'controller' => 'trucks',
			'action' => 'kir'
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Kir', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
		));
?>
<div class="row">
	<div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Informasi KIR'); ?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
        			<?php 
        					if( !empty($kir) && empty($kir['Kir']['status']) ) {
        						echo $this->Html->tag('label', __('Nopol Truk'));
        						echo $this->Html->tag('div', $kir['Kir']['no_pol']);
        					} else {
	        					$attrBrowse = array(
	                                'class' => 'ajaxModal visible-xs',
	                                'escape' => false,
									'title' => __('Data Truk'),
									'data-action' => 'browse-form',
									'data-change' => 'truckID',
	                            );
	        					$urlBrowse = array(
	                                'controller'=> 'ajax', 
									'action' => 'getTrucks',
	                            );
								echo $this->Form->label('truck_id', __('Truk * ').$this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse));
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
										'id' => 'truckID',
										'action_type' => 'nopol',
										'data-action' => 'kir_add',
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
								'label'=> __('Tgl Berakhir KIR'), 
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
							echo $this->Form->label('price_estimate', __('Biaya KIR')); 
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
									'placeholder' => __('Biaya KIR'),
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
		        <h3 class="box-title"><?php echo __('Biaya KIR'); ?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('tgl_kir', array(
								'label'=> __('Tanggal Perpanjang *'), 
								'class'=>'form-control custom-date',
								'type' => 'text',
								'required' => false,
								'value' => (!empty($this->request->data['Kir']['tgl_kir'])) ? $this->request->data['Kir']['tgl_kir'] : date('d/m/Y')
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
									'placeholder' => __('Biaya KIR'),
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
			if( empty($kir) || !empty($kir['Kir']['status']) ) {
	            if( empty($kir['Kir']['paid']) && empty($kir['Kir']['rejected']) ){
		    		echo $this->Form->button(__('Simpan'), array(
						'div' => false, 
						'class'=> 'btn btn-success',
						'type' => 'submit',
					));
		    	}
		    }

    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'kir'
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>
<?php
		echo $this->Form->end();
?>