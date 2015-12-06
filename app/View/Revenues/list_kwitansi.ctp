<?php 
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        
        if( empty($data_action) || ( !empty($data_action) && $data_action == 'excel' ) ){
            $this->Html->addCrumb($sub_module_title);
            $addStyle = 'width: 100%;height: 550px;';
            $tdStyle = '';
            $border = 0;
            $headerRowspan = false;

            if( !empty($invoices) ) {
                $addClass = 'easyui-datagrid';
            } else {
                $addClass = '';
            }

            if( $data_action == 'excel' ) {
                header('Content-type: application/ms-excel');
                header('Content-Disposition: attachment; filename='.$sub_module_title.'.xls');
                $border = 1;
                $tdStyle = 'text-align: center;';
                $addClass = '';
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
                            echo $this->Form->input('Ttuj.customer',array(
                                'label'=> __('Customer'),
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => __('Pilih Customer'),
                            ));
                    ?>
                </div>
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
                            echo $this->Form->input('no_invoice',array(
                                'label'=> __('No. Invoice'),
                                'class'=>'form-control',
                                'required' => false,
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('status',array(
                                'label'=> __('Status Invoice'),
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => __('Pilih Status Invoice'),
                                'options' => $invStatus,
                            ));
                    ?>
                </div>
                <?php 
                        // Custom Otorisasi
                        echo $this->Common->getCheckboxBranch();
                ?>
            </div>
        </div>
        <?php 
                echo $this->Form->end();
        ?>
    </div>
</div>

<section class="content invoice list_kwitansi">
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

                echo $this->Common->getInvoiceStatusContent( $dataStatus );
        ?>
        <table id="tt" class="table table-bordered <?php echo $addClass; ?>" style="<?php echo $addStyle; ?>" border="<?php echo $border; ?>" data-options="
                singleSelect: true,
                rowStyler: function(index,row){
                    var doc_status = $(row.status).filter('.label').html() + '';
                    doc_status = doc_status.toLowerCase();

                    if ( doc_status == 'void' ){
                        return 'background-color:#f2dede;';
                    } else if ( doc_status == 'half paid' ){
                        return 'background-color:#d9edf7;';
                    } else if ( doc_status == 'paid' ){
                        return 'background-color:#dff0d8;';
                    } else if ( doc_status == 'unpaid' ){
                        return 'background-color:#f5f5f5;';
                    }
                }
            ">
            <thead frozen="true">
                <tr>
                    <?php 
                            echo $this->Html->tag('th', __('No.'), array(
                                'style' => 'text-align: center;width: 50px;vertical-align: middle;',
                                'data-options' => 'field:\'no\',width:50',
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', $this->Common->getSorting('Invoice.invoice_date', __('Tgl Kwitansi')), array(
                                'style' => 'text-align: center;width: 100px;vertical-align: middle;',
                                'data-options' => 'field:\'invoice_date\',width:100',
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', $this->Common->getSorting('CustomerNoType.name', __('Customer')), array(
                                'style' => 'text-align: center;width: 120px;vertical-align: middle;',
                                'data-options' => 'field:\'customer\',width:120',
                                'align' => 'left',
                                'mainalign' => 'center',
                            ));
                            echo $this->Html->tag('th', $this->Common->getSorting('Invoice.no_invoice', __('No. Invoice')), array(
                                'style' => 'text-align: center;width: 120px;vertical-align: middle;',
                                'data-options' => 'field:\'no_invoice\',width:120',
                                'align' => 'left',
                                'mainalign' => 'center',
                            ));

                            if( $data_action != 'excel' && !empty($invoices) ) {
                    ?>
                </tr>
            </thead>
            <thead>
                <tr>
                    <?php 
                            }

                            echo $this->Html->tag('th', $this->Common->getSorting('Invoice.total', __('Total Tagihan')), array(
                                'style' => 'text-align: center;width: 150px;vertical-align: middle;',
                                'data-options' => 'field:\'total\',width:150,',
                                'align' => 'right',
                                'mainalign' => 'center',
                            ));
                            echo $this->Html->tag('th', __('Total PPH'), array(
                                'style' => 'text-align: center;width: 100px;vertical-align: middle;',
                                'data-options' => 'field:\'total_pph\',width:150',
                                'align' => 'right',
                                'mainalign' => 'center',
                            ));
                            echo $this->Html->tag('th', __('Periode Tanggal'), array(
                                'style' => 'text-align: center;width: 100px;vertical-align: middle;',
                                'data-options' => 'field:\'period\',width:100',
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', __('Periode Bulan'), array(
                                'style' => 'text-align: center;width: 100px;vertical-align: middle;',
                                'data-options' => 'field:\'period_month\',width:100',
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', __('Qty Unit'), array(
                                'style' => 'text-align: center;width: 80px;vertical-align: middle;',
                                'data-options' => 'field:\'qty\',width:80',
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', $this->Common->getSorting('Invoice.tarif_type', __('Jenis')), array(
                                'style' => 'text-align: center;width: 100px;vertical-align: middle;',
                                'data-options' => 'field:\'tarif_type\',width:100',
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', __('Tgl J.Tempo'), array(
                                'style' => 'text-align: center;width: 100px;vertical-align: middle;',
                                'data-options' => 'field:\'expired_date\',width:100',
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', __('Tgl Bayar'), array(
                                'style' => 'text-align: center;width: 100px;vertical-align: middle;',
                                'data-options' => 'field:\'paid_date\',width:100',
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', __('Total Transfer'), array(
                                'style' => 'text-align: center;width: 150px;vertical-align: middle;',
                                'data-options' => 'field:\'total_transfer\',width:150',
                                'align' => 'right',
                                'mainalign' => 'center',
                            ));
                            echo $this->Html->tag('th', $this->Common->getSorting('Invoice.complete_paid', __('Status')), array(
                                'style' => 'text-align: center;width: 100px;vertical-align: middle;',
                                'data-options' => 'field:\'status\',width:100,styler:cellStyler',
                                'align' => 'center',
                            ));
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                        if(!empty($invoices)){
                            $idx = $start;
                            $totalTagihan = 0;
                            $totalQty = 0;
                            $totalTransfer = 0;
                            $totalVoidTransfer = 0;
                            $totalVoid = 0;
                            $totalPPH = 0;
                            $grandtotalPPH = 0;

                            foreach ($invoices as $key => $invoice) {
                                $totalUnit = !empty($invoice['qty_unit'])?$invoice['qty_unit']:0;
                                $totalPPH = !empty($invoice['total_pph'])?$invoice['total_pph']:0;
                                $dateTOP = !empty($invoice['Invoice']['term_of_payment'])?date('d/m/Y', strtotime(sprintf('+%s day', $invoice['Invoice']['term_of_payment']), strtotime($invoice['Invoice']['invoice_date']))):'-';
                                $datePayment = array();
                                $totalPaid = 0;
                                $invoiceStatus = $this->Common->getInvoiceStatus( $invoice );
                                $totalTagihan += $invoice['Invoice']['total'];
                                $totalQty += $totalUnit;
                                $grandtotalPPH += $totalPPH;

                                if( !empty($invoice['InvoicePaymentDate']) ) {
                                    foreach ($invoice['InvoicePaymentDate'] as $key => $dtPaid) {
                                        $datePayment[] = $this->Common->customDate($dtPaid, 'd/m/Y');
                                    }
                                }

                                if( !empty($invoice[0]['total_payment']) ) {
                                    $totalPaid = $invoice[0]['total_payment'];
                                }

                                $totalTransfer += $totalPaid;

                                if( strtolower($invoiceStatus['text']) == 'void' ) {
                                    $totalVoidTransfer += $totalPaid;
                                    $totalVoid += $invoice['Invoice']['total'];
                                }
                ?>
                <tr>
                    <?php 
                            echo $this->Html->tag('td', $idx, array(
                                'style' => 'text-align: center;vertical-align: middle;',
                            ));
                            echo $this->Html->tag('td', $this->Common->customDate($invoice['Invoice']['invoice_date'], 'd/m/Y'), array(
                                'style' => 'text-align: center;vertical-align: middle;',
                            ));
                            echo $this->Html->tag('td', $this->Common->fullNameCustomer($invoice, 'CustomerNoType'), array(
                                'style' => 'text-align: left;vertical-align: middle;',
                            ));
                            echo $this->Html->tag('td', $this->Html->link($invoice['Invoice']['no_invoice'], array(
                                    'controller' => 'revenues',
                                    'action' => 'invoice_print',
                                    $invoice['Invoice']['id'],
                                ), array(
                                    'target' => '_blank'
                                )), array(
                                'style' => 'text-align: left;vertical-align: middle;',
                            ));
                            echo $this->Html->tag('td', $this->Number->currency($invoice['Invoice']['total'], Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                                'style' => 'text-align: right;vertical-align: middle;',
                            ));
                            echo $this->Html->tag('td', $this->Number->currency($totalPPH, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                                'style' => 'text-align: right;vertical-align: middle;',
                            ));
                            echo $this->Html->tag('td', $this->Common->combineDate($invoice['Invoice']['period_from'], $invoice['Invoice']['period_to']), array(
                                'style' => 'text-align: center;vertical-align: middle;',
                            ));
                            echo $this->Html->tag('td', $this->Common->combineDate($invoice['Invoice']['period_from'], $invoice['Invoice']['period_to'], 'M Y'), array(
                                'style' => 'text-align: center;vertical-align: middle;',
                            ));
                            echo $this->Html->tag('td', $totalUnit, array(
                                'style' => 'text-align: center;vertical-align: middle;',
                            ));
                            echo $this->Html->tag('td', ucfirst($invoice['Invoice']['tarif_type']), array(
                                'style' => 'text-align: center;vertical-align: middle;',
                            ));
                            echo $this->Html->tag('td', $dateTOP, array(
                                'style' => 'text-align: center;vertical-align: middle;',
                            ));
                            echo $this->Html->tag('td', implode('<br>', $datePayment), array(
                                'style' => 'text-align: center;vertical-align: middle;',
                            ));
                            echo $this->Html->tag('td', $this->Number->currency($totalPaid, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                                'style' => 'text-align: right;vertical-align: middle;',
                            ));
                            echo $this->Html->tag('td', $this->Html->tag('span', $invoiceStatus['text'], array(
                                'class' => $invoiceStatus['class']
                            )).$invoiceStatus['void_date'], array(
                                'style' => 'text-align: center;vertical-align: middle;',
                            ));
                    ?>
                </tr>
                <?php
                                $idx++;
                            }

                            $tdContent = $this->Html->tag('td', '');
                            $tdContent .= $this->Html->tag('td', '');
                            $tdContent .= $this->Html->tag('td', '');
                            $tdContent .= $this->Html->tag('td', $this->Html->tag('strong', __('Total'), array(
                                'style' => 'text-align: right;display: block'
                            )), array(
                                'style' => 'text-align: right;vertical-align: middle;',
                            ));
                            $tdContent .= $this->Html->tag('td', $this->Html->tag('strong', $this->Number->currency($totalTagihan, Configure::read('__Site.config_currency_code'), array('places' => 0))), array(
                                'style' => 'text-align: right;vertical-align: middle;',
                            ));
                            $tdContent .= $this->Html->tag('td', $this->Html->tag('strong', $this->Number->currency($grandtotalPPH, Configure::read('__Site.config_currency_code'), array('places' => 0))), array(
                                'style' => 'text-align: right;vertical-align: middle;',
                            ));
                            $tdContent .= $this->Html->tag('td', '');
                            $tdContent .= $this->Html->tag('td', '');
                            $tdContent .= $this->Html->tag('td', $this->Html->tag('strong', $totalQty), array(
                                'style' => 'text-align: center;vertical-align: middle;',
                            ));
                            $tdContent .= $this->Html->tag('td', '');
                            $tdContent .= $this->Html->tag('td', '');
                            $tdContent .= $this->Html->tag('td', '');
                            $tdContent .= $this->Html->tag('td', $this->Html->tag('strong', $this->Number->currency($totalTransfer, Configure::read('__Site.config_currency_code'), array('places' => 0))), array(
                                'style' => 'text-align: right;vertical-align: middle;',
                            ));
                            $tdContent .= $this->Html->tag('td', '', array(
                                'style' => $tdStyle,
                                'class' => 'text-center text-middle',
                            ));

                            echo $this->Html->tag('tr', $tdContent);

                            $tdContent = $this->Html->tag('td', '');
                            $tdContent .= $this->Html->tag('td', '');
                            $tdContent .= $this->Html->tag('td', '');
                            $tdContent .= $this->Html->tag('td', $this->Html->tag('strong', __('Total Void'), array(
                                'style' => 'text-align: right;display: block'
                            )), array(
                                'style' => 'text-align: right;vertical-align: middle;',
                            ));
                            $tdContent .= $this->Html->tag('td', $this->Html->tag('strong', $this->Number->currency($totalVoid, Configure::read('__Site.config_currency_code'), array('places' => 0))), array(
                                'style' => 'text-align: right;vertical-align: middle;',
                            ));
                            $tdContent .= $this->Html->tag('td', '');
                            $tdContent .= $this->Html->tag('td', '');
                            $tdContent .= $this->Html->tag('td', '');
                            $tdContent .= $this->Html->tag('td', '');
                            $tdContent .= $this->Html->tag('td', '');
                            $tdContent .= $this->Html->tag('td', '');
                            $tdContent .= $this->Html->tag('td', '');
                            $tdContent .= $this->Html->tag('td', $this->Html->tag('strong', $this->Number->currency($totalVoidTransfer, Configure::read('__Site.config_currency_code'), array('places' => 0))), array(
                                'style' => 'text-align: right;vertical-align: middle;',
                            ));
                            $tdContent .= $this->Html->tag('td', '');

                            echo $this->Html->tag('tr', $tdContent);
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
            }

            echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 14px;font-style: italic;margin-top: 10px;'
            ));
            
            if( $data_action != 'excel' ) {
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
                $totalTagihan = 0;
                $totalQty = 0;
                $totalTransfer = 0;
                $totalVoidTransfer = 0;
                $totalVoid = 0;
                $totalPPH = 0;
                $grandtotalPPH = 0;

                foreach ($invoices as $key => $invoice) {
                    $totalUnit = !empty($invoice['qty_unit'])?$invoice['qty_unit']:0;
                    $totalPPH = !empty($invoice['total_pph'])?$invoice['total_pph']:0;
                    $dateTOP = !empty($invoice['Invoice']['term_of_payment'])?date('d/m/Y', strtotime(sprintf('+%s day', $invoice['Invoice']['term_of_payment']), strtotime($invoice['Invoice']['invoice_date']))):'-';
                    $datePayment = array();
                    $totalPaid = 0;
                    $invoiceStatus = $this->Common->getInvoiceStatus( $invoice );
                    $totalTagihan += $invoice['Invoice']['total'];
                    $totalQty += $totalUnit;
                    $grandtotalPPH += $totalPPH;

                    if( !empty($invoice['InvoicePaymentDate']) ) {
                        foreach ($invoice['InvoicePaymentDate'] as $key => $dtPaid) {
                            $datePayment[] = $this->Common->customDate($dtPaid, 'd/m/Y');
                        }
                    }

                    if( !empty($invoice[0]['total_payment']) ) {
                        $totalPaid = $invoice[0]['total_payment'];
                    }

                    $totalTransfer += $totalPaid;

                    if( strtolower($invoiceStatus['text']) == 'void' ) {
                        $totalVoidTransfer += $totalPaid;
                        $totalVoid += $invoice['Invoice']['total'];
                    }

                    $content = $this->Html->tag('td', $no, array(
                        'style' => 'text-align: center;vertical-align: middle;',
                    ));
                    $content .= $this->Html->tag('td', $this->Common->customDate($invoice['Invoice']['invoice_date'], 'd/m/Y'), array(
                        'style' => 'text-align: center;vertical-align: middle;',
                    ));
                    $content .= $this->Html->tag('td', !empty($invoice['CustomerNoType']['name'])?$invoice['CustomerNoType']['name']:false, array(
                        'style' => 'text-align: left;vertical-align: middle;',
                    ));
                    $content .= $this->Html->tag('td', $invoice['Invoice']['no_invoice'], array(
                        'style' => 'text-align: left;vertical-align: middle;',
                    ));
                    $content .= $this->Html->tag('td', $this->Number->currency($invoice['Invoice']['total'], Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                        'style' => 'text-align: right;vertical-align: middle;',
                    ));
                    $content .= $this->Html->tag('td', $this->Number->currency($totalPPH, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                        'style' => 'text-align: right;vertical-align: middle;',
                    ));
                    $content .= $this->Html->tag('td', $this->Common->combineDate($invoice['Invoice']['period_from'], $invoice['Invoice']['period_to']), array(
                        'style' => 'text-align: center;vertical-align: middle;',
                    ));
                    $content .= $this->Html->tag('td', $this->Common->combineDate($invoice['Invoice']['period_from'], $invoice['Invoice']['period_to'], 'M Y'), array(
                        'style' => 'text-align: center;vertical-align: middle;',
                    ));
                    $content .= $this->Html->tag('td', $totalUnit, array(
                        'style' => 'text-align: center;vertical-align: middle;',
                    ));
                    $content .= $this->Html->tag('td', ucfirst($invoice['Invoice']['tarif_type']), array(
                        'style' => 'text-align: center;vertical-align: middle;',
                    ));
                    $content .= $this->Html->tag('td', $dateTOP, array(
                        'style' => 'text-align: center;',
                    ));
                    $content .= $this->Html->tag('td', implode('<br>', $datePayment), array(
                        'style' => 'text-align: center;vertical-align: middle;',
                    ));
                    $content .= $this->Html->tag('td', $this->Number->currency($totalPaid, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                        'style' => 'text-align: right;vertical-align: middle;',
                    ));
                    $content .= $this->Html->tag('td', $invoiceStatus['text'].$invoiceStatus['void_date'], array(
                        'style' => 'text-align: center;vertical-align: middle;',
                    ));

                    $each_loop_message .= $this->Html->tag('tr', $content);
                    $no++;
                }

                $tdContent = $this->Html->tag('td', $this->Html->tag('strong', __('Total'), array(
                    'style' => 'text-align: right;display: block'
                )), array(
                    'style' => 'text-align: right;vertical-align: middle;',
                    'colspan' => 4,
                ));
                $tdContent .= $this->Html->tag('td', $this->Html->tag('strong', $this->Number->currency($totalTagihan, Configure::read('__Site.config_currency_code'), array('places' => 0))), array(
                    'style' => 'text-align: right;vertical-align: middle;',
                ));
                $tdContent .= $this->Html->tag('td', $this->Html->tag('strong', $this->Number->currency($grandtotalPPH, Configure::read('__Site.config_currency_code'), array('places' => 0))), array(
                    'style' => 'text-align: right;vertical-align: middle;',
                ));
                $tdContent .= $this->Html->tag('td', '', array(
                    'colspan' => 2,
                ));
                $tdContent .= $this->Html->tag('td', $this->Html->tag('strong', $totalQty), array(
                    'style' => 'text-align: center;vertical-align: middle;',
                ));
                $tdContent .= $this->Html->tag('td', $this->Html->tag('strong', $this->Number->currency($totalTransfer, Configure::read('__Site.config_currency_code'), array('places' => 0))), array(
                    'style' => 'text-align: right;vertical-align: middle;',
                    'colspan' => 4,
                ));
                $tdContent .= $this->Html->tag('td', '');
                $each_loop_message .= $this->Html->tag('tr', $tdContent);

                $tdContent = $this->Html->tag('td', $this->Html->tag('strong', __('Total Void'), array(
                    'style' => 'text-align: right;display: block'
                )), array(
                    'style' => 'text-align: right;vertical-align: middle;',
                    'colspan' => 4,
                ));
                $tdContent .= $this->Html->tag('td', $this->Html->tag('strong', $this->Number->currency($totalVoid, Configure::read('__Site.config_currency_code'), array('places' => 0))), array(
                    'style' => 'text-align: right;vertical-align: middle;',
                ));
                $tdContent .= $this->Html->tag('td', $this->Html->tag('strong', $this->Number->currency($totalVoidTransfer, Configure::read('__Site.config_currency_code'), array('places' => 0))), array(
                    'style' => 'text-align: right;vertical-align: middle;',
                    'colspan' => 8,
                ));
                $tdContent .= $this->Html->tag('td', '');
                $each_loop_message .= $this->Html->tag('tr', $tdContent);
            }

            $date_title = $sub_module_title;
            $print_label = $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 24px;font-style: italic;margin-top: 10px;'
            ));
