<?php
	if(!empty($data_ttuj)){
?>
	<div id="color-motor">
		<?php echo !empty($data_ttuj['ColorMotor']['name'])?$data_ttuj['ColorMotor']['name']:false;?>
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
<?php
	}
?>