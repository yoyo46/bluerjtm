<?php
		$title = __('Supplier Quotation');
		$urlRoot = array(
			'controller' => 'purchases',
			'action' => 'supplier_quotations',
			'admin' => false,
		);

		$this->Html->addCrumb($title, $urlRoot);
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('SupplierQuotation');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $title,
            ));
    ?>
    <div class="box-body">
    	<?php 
				echo $this->Common->buildInputForm('nodoc', __('No Dokumen *'));
				echo $this->Common->buildInputForm('transaction_date', __('Tgl Quotation *'), array(
					'type' => 'text',
                    'textGroup' => $this->Common->icon('calendar'),
                    'class' => 'form-control pull-right custom-date',
				));
				echo $this->Common->buildInputForm('vendor_id', __('Supplier *'), array(
					'empty' => __('- Pilih Supplier -'),
				));
				echo $this->Common->buildInputForm('available_date', __('Tgl Berlaku'), array(
					'type' => 'text',
                    'textGroup' => $this->Common->icon('calendar'),
                    'positionGroup' => 'right',
                    'class' => 'form-control pull-right date-range',
                ));
				echo $this->Common->buildInputForm('note', __('Keterangan'));

                echo $this->Html->tag('div', $this->Html->link($this->Common->icon('plus-square').__(' Ambil Barang'), $this->Html->url( array(
                        'controller'=> 'ajax', 
                        'action' => 'quotation_products',
                        'admin' => false,
                    )), array(
	                    'escape' => false,
	                    'title' => __('Daftar Barang'),
						'class' => 'btn bg-maroon ajaxCustomModal',
	                )), array(
                	'class' => "form-group",
            	));
	    ?>
    </div>
    <?php 
            echo $this->element('blocks/purchases/supplier_quotations/tables/detail_products');
    ?>
    <div class="box-footer text-center action">
    	<?php
	    		echo $this->Form->button(__('Simpan'), array(
					'div' => false, 
					'class'=> 'btn btn-success',
					'type' => 'submit',
				));
	    		echo $this->Html->link(__('Kembali'), $urlRoot, array(
					'class'=> 'btn btn-default',
				));
    	?>
    </div>
</div>
<?php
		echo $this->Form->end();
?>