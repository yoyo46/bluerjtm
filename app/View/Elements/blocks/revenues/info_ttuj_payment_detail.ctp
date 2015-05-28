<tbody id="checkbox-info-table">
	<?php
			$grandTotal = 0;

			if(!empty($this->request->data['Ttuj'])){
				foreach ($this->request->data['Ttuj'] as $key => $value) {
					$data = $this->request->data;
					$data_type = !empty($data['TtujPayment']['data_type'][$key])?$data['TtujPayment']['data_type'][$key]:false;
					$grandTotal += !empty($this->request->data['TtujPayment']['amount_payment'][$key])?$this->request->data['TtujPayment']['amount_payment'][$key]:0;

					echo $this->element('blocks/ajax/biaya_uang_jalan', array(
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
				'colspan' => 8,
				'class' => 'bold text-right',
			));
			echo $this->Html->tag('td', $this->Number->format($grandTotal, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
				'class' => 'text-right',
				'id' => 'total-biaya',
			));
	?>
</tr>
