<?php
	if(!empty($data_ttuj)){
?>
	<div id="form-qty">
		<?php
			echo $this->Form->input('KsuDetail.qty.', array(
				'value' => $data_ttuj['TtujPerlengkapan']['qty'],
				'empty' => __('Pilih Jumlah Klaim'),
				'class' => 'claim-number form-control input_price',
				'div' => false,
				'label' => false
			));
		?>
	</div>
<?php
	}
?>