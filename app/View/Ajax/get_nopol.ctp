<?php 
		// echo $this->Form->input('truck_id',array(
		// 	'label'=> false, 
		// 	'class'=>'form-control nopol',
		// 	'required' => false,
		// 	'empty' => __('Pilih No. Pol --'),
		// 	'options' => $result,
		// 	'div' => false,
		// 	'id' => 'truck_id',
		// ));

		if( !empty($result) ) {
			echo $this->Html->tag('div', $result['Truck']['capacity'], array(
				'id' => 'truck_capacity',
			));

			echo $this->Html->tag('div', $result['Driver']['name'], array(
				'id' => 'driver_name',
			));
		}

		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['uang_jalan_1'])?$uangJalan['UangJalan']['uang_jalan_1']:0, array(
			'id' => 'uang_jalan_1',
		));
		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['uang_jalan_2'])?$uangJalan['UangJalan']['uang_jalan_2']:0, array(
			'id' => 'uang_jalan_2',
		));
		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['uang_jalan_per_unit'])?$uangJalan['UangJalan']['uang_jalan_per_unit']:0, array(
			'id' => 'uang_jalan_per_unit',
		));

		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['commission'])?$uangJalan['UangJalan']['commission']:0, array(
			'id' => 'commission',
		));
		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['commission_per_unit'])?$uangJalan['UangJalan']['commission_per_unit']:0, array(
			'id' => 'commission_per_unit',
		));

		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['uang_kuli_muat'])?$uangJalan['UangJalan']['uang_kuli_muat']:0, array(
			'id' => 'uang_kuli_muat',
		));
		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['uang_kuli_muat_per_unit'])?$uangJalan['UangJalan']['uang_kuli_muat_per_unit']:0, array(
			'id' => 'uang_kuli_muat_per_unit',
		));

		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['uang_kuli_bongkar'])?$uangJalan['UangJalan']['uang_kuli_bongkar']:0, array(
			'id' => 'uang_kuli_bongkar',
		));
		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['uang_kuli_bongkar_per_unit'])?$uangJalan['UangJalan']['uang_kuli_bongkar_per_unit']:0, array(
			'id' => 'uang_kuli_bongkar_per_unit',
		));

		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['asdp'])?$uangJalan['UangJalan']['asdp']:0, array(
			'id' => 'asdp',
		));
		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['asdp_per_unit'])?$uangJalan['UangJalan']['asdp_per_unit']:0, array(
			'id' => 'asdp_per_unit',
		));

		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['uang_kawal'])?$uangJalan['UangJalan']['uang_kawal']:0, array(
			'id' => 'uang_kawal',
		));
		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['uang_kawal_per_unit'])?$uangJalan['UangJalan']['uang_kawal_per_unit']:0, array(
			'id' => 'uang_kawal_per_unit',
		));

		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['uang_keamanan'])?$uangJalan['UangJalan']['uang_keamanan']:0, array(
			'id' => 'uang_keamanan',
		));
		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['uang_keamanan_per_unit'])?$uangJalan['UangJalan']['uang_keamanan_per_unit']:0, array(
			'id' => 'uang_keamanan_per_unit',
		));

		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['uang_jalan_extra'])?$uangJalan['UangJalan']['uang_jalan_extra']:0, array(
			'id' => 'uang_jalan_extra',
		));

		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['min_capacity'])?$uangJalan['UangJalan']['min_capacity']:0, array(
			'id' => 'min_capacity',
		));

		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['is_unit'])?$uangJalan['UangJalan']['is_unit']:0, array(
			'id' => 'is_unit',
		));
?>