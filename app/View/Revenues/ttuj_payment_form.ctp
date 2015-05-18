<?php
		switch ($action_type) {
			case 'commission':
				$labelBiaya = __('Komisi');
				$titleCrumb = __('Pembayaran Komisi');
				break;

			case 'uang_kuli_muat':
				$labelBiaya = __('Kuli Muat');
				$titleCrumb = __('Pembayaran Kuli Muat');
				break;

			case 'uang_kuli_bongkar':
				$labelBiaya = __('Kuli Bongkar');
				$titleCrumb = __('Pembayaran Kuli Bongkar');
				break;

			case 'asdp':
				$labelBiaya = __('Biaya Penyebrangan');
				$titleCrumb = __('Pembayaran Biaya Penyebrangan');
				break;

			case 'uang_kawal':
				$labelBiaya = __('Uang Kawal');
				$titleCrumb = __('Pembayaran Uang Kawal');
				break;

			case 'uang_keamanan':
				$labelBiaya = __('Uang Keamanan');
				$titleCrumb = __('Pembayaran Uang Keamanan');
				break;
			
			default:
				$labelBiaya = __('Biaya Uang Jalan');
				$titleCrumb = __('Pembayaran Uang Jalan');
				break;
		}

		$this->Html->addCrumb($titleCrumb, array(
			'controller' => 'revenues',
			'action' => 'ttuj_payments',
			$action_type,
		));
		$this->Html->addCrumb($sub_module_title);
		$disabled = false;
		$receiver_type = false;

		if( !empty($invoice) ) {
			$disabled = true;
		}

		if( !empty($invoice['TtujPayment']['receiver_type']) ) {
			$receiver_type = $this->Common->getReceiverType($invoice['TtujPayment']['receiver_type']);
		}

		echo $this->Form->create('TtujPayment', array(
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
                <h3 class="box-title"><?php echo __('Informasi Pembayaran'); ?></h3>
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
		                            'ttuj_payment',
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
										'data-action' => $action_type,
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
							echo $this->Form->label('receiver', __('Dibayar Kepada').$this->Html->tag('span', $receiver_type, array(
								'id' => 'tag-receiver-type'
							)));

							if( empty($disabled) ) {
								if( in_array($action_type, array( 'commission', 'uang_jalan' )) ) {
									$ajaxType = 'driver';
								} else {
									$ajaxType = 'ttuj';
								}
					?>
					<div class="row">
						<div class="col-sm-10">
							<?php
								echo $this->Form->input('receiver_name',array(
									'label'=> false, 
									'class'=>'form-control',
									'required' => false,
									'id' => 'ttuj-receiver',
									'readonly' => true
								));
							?>
						</div>
						<div class="col-sm-2 hidden-xs">
							<?php 
									$attrBrowse = array(
		                                'class' => 'ajaxModal visible-xs',
		                                'escape' => false,
		                                'title' => __('Dibayar Kepada'),
		                                'data-action' => 'browse-form',
		                                'data-change' => 'ttuj-receiver',
		                            );
		        					$urlBrowse = array(
		                                'controller'=> 'ajax', 
		                                'action' => 'getUserCashBank',
		                                $ajaxType,
		                            );
									$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
		                            echo $this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse);
		                    ?>
						</div>
					</div>
					<?php
							} else {
								echo $this->Form->input('TtujPayment.receiver_name',array(
									'label'=> false, 
									'class'=>'form-control',
									'required' => false,
									'disabled' => true,
								));
							}
					?>
		        </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('date_payment', array(
                                'label'=> __('Tgl Dibayar *'), 
                                'class'=>'form-control custom-date',
                                'type' => 'text',
                                'required' => false,
                                'value' => (!empty($this->request->data['TtujPayment']['date_payment'])) ? $this->Common->customDate($this->request->data['TtujPayment']['date_payment'], 'd/m/Y') : date('d/m/Y'),
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
    <div class="col-sm-6">
		<div class="box box-primary">
		    <div class="box-header">
		        <h3 class="box-title"><?php echo __('Informasi Biaya'); ?></h3>
		    </div>
		    <div class="box-body">
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
							echo $this->Form->input('driver',array(
								'label'=> __('Supir'), 
								'class'=>'form-control driver-name',
								'required' => false,
								'disabled' => true,
								'type' => 'text',
							));
					?>
		        </div>
		        <div class="form-group">
		        	<?php 
							echo $this->Form->input('change_driver',array(
								'label'=> __('Supir Pengganti'), 
								'class'=>'form-control change-driver-name',
								'required' => false,
								'disabled' => true,
								'type' => 'text',
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
                            echo $this->Form->label('total_payment', $labelBiaya); 
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
                                    'placeholder' => $labelBiaya,
                                    'disabled' => true,
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
    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'ttuj_payments', 
				$action_type,
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
		echo $this->Form->input('receiver_type',array(
			'label'=> false, 
			'class'=>'form-control',
			'required' => false,
			'type' => 'hidden',
			'id' => 'receiver-type'
		));
		echo $this->Form->input('receiver_id',array(
			'label'=> false, 
			'class'=>'form-control',
			'required' => false,
			'type' => 'hidden',
			'id' => 'receiver-id'
		));

		echo $this->Form->end();
?>