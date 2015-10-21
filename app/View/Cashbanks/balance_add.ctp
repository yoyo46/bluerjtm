<?php
		$this->Html->addCrumb(__('COA Balance'), array(
			'action' => 'balances',
			'admin' => false,
		));
		$this->Html->addCrumb($sub_module_title);

		echo $this->Form->create('CoaBalance');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Info Barang'); ?></h3>
    </div>
    <div class="box-body">
    	<?php 
				echo $this->Common->buildForm('coa_id', __('COA *'), array(
					'empty' => __('Pilih COA'),
				));
				echo $this->Common->buildForm('date', __('Tanggal *'), array(
					'type' => 'text',
					'class' => 'custom-date',
					'default' => 'current-date',
				));
				echo $this->Common->buildForm('type', __('Tipe COA *'), array(
					'empty' => __('Pilih Tipe COA'),
					'options' => array(
						'debit' => __('Debit'),
						'credit' => __('Credit'),
					),
				));
				echo $this->Common->buildForm('saldo', __('Saldo *'), array(
					'type' => 'text',
					'class' => 'input_price',
				));
				echo $this->Common->buildForm('note', __('Keterangan *'));
	    ?>
    </div>
</div>

<div class="box-footer text-center action">
	<?php
    		echo $this->Form->button(__('Simpan'), array(
				'div' => false, 
				'class'=> 'btn btn-success',
				'type' => 'submit',
			));
    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'index', 
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>
<?php
		echo $this->Form->end();
?>