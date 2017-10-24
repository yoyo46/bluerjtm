<?php
		$title = __('Purchase Order');
		$urlRoot = array(
			'controller' => 'purchases',
			'action' => 'purchase_orders',
			'admin' => false,
		);
		$view = !empty($view)?$view:false;
		$value = !empty($value)?$value:false;
		$status = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'status');

		$this->Html->addCrumb($title, $urlRoot);
		$this->Html->addCrumb($sub_module_title);

        echo $this->element('blocks/cashbanks/tables/list_approvals', array(
        	'urlBack' => false,
        	'modelName' => 'PurchaseOrder',
    	));

		echo $this->Form->create('PurchaseOrder');
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
						echo $this->Common->buildInputForm('nodoc', __('No PO'));
						echo $this->Common->buildInputForm('transaction_date', __('Tgl PO *'), array(
							'type' => 'text',
		                    'textGroup' => $this->Common->icon('calendar'),
		                    'class' => 'form-control pull-right custom-date',
						));
						echo $this->Common->buildInputForm('vendor_id', __('Supplier *'), array(
							'empty' => __('- Pilih Supplier -'),
							'id' => 'supplier-val',
							'class' => 'form-control chosen-select ajax-change',
							'attributes' => array(
								'data-wrapper-write' => '.wrapper-supplier',
								'href' => $this->Html->url(array(
									'controller' => 'ajax',
									'action' => 'get_supplier_top',
									'model_name' => 'PurchaseOrder',
								)),
								'data-empty' => '.temp-document-picker table tbody',
							),
						));

						/*
				?>
				<div class="form-group">
					<?php
						echo $this->Form->label('no_sq', __('No. SQ'));
					?>
					<div class="row">
						<div class="col-sm-10">
							<?php
									echo $this->Common->buildInputForm('no_sq', false, array(
										'frameClass' => false,
										'id' => 'supplier-quotation',
										'attributes' => array(
											'placeholder' => __('No. Penawaran Supplier'),
											'readonly' => true,
										),
									));
									echo $this->Html->tag('span', '', array(
										'id' => 'available-date',
									));
							?>
						</div>
						<?php 
								if( $view != 'detail' ) {
						?>
						<div class="col-sm-2 hidden-xs">
							<?php 
									$attrBrowse = array(
		                                'class' => 'ajaxModal visible-xs browse-docs',
		                                'escape' => false,
		                                'title' => __('Data Penawaran Supplier'),
		                                'data-action' => 'browse-form',
		                                'data-check' => '#supplier-val',
		                                'data-check-title' => 'supplier',
		                            );
		        					$urlBrowse = array(
		                                'controller'=> 'ajax', 
		                                'action' => 'supplier_quotations'
		                            );
									$attrBrowse['class'] = 'btn bg-maroon ajaxModal';
		                            echo $this->Html->link('<i class="fa fa-plus-square"></i>', $urlBrowse, $attrBrowse);
		                    ?>
						</div>
						<?php
								}
						?>
					</div>
				</div>
				<?php 
						*/
				
						echo $this->Common->buildRadioForm('ppn_include', __('Jenis Pajak'), array(
							'options' => array(
								0 => __('Normal'),
								1 => __('PPN Include'),
							),
							'default' => 0,
							'class' => 'ppn_include',
						));
				?>
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="box box-warning">
		    <?php 
		            echo $this->element('blocks/common/box_header', array(
		                'title' => __('Informasi Tambahan'),
		            ));
		    ?>
		    <div class="box-body">
		    	<?php 
						echo $this->Common->buildInputForm('etd', __('E.T.D'), array(
							'type' => 'text',
							'textGroup' => __('Hari'),
							'column' => 'col-sm-6',
						));
						echo $this->element('blocks/purchases/purchase_orders/get_supplier_top', array(
							'model_name' => 'PurchaseOrder',
						));
						echo $this->Common->buildInputForm('note', __('Keterangan'));
			    ?>
		    </div>
		</div>
	</div>
</div>
<?php     	
        echo $this->element('blocks/purchases/purchase_orders/tables/detail_products');
?>
<div class="box-footer text-center action">
	<?php
			echo $this->Html->link(__('Kembali'), $urlRoot, array(
				'class'=> 'btn btn-default',
			));
			
			if( $view != 'detail' ) {
				$this->Common->_getButtonPostingUnposting( $value, 'PurchaseOrder', array( 'Commit', 'Draft' ) );
			}
	?>
</div>
<?php
		echo $this->Form->end();
?>