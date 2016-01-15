<?php 
		$data_print_type = isset($data_print_type)?$data_print_type:false;
		$values = $this->Common->filterEmptyField($invoice, 'InvoiceDetail');
?>

<table border="0" width="100%" style="margin-top: 20px;">
	<thead class="header-invoice-print">
		<?php 
	            if( $action_print == 'excel' ) {
					$invoice = !empty($invoice)?$invoice:false;
					$customer = $this->Common->filterEmptyField($invoice, 'Customer', 'name');
					$no_invoice = $this->Common->filterEmptyField($invoice, 'Invoice', 'no_invoice');
		?>
		<tr>
			<td colspan="5" style="text-align: left;" valign="top">
				<?php 
						echo $this->Html->tag('div', sprintf(__('%s - TARIF 3'), $customer), array(
							'style' => 'font-size: 18px;font-weight: 700;margin-bottom: 20px;'
						));
				?>
			</td>
			<td colspan="5" style="text-align: right;">
				<?php 
						echo $this->Html->tag('div', $no_invoice, array(
							'style' => 'font-size: 16px;font-weight: 700;'
						));
						echo $this->Html->tag('div', __('JASA ANGKUT JOGYA TARIF 3'), array(
							'style' => 'font-size: 16px;font-weight: 700;margin-bottom: 20px;'
						));
				?>
			</td>
		</tr>
		<?php 
				}
		?>
		<tr>
			<?php 
					echo $this->Html->tag('th', __('NO'), array(
						'style' => 'text-align:center;width: 5%;border: 1px solid;',
					));
					echo $this->Html->tag('th', __('TANGGAL'), array(
						'style' => 'text-align:center;width: 10%;border: 1px solid;',
					));
					echo $this->Html->tag('th', __('DEALER'), array(
						'style' => 'text-align:center;width: 12%;border: 1px solid;',
					));
					echo $this->Html->tag('th', __('NOPOL'), array(
						'style' => 'text-align:center;width: 10%;border: 1px solid;',
					));
					echo $this->Html->tag('th', __('No BSTK'), array(
						'style' => 'text-align:center;width: 13%;border: 1px solid;',
					));
					echo $this->Html->tag('th', __('QTY'), array(
						'style' => 'text-align:center;width: 10%;border: 1px solid;',
					));
					echo $this->Html->tag('th', __('HARGA'), array(
						'style' => 'text-align:center;width: 10%;border: 1px solid;',
					));
					echo $this->Html->tag('th', __('JUMLAH'), array(
						'style' => 'text-align:center;width: 10%;border: 1px solid;',
					));
					echo $this->Html->tag('th', __('EKSPEDISI'), array(
						'style' => 'text-align:center;width: 10%;border: 1px solid;',
					));
					echo $this->Html->tag('th', __('NO REF'), array(
						'style' => 'text-align:center;width: 10%;border: 1px solid;',
					));
			?>
		</tr>
	</thead>
	<tbody>
		<?php
				if(!empty($values)){
					$no = 1;
					$grandTotalUnit = 0;
					$grandTotalTarif = 0;

					foreach ($values as $key => $value) {
	    				$revenue_id = $this->Common->filterEmptyField($value, 'Revenue', 'id');
	    				$date_revenue = $this->Common->filterEmptyField($value, 'Revenue', 'date_revenue');
	    				$nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol', '-');
	    				$noref = $this->Common->getNoRef($revenue_id);

	    				$sj = $this->Common->filterEmptyField($value, 'RevenueDetail', 'no_sj');
	    				$no_do = $this->Common->filterEmptyField($value, 'RevenueDetail', 'no_do');
	    				$price_unit = $this->Common->filterEmptyField($value, 'RevenueDetail', 'price_unit', 0);
	    				$totalUnit = $this->Common->filterEmptyField($value, 'RevenueDetail', 'qty_unit', 0);

						$amount = $totalUnit * $price_unit;
						$grandTotalUnit += $totalUnit;
						$grandTotalTarif += $amount;

						$date_revenue = $this->Common->formatDate($date_revenue, 'd/m/Y');
						$customPrice = $this->Common->getFormatPrice($price_unit);
						$customAmount = $this->Common->getFormatPrice($amount);
						$noref = sprintf('#%s', $noref);
		?>
		<tr>
			<?php 
					echo $this->Html->tag('td', $no, array(
						'style' => 'text-align: center;border: 1px solid;',
					));
					echo $this->Html->tag('td', $date_revenue, array(
						'style' => 'text-align: center;border: 1px solid;',
					));
					echo $this->Html->tag('td', $no_do, array(
						'style' => 'border: 1px solid;',
					));
					echo $this->Html->tag('td', $nopol, array(
						'style' => 'border: 1px solid;',
					));
					echo $this->Html->tag('td', $sj, array(
						'style' => 'border: 1px solid;',
					));
					echo $this->Html->tag('td', $totalUnit, array(
						'style' => 'text-align: center;border: 1px solid;',
					));
					echo $this->Html->tag('td', $customPrice, array(
						'style' => 'text-align: right;border: 1px solid;',
					));
					echo $this->Html->tag('td', $customAmount, array(
						'style' => 'text-align: right;border: 1px solid;',
					));
					echo $this->Html->tag('td', 'RJTM', array(
						'style' => 'text-align: center;border: 1px solid;',
					));
					echo $this->Html->tag('td', $noref, array(
						'style' => 'border: 1px solid;',
					));
			?>
		</tr>
		<?php
						$no++;
					}

					$grandTotalTarif = $this->Common->getFormatPrice($grandTotalTarif);

					$colom = $this->Html->tag('td', __('TOTAL'), array(
						'colspan' => 5,
						'style' => 'text-align: right;border: 1px solid;',
					));
					$colom .= $this->Html->tag('td', $grandTotalUnit, array(
						'style' => 'text-align: center;border: 1px solid;',
					));
					$colom .= $this->Html->tag('td', '', array(
						'style' => 'border: 1px solid;',
					));
					$colom .= $this->Html->tag('td', $grandTotalTarif, array(
						'style' => 'text-align: right;border: 1px solid;',
					));
					$colom .= $this->Html->tag('td', '', array(
						'style' => 'border: 1px solid;',
					));
					$colom .= $this->Html->tag('td', '', array(
						'style' => 'border: 1px solid;',
					));

					echo $this->Html->tag('tr', $colom, array(
						'style' => 'font-weight: bold;'
					));
				}else{
					$colom = $this->Html->tag('td', __('Data tidak ditemukan.'), array(
						'colspan' => 10,
						'style' => 'border: 1px solid;',
					));

					echo $this->Html->tag('tr', $colom);
				}
		?>
	</tbody>
</table>