<?php 
		echo $this->Form->input('truck_id',array(
			'label'=> false, 
			'class'=>'form-control nopol',
			'required' => false,
			'empty' => __('Pilih No. Pol --'),
			'options' => $result,
			'div' => false,
			'id' => 'truck_id',
		));

		echo $this->Html->tag('div', number_format($uangJalan['UangJalan']['uang_jalan_1'], 0), array(
			'id' => 'uang_jalan_1',
		));

		echo $this->Html->tag('div', number_format($uangJalan['UangJalan']['uang_jalan_2'], 0), array(
			'id' => 'uang_jalan_2',
		));

		echo $this->Html->tag('div', number_format($uangJalan['UangJalan']['commission'], 0), array(
			'id' => 'commission',
		));

		echo $this->Html->tag('div', number_format($uangJalan['UangJalan']['uang_kuli_muat'], 0), array(
			'id' => 'uang_kuli_muat',
		));

		echo $this->Html->tag('div', number_format($uangJalan['UangJalan']['uang_kuli_bongkar'], 0), array(
			'id' => 'uang_kuli_bongkar',
		));

		echo $this->Html->tag('div', number_format($uangJalan['UangJalan']['asdp'], 0), array(
			'id' => 'asdp',
		));

		echo $this->Html->tag('div', number_format($uangJalan['UangJalan']['uang_kawal'], 0), array(
			'id' => 'uang_kawal',
		));

		echo $this->Html->tag('div', number_format($uangJalan['UangJalan']['uang_keamanan'], 0), array(
			'id' => 'uang_keamanan',
		));
?>