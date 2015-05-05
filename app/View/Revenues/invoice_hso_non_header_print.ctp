<?php

		if( !empty($data_print) && $data_print == 'date' ) {
			$totalMerge = 10;
			$totalMergeTotal = 6;
			$colName = '<th class="text-center">Kota</th>';
		} else {
			$totalMerge = 9;
			$totalMergeTotal = 5;
			$colName = '';
		}

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
				$cityName = sprintf('Tarif Angkutan : %s', $this->Number->currency($val_detail[0]['RevenueDetail']['price_unit'], Configure::read('__Site.config_currency_second_code'), array('places' => 0)) );
			}else{
                if( $data_print == 'date' && !empty($val_detail[0]['Invoice']['invoice_date']) ) {
					$cityName = $this->Common->customDate($val_detail[0]['Invoice']['invoice_date'], 'd/m/Y');
                } else {
					$cityName = $val_detail[0]['City']['name'];
                }
			}

			$content .= '<table border="1" width="100%" style="padding: 5px; font-size: 25px;">
				<thead class="header-invoice-print">
					<tr>
						<th colspan="'.$totalMerge.'" style="text-transform:uppercase;text-align: center;">'.$cityName.'</th>
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
					// $colom .= $this->Html->tag('td', !empty($value['RevenueDetail']['note'])?$value['RevenueDetail']['note']:'');

					if(!empty($value['Revenue']['date_revenue'])){
						$date_revenue = $this->Common->customDate($value['Revenue']['date_revenue'], 'd/m/Y');
					}
					$colom .= $this->Html->tag('td', $date_revenue);
					$colom .= $this->Html->tag('td', $qty, array(
						'align' => 'center'
					));
					$colom .= $this->Html->tag('td', $this->Number->currency($price, '', array('places' => 0)), array(
						'align' => 'right'
					));

					if(!empty($value['RevenueDetail']['payment_type']) && $value['RevenueDetail']['payment_type'] == 'per_truck'){
						if( !empty($value['RevenueDetail']['total_price_unit']) ) {
							$total = $value['RevenueDetail']['total_price_unit'];

							$colom .= $this->Html->tag('td', $this->Number->currency($total, '', array('places' => 0)), array(
								'align' => 'right'
							));
						} else {
							if( empty($rowSpan) ) {
								$total = !empty($value['Revenue']['tarif_per_truck'])?$value['Revenue']['tarif_per_truck']:0;
								$colom .= $this->Html->tag('td', $this->Number->currency($total, '', array('places' => 0)), array(
									'align' => 'right',
									'data-rowspan' => 'data-value'
								));
							}

							$rowSpan++;
						}
					}else{
						$total = $price * $qty;

						$colom .= $this->Html->tag('td', $this->Number->currency($total, '', array('places' => 0)), array(
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
				$colom .= $this->Html->tag('td', $this->Number->currency($grandTotal, Configure::read('__Site.config_currency_second_code'), array('places' => 0)), array(
					'align' => 'right',
					'colspan' => 2,
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
		$title_tipe_invoice = sprintf('Tarif Angkutan : %s', $this->Number->currency($val_detail[0]['RevenueDetail']['price_unit'], Configure::read('__Site.config_currency_second_code'), array('places' => 0)) );
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
            } else {
        		$this->Html->addCrumb(__('Invoice'), array(
        			'controller' => 'revenues',
        			'action' => 'invoices'
    			));
        		$this->Html->addCrumb($sub_module_title);

				echo $this->Html->tag('h2', __('PERINCIAN PENGIRIMAN UNIT'), array(
					'class' => 'page-header'
				));
            }

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
<div class="clear"></div>
<div class="invoice-print">
	<?php
			if(!empty($invoice)){
	?>
	<table border="1" width="100%" style="margin-top: 20px;">
		<thead class="header-invoice-print">
			<tr>
				<th class="text-center"><?php echo __('No. BPH');?></th>
				<th class="text-center"><?php echo __('Tahun Kirim');?></th>
				<th class="text-center"><?php echo __('Hrg/ Unit');?></th>
				<th class="text-center"><?php echo __('No. SL');?></th>
				<th class="text-center"><?php echo __('Redaksional');?></th>
				<th class="text-center"><?php echo __('Region');?></th>
				<th class="text-center"><?php echo __('Qty');?></th>
				<th class="text-center"><?php echo __('Route');?></th>
				<th class="text-center"><?php echo __('Text');?></th>
			</tr>
		</thead>
		<tbody>
			<?php
					if(!empty($revenue_detail)){
						$no = 1;

						foreach ($revenue_detail as $key => $revenue) {
							if( !empty($revenue) ) {
								foreach ($revenue as $key => $valueDetail) {
									$no_sj = !empty($valueDetail['RevenueDetail']['no_sj'])?$valueDetail['RevenueDetail']['no_sj']:'-';
									$price_unit = !empty($valueDetail['RevenueDetail']['price_unit'])?$valueDetail['RevenueDetail']['price_unit']:0;
									$qty_unit = !empty($valueDetail['RevenueDetail']['qty_unit'])?$valueDetail['RevenueDetail']['qty_unit']:0;
									$year = !empty($valueDetail['Revenue']['date_revenue'])?$this->Common->customDate($valueDetail['Revenue']['date_revenue'], 'Y'):'-';
									$month = !empty($valueDetail['Revenue']['date_revenue'])?strtoupper($this->Common->customDate($valueDetail['Revenue']['date_revenue'], 'M')):'-';
									$fromCity = !empty($valueDetail['FromCity']['code'])?$valueDetail['FromCity']['code']:false;
									$toCity = !empty($valueDetail['City']['code'])?$valueDetail['City']['code']:false;
									$toCityName = !empty($valueDetail['City']['name'])?$valueDetail['City']['name']:false;
									$initial = !empty($valueDetail['City']['initial'])?$valueDetail['City']['initial']:false;
									$text = sprintf('%s - %s %s %s', $fromCity, $toCity, $month, $year);
									$redaksional = sprintf('OKS ANGK SMH %s', $text);
									$tujuan = sprintf('%s - %s', $fromCity, $toCityName);
			?>
			<tr>
				<?php 
						echo $this->Html->tag('td', '');
						echo $this->Html->tag('td', $year, array(
							'style' => 'text-align: center;'
						));

						if( !empty($price_unit) ) {
							echo $this->Html->tag('td', $this->Number->format($price_unit, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
								'style' => 'text-align: right;'
							));
						} else {
							echo $this->Html->tag('td', 0, array(
								'style' => 'text-align: right;'
							));
						}

						echo $this->Html->tag('td', $no_sj);
						echo $this->Html->tag('td', $redaksional);
						echo $this->Html->tag('td', $initial, array(
							'style' => 'text-align: center;'
						));
						echo $this->Html->tag('td', $qty_unit, array(
							'style' => 'text-align: center;'
						));
						echo $this->Html->tag('td', $tujuan);
						echo $this->Html->tag('td', $text);
				?>
			</tr>
			<?php
									$no++;
								}
							}
						}
					}else{
						$colom = $this->Html->tag('td', __('Data tidak ditemukan.'), array(
							'colspan' => $totalMerge,
						));

						echo $this->Html->tag('tr', $colom);
					}
			?>
		</tbody>
	</table>
	<?php
			} else {
	            echo $this->Html->tag('p', $this->Html->tag('td', __('Data belum tersedia.'), array(
	                'class' => 'alert alert-warning text-center',
	            )));
	        }
	?>
</div>
<?php
	}
?>