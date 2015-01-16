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

		echo $this->Html->tag('div', $uangJalan['UangJalan']['uang_jalan_1'], array(
			'id' => 'uang_jalan_1',
		));
		echo $this->Html->tag('div', $uangJalan['UangJalan']['uang_jalan_2'], array(
			'id' => 'uang_jalan_2',
		));
		echo $this->Html->tag('div', $uangJalan['UangJalan']['uang_jalan_per_unit'], array(
			'id' => 'uang_jalan_per_unit',
		));

		echo $this->Html->tag('div', $uangJalan['UangJalan']['commission'], array(
			'id' => 'commission',
		));
		echo $this->Html->tag('div', $uangJalan['UangJalan']['commission_per_unit'], array(
			'id' => 'commission_per_unit',
		));

		echo $this->Html->tag('div', number_format($uangJalan['UangJalan']['uang_kuli_muat'], 0), array(
			'id' => 'uang_kuli_muat',
		));
		echo $this->Html->tag('div', $uangJalan['UangJalan']['uang_kuli_muat_per_unit'], array(
			'id' => 'uang_kuli_muat_per_unit',
		));

		echo $this->Html->tag('div', number_format($uangJalan['UangJalan']['uang_kuli_bongkar'], 0), array(
			'id' => 'uang_kuli_bongkar',
		));
		echo $this->Html->tag('div', $uangJalan['UangJalan']['uang_kuli_bongkar_per_unit'], array(
			'id' => 'uang_kuli_bongkar_per_unit',
		));

		echo $this->Html->tag('div', $uangJalan['UangJalan']['asdp'], array(
			'id' => 'asdp',
		));
		echo $this->Html->tag('div', $uangJalan['UangJalan']['asdp_per_unit'], array(
			'id' => 'asdp_per_unit',
		));

		echo $this->Html->tag('div', $uangJalan['UangJalan']['uang_kawal'], array(
			'id' => 'uang_kawal',
		));
		echo $this->Html->tag('div', $uangJalan['UangJalan']['uang_kawal_per_unit'], array(
			'id' => 'uang_kawal_per_unit',
		));

		echo $this->Html->tag('div', $uangJalan['UangJalan']['uang_keamanan'], array(
			'id' => 'uang_keamanan',
		));
		echo $this->Html->tag('div', $uangJalan['UangJalan']['uang_keamanan_per_unit'], array(
			'id' => 'uang_keamanan_per_unit',
		));

		echo $this->Html->tag('div', number_format($uangJalan['UangJalan']['uang_jalan_extra'], 0), array(
			'id' => 'uang_jalan_extra',
		));

		echo $this->Html->tag('div', $uangJalan['UangJalan']['min_capacity'], array(
			'id' => 'min_capacity',
		));

		echo $this->Html->tag('div', number_format($uangJalan['UangJalan']['is_unit'], 0), array(
			'id' => 'is_unit',
		));
?>