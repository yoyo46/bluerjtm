<?php
		$title = __('Jurnal Umum');
		$urlRoot = array(
			'controller' => 'cashbanks',
			'action' => 'general_ledgers',
			'admin' => false,
		);
		$value = !empty($value)?$value:false;
        $status = $this->Common->filterEmptyField($value, 'GeneralLedger', 'status');
        $transaction_status = $this->Common->filterEmptyField($value, 'GeneralLedger', 'transaction_status');
        $customStatus = $this->Common->_callTransactionStatus($value, 'GeneralLedger');

        echo $this->element('blocks/cashbanks/tables/list_approvals', array(
        	'urlBack' => false,
        	'modelName' => 'GeneralLedger',
    	));

		$this->Html->addCrumb($title, $urlRoot);
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('GeneralLedger');
?>
<div class="box form-added" id="general-ledger">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $title,
            ));
    ?>
    <div class="box-body">
    	<?php 
    			if( !empty($value) ) {
					echo $this->Common->buildInputForm('nodoc', __('No Dokumen *'), array(
						'disabled' => true,
					));
				}

				echo $this->Common->buildInputForm('transaction_date', __('Tgl Transaksi *'), array(
					'type' => 'text',
                    'textGroup' => $this->Common->icon('calendar'),
                    'class' => 'form-control pull-right custom-date',
				));
				echo $this->Common->buildInputForm('note', __('Keterangan'));

				if( empty($view) ) {
	                echo $this->Html->tag('div', $this->Html->link(__('%s Tambah', $this->Common->icon('plus-square')), '#', array(
		                    'escape' => false,
							'class' => 'btn bg-maroon field-added',
		                )), array(
	                	'class' => "form-group",
	            	));
	            } else {
        			$canceled_note = $this->Common->filterEmptyField($value, 'GeneralLedger', 'canceled_note', false, true, 'EOL');

        			if( !empty($canceled_note) ) {
        				$canceled_note = '<br>'.$canceled_note;
        			}

	            	echo $this->Html->tag('div', 
	            		$this->Html->tag('label', __('Status')).
	            		$this->Html->tag('p', $customStatus.$canceled_note), array(
	            		'class' => 'form-group',
            		));
	            }
	    ?>
    </div>
    <?php 
            echo $this->element('blocks/cashbanks/tables/general_ledger');
    ?>
    <div class="box-footer text-center action">
    	<?php
	    		echo $this->Html->link(__('Kembali'), $urlRoot, array(
					'class'=> 'btn btn-default',
				));
			
				if( empty($view) ) {
					$this->Common->_getButtonPostingUnposting( $value, 'GeneralLedger', array( 'Commit', 'Draft' ) );
				}
    	?>
    </div>
</div>
<?php
		echo $this->Form->end();
?>