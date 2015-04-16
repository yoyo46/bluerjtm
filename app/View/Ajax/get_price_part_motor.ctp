<?php
		$price = 0;
		if(!empty($part_motor['PartsMotor']['biaya_claim_unit'])){
			$price = $part_motor['PartsMotor']['biaya_claim_unit'];
		}

		echo $this->Html->tag('div', $price, array(
			'id' => 'price'
		));
?>