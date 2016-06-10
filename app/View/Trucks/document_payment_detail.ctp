<?php 
		$this->Html->addCrumb(__('Pembayaran Surat-surat Truk'), array(
			'controller' => 'trucks',
			'action' => 'document_payments',
		));
		$this->Html->addCrumb($sub_module_title);

		$id = $this->Common->filterEmptyField($value, 'DocumentPayment', 'id');
		$nodoc = $this->Common->filterEmptyField($value, 'DocumentPayment', 'nodoc');
		$date_payment = $this->Common->filterEmptyField($value, 'DocumentPayment', 'date_payment');
		$description = $this->Common->filterEmptyField($value, 'DocumentPayment', 'description', false, true, 'EOL');
		$coa = $this->Common->filterEmptyField($value, 'Coa', 'coa_name');
		
        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
        $datePayment = $this->Common->customDate($date_payment, 'd/m/Y');

        $dataColumns = array(
            'noref' => array(
                'name' => __('No. Ref'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'noref\',width:80',
            ),
            'nopol' => array(
                'name' => __('NoPol'),
                'style' => 'text-align: center;left: 80px;vertical-align: middle;',
                'data-options' => 'field:\'nopol\',width:80',
            ),
            'type' => array(
                'name' => __('Jenis Surat'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'type\',width:100',
            ),
            'document_date' => array(
                'name' => __('Tgl Perpanjang'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'document_date\',width:80',
                'align' => 'center',
                'mainalign' => 'center',
            ),
            'expired_date' => array(
                'name' => __('Tgl Berakhir'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'expired_date\',width:80',
                'align' => 'center',
                'mainalign' => 'center',
            ),
            'to_date' => array(
                'name' => __('Perpanjang Hingga'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'to_date\',width:80',
                'align' => 'center',
                'mainalign' => 'center',
                'fix_column' => true,
            ),
            'price_estimate' => array(
                'name' => __('Estimasi Biaya'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'price_estimate\',width:120',
                'align' => 'right',
                'mainalign' => 'center',
            ),
            'price' => array(
                'name' => __('Biaya Perpanjang'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'price\',width:120',
                'align' => 'right',
                'mainalign' => 'center',
            ),
            'denda' => array(
                'name' => __('Denda'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'denda\',width:100',
                'align' => 'right',
                'mainalign' => 'center',
            ),
            'biaya_lain' => array(
                'name' => __('Biaya Lain2'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'biaya_lain\',width:120',
                'align' => 'right',
                'mainalign' => 'center',
            ),
            'total' => array(
                'name' => __('Total'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'total\',width:120',
                'align' => 'right',
                'mainalign' => 'center',
            ),
            'note' => array(
                'name' => __('Keterangan'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'note\',width:150',
                'align' => 'left',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Informasi Pembayaran');?></h3>
    </div>
	<div class="box-body table-responsive">
	    <table class="table table-hover">
			<tr>
				<th><?php echo __('No. Referensi');?></th>
				<td><?php echo $noref;?></td>
			</tr>
			<tr>
				<th><?php echo __('No. Dokumen');?></th>
				<td><?php echo $nodoc;?></td>
			</tr>
			<tr>
				<th><?php echo __('Account Kas/Bank');?></th>
				<td><?php echo $coa;?></td>
			</tr>
			<tr>
				<th><?php echo __('Tgl Dibayar');?></th>
				<td><?php echo $datePayment;?></td>
			</tr>
			<tr>
				<th><?php echo __('Keterangan');?></th>
				<td><?php echo $description;?></td>
			</tr>
		</table>
	</div>
</div>
<div class="checkbox-info-detail">
	<div class="box box-primary">
	    <div class="box-header">
	        <h3 class="box-title"><?php echo __('Detail Biaya Dokumen'); ?></h3>
	    </div>
	    <div class="box-body table-responsive">
        	<table id="tt" class="table table-bordered easyui-datagrid" style="width: 100%;height: 550px;" singleSelect="true">
	        	<thead frozen="true">
	                <tr>
	                    <?php
	                            if( !empty($fieldColumn) ) {
	                                echo $fieldColumn;
	                            }
	                    ?>
	                </tr>
	            </thead>
                <tbody id="checkbox-info-table">
					<?php
							$grandTotal = 0;

							if(!empty($value['DocumentPaymentDetail'])){
								foreach ($value['DocumentPaymentDetail'] as $key => $val) {
									$id = $this->Common->filterEmptyField($val, 'DocumentPaymentDetail', 'document_id');
					                $document_type = $this->Common->filterEmptyField($val, 'DocumentPaymentDetail', 'document_type');
					                $amount = $this->Common->filterEmptyField($val, 'DocumentPaymentDetail', 'amount');
					                $modelName = $this->Truck->_callDocumentType($document_type);

					                $nopol = $this->Common->filterEmptyField($val, $modelName, 'no_pol');
					                $to_date = $this->Common->filterEmptyField($val, $modelName, 'to_date');
					                $price = $this->Common->filterEmptyField($val, $modelName, 'price');
					                $denda = $this->Common->filterEmptyField($val, $modelName, 'denda');
					                $biaya_lain = $this->Common->filterEmptyField($val, $modelName, 'biaya_lain');
					                $price_estimate = $this->Common->filterEmptyField($val, $modelName, 'price_estimate');
					                $note = $this->Common->filterEmptyField($val, $modelName, 'note');

									$grandTotal += $amount;
					                
					                switch ($document_type) {
					                    case 'stnk':
					                        $type = __('STNK 1 Thn');
					                		$document_date = $this->Common->filterEmptyField($val, $modelName, 'tgl_bayar');
					                		$expired_date = $this->Common->filterEmptyField($val, $modelName, 'from_date', false, false, array(
					                			'date' => 'd/m/Y',
				                			));
					                        break;
					                    case 'stnk_5_thn':
					                        $type = __('STNK 5 Thn');
					                		$document_date = $this->Common->filterEmptyField($val, $modelName, 'tgl_bayar');
					                		$expired_date = $this->Common->filterEmptyField($val, $modelName, 'from_date', false, false, array(
					                			'date' => 'd/m/Y',
				                			));
					                        break;
					                    case 'siup':
					                        $type = ucwords($document_type);
					                		$document_date = $this->Common->filterEmptyField($val, $modelName, 'tgl_siup');
					                		$expired_date = $this->Common->filterEmptyField($val, $modelName, 'from_date', false, false, array(
					                			'date' => 'd/m/Y',
				                			));
					                        break;
					                    default:
					                        $type = ucwords($document_type);
					                		$document_date = $this->Common->filterEmptyField($val, $modelName, 'tgl_kir');
					                		$expired_date = $this->Common->filterEmptyField($val, $modelName, 'from_date', false, false, array(
					                			'date' => 'd/m/Y',
				                			));
					                        break;
					                }

					                $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
					                $to_date = $this->Common->formatDate($to_date, 'd/m/Y');
					                $document_date = $this->Common->formatDate($document_date, 'd/m/Y');
					                $customPrice = $this->Common->getFormatPrice($price);
					                $customDenda = $this->Common->getFormatPrice($denda);
					                $customBiayaLain = $this->Common->getFormatPrice($biaya_lain);
					                $customPriceEstimate = $this->Common->getFormatPrice($price_estimate);
					                $customAmount = $this->Common->getFormatPrice($amount);

					                $checkbox = isset($checkbox)?$checkbox:true;
					                $alias = sprintf('child-%s-%s', $id, $document_type);

					                $documentPayment = $this->Common->filterEmptyField($this->request->data, 'DocumentPayment');
				    ?>
				    <tr class="child child-<?php echo $alias; ?>">
				        <td><?php echo $noref;?></td>
				        <td><?php echo $nopol;?></td>
				        <td><?php echo $type;?></td>
				        <td class="text-center"><?php echo $document_date;?></td>
				        <td class="text-center"><?php echo $expired_date;?></td>
				        <td class="text-center"><?php echo $to_date;?></td>
				        <td class="text-right"><?php echo $customPriceEstimate;?></td>
				        <td class="text-right"><?php echo $customPrice;?></td>
				        <td class="text-right"><?php echo $customDenda;?></td>
				        <td class="text-right"><?php echo $customBiayaLain;?></td>
				        <td class="text-right">
				            <?php
				                    echo $customAmount;
				            ?>
				        </td>
				        <td><?php echo $note;?></td>
				    </tr>
				    <?php
								}
							}
					?>
				</tbody>
				<tr>
					<?php 
							echo $this->Html->tag('td', '');
							echo $this->Html->tag('td', '');
							echo $this->Html->tag('td', '');
							echo $this->Html->tag('td', '');
							echo $this->Html->tag('td', '');
							echo $this->Html->tag('td', '');
							echo $this->Html->tag('td', '');
							echo $this->Html->tag('td', '');
							echo $this->Html->tag('td', '');
							echo $this->Html->tag('td', $this->Html->tag('strong', __('Total')), array(
								'class' => 'bold text-right',
							));
							echo $this->Html->tag('td', $this->Html->tag('strong', $this->Common->getFormatPrice($grandTotal)), array(
								'class' => 'text-right',
								'id' => 'total-biaya',
							));
					?>
				</tr>

	    	</table>
	    </div>
	</div>
</div>
<div class="box-footer text-center action">
	<?php
    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'document_payments', 
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>