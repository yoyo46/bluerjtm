<?php 
        $dataColumns = array(
            'code' => array(
                'name' => __('Customer'),
                'style' => 'text-align: center;width: 150px;vertical-align: middle;',
                'data-options' => 'field:\'code\',width:120',
                'field_model' => 'Customer.code',
                'display' => true,
                'fix_column' => true
            ),
            'saldo' => array(
                'name' => sprintf(__('SALDO %s'), $this->Common->customDate($lastMonth, 'M Y')),
                'style' => 'text-align: center;width: 120px;vertical-align: middle;',
                'data-options' => 'field:\'saldo\',width:120',
                'align' => 'right',
                'field_model' => false,
                'display' => true,
            ),
            'invoice' => array(
                'name' => sprintf(__('KW. %s'), $this->Common->getCombineDate($fromMonthYear, $toMonthYear, 'short')),
                'style' => 'text-align: center;width: 120px;vertical-align: middle;',
                'data-options' => 'field:\'invoice\',width:120',
                'align' => 'right',
                'field_model' => false,
                'display' => true,
            ),
            'ar' => array(
                'name' => __('A/R TERBAYAR'),
                'style' => 'text-align: center;width: 120px;vertical-align: middle;',
                'data-options' => 'field:\'ar\',width:120',
                'align' => 'right',
                'field_model' => false,
                'display' => true,
            ),
            'void' => array(
                'name' => __('KW. Batal'),
                'style' => 'text-align: center;width: 120px;vertical-align: middle;',
                'data-options' => 'field:\'void\',width:120',
                'align' => 'right',
                'field_model' => false,
                'display' => true,
            ),
            'saldo_ar' => array(
                'name' => __('SALDO A/R'),
                'style' => 'text-align: center;width: 140px;vertical-align: middle;',
                'data-options' => 'field:\'saldo_ar\',width:120',
                'align' => 'right',
                'field_model' => false,
                'display' => true,
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table', $data_action );

        if( empty($data_action) || ( !empty($data_action) && $data_action == 'excel' ) ){
            $this->Html->addCrumb($sub_module_title);
            $addStyle = 'width: 100%;height: 550px;';
            $tdStyle = '';
            $border = 0;
            $headerRowspan = false;
            $addClass = 'easyui-datagrid';

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
                echo $this->element('blocks/revenues/search_report_revenue_monthly');
?>
<section class="content invoice">
    <?php 
            }
    ?>
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
    </h2>
    <?php 
            if( $data_action != 'excel' ) {
                echo $this->Common->_getPrint(array(
                    '_attr' => array(
                        'escape' => false,
                    ),
                ));
    ?>
    <div class="table-responsive">
        <?php 
                }
        ?>
        <table id="tt" class="table table-bordered <?php echo $addClass; ?>" style="<?php echo $addStyle; ?>" singleSelect="true" border="<?php echo $border; ?>">
            <thead frozen="true">
                <tr>
                    <?php
                            if( !empty($fieldColumn) ) {
                                echo $fieldColumn;
                            }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                        if(!empty($customers)){
                            $totalSaldoLastMonth = 0;
                            $totalKW = 0;
                            $totalAR = 0;
                            $totalKWVoid = 0;
                            $totalSaldoAR = 0;

                            foreach ($customers as $key => $customer) {
                                $invLastMonth = !empty($customer['InvLastMonth'][0]['total'])?$customer['InvLastMonth'][0]['total']:0;
                                $invVoidLastMonth = !empty($customer['InvVoidLastMonth'][0]['total'])?$customer['InvVoidLastMonth'][0]['total']:0;
                                $invPaidLastMonth = !empty($customer['InvPaidLastMonth'][0]['total'])?$customer['InvPaidLastMonth'][0]['total']:0;
                                $invoiceTotal = !empty($customer['InvoiceTotal'][0]['total'])?$customer['InvoiceTotal'][0]['total']:0;
                                $invoicePaymentTotal = !empty($customer['InvoicePaymentTotal'][0]['total'])?$customer['InvoicePaymentTotal'][0]['total']:0;
                                $invoiceVoidTotal = !empty($customer['InvoiceVoidTotal'][0]['total'])?$customer['InvoiceVoidTotal'][0]['total']:0;
                                $total = $invLastMonth + $invPaidLastMonth - $invVoidLastMonth + $invoiceTotal - $invoicePaymentTotal - $invoiceVoidTotal;
                                $totalSaldoLastMonth += $invLastMonth + $invPaidLastMonth - $invVoidLastMonth;
                                $totalKW += $invoiceTotal;
                                $totalAR += $invoicePaymentTotal;
                                $totalKWVoid += $invoiceVoidTotal;
                                $totalSaldoAR += $total;
                ?>
                <tr>
                    <?php 
                            echo $this->Html->tag('td', $customer['Customer']['code']);
                            echo $this->Html->tag('td', $this->Number->format($invLastMonth+$invPaidLastMonth-$invVoidLastMonth, '', array('places' => 0)), array(
                                'style' => 'text-align: right;vertical-align: middle;',
                            ));
                            echo $this->Html->tag('td', $this->Number->format($invoiceTotal, '', array('places' => 0)), array(
                                'style' => 'text-align: right;vertical-align: middle;',
                            ));
                            echo $this->Html->tag('td', $this->Number->format($invoicePaymentTotal, '', array('places' => 0)), array(
                                'style' => 'text-align: right;vertical-align: middle;',
                            ));
                            echo $this->Html->tag('td', $this->Number->format($invoiceVoidTotal, '', array('places' => 0)), array(
                                'style' => 'text-align: right;vertical-align: middle;',
                            ));
                            echo $this->Html->tag('td', $this->Number->format($total, '', array('places' => 0)), array(
                                'style' => 'text-align: right;vertical-align: middle;',
                            ));
                    ?>
                </tr>
                <?php
                            }
                ?>

                <tr>
                    <?php 
                            echo $this->Html->tag('td', $this->Html->tag('strong', __('Total')), array(
                                'style' => 'text-align: right;vertical-align: middle;',
                            ));
                            echo $this->Html->tag('td', $this->Html->tag('strong', $this->Number->format($totalSaldoLastMonth, '', array('places' => 0))), array(
                                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
                            ));
                            echo $this->Html->tag('td', $this->Html->tag('strong', $this->Number->format($totalKW, '', array('places' => 0))), array(
                                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
                            ));
                            echo $this->Html->tag('td', $this->Html->tag('strong', $this->Number->format($totalAR, '', array('places' => 0))), array(
                                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
                            ));
                            echo $this->Html->tag('td', $this->Html->tag('strong', $this->Number->format($totalKWVoid, '', array('places' => 0))), array(
                                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
                            ));
                            echo $this->Html->tag('td', $this->Html->tag('strong', $this->Number->format($totalSaldoAR, '', array('places' => 0))), array(
                                'style' => 'text-align: right;vertical-align: middle;font-weight:bold;',
                            ));
                    ?>
                </tr>
                <?php
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

            echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $User['full_name'])), array(
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
            $tdStyle = 'text-align: center;vertical-align: middle;';
            $contentTr = '';

            if(!empty($customers)){
                foreach ($customers as $key => $customer) {
                    $invLastMonth = !empty($customer['InvLastMonth'][0]['total'])?$customer['InvLastMonth'][0]['total']:0;
                    $invVoidLastMonth = !empty($customer['InvVoidLastMonth'][0]['total'])?$customer['InvVoidLastMonth'][0]['total']:0;
                    $invPaidLastMonth = !empty($customer['InvPaidLastMonth'][0]['total'])?$customer['InvPaidLastMonth'][0]['total']:0;
                    $invoiceTotal = !empty($customer['InvoiceTotal'][0]['total'])?$customer['InvoiceTotal'][0]['total']:0;
                    $invoicePaymentTotal = !empty($customer['InvoicePaymentTotal'][0]['total'])?$customer['InvoicePaymentTotal'][0]['total']:0;
                    $invoiceVoidTotal = !empty($customer['InvoiceVoidTotal'][0]['total'])?$customer['InvoiceVoidTotal'][0]['total']:0;
                    $total = $invLastMonth + $invPaidLastMonth - $invVoidLastMonth + $invoiceTotal + $invoicePaymentTotal - $invoiceVoidTotal;

                    $contentTd = $this->Html->tag('td', $customer['Customer']['code']);
                    $contentTd .= $this->Html->tag('td', $this->Number->format($invLastMonth+$invPaidLastMonth-$invVoidLastMonth, '', array('places' => 0)), array(
                        'style' => 'text-align: right;vertical-align: middle;',
                    ));
                    $contentTd .= $this->Html->tag('td', $this->Number->format($invoiceTotal, '', array('places' => 0)), array(
                        'style' => 'text-align: right;vertical-align: middle;',
                    ));
                    $contentTd .= $this->Html->tag('td', $this->Number->format($invoicePaymentTotal, '', array('places' => 0)), array(
                        'style' => 'text-align: right;vertical-align: middle;',
                    ));
                    $contentTd .= $this->Html->tag('td', $this->Number->format($invoiceVoidTotal, '', array('places' => 0)), array(
                        'style' => 'text-align: right;vertical-align: middle;',
                    ));
                    $contentTd .= $this->Html->tag('td', $this->Number->format($total, '', array('places' => 0)), array(
                        'style' => 'text-align: right;vertical-align: middle;',
                    ));

                    $contentTr .= $this->Html->tag('tr', $contentTd);
                }
            }

            $date_title = $sub_module_title;
            $print_label = $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $User['full_name'])), array(
                'style' => 'font-size: 24px;font-style: italic;margin-top: 10px;'
            ));
$tbl = <<<EOD

      <div class="clearfix container_16" id="content">
        <h2 class="grid_8" style="text-align: center;">$date_title</h2>
        <table cellpadding="2" cellspacing="2" nobr="true" style="$table;display: block;">
            <thead>
                <tr style="$table_tr_head">
                    $fieldColumn
                </tr>
            </thead>
            <tbody>
                $contentTr
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