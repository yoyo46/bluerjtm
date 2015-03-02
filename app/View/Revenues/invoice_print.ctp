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
    $table_tr_head = 'background-color: #ccc; border-right: 1px solid #FFFFFF; color: #333; font-weight: bold; padding: 0 10px; text-align: center;';
    $table_th = 'padding-top: 3px';
    $table = 'width:100%;font-size: 24px; border: 1px solid #CCC; border-collapse: collapse; padding: 0; margin: 15px 0 0 0;';

    $no = 1;
    $each_loop_message = '';

    $content = '';
    if(!empty($revenue_detail)){
		foreach ($revenue_detail as $key => $val_detail) {
			if(!empty($val_detail)){
				$no=1;
				$grandTotal = 0;
				$grandTotalUnit = 0;
				foreach ($val_detail as $key => $value) {
					$grandTotalUnit += $qty = $value['RevenueDetail']['qty_unit'];
					$price = $value['RevenueDetail']['price_unit'];
					$total = $qty * $price;
					$grandTotal += $total; 

					$colom = $this->Html->tag('td', $no++);
					$colom .= $this->Html->tag('td', $value['Ttuj']['nopol']);
					$colom .= $this->Html->tag('td', $value['RevenueDetail']['no_do']);
					$colom .= $this->Html->tag('td', $this->Common->customDate($value['Revenue']['date_revenue'], 'd/m/Y'));
					$colom .= $this->Html->tag('td', $qty, array(
						'align' => 'center'
					));
					$colom .= $this->Html->tag('td', $this->Number->currency($price, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
						'align' => 'right'
					));
					$colom .= $this->Html->tag('td', $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
						'align' => 'right'
					));
					$colom .= $this->Html->tag('td', $value['RevenueDetail']['no_reference']);

					if(!empty($value['RevenueDetail']['payment_type']) && $value['RevenueDetail']['payment_type'] == 'per_truck'){
						$total += $value['RevenueDetail']['price_unit'];
					}else{
						$total += $value['RevenueDetail']['price_unit'] * $value['RevenueDetail']['qty_unit'];
					}
					$content .= $this->Html->tag('tr', $colom);
				}
				$colom = $this->Html->tag('td', __('Total '), array(
					'colspan' => 4,
					'align' => 'right'
				));
				$colom .= $this->Html->tag('td', $this->Number->format($grandTotalUnit), array(
					'align' => 'center'
				));
				$colom .= $this->Html->tag('td', '&nbsp;');
				$colom .= $this->Html->tag('td', $this->Number->currency($grandTotal, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
					'align' => 'right'
				));
				$colom .= $this->Html->tag('td', '&nbsp;');
				$content .=  $this->Html->tag('tr', $colom);
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
		$title_tipe_invoice = $val_detail[0]['City']['name'];
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
				<th colspan="8" align="center">$title_tipe_invoice</th>
			</tr>
			<tr style="$table_tr_head">
				<th align="center">No.</th>
				<th align="center">No. Truk</th>
				<th align="center">No.DO/Shipping List</th>
				<th align="center">Tanggal</th>
				<th align="center">Total Unit</th>
				<th align="center">Harga</th>
				<th align="center">Total</th>
				<th align="center">No. Ref</th>
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
		<thead class="header-invoice-print">
			<tr>
				<th colspan="8" class="text-center" style="text-transform:uppercase;">
					<?php 
						if($action == 'tarif'){
							printf('Tarif Angkutan : %s', $this->Number->currency($val_detail[0]['RevenueDetail']['price_unit'], Configure::read('__Site.config_currency_code'), array('places' => 0)) );
						}else{
							echo $val_detail[0]['City']['name'];
						}
					?>
				</th>
			</tr>
			<tr>
				<th class="text-center"><?php echo __('No.');?></th>
				<th class="text-center"><?php echo __('No. Truk.');?></th>
				<th class="text-center"><?php echo __('No.DO/Shipping List.');?></th>
				<th class="text-center"><?php echo __('Tanggal');?></th>
				<th class="text-center"><?php echo __('Total Unit');?></th>
				<th class="text-center"><?php echo __('Harga');?></th>
				<th class="text-center"><?php echo __('Total');?></th>
				<th class="text-center"><?php echo __('No. Ref');?></th>
			</tr>
		</thead>
		<tbody>
			<?php
				if(!empty($val_detail)){
					$no=1;
					$grandTotal = 0;
					$grandTotalUnit = 0;
					foreach ($val_detail as $key => $value) {
						$grandTotalUnit += $qty = $value['RevenueDetail']['qty_unit'];
						$price = $value['RevenueDetail']['price_unit'];
						$total = $qty * $price;
						$grandTotal += $total; 

						$colom = $this->Html->tag('td', $no++);
						$colom .= $this->Html->tag('td', !empty($value['Ttuj']['nopol'])?$value['Ttuj']['nopol']:false);
						$colom .= $this->Html->tag('td', $value['RevenueDetail']['no_do']);
						$colom .= $this->Html->tag('td', $this->Common->customDate($value['Revenue']['date_revenue'], 'd/m/Y'));
						$colom .= $this->Html->tag('td', $qty, array(
							'align' => 'center'
						));
						$colom .= $this->Html->tag('td', $this->Number->currency($price, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
							'align' => 'right'
						));
						$colom .= $this->Html->tag('td', $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
							'align' => 'right'
						));
						$colom .= $this->Html->tag('td', $value['RevenueDetail']['no_reference']);

						if(!empty($value['RevenueDetail']['payment_type']) && $value['RevenueDetail']['payment_type'] == 'per_truck'){
							$total += $value['RevenueDetail']['price_unit'];
						}else{
							$total += $value['RevenueDetail']['price_unit'] * $value['RevenueDetail']['qty_unit'];
						}
						echo $this->Html->tag('tr', $colom);
					}
					$colom = $this->Html->tag('td', __('Total '), array(
						'colspan' => 4,
						'align' => 'right'
					));
					$colom .= $this->Html->tag('td', $this->Number->format($grandTotalUnit), array(
						'class' => 'text-center'
					));
					$colom .= $this->Html->tag('td', '&nbsp;');
					$colom .= $this->Html->tag('td', $this->Number->currency($grandTotal, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
						'align' => 'right'
					));
					$colom .= $this->Html->tag('td', '&nbsp;');
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
	<?php
			}
		}
	?>
</div>
<?php
	}
?>