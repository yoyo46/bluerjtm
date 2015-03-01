<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Detail Info LKU'); ?></h3>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover">
        	<thead>
        		<tr>
        			<th width="20%"><?php echo __('No.Invoice');?></th>
                    <th><?php echo __('Tgl Invoice');?></th>
                    <th class="text-center"><?php echo __('Periode');?></th>
                    <th class="text-center"><?php echo __('Total Pembayaran <br>Invoice');?></th>
                    <th class="text-center"><?php echo __('Telah Terbayar');?></th>
                    <th class="text-center"><?php echo __('Bayar');?></th>
                    <th class="text-center"><?php echo __('Action');?></th>
        		</tr>
        	</thead>
        	<tbody class="ttuj-info-table">
                <?php
                    $total = $i = 0;

                    if(!empty($invoices)){
                    	foreach ($invoices as $key => $value) {
                    		$invoice = $value['Invoice'];
                ?>
        		<tr class="child child-<?php echo $key;?>" rel="<?php echo $key;?>">
                    <td>
                        <?php
                            echo $invoice['no_invoice'];

                            echo $this->Form->input('InvoicePaymentDetail.invoice_id.'.$key, array(
                            	'type' => 'hidden',
                            	'value' => $invoice['id']
                            ));
                        ?>
                    </td>
        			<td>
                        <?php
                            echo $this->Common->customDate($invoice['invoice_date']);
                        ?>
                    </td>
                    <td class="text-center">
                        <?php
                            echo $this->Common->customDate($invoice['period_from']).'<br>';
                            echo 'sampai<br>';
                            echo $this->Common->customDate($invoice['period_to']);
                        ?>
                    </td>
                    <td class="text-right">
                        <?php
                            echo $this->Number->currency($invoice['total'], Configure::read('__Site.config_currency_code'), array('places' => 0));
                        ?>
                    </td>
                    <td class="text-right">
                        <?php
                        	if(!empty($value['invoice_has_paid'])){
                        		echo $this->Number->currency($value['invoice_has_paid'], Configure::read('__Site.config_currency_code'), array('places' => 0));	
                        	}else{
                        		echo '-';
                        	}
                            
                        ?>
                    </td>
                    <td class="text-right" valign="top">
                        <?php
                            echo $this->Form->input('InvoicePaymentDetail.price_pay.'.$key, array(
                            	'type' => 'text',
                            	'label' => false,
                            	'div' => false,
                            	'required' => false,
                            	'class' => 'form-control input_price invoice-price-payment',
                            	'value' => (!empty($this->request->data['InvoicePaymentDetail']['price_pay'][$key])) ? $this->request->data['InvoicePaymentDetail']['price_pay'][$key] : 0
                            ));

                            if(!empty($this->request->data['InvoicePaymentDetail']['price_pay'][$key])){
                            	$total += str_replace(',', '', $this->request->data['InvoicePaymentDetail']['price_pay'][$key]);
                            }
                        ?>
                    </td>
                    <td>
                        <?php
                            echo $this->Html->link('<i class="fa fa-times"></i> Hapus', 'javascript:', array(
                                'class' => 'delete-custom-field btn btn-danger btn-xs',
                                'escape' => false,
                                'action_type' => 'invoice_first'
                            ));
                        ?>
                    </td>
        		</tr>
                <?php
                		}
                ?>
                <tr id="field-grand-total-ttuj">
                    <td align="right" colspan="5"><?php echo __('Total')?></td>
                    <td align="right" id="grand-total-payment">
                    	<?php 
                    		echo $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0));
                    	?>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <?php
                	}else{
                		echo $this->Html->tag('tr', $this->Html->tag('td', __('Data tidak ditemukan')) );
                	}
                ?>
        	</tbody>
    	</table>
    </div>
</div>