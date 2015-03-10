<?php 
        if( empty($data_action) || ( !empty($data_action) && $data_action == 'excel' ) ){
            $this->Html->addCrumb($sub_module_title);
            $addStyle = '';
            $tdStyle = '';
            $border = 0;

            if( $data_action == 'excel' ) {
                header('Content-type: application/ms-excel');
                header('Content-Disposition: attachment; filename='.$sub_module_title.'.xls');
                $border = 1;
                $tdStyle = 'text-align: center;';
            }

            if( !empty($cities) ) {
                $addStyle = 'min-width: 1000px;';
            }

            if( $data_action != 'excel' ) {
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
            echo $this->Form->create('Invoice', array(
                'url'=> $this->Html->url( array(
                    'controller' => 'revenues',
                    'action' => 'search',
                    'list_kwitansi'
                )), 
                'role' => 'form',
                'inputDefaults' => array('div' => false),
            ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('date',array(
                                'label'=> __('Periode Tanggal'),
                                'class'=>'form-control date-range',
                                'required' => false,
                            ));
                    ?>
                </div>
                <div class="form-group action">
                    <?php
                            echo $this->Form->button('<i class="fa fa-search"></i> '.__('Submit'), array(
                                'div' => false, 
                                'class'=> 'btn btn-success btn-sm',
                                'type' => 'submit',
                            ));
                            echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                                'action' => 'list_kwitansi', 
                            ), array(
                                'escape' => false, 
                                'class'=> 'btn btn-default btn-sm',
                            ));
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('transaction_status',array(
                                'label'=> __('Status Invoice'),
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => __('Pilih Status Invoice'),
                                'options' => array(
                                    'invoiced' => 'Unpaid',
                                    'paid' => 'Paid',
                                )
                            ));
                    ?>
                </div>
            </div>
        </div>
        <?php 
                echo $this->Form->end();
        ?>
    </div>
</div>
<section class="content invoice">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
    </h2>
    <div class="row no-print print-action">
        <div class="col-xs-12 action">
            <?php
                    $urlExcel = $this->passedArgs;
                    $urlExcel[] = 'excel';
                    echo $this->Html->link('<i class="fa fa-download"></i> Download Excel', $urlExcel, array(
                        'escape' => false,
                        'class' => 'btn btn-success pull-right'
                    ));

                    $urlPdf = $this->passedArgs;
                    $urlPdf[] = 'pdf';
                    echo $this->Html->link('<i class="fa fa-download"></i> Download PDF', $urlPdf, array(
                        'escape' => false,
                        'class' => 'btn btn-primary pull-right'
                    ));
            ?>
        </div>
    </div>
    <div class="table-responsive">
        <?php 
                }
        ?>
        <table class="table table-bordered report" style="<?php echo $addStyle; ?>" border="<?php echo $border; ?>">
            <thead>
                <tr>
                    <?php 
                            echo $this->Html->tag('th', __('No. Invoice'), array(
                                'style' => $tdStyle,
                                'class' => 'text-center text-middle',
                            ));
                            echo $this->Html->tag('th', __('Periode Tanggal'), array(
                                'style' => $tdStyle,
                                'class' => 'text-center text-middle',
                            ));
                            echo $this->Html->tag('th', __('Periode Bulan'), array(
                                'style' => $tdStyle,
                                'class' => 'text-center text-middle',
                            ));
                            echo $this->Html->tag('th', __('Qty Unit'), array(
                                'style' => $tdStyle,
                                'class' => 'text-center text-middle',
                            ));
                            echo $this->Html->tag('th', __('Jenis'), array(
                                'style' => $tdStyle,
                                'class' => 'text-center text-middle',
                            ));
                            echo $this->Html->tag('th', __('Tgl J.Tempo'), array(
                                'style' => $tdStyle,
                                'class' => 'text-center text-middle',
                            ));
                            echo $this->Html->tag('th', __('Tgl Bayar'), array(
                                'style' => $tdStyle,
                                'class' => 'text-center text-middle',
                            ));
                            echo $this->Html->tag('th', __('Total Transfer'), array(
                                'style' => $tdStyle,
                                'class' => 'text-center text-middle',
                            ));
                            echo $this->Html->tag('th', __('Status'), array(
                                'style' => $tdStyle,
                                'class' => 'text-center text-middle',
                            ));
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                        if(!empty($invoices)){
                            foreach ($invoices as $key => $invoice) {
                                $totalUnit = !empty($invoice['RevenueDetail']['qty_unit'])?$invoice['RevenueDetail']['qty_unit']:0;
                                $dateTOP = !empty($invoice['Invoice']['term_of_payment'])?date('d/m/Y', strtotime(sprintf('+%s day', $invoice['Invoice']['term_of_payment']), strtotime($invoice['Invoice']['invoice_date']))):'-';
                                $datePayment = array();
                                $totalPaid = 0;

                                if( !empty($invoice['InvoicePaymentDate']) ) {
                                    foreach ($invoice['InvoicePaymentDate'] as $key => $dtPaid) {
                                        $datePayment[] = $this->Common->customDate($dtPaid, 'd/m/Y');
                                    }
                                }

                                if( !empty($invoice[0]['total_payment']) ) {
                                    $totalPaid = $invoice[0]['total_payment'];
                                }

                                if(!empty($invoice['Invoice']['complete_paid'])){
                                    $class_status = 'label label-success';
                                    $status = __('Paid');
                                } else {
                                    $class_status = 'label label-primary';
                                    $status = __('Unpaid');
                                }
                ?>
                <tr>
                    <?php 
                            echo $this->Html->tag('td', $invoice['Invoice']['no_invoice'], array(
                                'class' => 'text-left',
                                'style' => $tdStyle,
                            ));
                            echo $this->Html->tag('td', $this->Common->combineDate($invoice['Invoice']['period_from'], $invoice['Invoice']['period_to']), array(
                                'class' => 'text-center',
                                'style' => $tdStyle,
                            ));
                            echo $this->Html->tag('td', $this->Common->combineDate($invoice['Invoice']['period_from'], $invoice['Invoice']['period_to'], 'M Y'), array(
                                'class' => 'text-center',
                                'style' => $tdStyle,
                            ));
                            echo $this->Html->tag('td', $totalUnit, array(
                                'class' => 'text-center',
                                'style' => $tdStyle,
                            ));
                            echo $this->Html->tag('td', __('Angkut'), array(
                                'class' => 'text-center',
                                'style' => $tdStyle,
                            ));
                            echo $this->Html->tag('td', $dateTOP, array(
                                'class' => 'text-center',
                                'style' => $tdStyle,
                            ));
                            echo $this->Html->tag('td', implode('<br>', $datePayment), array(
                                'class' => 'text-center',
                                'style' => $tdStyle,
                            ));
                            echo $this->Html->tag('td', $this->Number->currency($totalPaid, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                                'style' => 'text-align: right;',
                            ));
                            echo $this->Html->tag('td', $this->Html->tag('span', $status, array('class' => $class_status)), array(
                                'style' => 'text-align: right;',
                            ));
                    ?>
                </tr>
                <?php
                            }
                        }
                ?>
            </tbody>
        </table>
        <?php 
                if( $data_action != 'excel' ) {
                    if(empty($invoices)){
                        echo $this->Html->tag('p', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                        ));
                    }
        ?>
    </div><!-- /.box-body -->
    <?php 
                echo $this->Html->tag('div', $this->element('pagination'), array(
                    'class' => 'pagination-report'
                ));
    ?>
</div>
<?php
            }
        } else if( $data_action == 'pdf' ) {
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
            $table = 'width:100%;font-size: 24px; border: 1px solid #CCC; border-collapse: collapse; padding: 0; margin: 0;';
        
            $no = 1;
            $each_loop_message = '';

            if(!empty($invoices)){
                foreach ($invoices as $key => $invoice) {
                    $totalUnit = !empty($invoice['RevenueDetail']['qty_unit'])?$invoice['RevenueDetail']['qty_unit']:0;
                    $dateTOP = !empty($invoice['Invoice']['term_of_payment'])?date('d/m/Y', strtotime(sprintf('+%s day', $invoice['Invoice']['term_of_payment']), strtotime($invoice['Invoice']['invoice_date']))):'-';
                    $datePayment = array();
                    $totalPaid = 0;

                    if( !empty($invoice['InvoicePaymentDate']) ) {
                        foreach ($invoice['InvoicePaymentDate'] as $key => $dtPaid) {
                            $datePayment[] = $this->Common->customDate($dtPaid, 'd/m/Y');
                        }
                    }

                    if( !empty($invoice[0]['total_payment']) ) {
                        $totalPaid = $invoice[0]['total_payment'];
                    }

                    if(!empty($invoice['Invoice']['complete_paid'])){
                        $class_status = 'label label-success';
                        $status = __('Paid');
                    } else {
                        $class_status = 'label label-primary';
                        $status = __('Unpaid');
                    }

                    $content = $this->Html->tag('td', $invoice['Invoice']['no_invoice'], array(
                        'style' => 'text-align: left;',
                    ));
                    $content .= $this->Html->tag('td', $this->Common->combineDate($invoice['Invoice']['period_from'], $invoice['Invoice']['period_to']), array(
                        'style' => 'text-align: center;',
                    ));
                    $content .= $this->Html->tag('td', $this->Common->combineDate($invoice['Invoice']['period_from'], $invoice['Invoice']['period_to'], 'M Y'), array(
                        'style' => 'text-align: center;',
                    ));
                    $content .= $this->Html->tag('td', $totalUnit, array(
                        'style' => 'text-align: center;',
                    ));
                    $content .= $this->Html->tag('td', __('Angkut'), array(
                        'style' => 'text-align: center;',
                    ));
                    $content .= $this->Html->tag('td', $dateTOP, array(
                        'style' => 'text-align: center;',
                    ));
                    $content .= $this->Html->tag('td', implode('<br>', $datePayment), array(
                        'style' => 'text-align: center;',
                    ));
                    $content .= $this->Html->tag('td', $this->Number->currency($totalPaid, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                        'style' => 'text-align: right;',
                    ));
                    $content .= $this->Html->tag('td', $status, array(
                        'style' => 'text-align: center;',
                    ));

                    $each_loop_message .= $this->Html->tag('tr', $content);
                    $no++;
                }
            }

            $date_title = $sub_module_title;
$tbl = <<<EOD

      <div class="clearfix container_16" id="content">

        <h2 class="grid_8" style="text-align: center;">$date_title</h2>
        <table cellpadding="2" cellspacing="2" nobr="true" style="$table">
            <thead>
                <tr style="$table_tr_head">
                    <th style="text-align: center;">No. Invoice</th>
                    <th style="text-align: center;">Periode Tanggal</th>
                    <th style="text-align: center;">Periode Bulan</th>
                    <th style="text-align: center;">Qty Unit</th>
                    <th style="text-align: center;">Jenis</th>
                    <th style="text-align: center;">Tgl J.Tempo</th>
                    <th style="text-align: center;">Tgl Bayar</th>
                    <th style="text-align: center;">Total Transfer</th>
                    <th style="text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>          
                $each_loop_message
            </tbody>
        </table> 
      </div>
EOD;
// echo $tbl;
// die();

        // output the HTML content
        $tcpdf->writeHTML($tbl, true, false, false, false, '');

        $tcpdf->SetTextColor(0, 0, 0);
        $tcpdf->SetFont($textfont,'B',10);

        $path = $this->Common->pathDirTcpdf();
        $filename = 'Laporan_Truk_'.$date_title.'.pdf';
        $tcpdf->Output($path.'/'.$filename, 'F'); 

        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        readfile($path.'/'.$filename);
    }
?>