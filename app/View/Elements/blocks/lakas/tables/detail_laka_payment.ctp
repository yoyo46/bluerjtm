<div class="checkbox-info-detail <?php echo (!empty($this->request->data['Laka'])) ? '' : 'hide';?>">
	<div class="box box-primary">
	    <div class="box-header">
	        <h3 class="box-title"><?php echo __('Detail Biaya Dokumen'); ?></h3>
	    </div>
	    <div class="box-body table-responsive">
	        <table class="table table-hover">
	        	<thead>
	        		<tr>
	        			<?php 
			                    echo $this->Html->tag('th', __('No. Ref'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('NoPol'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Jenis Surat'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Tgl Berakhir'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Tgl Perpanjang'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Estimasi Biaya'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Biaya Perpanjang'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Denda'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Biaya Lain2'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Total'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Keterangan'), array(
			                        'class' => 'text-center',
			                        'width' => '10%',
			                    ));
            					
            					if( empty($document_info) ) {
				                    echo $this->Html->tag('th', __('Action'), array(
				                    	'class' => 'action-biaya-document',
			                    	));
				                }
			            ?>
	        		</tr>
	        	</thead>
                <tbody id="checkbox-info-table">
					<?php
							$grandTotal = 0;
							$data = $this->request->data;

							if(!empty($data['Laka'])){
								foreach ($data['Laka'] as $key => $value) {
									$id = $this->Common->filterEmptyField($value, 'Laka', 'id');
					                $nopol = $this->Common->filterEmptyField($value, 'Laka', 'no_pol');
					                $nopol = $this->Common->filterEmptyField($value, 'Laka', 'nopol', $nopol);
					                $data_type = $this->Common->filterEmptyField($value, 'Laka', 'data_type');
					                $to_date = $this->Common->filterEmptyField($value, 'Laka', 'to_date');
					                $price = $this->Common->filterEmptyField($value, 'Laka', 'price');
					                $denda = $this->Common->filterEmptyField($value, 'Laka', 'denda');
					                $biaya_lain = $this->Common->filterEmptyField($value, 'Laka', 'biaya_lain');
					                $price_estimate = $this->Common->filterEmptyField($value, 'Laka', 'price_estimate');
					                $note = $this->Common->filterEmptyField($value, 'Laka', 'note');

					                if( !empty($data['LakaPaymentDetail']['amount'][$key]) ) {
					                	$total = $this->Common->convertPriceToString($data['LakaPaymentDetail']['amount'][$key]);
					                } else {
					                	$total = $price + $denda + $biaya_lain;
					                }

									$grandTotal += $total;
			                		$document_date = $this->Common->filterEmptyField($value, 'Laka', 'tgl_bayar');

					                $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
					                $to_date = $this->Common->formatDate($to_date, 'd/m/Y');
					                $document_date = $this->Common->formatDate($document_date, 'd/m/Y');
					                $customPrice = $this->Common->getFormatPrice($price);
					                $customDenda = $this->Common->getFormatPrice($denda);
					                $customBiayaLain = $this->Common->getFormatPrice($biaya_lain);
					                $customPriceEstimate = $this->Common->getFormatPrice($price_estimate);
					                $customTotal = $this->Common->getFormatPrice($total);

					                $checkbox = isset($checkbox)?$checkbox:true;
					                $alias = sprintf('child-%s-%s', $id, $data_type);

					                $documentPayment = $this->Common->filterEmptyField($this->request->data, 'LakaPayment');
				    ?>
				    <tr class="child child-<?php echo $alias; ?>">
				        <td><?php echo $noref;?></td>
				        <td><?php echo $nopol;?></td>
				        <td class="text-center"><?php echo $to_date;?></td>
				        <td class="text-center"><?php echo $document_date;?></td>
				        <td class="text-right"><?php echo $customPriceEstimate;?></td>
				        <td class="text-right"><?php echo $customPrice;?></td>
				        <td class="text-right"><?php echo $customDenda;?></td>
				        <td class="text-right"><?php echo $customBiayaLain;?></td>
				        <td class="text-right">
				            <?php
				                    echo $this->Form->input('LakaPaymentDetail.amount.'.$key,array(
				                        'label'=> false,
				                        'class'=>'form-control input_price text-right sisa-amount',
				                        'required' => false,
				                    ));
				                    echo $this->Form->hidden('LakaPaymentDetail.laka_id.'.$key);
				            ?>
				        </td>
				        <td><?php echo $note;?></td>
				        <?php 
				                echo $this->Html->tag('td', $this->Html->link('<i class="fa fa-times"></i>', 'javascript:', array(
				                    'class' => 'delete-document-current btn btn-danger btn-xs',
				                    'escape' => false,
				                    'data-id' => sprintf('child-%s', $alias),
				                )), array(
				                    'class' => 'document-table-action',
				                ));
				        ?>
				    </tr>
				    <?php
								}
							}
					?>
				</tbody>
				<tr>
					<?php 
							echo $this->Html->tag('td', __('Total'), array(
								'colspan' => 9,
								'class' => 'bold text-right',
							));
							echo $this->Html->tag('td', $this->Number->format($grandTotal, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
								'class' => 'text-right',
								'id' => 'total-biaya',
							));
					?>
				</tr>

	    	</table>
	    </div>
	</div>
</div>