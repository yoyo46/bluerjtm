<?php
	if($is_ajax && $msg['type'] == 'success' && !empty($msg['msg'])){
		echo $this->Html->tag('div', $msg['type'], array('id' => 'status'));
		echo $this->Html->tag('div', $msg['msg'], array('id' => 'message'));
	}else{
		echo $this->Form->create($modelName, array(
			'url' => $this->Html->url(null, true),
			'role' => 'form',
			'inputDefaults' => array('div' => false),
			'id' => 'form-content',
		));
		
		if(!empty($msg['msg'])){
			echo $this->Html->tag('div', $msg['msg'], array('class' => 'alert alert-danger'));
		}

		switch ($action_type) {
			case 'invoice':
				echo $this->Html->tag('div', $this->Html->tag('p', __('Dengan melakukan penghapusan invoice, revenue akan berstatus menjadi posting dan jika sudah ada pembayaran invoice yang di lakukan, otomatis pembayaran invoice juga akan di batalkan.')) );
				break;
			
			default:
				echo $this->Html->tag('div', $this->Html->tag('p', __('Pastikan data yang Anda batalkan telah sesuai. Data yang telah dibatalkan tidak dapat diaktifkan kembali.')) );
				break;
		}

		echo $this->Form->input('canceled_date', array(
			'class' => 'form-control custom-date',
			'label' => __('Tanggal pembatalan'),
			'div' => array(
				'class' => 'form-group'
			),
			'type' => 'text',
			'value' =>  (!empty($canceled_date)) ? $this->Common->customDate($canceled_date) : date('d/m/Y')
		));

		echo $this->Form->input('canceled_note', array(
			'class' => 'form-control',
			'label' => __('Keterangan (optional)'),
			'div' => array(
				'class' => 'form-group'
			),
			'type' => 'textarea'
		));

		echo $this->Form->button('Hapus Data', array(
			// 'class' => 'btn btn-success',
			'class' => 'btn btn-success btn-submit-form',
			'data-action' => 'canceled-date'
		));

		echo $this->Form->end();
	}
?>