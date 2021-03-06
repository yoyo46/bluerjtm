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
				echo $this->Html->tag('th', __('No. SJ'), array(
					'style' => 'text-align:center;'.(($data_print_type == 'pdf')?'':'width: 120px;'),
					'rowspan' => 2,
				));
				echo $this->Html->tag('th', __('Nomor'), array(
					'style' => 'text-align:center;'.(($data_print_type == 'pdf')?'':'width: 120px;'),
					'rowspan' => 2,
				));
				echo $this->Html->tag('th', __('Shipping list'), array(
					'style' => 'text-align:center;',
					'colspan' => 3,
				));
				echo $this->Html->tag('th', __('Unit'), array(
					'style' => 'text-align:center;'.(($data_print_type == 'pdf')?'':'width: 80px;'),
					'rowspan' => 2,
				));
				echo $this->Html->tag('th', __('Rate'), array(
					'style' => 'text-align:center;'.(($data_print_type == 'pdf')?'':'width: 110px;'),
					'rowspan' => 2,
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
				echo $this->Html->tag('th', __('Date'), array(
					'style' => 'text-align:center;'.(($data_print_type == 'pdf')?'':'width: 100px;'),
				));
		?>
	</tr>
</thead>
<tbody>
	<?php
			if(!empty($invoice['Revenue'])){
				$no = 1;
				$grandTotalUnit = 0;
				$grandTotalMainUnit = 0;
				$grandTotalTarif = 0;
				$tempRate = 0;

				foreach ($invoice['Revenue'] as $key => $revenue) {
					$showRate = false;
					$id = !empty($revenue['Revenue']['id'])?$revenue['Revenue']['id']:false;
					$totalUnit = !empty($revenue['qty_unit'])?$revenue['qty_unit']:0;
					$grandTotalMainUnit += $totalUnit;
					$is_charge = false;
					$revenue_temp = sprintf('%s-%s', $id, $is_charge);

					if( !empty($revenue['RevenueDetail'][0]) ) {
						$revenueDetail = $revenue['RevenueDetail'][0];
						$price_unit = $revenueDetail['price_unit'];
						$is_charge = $revenueDetail['is_charge'];
						$grandTotalUnit += $revenueDetail['qty_unit'];

						$payment_type = $this->Common->filterEmptyField($revenueDetail, 'payment_type');
						$total_price_unit = $this->Common->filterEmptyField($revenueDetail, 'total_price_unit');
						$totalPriceFormat = '';

						if( !empty($is_charge) ) {
			                $totalPriceFormat = $this->Common->getFormatPrice($total_price_unit);
			            } else {
			                $total_price_unit = 0;
			            }

						$grandTotalTarif += $total_price_unit;

						if( $tempRate != $price_unit ) {
							$tempRate = $price_unit;
							$showRate = true;
						}

						unset($revenue['RevenueDetail'][0]);
					} else {
						$revenueDetail = false;
					}

					echo $this->element('blocks/revenues/invoice_hso', array(
						'no' => $no,
						'revenue_temp' => $revenue_temp,
						'revenue' => $revenue,
						'revenueDetail' => $revenueDetail,
						'totalUnit' => $totalUnit,
						'showRate' => $showRate,
					));

					if( !empty($revenue['RevenueDetail']) ) {
						foreach ($revenue['RevenueDetail'] as $key => $revenueDetail) {
							$showRate = false;
							$id = !empty($revenue['Revenue']['id'])?$revenue['Revenue']['id']:false;
							$price_unit = $revenueDetail['price_unit'];
							$is_charge = $revenueDetail['is_charge'];
							$grandTotalUnit += $revenueDetail['qty_unit'];
						
							$payment_type = $this->Common->filterEmptyField($revenueDetail, 'payment_type');
							$total_price_unit = $this->Common->filterEmptyField($revenueDetail, 'total_price_unit');
							$totalPriceFormat = '';

							if( !empty($is_charge) ) {
				                $totalPriceFormat = $this->Common->getFormatPrice($total_price_unit);
				            } else {
				                $total_price_unit = 0;
				            }

							$grandTotalTarif += $total_price_unit;

							if( $tempRate != $price_unit ) {
								$tempRate = $price_unit;
								$showRate = true;
							}

							$revenue_temp = sprintf('%s-%s', $id, $is_charge);
							echo $this->element('blocks/revenues/invoice_hso', array(
								'revenue_temp' => $revenue_temp,
								'revenue' => $revenue,
								'revenueDetail' => $revenueDetail,
								'showRate' => $showRate,
							));
						}
					}

					$tempRate = 0;
					$no++;
				}

				$colom = $this->Html->tag('td', __('JUMLAH'), array(
					'colspan' => 5,
					'style' => 'text-align: right;',
				));
				$colom .= $this->Html->tag('td', $grandTotalUnit, array(
					'style' => 'text-align: center;',
				));
				$colom .= $this->Html->tag('td', '');
				$colom .= $this->Html->tag('td', $grandTotalMainUnit, array(
					'style' => 'text-align: center;',
				));
				$colom .= $this->Html->tag('td', '');
				$colom .= $this->Html->tag('td', $this->Number->format($grandTotalTarif, '', array('places' => 0)), array(
					'style' => 'text-align: right;',
				));
				$colom .= $this->Html->tag('td', '');

				echo $this->Html->tag('tr', $colom, array(
					'style' => 'font-weight: bold;background-color: #3c8dbc;color: #FFFFFF;'
				));
			}else{
				$colom = $this->Html->tag('td', __('Data tidak ditemukan.'), array(
					'colspan' => 11,
				));

				echo $this->Html->tag('tr', $colom);
			}
	?>
</tbody>