<div class="checkbox-info-detail <?php echo (!empty($this->request->data['DebtDetail'])) ? '' : 'hide';?>">
	<div class="box box-primary">
	    <div class="box-header">
	        <h3 class="box-title"><?php echo __('List Hutang'); ?></h3>
	    </div>
	    <div class="box-body table-responsive">
	        <table class="table table-hover">
	        	<thead>
	        		<tr>
	        			<?php 
			                    echo $this->Html->tag('th', __('Karyawan'));
			                    echo $this->Html->tag('th', __('Kategori'));
			                    echo $this->Html->tag('th', __('Ket.'));
			                    echo $this->Html->tag('th', __('Tgl Hutang'), array(
			                        'class' => 'text-center',
			                    ));
			                    echo $this->Html->tag('th', __('Total Hutang'), array(
			                        'class' => 'text-right',
			                    ));
			                    echo $this->Html->tag('th', __('Dibayar'), array(
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

							if(!empty($data['DebtDetail'])){
								$idx = 0;

								foreach ($data['DebtDetail'] as $key => $value) {
									$id = Common::hashEmptyField($value, 'DebtDetail.id');
									$debt_id = Common::hashEmptyField($value, 'Debt.id');
					                $employe_id = Common::hashEmptyField($value, 'ViewStaff.id');
					                $employe_name = Common::hashEmptyField($value, 'ViewStaff.full_name');
                    				$type = Common::hashEmptyField($value, 'ViewStaff.type');
                        			$document_date = Common::hashEmptyField($value, 'Debt.transaction_date');
                        			$note = Common::hashEmptyField($value, 'DebtDetail.note');
                        			$last_paid = Common::hashEmptyField($value, 'DebtDetail.last_paid');

                        			$amount = !empty($data['DebtPaymentDetail']['amount'][$key])?$data['DebtPaymentDetail']['amount'][$key]:0;
                        			$amount = $this->Common->convertPriceToString($amount);
                        
                        			$document_date = $this->Common->formatDate($document_date, 'd M Y');
					                $checkbox = isset($checkbox)?$checkbox:true;
					                $alias = sprintf('child-%s', $id);

					                $grandTotal += $amount;
				    ?>
				    <tr class="child child-<?php echo $alias; ?>">
				        <td><?php echo $employe_name;?></td>
				        <td><?php echo $type;?></td>
				        <td><?php echo $note;?></td>
	                    <td class="text-center"><?php echo $document_date;?></td>
	                    <td class="text-right"><?php echo Common::getFormatPrice($last_paid);?></td>
				        <td class="text-right">
				            <?php
				                    echo $this->Form->input('DebtPaymentDetail.amount.'.$key,array(
				                        'label'=> false,
				                        'class'=>'form-control input_price text-right sisa-amount',
				                        'required' => false,
				                    ));
				                    echo $this->Form->error('DebtPaymentDetail.'.$idx.'.amount');
				                    echo $this->Form->hidden('DebtPaymentDetail.debt_detail_id.'.$key, array(
				                    	'value' => $id,
				                    ));
	                                echo $this->Form->hidden('DebtPaymentDetail.debt_id.'.$key,array(
	                                    'value'=> $debt_id,
	                                ));
                                echo $this->Form->hidden('DebtPaymentDetail.employe_id.'.$key,array(
                                    'value'=> $employe_id,
                                ));
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
									$idx++;
								}
							}
					?>
				</tbody>
				<tr>
					<?php 
							echo $this->Html->tag('td', __('Total'), array(
								'colspan' => 5,
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