<?php
		$this->Html->addCrumb(__('Pembayaran Uang Jalan'), array(
			'controller' => 'revenues',
			'action' => 'uang_jalan_payments'
		));
		$this->Html->addCrumb($sub_module_title);
		$disabled = false;

		if( !empty($invoice) ) {
			$disabled = true;
		}

		echo $this->Form->create('UangJalanPayment', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
    		'autocomplete'=> 'off', 
		));
?>
<div class="row">
    <div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Informasi Uang Jalan'); ?></h3>
		    </div>
		    <div class="box-body">
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('nodoc',array(
								'label'=> __('No. Dokumen *'), 
								'class'=>'form-control',
								'required' => false,
								'placeholder' => __('No. Dokumen'),
								'disabled' => $disabled,
							));
					?>
		        </div>
		        <div class="form-group">
	                <?php 
	    					$titleTtuj = __('No. TTUJ * ');

							if( empty($disabled) ) {
		    					$attrBrowse = array(
		                            'class' => 'ajaxModal visible-xs',
		                            'escape' => false,
		                            'title' => __('Data TTUJ'),
		                            'data-action' => 'browse-form',
		                            'data-change' => 'ttujID',
		                            'id' => 'truckBrowse',
		                        );
		    					$urlBrowse = array(
		                            'controller'=> 'ajax', 
		                            'action' => 'getTtujs',
		                            'uang_jalan_payment',
		                            !empty($data_local['Ttuj']['id'])?$data_local['Ttuj']['id']:false,
		                        );
	    						$titleTtuj = __('No. TTUJ * ').$this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse);
		    				}

	                        echo $this->Form->label('ttuj_id', $titleTtuj);

							if( empty($disabled) ) {
	                ?>
	                <div class="row">
	                    <div class="col-sm-10">
				        	<?php 
									echo $this->Form->input('ttuj_id',array(
										'label'=> false, 
										'class'=>'form-control ttuj-invoice-ajax',
										'required' => false,
										'empty' => __('Pilih No. TTUJ --'),
										'div' => array(
											'class' => 'ttuj_id'
										),
										'id' => 'ttujID',
										'data-action' => 'uang_jalan',
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
	                		} else {
	                			$ttuj_id = !empty($invoice['Ttuj']['id'])?$invoice['Ttuj']['id']:false;
	                			$no_ttuj = !empty($invoice['Ttuj']['no_ttuj'])?$invoice['Ttuj']['no_ttuj']:'-';

	                			echo $this->Html->tag('p', $this->Html->link($no_ttuj, array(
	                				'controller' => 'revenues',
	                				'action' => 'ttuj_edit',
	                				$ttuj_id,
                				), array(
                					'target' => '_blank'
                				)));
	                		}
	                ?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('customer',array(
								'label'=> __('Customer'), 
								'class'=>'form-control customer-name',
								'required' => false,
								'disabled' => true,
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->label('from_city', __('Tujuan'));
					?>

		        	<div class="row">
		        		<div class="col-sm-6">
		        			<?php 
									echo $this->Form->input('from_city',array(
										'label'=> false, 
										'class'=>'form-control from-city-name',
										'required' => false,
										'disabled' => true,
									));
							?>
		        		</div>
		        		<div class="col-sm-6">
		        			<?php 
									echo $this->Form->input('to_city',array(
										'label'=> false, 
										'class'=>'form-control to-city-name',
										'required' => false,
										'disabled' => true,
									));
							?>
		        		</div>
		        	</div>
		        </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->label('total_payment', __('Biaya Uang Jalan')); 
                    ?>
                    <div class="input-group">
                        <?php 
                                echo $this->Html->tag('span', Configure::read('__Site.config_currency_code'), array(
                                    'class' => 'input-group-addon'
                                ));
                                echo $this->Form->input('total_payment', array(
                                    'type' => 'text',
                                    'label'=> false, 
                                    'class'=>'form-control input_price total-payment-ttuj',
                                    'required' => false,
                                    'placeholder' => __('Biaya Uang Jalan'),
                                    'disabled' => true,
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
                <h3 class="box-title"><?php echo __('Informasi Pembayaran'); ?></h3>
            </div>
            <div class="box-body">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('date_payment', array(
                                'label'=> __('Tgl Dibayar *'), 
                                'class'=>'form-control custom-date',
                                'type' => 'text',
                                'required' => false,
                                'value' => (!empty($this->request->data['UangJalanPayment']['date_payment'])) ? $this->request->data['UangJalanPayment']['date_payment'] : date('d/m/Y'),
								'disabled' => $disabled,
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('description', array(
                                'label'=> __('Keterangan'), 
                                'class'=>'form-control',
                                'type' => 'textarea',
                                'required' => false,
								'disabled' => $disabled,
                            ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="box-footer text-center action">
	<?php
    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'uang_jalan_payments', 
			), array(
				'class'=> 'btn btn-default',
			));
			if( empty($disabled) ) {
	    		echo $this->Form->button(__('Buat Pembayaran Invoice'), array(
	    			'type' => 'submit',
					'class'=> 'btn btn-success btn-lg',
				));
			}
	?>
</div>
<?php
		echo $this->Form->end();
?>