<?php
	if(!empty($revenue_detail)){
		foreach ($revenue_detail as $key => $val_detail) {
?>
<table border="1">
	<thead>
		<tr>
			<th colspan="8" class="text-center">
				<?php 
						if($action == 'tarif'){
							printf('Tarif Angkutan : %s', $this->Number->currency($val_detail[0]['RevenueDetail']['price_unit'], Configure::read('__Site.config_currency_code'), array('places' => 0)) );
						}else{
							printf('%s', $val_detail[0]['City']['name']);
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
			<th><?php echo __('Total Unit');?></th>
			<th><?php echo __('Harga');?></th>
			<th><?php echo __('No. Ref');?></th>
		</tr>
	</thead>
	<tbody>
		<?php
				if(!empty($val_detail)){
					$no=1;
					$total = 0;
					$qtyTotal = 0;

					foreach ($val_detail as $key => $value) {
						$qtyTotal += $value['RevenueDetail']['qty_unit'];

						$colom = $this->Html->tag('td', $no++);
						$colom .= $this->Html->tag('td', !empty($value['Revenue']['Ttuj']['nopol'])?$value['Revenue']['Ttuj']['nopol']:'-');
						$colom .= $this->Html->tag('td', !empty($value['RevenueDetail']['no_do'])?$value['RevenueDetail']['no_do']:'-');
						$colom .= $this->Html->tag('td', !empty($value['RevenueDetail']['no_sj'])?$value['RevenueDetail']['no_sj']:'-');
						$colom .= $this->Html->tag('td', date('d/m/Y', strtotime($value['Revenue']['date_revenue'])));
						$colom .= $this->Html->tag('td', $value['RevenueDetail']['qty_unit']);
						$colom .= $this->Html->tag('td', $this->Number->currency($value['RevenueDetail']['price_unit'], Configure::read('__Site.config_currency_code'), array('places' => 0)));
						$colom .= $this->Html->tag('td', $value['RevenueDetail']['no_reference']);

						if($value['TarifAngkutan']['jenis_unit'] == 'per_truck'){
							$total += $value['RevenueDetail']['price_unit'];
						}else{
							$total += $value['RevenueDetail']['price_unit'] * $value['RevenueDetail']['qty_unit'];
						}
						echo $this->Html->tag('tr', $colom);
					}
					$colom = $this->Html->tag('td', __('Total'), array(
						'colspan' => 5
					));
					$colom .= $this->Html->tag('td', $this->Number->currency($qtyTotal, '', array('places' => 0)), array('colspan' => 1) );
					$colom .= $this->Html->tag('td', $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0)), array('colspan' => 1) );
					echo $this->Html->tag('tr', $colom);
				}else{
					$colom = $this->Html->tag('td', __('Data tidak ditemukan.'), array(
						'colspan' => 7
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