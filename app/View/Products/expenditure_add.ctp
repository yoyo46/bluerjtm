<?php
		$title = __('Pengeluaran Barang');
		$urlRoot = array(
			'controller' => 'products',
			'action' => 'expenditures',
			'admin' => false,
		);
		$value = !empty($value)?$value:false;
		$view = !empty($view)?$view:false;
		$employes = !empty($employes)?$employes:false;

		$this->Html->addCrumb($title, $urlRoot);
		$this->Html->addCrumb($sub_module_title);

        echo $this->element('blocks/cashbanks/tables/list_approvals', array(
        	'urlBack' => false,
        	'modelName' => 'ProductExpenditure',
    	));

		echo $this->Form->create('ProductExpenditure', array(
			'class' => 'expenditure-form',
		));
?>
<div class="box box-primary">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $title,
            ));
    ?>
    <div class="box-body">
    	<?php 
				echo $this->Common->_callInputForm('nodoc', array(
					'label' => 'No. Pengeluaran *',
				));
				echo $this->Common->buildInputForm('transaction_date', __('Tgl Pengeluaran *'), array(
					'type' => 'text',
                    'textGroup' => $this->Common->icon('calendar'),
                    'class' => 'form-control pull-right custom-date',
				));
		?>
		<div class="form-group">
			<?php
				echo $this->Form->label('document_number', __('No. SPK *'));
			?>
			<div class="row">
				<div class="col-sm-10">
					<?php
							echo $this->Common->buildInputForm('ProductExpenditure.document_number', false, array(
								'frameClass' => false,
								'id' => 'document-number',
								'attributes' => array(
									'placeholder' => __('Pilih No. SPK'),
									'readonly' => true,
								),
								'fieldError' => 'ProductExpenditure.document_id',
							));
							echo $this->Html->tag('span', '', array(
								'id' => 'available-date',
							));
					?>
				</div>
				<?php 
						if( empty($view) ) {
				?>
				<div class="col-sm-2 hidden-xs">
					<?php 
							$attrBrowse = array(
	                            'class' => 'ajaxCustomModal btn bg-maroon',
	                            'escape' => false,
	                            'allow' => true,
	                            'title' => __('Ambil Data'),
	                        );
	    					$urlBrowse = array(
	                            'controller'=> 'products', 
	                            'action' => 'expenditure_documents',
	                            'admin' => false,
	                        );
	                        echo $this->Html->link($this->Common->icon('plus-square'), $urlBrowse, $attrBrowse);
	                ?>
				</div>
				<?php 
						}
				?>
			</div>
		</div>
    	<?php 
				echo $this->Common->buildInputForm('note', __('Keterangan'), array(
					'type' => 'textarea',
				));
		?>
	</div>
</div>
<?php     	
        echo $this->element('blocks/products/expenditures/tables/detail_products');
?>
<div class="box-footer text-center action">
	<?php
    		echo $this->Html->link(__('Kembali'), $urlRoot, array(
				'class'=> 'btn btn-default',
			));
			
			if( empty($view) ) {
				$this->Common->_getButtonPostingUnposting( $value, 'ProductExpenditure', array( 'Commit', 'Draft' ) );
			}
	?>
</div>
<?php
		echo $this->Form->hidden('session_id');
		echo $this->Form->end();
?>