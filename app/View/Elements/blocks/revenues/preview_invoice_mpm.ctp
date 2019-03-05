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
						if(in_array($action, array( 'tarif', 'tarif_name' )) && $data_print == 'invoice'){
                        	if( $action == 'tarif_name' ) {
                        		$name_tarif = !empty($val_detail[0]['TarifAngkutan']['name_tarif'])?$val_detail[0]['TarifAngkutan']['name_tarif']:__('[Tidak ada Tarif]');
								echo $name_tarif;
                        	} else {
								printf('Tarif Angkutan : %s', $this->Common->getFormatPrice($val_detail[0]['RevenueDetail']['price_unit']) );
							}
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
			<th class="text-center" style="width: 14%;"><?php echo __('No. Truk');?></th>
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
					$old_detail_id = false;
					$recenueCnt = array();

					foreach ($val_detail as $key => $value) {
						$is_charge = $this->Common->filterEmptyField($value, 'RevenueDetail', 'is_charge');
						$detail_id = $this->Common->filterEmptyField($value, 'RevenueDetail', 'id');

						if( empty($is_charge) ) {
							if( !empty($recenueCnt[$old_detail_id]) ) {
								$recenueCnt[$old_detail_id]++;
							} else {
								$recenueCnt[$old_detail_id] = 1;
							}
						} else {
							$old_detail_id = $detail_id;
						}						
					}

					foreach ($val_detail as $key => $value) {
        				$revenue_id = $this->Common->filterEmptyField($value, 'Revenue', 'id');
        				$date_revenue = $this->Common->filterEmptyField($value, 'Revenue', 'date_revenue');
						$revenue_jenis_tarif = $this->Common->filterEmptyField($value, 'Revenue', 'revenue_tarif_type');

						$detail_id = $this->Common->filterEmptyField($value, 'RevenueDetail', 'id');
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
						$date_revenue = $this->Common->formatDate($date_revenue, 'd M Y');

						$grandTotalUnit += $qty = $this->Common->filterEmptyField($value, 'RevenueDetail', 'qty_unit');
						$payment_type = $this->Common->filterEmptyField($value, 'RevenueDetail', 'payment_type');
						$jenis_tarif = $this->Common->filterEmptyField($value, 'RevenueDetail', 'revenue_tarif_type');

						$price = 0;
						$totalPriceFormat = '';

						if( !empty($is_charge) ) {
							$totalPriceFormat = $this->Common->getFormatPrice($total_price_unit);
							$priceFormat = $this->Common->getFormatPrice($price_unit, 0);
						} else {
							$total_price_unit = 0;
							$priceFormat = '';
						}

						if( !empty($value['Revenue']['nopol']) ) {
							$nopol = $value['Revenue']['nopol'];
						} else if( !empty($value['Revenue']['Ttuj']['nopol']) ) {
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

						if( $revenue_jenis_tarif == 'per_truck' ){
							if( !empty($recenueCnt[$detail_id]) ) {
								$colom .= $this->Html->tag('td', $totalPriceFormat, array(
									'align' => 'right',
									'rowspan' => $recenueCnt[$detail_id] + 1,
								));
							} else if( !empty($is_charge) ) {
								$colom .= $this->Html->tag('td', $totalPriceFormat, array(
									'align' => 'right'
								));
							}
						} else {
							$colom .= $this->Html->tag('td', $totalPriceFormat, array(
								'align' => 'right'
							));
						}

						$colom .= $this->Html->tag('td', $this->Common->getNoRef($value['Revenue']['id']), array(
							'class' => 'string',
						));
						$trData .= $this->Html->tag('tr', $colom);
						$grandTotal += $total_price_unit;
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