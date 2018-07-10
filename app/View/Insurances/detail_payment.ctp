<?php
		$this->Html->addCrumb(__('Pembayaran Asuransi'), array(
			'controller' => 'insurances',
			'action' => 'payments'
		));
		$this->Html->addCrumb($sub_module_title);

		$id = $this->Common->filterEmptyField($value, 'InsurancePayment', 'id');
		$nodoc = $this->Common->filterEmptyField($value, 'InsurancePayment', 'nodoc');
		$payment_date = $this->Common->filterEmptyField($value, 'InsurancePayment', 'payment_date');
        $rejected = $this->Common->filterEmptyField($value, 'InsurancePayment', 'rejected');
        $note = $this->Common->filterEmptyField($value, 'InsurancePayment', 'note', '-', true, 'EOL');
		$coa_name = $this->Common->filterEmptyField($value, 'Coa', 'coa_name', '-');
		$cogs_name = $this->Common->filterEmptyField($value, 'Cogs', 'cogs_name', '-');

        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
        $payment_date = $this->Common->formatDate($payment_date, 'd/m/Y');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Informasi Pembayaran');?></h3>
    </div>
	<div class="box-body table-responsive">
	    <table class="table table-hover">
			<tr>
				<th><?php echo __('No. Referensi');?></th>
				<td><?php echo $noref;?></td>
			</tr>
			<tr>
				<th><?php echo __('No. Dokumen');?></th>
				<td><?php echo $nodoc;?></td>
			</tr>
			<tr>
				<th><?php echo __('Account Kas/Bank');?></th>
				<td><?php echo $coa_name;?></td>
			</tr>
			<tr>
				<th><?php echo __('Cost Center');?></th>
				<td><?php echo $cogs_name;?></td>
			</tr>
			<tr>
				<th><?php echo __('Tgl Bayar');?></th>
				<td><?php echo $payment_date;?></td>
			</tr>
			<tr>
				<th><?php echo __('Keterangan');?></th>
				<td><?php echo $note;?></td>
			</tr>
			<tr>
				<th><?php echo __('Status');?></th>
				<td>
					<?php
	                        if( !empty($rejected) ) {
	                            echo $this->Html->tag('span', __('Void'), array(
	                                'class' => 'label label-danger',
	                            ));
	                        } else {
	                            echo $this->Html->tag('span', __('Dibayar'), array(
	                                'class' => 'label label-success',
	                            ));
	                        }
					?>
				</td>
			</tr>
		</table>
	</div>
</div>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Detail Pembayaran');?></h3>
    </div>
	<div class="box-body table-responsive">
		<table class="table table-hover">
			<thead>
        		<tr>
	                <th><?php echo __('No. Polis');?></th>
	                <th><?php echo __('Asuransi');?></th>
	                <th><?php echo __('Nama Tertanggung');?></th>
	                <th class="text-center"><?php echo __('Tgl Polis');?></th>
	                <th class="text-center"><?php echo __('Total Premi');?></th>
        		</tr>
        	</thead>
        	<tbody>
                <?php
		    			echo $this->element('blocks/insurances/payment_detail');
		    	?>
        	</tbody>
		</table>
	</div>
</div>
<div class="box-footer text-center action">
	<?php
    		echo $this->Html->link(__('Kembali'), array(
				'controller' => 'insurances',
				'action' => 'payments'
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>