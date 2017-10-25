<?php 
        $dataColumns = array(
            'nodoc' => array(
                'name' => __('No SPK'),
            ),
            'transaction_date' => array(
                'name' => __('Tgl SPK'),
            ),
            'note_item' => array(
                'name' => __('Ket. Barang'),
            ),
            'code' => array(
                'name' => __('Kode Barang'),
            ),
            'name' => array(
                'name' => __('Nama Barang'),
            ),
            'unit' => array(
                'name' => __('Satuan'),
                'width' => '8%',
                'class' => 'text-center',
            ),
            'qty' => array(
                'name' => __('Qty'),
                'width' => '8%',
                'class' => 'text-center',
            ),
            'note' => array(
                'name' => __('Ket. SPK'),
            ),
            'status' => array(
                'name' => __('Status'),
                'class' => 'text-center',
            ),
            'action' => array(
                'name' => __('Action'),
                'width' => '5%',
                'class' => 'text-left',
            ),
        );

        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
        echo $this->element('blocks/spk/forms/history_search');
?>
<div id="wrapper-sj">
	<div class="box box-primary">
	    <div class="box-header">
	        <h3 class="box-title">
	        	<?php
	        			echo $sub_module_title;
	        	?>
	        </h3>
	    </div>
	    <div class="box-body">
			<div class="box-body table-responsive">
			    <table class="table table-hover">
		            <?php
		                    if( !empty($fieldColumn) ) {
		                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
		                    }
		            ?>
		            <tbody>
		            <?php
		                    if(!empty($values)){
		                        foreach ($values as $key => $value) {
		                            $id = $this->Common->filterEmptyField($value, 'Spk', 'id');
		                            $nodoc = $this->Common->filterEmptyField($value, 'Spk', 'nodoc', '-');
		                            $transactionDate = $this->Common->filterEmptyField($value, 'Spk', 'transaction_date');
		                            $note = $this->Common->filterEmptyField($value, 'Spk', 'note', '-');
		                            $receipt_status = $this->Common->filterEmptyField($value, 'Spk', 'receipt_status', 'none');
		                            $transaction_status = $this->Common->filterEmptyField($value, 'Spk', 'transaction_status');

		                            $code = $this->Common->filterEmptyField($value, 'Product', 'code', '-');
		                            $name = $this->Common->filterEmptyField($value, 'Product', 'name', '-');
		                            $unit = Common::hashEmptyField($value, 'Product.ProductUnit.name', '-');
		                            $note_item = Common::hashEmptyField($value, 'SpkProduct.note', '-');
		                            $qty = Common::hashEmptyField($value, 'SpkProduct.qty', '-');

		                            $customStatus = $this->Common->_callTransactionStatus($value, 'Spk');
		                            $customDate = $this->Common->formatDate($transactionDate, 'd/m/Y');

		                            $customAction = $this->Html->link(__('Lihat'), array(
		                                'controller' => 'spk',
		                                'action' => 'detail',
		                                $id,
		                                'admin' => false,
		                            ), array(
		                                'class' => 'btn btn-info btn-xs'
		                            ));
		            ?>
		            <tr>
		                <?php 
		                        echo $this->Html->tag('td', $nodoc);
		                        echo $this->Html->tag('td', $customDate);
		                        echo $this->Html->tag('td', $note_item);
		                        echo $this->Html->tag('td', $code);
		                        echo $this->Html->tag('td', $name);
		                        echo $this->Html->tag('td', $unit, array(
		                            'class' => 'text-center',
		                        ));
		                        echo $this->Html->tag('td', $qty, array(
		                            'class' => 'text-center',
		                        ));
		                        echo $this->Html->tag('td', $note);
		                        echo $this->Html->tag('td', $customStatus, array(
		                            'class' => 'text-center',
		                        ));

		                        echo $this->Html->tag('td', $customAction, array(
		                            'class' => 'action text-left',
		                        ));
		                ?>
		            </tr>
		            <?php
		                        }
		                    } else {
		                         echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
		                            'class' => 'alert alert-warning text-center',
		                            'colspan' => '10'
		                        )));
		                    }
		            ?>
			    </table>
			</div>
	    </div>
	</div>
</div>