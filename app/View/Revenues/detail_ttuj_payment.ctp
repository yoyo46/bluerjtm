<?php
		echo $this->element('blocks/revenues/ttuj_payment_crumb');
		$this->Html->addCrumb($sub_module_title);

		$id = $this->Common->filterEmptyField($invoice, 'TtujPayment', 'id');
		$nodoc = $this->Common->filterEmptyField($invoice, 'TtujPayment', 'nodoc');
		$date_payment = $this->Common->filterEmptyField($invoice, 'TtujPayment', 'date_payment');
		$receiver_name = $this->Common->filterEmptyField($invoice, 'TtujPayment', 'receiver_name');
		$description = $this->Common->filterEmptyField($invoice, 'TtujPayment', 'description', false, true, 'EOL');
		$coa = $this->Common->filterEmptyField($invoice, 'Coa', 'coa_name');
		
        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
        $datePayment = $this->Common->customDate($date_payment, 'd/m/Y');
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
				<td><?php echo $coa;?></td>
			</tr>
			<tr>
				<th><?php echo __('Tgl Dibayar');?></th>
				<td><?php echo $datePayment;?></td>
			</tr>
			<tr>
				<th><?php echo __('Dibayar Kepada');?></th>
				<td><?php echo $receiver_name;?></td>
			</tr>
			<tr>
				<th><?php echo __('Keterangan');?></th>
				<td><?php echo $description;?></td>
			</tr>
		</table>
	</div>
</div>
<?php
		$this->request->data = $invoice;
		echo $this->element('blocks/revenues/tables/detail_ttuj_payment');
?>
<div class="box-footer text-center action">
	<?php
    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'ttuj_payments', 
				$action_type,
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>