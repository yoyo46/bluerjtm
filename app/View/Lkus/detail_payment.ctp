<?php
		$this->Html->addCrumb(__('Data Pembayaran LKU'), array(
			'controller' => 'lkus',
			'action' => 'payments'
		));
		$this->Html->addCrumb($sub_module_title);
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Informasi Pembayaran LKU');?></h3>
    </div>
	<div class="box-body table-responsive">
	    <table class="table table-hover">
			<tr>
				<th><?php echo __('No. Dokumen Pembayaran');?></th>
				<td><?php echo $LkuPayment['LkuPayment']['no_doc'];?></td>
			</tr>
			<tr>
				<th><?php echo __('Customer');?></th>
				<td><?php echo !empty($LkuPayment['CustomerNoType']['name'])?$LkuPayment['CustomerNoType']['name']:false;?></td>
			</tr>
			<!-- <tr>
				<th><?php // echo __('Account');?></th>
				<td><?php // echo !empty($LkuPayment['Coa']['name'])?$LkuPayment['Coa']['name']:false;?></td>
			</tr> -->
			<tr>
				<th><?php echo __('Tanggal Pembayaran');?></th>
				<td><?php echo $this->Common->customDate($LkuPayment['LkuPayment']['tgl_bayar']);?></td>
			</tr>
			<tr>
				<th><?php echo __('Total Pembayaran');?></th>
				<td><?php echo $this->Number->currency($LkuPayment['LkuPayment']['grandtotal'], Configure::read('__Site.config_currency_code'), array('places' => 0));?></td>
			</tr>
			<tr>
				<th><?php echo __('PPH');?></th>
				<td><?php echo $LkuPayment['LkuPayment']['pph'].'%';?></td>
			</tr>
			<tr>
				<th><?php echo __('PPN');?></th>
				<td><?php echo $LkuPayment['LkuPayment']['ppn'].'%';?></td>
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
			<thead class="header-LkuPayment-print">
				<tr>
					<th class="text-center"><?php echo __('No LKU');?></th>
					<th class="text-center"><?php echo __('Tgl LKU');?></th>
					<th class="text-right"><?php echo __('Jumlah Pembayaran.');?></th>
				</tr>
			</thead>
			<tbody>
				<?php
						if(!empty($LkuPayment['LkuPaymentDetail'])){
							$no=1;
							$grandTotal = 0;

							foreach ($LkuPayment['LkuPaymentDetail'] as $key => $value) {
								$grandTotal += $value['total_biaya_klaim'];
								$colom = $this->Html->tag('td', $value['Lku']['no_doc'], array(
									'class' => 'text-center'
								));
								$colom .= $this->Html->tag('td', $this->Common->customDate($value['Lku']['tgl_lku']), array(
									'class' => 'text-center'
								));
								$colom .= $this->Html->tag('td', $this->Number->currency($value['total_biaya_klaim'], Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
									'class' => 'text-right'
									));

								echo $this->Html->tag('tr', $colom);
							}

							$colom = $this->Html->tag('td', __('Total '), array(
								'colspan' => 2,
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
								'colspan' => 2
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
				'action' => 'payments', 
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>