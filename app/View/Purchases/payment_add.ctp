<?php
		$title = __('Pembayaran PO');
		$urlRoot = array(
			'controller' => 'purchases',
			'action' => 'payments',
			'admin' => false,
		);
		$value = !empty($value)?$value:false;
		$view = !empty($view)?$view:false;

		$this->Html->addCrumb($title, $urlRoot);
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('PurchaseOrderPayment');
?>
<div class="box box-primary">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $title,
            ));
    ?>
    <div class="box-body">
    	<?php 
				echo $this->Common->buildInputForm('transaction_date', __('Tgl Dibayar *'), array(
					'type' => 'text',
                    'textGroup' => $this->Common->icon('calendar'),
                    'class' => 'form-control pull-right custom-date',
				));
				echo $this->Common->buildInputForm('coa_id', __('Account Kas/Bank *'), array(
					'empty' => __('- Pilih Kas/Bank -'),
					'class' => 'form-control chosen-select',
				));
				echo $this->Common->buildInputForm('vendor_id', __('Supplier *'), array(
					'empty' => __('- Pilih Supplier -'),
					'id' => 'supplier-val',
				));
				echo $this->Common->buildInputForm('note', __('Keterangan'));
		?>
	</div>
</div>
<?php     	
        echo $this->element('blocks/purchases/payments/tables/detail');
?>
<div class="box-footer text-center action">
	<?php
    		echo $this->Html->link(__('Kembali'), $urlRoot, array(
				'class'=> 'btn btn-default',
			));

			if( empty($view) ) {
				$this->Common->_getButtonPostingUnposting( $value, 'PurchaseOrderPayment', array( 'Commit', 'Draft' ) );
			}
	?>
</div>
<?php
		echo $this->Form->end();
?>