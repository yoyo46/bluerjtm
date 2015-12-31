<?php 
		switch ($action_type) {
			case 'biaya_ttuj':
				$titleCrumb = __('Pembayaran Biaya TTUJ');
				$titleBrowse = __('Biaya TTUJ');
				break;
			
			default:
				$titleCrumb = __('Pembayaran Uang Jalan/Komisi');
				$titleBrowse = __('Biaya Uang Jalan/Komisi');
				break;
		}

		$this->Html->addCrumb($titleCrumb, array(
			'controller' => 'revenues',
			'action' => 'ttuj_payments',
			$action_type,
		));
?>