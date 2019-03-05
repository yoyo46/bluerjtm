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
			                    echo $this->Html->tag('th', __('Supir'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Tgl LAKA'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Lokasi LAKA'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Status Muatan'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Total'), array(
			                        'class' => 'text-center',
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
					                $nopol = $this->Common->filterEmptyField($value, 'Laka', 'nopol');
                        			$document_date = $this->Common->filterEmptyField($value, 'Laka', 'tgl_laka');
                    				$lokasi = $this->Common->filterEmptyField($value, 'Laka', 'lokasi_laka');
                        			$status_muatan = $this->Common->filterEmptyField($value, 'Laka', 'status_muatan');

                        			$amount = !empty($data['LakaPaymentDetail']['amount'][$key])?$data['LakaPaymentDetail']['amount'][$key]:0;
                        			$amount = $this->Common->convertPriceToString($amount);
                        
			                        $driver = $this->Common->filterEmptyField($value, 'Laka', 'driver_name');
			                        $driver = $this->Common->filterEmptyField($value, 'Laka', 'change_driver_name', $driver);

                        			$noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                        			$document_date = $this->Common->formatDate($document_date, 'd M Y');
                        			$status_muatan = strtoupper($status_muatan);
					                $checkbox = isset($checkbox)?$checkbox:true;
					                $alias = sprintf('child-%s', $id);

					                $grandTotal += $amount;

					                $documentPayment = $this->Common->filterEmptyField($this->request->data, 'LakaPayment');
				    ?>
				    <tr class="child child-<?php echo $alias; ?>">
				        <td><?php echo $noref;?></td>
				        <td><?php echo $nopol;?></td>
				        <td class="text-center"><?php echo $driver;?></td>
	                    <td class="text-center"><?php echo $document_date;?></td>
	                    <td><?php echo $lokasi;?></td>
	                    <td class="text-center"><?php echo $status_muatan;?></td>
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
								'colspan' => 6,
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