<?php 
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
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
            echo $this->Form->create('Ttuj', array(
                'url'=> $this->Html->url( array(
                    'controller' => 'revenues',
                    'action' => 'search',
                    'report_customers'
                )), 
                'role' => 'form',
                'inputDefaults' => array('div' => false),
            ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <?php 
                        echo $this->Form->label('fromMonth', __('Periode Bulan'));
                ?>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php 
                                    echo $this->Form->month('from', array(
                                        'label'=> false, 
                                        'class'=>'form-control',
                                        'required' => false,
                                        'empty' => false,
                                        'value' => $fromMonth,
                                    ));
                            ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php 
                                    echo $this->Form->month('to', array(
                                        'label'=> false, 
                                        'class'=>'form-control',
                                        'required' => false,
                                        'empty' => false,
                                        'value' => $toMonth,
                                    ));
                            ?>
                        </div>
                    </div>
                </div>
                <?php 
                        echo $this->Form->label('fromMonth', __('Periode tahun'));
                ?>
                <div class="form-group">
                    <?php 
                            echo $this->Form->year('from', 1949, date('Y'), array(
                                'label'=> __('Periode tahun'), 
                                'class'=>'form-control',
                                'empty' => false,
                                'value' => $fromYear,
                            ));
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('customer', array(
                                'label'=> __('Customer'), 
                                'class'=>'form-control',
                                'empty' => __('Pilih Customer'),
                                'options' => $customerList,
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            // Custom Otorisasi
                            echo $this->Common->getCheckboxBranch();
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
                                'action' => 'report_customers', 
                            ), array(
                                'escape' => false, 
                                'class'=> 'btn btn-default btn-sm',
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
                    $urlDefault = $this->passedArgs;
                    $urlDefault['controller'] = 'revenues';
                    $urlDefault['action'] = 'report_customers';

                    $urlExcel = $urlDefault;
                    $urlExcel[] = 'excel';
                    echo $this->Html->link('<i class="fa fa-download"></i> Download Excel', $urlExcel, array(
                        'escape' => false,
                        'class' => 'btn btn-success pull-right'
                    ));

                    $urlPdf = $urlDefault;
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
                            echo $this->Html->tag('th', $this->Common->getSorting('Customer.code', __('Customer')), array(
                                'style' => 'width: 150px;'.$tdStyle,
                                'class' => 'text-center text-middle',
                            ));
                            echo $this->Html->tag('th', sprintf(__('AVG %s'), $avgYear), array(
                                'style' => 'width: 100px;'.$tdStyle,
                                'class' => 'text-center text-middle',
                            ));
                            echo $this->Html->tag('th', __('Bulan'), array(
                                'style' => 'width: 100px;'.$tdStyle,
                                'class' => 'text-center text-middle',
                            ));
                            echo $this->Html->tag('th', __('Saldo Awal'), array(
                                'style' => 'width: 150px;'.$tdStyle,
                                'class' => 'text-center text-middle',
                            ));
                            echo $this->Html->tag('th', __('Tagihan'), array(
                                'style' => 'width: 150px;'.$tdStyle,
                                'class' => 'text-center text-middle',
                            ));
                            echo $this->Html->tag('th', __('Total'), array(
                                'style' => 'width: 150px;'.$tdStyle,
                                'class' => 'text-center text-middle',
                            ));
                            echo $this->Html->tag('th', __('Collection'), array(
                                'style' => 'width: 150px;'.$tdStyle,
                                'class' => 'text-center text-middle',
                            ));
                            echo $this->Html->tag('th', __('Kw. Batal'), array(
                                'style' => 'width: 150px;'.$tdStyle,
                                'class' => 'text-center text-middle',
                            ));
                            echo $this->Html->tag('th', __('Saldo Akhir'), array(
                                'style' => 'width: 150px;'.$tdStyle,
                                'class' => 'text-center text-middle',
                            ));
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                        if(!empty($customers)){
                            $grandtotalSaldoAwal = 0;
                            $grandtotalTagihan = 0;
                            $grandtotal = 0;
                            $grandtotalColection = 0;
                            $grandtotalKwBatal = 0;
                            $grandtotalSaldoAkhir = 0;

                            foreach ($customers as $key => $value) {
                                $id = $value['Customer']['id'];
                                $customer_name = !empty($value['Customer']['code'])?$value['Customer']['code']:'-';
                                $flag = false;
                                $grandtotalSaldoAwalCust = 0;
                                $grandtotalTagihanCust = 0;
                                $grandtotalCust = 0;
                                $grandtotalColectionCust = 0;
                                $grandtotalKwBatalCust = 0;
                                $grandtotalSaldoAkhirCust = 0;
                ?>
                <!-- <tr> -->
                    <?php 
                            $saldoAwal = 0;
                            if( isset($totalCnt) ) {
                                for ($i=0; $i <= $totalCnt; $i++) {
                                    $monthName = date('F', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear));
                                    $monthDt = date('Y-m', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear));
                                    $beforeMonthDt = date('Y-m', mktime(0, 0, 0, ($fromMonth+$i)-1, 1, $fromYear));
                                    $monthTotal = !empty($value['Invoice'][$monthDt])?$value['Invoice'][$monthDt]:0;
                                    $beforeMonthTotal = !empty($value['InvoiceBefore'][$beforeMonthDt])?$value['InvoiceBefore'][$beforeMonthDt]:$saldoAwal;
                                    $monthPaidTotal = !empty($value['InvoicePayment'][$monthDt])?$value['InvoicePayment'][$monthDt]:0;
                                    $monthVoidTotal = !empty($value['InvoiceVoid'][$monthDt])?$value['InvoiceVoid'][$monthDt]:0;
                                    $totalInvoice = $monthTotal+$beforeMonthTotal;
                                    $totalSaldo = $totalInvoice-$monthPaidTotal-$monthVoidTotal;

                                    $grandtotalSaldoAwal += $beforeMonthTotal;
                                    $grandtotalTagihan += $monthTotal;
                                    $grandtotal += $totalInvoice;
                                    $grandtotalColection += $monthPaidTotal;
                                    $grandtotalKwBatal += $monthVoidTotal;
                                    $grandtotalSaldoAkhir += $totalSaldo;

                                    $grandtotalSaldoAwalCust += $beforeMonthTotal;
                                    $grandtotalTagihanCust += $monthTotal;
                                    $grandtotalCust += $totalInvoice;
                                    $grandtotalColectionCust += $monthPaidTotal;
                                    $grandtotalKwBatalCust += $monthVoidTotal;
                                    $grandtotalSaldoAkhirCust += $totalSaldo;

                                    echo '<tr>';
                                    if( !$flag ) {
                                        echo $this->Html->tag('td', $customer_name, array(
                                            'rowspan' => $totalCnt+2,
                                            'style' => 'vertical-align: middle;',
                                        ));
                                        echo $this->Html->tag('td', $this->Number->format($value['InvoiceYear'], '', array('places' => 0)), array(
                                            'rowspan' => $totalCnt+2,
                                            'style' => 'vertical-align: middle;text-align:right;',
                                        ));
                                    }

                                    echo $this->Html->tag('td', $monthName);
                                    echo $this->Html->tag('td', $this->Number->format($beforeMonthTotal, '', array('places' => 0)), array(
                                        'style' => 'text-align: right;',
                                    ));
                                    echo $this->Html->tag('td', $this->Number->format($monthTotal, '', array('places' => 0)), array(
                                        'style' => 'text-align: right;',
                                    ));
                                    echo $this->Html->tag('td', $this->Number->format($totalInvoice, '', array('places' => 0)), array(
                                        'style' => 'text-align: right;',
                                    ));
                                    echo $this->Html->tag('td', $this->Number->format($monthPaidTotal, '', array('places' => 0)), array(
                                        'style' => 'text-align: right;',
                                    ));
                                    echo $this->Html->tag('td', $this->Number->format($monthVoidTotal, '', array('places' => 0)), array(
                                        'style' => 'text-align: right;',
                                    ));
                                    echo $this->Html->tag('td', $this->Number->format($totalSaldo, '', array('places' => 0)), array(
                                        'style' => 'text-align: right;',
                                    ));
                                    echo '</tr>';
                                    $flag = true;
                                    $saldoAwal = $totalSaldo;
                                }

                                echo '<tr>';
                                echo $this->Html->tag('td', __('Total'), array(
                                    'style' => 'font-weight: bold;text-align: right;',
                                    'colspan' => 2,
                                ));
                                // echo $this->Html->tag('td', $this->Number->format($grandtotalSaldoAwalCust, '', array('places' => 0)), array(
                                //     'style' => 'font-weight: bold;text-align: right;',
                                // ));
                                echo $this->Html->tag('td', $this->Number->format($grandtotalTagihanCust, '', array('places' => 0)), array(
                                    'style' => 'font-weight: bold;text-align: right;',
                                ));
                                echo $this->Html->tag('td', '');
                                // echo $this->Html->tag('td', $this->Number->format($grandtotalCust, '', array('places' => 0)), array(
                                //     'style' => 'font-weight: bold;text-align: right;',
                                // ));
                                echo $this->Html->tag('td', $this->Number->format($grandtotalColectionCust, '', array('places' => 0)), array(
                                    'style' => 'font-weight: bold;text-align: right;',
                                ));
                                echo $this->Html->tag('td', $this->Number->format($grandtotalKwBatalCust, '', array('places' => 0)), array(
                                    'style' => 'font-weight: bold;text-align: right;',
                                ));
                                // echo $this->Html->tag('td', $this->Number->format($grandtotalSaldoAkhirCust, '', array('places' => 0)), array(
                                //     'style' => 'font-weight: bold;text-align: right;',
                                // ));
                                echo $this->Html->tag('td', '');
                                echo '</tr>';
                            }
                    ?>
                <!-- </tr> -->
                <?php
                            }

                            echo '<tr>';
                            echo $this->Html->tag('td', __('Grand Total:'), array(
                                'style' => 'font-weight: bold;text-align: right;',
                                'colspan' => 4,
                            ));
                            // echo $this->Html->tag('td', $this->Number->format($grandtotalSaldoAwal, '', array('places' => 0)), array(
                            //     'style' => 'font-weight: bold;text-align: right;',
                            // ));
                            echo $this->Html->tag('td', $this->Number->format($grandtotalTagihan, '', array('places' => 0)), array(
                                'style' => 'font-weight: bold;text-align: right;',
                            ));
                            echo $this->Html->tag('td', '');
                            // echo $this->Html->tag('td', $this->Number->format($grandtotal, '', array('places' => 0)), array(
                            //     'style' => 'font-weight: bold;text-align: right;',
                            // ));
                            echo $this->Html->tag('td', $this->Number->format($grandtotalColection, '', array('places' => 0)), array(
                                'style' => 'font-weight: bold;text-align: right;',
                            ));
                            echo $this->Html->tag('td', $this->Number->format($grandtotalKwBatal, '', array('places' => 0)), array(
                                'style' => 'font-weight: bold;text-align: right;',
                            ));
                            echo $this->Html->tag('td', '');
                            // echo $this->Html->tag('td', $this->Number->format($grandtotalSaldoAkhir, '', array('places' => 0)), array(
                            //     'style' => 'font-weight: bold;text-align: right;',
                            // ));
                            echo '</tr>';
                        }
                ?>
            </tbody>
        </table>
        <?php 
                if( $data_action != 'excel' ) {
                    if(empty($customers)){
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
            $tdStyle = 'text-align: center;';
            $each_loop_message = '';

            $contentHeader = $this->Html->tag('th', $this->Common->getSorting('Customer.code', __('Customer')), array(
                'style' => 'width: 150px;'.$tdStyle,
            ));
            $contentHeader .= $this->Html->tag('th', __('Bulan'), array(
                'style' => 'width: 100px;'.$tdStyle,
            ));
            $contentHeader .= $this->Html->tag('th', __('Saldo Awal'), array(
                'style' => 'width: 150px;'.$tdStyle,
            ));
            $contentHeader .= $this->Html->tag('th', __('Tagihan'), array(
                'style' => 'width: 150px;'.$tdStyle,
            ));
            $contentHeader .= $this->Html->tag('th', __('Total'), array(
                'style' => 'width: 150px;'.$tdStyle,
            ));
            $contentHeader .= $this->Html->tag('th', __('Collection'), array(
                'style' => 'width: 150px;'.$tdStyle,
            ));
            $contentHeader .= $this->Html->tag('th', __('Kw. Batal'), array(
                'style' => 'width: 150px;'.$tdStyle,
            ));
            $contentHeader .= $this->Html->tag('th', __('Saldo Akhir'), array(
                'style' => 'width: 150px;'.$tdStyle,
            ));

            if(!empty($customers)){
                $grandtotalSaldoAwal = 0;
                $grandtotalTagihan = 0;
                $grandtotal = 0;
                $grandtotalColection = 0;
                $grandtotalKwBatal = 0;
                $grandtotalSaldoAkhir = 0;

                foreach ($customers as $key => $value) {
                    $id = $value['Customer']['id'];
                    $customer_name = !empty($value['Customer']['code'])?$value['Customer']['code']:'-';
                    $flag = false;
                    $grandtotalSaldoAwalCust = 0;
                    $grandtotalTagihanCust = 0;
                    $grandtotalCust = 0;
                    $grandtotalColectionCust = 0;
                    $grandtotalKwBatalCust = 0;
                    $grandtotalSaldoAkhirCust = 0;
                    
                    if( isset($totalCnt) ) {
                        for ($i=0; $i <= $totalCnt; $i++) {
                            $monthName = date('F', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear));
                            $monthDt = date('Y-m', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear));
                            $beforeMonthDt = date('Y-m', mktime(0, 0, 0, ($fromMonth+$i)-1, 1, $fromYear));
                            $monthTotal = !empty($value['Invoice'][$monthDt])?$value['Invoice'][$monthDt]:0;
                            $beforeMonthTotal = !empty($value['InvoiceBefore'][$beforeMonthDt])?$value['InvoiceBefore'][$beforeMonthDt]:0;
                            $monthPaidTotal = !empty($value['InvoicePayment'][$monthDt])?$value['InvoicePayment'][$monthDt]:0;
                            $monthVoidTotal = !empty($value['InvoiceVoid'][$monthDt])?$value['InvoiceVoid'][$monthDt]:0;
                            $totalInvoice = $monthTotal+$beforeMonthTotal;
                            $totalSaldo = $totalInvoice-$monthPaidTotal-$monthVoidTotal;

                            $grandtotalSaldoAwal += $beforeMonthTotal;
                            $grandtotalTagihan += $monthTotal;
                            $grandtotal += $totalInvoice;
                            $grandtotalColection += $monthPaidTotal;
                            $grandtotalKwBatal += $monthVoidTotal;
                            $grandtotalSaldoAkhir += $totalSaldo;

                            $grandtotalSaldoAwalCust += $beforeMonthTotal;
                            $grandtotalTagihanCust += $monthTotal;
                            $grandtotalCust += $totalInvoice;
                            $grandtotalColectionCust += $monthPaidTotal;
                            $grandtotalKwBatalCust += $monthVoidTotal;
                            $grandtotalSaldoAkhirCust += $totalSaldo;

                            $each_loop_message .= '<tr>';
                            if( !$flag ) {
                                $each_loop_message .= $this->Html->tag('td', $customer_name, array(
                                    'rowspan' => $totalCnt+2,
                                    'style' => 'vertical-align: middle;',
                                ));
                            }

                            $each_loop_message .= $this->Html->tag('td', $monthName);
                            $each_loop_message .= $this->Html->tag('td', $this->Number->format($beforeMonthTotal, '', array('places' => 0)), array(
                                'style' => 'text-align: right;',
                            ));
                            $each_loop_message .= $this->Html->tag('td', $this->Number->format($monthTotal, '', array('places' => 0)), array(
                                'style' => 'text-align: right;',
                            ));
                            $each_loop_message .= $this->Html->tag('td', $this->Number->format($totalInvoice, '', array('places' => 0)), array(
                                'style' => 'text-align: right;',
                            ));
                            $each_loop_message .= $this->Html->tag('td', $this->Number->format($monthPaidTotal, '', array('places' => 0)), array(
                                'style' => 'text-align: right;',
                            ));
                            $each_loop_message .= $this->Html->tag('td', $this->Number->format($monthVoidTotal, '', array('places' => 0)), array(
                                'style' => 'text-align: right;',
                            ));
                            $each_loop_message .= $this->Html->tag('td', $this->Number->format($totalSaldo, '', array('places' => 0)), array(
                                'style' => 'text-align: right;',
                            ));
                            $each_loop_message .= '</tr>';
                            $flag = true;
                        }

                        $each_loop_message .= '<tr>';
                        $each_loop_message .= $this->Html->tag('td', __('Total'), array(
                            'style' => 'font-weight: bold;text-align: left;',
                        ));
                        $each_loop_message .= $this->Html->tag('td', $this->Number->format($grandtotalSaldoAwalCust, '', array('places' => 0)), array(
                            'style' => 'font-weight: bold;text-align: right;',
                        ));
                        $each_loop_message .= $this->Html->tag('td', $this->Number->format($grandtotalTagihanCust, '', array('places' => 0)), array(
                            'style' => 'font-weight: bold;text-align: right;',
                        ));
                        $each_loop_message .= $this->Html->tag('td', $this->Number->format($grandtotalCust, '', array('places' => 0)), array(
                            'style' => 'font-weight: bold;text-align: right;',
                        ));
                        $each_loop_message .= $this->Html->tag('td', $this->Number->format($grandtotalColectionCust, '', array('places' => 0)), array(
                            'style' => 'font-weight: bold;text-align: right;',
                        ));
                        $each_loop_message .= $this->Html->tag('td', $this->Number->format($grandtotalKwBatalCust, '', array('places' => 0)), array(
                            'style' => 'font-weight: bold;text-align: right;',
                        ));
                        $each_loop_message .= $this->Html->tag('td', $this->Number->format($grandtotalSaldoAkhirCust, '', array('places' => 0)), array(
                            'style' => 'font-weight: bold;text-align: right;',
                        ));
                        $each_loop_message .= '</tr>';
                    }
                }

                $each_loop_message .= '<tr>';
                $each_loop_message .= $this->Html->tag('td', __('Grand Total:'), array(
                    'style' => 'font-weight: bold;text-align: center;',
                    'colspan' => 2,
                ));
                $each_loop_message .= $this->Html->tag('td', $this->Number->format($grandtotalSaldoAwal, '', array('places' => 0)), array(
                    'style' => 'font-weight: bold;text-align: right;',
                ));
                $each_loop_message .= $this->Html->tag('td', $this->Number->format($grandtotalTagihan, '', array('places' => 0)), array(
                    'style' => 'font-weight: bold;text-align: right;',
                ));
                $each_loop_message .= $this->Html->tag('td', $this->Number->format($grandtotal, '', array('places' => 0)), array(
                    'style' => 'font-weight: bold;text-align: right;',
                ));
                $each_loop_message .= $this->Html->tag('td', $this->Number->format($grandtotalColection, '', array('places' => 0)), array(
                    'style' => 'font-weight: bold;text-align: right;',
                ));
                $each_loop_message .= $this->Html->tag('td', $this->Number->format($grandtotalKwBatal, '', array('places' => 0)), array(
                    'style' => 'font-weight: bold;text-align: right;',
                ));
                $each_loop_message .= $this->Html->tag('td', $this->Number->format($grandtotalSaldoAkhir, '', array('places' => 0)), array(
                    'style' => 'font-weight: bold;text-align: right;',
                ));
                $each_loop_message .= '</tr>';
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
                    $contentHeader
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