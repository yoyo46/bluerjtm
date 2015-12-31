<?php
		echo $this->element('blocks/revenues/ttuj_payment_crumb');
		$this->Html->addCrumb($sub_module_title);

		$receiver_type = false;
		$receiver_label = false;

		if( !empty($this->request->data['TtujPayment']['receiver_type']) ) {
			$receiver_type = $this->request->data['TtujPayment']['receiver_type'];
		} else if( !empty($invoice['TtujPayment']['receiver_type']) ) {
			$receiver_type = $invoice['TtujPayment']['receiver_type'];
		}

		if( !empty($receiver_type) ) {
			$receiver_label = $this->Common->getReceiverType($receiver_type);
		}

		echo $this->Form->create('TtujPayment', array(
			'url'=> $this->Html->url( null, true ), 
			'role' => 'form',
			'inputDefaults' => array('div' => false),
    		'autocomplete'=> 'off', 
		));
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Informasi Pembayaran'); ?></h3>
    </div>
    <div class="box-body">
    	<?php 
    			if( !empty($invoice) ) {
					$id = $this->Common->filterEmptyField($invoice, 'TtujPayment', 'id');
                    $noref = str_pad($id, 6, '0', STR_PAD_LEFT);

					echo $this->Form->input('id',array(
						'type' => 'text',
						'label'=> __('No. Referensi'), 
						'class'=>'form-control',
						'required' => false,
						'disabled' => true,
						'div' => array(
							'class' => 'form-group',
						),
						'value' => $noref,
					));
    			}
    	?>
        <div class="form-group">
        	<?php 
					echo $this->Form->input('nodoc',array(
						'label'=> __('No. Dokumen *'), 
						'class'=>'form-control',
						'required' => false,
						'placeholder' => __('No. Dokumen'),
					));
			?>
        </div>
        <?php 
				echo $this->Html->tag('div', $this->Form->input('coa_id',array(
					'label'=> __('Account Kas/Bank *'), 
					'class'=>'form-control chosen-select',
					'required' => false,
					'empty' => __('Pilih Kas/Bank '),
					'options' => !empty($coas)?$coas:false,
				)), array(
					'class' => 'form-group'
				));
        ?>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('date_payment', array(
                        'label'=> __('Tgl Dibayar *'), 
                        'class'=>'form-control custom-date',
                        'type' => 'text',
                        'required' => false,
                        'value' => (!empty($this->request->data['TtujPayment']['date_payment'])) ? $this->Common->customDate($this->request->data['TtujPayment']['date_payment'], 'd/m/Y') : date('d/m/Y'),
                    ));
            ?>
        </div>
        <?php 
        		if( $action_type == 'biaya_ttuj' ) {
        ?>
        <div class="form-group">
        	<?php 
					echo $this->Form->label('receiver', __('Dibayar Kepada').$this->Html->tag('span', $receiver_label, array(
						'id' => 'tag-receiver-type'
					)));
					echo $this->Form->hidden('receiver_type', array(
						'value' => $receiver_type,
						'id' => 'hid-receiver-type'
					));

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
                                'class' => 'ajaxModal visible-xs browse-docs',
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
        </div>
        <?php 
        		}
        ?>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('description', array(
                        'label'=> __('Keterangan'), 
                        'class'=>'form-control',
                        'type' => 'textarea',
                        'required' => false,
                    ));
            ?>
        </div>
    	<?php 
    			$attrBrowse = array(
                    'class' => 'ajaxModal visible-xs browse-docs',
                    'escape' => false,
                    'data-action' => 'browse-check-docs',
                    'data-change' => 'ttuj-info-table',
                    'url' => $this->Html->url( array(
                        'controller'=> 'ajax', 
                        'action' => 'getBiayaTtuj',
                        $action_type
                    )),
                    'title' => sprintf(__('Detail %s'), $titleBrowse),
                );
				$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
                echo $this->Html->tag('div', $this->Html->link('<i class="fa fa-plus-square"></i> '.$titleBrowse, 'javascript:', $attrBrowse), array(
                	'class' => "form-group",
            	));
        ?>
    </div>
</div>
<?php
		echo $this->element('blocks/revenues/tables/detail_ttuj_payment');
?>
<div class="box-footer text-center action">
	<?php
    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'ttuj_payments', 
				$action_type,
			), array(
				'class'=> 'btn btn-default',
			));
    		echo $this->Form->button(__('Simpan'), array(
    			'type' => 'submit',
				'class'=> 'btn btn-success btn-lg',
			));
	?>
</div>
<?php
		echo $this->Form->hidden('receiver_id',array(
			'id' => 'receiver-id'
		));
		echo $this->Form->end();
?>