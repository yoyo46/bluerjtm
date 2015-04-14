<tr>
	<?php 
			$noDoc = !empty($revenue['Revenue']['no_doc'])?$revenue['Revenue']['no_doc']:'-';
			$nopol = !empty($revenue['Ttuj']['nopol'])?$revenue['Ttuj']['nopol']:'-';
			$total_price = !empty($revenue['total_price'])?$revenue['total_price']:0;
			$amount = 0;

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
				$amount = $revenueDetail['qty_unit'] * $revenueDetail['price_unit'];

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
				$price_unit = !empty($revenueDetail['price_unit'])?$revenueDetail['price_unit']:0;
				echo $this->Html->tag('td', $this->Number->format($price_unit, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
					'style' => 'text-align: right;'
				));
			} else {
				echo $this->Html->tag('td', '');
			}	

			if( !empty($no) ) {
				echo $this->Html->tag('td', $this->Number->format($total_price, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
					'style' => 'text-align: right;'
				));
			} else {
				echo $this->Html->tag('td', '');
			}

			echo $this->Html->tag('td', '');
	?>
</tr>