<?php 
		$data_print_type = isset($data_print_type)?$data_print_type:false;
?>
<thead class="header-invoice-print">
	<tr>
		<?php 
				echo $this->Html->tag('th', __('No.'), array(
					'style' => 'text-align:center;'.(($data_print_type == 'pdf')?'':'width: 30px;'),
					'rowspan' => 2,
				));
				echo $this->Html->tag('th', __('No. Truk'), array(
					'style' => 'text-align:center;'.(($data_print_type == 'pdf')?'':'width: 100px;'),
					'rowspan' => 2,
				));
				echo $this->Html->tag('th', __('No. Dokumen'), array(
					'style' => 'text-align:center;'.(($data_print_type == 'pdf')?'':'width: 120px;'),
					'rowspan' => 2,
				));
				echo $this->Html->tag('th', __('Nomor'), array(
					'style' => 'text-align:center;'.(($data_print_type == 'pdf')?'':'width: 120px;'),
					'rowspan' => 2,
				));
				echo $this->Html->tag('th', __('Date'), array(
					'style' => 'text-align:center;'.(($data_print_type == 'pdf')?'':'width: 100px;'),
					'rowspan' => 2,
				));
				echo $this->Html->tag('th', __('Shipping list'), array(
					'style' => 'text-align:center;',
					'colspan' => 3,
				));
				echo $this->Html->tag('th', __('Amount'), array(
					'style' => 'text-align:center;'.(($data_print_type == 'pdf')?'':'width: 120px;'),
					'rowspan' => 2,
				));
				echo $this->Html->tag('th', __('Ket'), array(
					'style' => 'text-align:center;'.(($data_print_type == 'pdf')?'':'width: 120px;'),
					'rowspan' => 2,
				));
		?>
	</tr>
	<tr>
		<?php 
				echo $this->Html->tag('th', __('Nomor'), array(
					'style' => 'text-align:center;'.(($data_print_type == 'pdf')?'':'width: 120px;'),
				));
				echo $this->Html->tag('th', __('Unit'), array(
					'style' => 'text-align:center;'.(($data_print_type == 'pdf')?'':'width: 80px;'),
				));
				echo $this->Html->tag('th', __('Tarif'), array(
					'style' => 'text-align:center;'.(($data_print_type == 'pdf')?'':'width: 110px;'),
				));
		?>
	</tr>
</thead>
<tbody>
	<?php
			if(!empty($invoice['Revenue'])){
				$no = 1;
				$grandTotalUnit = 0;
				$grandTotalTarif = 0;

				foreach ($invoice['Revenue'] as $key => $revenue) {
					$totalUnit = 0;
					$totalTarif = 0;

					if( !empty($revenue['RevenueDetail'][0]) ) {
						$revenueDetail = $revenue['RevenueDetail'][0];
						$totalUnit += $revenueDetail['qty_unit'];
						$grandTotalUnit += $revenueDetail['qty_unit'];
						$amount = $revenueDetail['qty_unit'] * $revenueDetail['price_unit'];
						$totalTarif += $amount;
						$grandTotalTarif += $amount;
						unset($revenue['RevenueDetail'][0]);
					} else {
						$revenueDetail = false;
					}

					echo $this->element('blocks/revenues/invoice_hso', array(
						'no' => $no,
						'revenue' => $revenue,
						'revenueDetail' => $revenueDetail,
					));

					if( !empty($revenue['RevenueDetail']) ) {
						foreach ($revenue['RevenueDetail'] as $key => $revenueDetail) {
							$totalUnit += $revenueDetail['qty_unit'];
							$grandTotalUnit += $revenueDetail['qty_unit'];
							$amount = $revenueDetail['qty_unit'] * $revenueDetail['price_unit'];
							$totalTarif += $amount;
							$grandTotalTarif += $amount;
							echo $this->element('blocks/revenues/invoice_hso', array(
								'revenue' => $revenue,
								'revenueDetail' => $revenueDetail,
							));
						}
					}

					$colom = $this->Html->tag('td', $this->Html->tag('strong', __('Jumlah')), array(
						'colspan' => 6,
						'style' => 'text-align: right;',
					));
					$colom .= $this->Html->tag('td', $this->Html->tag('strong', $totalUnit), array(
						'style' => 'text-align: center;',
					));
					$colom .= $this->Html->tag('td', '');
					$colom .= $this->Html->tag('td', $this->Html->tag('strong', $this->Number->format($totalTarif, Configure::read('__Site.config_currency_code'), array('places' => 0))), array(
						'style' => 'text-align: right;',
					));
					$colom .= $this->Html->tag('td', '');

					echo $this->Html->tag('tr', $colom);

					$no++;
				}

				$colom = $this->Html->tag('td', __('Grandtotal'), array(
					'colspan' => 6,
					'style' => 'text-align: right;',
				));
				$colom .= $this->Html->tag('td', $grandTotalUnit, array(
					'style' => 'text-align: center;',
				));
				$colom .= $this->Html->tag('td', '');
				$colom .= $this->Html->tag('td', $this->Number->format($grandTotalTarif, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
					'style' => 'text-align: right;',
				));
				$colom .= $this->Html->tag('td', '');

				echo $this->Html->tag('tr', $colom, array(
					'style' => 'font-weight: bold;background-color: #3c8dbc;color: #FFFFFF;'
				));
			}else{
				$colom = $this->Html->tag('td', __('Data tidak ditemukan.'), array(
					'colspan' => $totalMerge,
				));

				echo $this->Html->tag('tr', $colom);
			}
	?>
</tbody>