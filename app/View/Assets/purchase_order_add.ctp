<?php
		$title = __('PO Asset');
		$urlRoot = array(
			'controller' => 'purchases',
			'action' => 'purchase_orders',
			'admin' => false,
		);
		$value = !empty($value)?$value:false;
		$view = !empty($view)?$view:false;

		$this->Html->addCrumb($title, $urlRoot);
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('PurchaseOrder');
?>
<div class="box box-primary">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $title,
            ));
    ?>
    <div class="box-body">
    	<?php 
				// echo $this->Common->buildInputForm('nodoc', __('No Dokumen *'));
				echo $this->Common->buildInputForm('transaction_date', __('Tgl PO *'), array(
					'type' => 'text',
                    'textGroup' => $this->Common->icon('calendar'),
                    'class' => 'form-control pull-right custom-date',
				));
				echo $this->Common->buildInputForm('vendor_id', __('Supplier *'), array(
					'empty' => __('- Pilih Supplier -'),
					'id' => 'supplier-val'
				));
				echo $this->Common->buildInputForm('note', __('Keterangan'), array(
					'empty' => __('- Pilih Supplier -'),
					'id' => 'supplier-val'
				));
		?>
	</div>
</div>
<?php     	
        echo $this->element('blocks/assets/tables/purchase_orders/detail');
?>
<div class="box-footer text-center action">
	<?php
			if( empty($view) ) {
	    		echo $this->Form->button(__('Simpan'), array(
					'div' => false, 
					'class'=> 'btn btn-success',
					'type' => 'submit',
				));
	    	}
	    	
    		echo $this->Html->link(__('Kembali'), $urlRoot, array(
				'class'=> 'btn btn-default',
			));
	?>
</div>
<?php
		echo $this->Form->end();
?>