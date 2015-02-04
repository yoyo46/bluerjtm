<?php
	if(!empty($revenue_detail)){
		foreach ($revenue_detail as $key => $val_detail) {
?>
<table border="1">
	<thead>
		<tr>
			<th colspan="5" align="left">
				<?php 
					if($action == 'tarif'){
						printf('Tarif Angkutan : %s', $this->Number->currency($val_detail[0]['RevenueDetail']['price_unit'], Configure::read('__Site.config_currency_code'), array('places' => 0)) );
					}else{
						printf('Kota : %s', $val_detail[0]['City']['name']);
					}
				?>
			</th>
		</tr>
		<tr>
			<th><?php echo __('No.');?></th>
			<th><?php echo __('Nama Tipe Motor.');?></th>
			<th><?php echo __('qty.');?></th>
			<th><?php echo __('Harga.');?></th>
			<th><?php echo __('No. Ref');?></th>
		</tr>
	</thead>
	<tbody>
		<?php
			if(!empty($val_detail)){
				$no=1;
				$total = 0;
				foreach ($val_detail as $key => $value) {
					$colom = $this->Html->tag('td', $no++);
					$colom .= $this->Html->tag('td', $value['TipeMotor']['name']);
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
					'colspan' => 3
				));
				$colom .= $this->Html->tag('td', $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0)), array('colspan' => 2) );
				echo $this->Html->tag('tr', $colom);
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