$tbl = <<<EOD

      <div class="clearfix container_16" id="content">

        <h2 class="grid_8" style="text-align: center;">$date_title</h2>
        <table cellpadding="2" cellspacing="2" nobr="true" style="$table">
            <thead>
                <tr style="$table_tr_head">
                    <th style="text-align: center;vertical-align: middle;">No.</th>
                    <th style="text-align: center;vertical-align: middle;">Tgl Kwitansi</th>
                    <th style="text-align: center;vertical-align: middle;">Customer</th>
                    <th style="text-align: center;vertical-align: middle;">No. Invoice</th>
                    <th style="text-align: center;vertical-align: middle;">Total Tagihan</th>
                    <th style="text-align: center;vertical-align: middle;">Total PPH</th>
                    <th style="text-align: center;vertical-align: middle;">Periode Tanggal</th>
                    <th style="text-align: center;vertical-align: middle;">Periode Bulan</th>
                    <th style="text-align: center;vertical-align: middle;">Qty Unit</th>
                    <th style="text-align: center;vertical-align: middle;">Jenis</th>
                    <th style="text-align: center;vertical-align: middle;">Tgl J.Tempo</th>
                    <th style="text-align: center;vertical-align: middle;">Tgl Bayar</th>
                    <th style="text-align: center;vertical-align: middle;">Total Transfer</th>
                    <th style="text-align: center;vertical-align: middle;">Status</th>
                </tr>
            </thead>
            <tbody>          
                $each_loop_message
            </tbody>
        </table> 
        <br>
        $print_label
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
<script type="text/javascript">
    function cellStyler(value,row,index){
        var status_doc = $(value).filter('.label').html() + '';
        status_doc = status_doc.toLowerCase();

        if( status_doc == 'unpaid' ) {
            return 'background-color:#f5f5f5;';
        }
    }
</script>