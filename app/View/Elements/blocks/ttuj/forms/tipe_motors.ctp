<?php 
		$idx = !empty($idx)?$idx:0;
		$city_id = !empty($city_id)?$city_id:false;
		$tipe_motor_id = !empty($tipe_motor_id)?$tipe_motor_id:false;
		$qty = !empty($qty)?$qty:false;
		$color_motor_id = !empty($color_motor_id)?$color_motor_id:false;
		$content = '';

		if( $data_action == 'retail' ) {
			$content .= $this->Html->tag('div', $this->Form->input('TtujTipeMotor.city_id.',array(
				'label'=> false, 
				'class'=>'form-control city-retail-id chosen-select',
				'required' => false,
				'empty' => __('Pilih Tujuan --'),
				'options' => !empty($tmpCities)?$tmpCities:false,
				'value' => $city_id,
			)), array(
				'class' => 'col-sm-3',
			));
			$colTipeMotor = 'col-sm-3';
			$colWarnaMotor = 'col-sm-2';
		} else {
			$colTipeMotor = 'col-sm-5';
			$colWarnaMotor = 'col-sm-3';
		}

		$content .= $this->Html->tag('div', $this->Form->input('TtujTipeMotor.tipe_motor_id.', array(
			'class' => 'form-control tipe_motor_id chosen-select',
			'label' => false,
			'empty' => __('Tipe Motor'),
			'options' => $tipeMotors,
			'required' => false,
			'value' => $tipe_motor_id,
		)), array(
			'class' => $colTipeMotor,
		));
		$content .= $this->Html->tag('div', $this->Form->input('TtujTipeMotor.color_motor_id.', array(
			'class' => 'form-control',
			'label' => false,
			'empty' => __('Warna Motor'),
			'options' => $colors,
			'required' => false,
			'value' => $color_motor_id,
		)), array(
			'class' => $colWarnaMotor,
		));
		$content .= $this->Html->tag('div', $this->Form->input('TtujTipeMotor.qty.', array(
			'class' => 'form-control qty-muatan',
			'label' => false,
			'required' => false,
			'div' => false,
			'value' => $qty,
		)), array(
			'class' => 'col-sm-2',
		));
		$content .= $this->Html->tag('div', $this->Html->link('<i class="fa fa-times"></i> ', 'javascript:', array(
			'class' => 'removed btn btn-danger btn-xs',
			'escape' => false
		)), array(
			'class' => 'col-sm-2 text-center',
		));

		$itemContent = $this->Html->tag('div', $content, array(
			'class' => 'row item',
		));

		if( empty($idx) ) {
			$itemContent = $this->Html->tag('div', $itemContent, array(
				'class' => 'field-copy',
			));
		}

		echo $itemContent;
?>
