<?php 
		$type = !empty($type)?$type:false;

		switch ($type) {
			case 'po':
				echo $this->element('blocks/products/receipts/tables/po_items');
				break;
		}
?>