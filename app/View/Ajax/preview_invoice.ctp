<?php
	if(!empty($revenue_detail)){
		foreach ($revenue_detail as $key => $val_detail) {
?>
<table border="1" width="100%">
		<thead class="header-invoice-print">
			<tr>
				<th colspan="8" class="text-center" style="text-transform:uppercase;">
					<?php 
						if($action == 'tarif'){
							printf('Tarif Angkutan : %s', $this->Number->currency($val_detail[0]['RevenueDetail']['price_unit'], Configure::read('__Site.config_currency_code'), array('places' => 0)) );
						}else{
							echo $val_detail[0]['City']['name'];
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
					foreach ($val_detail as $key => $value) {
						$grandTotalUnit += $qty = $value['RevenueDetail']['qty_unit'];
						$price = $value['RevenueDetail']['price_unit'];
						$total = $qty * $price;
						$grandTotal += $total; 

						$colom = $this->Html->tag('td', $no++);
						$colom .= $this->Html->tag('td', $value['Ttuj']['nopol']);
						$colom .= $this->Html->tag('td', $value['RevenueDetail']['no_do']);
						$colom .= $this->Html->tag('td', $value['Revenue']['date_revenue']);
						$colom .= $this->Html->tag('td', $qty, array(
							'align' => 'center'
						));
						$colom .= $this->Html->tag('td', $this->Number->currency($price, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
							'align' => 'right'
						));
						$colom .= $this->Html->tag('td', $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
							'align' => 'right'
						));
						$colom .= $this->Html->tag('td', $value['RevenueDetail']['no_reference']);

						if(!empty($value['RevenueDetail']['payment_type']) && $value['RevenueDetail']['payment_type'] == 'per_truck'){
							$total += $value['RevenueDetail']['price_unit'];
						}else{
							$total += $value['RevenueDetail']['price_unit'] * $value['RevenueDetail']['qty_unit'];
						}
						echo $this->Html->tag('tr', $colom);
					}
					$colom = $this->Html->tag('td', __('Total '), array(
						'colspan' => 4,
						'align' => 'right'
					));
					$colom .= $this->Html->tag('td', $this->Number->format($grandTotalUnit), array(
						'class' => 'text-center'
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
	}
?>