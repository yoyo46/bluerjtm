<?php
		echo $this->Form->hidden('error', array(
			'id' => 'status',
			'value' => $msg['error'],
		));
		echo $this->Form->hidden('text', array(
			'id' => 'message',
			'value' => $msg['text'],
		));
		echo $this->element('blocks/revenues/invoice_info');
?>