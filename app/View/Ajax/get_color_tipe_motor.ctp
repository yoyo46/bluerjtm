<div id="color-motor">
	<?php echo !empty($data_ttuj['ColorMotor']['name'])?$data_ttuj['ColorMotor']['name']:false;?>
</div>
<div id="form-qty">
	<?php
		echo $this->Form->input('LkuDetail.qty.', array(
			'value' => !empty($data_ttuj['TtujTipeMotor']['qty']) ? $data_ttuj['TtujTipeMotor']['qty'] : 0,
			'placeholder' => __('Jumlah Klaim'),
			'class' => 'claim-number form-control',
			'div' => false,
			'label' => false
		));
	?>
</div>