<?php
	echo $this->Html->tag('div', (!empty($lku['Ttuj']['nopol'])) ? $lku['Ttuj']['nopol'] : '-', array(
		'id' => 'data-nopol'
	));
	echo $this->Html->tag('div', (!empty($lku['Ttuj']['from_city_name'])) ? $lku['Ttuj']['from_city_name'] : '-', array(
		'id' => 'data-from-city'
	));
	echo $this->Html->tag('div', (!empty($lku['Ttuj']['to_city_name'])) ? $lku['Ttuj']['to_city_name'] : '-', array(
		'id' => 'data-to-city'
	));

	$max_qty = 0;
	if(!empty($lku['Lku']['total_klaim'])){
		$max_qty = $lku['Lku']['total_klaim'];
	}

	$content = '-';
	if(!empty($lku['Lku']['total_klaim'])){
		$content = $lku['Lku']['total_klaim'].$this->Form->hidden('LkuPaymentDetail.total_klaim.', array(
			'empty' => __('Pilih Jumlah Klaim'),
			'class' => 'lku-claim-number form-control',
			'div' => false,
			'label' => false,
			'value' => $max_qty
		));
	}
	echo $this->Html->tag('div', $content, array(
		'id' => 'data-total-claim'
	));

	$max_qty = 0;
	if(!empty($lku['Lku']['total_price'])){
		$max_qty = $lku['Lku']['total_price'];
	}

    $content = '-';
	if(!empty($lku['Lku']['total_price'])){
		$content = $this->Number->currency($lku['Lku']['total_price'], Configure::read('__Site.config_currency_code'), array('places' => 0)).$this->Form->hidden('LkuPaymentDetail.total_biaya_klaim.', array(
	        'type' => 'text',
	        'label' => false,
	        'class' => 'form-control price-lku input_number',
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