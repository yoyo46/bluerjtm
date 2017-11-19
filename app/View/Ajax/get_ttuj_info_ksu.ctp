<?php
	echo $this->Html->tag('div', (!empty($Ksu['Ttuj']['nopol'])) ? $Ksu['Ttuj']['nopol'] : '-', array(
		'id' => 'data-nopol'
	));
	echo $this->Html->tag('div', (!empty($Ksu['Ttuj']['from_city_name'])) ? $Ksu['Ttuj']['from_city_name'] : '-', array(
		'id' => 'data-from-city'
	));
	echo $this->Html->tag('div', (!empty($Ksu['Ttuj']['to_city_name'])) ? $Ksu['Ttuj']['to_city_name'] : '-', array(
		'id' => 'data-to-city'
	));

	$max_qty = 0;
	if(!empty($Ksu['Ksu']['total_klaim'])){
		$max_qty = $Ksu['Ksu']['total_klaim'];
	}

	$content = '-';
	if(!empty($Ksu['Ksu']['total_klaim'])){
		$content = $Ksu['Ksu']['total_klaim'].$this->Form->hidden('KsuPaymentDetail.total_klaim.', array(
			'empty' => __('Pilih Jumlah Klaim'),
			'class' => 'Ksu-claim-number form-control',
			'div' => false,
			'label' => false,
			'value' => $max_qty
		));
	}
	echo $this->Html->tag('div', $content, array(
		'id' => 'data-total-claim'
	));

	$max_qty = 0;
	if(!empty($Ksu['Ksu']['total_price'])){
		$max_qty = $Ksu['Ksu']['total_price'];
	}

    $content = '-';
	if(!empty($Ksu['Ksu']['total_price'])){
		$content = $this->Number->currency($Ksu['Ksu']['total_price'], Configure::read('__Site.config_currency_code'), array('places' => 0)).$this->Form->hidden('KsuPaymentDetail.total_biaya_klaim.', array(
	        'type' => 'text',
	        'label' => false,
	        'class' => 'form-control price-ksu input_number',
	        'required' => false,
	        'max_price' => $max_qty,
	        'value' => $max_qty,
	        'placeholder' => sprintf(__('maksimal pembayaran : %s'), $max_qty)
	    ));
	}
	echo $this->Html->tag('div', $content, array(
		'id' => 'data-total-price-claim'
	));
?>