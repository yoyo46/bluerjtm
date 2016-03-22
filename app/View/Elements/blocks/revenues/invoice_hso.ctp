<tr>
	<?php 
			$totalUnit = !empty($totalUnit)?$totalUnit:0;
			$revenue_temp = !empty($revenue_temp)?$revenue_temp:false;
			$old_revenue_id = Configure::read('Revenue.temp');
			$noDoc = !empty($revenue['Revenue']['no_doc'])?$revenue['Revenue']['no_doc']:'-';
			$nopol = !empty($revenue['Ttuj']['nopol'])?$revenue['Ttuj']['nopol']:'-';
			// $total_price = !empty($revenue['total_price'])?$revenue['total_price']:0;
			$price = 0;

			Configure::write('Revenue.temp', $revenue_temp);

			$price_unit = $this->Common->filterEmptyField($revenueDetail, 'price_unit');
			$total_price_unit = $this->Common->filterEmptyField($revenueDetail, 'total_price_unit');
			$payment_type = $this->Common->filterEmptyField($revenueDetail, 'payment_type');
			$is_charge = $this->Common->filterEmptyField($revenueDetail, 'is_charge');

			if( $payment_type == 'per_truck' ){
				$priceFormat = '-';
			} else {
				$price = $price_unit;
				$priceFormat = $this->Common->getFormatPrice($price);
			}

			if( $payment_type == 'per_truck' ){
				if( !empty($total_price_unit) ) {
					$total = $total_price_unit;
					$total_price = $this->Common->getFormatPrice($total);
				} else {
					if( $revenue_temp != $old_revenue_id ) {
						$total = !empty($revenue['Revenue']['tarif_per_truck'])?$revenue['Revenue']['tarif_per_truck']:0;
						$total_price = $this->Common->getFormatPrice($total);
					} else {
						$total_price = '';
					}
				}
			}else{
				$total = $price * $totalUnit;
				$total_price = $this->Common->getFormatPrice($total);
			}

			if( !empty($no) ) {
				echo $this->Html->tag('td', $no);
				echo $this->Html->tag('td', $nopol);
				echo $this->Html->tag('td', $noDoc);
				echo $this->Html->tag('td', $revenueDetail['no_do']);
			} else {
				echo $this->Html->tag('td', '');
				echo $this->Html->tag('td', '');
				echo $this->Html->tag('td', '');
				echo $this->Html->tag('td', '');
			}

			if( !empty($revenueDetail) ) {
				$revenueDetail = $revenueDetail;

				echo $this->Html->tag('td', $revenueDetail['no_sj']);
				echo $this->Html->tag('td', $revenueDetail['qty_unit'], array(
					'style' => 'text-align: center;'
				));

				if( !empty($no) ) {
					echo $this->Html->tag('td', $this->Common->customDate($revenue['Revenue']['date_revenue'], 'd/m/Y'), array(
						'style' => 'text-align: center;'
					));
				} else {
					echo $this->Html->tag('td', '');
				}
			} else {
				echo $this->Html->tag('td', '');
				echo $this->Html->tag('td', '');
				echo $this->Html->tag('td', '');
			}

			if( !empty($totalUnit) ) {
				echo $this->Html->tag('td', $totalUnit, array(
					'style' => 'text-align: center;'
				));
			} else {
				echo $this->Html->tag('td', '');
			}

			if( !empty($showRate) ) {
				echo $this->Html->tag('td', $priceFormat, array(
					'style' => 'text-align: right;'
				));
			} else {
				echo $this->Html->tag('td', '');
			}	

			if( !empty($no) || !empty($is_charge) ) {
				echo $this->Html->tag('td', $total_price, array(
					'style' => 'text-align: right;'
				));
			} else {
				echo $this->Html->tag('td', '');
			}

			echo $this->Html->tag('td', '');
	?>
</tr>