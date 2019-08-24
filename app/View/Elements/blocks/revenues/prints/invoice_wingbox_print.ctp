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
					$totalMultiDrop = 0;
					$totalOverNight = 0;

					foreach ($values as $key => $value) {
	    				$ttuj_date = Common::hashEmptyField($value, 'Revenue.Ttuj.ttuj_date', NULL, array(
	    					'date' => 'd-M',
	    				));
	    				$tgljam_tiba = Common::hashEmptyField($value, 'Revenue.Ttuj.tgljam_tiba', NULL, array(
	    					'date' => 'd-M',
	    				));
	    				// $totalExtra = Common::hashEmptyField($value, 'Revenue.Ttuj.uang_jalan_extra', 0);

		                $is_charge = Common::hashEmptyField($value, 'RevenueDetail.is_charge');
		                $total_price_unit = Common::hashEmptyField($value, 'RevenueDetail.total_price_unit');

	    				$city = Common::hashEmptyField($value, 'RevenueDetail.City.name');
	    				$truck_category = Common::hashEmptyField($value, 'Revenue.Truck.TruckCategory.name');
	    				
	    				$nopol = Common::hashEmptyField($value, 'Revenue.Ttuj.nopol', '-');
	    				$nopol = Common::hashEmptyField($value, 'Revenue.nopol', $nopol);

	    				$sj = Common::hashEmptyField($value, 'RevenueDetail.no_sj');
	    				$no_do = Common::hashEmptyField($value, 'RevenueDetail.no_do', '-');
	    				// $price_unit = Common::hashEmptyField($value, 'RevenueDetail.price_unit', 0);
	    				$totalUnit = Common::hashEmptyField($value, 'RevenueDetail.qty_unit', 0);
	    				$multi_drop = Common::hashEmptyField($value, 'multi_drop', 0);
	    				$overnight_charges = Common::hashEmptyField($value, 'overnight_charges', 0);

	    				$tarif_extra = Common::hashEmptyField($value, 'RevenueDetail.tarif_extra', 0);
	    				$tarif_extra_min_capacity = Common::hashEmptyField($value, 'RevenueDetail.tarif_extra_min_capacity', 0);
	    				$tarif_extra_per_unit = Common::hashEmptyField($value, 'RevenueDetail.tarif_extra_per_unit', 0);
	    				
	    				$tarifExtra = 0;
	    				$qtyExtra = 0;

	    				if( $tarif_extra_min_capacity != 0 ) {
				            if( $totalUnit > $tarif_extra_min_capacity ) {
			                    $sisa_muatan = $totalUnit - $tarif_extra_min_capacity;

				                if( $tarif_extra_per_unit != 0 ) {
				                    $tarif_extra = $tarif_extra * $sisa_muatan;
				                }

				                $tarifExtra = $tarif_extra;
	    						$qtyExtra = $sisa_muatan;
				            }
				        }

		                $price_unit = $total_price_unit - $tarifExtra;
						$totalPriceFormat = '';
						$priceUnitFormat = '';
						$priceExtraFormat = '';

        				if( !empty($sj) ) {
	        				$no_sj = substr($sj, 0, 28);
	        				$dealer = substr($sj, 29, strlen($sj));
	        			} else {
							$no_sj = '-';
	        				$dealer = '-';
	        			}

	    				if( !empty($is_charge) ) {
							$totalPriceFormat = $this->Common->getFormatPrice($total_price_unit);
							$priceUnitFormat = $this->Common->getFormatPrice($price_unit);
							$priceExtraFormat = $this->Common->getFormatPrice($tarifExtra);
							// $customPrice = $this->Common->getFormatPrice($price_unit);
						} else {
							$total_price_unit = 0;
							// $customPrice = '';
						}

						$totalUnit = $this->Common->getFormatPrice($totalUnit);
						// $customTotalExtra = $this->Common->getFormatPrice($totalExtra);
						$custom_multi_drop = $this->Common->getFormatPrice($multi_drop);
						$custom_overnight_charges = $this->Common->getFormatPrice($overnight_charges);

						$totalBiaya += $price_unit;
						$totalBiayaExtra += $tarifExtra;
						$totalMultiDrop += $multi_drop;
						$totalOverNight += $overnight_charges;
		?>
		<tr style="border: 1px solid #ddd;">
			<?php 
					echo $this->Html->tag('td', $no, array(
						'style' => 'text-align: center;padding: 10px;border: 1px solid #ddd;',
					));
					// echo $this->Html->tag('td', $no_do, array(
					echo $this->Html->tag('td', '', array(
						'style' => 'padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $no_do, array(
						'style' => 'text-align: left;padding: 10px;border: 1px solid #ddd;',
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
					echo $this->Html->tag('td', $no_sj, array(
						'style' => 'padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $truck_category, array(
						'style' => 'padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $nopol, array(
						'style' => 'padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $totalUnit, array(
					// echo $this->Html->tag('td', '', array(
						'style' => 'text-align: center;padding: 10px;border: 1px solid #ddd;',
					));
					// echo $this->Html->tag('td', $customPrice, array(
					echo $this->Html->tag('td', '', array(
						'style' => 'padding: 10px;text-align:right;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $priceUnitFormat, array(
						'style' => 'padding: 10px;text-align:right;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $custom_multi_drop, array(
						'style' => 'padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $custom_overnight_charges, array(
						'style' => 'padding: 10px;border: 1px solid #ddd;',
					));
					echo $this->Html->tag('td', $qtyExtra, array(
						'style' => 'padding: 10px;border: 1px solid #ddd;text-align:center;',
					));
					echo $this->Html->tag('td', $priceExtraFormat, array(
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

					$grandtotal = $totalBiaya + $totalBiayaExtra + $totalMultiDrop + $totalOverNight;

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
					$colom .= $this->Html->tag('td', Common::getFormatPrice($totalMultiDrop), array(
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
					$colom .= $this->Html->tag('td', Common::getFormatPrice($totalOverNight), array(
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
					$colom .= $this->Html->tag('td', $this->Html->tag('div', Common::getFormatPrice($totalBiaya+$totalMultiDrop+$totalOverNight+$totalBiayaExtra), array(
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