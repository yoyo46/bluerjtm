<?php
		$title = __('SPK');
		$urlRoot = array(
			'controller' => 'spk',
			'action' => 'index',
			'admin' => false,
		);
		$value = !empty($value)?$value:false;
		$view = !empty($view)?$view:false;

		$this->Html->addCrumb($title, $urlRoot);
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Spk', array(
			'class' => 'receipt-form',
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
						echo $this->Common->buildInputForm('document_type', __('Jenis SPK *'), array(
		                    'class' => 'form-control',
		                    'empty' => __('Pilih Jenis SPK'),
		                    'options' => array(
						    	'internal' => __('Internal'),
						    	'eksternal' => __('Eksternal'),
						    	'wht' => __('WHT'),
						    	'production' => __('Produksi'),
					    	),
						));
						echo $this->Common->buildInputForm('employe_id', __('Kepala Mekanik *'), array(
							'type' => 'select',
							'empty' => __('- Pilih Penerima -'),
							'class' => 'form-control chosen-select',
						));
						echo $this->Common->_callInputForm('SpkMechanic.employe_id', array(
							'type' => 'select',
							'label' => __('Mekanik *'),
							'empty' => __('- Pilih Mekanik -'),
                            'class'=>'form-control chosen-select',
                            'multiple' => true,
                            'fieldError' => 'Spk.mechanic',
						));
						echo $this->Common->_callInputForm('start', array(
							'type' => 'datetime',
							'label' => __('Tgl Mulai'),
						));
						echo $this->Common->_callInputForm('estimation', array(
							'type' => 'datetime',
							'label' => __('Estimasi Penyelesaian'),
						));
						echo $this->Common->_callInputForm('complete', array(
							'type' => 'datetime',
							'label' => __('Tgl Selesai'),
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
						echo $this->Common->_callInputForm('transaction_status', array(
							'label' => __('Status *'),
							'empty' => __('- Pilih Status -'),
							'options' => array(
								'open' => __('Open'),
								'closed' => __('Closed'),
							),
						));
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
						));
						echo $this->Common->buildInputForm('note', __('Keterangan'));
			    ?>
		    </div>
		</div>
	</div>
</div>
<?php     	
        echo $this->element('blocks/spk/tables/detail_products');
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