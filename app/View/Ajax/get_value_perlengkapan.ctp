<div id="form-qty">
	<?php
		echo $this->Form->input('KsuDetail.qty.', array(
			'value' => !empty($data_ttuj['TtujPerlengkapan']['qty']) ? $data_ttuj['TtujPerlengkapan']['qty'] : 0,
			'empty' => __('Pilih Jumlah Klaim'),
			'class' => 'claim-number form-control input_price',
			'div' => false,
			'label' => false
		));
	?>
</div>