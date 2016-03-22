<?php
		if(!empty($revenue_detail)){
			$totalAll = 0;
			$totalAllUnit = 0;
			$idx = 1;

			foreach ($revenue_detail as $key => $val_detail) {
				$data_print = !empty($data_print)?$data_print:'invoice';

				$totalMerge = 10;
				$totalMergeTotal = 6;
?>
<table border="1" width="100%" style="margin-top: 20px;">
	<thead class="header-invoice-print">
		<tr>
			<th colspan="<?php echo $totalMerge; ?>" class="text-center" style="text-transform:uppercase;">
				<?php 
						if($action == 'tarif' && $data_print == 'invoice'){
							printf('Tarif Angkutan : %s', $this->Common->getFormatPrice($val_detail[0]['RevenueDetail']['price_unit']) );
						}else{
		                	if( $val_detail[0]['Revenue']['revenue_tarif_type'] == 'per_truck' && !empty($val_detail[0]['Revenue']['no_doc']) ) {
								echo $val_detail[0]['Revenue']['no_doc'];
		                	} else {
								echo $val_detail[0]['City']['name'];
							}
						}
				?>
			</th>
		</tr>
		<tr>
			<th class="text-center" style="width: 5%;"><?php echo __('No.');?></th>
			<th class="text-center" style="width: 10%;"><?php echo __('No. Truk');?></th>
			<th class="text-center" style="width: 12%;">
				<?php
						echo __('No.DO');
				?>
			</th>
			<th class="text-center" style="width: 13%;"><?php echo __('No.Shipping List');?></th>
			<th class="text-center" style="width: 15%;"><?php echo __('Nama Dealer');?></th>
			<th class="text-center" style="width: 10%;"><?php echo __('Tanggal');?></th>
			<th class="text-center" style="width: 5%;"><?php echo __('Total Unit');?></th>
			<th class="text-center" style="width: 10%;"><?php echo __('Harga');?></th>
			<th class="text-center" style="width: 10%;"><?php echo __('Total');?></th>
			<th class="text-center" style="width: 10%;"><?php echo __('No. Ref');?></th>
		</tr>
	</thead>
	<tbody>
		<?php
				if(!empty($val_detail)){
					$no=1;
					$grandTotal = 0;
					$grandTotalUnit = 0;
					$trData = '';
					$totalFlag = true;
					$old_revenue_id = false;
					$recenueCnt = array();

					foreach ($val_detail as $key => $value) {
        				$revenue_id = $this->Common->filterEmptyField($value, 'Revenue', 'id');
        				$total_price_unit = $this->Common->filterEmptyField($value, 'RevenueDetail', 'total_price_unit');
    					$is_charge = $this->Common->filterEmptyField($value, 'RevenueDetail', 'is_charge');

	    				if( empty($is_charge) ) {
							if( empty($total_price_unit) ) {
								if( !empty($recenueCnt[$revenue_id]) ) {
									$recenueCnt[$revenue_id]++;
								} else {
									$recenueCnt[$revenue_id] = 1;
								}
							}
						}
					}

					foreach ($val_detail as $key => $value) {
        				$revenue_id = $this->Common->filterEmptyField($value, 'Revenue', 'id');
        				$date_revenue = $this->Common->filterEmptyField($value, 'Revenue', 'date_revenue');
        				$total_price_unit = $this->Common->filterEmptyField($value, 'RevenueDetail', 'total_price_unit');
        				$price_unit = $this->Common->filterEmptyField($value, 'RevenueDetail', 'price_unit');

        				$is_charge = $this->Common->filterEmptyField($value, 'RevenueDetail', 'is_charge');
        				$no_do = $this->Common->filterEmptyField($value, 'RevenueDetail', 'no_do');
        				$sj = $this->Common->filterEmptyField($value, 'RevenueDetail', 'no_sj');

        				if( !empty($sj) ) {
	        				$no_sj = substr($sj, 0, 28);
	        				$dealer = substr($sj, 29, strlen($sj));
	        			} else {
							$no_sj = false;
	        				$dealer = false;
	        			}

						$revenue_temp = sprintf('%s-%s', $revenue_id, $is_charge);
						$date_revenue = $this->Common->formatDate($date_revenue, 'd/m/Y');

						$grandTotalUnit += $qty = $this->Common->filterEmptyField($value, 'RevenueDetail', 'qty_unit');
						$payment_type = $this->Common->filterEmptyField($value, 'RevenueDetail', 'payment_type');
						$jenis_tarif = $this->Common->filterEmptyField($value, 'RevenueDetail', 'revenue_tarif_type');

						$price = 0;
						$total = 0;

						if( $payment_type == 'per_truck' ){
							$priceFormat = '-';
						} else {
							$price = $price_unit;
							$priceFormat = $this->Number->currency($price, '', array('places' => 0));
						}

						if( !empty($value['Revenue']['Ttuj']['nopol']) ) {
							$nopol = $value['Revenue']['Ttuj']['nopol'];
						} else if( !empty($value['Truck']['nopol']) ) {
							$nopol = $value['Truck']['nopol'];
						} else {
							$nopol = false;
						}

						$colom = $this->Html->tag('td', $no++, array(
							'style' => 'text-align: center;'
						));

						if( !empty($data_print) && $data_print == 'date' ) {
							$city_name = !empty($value['City']['name'])?$value['City']['name']:false;
							$colom .= $this->Html->tag('td', $value['City']['name']);
						}

						$colom .= $this->Html->tag('td', $nopol);
						$colom .= $this->Html->tag('td', $no_do);
						$colom .= $this->Html->tag('td', $no_sj);
						$colom .= $this->Html->tag('td', $dealer);
						$colom .= $this->Html->tag('td', $date_revenue, array(
							'style' => 'text-align: center;'
						));
						$colom .= $this->Html->tag('td', $qty, array(
							'align' => 'center'
						));
						$colom .= $this->Html->tag('td', $priceFormat, array(
							'align' => 'right'
						));

						if( $payment_type == 'per_truck' ){
							if( !empty($total_price_unit) ) {
								$total = $total_price_unit;

								$colom .= $this->Html->tag('td', $this->Common->getFormatPrice($total), array(
									'align' => 'right'
								));
							} else {
								if( $revenue_temp != $old_revenue_id ) {
									$total = !empty($value['Revenue']['tarif_per_truck'])?$value['Revenue']['tarif_per_truck']:0;
									$colom .= $this->Html->tag('td', $this->Common->getFormatPrice($total), array(
										'align' => 'right',
										'rowspan' => !empty($recenueCnt[$revenue_id])?$recenueCnt[$revenue_id]:false,
									));
								}
							}
						}else{
							$total = $price * $qty;

							$colom .= $this->Html->tag('td', $this->Number->currency($total, '', array('places' => 0)), array(
								'style' => 'text-align:right;',
							));
						}

						$colom .= $this->Html->tag('td', $this->Common->getNoRef($value['Revenue']['id']), array(
							'class' => 'string',
						));
						$trData .= $this->Html->tag('tr', $colom);
						$grandTotal += $total;
						$old_revenue_id = sprintf('%s-%s', $revenue_id, $is_charge);
					}

					echo $trData;

					$totalAll += $grandTotal;
					$totalAllUnit += $grandTotalUnit;

					$colom = $this->Html->tag('td', __('Total '), array(
						'colspan' => $totalMergeTotal,
						'align' => 'right',
						'style' => 'font-weight: bold;',
					));
					$colom .= $this->Html->tag('td', $this->Number->format($grandTotalUnit), array(
						'align' => 'center'
					));
					$colom .= $this->Html->tag('td', '&nbsp;', array(
						'align' => 'right',
						'style' => 'font-weight: bold;',
					));
					$colom .= $this->Html->tag('td', $this->Common->getFormatPrice($grandTotal), array(
						'align' => 'right',
						'style' => 'font-weight: bold;',
					));
					$colom .= $this->Html->tag('td', '&nbsp;');
					
					echo $this->Html->tag('tr', $colom, array(
						'class' => 'total-row'
					));

					// $pph = !empty($totalPPh[0]['pph'])?$totalPPh[0]['pph']:0;
					$ppn = !empty($totalPPN[0]['ppn'])?$totalPPN[0]['ppn']:0;

					if( !empty($ppn) ) {
						$colom = $this->Html->tag('td', '&nbsp;', array(
							'colspan' => $totalMergeTotal+1,
						));
						$colom .= $this->Html->tag('td', __('PPN '), array(
							'align' => 'right',
							'style' => 'font-weight: bold;',
						));
						$colom .= $this->Html->tag('td', $this->Common->getFormatPrice($ppn), array(
							'align' => 'right',
							'style' => 'font-weight: bold;',
						));
						$colom .= $this->Html->tag('td', '&nbsp;');

						echo $this->Html->tag('tr', $colom, array(
							'class' => 'total-row'
						));
					}

					if( !empty($ppn) || !empty($pph) ) {
						$grandTotalInvoice = $grandTotal + $ppn;
						$totalAll += $ppn;

						$colom = $this->Html->tag('td', '&nbsp;', array(
							'colspan' => $totalMergeTotal+1,
						));
						$colom .= $this->Html->tag('td', __('GrandTotal '), array(
							'align' => 'right',
							'style' => 'font-weight: bold;',
						));
						$colom .= $this->Html->tag('td', $this->Common->getFormatPrice($grandTotalInvoice), array(
							'align' => 'right',
							'style' => 'font-weight: bold;',
						));
						$colom .= $this->Html->tag('td', '&nbsp;');

						echo $this->Html->tag('tr', $colom, array(
							'class' => 'total-row'
						));
					}
				}else{
					$colom = $this->Html->tag('td', __('Data tidak ditemukan.'), array(
						'colspan' => $totalMerge,
					));

					echo $this->Html->tag('tr', $colom);
				}
		?>
	</tbody>
	<?php 
			if( $idx == count($revenue_detail) ) {
	?>
	<tfoot>
		<?php
				$colom = $this->Html->tag('td', __('GrandTotal '), array(
					'align' => 'right',
					'style' => 'font-weight: bold;',
					'colspan' => $totalMergeTotal,
				));
				$colom .= $this->Html->tag('td', $this->Number->format($totalAllUnit), array(
					'align' => 'center'
				));
				$colom .= $this->Html->tag('td', '&nbsp;');
				$colom .= $this->Html->tag('td', $this->Common->getFormatPrice($totalAll), array(
					'align' => 'right',
					'style' => 'font-weight: bold;',
				));
				$colom .= $this->Html->tag('td', '&nbsp;');
				
				echo $this->Html->tag('tr', $colom, array(
					'class' => 'total-row'
				));
		?>
	</tfoot>
	<?php 
			}
	?>
</table>
<?php
				$idx++;
			}
		} else {
            echo $this->Html->tag('p', $this->Html->tag('td', __('Data belum tersedia.'), array(
                'class' => 'alert alert-warning text-center',
            )));
        }
?>