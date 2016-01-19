<style>
    .string{ mso-number-format:\@; }
</style>
<?php
		if(!empty($revenue_detail)){
			foreach ($revenue_detail as $key => $val_detail) {
				$data_print = !empty($data_print)?$data_print:'invoice';

				if( !empty($data_print) && $data_print == 'date' ) {
					$totalMerge = 10;
					$totalMergeTotal = 6;
				} else {
					$totalMerge = 9;
					$totalMergeTotal = 5;
				}
?>
<table border="1" width="100%" style="margin-top: 20px;">
	<thead class="header-invoice-print">
		<?php 
				if( $data_print != 'date' ) {
		?>
		<tr>
			<th colspan="<?php echo $totalMerge; ?>" class="text-center" style="text-transform:uppercase;">
				<?php 
						if($action == 'tarif' && $data_print == 'invoice'){
							printf('Tarif Angkutan : %s', $this->Number->currency($val_detail[0]['RevenueDetail']['price_unit'], Configure::read('__Site.config_currency_second_code'), array('places' => 0)) );
						}else{
			                if( $data_print == 'date' && !empty($val_detail[0]['Revenue']['date_revenue']) ) {
								echo $this->Common->customDate($val_detail[0]['Revenue']['date_revenue'], 'd/m/Y');
			                } else {
			                	if( $val_detail[0]['Revenue']['revenue_tarif_type'] == 'per_truck' && !empty($val_detail[0]['Revenue']['no_doc']) ) {
									echo $val_detail[0]['Revenue']['no_doc'];
			                	} else {
									echo $val_detail[0]['City']['name'];
								}
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
					if( !empty($data_print) && $data_print == 'date' ) {
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
							echo __('No.DO');
						}
				?>
			</th>
			<th class="text-center" style="width: 15%;"><?php echo __('No. SJ');?></th>
			<!-- <th class="text-center"><?php // echo __('Keterangan');?></th> -->
			<th class="text-center" style="width: 10%;"><?php echo __('Tanggal');?></th>
			<th class="text-center" style="width: 8%;"><?php echo __('Total Unit');?></th>
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
						} else if( !empty($value['RevenueDetail']['total_price_unit']) ) {
							$price = $value['RevenueDetail']['price_unit'];
							$priceFormat = $this->Number->currency($price, '', array('places' => 0));
						} else {
							$priceFormat = '-';
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
						$colom .= $this->Html->tag('td', $value['RevenueDetail']['no_do']);
						$colom .= $this->Html->tag('td', $value['RevenueDetail']['no_sj']);
						// $colom .= $this->Html->tag('td', $value['RevenueDetail']['note']);
						$colom .= $this->Html->tag('td', $this->Common->customDate($value['Revenue']['date_revenue'], 'd/m/Y'));
						$colom .= $this->Html->tag('td', $qty, array(
							'align' => 'center'
						));
						$colom .= $this->Html->tag('td', $priceFormat, array(
							'align' => 'right'
						));

						if( $jenis_tarif == 'per_truck' ){
							if( !empty($value['RevenueDetail']['total_price_unit']) ) {
								$total = $value['RevenueDetail']['total_price_unit'];

								$colom .= $this->Html->tag('td', $this->Number->currency($total, '', array('places' => 0)), array(
									'align' => 'right'
								));
							} else {
								if( $revenue_temp != $old_revenue_id ) {
									$total = !empty($value['Revenue']['tarif_per_truck'])?$value['Revenue']['tarif_per_truck']:0;
									$colom .= $this->Html->tag('td', $this->Number->currency($total, '', array('places' => 0)), array(
										'align' => 'right',
										'rowspan' => !empty($recenueCnt[$revenue_id])?$recenueCnt[$revenue_id]:false,
									));
								}
							}
						} else if( !empty($value['RevenueDetail']['total_price_unit']) ) {
							$total = $price * $qty;

							$colom .= $this->Html->tag('td', $this->Number->currency($total, '', array('places' => 0)), array(
								'align' => 'right'
							));
						} else {
							$colom .= $this->Html->tag('td', '-', array(
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

					$colom = $this->Html->tag('td', '&nbsp;', array(
						'colspan' => $totalMergeTotal,
					));
					$colom .= $this->Html->tag('td', $this->Number->format($grandTotalUnit), array(
						'align' => 'center'
					));
					$colom .= $this->Html->tag('td', __('Total '), array(
						'align' => 'right',
						'style' => 'font-weight: bold;',
					));
					$colom .= $this->Html->tag('td', $this->Number->currency($grandTotal, Configure::read('__Site.config_currency_second_code'), array('places' => 0)), array(
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
						$colom .= $this->Html->tag('td', $this->Number->currency($ppn, Configure::read('__Site.config_currency_second_code'), array('places' => 0)), array(
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
					// 	$colom .= $this->Html->tag('td', $this->Number->currency($pph, Configure::read('__Site.config_currency_second_code'), array('places' => 0)), array(
					// 		'align' => 'right',
					// 		'style' => 'font-weight: bold;',
					// 	));
					// 	$colom .= $this->Html->tag('td', '&nbsp;');

					// 	echo $this->Html->tag('tr', $colom, array(
					// 		'class' => 'total-row'
					// 	));
					// }

					if( !empty($ppn) || !empty($pph) ) {
						// $grandTotalInvoice = $grandTotal + $ppn - $pph;
						$grandTotalInvoice = $grandTotal + $ppn;
						$colom = $this->Html->tag('td', '&nbsp;', array(
							'colspan' => $totalMergeTotal+1,
						));
						$colom .= $this->Html->tag('td', __('Grantotal '), array(
							'align' => 'right',
							'style' => 'font-weight: bold;',
						));
						$colom .= $this->Html->tag('td', $this->Number->currency($grandTotalInvoice, Configure::read('__Site.config_currency_second_code'), array('places' => 0)), array(
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