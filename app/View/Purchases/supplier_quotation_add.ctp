<?php
		$title = __('Penawaran Supplier');
		$urlRoot = array(
			'controller' => 'purchases',
			'action' => 'supplier_quotations',
			'admin' => false,
		);
		$value = !empty($value)?$value:false;
        $status = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'status');
        $transaction_status = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'transaction_status');

        echo $this->element('blocks/cashbanks/tables/list_approvals', array(
        	'urlBack' => false,
        	'modelName' => 'SupplierQuotation',
    	));

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
					'class' => 'form-control chosen-select',
				));
				// echo $this->Common->buildInputForm('available_date', __('Tgl Berlaku *'), array(
				// 	'type' => 'text',
    //                 'textGroup' => $this->Common->icon('calendar'),
    //                 'positionGroup' => 'right',
    //                 'class' => 'form-control pull-right date-range',
    //             ));
		?>
        <div class="form-group">
            <?php
                echo $this->Form->label('available_from', 'Tgl Berlaku'); 
            ?>
            <div class="row">
                <div class="col-sm-6">
                    <?php 
                            echo $this->Form->input('available_from',array(
                            	'type' => 'text',
                                'label'=> false,
                                'class'=>'form-control custom-date',
                                'required' => false,
                                'placeholder' => __('Dari')
                            ));
                    ?>
                </div>
                <div class="col-sm-6">
                    <?php 
                            echo $this->Form->input('available_to',array(
                            	'type' => 'text',
                                'label'=> false,
                                'class'=>'form-control custom-date',
                                'required' => false,
                                'placeholder' => __('Sampai')
                            ));
                    ?>
                </div>
            </div>
        </div>
		<?php
				echo $this->Common->buildInputForm('note', __('Keterangan'));

				if( empty($view) ) {
	                echo $this->Html->tag('div', $this->Html->link($this->Common->icon('plus-square').__(' Ambil Barang'), $this->Html->url( array(
	                        'controller'=> 'ajax', 
	                        'action' => 'products',
	                        'admin' => false,
	                    )), array(
		                    'escape' => false,
		                    'title' => __('Daftar Barang'),
							'class' => 'btn bg-maroon ajaxCustomModal',
		                )), array(
	                	'class' => "form-group",
	            	));
	            }
	    ?>
    </div>
    <?php 
            echo $this->element('blocks/purchases/supplier_quotations/tables/detail_products');
    ?>
    <div class="box-footer text-center action">
    	<?php
	    		echo $this->Html->link(__('Kembali'), $urlRoot, array(
					'class'=> 'btn btn-default',
				));
			
				if( empty($view) ) {
					$this->Common->_getButtonPostingUnposting( $value, 'SupplierQuotation', array( 'Commit', 'Draft' ) );
				}
    	?>
    </div>
</div>
<?php
		echo $this->Form->end();
?>