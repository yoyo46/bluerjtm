<?php 
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $dataColumns = array(
            'noref' => array(
                'name' => __('No. Ref'),
                'field_model' => 'Cashbank.id',
                'rowspan' => 2,
                'display' => true,
            ),
            'nodoc' => array(
                'name' => __('No. Doc'),
                'field_model' => 'Cashbank.nodoc',
                'rowspan' => 2,
                'display' => true,
            ),
            'tgl_cash_bank' => array(
                'name' => __('Tanggal'),
                'field_model' => 'Cashbank.tgl_cash_bank',
                'rowspan' => 2,
                'display' => true,
            ),
            'coa_name' => array(
                'name' => __('Acc'),
                'field_model' => 'Coa.name',
                'rowspan' => 2,
                'display' => true,
            ),
            'atas_nama' => array(
                'name' => __('Diterima/Dibayar kepada'),
                'field_model' => false,
                'rowspan' => 2,
                'display' => true,
            ),
            'description' => array(
                'name' => __('Keterangan'),
                'field_model' => 'Cashbank.description',
                'rowspan' => 2,
                'display' => true,
            ),
            'total' => array(
                'name' => __('Total'),
                'field_model' => false,
                'child' => array(
                    'debit_total' => array(
                        'name' => __('Debet'),
                        'field_model' => false,
                        'display' => true,
                    ),
                    'credit_total' => array(
                        'name' => __('Credit'),
                        'field_model' => false,
                        'display' => true,
                    ),
                ),
                'display' => true,
            ),
        );

        if( $data_action == 'pdf' ) {
            $table_tr_head = 'background-color: #3C8DBC; border-right: 1px solid #FFFFFF; color: #FFFFFF; font-weight: bold; padding: 0 10px; text-align: center;';
        } else {
            $table_tr_head = false;
        }

        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table', $data_action, array(
            'class' => 'text-middle text-center',
            'style' => $table_tr_head,
        ));

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
                echo $this->element('blocks/cashbanks/search_prepayment_report');
?>
<section class="content invoice">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
        <?php
                echo $this->Html->tag('div', $period_label, array(
                    'class' => 'pull-right',
                ));
        ?>
        <div class="clear"></div>
    </h2>
    <?php
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
        <table class="table table-bordered" style="<?php echo $addStyle; ?>" border="<?php echo $border; ?>">
            <thead>
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
                        if(!empty($prepayments)){
                            $total_debit = 0;
                            $total_credit = 0;

                            foreach ($prepayments as $key => $prepayment) {
                                $total = $this->Common->getMergeTotalPrepayment($prepayment);
                                echo $this->Common->getMergePrepayment($prepayment);

                                $total_debit += !empty($total['debit_total'])?$total['debit_total']:0;
                                $total_credit += !empty($total['credit_total'])?$total['credit_total']:0;
                            }

                            $kolom = $this->Html->tag('td', __('Total'), array(
                                'colspan' => 6,
                                'style' => 'text-align: right;font-weight:bold;'
                            ));
                            $kolom .= $this->Html->tag('td', $this->Common->getFormatPrice($total_debit, false, 2), array(
                                'style' => 'text-align: right;font-weight:bold;'
                            ));
                            $kolom .= $this->Html->tag('td', $this->Common->getFormatPrice($total_credit, false, 2), array(
                                'style' => 'text-align: right;font-weight:bold;'
                            ));
                            echo $this->Html->tag('tr', $kolom);
                        }
                ?>
            </tbody>
        </table>
        <?php 
                if( $data_action != 'excel' ) {
                    if(empty($prepayments)){
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
            $table_th = 'padding-top: 3px';
            $table = 'width:100%;font-size: 24px; border: 1px solid #CCC; border-collapse: collapse; padding: 0; margin: 0;';
            $tdStyle = 'text-align: center;';

            $date_title = $sub_module_title;
            $print_label = $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 24px;font-style: italic;margin-top: 10px;'
            ));
            $each_loop_message = false;

             if( empty($fieldColumn) ) {
                $fieldColumn = false;
            }

            if(!empty($prepayments)){
                foreach ($prepayments as $key => $prepayment) {
                    $each_loop_message .= $this->Common->getMergePrepayment($prepayment);
                }
            }
$tbl = <<<EOD

      <div class="clearfix container_16" id="content">

        <div style="text-align: center;font-size: 44px;line-height: 0px;">$date_title</div>
        <div style="text-align: center;font-size: 24px;">$period_label</div>
        <br>

        <table cellpadding="2" cellspacing="2" nobr="true" style="$table">
            <thead>
                <tr style="$table_tr_head">
                    $fieldColumn
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