<?php 
		if( !empty($result) ) {
			echo $this->Html->tag('div', $result['Truck']['capacity'], array(
				'id' => 'truck_capacity',
			));

			$driver_name = !empty($result['Driver']['name'])?$result['Driver']['name']:'';
			$driver_id = !empty($result['Driver']['id'])?$result['Driver']['id']:'';

			if( !empty($result['Driver']['alias']) ) {
				$driver_name = sprintf('%s ( %s )', $driver_name, $result['Driver']['alias']);
			}

			echo $this->Html->tag('div', $driver_name, array(
				'id' => 'driver_name',
			));

			echo $this->Html->tag('div', $driver_id, array(
				'id' => 'driver_id',
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

		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['commission_extra'])?$uangJalan['UangJalan']['commission_extra']:0, array(
			'id' => 'commission_extra',
		));
		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['commission_extra_per_unit'])?$uangJalan['UangJalan']['commission_extra_per_unit']:0, array(
			'id' => 'commission_extra_per_unit',
		));

		echo $this->Html->tag('div', !empty($uangKuliMuat['UangKuli']['uang_kuli'])?$uangKuliMuat['UangKuli']['uang_kuli']:0, array(
			'id' => 'uang_kuli_muat',
		));
		echo $this->Html->tag('div', ( !empty($uangKuliMuat['UangKuli']['uang_kuli_type']) && $uangKuliMuat['UangKuli']['uang_kuli_type'] == 'per_unit' )?1:0, array(
			'id' => 'uang_kuli_muat_per_unit',
		));

		echo $this->Html->tag('div', !empty($uangKuliBongkar['UangKuli']['uang_kuli'])?$uangKuliBongkar['UangKuli']['uang_kuli']:0, array(
			'id' => 'uang_kuli_bongkar',
		));
		echo $this->Html->tag('div', ( !empty($uangKuliBongkar['UangKuli']['uang_kuli_type']) && $uangKuliBongkar['UangKuli']['uang_kuli_type'] == 'per_unit' )?1:0, array(
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

		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['uang_jalan_extra_per_unit'])?$uangJalan['UangJalan']['uang_jalan_extra_per_unit']:0, array(
			'id' => 'uang_jalan_extra_per_unit',
		));

		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['min_capacity'])?$uangJalan['UangJalan']['min_capacity']:0, array(
			'id' => 'min_capacity',
		));

		echo $this->Html->tag('div', !empty($uangJalan['UangJalan']['is_unit'])?$uangJalan['UangJalan']['is_unit']:0, array(
			'id' => 'is_unit',
		));
?>
<div id="list-tipe-motor">
	<?php 
			if( !empty($uangJalan['UangJalanTipeMotor']) ) {
				foreach ($uangJalan['UangJalanTipeMotor'] as $key => $value) {
					echo $this->Html->tag('div', $value['uang_jalan_1'], array(
						'class' => sprintf('uang-jalan-1-%s', $value['group_motor_id'])
					));
					echo $this->Html->tag('div', !empty($value['uang_jalan_2'])?$value['uang_jalan_2']:0, array(
						'class' => sprintf('uang-jalan-2-%s', $value['group_motor_id'])
					));
				}
			}

			if( !empty($uangKuliMuat['UangKuliGroupMotor']) ) {
				foreach ($uangKuliMuat['UangKuliGroupMotor'] as $key => $value) {
					echo $this->Html->tag('div', $value['uang_kuli'], array(
						'class' => sprintf('uang-kuli-muat-%s', $value['group_motor_id'])
					));
				}
			}

			if( !empty($uangKuliBongkar['UangKuliGroupMotor']) ) {
				foreach ($uangKuliBongkar['UangKuliGroupMotor'] as $key => $value) {
					echo $this->Html->tag('div', $value['uang_kuli'], array(
						'class' => sprintf('uang-kuli-bongkar-%s', $value['group_motor_id'])
					));
				}
			}

			// if( !empty($uangJalan['UangExtraGroupMotor']) ) {
			// 	foreach ($uangJalan['UangExtraGroupMotor'] as $key => $value) {
			// 		echo $this->Html->tag('div', $value['uang_jalan_extra'], array(
			// 			'class' => sprintf('uang-jalan-extra-%s', $value['group_motor_id'])
			// 		));
			// 		echo $this->Html->tag('div', $value['min_capacity'], array(
			// 			'class' => sprintf('uang-jalan-extra-min-capacity-%s', $value['group_motor_id'])
			// 		));
			// 	}
			// }

			if( !empty($uangJalan['CommissionGroupMotor']) ) {
				foreach ($uangJalan['CommissionGroupMotor'] as $key => $value) {
					echo $this->Html->tag('div', $value['commission'], array(
						'class' => sprintf('commission-%s', $value['group_motor_id'])
					));
				}
			}

			// if( !empty($uangJalan['CommissionExtraGroupMotor']) ) {
			// 	foreach ($uangJalan['CommissionExtraGroupMotor'] as $key => $value) {
			// 		echo $this->Html->tag('div', $value['commission'], array(
			// 			'class' => sprintf('commission-extra-%s', $value['group_motor_id'])
			// 		));
			// 		echo $this->Html->tag('div', $value['min_capacity'], array(
			// 			'class' => sprintf('commission-extra-min-capacity-%s', $value['group_motor_id'])
			// 		));
			// 	}
			// }

			if( !empty($uangJalan['AsdpGroupMotor']) ) {
				foreach ($uangJalan['AsdpGroupMotor'] as $key => $value) {
					echo $this->Html->tag('div', $value['asdp'], array(
						'class' => sprintf('asdp-%s', $value['group_motor_id'])
					));
				}
			}

			if( !empty($uangJalan['UangKawalGroupMotor']) ) {
				foreach ($uangJalan['UangKawalGroupMotor'] as $key => $value) {
					echo $this->Html->tag('div', $value['uang_kawal'], array(
						'class' => sprintf('uang-kawal-%s', $value['group_motor_id'])
					));
				}
			}

			if( !empty($uangJalan['UangKeamananGroupMotor']) ) {
				foreach ($uangJalan['UangKeamananGroupMotor'] as $key => $value) {
					echo $this->Html->tag('div', $value['uang_keamanan'], array(
						'class' => sprintf('uang-keamanan-%s', $value['group_motor_id'])
					));
				}
			}
	?>	
</div>