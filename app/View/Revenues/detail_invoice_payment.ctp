<?php
		$this->Html->addCrumb(__('Pembayaran Invoice'), array(
			'controller' => 'revenues',
			'action' => 'invoice_payments'
		));
		$this->Html->addCrumb($sub_module_title);

		$is_canceled = $this->Common->filterEmptyField($invoice, 'InvoicePayment', 'is_canceled');
		$status = $this->Common->filterEmptyField($invoice, 'InvoicePayment', 'status');
		$canceled_date = $this->Common->filterEmptyField($invoice, 'InvoicePayment', 'canceled_date');
		
		$id = $this->Common->filterEmptyField($invoice, 'InvoicePayment', 'id');
        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
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
				<td><?php echo $invoice['InvoicePayment']['nodoc'];?></td>
			</tr>
			<tr>
				<th><?php echo __('Customer');?></th>
				<td><?php echo !empty($invoice['Customer']['customer_name'])?$invoice['Customer']['customer_name']:false;?></td>
			</tr>
			<tr>
				<th><?php echo __('Account');?></th>
				<td><?php echo !empty($invoice['Coa']['name'])?$invoice['Coa']['name']:false;?></td>
			</tr>
			<tr>
				<th><?php echo __('Tgl Pembayaran');?></th>
				<td><?php echo $this->Common->customDate($invoice['InvoicePayment']['date_payment']);?></td>
			</tr>
			<tr>
				<th><?php echo __('Status');?></th>
				<td><?php echo $this->Revenue->_callStatusCustom($invoice, 'InvoicePayment');?></td>
			</tr>
			<!-- <tr>
				<th><?php echo __('Total Pembayaran');?></th>
				<td><?php echo $this->Number->currency($invoice['InvoicePayment']['grand_total_payment'], Configure::read('__Site.config_currency_code'), array('places' => 0));?></td>
			</tr> -->
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
					<th class="text-center"><?php echo __('Invoice Dibayar');?></th>
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

							$ppn = $this->Common->filterEmptyField($invoice, 'InvoicePayment', 'ppn', 0);
							$pph = $this->Common->filterEmptyField($invoice, 'InvoicePayment', 'pph', 0);
							$ppn_total = $this->Common->filterEmptyField($invoice, 'InvoicePayment', 'ppn_total', 0);
							$pph_total = $this->Common->filterEmptyField($invoice, 'InvoicePayment', 'pph_total', 0);
							$grandTotal += $ppn_total;

							if( !empty($ppn) ) {
								$colom = $this->Html->tag('td', __('PPN ('.$ppn.'%)'), array(
									'colspan' => 3,
									'align' => 'right',
									'style' => 'font-weight: bold;',
								));
								$colom .= $this->Html->tag('td', $this->Number->currency($ppn_total, Configure::read('__Site.config_currency_second_code'), array('places' => 0)), array(
									'align' => 'right',
									'style' => 'font-weight: bold;',
								));

								echo $this->Html->tag('tr', $colom, array(
									'class' => 'total-row'
								));
							}

							if( !empty($pph) ) {
								$colom = $this->Html->tag('td', __('PPh ('.$pph.'%)'), array(
									'colspan' => 3,
									'align' => 'right',
									'style' => 'font-weight: bold;',
								));
								$colom .= $this->Html->tag('td', $this->Number->currency($pph_total, Configure::read('__Site.config_currency_second_code'), array('places' => 0)), array(
									'align' => 'right',
									'style' => 'font-weight: bold;',
								));

								echo $this->Html->tag('tr', $colom, array(
									'class' => 'total-row'
								));
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