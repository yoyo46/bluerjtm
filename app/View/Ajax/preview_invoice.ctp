<?php
	if(!empty($revenue_detail)){
		foreach ($revenue_detail as $key => $val_detail) {
?>
<table border="1" style="margin-bottom: 20px;">
	<thead>
		<tr>
			<th colspan="9" style="text-align: left;">
				<?php 
						if($action == 'tarif'){
							printf('Tarif Angkutan : %s', $this->Number->currency($val_detail[0]['RevenueDetail']['price_unit'], Configure::read('__Site.config_currency_code'), array('places' => 0)) );
						}else{
							printf('No. Ref: %s', str_pad($val_detail[0]['Revenue']['id'], 5, '0', STR_PAD_LEFT));
						}
				?>
			</th>
		</tr>
		<tr>
			<th><?php echo __('No.');?></th>
			<th><?php echo __('No. Truk');?></th>
			<th><?php echo __('No.DO');?></th>
			<th><?php echo __('Shipping List');?></th>
			<th><?php echo __('Tanggal');?></th>
			<th><?php echo __('Group Motor');?></th>
			<th><?php echo __('Unit');?></th>
			<th><?php echo __('Tarif');?></th>
			<th><?php echo __('Harga');?></th>
		</tr>
	</thead>
	<tbody>
		<?php
				if(!empty($val_detail)){
					$flagTotal = false;
					$no = 1;
					$total = 0;
					$qtyTotal = 0;
					$ppn = !empty($val_detail[0]['Revenue']['ppn'])?$val_detail[0]['Revenue']['ppn']:0;
					$pph = !empty($val_detail[0]['Revenue']['pph'])?$val_detail[0]['Revenue']['pph']:0;
					$mergeTotalUnit = Set::extract('/RevenueDetail/total_price_unit', $val_detail);
					$fillTotalUnit = array_filter($mergeTotalUnit);
					$cntMergeTotalUnit = count($mergeTotalUnit) - count($fillTotalUnit);

					foreach ($val_detail as $key => $value) {
						$qtyTotal += $value['RevenueDetail']['qty_unit'];
						$priceUnit = 0;
						$totalPriceUnit = 0;

						if($value['TarifAngkutan']['jenis_unit'] == 'per_truck'){
							$totalPriceUnit = $value['RevenueDetail']['total_price_unit'];
							$priceUnit = '';
						}else{
							$totalPriceUnit = $value['RevenueDetail']['price_unit'] * $value['RevenueDetail']['qty_unit'];
							$priceUnit = $value['RevenueDetail']['price_unit'];
						}

						$colom = $this->Html->tag('td', $no++);
						$colom .= $this->Html->tag('td', !empty($value['Revenue']['Ttuj']['nopol'])?$value['Revenue']['Ttuj']['nopol']:'-');
						$colom .= $this->Html->tag('td', !empty($value['RevenueDetail']['no_do'])?$value['RevenueDetail']['no_do']:'-');
						$colom .= $this->Html->tag('td', !empty($value['RevenueDetail']['no_sj'])?$value['RevenueDetail']['no_sj']:'-');
						$colom .= $this->Html->tag('td', date('d/m/Y', strtotime($value['Revenue']['date_revenue'])));
						$colom .= $this->Html->tag('td', !empty($value['GroupMotor']['name'])?$value['GroupMotor']['name']:'-');
						$colom .= $this->Html->tag('td', $value['RevenueDetail']['qty_unit']);
						$colom .= $this->Html->tag('td', $this->Number->currency($priceUnit, Configure::read('__Site.config_currency_code'), array('places' => 0)));

						if( empty($totalPriceUnit) && empty($flagTotal) && !empty($cntMergeTotalUnit) ) {
							$colom .= $this->Html->tag('td', $this->Number->currency($value['Revenue']['tarif_per_truck'], Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
								'rowspan' => $cntMergeTotalUnit,
							));
							$flagTotal = true;
							$total += $value['Revenue']['tarif_per_truck'];
						} else if( !empty($totalPriceUnit) ) {
							$colom .= $this->Html->tag('td', $this->Number->currency($totalPriceUnit, Configure::read('__Site.config_currency_code'), array('places' => 0)));
							$flagTotal = false;
							$total += $totalPriceUnit;
						}

						echo $this->Html->tag('tr', $colom);
					}
					$colom = $this->Html->tag('td', '', array(
						'colspan' => 6,
						'style' => 'text-align: right;'
					));
					$colom .= $this->Html->tag('td', $this->Number->currency($qtyTotal, '', array('places' => 0)), array('colspan' => 1) );
					$colom .= $this->Html->tag('td', __('Total'), array(
						'colspan' => 1,
						'style' => 'text-align: right;font-weight:bold;'
					) );
					$colom .= $this->Html->tag('td', $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0)), array('colspan' => 1) );
					echo $this->Html->tag('tr', $colom);
					$grandtotal = $total;

					$ppn = $this->Common->calcFloat($total, $val_detail[0]['Revenue']['ppn']);
					$grandtotal += $ppn;
					$colom = $this->Html->tag('td', __('PPN'), array(
						'colspan' => 8,
						'style' => 'text-align: right;font-weight:bold;'
					));
					$colom .= $this->Html->tag('td', $this->Number->currency($ppn, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
						'style' => 'text-align: right;'
					));
					echo $this->Html->tag('tr', $colom);

					$pph = $this->Common->calcFloat($total, $val_detail[0]['Revenue']['pph'])*-1;
					$grandtotal += $pph;
					$colom = $this->Html->tag('td', __('pph'), array(
						'colspan' => 8,
						'style' => 'text-align: right;font-weight:bold;'
					));
					$colom .= $this->Html->tag('td', $this->Number->currency($pph, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
						'style' => 'text-align: right;'
					));
					echo $this->Html->tag('tr', $colom);

					$colom = $this->Html->tag('td', __('Grandtotal'), array(
						'colspan' => 8,
						'style' => 'text-align: right;font-weight:bold;'
					));
					$colom .= $this->Html->tag('td', $this->Number->currency($grandtotal, Configure::read('__Site.config_currency_code'), array('places' => 0)), array('colspan' => 1) );
					echo $this->Html->tag('tr', $colom);
				}else{
					$colom = $this->Html->tag('td', __('Data tidak ditemukan.'), array(
						'colspan' => 9
					));

					echo $this->Html->tag('tr', $colom);
				}
		?>
	</tbody>
</table>
<?php
		}
	}
?>