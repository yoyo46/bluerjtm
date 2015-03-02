<?php
		$this->Html->addCrumb(__('Pembayaran Invoice'), array(
			'controller' => 'revenues',
			'action' => 'invoice_payments'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Informasi Pembayaran');?></h3>
    </div>
	<div class="box-body table-responsive">
	    <table class="table table-hover">
			<tr>
				<th><?php echo __('No. Dokumen Pembayaran');?></th>
				<td><?php echo $invoice['InvoicePayment']['nodoc'];?></td>
			</tr>
			<tr>
				<th><?php echo __('Customer');?></th>
				<td><?php echo !empty($invoice['Customer']['customer_name'])?$invoice['Customer']['customer_name']:false;?></td>
			</tr>
			<tr>
				<th><?php echo __('Tanggal Pembayaran');?></th>
				<td><?php echo $this->Common->customDate($invoice['InvoicePayment']['date_payment']);?></td>
			</tr>
			<tr>
				<th><?php echo __('Total Pembayaran');?></th>
				<td><?php echo $this->Number->currency($invoice['InvoicePayment']['total_payment'], Configure::read('__Site.config_currency_code'), array('places' => 0));?></td>
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
			<thead class="header-invoice-print">
				<tr>
					<th class="text-center"><?php echo __('No. Invoice');?></th>
					<th class="text-center"><?php echo __('Tgl Invoice');?></th>
                    <th class="text-center"><?php echo __('Periode');?></th>
					<th class="text-center"><?php echo __('Jumlah Pembayaran.');?></th>
				</tr>
			</thead>
			<tbody>
				<?php
						if(!empty($invoice['InvoicePaymentDetail'])){
							$no=1;
							$grandTotal = 0;

							foreach ($invoice['InvoicePaymentDetail'] as $key => $value) {
								$grandTotal += $value['price_pay'];
								$colom = $this->Html->tag('td', $value['Invoice']['no_invoice'], array(
									'class' => 'text-center'
								));
								$colom .= $this->Html->tag('td', $this->Common->customDate($value['Invoice']['invoice_date']), array(
									'class' => 'text-center'
								));
                                $colom .= $this->Html->tag('td', sprintf('%s s/d %s', $this->Common->customDate($value['Invoice']['period_from'], 'd/m/Y'), $this->Common->customDate($value['Invoice']['period_to'], 'd/m/Y')), array(
									'class' => 'text-center'
								));
								$colom .= $this->Html->tag('td', $this->Number->currency($value['price_pay'], Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
									'class' => 'text-right'
									));

								echo $this->Html->tag('tr', $colom);
							}

							$colom = $this->Html->tag('td', __('Total '), array(
								'colspan' => 3,
								'align' => 'right'
							));
							$colom .= $this->Html->tag('td', $this->Number->currency($grandTotal, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
								'class' => 'text-right'
							));

							echo $this->Html->tag('tr', $colom, array(
								'class' => 'total-row'
							));
						}else{
							$colom = $this->Html->tag('td', __('Data tidak ditemukan.'), array(
								'colspan' => 3
							));

							echo $this->Html->tag('tr', $colom);
						}
				?>
			</tbody>
		</table>
	</div>
</div>
<div class="box-footer text-center action">
	<?php
    		echo $this->Html->link(__('Kembali'), array(
				'action' => 'invoice_payments', 
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>