<?php 
		$result = !empty($result)?$result:false;
		$status = $this->Common->filterEmptyField($result, 'status');
		$class_wrapper_success =  sprintf('serial-number-fill-%s', $id);
?>
<div class="form-serial-number">
	<?php 
			if( $status == 'success' ) {
				echo $this->element('blocks/products/receipts/tables/serial_number_counter');
			} else {
				echo $this->Form->create('ProductReceiptDetailSerialNumber', array(
					'class' => 'serial-number-form ajax-form',
					'data-close-modal' => 'true',
					'data-wrapper-write' => '.form-serial-number',
					'data-wrapper-success' => sprintf('.%s', $class_wrapper_success),
				));

				if( !empty($number) ) {
					$dataArr = array();

					for ($i=0; $i < $number; $i++) { 
						$dataArr[] = $this->Html->tag('li', $this->Common->buildInputForm(sprintf('ProductReceiptDetailSerialNumber.serial_number.%s', $i), false, array(
							'fieldError' => sprintf('ProductReceiptDetailSerialNumber.%s.serial_number', $i),
						)), array(
							'class' => 'col-sm-4',
						));
					}

					echo $this->Html->tag('ul', implode('', $dataArr), array(
						'class' => 'row',
					));	
		        	
		        	if( empty($view) ) {
		        		echo $this->element('blocks/common/forms/submit_action');
		        	}
				} else {
                     echo $this->Html->tag('p', __('Mohon masukan QTY'), array(
                        'class' => 'alert alert-warning text-center',
                    ));
				}

				echo $this->Form->hidden('ProductReceipt.session_id');
				echo $this->Form->end();
			}
	?>
</div>