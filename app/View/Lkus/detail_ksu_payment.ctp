<?php
		$this->Html->addCrumb(__('Data Pembayaran KSU'), array(
			'controller' => 'lkus',
			'action' => 'ksu_payments'
		));
		$this->Html->addCrumb($sub_module_title);
		
		$coa_name = $this->Common->filterEmptyField($KsuPayment, 'Coa', 'coa_name', '-');
		$id = $this->Common->filterEmptyField($KsuPayment, 'KsuPayment', 'id');
        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo __('Informasi Pembayaran KSU');?></h3>
    </div>
	<div class="box-body table-responsive">
	    <table class="table table-hover">
			<tr>
				<th><?php echo __('No. Referensi');?></th>
				<td><?php echo $noref;?></td>
			</tr>
			<tr>
				<th><?php echo __('No. Dokumen');?></th>
				<td><?php echo $KsuPayment['KsuPayment']['no_doc'];?></td>
			</tr>
			<tr>
				<th><?php echo __('Customer');?></th>
				<td><?php echo !empty($KsuPayment['CustomerNoType']['name'])?$KsuPayment['CustomerNoType']['name']:false;?></td>
			</tr>
			<tr>
				<th><?php echo __('Account Kas/Bank');?></th>
				<td><?php echo $coa_name;?></td>
			</tr>
			<tr>
				<th><?php echo __('Tgl Pembayaran');?></th>
				<td><?php echo $this->Common->customDate($KsuPayment['KsuPayment']['tgl_bayar']);?></td>
			</tr>
			<tr>
				<th><?php echo __('Total Pembayaran');?></th>
				<td><?php echo $this->Number->currency($KsuPayment['KsuPayment']['grandtotal'], Configure::read('__Site.config_currency_code'), array('places' => 0));?></td>
			</tr>
			<?php
				if(!empty($KsuPayment['KsuPayment']['pph'])){
			?>
			<tr>
				<th><?php echo __('PPH');?></th>
				<td><?php echo $KsuPayment['KsuPayment']['pph'].'%';?></td>
			</tr>
			<?php
				}
			?>
			<?php
				if(!empty($KsuPayment['KsuPayment']['ppn'])){
			?>
			<tr>
				<th><?php echo __('PPN');?></th>
				<td><?php echo $KsuPayment['KsuPayment']['ppn'].'%';?></td>
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
			<thead class="header-KsuPayment-print">
				<tr>
					<th class="text-center"><?php echo __('No KSU');?></th>
					<th class="text-center"><?php echo __('Tgl KSU');?></th>
	                <th><?php echo __('TTUJ');?></th>
	                <th><?php echo __('Supir');?></th>
	                <th class="text-center"><?php echo __('Nopol');?></th>
					<th class="text-center"><?php echo __('Perlengkapan');?></th>
					<th class="text-right"><?php echo __('Jumlah Pembayaran.');?></th>
				</tr>
			</thead>
			<tbody>
				<?php
						if(!empty($KsuPayment['KsuPaymentDetail'])){
							$no=1;
							$grandTotal = 0;

							foreach ($KsuPayment['KsuPaymentDetail'] as $key => $value) {
            					$no_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'no_ttuj');
            					$nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol');
                				$driver = $this->Common->_callGetDriver($value);

								$grandTotal += $value['total_biaya_klaim'];

								$link_lku = '-';
								if(!empty($value['Ksu']['no_doc'])){
									$link_lku = $this->Html->link($value['Ksu']['no_doc'], array(
										'controller' => 'lkus',
										'action' => 'detail_ksu',
										$value['Ksu']['id']
									), array(
										'target' => 'blank'
									));
								}

								$colom = $this->Html->tag('td', $link_lku, array(
									'class' => 'text-center'
								));
								$colom .= $this->Html->tag('td', (!empty($value['Ksu']['tgl_ksu']) ? $this->Common->customDate($value['Ksu']['tgl_ksu']) : '-'), array(
									'class' => 'text-center'
								));
								$colom .= $this->Html->tag('td', $no_ttuj);
								$colom .= $this->Html->tag('td', $driver);
								$colom .= $this->Html->tag('td', $nopol, array(
									'class' => 'text-center'
								));

								$colom .= $this->Html->tag('td', (!empty($value['Perlengkapan']['name']) ? $value['Perlengkapan']['name'] : ' - '), array(
									'class' => 'text-center'
								));
								
								$colom .= $this->Html->tag('td', $this->Number->currency($value['total_biaya_klaim'], Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
									'class' => 'text-right',
									));

								echo $this->Html->tag('tr', $colom);
							}

							$colom = $this->Html->tag('td', __('Total '), array(
								'colspan' => 6,
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
								'colspan' => 8
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
				'action' => 'ksu_payments', 
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>