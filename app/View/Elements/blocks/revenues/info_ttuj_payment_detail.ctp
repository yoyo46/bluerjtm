<tbody id="checkbox-info-table">
	<?php
			$biaya_total = 0;
			$grandTotal = 0;
			$data = $this->request->data;

			if(!empty($data['Ttuj'])){
				foreach ($data['Ttuj'] as $key => $value) {
					$data_type = !empty($data['TtujPayment']['data_type'][$key])?$data['TtujPayment']['data_type'][$key]:false;
					$amount_payment = !empty($data['TtujPayment']['amount_payment'][$key])?$data['TtujPayment']['amount_payment'][$key]:0;
					$no_claim = !empty($data['TtujPayment']['no_claim'][$key])?$data['TtujPayment']['no_claim'][$key]:0;
					$stood = !empty($data['TtujPayment']['stood'][$key])?$data['TtujPayment']['stood'][$key]:0;
					$lainnya = !empty($data['TtujPayment']['lainnya'][$key])?$data['TtujPayment']['lainnya'][$key]:0;
					$titipan = !empty($data['TtujPayment']['titipan'][$key])?$data['TtujPayment']['titipan'][$key]:0;
					$claim = !empty($data['TtujPayment']['claim'][$key])?$data['TtujPayment']['claim'][$key]:0;
					$laka = !empty($data['TtujPayment']['laka'][$key])?$data['TtujPayment']['laka'][$key]:0;
					$debt = !empty($data['TtujPayment']['debt'][$key])?$data['TtujPayment']['debt'][$key]:0;
					
					$biaya_total += $amount_payment;
					$grandTotal += $amount_payment + $no_claim + $stood + $lainnya - $titipan - $claim - $laka - $debt;

					echo $this->element('blocks/ajax/pembayaran_uang_jalan', array(
	                    'ttuj' => $value,
	                    'data_type' => $data_type,
	                    'idx' => $key,
	                    'checkbox' => false,
	                ));
				}
			}
	?>
</tbody>
<tr>
	<?php 
			echo $this->Html->tag('td', __('Total'), array(
				'class' => 'bold text-right',
			));
			echo $this->Html->tag('td', Common::getFormatPrice($biaya_total), array(
				'class' => 'text-right',
				'id' => 'total-biaya',
			));
			echo $this->Html->tag('td', '', array(
				'colspan' => 7,
			));
			echo $this->Html->tag('td', Common::getFormatPrice($grandTotal), array(
				'class' => 'text-right',
				'id' => 'grandtotal-biaya',
			));
	?>
</tr>
