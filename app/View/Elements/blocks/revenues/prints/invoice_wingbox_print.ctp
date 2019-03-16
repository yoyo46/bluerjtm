<?php 
		$data_print_type = isset($data_print_type)?$data_print_type:false;
        $no_invoice = Common::hashEmptyField($invoice, 'Invoice.no_invoice');
		$values = Common::hashEmptyField($invoice, 'InvoiceDetail');
        $full_name = Common::hashEmptyField($invoice, 'Employe.full_name');
?>

<table class="table table-bordered">
    <thead class="header-invoice-print">
        <tr>
            <?php
                    if( !empty($fieldColumn) ) {
                        echo $fieldColumn;
                    }
            ?>
        </tr>
    </thead>
	<tbody>
		<?php
				if(!empty($values)){
					$no = 1;
					$totalBiaya = 0;
					$totalBiayaExtra = 0;

					foreach ($values as $key => $value) {
	    				$ttuj_date = Common::hashEmptyField($value, 'Revenue.Ttuj.ttuj_date', NULL, array(
	    					'date' => 'd-M',
	    				));
	    				$tgljam_tiba = Common::hashEmptyField($value, 'Revenue.Ttuj.tgljam_tiba', NULL, array(
	    					'date' => 'd-M',
	    				));
	    				$totalExtra = Common::hashEmptyField($value, 'Revenue.Ttuj.uang_jalan_extra', 0);

		                $is_charge = Common::hashEmptyField($value, 'RevenueDetail.is_charge');
		                $total_price_unit = Common::hashEmptyField($value, 'RevenueDetail.total_price_unit');

	    				$city = Common::hashEmptyField($value, 'RevenueDetail.City.code');
	    				$truck_category = Common::hashEmptyField($value, 'Revenue.Truck.TruckCategory.name');
	    				
	    				$nopol = Common::hashEmptyField($value, 'Revenue.Ttuj.nopol', '-');
	    				$nopol = Common::hashEmptyField($value, 'Revenue.nopol', $nopol);

	    				$sj = Common::hashEmptyField($value, 'RevenueDetail.no_sj');
	    				$no_do = Common::hashEmptyField($value, 'RevenueDetail.no_do');
	    				$price_unit = Common::hashEmptyField($value, 'RevenueDetail.price_unit', 0);
	    				$totalUnit = Common::hashEmptyField($value, 'RevenueDetail.qty_unit', 0);
						$totalPriceFormat = '';

	    				if( !empty($is_charge) ) {
							$totalPriceFormat = $this->Common->getFormatPrice($total_price_unit);
							$customPrice = $this->Common->getFormatPrice($price_unit);
						} else {
							$total_price_unit = 0;
							$customPrice = '';
						}

						$totalUnit = $this->Common->getFormatPrice($totalUnit);
						$customTotalExtra = $this->Common->getFormatPrice($totalExtra);

						$totalBiaya += $total_price_unit;
						$totalBiayaExtra += $totalExtra;
		?>
		<tr style="border: 1px solid #ddd;">
			<?php 
					echo $this->Html->tag('td', $no, array(
						'style' => 'text-align: center;padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $no_do, array(
						'style' => 'padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', '-', array(
						'style' => 'text-align: center;padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $city, array(
						'style' => 'padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $ttuj_date, array(
						'style' => 'padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $tgljam_tiba, array(
						'style' => 'padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $tgljam_tiba, array(
						'style' => 'padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $sj, array(
						'style' => 'padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $truck_category, array(
						'style' => 'padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $nopol, array(
						'style' => 'padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $totalUnit, array(
						'style' => 'text-align: center;padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $customPrice, array(
						'style' => 'padding: 10px;text-align:right;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $totalPriceFormat, array(
						'style' => 'padding: 10px;text-align:right;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', '', array(
						'style' => 'padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', '', array(
						'style' => 'padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', '', array(
						'style' => 'padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $customTotalExtra, array(
						'style' => 'padding: 10px;text-align:right;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $no_invoice, array(
						'style' => 'padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', '', array(
						'style' => 'padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', '', array(
						'style' => 'padding: 10px;border: 1px solid #ddd;',
					));
			?>
		</tr>
		<?php
						$no++;
					}

					$grandtotal = $totalBiaya + $totalBiayaExtra;

					// Biaya Utama
					$colom = $this->Html->tag('td', '', array(
						'colspan' => 2,
					));
					$colom .= $this->Html->tag('td', '', array(
						'colspan' => 5,
					));
					$colom .= $this->Html->tag('td', '');
					$colom .= $this->Html->tag('td', __('Biaya Utama'), array(
						'colspan' => 8,
						'style' => 'padding: 10px;',
					));
					$colom .= $this->Html->tag('td', Common::getFormatPrice($totalBiaya), array(
						'style' => 'padding: 10px;text-align:right;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('tr', $colom, array(
						'style' => 'font-weight: bold;'
					));

					// Biaya Multi Drop
					$colom = $this->Html->tag('td', '', array(
						'colspan' => 2,
					));
					$colom .= $this->Html->tag('td', __('Dibuat Oleh'), array(
						'colspan' => 2,
						'style' => 'text-align: center;padding: 10px;',
					));
					$colom .= $this->Html->tag('td', __('Diverifikasi Oleh'), array(
						'colspan' => 3,
						'style' => 'text-align: center;padding: 10px;',
					));
					$colom .= $this->Html->tag('td', '');
					$colom .= $this->Html->tag('td', __('Biaya Multi Drop'), array(
						'colspan' => 8,
						'style' => 'padding: 10px;',
					));
					$colom .= $this->Html->tag('td', '-', array(
						'style' => 'padding: 10px;text-align:right;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('tr', $colom, array(
						'style' => 'font-weight: bold;'
					));

					// Biaya Lain-lain
					$colom = $this->Html->tag('td', '', array(
						'colspan' => 2,
					));
					$colom .= $this->Html->tag('td', '', array(
						'colspan' => 5,
					));
					$colom .= $this->Html->tag('td', '');
					$colom .= $this->Html->tag('td', __('Biaya Lain-Lain'), array(
						'colspan' => 8,
						'style' => 'padding: 10px;',
					));
					$colom .= $this->Html->tag('td', Common::getFormatPrice($totalBiayaExtra), array(
						'style' => 'padding: 10px;text-align:right;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('tr', $colom, array(
						'style' => 'font-weight: bold;'
					));

					// Biaya Overnigh
					$colom = $this->Html->tag('td', '', array(
						'colspan' => 2,
					));
					$colom .= $this->Html->tag('td', '', array(
						'colspan' => 5,
					));
					$colom .= $this->Html->tag('td', '');
					$colom .= $this->Html->tag('td', __('Biaya Overnigh'), array(
						'colspan' => 8,
						'style' => 'padding: 10px;',
					));
					$colom .= $this->Html->tag('td', '-', array(
						'style' => 'padding: 10px;text-align:right;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('tr', $colom, array(
						'style' => 'font-weight: bold;'
					));

					// Biaya Utama + Multi Drop
					$colom = $this->Html->tag('td', '', array(
						'colspan' => 2,
					));
					$colom .= $this->Html->tag('td', '', array(
						'colspan' => 5,
					));
					$colom .= $this->Html->tag('td', '');
					$colom .= $this->Html->tag('td', $this->Html->tag('div', __('Biaya Utama + Multi Drop'), array(
						'style' => 'border: 1px solid #000;padding: 10px;',
					)), array(
						'colspan' => 8,
						'style' => 'padding: 0;',
					));
					$colom .= $this->Html->tag('td', $this->Html->tag('div', Common::getFormatPrice($totalBiaya), array(
						'style' => 'border: 1px solid #000;padding: 10px;',
					)), array(
						'style' => 'padding: 0;text-align:right;',
					));
					echo $this->Html->tag('tr', $colom, array(
						'style' => 'font-weight: bold;'
					));

					// EMPTY
					$colom = $this->Html->tag('td', '', array(
						'colspan' => 2,
					));
					$colom .= $this->Html->tag('td', '', array(
						'colspan' => 5,
					));
					$colom .= $this->Html->tag('td', '');
					$colom .= $this->Html->tag('td', '&nbsp;', array(
						'colspan' => 8,
						'style' => 'padding: 10px;',
					));
					$colom .= $this->Html->tag('td', '&nbsp;', array(
						'style' => 'padding: 10px;text-align:right;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('tr', $colom, array(
						'style' => 'font-weight: bold;'
					));

					// EMPTY
					$colom = $this->Html->tag('td', '', array(
						'colspan' => 2,
					));
					$colom .= $this->Html->tag('td', '', array(
						'colspan' => 5,
					));
					$colom .= $this->Html->tag('td', '');
					$colom .= $this->Html->tag('td', '&nbsp;', array(
						'colspan' => 8,
						'style' => 'padding: 10px;',
					));
					$colom .= $this->Html->tag('td', '&nbsp;', array(
						'style' => 'padding: 10px;text-align:right;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('tr', $colom, array(
						'style' => 'font-weight: bold;'
					));

					// Total Biaya
					$colom = $this->Html->tag('td', '', array(
						'colspan' => 2,
					));
					$colom .= $this->Html->tag('td', $this->Html->tag('div', __('( %s )', $this->Html->tag('span', $full_name, array(
						'style' => 'min-width: 100px;display:inline-block;',
					))), array(
						'style' => 'display: inline-block;border-bottom: 1px solid;',
					)).'<br>'.__('Transporter'), array(
						'colspan' => 2,
						'style' => 'padding: 10px;text-align:center;',
					));
					$colom .= $this->Html->tag('td', '&nbsp;', array(
						'colspan' => 3,
						'style' => 'padding: 10px;text-align:center;',
					));
					$colom .= $this->Html->tag('td', '');
					$colom .= $this->Html->tag('td', __('TOTAL BIAYA'), array(
						'colspan' => 8,
						'style' => 'padding: 10px;',
					));
					$colom .= $this->Html->tag('td', Common::getFormatPrice($grandtotal), array(
						'style' => 'padding: 10px;text-align:right;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('tr', $colom, array(
						'style' => 'font-weight: bold;'
					));
				}else{
					$colom = $this->Html->tag('td', __('Data tidak ditemukan.'), array(
						'colspan' => 10,
						'style' => 'border: 1px solid #ddd;',
					));

					echo $this->Html->tag('tr', $colom);
				}
		?>
	</tbody>
</table>