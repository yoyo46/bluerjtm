<style>
    .string{ mso-number-format:\@; }
</style>
<?php
		$data_print = !empty($data_print)?$data_print:'invoice';
		$footerGrandTotal = 0;
		$footerGrandTotalUnit = 0;
		$idx = 0;

		if(!empty($revenue_detail)){
			foreach ($revenue_detail as $key => $val_detail) {
				$idx++;

				if( in_array($data_print, array( 'date', 'hso-smg', 'preview', 'sa' )) ) {
					$totalMerge = 10;
					$totalMergeTotal = 6;
				} else {
					$totalMerge = 9;
					$totalMergeTotal = 5;
				}
?>
<table border="1" width="100%" style="margin-top: 20px;">
	<?php 
			if( !in_array($data_print, array( 'date', 'hso-smg', 'sa' )) ) {
	?>
	<tr>
		<th colspan="<?php echo $totalMerge; ?>" class="text-center" style="text-transform:uppercase;">
			<?php 
					if( in_array($action, array( 'tarif', 'tarif_name' )) ){
                    	if( $action == 'tarif_name' ) {
                    		$name_tarif = !empty($val_detail[0]['TarifAngkutan']['name_tarif'])?$val_detail[0]['TarifAngkutan']['name_tarif']:__('[Tidak ada Tarif]');
							echo $name_tarif;
                    	} else {
							printf('Tarif Angkutan : %s', $this->Common->getFormatPrice($val_detail[0]['RevenueDetail']['price_unit']) );
						}
					}else{
		                if( in_array($data_print, array( 'date', 'sa' )) && !empty($val_detail[0]['Revenue']['date_revenue']) ) {
							echo $this->Common->customDate($val_detail[0]['Revenue']['date_revenue'], 'd/m/Y');
		                } else {
	                		$no_doc = !empty($val_detail[0]['Revenue']['no_doc'])?$val_detail[0]['Revenue']['no_doc']:false;
							echo !empty($val_detail[0]['City']['name'])?$val_detail[0]['City']['name']:$no_doc;
		                }
					}
			?>
		</th>
	</tr>
	<?php 
			}
	?>
	<tr>
		<th class="text-center" style="width: 5%;"><?php echo __('No.');?></th>
		<?php 
				if( in_array($data_print, array( 'date', 'sa' )) ) {
					echo $this->Html->tag('th', __('Kota'), array(
						'class' => 'text-center',
						'width' => '13%'
					));
				}
		?>
		<th class="text-center" style="width: 14%;"><?php echo __('No. Truk');?></th>
		<th class="text-center" style="width: 12%;">
			<?php
					if( in_array($data_print, array( 'date', 'sa' )) ) {
						if( in_array($data_print, array( 'sa' )) ) {
							echo __('No.Do / No.SJ');
						} else {
							echo __('Keterangan');
						}
					} else {
						echo __('No.DO');
					}
			?>
		</th>
		<?php 
				if( in_array($data_print, array( 'sa' )) ) {
					echo $this->Html->tag('th', __('No. SA'), array(
						'class' => 'text-center',
						'width' => '15%'
					));
				} else {
					if( in_array($data_print, array( 'hso-smg', 'sa' )) ) {
						$labelNameSj = __('Nama Dealer');
					} else {
						$labelNameSj = __('No. SJ');
					}
					echo $this->Html->tag('th', $labelNameSj, array(
						'class' => 'text-center',
						'width' => '15%'
					));
				}
		?>
		<th class="text-center" style="width: 10%;"><?php echo __('Tanggal');?></th>
		<?php 
				if( in_array($data_print, array( 'hso-smg', 'preview' )) ) {
					echo $this->Html->tag('th', __('Kota'), array(
						'class' => 'text-center',
						'width' => '13%'
					));
				}
		?>
		<th class="text-center" style="width: 8%;"><?php echo __('Unit');?></th>
		<th class="text-center" style="width: 10%;"><?php echo __('Harga');?></th>
		<th class="text-center" style="width: 10%;"><?php echo __('Total');?></th>
		<th class="text-center" style="width: 7%;"><?php echo __('No. Ref');?></th>
	</tr>
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
				$recenueCharge = array();
				$recenueCol = array();

				foreach ($val_detail as $key => $value) {
					$id = $this->Common->filterEmptyField($value, 'Revenue', 'id');
					$is_charge = $this->Common->filterEmptyField($value, 'RevenueDetail', 'is_charge');
					$detail_id = $this->Common->filterEmptyField($value, 'RevenueDetail', 'id');
					$city_name = $this->Common->filterEmptyField($value, 'City', 'name');

					if( empty($is_charge) ) {
						if( empty($recenueCharge[$id]) ) {
							$recenueCol[$detail_id] = true;
						} else if( !empty($recenueCharge[$id]) ) {
							// $val_detail[$key]['City']['name'] = $recenueCharge[$id];
						}

						if( !empty($recenueCnt[$id][$old_detail_id]) ) {
							$recenueCnt[$id][$old_detail_id]++;
						} else if( !empty($recenueCharge[$id]) ) {
							$recenueCnt[$id][$old_detail_id] = 1;
						}
					} else {
						$old_detail_id = $detail_id;
						$recenueCharge[$id] = $city_name;
					}						
				}

				foreach ($val_detail as $key => $value) {
					$detail_id = $this->Common->filterEmptyField($value, 'RevenueDetail', 'id');
					$is_charge = $this->Common->filterEmptyField($value, 'RevenueDetail', 'is_charge');
					$revenue_jenis_tarif = $this->Common->filterEmptyField($value, 'Revenue', 'revenue_tarif_type');
					$jenis_tarif = $this->Common->filterEmptyField($value, 'RevenueDetail', 'payment_type');
					$tarif_angkutan_type = $this->Common->filterEmptyField($value, 'RevenueDetail', 'tarif_angkutan_type');
					$price_unit = $this->Common->filterEmptyField($value, 'RevenueDetail', 'price_unit');
					$total_price_unit = $this->Common->filterEmptyField($value, 'RevenueDetail', 'total_price_unit');
					$no_do = $this->Common->filterEmptyField($value, 'RevenueDetail', 'no_do');
					$no_sj = $this->Common->filterEmptyField($value, 'RevenueDetail', 'no_sj');
					$date_revenue = $this->Common->filterEmptyField($value, 'Revenue', 'date_revenue');

					$id = $this->Common->filterEmptyField($value, 'Revenue', 'id');
					$no_doc = $this->Common->filterEmptyField($value, 'Revenue', 'no_doc');
					$revenue_id = $this->Common->filterEmptyField($value, 'Revenue', 'id');
					$city_name = $this->Common->filterEmptyField($value, 'City', 'name');

					$revenue_temp = sprintf('%s-%s', $revenue_id, $is_charge);
					$qty = $value['RevenueDetail']['qty_unit'];
					$grandTotalUnit += $qty;

					$price = 0;
					$totalPriceFormat = '';
					$date_revenue = $this->Common->formatDate($date_revenue, 'd/m/Y');

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

					if( in_array($data_print, array( 'date', 'sa' )) ) {
						$colom .= $this->Html->tag('td', $city_name);
					}

					$colom .= $this->Html->tag('td', $nopol);
					
					if( in_array($data_print, array( 'sa' )) ) {
						$no_do_sj = array();

						if( !empty($no_do) ) {
							$no_do_sj[] = $no_do;
						}
						if( !empty($no_sj) ) {
							$no_do_sj[] = $no_sj;
						}

						if( !empty($no_do_sj) ) {
							$colom .= $this->Html->tag('td', implode(' / ', $no_do_sj), array(
								'class' => 'string'
							));
						} else {
							$colom .= $this->Html->tag('td', '');
						}

						if( in_array($data_print, array( 'sa' )) ) {
							$colom .= $this->Html->tag('td', $no_doc);
						}
					} else {
						$colom .= $this->Html->tag('td', $no_do);
						$colom .= $this->Html->tag('td', $no_sj);
					}

					$colom .= $this->Html->tag('td', $date_revenue);

					if( in_array($data_print, array( 'hso-smg', 'preview' )) ) {
						$colom .= $this->Html->tag('td', $city_name);
					}

					$colom .= $this->Html->tag('td', $qty, array(
						'align' => 'center'
					));
					$colom .= $this->Html->tag('td', $priceFormat, array(
						'align' => 'right'
					));

					if( $revenue_jenis_tarif == 'per_truck' ){
						if( !empty($recenueCnt[$id][$detail_id]) ) {
							$colom .= $this->Html->tag('td', $totalPriceFormat, array(
								'align' => 'right',
								'rowspan' => $recenueCnt[$id][$detail_id] + 1,
							));
						} else if( !empty($is_charge) ) {
							$colom .= $this->Html->tag('td', $totalPriceFormat, array(
								'align' => 'right'
							));
						} else if( !empty($recenueCol[$detail_id]) ) {
							$colom .= $this->Html->tag('td', '');
						}
					} else {
						$colom .= $this->Html->tag('td', $totalPriceFormat, array(
							'align' => 'right'
						));
					}

					$colom .= $this->Html->tag('td', $this->Common->getNoRef($revenue_id), array(
		                'class' => 'string',
		            ));
					$trData .= $this->Html->tag('tr', $colom);
					$grandTotal += $total_price_unit;
					$old_revenue_id = sprintf('%s-%s', $revenue_id, $is_charge);
				}

				echo $trData;

				$colom = $this->Html->tag('td', __('Total '), array(
					'colspan' => $totalMergeTotal,
					'style' => 'text-align: right;',
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

				// if( !empty($ppn) ) {
				// 	$colom = $this->Html->tag('td', '&nbsp;', array(
				// 		'colspan' => $totalMergeTotal+1,
				// 	));
				// 	$colom .= $this->Html->tag('td', __('PPN '), array(
				// 		'align' => 'right',
				// 		'style' => 'font-weight: bold;',
				// 	));
				// 	$colom .= $this->Html->tag('td', $this->Common->getFormatPrice($ppn), array(
				// 		'align' => 'right',
				// 		'style' => 'font-weight: bold;',
				// 	));
				// 	$colom .= $this->Html->tag('td', '&nbsp;');

				// 	echo $this->Html->tag('tr', $colom, array(
				// 		'class' => 'total-row'
				// 	));
				// }

				// if( !empty($ppn) || !empty($pph) ) {
				// 	// $grandTotalInvoice = $grandTotal + $ppn - $pph;
				// 	$colom = $this->Html->tag('td', '&nbsp;', array(
				// 		'colspan' => $totalMergeTotal+1,
				// 	));
				// 	$colom .= $this->Html->tag('td', __('Grantotal '), array(
				// 		'align' => 'right',
				// 		'style' => 'font-weight: bold;',
				// 	));
				// 	$colom .= $this->Html->tag('td', $this->Common->getFormatPrice($grandTotalInvoice), array(
				// 		'align' => 'right',
				// 		'style' => 'font-weight: bold;',
				// 	));
				// 	$colom .= $this->Html->tag('td', '&nbsp;');

				// 	echo $this->Html->tag('tr', $colom, array(
				// 		'class' => 'total-row'
				// 	));
				// }
			}else{
				$colom = $this->Html->tag('td', __('Data tidak ditemukan.'), array(
					'colspan' => $totalMerge,
				));

				echo $this->Html->tag('tr', $colom);
			}

			$footerGrandTotal += $grandTotal;
			$footerGrandTotalUnit += $grandTotalUnit;

			if( $idx == count($revenue_detail) && in_array($data_print, array( 'hso-smg', 'invoice' )) ) {
				$footerGrandTotal = $this->Common->getFormatPrice($footerGrandTotal);
				$footerGrandTotalUnit = $this->Common->getFormatPrice($footerGrandTotalUnit);

				$colom = $this->Html->tag('td', __('Grandtotal'), array(
					'colspan' => $totalMergeTotal,
					'style' => 'text-align: right;',
				));
				$colom .= $this->Html->tag('td', $footerGrandTotalUnit, array(
					'align' => 'center'
				));
				$colom .= $this->Html->tag('td', '&nbsp;', array(
					'align' => 'right',
					'style' => 'font-weight: bold;',
				));
				$colom .= $this->Html->tag('td', $footerGrandTotal, array(
					'align' => 'right',
					'style' => 'font-weight: bold;',
				));
				$colom .= $this->Html->tag('td', '&nbsp;');
				
				echo $this->Html->tag('tr', $colom, array(
					'class' => 'total-row'
				));
			}
	?>
</table>
<?php
			}
		} else {
            echo $this->Html->tag('p', $this->Html->tag('td', __('Data belum tersedia.'), array(
                'class' => 'alert alert-warning text-center',
            )));
        }

        if( empty($preview) && empty($action_print) ) {
?>
<div class="box-footer text-center action hidden-print">
	<?php
    		echo $this->Html->link(__('Kembali'), array(
                'controller' => 'revenues', 
				'action' => 'invoices', 
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>
<?php 
		}
?>