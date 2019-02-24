<?php 
		$data_print_type = isset($data_print_type)?$data_print_type:false;
		$values = $this->Common->filterEmptyField($invoice, 'InvoiceDetail');
		$company_name = $this->Common->filterEmptyField($invoice, 'Company', 'code', 'RJTM');

        if( !empty($action_print) && $action_print == 'pdf' ) {
        	$table_tr_head = 'background-color: #3C8DBC; border-right: 1px solid #FFFFFF; color: #FFFFFF; font-weight: bold; padding: 0 10px; text-align: left;';
	        $table = 'width:100%;font-size: 24px; border: 1px solid #CCC; border-collapse: collapse; padding: 0; margin: 0;';
        } else {
        	$table_tr_head = '';
	        $table_th = '';
	        $table = '';
        }
?>

<table border="0" style="width: 100%;">
	<?php 
            if( !empty($action_print) ) {
        		$tarif_name = !empty($tarif_name)?$tarif_name:false;
				$invoice = !empty($invoice)?$invoice:false;
				$customer = $this->Common->filterEmptyField($invoice, 'Customer', 'name');
				$no_invoice = $this->Common->filterEmptyField($invoice, 'Invoice', 'no_invoice');
	?>
	<tr>
		<td>
			<table border="0" style="width: 100%;margin-bottom: 20px;">
				<tr>
					<td colspan="5" style="text-align: left;width: 50%;" valign="top">
						<?php 
								echo $this->Html->tag('div', $customer, array(
									'style' => 'font-size: 18px;font-weight: 700;margin-bottom: 20px;'
								));
						?>
					</td>
					<td colspan="5" style="text-align: right;width: 50%;">
						<?php 
								echo $this->Html->tag('div', $no_invoice, array(
									'style' => 'font-size: 16px;font-weight: 700;magin:0;'
								));
								echo $this->Html->tag('div', $tarif_name, array(
									'style' => 'font-size: 16px;font-weight: 700;'
								));
						?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<?php 
			}
	?>
	<tr>
		<td>
			<?php 
					if(!empty($resultDetails)){
						$totalUnitTfoot = 0;
						$totalPriceUnitTfoot = 0;
						$idx = 1;

						foreach ($resultDetails as $price_unit => $values) {
							// $price_unit = $this->Common->getFormatPrice($price_unit);
			?>
				<table border="1" width="100%" style="margin-top: 20px;<?php echo $table; ?>">
				<?php 
	                    if( !empty($fieldColumn) ) {
	                    	// if( $idx == 2 ) {
	                    	// 	$label = $this->Html->tag('tr', 
		                    //     	$this->Html->tag('td', __('%s - CBR/PCX', $tarif_name), array(
			                   //      	'style' => 'text-transform:uppercase;text-align:center;font-weight: bold;',
			                   //      	'colspan' => '10',
		                    //     	)), array(
		                    //     	'style' => $table_tr_head,
	                     //    	));
	                    	// } else {
	                    	// 	$label = '';
	                    	// }

	                        echo $this->Html->tag('thead', 
	                        	// $label.
	                        	$this->Html->tag('tr', $fieldColumn, array(
		                        	'style' => $table_tr_head,
	                        	)), array(
	                        	'class' => 'header-invoice-print',
                        	));
	                    }
				?>
				<tbody>
					<?php
							if(!empty($values)){
								$no = 1;
								$grandTotalUnit = 0;
								$grandTotalTarif = 0;
            					$temp = false;

								foreach ($values as $key => $value) {
				    				$revenue_id = $this->Common->filterEmptyField($value, 'Revenue', 'id');
				    				$date_revenue = $this->Common->filterEmptyField($value, 'Revenue', 'date_revenue');
					                $revenue_tarif_type = $this->Common->filterEmptyField($value, 'Revenue', 'revenue_tarif_type');
					                $is_charge = $this->Common->filterEmptyField($value, 'RevenueDetail', 'is_charge');
					                $total_price_unit = $this->Common->filterEmptyField($value, 'RevenueDetail', 'total_price_unit');

				    				$city = $this->Common->filterEmptyField($value, 'City', 'code');
				    				$nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol', '-');
				    				$noref = $this->Common->getNoRef($revenue_id);

									if( !empty($value['Revenue']['nopol']) ) {
										$nopol = $value['Revenue']['nopol'];
									} else if( !empty($value['Revenue']['Ttuj']['nopol']) ) {
										$nopol = $value['Revenue']['Ttuj']['nopol'];
									} else if( !empty($value['Truck']['nopol']) ) {
										$nopol = $value['Truck']['nopol'];
									}

				    				$sj = $this->Common->filterEmptyField($value, 'RevenueDetail', 'no_sj');
				    				$no_do = $this->Common->filterEmptyField($value, 'RevenueDetail', 'no_do');
				    				$price_unit = $this->Common->filterEmptyField($value, 'RevenueDetail', 'price_unit', 0);
				    				$totalUnit = $this->Common->filterEmptyField($value, 'RevenueDetail', 'qty_unit', 0);
									$totalPriceFormat = '';
									$doArr = array(
										$no_do,
									);

				    				if( !empty($is_charge) ) {
										$totalPriceFormat = $this->Common->getFormatPrice($total_price_unit);
										$customPrice = $this->Common->getFormatPrice($price_unit);
									} else {
										$total_price_unit = 0;
										$customPrice = '';
									}

									if( !empty($city) ) {
										$doArr[] = $city;
									}

									if( !empty($doArr) ) {
										$doArr = array_filter($doArr);
										$no_do = implode(' - ', $doArr);
									}

									$grandTotalUnit += $totalUnit;
									$grandTotalTarif += $total_price_unit;
									
									$totalUnitTfoot += $totalUnit;
									$totalPriceUnitTfoot += $total_price_unit;
									
									$date_revenue = $this->Common->formatDate($date_revenue, 'd/m/Y');
									$totalUnit = $this->Common->getFormatPrice($totalUnit);
					?>
					<tr>
						<?php 
								echo $this->Html->tag('td', $no, array(
									'style' => 'text-align: center;padding: 10px;',
								));
								echo $this->Html->tag('td', $date_revenue, array(
									'style' => 'text-align: center;padding: 10px;',
								));
								echo $this->Html->tag('td', $no_do, array(
									'style' => 'padding: 10px;',
								));
								echo $this->Html->tag('td', $nopol, array(
									'style' => 'padding: 10px;',
								));
								echo $this->Html->tag('td', $sj, array(
									'style' => 'padding: 10px;',
								));
								echo $this->Html->tag('td', $totalUnit, array(
									'style' => 'text-align: center;padding: 10px;',
								));
								echo $this->Html->tag('td', $customPrice, array(
									'style' => 'text-align: right;padding: 10px;',
								));
								echo $this->Html->tag('td', $totalPriceFormat, array(
									'style' => 'text-align: right;padding: 10px;',
								));
								echo $this->Html->tag('td', $company_name, array(
									'style' => 'text-align: center;padding: 10px;',
								));
								echo $this->Html->tag('td', $noref, array(
									'class' => 'string',
									'style' => 'padding: 10px;',
								));
						?>
					</tr>
					<?php
									$no++;
            						$temp = $revenue_id;
								}

								$grandTotalUnit = $this->Common->getFormatPrice($grandTotalUnit);
								$grandTotalTarif = $this->Common->getFormatPrice($grandTotalTarif);

								$colom = $this->Html->tag('td', __('TOTAL'), array(
									'colspan' => 5,
									'style' => 'text-align: right;padding: 10px;',
								));
								$colom .= $this->Html->tag('td', $grandTotalUnit, array(
									'style' => 'text-align: center;padding: 10px;',
								));
								$colom .= $this->Html->tag('td', '', array(
									'style' => '',
								));
								$colom .= $this->Html->tag('td', $grandTotalTarif, array(
									'style' => 'text-align: right;padding: 10px;',
								));
								$colom .= $this->Html->tag('td', '', array(
									'style' => '',
								));
								$colom .= $this->Html->tag('td', '', array(
									'style' => '',
								));

								echo $this->Html->tag('tr', $colom, array(
									'style' => 'font-weight: bold;'
								));
							}else{
								$colom = $this->Html->tag('td', __('Data tidak ditemukan.'), array(
									'colspan' => 10,
									'style' => '',
								));

								echo $this->Html->tag('tr', $colom);
							}
					?>
				</tbody>
				<?php 
						/*
						if( $idx == count($resultDetails) ) {
							$totalUnitTfoot = $this->Common->getFormatPrice($totalUnitTfoot);
							$totalPriceUnitTfoot = $this->Common->getFormatPrice($totalPriceUnitTfoot);
				?>
				<tfoot>
					<?php
							$colom = $this->Html->tag('td', __('GrandTotal '), array(
								'align' => 'right',
								'style' => 'font-weight: bold;',
								'colspan' => 5,
							));
							$colom .= $this->Html->tag('td', $totalUnitTfoot, array(
								'align' => 'center'
							));
							$colom .= $this->Html->tag('td', '&nbsp;');
							$colom .= $this->Html->tag('td', $totalPriceUnitTfoot, array(
								'align' => 'right',
								'style' => 'font-weight: bold;',
							));
							$colom .= $this->Html->tag('td', '&nbsp;');
							$colom .= $this->Html->tag('td', '&nbsp;');
							
							echo $this->Html->tag('tr', $colom, array(
								'class' => 'total-row'
							));
					?>
				</tfoot>
				<?php
						}
						*/
				?>
			</table>
			<?php 
							$idx++;
						}
					}
			?>
		</td>
	</tr>
</table>