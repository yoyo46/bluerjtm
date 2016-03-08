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

				if( in_array($data_print, array( 'date', 'hso-smg' )) ) {
					$totalMerge = 10;
					
					if( $data_print == 'hso-smg' ) {
						$totalMergeTotal = 5;
					} else {
						$totalMergeTotal = 6;
					}
				} else {
					$totalMerge = 9;
					$totalMergeTotal = 5;
				}
?>
<table border="1" width="100%" style="margin-top: 20px;">
	<thead class="header-invoice-print">
		<?php 
				if( !in_array($data_print, array( 'date', 'hso-smg' )) ) {
		?>
		<tr>
			<th colspan="<?php echo $totalMerge; ?>" class="text-center" style="text-transform:uppercase;">
				<?php 
						if($action == 'tarif' && $data_print == 'invoice'){
							printf('Tarif Angkutan : %s', $this->Common->getFormatPrice($val_detail[0]['RevenueDetail']['price_unit']) );
						}else{
			                if( in_array($data_print, array( 'date' )) && !empty($val_detail[0]['Revenue']['date_revenue']) ) {
								echo $this->Common->customDate($val_detail[0]['Revenue']['date_revenue'], 'd/m/Y');
			                } else {
			      //           	if( $val_detail[0]['Revenue']['revenue_tarif_type'] == 'per_truck' && !empty($val_detail[0]['Revenue']['no_doc']) ) {
									// echo $val_detail[0]['Revenue']['no_doc'];
			      //           	} else {
			                		$no_doc = !empty($val_detail[0]['Revenue']['no_doc'])?$val_detail[0]['Revenue']['no_doc']:false;
									echo !empty($val_detail[0]['City']['name'])?$val_detail[0]['City']['name']:$no_doc;
								// }
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
					if( $data_print == 'date' ) {
						echo $this->Html->tag('th', __('Kota'), array(
							'class' => 'text-center',
							'width' => '13%'
						));
					}
			?>
			<th class="text-center" style="width: 10%;"><?php echo __('No. Truk');?></th>
			<th class="text-center" style="width: 12%;">
				<?php
						if( $data_print == 'date' ) {
							echo __('Keterangan');
						} else {
							if( $data_print != 'hso-smg' ) {
								echo __('No.DO');
							} else {
								echo __('No.DO - Nama Dealer');
							}
						}
				?>
			</th>
			<?php 
					if( $data_print != 'hso-smg' ) {
						echo $this->Html->tag('th', __('No. SJ'), array(
							'class' => 'text-center',
							'width' => '15%'
						));
					}
			?>
			<th class="text-center" style="width: 10%;"><?php echo __('Tanggal');?></th>
			<?php 
					if( $data_print == 'hso-smg' ) {
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
						$revenue_id = !empty($value['Revenue']['id'])?$value['Revenue']['id']:false;

						if( empty($value['RevenueDetail']['total_price_unit']) ) {
							if( !empty($recenueCnt[$revenue_id]) ) {
								$recenueCnt[$revenue_id]++;
							} else {
								$recenueCnt[$revenue_id] = 1;
							}
						}
					}

					foreach ($val_detail as $key => $value) {
						$revenue_id = !empty($value['Revenue']['id'])?$value['Revenue']['id']:false;
						$is_charge = !empty($value['RevenueDetail']['is_charge'])?$value['RevenueDetail']['is_charge']:false;
						$revenue_temp = sprintf('%s-%s', $revenue_id, $is_charge);

						$grandTotalUnit += $qty = $value['RevenueDetail']['qty_unit'];
						$price = 0;
						$total = 0;
						$payment_type = !empty($value['RevenueDetail']['payment_type'])?$value['RevenueDetail']['payment_type']:false;
						$jenis_tarif = !empty($value['Revenue']['revenue_tarif_type'])?$value['Revenue']['revenue_tarif_type']:false;

						if( $payment_type == 'per_truck' ){
							$priceFormat = '-';
						} else {
							$price = $value['RevenueDetail']['price_unit'];
							$priceFormat = $this->Common->getFormatPrice($price);
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

						if( $data_print == 'date' ) {
							$city_name = !empty($value['City']['name'])?$value['City']['name']:false;
							$colom .= $this->Html->tag('td', $value['City']['name']);
						}

						$colom .= $this->Html->tag('td', $nopol);
						$colom .= $this->Html->tag('td', $value['RevenueDetail']['no_do']);

						if( $data_print != 'hso-smg' ) {
							$colom .= $this->Html->tag('td', $value['RevenueDetail']['no_sj']);
						}

						$colom .= $this->Html->tag('td', $this->Common->customDate($value['Revenue']['date_revenue'], 'd/m/Y'));

						if( $data_print == 'hso-smg' ) {
							$city_name = !empty($value['City']['name'])?$value['City']['name']:false;
							$colom .= $this->Html->tag('td', $value['City']['name']);
						}

						$colom .= $this->Html->tag('td', $qty, array(
							'align' => 'center'
						));
						$colom .= $this->Html->tag('td', $priceFormat, array(
							'align' => 'right'
						));

						if( $jenis_tarif == 'per_truck' ){
							if( !empty($value['RevenueDetail']['total_price_unit']) ) {
								$total = $value['RevenueDetail']['total_price_unit'];

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
						} else {
							$total = $price * $qty;

							$colom .= $this->Html->tag('td', $this->Common->getFormatPrice($total), array(
								'align' => 'right'
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

					// if( !empty($pph) ) {
					// 	$colom = $this->Html->tag('td', '&nbsp;', array(
					// 		'colspan' => $totalMergeTotal+1,
					// 	));
					// 	$colom .= $this->Html->tag('td', __('PPh '), array(
					// 		'align' => 'right',
					// 		'style' => 'font-weight: bold;',
					// 	));
					// 	$colom .= $this->Html->tag('td', $this->Common->getFormatPrice($pph), array(
					// 		'align' => 'right',
					// 		'style' => 'font-weight: bold;',
					// 	));
					// 	$colom .= $this->Html->tag('td', '&nbsp;');

					// 	echo $this->Html->tag('tr', $colom, array(
					// 		'class' => 'total-row'
					// 	));
					// }

					$grandTotalInvoice = $grandTotal + $ppn;

					if( !empty($ppn) || !empty($pph) ) {
						// $grandTotalInvoice = $grandTotal + $ppn - $pph;
						$colom = $this->Html->tag('td', '&nbsp;', array(
							'colspan' => $totalMergeTotal+1,
						));
						$colom .= $this->Html->tag('td', __('Grantotal '), array(
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

				$footerGrandTotal += $grandTotalInvoice;
				$footerGrandTotalUnit += $grandTotalUnit;

				if( $idx == count($revenue_detail) && $data_print == 'hso-smg' ) {
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
	</tbody>
</table>
<?php
			}
		} else {
            echo $this->Html->tag('p', $this->Html->tag('td', __('Data belum tersedia.'), array(
                'class' => 'alert alert-warning text-center',
            )));
        }

        if( empty($preview) ) {
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