<?php
		switch ($action_type) {
			case 'biaya_ttuj':
				$titleCrumb = __('Pembayaran Biaya TTUJ');
				break;
			
			default:
				$titleCrumb = __('Pembayaran Uang Jalan/Komisi');
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
        <?php 
        		if( $action_type == 'biaya_ttuj' ) {
        ?>
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
						'disabled' => $disabled,
                    ));
            ?>
        </div>
    	<?php 
    			if( empty($invoice) ) {
        			$attrBrowse = array(
                        'class' => 'ajaxModal visible-xs',
                        'escape' => false,
                        'data-action' => 'browse-check-docs',
                        'data-change' => 'ttuj-info-table',
                        'url' => $this->Html->url( array(
                            'controller'=> 'ajax', 
                            'action' => 'getBiayaTtuj',
                            $action_type
                        )),
                        'title' => $titleCrumb,
                    );
					$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
                    echo $this->Html->tag('div', $this->Html->link('<i class="fa fa-plus-square"></i> '.$titleCrumb, 'javascript:', $attrBrowse), array(
                    	'class' => "form-group",
                	));
				}
        ?>
    </div>
</div>
<div class="checkbox-info-detail <?php echo (!empty($this->request->data['Ttuj'])) ? '' : 'hide';?>">
	<div class="box box-primary">
	    <div class="box-header">
	        <h3 class="box-title"><?php echo __('Detail Biaya Uang Jalan / Komisi'); ?></h3>
	    </div>
	    <div class="box-body table-responsive">
	        <table class="table table-hover">
	        	<thead>
	        		<tr>
	        			<?php 
			                    echo $this->Html->tag('th', __('No TTUJ'));
			                    echo $this->Html->tag('th', __('Tgl'), array(
			                        'width' => '5%',
			                    ));
			                    echo $this->Html->tag('th', __('NoPol'));
			                    echo $this->Html->tag('th', __('Customer'));
			                    echo $this->Html->tag('th', __('Tujuan'));
			                    echo $this->Html->tag('th', __('Supir'));
			                    echo $this->Html->tag('th', __('Jenis'), array(
			                        'width' => '5%',
			                    ));
			                    echo $this->Html->tag('th', __('Total'));
			                    echo $this->Html->tag('th', __('Sisa'), array(
			                        'width' => '15%',
			                    ));
			                    echo $this->Html->tag('th', __('Action'), array(
			                    	'class' => 'hide action-biaya-ttuj',
		                    	));
			            ?>
	        		</tr>
	        	</thead>
                <?php
		    			echo $this->element('blocks/revenues/info_ttuj_payment_detail');
		    	?>
	    	</table>
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
		echo $this->Form->hidden('receiver_id',array(
			'id' => 'receiver-id'
		));
		echo $this->Form->end();
?>