<?php
		$message_alert = !empty($message_alert)?$message_alert:false;
		
		if($is_ajax && $msg['type'] == 'success' && !empty($msg['msg'])){
			echo $this->Html->tag('div', $msg['type'], array('id' => 'msg-status'));
			echo $this->Html->tag('div', $msg['msg'], array('id' => 'msg-text'));
		}else{
			echo $this->Form->create($modelName, array(
				'url' => $this->Html->url(null, true),
				'role' => 'form',
				'inputDefaults' => array('div' => false),
				'id' => 'form-content',
				'class' => 'ajax-form',
				'data-reload' => 'true',
			));
			
			if(!empty($msg['msg'])){
				echo $this->Html->tag('div', $msg['msg'], array('class' => 'alert alert-danger'));
			}

			if( !empty($message_alert) ) {
				echo $this->Html->tag('div', $this->Html->tag('p', $message_alert) );
			}

			// echo $this->Form->input('complete_date', array(
			// 	'class' => 'form-control custom-date',
			// 	'label' => __('Tgl Selesai'),
			// 	'div' => array(
			// 		'class' => 'form-group'
			// 	),
			// 	'type' => 'text',
			// 	'value' =>  date('d/m/Y'),
			// ));
			echo $this->Common->_callInputForm('complete', array(
				'type' => 'datetime',
				'label' => __('Tgl Selesai *'),
			));

			echo $this->Form->button('Ubah Status', array(
				'class' => 'btn btn-success',
				'data-action' => 'canceled-date'
			));

			echo $this->Form->end();
		}
?>