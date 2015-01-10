<div id="color-motor">
	<?php echo $data_ttuj['ColorMotor']['name'];?>
</div>
<div id="form-qty">
	<?php
		$options = array();
		for ($i=1; $i <= $data_ttuj['TtujTipeMotor']['qty']; $i++) { 
			$options[$i] = $i;
		}

		echo $this->Form->input('LkuDetail.qty.', array(
			'options' => $options,
			'empty' => __('Pilih Jumlah Klaim'),
			'class' => 'claim-number form-control',
			'div' => false,
			'label' => false
		));
	?>
</div>