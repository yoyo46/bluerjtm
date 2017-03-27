<tr>
	<?php 
			$totalUnit = !empty($totalUnit)?$totalUnit:0;
			$revenue_temp = !empty($revenue_temp)?$revenue_temp:false;
			$old_revenue_id = Configure::read('Revenue.temp');
			$noDoc = !empty($revenue['Revenue']['no_doc'])?$revenue['Revenue']['no_doc']:'-';

            $nopol = $this->Common->filterEmptyField($revenue, 'Truck', 'nopol');
            $nopol = $this->Common->filterEmptyField($revenue, 'Ttuj', 'nopol', $nopol);
            $nopol = $this->Common->filterEmptyField($revenue, 'Revenue', 'nopol', $nopol);

			// $total_price = !empty($revenue['total_price'])?$revenue['total_price']:0;
			$price = 0;

			Configure::write('Revenue.temp', $revenue_temp);

			$price_unit = $this->Common->filterEmptyField($revenueDetail, 'price_unit');
			$total_price_unit = $this->Common->filterEmptyField($revenueDetail, 'total_price_unit');
			$payment_type = $this->Common->filterEmptyField($revenueDetail, 'payment_type');
			$is_charge = $this->Common->filterEmptyField($revenueDetail, 'is_charge');
			$priceFormat = $this->Common->getFormatPrice($price_unit, 0);
			$totalPriceFormat = '';

			if( !empty($is_charge) ) {
                $totalPriceFormat = $this->Common->getFormatPrice($total_price_unit);
            } else {
                $total_price_unit = 0;
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
				echo $this->Html->tag('td', $totalPriceFormat, array(
					'style' => 'text-align: right;'
				));
			} else {
				echo $this->Html->tag('td', '');
			}

			echo $this->Html->tag('td', '');
	?>
</tr>