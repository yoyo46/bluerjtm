<?php
		$customer = $this->Common->filterEmptyField($invoice, 'Customer', 'name');
		$no_invoice = $this->Common->filterEmptyField($invoice, 'Invoice', 'no_invoice');

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
    $content = '';

	if( !empty($invoice['Customer']) ) {
		$content .= $this->Html->tag('h3', $invoice['Customer']['name'], array(
			'style' => 'margin-bottom: 20px;text-align: center;'
		));
	}

	$tdContentNo = $this->Html->tag('th', __('NO'), array(
		'style' => 'width: 100px;text-align: left;'
	));
	$tdContentNo .= $this->Html->tag('td', $invoice['Invoice']['no_invoice']);
	$tdContent = $this->Html->tag('tr', $tdContentNo);

	$tdContentPeriode = $this->Html->tag('th', __('Periode'), array(
		'style' => 'width: 100px;text-align: left;'
	));
	$tdContentPeriode .= $this->Html->tag('td', $this->Common->combineDate($invoice['Invoice']['period_from'], $invoice['Invoice']['period_to'], 'M Y'));
	$tdContent .= $this->Html->tag('tr', $tdContentPeriode);
	$content .= '<div class="table-responsive">
	    <table class="table">
	        <tbody>
	    		'.$tdContent.'
		    </tbody>
		</table>
	</div>';
	$content .= '<table border="1" width="100%" style="padding: 5px; font-size: 25px;">';
	$content .= $this->element('blocks/revenues/prints/invoice_hso_print', array(
		'data_print_type' => 'pdf',
	));
	$content .= '</table>';
	
    $date_title = $sub_module_title;
$tbl = <<<EOD
	<h2 style="text-align: center;margin-bottom: 10px;">Laporan $date_title</h2>
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
            if( $action_print == 'excel' ) {
            	echo $this->element(sprintf('blocks/common/tables/export_%s', $action_print), array(
	                'tableContent' => $this->element('blocks/revenues/prints/invoice_hso_yogya_print'),
	                'filename' => $sub_module_title,
	                'sub_module_title' => false,
	            ));
            } else {
        		$this->Html->addCrumb(__('Invoice'), array(
        			'controller' => 'revenues',
        			'action' => 'invoices'
    			));
        		$this->Html->addCrumb($sub_module_title);

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
?>
<div class="header-title">
	<?php 
			echo $this->Html->tag('h3', sprintf(__('%s - TARIF 3'), $customer), array(
				'class' => 'header-invoice',
				'style' => 'font-size: 18px;font-weight: 700;margin-top: 0;margin-bottom: 20px;'
			));
	?>
	<div class="no-invoice" style="float: right;">
		<?php 
				echo $this->Html->tag('p', $no_invoice, array(
					'style' => 'font-size: 16px;font-weight: 700;margin: 0 0 5px;'
				));
				echo $this->Html->tag('p', __('JASA ANGKUT JOGYA TARIF 3'), array(
					'style' => 'font-size: 16px;font-weight: 700;margin: 0;'
				));
		?>
	</div>
	<div class="clear"></div>
</div>
<div class="invoice-print">
	<?php
			if(!empty($invoice)){
				echo $this->element('blocks/revenues/prints/invoice_hso_yogya_print');
			} else {
	            echo $this->Html->tag('p', $this->Html->tag('td', __('Data belum tersedia.'), array(
	                'class' => 'alert alert-warning text-center',
	            )));
	        }
	?>
</div>
<?php
			}
		}
?>