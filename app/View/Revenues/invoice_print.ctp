<?php
		$data_print = !empty($data_print)?$data_print:'invoice';

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
			$cityName = false;

			if( !in_array($data_print, array( 'date', 'hso-smg', 'sa' )) ) {
				if($action == 'tarif' && $data_print == 'invoice'){
					$cityName = sprintf('Tarif Angkutan : %s', $this->Number->currency($val_detail[0]['RevenueDetail']['price_unit'], Configure::read('__Site.config_currency_second_code'), array('places' => 0)) );
				}else{
	                if( in_array($data_print, array( 'date', 'sa' )) && !empty($val_detail[0]['Revenue']['date_revenue']) ) {
						$cityName = $this->Common->customDate($val_detail[0]['Revenue']['date_revenue'], 'd/m/Y');
	                } else {
	                	if( $val_detail[0]['Revenue']['revenue_tarif_type'] == 'per_truck' && !empty($val_detail[0]['Revenue']['no_doc']) ) {
							$cityName = $val_detail[0]['Revenue']['no_doc'];
	                	} else {
							$cityName = $val_detail[0]['City']['name'];
						}
	                }
				}
			}

			$colName = '';
			$colNameCity = '';
			$colNameDo = '';
			$colNameSj = '';

			if( in_array($data_print, array( 'date', 'hso-smg', 'preview', 'sa' )) ) {
				$totalMerge = 10;
					
				if( $data_print == 'hso-smg' ) {
					$totalMergeTotal = 5;
				} else {
					$totalMergeTotal = 6;
				}

				if( in_array($data_print, array( 'date', 'sa' )) ) {
					$colName = '<th class="text-center">Kota</th>';
				} else if( in_array($data_print, array( 'hso-smg', 'preview' )) ) {
					$colNameCity = '<th class="text-center">Kota</th>';
				}
			} else {
				$totalMerge = 9;
				$totalMergeTotal = 5;
			}

			if( !empty($cityName) ) {
				$cityName = '<tr>
					<th colspan="'.$totalMerge.'" style="text-transform:uppercase;text-align: center;">'.$cityName.'</th>
				</tr>';
			}

			if( in_array($data_print, array( 'date', 'sa' )) ) {
				if( in_array($data_print, array( 'sa' )) ) {
					$colNameDo = __('No.Do / No.SJ');
				} else {
					$colNameDo = __('Keterangan');
				}
			} else {
				$colNameDo = __('No.DO');
			}

			if( in_array($data_print, array( 'sa' )) ) {
				$colNameSj = $this->Html->tag('th', __('No. SA'), array(
					'class' => 'text-center',
				));
			} else if( in_array($data_print, array( 'hso-smg', 'sa' )) ) {
				if( in_array($data_print, array( 'hso-smg' )) ) {
					$labelNameSj = __('Nama Dealer');
				} else {
					$labelNameSj = __('No. SJ');
				}

				$colNameSj = $this->Html->tag('th', $labelNameSj, array(
					'class' => 'text-center',
				));
			}

			$content .= '<table border="1" width="100%" style="padding: 5px; font-size: 25px;">
				<thead class="header-invoice-print">
					'.$cityName.'
					<tr>
						<th class="text-center">No.</th>
						'.$colName.'
						<th class="text-center">No. Truk</th>
						<th class="text-center">'.$colNameDo.'</th>
						'.$colNameSj.'
						<th class="text-center">Tanggal</th>
						'.$colNameCity.'
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
				$trData = '';
				$totalFlag = true;
				$old_revenue_id = false;
				$recenueCnt = array();

				foreach ($val_detail as $key => $value) {
					$revenue_id = !empty($value['Revenue']['id'])?$value['Revenue']['id']:false;

					if( !empty($recenueCnt[$revenue_id]) ) {
						$recenueCnt[$revenue_id]++;
					} else {
						$recenueCnt[$revenue_id] = 1;
					}
				}

				foreach ($val_detail as $key => $value) {
                	$nopol = $this->Common->filterEmptyField($value, 'Truck', 'nopol');
					$revenue_id = !empty($value['Revenue']['id'])?$value['Revenue']['id']:false;
					$nopol = !empty($value['Revenue']['Ttuj']['nopol'])?$value['Revenue']['Ttuj']['nopol']:$nopol;

					$grandTotalUnit += $qty = $value['RevenueDetail']['qty_unit'];
					$date_revenue = '-';
					$payment_type = !empty($value['RevenueDetail']['payment_type'])?$value['RevenueDetail']['payment_type']:false;
                	$is_charge = $this->Common->filterEmptyField($value, 'RevenueDetail', 'is_charge');
                	$rate = $this->Common->filterEmptyField($value, 'RevenueDetail', 'price_unit');
                	$total_price_unit = $this->Common->filterEmptyField($value, 'RevenueDetail', 'total_price_unit');
	                $totalPriceFormat = '';

					$no_doc = $this->Common->filterEmptyField($value, 'Revenue', 'no_doc');
					$no_do = $this->Common->filterEmptyField($value, 'RevenueDetail', 'no_do');
					$no_sj = $this->Common->filterEmptyField($value, 'RevenueDetail', 'no_sj');

	                if( !empty($is_charge) ) {
	                    $totalPriceFormat = $this->Common->getFormatPrice($total_price_unit);
                		$priceFormat = $this->Common->getFormatPrice($rate, false);
	                } else {
	                    $total_price_unit = 0;
                		$priceFormat = '';
	                }

					$colom = $this->Html->tag('td', $no++);

					if( in_array($data_print, array( 'date', 'sa' )) ) {
						$city_name = !empty($value['City']['name'])?$value['City']['name']:false;
						$colom .= $this->Html->tag('td', $value['City']['name']);
					}

					$colom .= $this->Html->tag('td', $nopol);

					if( in_array($data_print, array( 'sa' )) ) {
						$no_do_sj = array();

						if( !empty($no_do) ) {
							$no_do_sj[] = $no_do;
						}
						if( !empty($no_sj) ) {
							$no_do_sj[] = $no_sj;
						}

						if( !empty($no_do_sj) ) {
							$colom .= $this->Html->tag('td', implode(' / ', $no_do_sj));
						} else {
							$colom .= $this->Html->tag('td', '');
						}

						$colom .= $this->Html->tag('td', $no_doc);
					} else {
						$colom .= $this->Html->tag('td', $no_do);
						$colom .= $this->Html->tag('td', $no_sj);
					}


					if(!empty($value['Revenue']['date_revenue'])){
						$date_revenue = $this->Common->customDate($value['Revenue']['date_revenue'], 'd/m/Y');
					}
					$colom .= $this->Html->tag('td', $date_revenue);

					if( $data_print == 'hso-smg' ) {
						$city_name = !empty($value['City']['name'])?$value['City']['name']:false;
						$colom .= $this->Html->tag('td', $value['City']['name']);
					}

					$colom .= $this->Html->tag('td', $qty, array(
						'style' => 'text-align:center;',
					));
					$colom .= $this->Html->tag('td', $priceFormat, array(
						'style' => 'text-align:right;',
					));

					$colom .= $this->Html->tag('td', $totalPriceFormat, array(
						'style' => 'text-align:right;',
					));

					$colom .= $this->Html->tag('td', $this->Common->getNoRef($value['Revenue']['id']));
					$trData .= $this->Html->tag('tr', $colom);
					$grandTotal += $total_price_unit;
					$old_revenue_id = $revenue_id;
				}

				$content .= $trData;
				$colom = $this->Html->tag('td', '&nbsp;', array(
					'colspan' => $totalMergeTotal,
					'style' => 'text-align:right;',
				));
				$colom .= $this->Html->tag('td', $this->Number->format($grandTotalUnit), array(
					'style' => 'text-align:center;',
				));
				$colom .= $this->Html->tag('td', __('Total '), array(
					'style' => 'font-weight: bold;text-align:right;',
				));
				$colom .= $this->Html->tag('td', $this->Number->currency($grandTotal, Configure::read('__Site.config_currency_second_code'), array('places' => 0)), array(
					'style' => 'text-align:right;',
				));
				$colom .= $this->Html->tag('td', '&nbsp;');
				$content .=  $this->Html->tag('tr', $colom);

				$pph = !empty($totalPPh[0]['pph'])?$totalPPh[0]['pph']:0;
				$ppn = !empty($totalPPN[0]['ppn'])?$totalPPN[0]['ppn']:0;

				if( !empty($ppn) ) {
					$colom = $this->Html->tag('td', '&nbsp;', array(
						'colspan' => $totalMergeTotal+1,
					));
					$colom .= $this->Html->tag('td', __('PPN '), array(
						'style' => 'font-weight: bold;text-align:right;',
					));
					$colom .= $this->Html->tag('td', $this->Number->currency($ppn, Configure::read('__Site.config_currency_second_code'), array('places' => 0)), array(
						'style' => 'font-weight: bold;text-align:right;',
					));
					$colom .= $this->Html->tag('td', '&nbsp;');

					$content .= $this->Html->tag('tr', $colom, array(
						'class' => 'total-row'
					));
				}

				if( !empty($pph) ) {
					$colom = $this->Html->tag('td', '&nbsp;', array(
						'colspan' => $totalMergeTotal+1,
					));
					$colom .= $this->Html->tag('td', __('PPh '), array(
						'align' => 'right',
						'style' => 'font-weight: bold;',
					));
					$colom .= $this->Html->tag('td', $this->Number->currency($pph, Configure::read('__Site.config_currency_second_code'), array('places' => 0)), array(
						'align' => 'right',
						'style' => 'font-weight: bold;',
					));
					$colom .= $this->Html->tag('td', '&nbsp;');

					$content .= $this->Html->tag('tr', $colom, array(
						'class' => 'total-row'
					));
				}

				if( !empty($ppn) || !empty($pph) ) {
					// $grandTotalInvoice = $grandTotal + $ppn - $pph;
					$grandTotalInvoice = $grandTotal + $ppn;
					$colom = $this->Html->tag('td', '&nbsp;', array(
						'colspan' => $totalMergeTotal+1,
					));
					$colom .= $this->Html->tag('td', __('Grantotal '), array(
						'align' => 'right',
						'style' => 'font-weight: bold;',
					));
					$colom .= $this->Html->tag('td', $this->Number->currency($grandTotalInvoice, Configure::read('__Site.config_currency_second_code'), array('places' => 0)), array(
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
$filename = 'Laporan_'.$this->Common->toSlug($date_title).'.pdf';
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
<div class="clear"></div>
<?php 
		}

     //    echo $this->element('blocks/common/tables/logo', array(
     //    	'value' => $invoice,
    	// ));
?>
<div class="invoice-print">
	<?php
			echo $this->Html->tag('h2', $module_title, array(
                'style' => 'text-align: center;font-size: 24px;margin: 20px 0 25px;',
            ));

            echo $this->element('blocks/revenues/preview_invoice');
	?>
</div>
<?php
	}
?>