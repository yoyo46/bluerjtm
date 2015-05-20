<?php
		$price = 0;
		if(!empty($part_motor['PartsMotor']['biaya_claim_unit'])){
			$price = $this->Number->currency($part_motor['PartsMotor']['biaya_claim_unit'], '', array('places' => 0));
		}

		echo $this->Html->tag('div', $price, array(
			'id' => 'price'
		));
?>