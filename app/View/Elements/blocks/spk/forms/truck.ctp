<?php
        $data = $this->request->data;
		$nonprodClass = Common::_callDisplayToggle('non-production', $data);

		echo $this->element('blocks/common/forms/input_pickup', array(
			'fieldName' => 'Spk.nolaka',
			'label' => __('No. LAKA'),
			'dataUrl' => array(
				'controller' => 'ajax',
				'action' => 'laka_picker',
			),
			'onchange' => 'false',
			'attributes' => array(
				'class' => 'ajax-change form-control',
				'href' => $this->Html->url(array(
					'controller' => 'spk',
					'action' => 'laka',
				)),
				'data-wrapper-write-page' => '.wrapper-driver,.wrapper-truck',
			),
			'input_id' => 'laka-id',
			'pickup_title' => __('LAKA'),
			'wrapper_class' => 'wrapper-internal',
		));
		echo $this->element('blocks/common/forms/input_pickup', array(
			'fieldName' => 'Spk.nopol',
			'label' => __('No. Pol %s', $this->Html->tag('span', '*', array(
				'class' => __('wrapper-non-production %s', $nonprodClass),
			))).$this->Html->tag('span', $this->element('blocks/spk/truck_history'), array(
				'class' => 'wrapper-truck-history',
			)),
			'dataUrl' => array(
				'controller' => 'ajax',
				'action' => 'truck_picker',
				'return_value' => 'nopol',
				'without_branch' => 1,
			),
			'onchange' => 'false',
			'attributes' => array(
				'class' => 'ajax-change form-control',
				'href' => $this->Html->url(array(
					'controller' => 'spk',
					'action' => 'driver_truck',
				)),
				'data-wrapper-write-page' => '.wrapper-driver,.wrapper-truck-history',
			),
			'wrapper_class' => 'wrapper-truck',
			'pickup_title' => __('Truk'),
		));
		echo $this->element('blocks/spk/forms/driver');
?>