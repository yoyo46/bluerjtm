<?php
		$this->Html->addCrumb(__('Pembayaran Surat-surat Truk'), array(
			'controller' => 'trucks',
			'action' => 'document_payments',
		));
		$this->Html->addCrumb($sub_module_title);

		$titleBrowse = __('Biaya Surat-surat Truk');
		$id = !empty($id)?$id:false;

		echo $this->Form->create('DocumentPayment', array(
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
					$id = $this->Common->filterEmptyField($value, 'DocumentPayment', 'id');
                    $noref = str_pad($id, 6, '0', STR_PAD_LEFT);


					$contentForm = $this->Html->tag('label', __('No. Referensi'));
					$contentForm .= $this->Html->tag('div', $noref);

					echo $this->Html->tag('div', $contentForm, array(
						'class' => 'form-group',
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
                        'value' => (!empty($this->request->data['DocumentPayment']['date_payment'])) ? $this->Common->customDate($this->request->data['DocumentPayment']['date_payment'], 'd/m/Y') : date('d/m/Y'),
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
    			$attrBrowse = array(
                    'class' => 'ajaxModal visible-xs browse-docs',
                    'escape' => false,
                    'data-action' => 'browse-check-docs',
                    'data-change' => 'ttuj-info-table',
                    'url' => $this->Html->url( array(
                        'controller'=> 'ajax', 
                        'action' => 'getDocumentTrucks',
                        'payment_id' => $id,
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
		echo $this->element('blocks/trucks/tables/detail_document_payment');
?>
<div class="box-footer text-center action">
	<?php
    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'document_payments', 
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
		echo $this->Form->end();
?>