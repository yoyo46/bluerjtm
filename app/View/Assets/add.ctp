<?php
		$title = __('Asset');
		$urlRoot = array(
			'action' => 'index',
		);
		$value = !empty($value)?$value:false;
		$data = $this->request->data;

		$this->Html->addCrumb($title, $urlRoot);
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('Asset');
?>
<div class="row">
	<div class="col-sm-6">
		<div class="box">
		    <div class="box-body">
		    	<?php 
						echo $this->element('blocks/common/forms/input_pickup', array(
							'fieldName' => 'name',
							'label' => __('Nama Asset/Truk'),
							'dataUrl' => array(
								'controller' => 'ajax',
								'action' => 'truck_picker',
								'return_value' => 'nopol',
							),
							'readonly' => false,
							'onchange' => 'false',
						));
						echo $this->Common->buildInputForm('asset_group_id', __('Group Asset *'), array(
							'empty' => __('Pilih Group Asset'),
							'class' => 'ajax-change form-control asset_group',
							'attributes' => array(
								'data-wrapper-write' => '#asset-group-content',
								'href' => $this->Html->url(array(
									'action' => 'get_asset_group',
								)),
								'data-function' => 'calcDeprBulan',
							),
						));
						echo $this->Common->buildInputForm('purchase_date', __('Tanggal pembelian *'), array(
							'type' => 'text',
							'class' => 'custom-date form-control',
						));
						echo $this->Common->buildInputForm('neraca_date', __('Tanggal neraca *'), array(
							'type' => 'text',
							'class' => 'custom-date form-control',
						));
						echo $this->Common->buildInputForm('note', __('Keterangan'));
						echo $this->element('blocks/assets/group', array(
							'value' => $data,
						));
			    ?>
		    </div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box">
		    <div class="box-body">
		    	<?php 
						echo $this->Common->buildInputForm('lokasi', __('Lokasi'));
						echo $this->Common->buildInputForm('nilai_perolehan', __('Nilai perolehan *'), array(
							'type' => 'text',
							'class' => 'input_price_coma form-control nilai_perolehan',
							'attributes' => array(
								'data-function' => 'calcDeprBulan',
							),
						));
						echo $this->Common->buildInputForm('depr_bulan', __('Depr/bulan *'), array(
							'type' => 'text',
							'class' => 'input_price_coma form-control depr_bulan',
							'attributes' => array(
								'data-function' => 'calcDeprBulan',
								'data-function-type' => 'depr_bulan',
							),
						));
						echo $this->Common->buildInputForm('ak_penyusutan', __('Ak. Penyusutan *'), array(
							'type' => 'text',
							'class' => 'input_price_coma form-control ak_penyusutan',
							'attributes' => array(
								'data-function' => 'calcDeprBulan',
								'data-function-type' => 'ak_penyusutan',
							),
						));
						echo $this->Common->buildInputForm('nilai_buku', __('Nilai Buku *'), array(
							'type' => 'text',
							'class' => 'input_price_coma form-control nilai_buku',
							'readonly' => true,
						));
			    ?>
		    </div>
		</div>
	</div>
</div>
<?php 
        echo $this->element('blocks/common/forms/submit_action', array(
            'urlBack' => $urlRoot,
        ));
		echo $this->Form->end();
?>