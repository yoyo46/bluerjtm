<?php
		$qty_unit = !empty($invoice['qty_unit'])?$invoice['qty_unit']:0;
        $full_name = $this->Common->filterEmptyField($invoice, 'Employe', 'full_name');
        $position = $this->Common->filterEmptyField($invoice, 'EmployePosition', 'name');
        $logo = $this->Common->filterEmptyField($invoice, 'Company', 'logo');

        if( !empty($logo) ) {
			$company_name = $this->Common->filterEmptyField( $invoice, 'Company', 'name' );
	        $logo = $this->Common->photo_thumbnail(array(
	            'save_path' => Configure::read('__Site.general_photo_folder'), 
	            'src' => $logo, 
	            'thumb'=>true,
	            'size' => 'pm',
	            'thumb' => true,
	            'fullpath' => true,
	        ), array(
	        	'width' => '75',
				'height' => '75',
	        ));
	    } else {
			$company_name = $this->Common->getDataSetting( $setting, 'company_name' );
	    	$logo = $this->Common->getDataSetting( $setting, 'logo', array(
				'width' => '75',
				'height' => '75',
			));
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
	$company_name = sprintf('%s%s', $company_name, $this->Html->tag('div', __('KWITANSI')));
	$no_invoice = sprintf(': %s', $this->Html->tag('strong', $invoice['Invoice']['no_invoice']));
	$customer_name = sprintf(': %s', $invoice['Customer']['name']);
	$total = sprintf(': %s', $this->Html->tag('strong', $this->Number->currency($invoice['Invoice']['total'], Configure::read('__Site.config_currency_second_code'), array('places' => 0))));
	$terbilang = sprintf(': %s', $this->Common->terbilang($invoice['Invoice']['total']));

	if( !empty($invoice['Invoice']['note']) ) {
		$totalUnit = str_replace(PHP_EOL, '<br>', $invoice['Invoice']['note']);
	} else {
		$totalUnit = sprintf(__(': JASA ANGKUT SEPEDA MOTOR<br>&nbsp;&nbsp;Sebanyak %s unit<br>&nbsp;&nbsp;PERIODE : <i>%s s/d %s</i>'), $qty_unit, $this->Common->customDate($invoice['Invoice']['period_from'], 'd F Y'), $this->Common->customDate($invoice['Invoice']['period_to'], 'd F Y'));
	}

	$dateLocation = sprintf('%s, %s', $this->Common->getDataSetting( $setting, 'pusat' ), date('d F Y'));
	$billing_name = $full_name;
	// $note = sprintf(__('*Mohon pembayaran dilakukan paling lambat %s hari dari tanggal kwitansi.'), $invoice['Invoice']['due_invoice']);
	$note = sprintf(__('*Mohon pembayaran dilakukan paling lambat %s hari dari tanggal kwitansi.'), $invoice['Invoice']['term_of_payment']);
	$bank_name = !empty($invoice['Bank']['name'])?$invoice['Bank']['name']:false;
	$bank_branch = !empty($invoice['Bank']['branch'])?$invoice['Bank']['branch']:false;
	$account_number = !empty($invoice['Bank']['account_number'])?$invoice['Bank']['account_number']:false;
	$account_name = !empty($invoice['Bank']['account_name'])?$invoice['Bank']['account_name']:false;

	if( !empty($invoice['Bank']['name']) ) {
		$bank_name = $this->Html->tag('p', sprintf(__('Pembayaran mohon ditransfer ke:<br><br>&nbsp;<b>%s</b><br>%s<br>&nbsp;A/C <b>%s</b><br>&nbsp;A/N <b>%s</b>'), $invoice['Bank']['name'], $bank_branch, $account_number, $account_name));
	}

	$company_name = $this->Html->tag('div', $company_name, array(
		'style' => 'font-size: 48px;',
	));

$tbl = <<<EOD
	<table class="text" style="padding: 5px;">
		<tbody>
			<tr align="left">
				<td colspan="3">
					<table width="100%">
						<tbody>
							<tr>
								<td width="90px">$logo</td>
								<td>$company_name</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>No. Kwitansi</td>
				<td style="padding-left: 5px;">$no_invoice</td>
			</tr>
			<tr>
				<td>Telah terima dari</td>
				<td style="padding-left: 5px;">$customer_name</td>
			</tr>
			<tr>
				<td>Telah terima dari</td>
				<td style="padding-left: 5px;">$total</td>
			</tr>
			<tr>
				<td>Terbilang</td>
				<td style="padding-left: 5px;">$terbilang</td>
			</tr>
			<tr valign="top">
				<td>Untuk pembayaran</td>
				<td style="padding-left: 5px;">$totalUnit</td>
			</tr>
			<tr>
			  	<td style="padding: 15px;">
			  		<br><br><br>
					<table style="border-collapse:collapse" border="1">
						<tbody>
							<tr valign="middle">
								<td valign="middle">$bank_name</td>
							</tr>
						</tbody>
					</table>
				</td>
				<td valign="top" align="right">
			  		<br><br><br>
					$dateLocation
			  		<br><br><br>
					<div style="margin: 70px 0 0;text-decoration: underline;">$billing_name</div>
					$position
				</td>
			</tr>
		</tbody>
	</table>
	<p style="font-style: italic;font-weight: bold;margin-top: 15px;">$note</p>
EOD;
// echo $tbl;
// die();

// output the HTML content
$tcpdf->writeHTML($tbl, true, false, false, false, '');

$tcpdf->SetTextColor(0, 0, 0);
$tcpdf->SetFont($textfont,'B',10);

$path = $this->Common->pathDirTcpdf();
$filename = 'Laporan_'.$this->Common->toSlug($sub_module_title).'.pdf';
$tcpdf->Output($path.'/'.$filename, 'F'); 

header('Content-type: application/pdf');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($path.'/'.$filename);
		}else{
	        if( $action_print == 'excel' ) {
	            header('Content-type: application/ms-excel');
	            header('Content-Disposition: attachment; filename='.$sub_module_title.'.xls');
	            $border = 1;
	            $tdStyle = 'text-align: center;';
	        }

	        if( empty($action_print) ) {
                echo $this->Html->tag('div', $this->Html->link('<i class="fa fa-print"></i> print', 'javascript:', array(
                    'class' => 'btn btn-default hidden-print print-window',
                    'escape' => false
                )), array(
                    'class' => 'action-print pull-right',
                ));
                echo $this->Common->_getPrint();
            }
?>
<div class="clear"></div>
<div align="center" id="invoice-header-preview">
	<table class="text print-width-70">
		<tbody>
			<tr align="center">
				<td colspan="3" style="font-size:24px">
					<table width="100%">
						<tbody>
							<?php 
    								if( $action_print == 'excel' ) {
							?>
							<tr>
								<td style="vertical-align: top;margin: 0;">
									<?php 
											echo $logo;
											echo $this->Html->tag('div', sprintf('%s<br>%s', $company_name, $this->Html->tag('span', __('KWITANSI'), array(
												'class' => 'lbl-kwitansi',
											))), array(
												'style' => 'margin-left: 90px;',
											));
									?>
								</td>
							</tr>
							<?php
    								} else {
							?>
							<tr>
								<?php 
										echo $this->Html->tag('td', $logo, array(
											'width' => '108px',
										));
								?>
								<td style="vertical-align: top;">
									<?php 
											echo $this->Html->tag('div', sprintf('%s<br>%s', $company_name, $this->Html->tag('span', __('KWITANSI'), array(
												'class' => 'lbl-kwitansi',
												'style' => 'font-size: 20px !important;',
											))), array(
												'style' => 'font-size: 24px;line-height: 25px;',
											));
									?>
								</td>
							</tr>
							<?php 
									}
							?>
						</tbody>
					</table>
				</td>
			</tr>
			<tr align="center">
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<?php 
						echo $this->Html->tag('td', __('No. Kwitansi'));
						echo $this->Html->tag('td', sprintf(': %s', $this->Html->tag('strong', $invoice['Invoice']['no_invoice'])), array(
							'style' => 'padding-left: 5px;',
						));
				?>
			</tr>
			<tr>
				<?php 
						echo $this->Html->tag('td', __('Telah terima dari'));
						echo $this->Html->tag('td', sprintf(': %s', $invoice['Customer']['name']), array(
							'style' => 'padding-left: 5px;',
						));
				?>
			</tr>
			<tr>
				<?php 
						echo $this->Html->tag('td', __('Uang sejumlah'));
						echo $this->Html->tag('td', sprintf(': %s', $this->Html->tag('strong', $this->Number->currency($invoice['Invoice']['total'], Configure::read('__Site.config_currency_second_code'), array('places' => 0)))), array(
							'style' => 'padding-left: 5px;',
						));
				?>
			</tr>
			<tr>
				<?php 
						echo $this->Html->tag('td', __('Terbilang'));
						echo $this->Html->tag('td', sprintf(': %s', $this->Common->terbilang($invoice['Invoice']['total'])), array(
							'style' => 'padding-left: 5px;',
						));
				?>
			</tr>
			<tr valign="top">
				<?php 
						echo $this->Html->tag('td', __('Untuk pembayaran'));

						if( !empty($invoice['Invoice']['note']) ) {
							$invoice_note = str_replace(PHP_EOL, '<br>', $invoice['Invoice']['note']);
						} else {
	                        switch ($invoice['Invoice']['tarif_type']) {
	                        	case 'kuli':
	                        		$ket = __('BIAYA KULI MUAT SEPEDA MOTOR');
	                        		break;

	                        	case 'asuransi':
	                        		$ket = __('BIAYA ASURANSI SEPEDA MOTOR');
	                        		break;
	                        	
	                        	default:
	                        		$ket = __('JASA ANGKUT SEPEDA MOTOR');
	                        		break;
	                        }

							$invoice_note = $this->Html->tag('td', sprintf(__(': %s<br>&nbsp;&nbsp;Sebanyak %s unit<br>&nbsp;&nbsp;PERIODE : <i>%s s/d %s</i>'), $ket, $qty_unit, $this->Common->customDate($invoice['Invoice']['period_from'], 'd F Y'), $this->Common->customDate($invoice['Invoice']['period_to'], 'd F Y')), array(
								'style' => 'padding-left: 5px;',
							));
						}

						echo $this->Html->tag('td', $invoice_note, array(
							'style' => 'padding-left: 5px;',
						));
				?>
			</tr>
			<tr>
			  	<td style="padding-top: 50px;" class="pt20">
					<table style="border-collapse:collapse" border="1">
						<tbody>
							<tr valign="middle">
								<td valign="middle" align="center">
									<?php 
											$bank_name = !empty($invoice['Bank']['name'])?$invoice['Bank']['name']:false;
											$bank_branch = !empty($invoice['Bank']['branch'])?$invoice['Bank']['branch']:false;
											$account_number = !empty($invoice['Bank']['account_number'])?$invoice['Bank']['account_number']:false;
											$account_name = !empty($invoice['Bank']['account_name'])?$invoice['Bank']['account_name']:false;
											
											if( !empty($bank_name) ) {
												echo $this->Html->tag('p', sprintf(__('Pembayaran mohon ditransfer ke:<br><br>&nbsp;<b>%s</b><br>%s<br>&nbsp;A/C <b>%s</b><br>&nbsp;A/N <b>%s</b>'), $bank_name, $bank_branch, $account_number, $account_name), array(
													'class' => 'setting-link'
												));
											}
									?>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
				<td valign="top" align="right" style="padding-top: 50px;" class="pt20">
					<?php 
							echo $this->Html->tag('p', sprintf('%s, %s', $this->Common->getDataSetting( $setting, 'pusat' ), date('d F Y')));
							echo $this->Html->tag('p', $full_name, array(
								'style' => 'margin: 90px 0 0;border-bottom: 1px solid #000;display: inline-block;'
							));
							echo $this->Html->tag('p', $position);
					?>
				</td>
			</tr>
		</tbody>
	</table>
	<?php 
			// echo $this->Html->tag('p', sprintf(__('*Mohon pembayaran dilakukan paling lambat %s hari dari tanggal kwitansi.'), $invoice['Invoice']['due_invoice']), array(
			// 	'style' => 'font-style: italic;font-weight: bold;margin-top: 15px;text-align: center;'
			// ));
			echo $this->Html->tag('p', sprintf(__('*Mohon pembayaran dilakukan paling lambat %s hari dari tanggal kwitansi.'), $invoice['Invoice']['term_of_payment']), array(
				'style' => 'font-style: italic;font-weight: bold;margin-top: 15px;text-align: center;'
			));
	?>
</div>
<?php 
		}
?>