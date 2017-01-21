<?php
		$title = __('SPK');
		$urlRoot = array(
			'controller' => 'spk',
			'action' => 'index',
			'admin' => false,
		);

        $data = $this->request->data;
		$view = !empty($view)?$view:false;

		$mechanicClass = $this->Spk->_callDisplayToggle('mechanic', $data);
		$whtClass = $this->Spk->_callDisplayToggle('wht', $data);

		$this->Html->addCrumb($title, $urlRoot);
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Spk', array(
			'class' => 'receipt-form',
			'id' => 'form-spk',
		));
?>
<div class="row">
	<div class="col-sm-6">
		<div class="box box-primary">
		    <?php 
		            echo $this->element('blocks/common/box_header', array(
		                'title' => $title,
		            ));
		    ?>
		    <div class="box-body">
		    	<?php 
						echo $this->Common->buildInputForm('nodoc', __('No. SPK *'), array(
		                    'class' => 'form-control',
						));
						echo $this->Common->buildInputForm('transaction_date', __('Tgl SPK *'), array(
							'type' => 'text',
		                    'textGroup' => $this->Common->icon('calendar'),
		                    'class' => 'form-control pull-right custom-date',
						));
						echo $this->Common->_callInputForm('document_type', array(
							'label' => __('Jenis SPK *'),
		                    'class' => 'form-control handle-toggle',
		                    'empty' => __('Pilih Jenis SPK'),
		                    'options' => Configure::read('__Site.Spk.type'),
					    	'data-match' => '[[\'.wrapper-mechanic\', [\'internal\',\'production\'], \'slide\'],[\'.wrapper-wht\', [\'wht\'], \'slide\'],[\'.wrapper-eksternal\', [\'eksternal\'], \'fade\']]',
						));

						echo $this->Html->tag('div',
							$this->Common->_callInputForm('employe_id', array(
								'label' => __('Kepala Mekanik *'), 
								'type' => 'select',
								'empty' => __('- Pilih Penerima -'),
								'class' => 'form-control chosen-select',
								'div' => 'form-group',
							)).
							$this->Common->_callInputForm('SpkMechanic.employe_id', array(
								'type' => 'select',
								'label' => __('Mekanik *'),
	                            'class'=>'form-control chosen-select',
	                            'multiple' => true,
	                            'fieldError' => 'Spk.mechanic',
							)), array(
							'class' => __('wrapper-mechanic select-block %s', $mechanicClass),
						));
						echo $this->Common->_callInputForm('start', array(
							'type' => 'datetime',
							'label' => __('Tgl Mulai *'),
						));
						echo $this->Common->_callInputForm('estimation', array(
							'type' => 'datetime',
							'label' => __('Estimasi Penyelesaian *'),
						));
						echo $this->Common->_callInputForm('complete', array(
							'type' => 'datetime',
							'label' => __('Tgl Selesai *'),
						));
				?>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-warning">
		    <?php 
		            echo $this->element('blocks/common/box_header', array(
		                'title' => __('Informasi Dokumen'),
		            ));
		    ?>
		    <div class="box-body">
		    	<?php 
						// echo $this->Common->_callInputForm('transaction_status', array(
						// 	'label' => __('Status *'),
						// 	'empty' => __('- Pilih Status -'),
						// 	'options' => array(
						// 		'open' => __('Open'),
						// 		'closed' => __('Closed'),
						// 	),
						// ));
						echo $this->element('blocks/common/forms/input_pickup', array(
							'fieldName' => 'nopol',
							'label' => __('No. Pol *'),
							'dataUrl' => array(
								'controller' => 'ajax',
								'action' => 'truck_picker',
								'return_value' => 'nopol',
							),
							'onchange' => 'false',
						));
						echo $this->Common->_callInputForm('vendor_id', array(
							'label' => __('Vendor *'),
							'empty' => __('- Pilih Vendor -'),
                            'class'=>'form-control chosen-select',
						));
						echo $this->Common->_callInputForm('to_branch_id', array(
							'label' => __('Gudang Penerima *'),
							'empty' => __('- Pilih Gudang Penerima -'),
                            'class'=>'form-control chosen-select',
                            'div' => 'form-group select-block',
                            'frameClass' => __('wrapper-wht %s', $whtClass),
						));
						echo $this->Common->buildInputForm('note', __('Keterangan'));
			    ?>
		    </div>
		</div>
	</div>
</div>
<?php     	
        echo $this->element('blocks/spk/tables/detail_products');
        echo $this->Html->tag('div', $this->element('blocks/spk/tables/detail_production'), array(
			'class' => __('wrapper-wht %s', $whtClass),
    	));
?>
<div class="box-footer text-center action">
	<?php
    		echo $this->Html->link(__('Kembali'), $urlRoot, array(
				'class'=> 'btn btn-default',
			));

			if( empty($view) ) {
				echo $this->Form->button(__('Simpan'), array(
					'type' => 'submit',
					'class'=> 'btn btn-success submit-form btn-lg',
				));

				if( !empty($id) ) {
					echo $this->Html->link(__('Finish'), array(
						'controller' => 'spk',
						'action' => 'completed',
						'admin' => false,
					), array(
						'class'=> 'btn btn-primary submit-form',
					));
				}
			}
	?>
</div>
<?php
		echo $this->Form->hidden('session_id');
		echo $this->Form->end();
?>