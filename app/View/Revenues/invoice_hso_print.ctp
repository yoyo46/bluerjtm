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

			echo $this->Html->tag('h2', __('PERINCIAN PENGIRIMAN UNIT'), array(
				'class' => 'page-header'
			));

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
<?php 
		if( !empty($invoice['Customer']) ) {
			echo $this->Html->tag('h3', $invoice['Customer']['name'], array(
				'class' => 'header-invoice'
			));
		}
?>
<div class="table-responsive">
    <table class="table">
        <tbody>
    		<?php 
    				$tdContent = $this->Html->tag('th', __('NO'), array(
    					'style' => 'width: 100px;text-align: left;'
					));
    				$tdContent .= $this->Html->tag('td', $invoice['Invoice']['no_invoice']);
    				echo $this->Html->tag('tr', $tdContent);

    				$tdContent = $this->Html->tag('th', __('Periode'), array(
    					'style' => 'width: 100px;text-align: left;'
					));
    				$tdContent .= $this->Html->tag('td', $this->Common->combineDate($invoice['Invoice']['period_from'], $invoice['Invoice']['period_to'], 'M Y'));
    				echo $this->Html->tag('tr', $tdContent);
    		?>
	    </tbody>
	</table>
</div>
<div class="invoice-print">
	<?php
			if(!empty($invoice)){
	?>
	<table border="1" width="100%" style="margin-top: 20px;">
		<?php 
				echo $this->element('blocks/revenues/prints/invoice_hso_print');
		?>
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