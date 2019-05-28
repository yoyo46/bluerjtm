<?php
		$this->Html->addCrumb(__('Pembayaran Hutang'), array(
			'controller' => 'debt',
			'action' => 'payments',
		));
		$this->Html->addCrumb($sub_module_title);

		$titleBrowse = __('Hutang Dibayar');
		$id = !empty($id)?$id:false;

		echo $this->Form->create('DebtPayment', array(
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
    			if( !empty($value) ) {
					$id = Common::hashEmptyField($value, 'DebtPayment.id');
                    $noref = str_pad($id, 6, '0', STR_PAD_LEFT);


					$contentForm = $this->Html->tag('label', __('No. Referensi'));
					$contentForm .= $this->Html->tag('div', $noref);

					echo $this->Html->tag('div', $contentForm, array(
						'class' => 'form-group',
					));
    			}
    	?>
        <!-- <div class="form-group">
        	<?php 
					// echo $this->Form->input('nodoc',array(
					// 	'label'=> __('No. Dokumen'), 
					// 	'class'=>'form-control',
					// 	'required' => false,
					// 	'placeholder' => __('No. Dokumen'),
					// ));
			?>
        </div> -->
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
                echo $this->element('blocks/common/forms/cost_center');
        ?>
        <div class="form-group">
            <?php 
                    echo $this->Form->input('date_payment', array(
                        'label'=> __('Tgl Dibayar *'), 
                        'class'=>'form-control custom-date',
                        'type' => 'text',
                        'required' => false,
                        'value' => (!empty($this->request->data['DebtPayment']['date_payment'])) ? $this->Common->customDate($this->request->data['DebtPayment']['date_payment'], 'd/m/Y') : date('d/m/Y'),
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
                    ));
            ?>
        </div>
    	<?php 
                if( empty($view) ) {
        			$attrBrowse = array(
                        'class' => 'ajaxModal visible-xs browse-docs',
                        'escape' => false,
                        'data-action' => 'browse-check-docs',
                        'data-change' => 'ttuj-info-table',
                        'url' => $this->Html->url( array(
                            'controller'=> 'debt', 
                            'action' => 'get_debt',
                            'payment_id' => $id,
                            'bypass' => true,
                        )),
                        'title' => sprintf(__('Detail %s'), $titleBrowse),
                    );
    				$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
                    echo $this->Html->tag('div', $this->Html->link('<i class="fa fa-plus-square"></i> '.$titleBrowse, 'javascript:', $attrBrowse), array(
                    	'class' => "form-group",
                	));
                }
        ?>
    </div>
</div>
<?php
		echo $this->element('blocks/debt/tables/detail_payment');
?>
<div class="box-footer text-center action">
	<?php
    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'payments', 
			), array(
				'class'=> 'btn btn-default',
			));

            if( empty($view) ) {
        		echo $this->Form->button(__('Simpan'), array(
        			'type' => 'submit',
    				'class'=> 'btn btn-success btn-lg',
    			));
            }
	?>
</div>
<?php
		echo $this->Form->end();
?>