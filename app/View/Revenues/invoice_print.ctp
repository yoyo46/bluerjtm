<?php
if($action_print == 'pdf'){
	App::import('Vendor','xtcpdf');
    ob_end_clean();
    $tcpdf = new XTCPDF();

    $tcpdf->setPrintHeader(false);
    $tcpdf->setPrintFooter(false);

    $textfont = 'freesans';

    $tcpdf->SetAuthor("AcuaSotf");
    $tcpdf->SetAutoPageBreak( true );
    $tcpdf->setHeaderFont(array($textfont,'',10));
    $tcpdf->xheadercolor = array(255,255,255);
    $tcpdf->AddPage('L');
    // Table with rowspans and THEAD
    $table_tr_head = 'background-color: #3C8DBC; border-right: 1px solid #FFFFFF; color: #FFFFFF; font-weight: bold; padding: 0 10px; text-align: left;';
    $table_th = 'padding-top: 3px';
    $table = 'width:100%;font-size: 24px; border: 1px solid #CCC; border-collapse: collapse; padding: 0; margin: 15px 0 0 0;';

    $no = 1;
    $each_loop_message = '';

    $content = '';
    if(!empty($revenue_detail)){
		foreach ($revenue_detail as $key => $val_detail) {
			if(!empty($val_detail)){
				$no=1;
				$total = 0;
				foreach ($val_detail as $key => $value) {
					$colom = $this->Html->tag('td', $no++);
					$colom .= $this->Html->tag('td', $value['TipeMotor']['name']);
					$colom .= $this->Html->tag('td', $value['RevenueDetail']['qty_unit']);
					$colom .= $this->Html->tag('td', $this->Number->currency($value['RevenueDetail']['price_unit'], Configure::read('__Site.config_currency_code'), array('places' => 0)));
					$colom .= $this->Html->tag('td', $value['RevenueDetail']['no_reference']);

					if(!empty($value['RevenueDetail']['payment_type']) && $value['RevenueDetail']['payment_type'] == 'per_truck'){
						$total += $value['RevenueDetail']['price_unit'];
					}else{
						$total += $value['RevenueDetail']['price_unit'] * $value['RevenueDetail']['qty_unit'];
					}
					$content .= $this->Html->tag('tr', $colom);
				}
				$colom = $this->Html->tag('td', __('Total '), array(
					'colspan' => 3,
					'align' => 'right'
				));
				$colom .= $this->Html->tag('td', $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0)), array('colspan' => 2) );
				$content .= $this->Html->tag('tr', $colom);
			}else{
				$colom = $this->Html->tag('td', __('Data tidak ditemukan.'), array(
					'colspan' => 5
				));

				$content .= $this->Html->tag('tr', $colom);
			}
		}
	}
	
    $date_title = $sub_module_title;
    $no_invoice = $invoice['Invoice']['no_invoice'];
    $Customer = $invoice['Customer']['name'];
    $Periode = $invoice['Invoice']['period_from'].' sampai '.$invoice['Invoice']['period_to'];
    $masa_berlaku = $invoice['Invoice']['due_invoice'];

    $title_tipe_invoice = '';
    if($action == 'tarif'){
		$title_tipe_invoice = sprintf('Tarif Angkutan : %s', $this->Number->currency($val_detail[0]['RevenueDetail']['price_unit'], Configure::read('__Site.config_currency_code'), array('places' => 0)) );
	}else{
		$title_tipe_invoice = sprintf('Kota : %s', $val_detail[0]['City']['name']);
	}

$tbl = <<<EOD
	<h2 class="grid_8" style="text-align: center;">Laporan $date_title</h2>
	<table border="1" width="100%">
		<tr>
			<td>No. Invoice</td>
			<td>$no_invoice</td>
		</tr>
		<tr>
			<td>Customer</td>
			<td>$Customer</td>
		</tr>
		<tr>
			<td>Periode</td>
			<td>$Periode</td>
		</tr>
		<tr>
			<td>Masa Berlaku Invoice</td>
			<td>$masa_berlaku</td>
		</tr>
	</table>
	<table cellpadding="2" cellspacing="2" nobr="true" style="$table">
		<thead>
			<tr style="$table_tr_head">
				<th colspan="5" align="left">$title_tipe_invoice</th>
			</tr>
			<tr style="$table_tr_head">
				<th>No</th>
				<th>Nama Tipe Motor</th>
				<th>Qty</th>
				<th>Harga</th>
				<th>No. Ref</th>
			</tr>
		</thead>
		<tbody>
		$content
		</tbody>
	</table>
