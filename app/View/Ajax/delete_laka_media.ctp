<?php
	echo $this->Html->tag('div', $msg['msg'], array('id' => 'message'));
	echo $this->Html->tag('div', $msg['type'], array('id' => 'status'));
?>