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
			$data_print = !empty($data_print)?$data_print:'invoice';
			
			if($action == 'tarif' && $data_print == 'invoice'){
				$cityName = sprintf('Tarif Angkutan : %s', $this->Number->currency($val_detail[0]['RevenueDetail']['price_unit'], Configure::read('__Site.config_currency_code'), array('places' => 0)) );
			}else{
                if( $data_print == 'date' && !empty($val_detail[0]['Invoice']['invoice_date']) ) {
					$cityName = $this->Common->customDate($val_detail[0]['Invoice']['invoice_date'], 'd/m/Y');
                } else {
					$cityName = $val_detail[0]['City']['name'];
                }
			}

			if( !empty($data_print) && $data_print == 'date' ) {
				$totalMerge = 10;
				$totalMergeTotal = 6;
				$colName = '<th class="text-center">Kota</th>';
			} else {
				$totalMerge = 9;
				$totalMergeTotal = 5;
				$colName = __('Kota');
			}

			$content .= '<table border="1" width="100%" style="padding: 5px; font-size: 25px;">
				<thead class="header-invoice-print">
					<tr>
						<th colspan="'.$totalMerge.'" class="text-center" style="text-transform:uppercase;">'.$cityName.'</th>
					</tr>
					<tr>
						<th class="text-center">No.</th>
						'.$colName.'
						<th class="text-center">No. Truk</th>
						<th class="text-center">No.DO</th>
						<th class="text-center">No.SJ</th>
						<th class="text-center">Tanggal</th>
						<th class="text-center">Total Unit</th>
						<th class="text-center">Harga</th>
						<th class="text-center">Total</th>
						<th class="text-center">No. Ref</th>
					</tr>
				</thead>
			<tbody>';
			if(!empty($val_detail)){
				$no=1;
				$grandTotal = 0;
				$grandTotalUnit = 0;
				$rowSpan = 0;
				$trData = '';
				$totalFlag = true;

				foreach ($val_detail as $key => $value) {
					$nopol = !empty($value['Revenue']['Ttuj']['nopol'])?$value['Revenue']['Ttuj']['nopol']:false;
					$grandTotalUnit += $qty = $value['RevenueDetail']['qty_unit'];
					$price = $value['RevenueDetail']['price_unit'];
					$total = 0;
					$date_revenue = '-';

					$colom = $this->Html->tag('td', $no++);

					if( !empty($data_print) && $data_print == 'date' ) {
						$city_name = !empty($value['City']['name'])?$value['City']['name']:false;
						$colom .= $this->Html->tag('td', $value['City']['name']);
					}

					$colom .= $this->Html->tag('td', $nopol);
					$colom .= $this->Html->tag('td', $value['RevenueDetail']['no_do']);
					$colom .= $this->Html->tag('td', $value['RevenueDetail']['no_sj']);

					if(!empty($value['Revenue']['date_revenue'])){
						$date_revenue = $this->Common->customDate($value['Revenue']['date_revenue'], 'd/m/Y');
					}
					$colom .= $this->Html->tag('td', $date_revenue);
					$colom .= $this->Html->tag('td', $qty, array(
						'align' => 'center'
					));
					$colom .= $this->Html->tag('td', $this->Number->currency($price, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
						'align' => 'right'
					));

					if(!empty($value['RevenueDetail']['payment_type']) && $value['RevenueDetail']['payment_type'] == 'per_truck'){
						if( !empty($value['RevenueDetail']['total_price_unit']) ) {
							$total = $value['RevenueDetail']['total_price_unit'];

							$colom .= $this->Html->tag('td', $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
								'align' => 'right'
							));
						} else {
							if( empty($rowSpan) ) {
								$total = !empty($value['Revenue']['tarif_per_truck'])?$value['Revenue']['tarif_per_truck']:0;
								$colom .= $this->Html->tag('td', $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
									'align' => 'right',
									'data-rowspan' => 'data-value'
								));
							}

							$rowSpan++;
						}
					}else{
						$total = $price * $qty;

						$colom .= $this->Html->tag('td', $this->Number->currency($total, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
							'align' => 'right'
						));
					}

					$colom .= $this->Html->tag('td', $value['RevenueDetail']['no_reference']);
					$trData .= $this->Html->tag('tr', $colom);
					$grandTotal += $total;
				}

				if( !empty($rowSpan) ) {
					$trData = str_replace(array( 'data-rowspan', 'data-value' ), array( 'rowspan', $rowSpan ), $trData);
				}
				$content .= $trData;
				$colom = $this->Html->tag('td', __('Total '), array(
					'colspan' => $totalMergeTotal,
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
					'colspan' => $totalMerge
				));

				$content .= $this->Html->tag('tr', $colom);
			}

			$content .= '
				</tbody>
			</table>';
		}
	}
	
    $date_title = $sub_module_title;
    $no_invoice = $invoice['Invoice']['no_invoice'];
    $Customer = $invoice['Customer']['name'];
    $Periode = $invoice['Invoice']['period_from'].' sampai '.$invoice['Invoice']['period_to'];
    $masa_berlaku = $invoice['Invoice']['term_of_payment'];
    // $masa_berlaku = $invoice['Invoice']['due_invoice'];

    $title_tipe_invoice = '';
    if($action == 'tarif'){
		$title_tipe_invoice = sprintf('Tarif Angkutan : %s', $this->Number->currency($val_detail[0]['RevenueDetail']['price_unit'], Configure::read('__Site.config_currency_code'), array('places' => 0)) );
	}else{
		$title_tipe_invoice = $val_detail[0]['City']['name'];
	}

