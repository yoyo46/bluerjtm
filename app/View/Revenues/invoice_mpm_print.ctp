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
		$totalAll = 0;
		$totalAllUnit = 0;
		$idx = 1;

		foreach ($revenue_detail as $key => $val_detail) {
			$data_print = !empty($data_print)?$data_print:'invoice';
			
			if($action == 'tarif' && $data_print == 'invoice'){
				$cityName = sprintf('Tarif Angkutan : %s', $this->Number->currency($val_detail[0]['RevenueDetail']['price_unit'], false, array('places' => 0)) );
			}else{
            	if( $val_detail[0]['Revenue']['revenue_tarif_type'] == 'per_truck' && !empty($val_detail[0]['Revenue']['no_doc']) ) {
					$cityName = $val_detail[0]['Revenue']['no_doc'];
            	} else {
					$cityName = $val_detail[0]['City']['name'];
				}
			}

			$totalMerge = 10;
			$totalMergeTotal = 6;

			$content .= '<table border="1" width="100%" style="padding: 5px; font-size: 25px;">
				<thead class="header-invoice-print">
					<tr>
						<th colspan="'.$totalMerge.'" style="text-transform:uppercase;text-align: center;">'.$cityName.'</th>
					</tr>
					<tr>
						<th class="text-center">No.</th>
						<th class="text-center">No. Truk</th>
						<th class="text-center">No.DO</th>
						<th class="text-center">No.Shipping List</th>
						<th class="text-center">Nama Dealer</th>
						<th class="text-center">Tanggal</th>
						<th class="text-center">Total Unit</th>
						<th class="text-center">Harga</th>
						<th class="text-center">Total</th>
						<th class="text-center">No. Ref</th>
					</tr>
				</thead>
				<tbody>
			';
			if(!empty($val_detail)){
				$no=1;
				$grandTotal = 0;
				$grandTotalUnit = 0;
				$trData = '';
				$totalFlag = true;
				$old_revenue_id = false;
				$recenueCnt = array();

				foreach ($val_detail as $key => $value) {
					$is_charge = $this->Common->filterEmptyField($value, 'RevenueDetail', 'is_charge');
					$detail_id = $this->Common->filterEmptyField($value, 'RevenueDetail', 'id');

					if( empty($is_charge) ) {
						if( !empty($recenueCnt[$old_detail_id]) ) {
							$recenueCnt[$old_detail_id]++;
						} else {
							$recenueCnt[$old_detail_id] = 1;
						}
					} else {
						$old_detail_id = $detail_id;
					}						
				}

				foreach ($val_detail as $key => $value) {
    				$revenue_id = $this->Common->filterEmptyField($value, 'Revenue', 'id');
    				$date_revenue = $this->Common->filterEmptyField($value, 'Revenue', 'date_revenue');
					$revenue_jenis_tarif = $this->Common->filterEmptyField($value, 'Revenue', 'revenue_tarif_type');

					$detail_id = $this->Common->filterEmptyField($value, 'RevenueDetail', 'id');
    				$total_price_unit = $this->Common->filterEmptyField($value, 'RevenueDetail', 'total_price_unit');
    				$price_unit = $this->Common->filterEmptyField($value, 'RevenueDetail', 'price_unit');

    				$no_do = $this->Common->filterEmptyField($value, 'RevenueDetail', 'no_do');
    				$sj = $this->Common->filterEmptyField($value, 'RevenueDetail', 'no_sj');
    				$is_charge = $this->Common->filterEmptyField($value, 'RevenueDetail', 'is_charge');

    				if( !empty($sj) ) {
        				$no_sj = substr($sj, 0, 28);
        				$dealer = substr($sj, 29, strlen($sj));
        			} else {
						$no_sj = false;
        				$dealer = false;
        			}

					$nopol = !empty($value['Revenue']['Ttuj']['nopol'])?$value['Revenue']['Ttuj']['nopol']:false;
					$grandTotalUnit += $qty = $this->Common->filterEmptyField($value, 'RevenueDetail', 'qty_unit');
					$payment_type = $this->Common->filterEmptyField($value, 'RevenueDetail', 'payment_type');

					$price = 0;
					$total = 0;
					$totalPriceFormat = '';
					$date_revenue = $this->Common->formatDate($date_revenue, 'd/m/Y');

					if( !empty($is_charge) ) {
						$totalPriceFormat = $this->Common->getFormatPrice($total_price_unit);
						$priceFormat = $this->Common->getFormatPrice($price_unit, 0);
					} else {
						$total_price_unit = 0;
						$priceFormat = '';
					}

					$colom = $this->Html->tag('td', $no++);

					if( !empty($data_print) && $data_print == 'date' ) {
						$city_name = !empty($value['City']['name'])?$value['City']['name']:false;
						$colom .= $this->Html->tag('td', $value['City']['name']);
					}

					$colom .= $this->Html->tag('td', $nopol);
					$colom .= $this->Html->tag('td', $no_do);
					$colom .= $this->Html->tag('td', $no_sj);
					$colom .= $this->Html->tag('td', $dealer);

					$colom .= $this->Html->tag('td', $date_revenue);
					$colom .= $this->Html->tag('td', $qty, array(
						'style' => 'text-align:center;',
					));
					$colom .= $this->Html->tag('td', $priceFormat, array(
						'style' => 'text-align:right;',
					));

					if( $revenue_jenis_tarif == 'per_truck' ){
						if( !empty($recenueCnt[$detail_id]) ) {
							$colom .= $this->Html->tag('td', $totalPriceFormat, array(
								'align' => 'right',
								'rowspan' => $recenueCnt[$detail_id] + 1,
							));
						} else if( !empty($is_charge) ) {
							$colom .= $this->Html->tag('td', $totalPriceFormat, array(
								'align' => 'right'
							));
						}
					} else {
						$colom .= $this->Html->tag('td', $totalPriceFormat, array(
							'align' => 'right'
						));
					}

					$colom .= $this->Html->tag('td', $this->Common->getNoRef($value['Revenue']['id']));
					$trData .= $this->Html->tag('tr', $colom);
					$grandTotal += $total_price_unit;
					$old_revenue_id = $revenue_id;
				}

				$totalAll += $grandTotal;
				$totalAllUnit += $grandTotalUnit;

				$content .= $trData;
				$colom = $this->Html->tag('td', __('Total '), array(
					'colspan' => $totalMergeTotal,
					'style' => 'font-weight: bold;text-align:right;',
				));
				$colom .= $this->Html->tag('td', $this->Number->format($grandTotalUnit), array(
					'style' => 'text-align:center;',
				));
				$colom .= $this->Html->tag('td', '&nbsp;', array(
					'style' => 'text-align:right;',
				));
				$colom .= $this->Html->tag('td', $this->Number->currency($grandTotal, false, array('places' => 0)), array(
					'style' => 'text-align:right;',
				));
				$colom .= $this->Html->tag('td', '&nbsp;');
				$content .=  $this->Html->tag('tr', $colom);

				$ppn = !empty($totalPPN[0]['ppn'])?$totalPPN[0]['ppn']:0;

				if( !empty($ppn) ) {
					$colom = $this->Html->tag('td', '&nbsp;', array(
						'colspan' => $totalMergeTotal+1,
					));
					$colom .= $this->Html->tag('td', __('PPN '), array(
						'style' => 'font-weight: bold;text-align:right;',
					));
					$colom .= $this->Html->tag('td', $this->Number->currency($ppn, false, array('places' => 0)), array(
						'style' => 'font-weight: bold;text-align:right;',
					));
					$colom .= $this->Html->tag('td', '&nbsp;');

					$content .= $this->Html->tag('tr', $colom, array(
						'class' => 'total-row'
					));
				}

				if( !empty($ppn) ) {
					$grandTotalInvoice = $grandTotal + $ppn;
					$totalAll += $ppn;

					$colom = $this->Html->tag('td', '&nbsp;', array(
						'colspan' => $totalMergeTotal+1,
					));
					$colom .= $this->Html->tag('td', __('GrandTotal '), array(
						'align' => 'right',
						'style' => 'font-weight: bold;',
					));
					$colom .= $this->Html->tag('td', $this->Number->currency($grandTotalInvoice, false, array('places' => 0)), array(
						'align' => 'right',
						'style' => 'font-weight: bold;',
					));
					$colom .= $this->Html->tag('td', '&nbsp;');

					$content .= $this->Html->tag('tr', $colom, array(
						'class' => 'total-row'
					));
				}
			}else{
				$colom = $this->Html->tag('td', __('Data tidak ditemukan.'), array(
					'colspan' => $totalMerge
				));

				$content .= $this->Html->tag('tr', $colom);
			}

			$content .= '
				</tbody>
			';

			if( $idx == count($revenue_detail) ) {
				$colom = $this->Html->tag('td', __('GrandTotal '), array(
					'style' => 'font-weight: bold;text-align:right;',
					'colspan' => $totalMergeTotal,
				));
				$colom .= $this->Html->tag('td', $this->Number->format($totalAllUnit), array(
					'style' => 'text-align:center;',
				));
				$colom .= $this->Html->tag('td', '&nbsp;', array(
					'style' => 'text-align:right;',
				));
				$colom .= $this->Html->tag('td', $this->Number->currency($totalAll, false, array('places' => 0)), array(
					'style' => 'text-align:right;',
				));
				$colom .= $this->Html->tag('td', '&nbsp;');
				$content .=  $this->Html->tag('tfoot', $this->Html->tag('tr', $colom));
			}

			$content .= '</table>';

			$idx++;
		}
	}
	
    $date_title = $this->Common->toSlug($sub_module_title);
    $no_invoice = $invoice['Invoice']['no_invoice'];
    $Customer = $invoice['Customer']['name'];
    $Periode = $invoice['Invoice']['period_from'].' sampai '.$invoice['Invoice']['period_to'];
    $masa_berlaku = $invoice['Invoice']['term_of_payment'];
    // $masa_berlaku = $invoice['Invoice']['due_invoice'];

    $title_tipe_invoice = '';
    if($action == 'tarif'){
		$title_tipe_invoice = sprintf('Tarif Angkutan : %s', $this->Number->currency($val_detail[0]['RevenueDetail']['price_unit'], false, array('places' => 0)) );
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
	            echo $this->element(sprintf('blocks/common/tables/export_%s', $action_print), array(
	                'tableContent' => $this->element('blocks/revenues/preview_invoice_mpm'),
	                'sub_module_title' => $module_title,
	            ));
			} else {
        		$this->Html->addCrumb(__('Invoice'), array(
        			'controller' => 'revenues',
        			'action' => 'invoices'
    			));
        		$this->Html->addCrumb($sub_module_title);

				echo $this->Html->tag('span', 'RJTM Invoice', array('class' => 'header-invoice'));

	            if( empty($action_print) ) {
					$print = $this->Html->link('<i class="fa fa-print"></i> print', 'javascript:', array(
						'class' => 'btn btn-default hidden-print print-window pull-right',
						'escape' => false
					));
		            $print .= $this->Common->_getPrint();

		            echo $this->Html->tag('div', $print, array(
		            	'class' => 'action-print',
		        	));
				}

	            echo $this->Html->tag('div', $this->element('blocks/revenues/preview_invoice_mpm'), array(
	            	'class' => 'invoice-print',
	        	));
			}
		}

        if( empty($preview) ) {
?>
<div class="box-footer text-center action hidden-print">
	<?php
    		echo $this->Html->link(__('Kembali'), array(
                'controller' => 'revenues', 
				'action' => 'invoices', 
			), array(
				'class'=> 'btn btn-default',
			));
	?>
</div>
<?php 
		}
?>