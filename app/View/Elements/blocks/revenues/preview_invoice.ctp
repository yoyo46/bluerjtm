<?php
		if(!empty($revenue_detail)){
			foreach ($revenue_detail as $key => $val_detail) {
				$data_print = !empty($data_print)?$data_print:'invoice';
?>
<table border="1" width="100%" style="margin-top: 20px;">
	<thead class="header-invoice-print">
		<tr>
			<th colspan="8" class="text-center" style="text-transform:uppercase;">
				<?php 
						if($action == 'tarif' && $data_print == 'invoice'){
							printf('Tarif Angkutan : %s', $this->Number->currency($val_detail[0]['RevenueDetail']['price_unit'], Configure::read('__Site.config_currency_code'), array('places' => 0)) );
						}else{
			                if( $data_print == 'date' && !empty($val_detail[0]['Invoice']['invoice_date']) ) {
								echo $this->Common->customDate($val_detail[0]['Invoice']['invoice_date'], 'd/m/Y');
			                } else {
								echo $val_detail[0]['City']['name'];
			                }
						}
				?>
			</th>
		</tr>
		<tr>
			<th class="text-center"><?php echo __('No.');?></th>
			<th class="text-center"><?php echo __('No. Truk');?></th>
			<th class="text-center"><?php echo __('No.DO/Shipping List');?></th>
			<th class="text-center"><?php echo __('Tanggal');?></th>
			<th class="text-center"><?php echo __('Total Unit');?></th>
			<th class="text-center"><?php echo __('Harga');?></th>
			<th class="text-center"><?php echo __('Total');?></th>
			<th class="text-center"><?php echo __('No. Ref');?></th>
		</tr>
	</thead>
	<tbody>
		<?php
				if(!empty($val_detail)){
					$no=1;
					$grandTotal = 0;
					$grandTotalUnit = 0;
					$rowSpan = 0;
					$trData = '';
					$totalFlag = true;

					foreach ($val_detail as $key => $value) {
						$nopol = !empty($value['Revenue']['Ttuj']['nopol'])?$value['Revenue']['Ttuj']['nopol']:false;
						$grandTotalUnit += $qty = $value['RevenueDetail']['qty_unit'];
						$price = $value['RevenueDetail']['price_unit'];
						$total = 0;

						$colom = $this->Html->tag('td', $no++);
						$colom .= $this->Html->tag('td', $nopol);
						$colom .= $this->Html->tag('td', $value['RevenueDetail']['no_do']);
						$colom .= $this->Html->tag('td', $this->Common->customDate($value['Revenue']['date_revenue'], 'd/m/Y'));
						$colom .= $this->Html->tag('td', $qty, array(
							'align' => 'center'
						));
						$colom .= $this->Html->tag('td', $this->Number->currency($price, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
							'align' => 'right'
						));

						if(!empty($value['RevenueDetail']['payment_type']) && $value['RevenueDetail']['payment_type'] == 'per_truck'){
							if( !empty($value['RevenueDetail']['total_price_unit']) ) {
								$total = $value['RevenueDetail']['total_price_unit'];

								$colom .= $this->Html->tag('td', $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
									'align' => 'right'
								));
							} else {
								if( empty($rowSpan) ) {
									$total = !empty($value['Revenue']['tarif_per_truck'])?$value['Revenue']['tarif_per_truck']:0;
									$colom .= $this->Html->tag('td', $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
										'align' => 'right',
										'data-rowspan' => 'data-value'
									));
								}

								$rowSpan++;
							}
						}else{
							$total = $price * $qty;

							$colom .= $this->Html->tag('td', $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
								'align' => 'right'
							));
						}

						$colom .= $this->Html->tag('td', $value['RevenueDetail']['no_reference']);
						$trData .= $this->Html->tag('tr', $colom);
						$grandTotal += $total;
					}

					if( !empty($rowSpan) ) {
						$trData = str_replace(array( 'data-rowspan', 'data-value' ), array( 'rowspan', $rowSpan ), $trData);
					}
					echo $trData;

					$colom = $this->Html->tag('td', __('Total '), array(
						'colspan' => 4,
						'align' => 'right'
					));
					$colom .= $this->Html->tag('td', $this->Number->format($grandTotalUnit), array(
						'align' => 'center'
					));
					$colom .= $this->Html->tag('td', '&nbsp;');
					$colom .= $this->Html->tag('td', $this->Number->currency($grandTotal, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
						'align' => 'right'
					));
					$colom .= $this->Html->tag('td', '&nbsp;');
					
					echo $this->Html->tag('tr', $colom, array(
						'class' => 'total-row'
					));
				}else{
					$colom = $this->Html->tag('td', __('Data tidak ditemukan.'), array(
						'colspan' => 5
					));

					echo $this->Html->tag('tr', $colom);
				}
		?>
	</tbody>
</table>
<?php
			}
		} else {
            echo $this->Html->tag('p', $this->Html->tag('td', __('Data belum tersedia.'), array(
                'class' => 'alert alert-warning text-center',
            )));
        }
?>