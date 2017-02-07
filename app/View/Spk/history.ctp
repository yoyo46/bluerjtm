<?php 
        $dataColumns = array(
            'nodoc' => array(
                'name' => __('No SPK'),
            ),
            'transaction_date' => array(
                'name' => __('Tgl SPK'),
            ),
            'document_type' => array(
                'name' => __('Jenis'),
            ),
            'vendor' => array(
                'name' => __('Supplier'),
            ),
            'kepala_mekanik' => array(
                'name' => __('Kepala Mekanik'),
                'width' => '13%',
            ),
            'mekanik' => array(
                'name' => __('Mekanik'),
                'width' => '15%',
            ),
            'est' => array(
                'name' => __('Estimasi'),
            ),
            'finish' => array(
                'name' => __('Tgl Selesai'),
            ),
            'note' => array(
                'name' => __('Keterangan'),
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
		                            $document_type = $this->Common->filterEmptyField($value, 'Spk', 'document_type');
		                            $estimation_date = $this->Common->filterEmptyField($value, 'Spk', 'estimation_date', false, true, array(
		                                'date' => 'd/m/Y',
		                            ));
		                            $complete_date = $this->Common->filterEmptyField($value, 'Spk', 'complete_date', false, true, array(
		                                'date' => 'd/m/Y',
		                            ));
		                            $receipt_status = $this->Common->filterEmptyField($value, 'Spk', 'receipt_status', 'none');
		                            $transaction_status = $this->Common->filterEmptyField($value, 'Spk', 'transaction_status');

		                            $mechanics = Common::hashEmptyField($value, 'SpkMechanic');
									$mechanics = Set::extract('/Employe/full_name', $mechanics);
									$mechanics = implode(', ', $mechanics);

		                            $document_type = ucwords($document_type);
		                            $vendor = $this->Common->filterEmptyField($value, 'Vendor', 'name', '-');

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
		                        echo $this->Html->tag('td', $document_type);
		                        echo $this->Html->tag('td', $vendor);
		                        echo $this->Html->tag('td', Common::hashEmptyField($value, 'Employe.full_name'));
		                        echo $this->Html->tag('td', $mechanics);
		                        echo $this->Html->tag('td', $estimation_date);
		                        echo $this->Html->tag('td', $complete_date);
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