$tbl = <<<EOD
	<h2 class="grid_8" style="text-align: center;">Laporan $date_title</h2>
	$content
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
            $addStyle = '';
            $tdStyle = '';
            $border = 0;

            if( $action_print == 'excel' ) {
                header('Content-type: application/ms-excel');
                header('Content-Disposition: attachment; filename='.$sub_module_title.'.xls');
                $border = 1;
                $tdStyle = 'text-align: center;';
            }

			echo $this->Html->tag('span', 'RJTM Invoice', array('class' => 'header-invoice'));

            if( empty($action_print) ) {
?>
<div class="action-print pull-right">
	<?php
			echo $this->Html->link('<i class="fa fa-print"></i> print', 'javascript:', array(
				'class' => 'btn btn-primary hidden-print print-window',
				'escape' => false
			));

			echo $this->Html->link('<i class="fa fa-download"></i> download Excel', $this->here.'/excel', array(
				'class' => 'btn btn-success hidden-print',
				'escape' => false
			));

			echo $this->Html->link('<i class="fa fa-download"></i> download PDF', $this->here.'/pdf', array(
				'class' => 'btn btn-danger hidden-print',
				'escape' => false
			));
	?>
</div>
<?php 
		}
?>
<div class="invoice-print">
	<?php
	/*
	<table border="1" width="100%">
		<tr>
			<th><?php echo __('No. Invoice');?></th>
			<td><?php echo $invoice['Invoice']['no_invoice'];?></td>
		</tr>
		<tr>
			<th><?php echo __('Tgl Invoice');?></th>
			<td><?php echo $this->Common->customDate($invoice['Invoice']['invoice_date'], 'd/m/Y');?></td>
		</tr>
		<tr>
			<th><?php echo __('Customer');?></th>
			<td><?php echo !empty($invoice['Customer']['customer_name'])?$invoice['Customer']['customer_name']:'-';?></td>
		</tr>
		<tr>
			<th><?php echo __('Periode');?></th>
			<td><?php printf('%s s/d %s', $this->Common->customDate($invoice['Invoice']['period_from'], 'd/m/Y'), $this->Common->customDate($invoice['Invoice']['period_to'], 'd/m/Y'));?></td>
		</tr>
		<tr>
			<th><?php echo __('Term Of Payment');?></th>
			<td><?php printf(__('%s Hari'), $invoice['Customer']['term_of_payment']);?></td>
			<!-- <td><?php // printf(__('%s Hari'), $invoice['Invoice']['due_invoice']);?></td> -->
		</tr>
	</table>
	*/
            echo $this->element('blocks/revenues/preview_invoice');
	?>
</div>
<?php
	}
?>