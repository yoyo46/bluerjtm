<?php
		$this->Html->addCrumb(__('Data Pembayaran LKU'), array(
			'controller' => 'lkus',
			'action' => 'payments'
		));
		$this->Html->addCrumb($sub_module_title);

		$coa_name = $this->Common->filterEmptyField($LkuPayment, 'Coa', 'coa_name', '-');
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
			<tr>
				<th><?php echo __('Account Kas/Bank');?></th>
				<td><?php echo $coa_name;?></td>
			</tr>
			<tr>
				<th><?php echo __('Tgl Pembayaran');?></th>
				<td><?php echo $this->Common->customDate($LkuPayment['LkuPayment']['tgl_bayar']);?></td>
			</tr>
			<tr>
				<th><?php echo __('Total Pembayaran');?></th>
				<td><?php echo $this->Number->currency($LkuPayment['LkuPayment']['grandtotal'], Configure::read('__Site.config_currency_code'), array('places' => 0));?></td>
			</tr>
			<?php
				if(!empty($LkuPayment['LkuPayment']['pph'])){
			?>
			<tr>
				<th><?php echo __('PPH');?></th>
				<td><?php echo $LkuPayment['LkuPayment']['pph'].'%';?></td>
			</tr>
			<?php
				}
			?>
			<?php
				if(!empty($LkuPayment['LkuPayment']['ppn'])){
			?>
			<tr>
				<th><?php echo __('PPN');?></th>
				<td><?php echo $LkuPayment['LkuPayment']['ppn'].'%';?></td>
			</tr>
			<?php
				}
			?>
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
					<th width="20%" class="text-center"><?php echo __('No LKU');?></th>
					<th width="20%" class="text-center"><?php echo __('Tgl LKU');?></th>
					<th width="20%" class="text-center"><?php echo __('Tipe Motor');?></th>
					<th width="20%" class="text-center"><?php echo __('Part Motor');?></th>
					<th width="20%" class="text-right"><?php echo __('Jumlah Pembayaran.');?></th>
				</tr>
			</thead>
			<tbody>
				<?php
						if(!empty($LkuPayment['LkuPaymentDetail'])){
							$no=1;
							$grandTotal = 0;

							foreach ($LkuPayment['LkuPaymentDetail'] as $key => $value) {
								$grandTotal += $value['total_biaya_klaim'];

								$link_lku = $this->Html->link($value['Lku']['no_doc'], array(
									'controller' => 'lkus',
									'action' => 'detail',
									$value['Lku']['id']
								), array(
									'target' => 'blank'
								));

								$colom = $this->Html->tag('td', $link_lku, array(
									'class' => 'text-center'
								));
								$colom .= $this->Html->tag('td', $this->Common->customDate($value['Lku']['tgl_lku']), array(
									'class' => 'text-center'
								));

								$colom .= $this->Html->tag('td', (!empty($value['TipeMotor']['name']) ? $value['TipeMotor']['name'] : ' - '), array(
									'class' => 'text-center'
								));
								$colom .= $this->Html->tag('td', (!empty($value['PartsMotor']['name']) ? $value['PartsMotor']['name'] : ' - '), array(
									'class' => 'text-center'
								));
								
								$colom .= $this->Html->tag('td', $this->Number->currency($value['total_biaya_klaim'], Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
									'class' => 'text-right',
									));

								echo $this->Html->tag('tr', $colom);
							}

							$colom = $this->Html->tag('td', __('Total '), array(
								'colspan' => 4,
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
								'colspan' => 5
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