EOD;
// echo $tbl;
// die();

// output the HTML content
$tcpdf->writeHTML($tbl, true, false, false, false, '');

$tcpdf->SetTextColor(0, 0, 0);
$tcpdf->SetFont($textfont,'B',10);

$path = $this->Common->pathDirTcpdf();
$filename = 'Laporan_'.$date_title.'.pdf';
$tcpdf->Output($path.'/'.$filename, 'F'); 

header('Content-type: application/pdf');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($path.'/'.$filename);
}else{
	echo $this->Html->tag('span', 'RJTM Invoice', array('class' => 'header-invoice'));
?>
<div class="action-print pull-right">
	<?php
		echo $this->Html->link('<i class="fa fa-print"></i> print', 'javascript:', array(
			'class' => 'btn btn-primary hidden-print print-window',
			'escape' => false
		));

		echo $this->Html->link('<i class="fa fa-download"></i> download PDF', $this->here.'/pdf', array(
			'class' => 'btn btn-danger hidden-print',
			'escape' => false
		));
	?>
</div>
<div class="invoice-print">
	<table border="1" width="100%">
		<tr>
			<td><?php echo __('No. Invoice');?></td>
			<td><?php echo $invoice['Invoice']['no_invoice'];?></td>
		</tr>
		<tr>
			<td><?php echo __('Customer');?></td>
			<td><?php echo $invoice['Customer']['name'];?></td>
		</tr>
		<tr>
			<td><?php echo __('Periode');?></td>
			<td><?php echo $invoice['Invoice']['period_from'].' sampai '.$invoice['Invoice']['period_to'];?></td>
		</tr>
		<tr>
			<td><?php echo __('Masa Berlaku Invoice');?></td>
			<td><?php echo $invoice['Invoice']['due_invoice'];?></td>
		</tr>
	</table>
	<?php
		if(!empty($revenue_detail)){
			foreach ($revenue_detail as $key => $val_detail) {
	?>
	<table border="1" width="100%">
		<thead>
			<tr>
				<th colspan="5" align="left">
					<?php 
						if($action == 'tarif'){
							printf('Tarif Angkutan : %s', $this->Number->currency($val_detail[0]['RevenueDetail']['price_unit'], Configure::read('__Site.config_currency_code'), array('places' => 0)) );
						}else{
							printf('Kota : %s', $val_detail[0]['City']['name']);
						}
					?>
				</th>
			</tr>
			<tr>
				<th><?php echo __('No.');?></th>
				<th><?php echo __('Nama Tipe Motor.');?></th>
				<th><?php echo __('qty.');?></th>
				<th><?php echo __('Harga.');?></th>
				<th><?php echo __('No. Ref');?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				if(!empty($val_detail)){
					$no=1;
					$total = 0;
					foreach ($val_detail as $key => $value) {
						$colom = $this->Html->tag('td', $no++);
						$colom .= $this->Html->tag('td', $value['TipeMotor']['name']);
						$colom .= $this->Html->tag('td', $value['RevenueDetail']['qty_unit']);
						$colom .= $this->Html->tag('td', $this->Number->currency($value['RevenueDetail']['price_unit'], Configure::read('__Site.config_currency_code'), array('places' => 0)));
						$colom .= $this->Html->tag('td', $value['RevenueDetail']['no_reference']);

						if(!empty($value['RevenueDetail']['payment_type']) && $value['RevenueDetail']['payment_type'] == 'per_truck'){
							$total += $value['RevenueDetail']['price_unit'];
						}else{
							$total += $value['RevenueDetail']['price_unit'] * $value['RevenueDetail']['qty_unit'];
						}
						echo $this->Html->tag('tr', $colom);
					}
					$colom = $this->Html->tag('td', __('Total '), array(
						'colspan' => 3,
						'align' => 'right'
					));
					$colom .= $this->Html->tag('td', $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0)), array('colspan' => 2) );
					echo $this->Html->tag('tr', $colom);
				}else{
					$colom = $this->Html->tag('td', __('Data tidak ditemukan.'), array(
						'colspan' => 5
					));

					echo $this->Html->tag('tr', $colom);
				}
			?>
		</tbody>
	</table>
	<?php
			}
		}
	?>
</div>
<?php
	}
?>