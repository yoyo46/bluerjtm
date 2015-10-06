<?php
        $data_action = !empty($data_action)?$data_action:false;

        if( empty($data_action) || ( !empty($data_action) && $data_action == 'excel' ) ){
            $border = 0;

            if( $data_action == 'excel' ) {
                header('Content-type: application/ms-excel');
                header('Content-Disposition: attachment; filename='.$sub_module_title.'.xls');
                $border = 1;
            } else {
                echo $this->element('blocks/cashbanks/searchs/journal_report');
            	$this->Html->addCrumb($sub_module_title);
?>
<section class="content invoice">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo $module_title;?>
    </h2>
    <!-- <div class="row no-print print-action">
        <div class="col-xs-12 action">
            <?php
                    echo $this->Html->link('<i class="fa fa-download"></i> Download Excel', $this->here.'/excel', array(
                        'escape' => false,
                        'class' => 'btn btn-success pull-right'
                    ));
                    echo $this->Html->link('<i class="fa fa-download"></i> Download PDF', $this->here.'/pdf', array(
                        'escape' => false,
                        'class' => 'btn btn-primary pull-right'
                    ));
            ?>
        </div>
    </div> -->
    <div class="table-responsive">
        <?php 
                }
        ?>
        <table class="table journal table-bordered" border="<?php echo $border; ?>">
            <thead>
    			<tr>
                    <th class="text-center text-middle"><?php echo __('Tgl');?></th>
    				<th class="text-center text-middle"><?php echo __('Tipe');?></th>
                    <th class="text-center text-middle"><?php echo __('COA');?></th>
                    <th class="text-center text-middle"><?php echo __('Debit');?></th>
                    <th class="text-center text-middle"><?php echo __('Kredit');?></th>
    				<th class="text-center text-middle"><?php echo __('No Dokumen');?></th>
    			</tr>
            </thead>
            <tbody>
                <?php
                        if(!empty($values)){
                            $old = false;
                            $documentType = Configure::read('__Site.Journal.Documents');

                            foreach ($values as $key => $value) {
                                $document_no = $this->Common->filterEmptyField($value, 'Journal', 'document_no');
                                $document_id = $this->Common->filterEmptyField($value, 'Journal', 'document_id');
                                $created = $this->Common->filterEmptyField($value, 'Journal', 'created');
                                $type = $this->Common->filterEmptyField($value, 'Journal', 'type');
                                $debit = $this->Common->filterEmptyField($value, 'Journal', 'debit');
                                $credit = $this->Common->filterEmptyField($value, 'Journal', 'credit');

                                $coa = $this->Common->filterEmptyField($value, 'Coa', 'coa_name');

                                $new = sprintf('%s-%s', $type, $document_no);
                                $customCreated = $this->Common->formatDate($created, 'd/m/Y');
                                $customDebit = $this->Common->getFormatPrice($debit, false);
                                $customCredit = $this->Common->getFormatPrice($credit, false);

                                if( !empty($documentType[$type]) ) {
                                    $customType = $documentType[$type];
                                } else {
                                    $customType = ucwords(str_replace('_', ' ', $type));
                                }

                                if( !empty($old) && $new != $old ) {
                                    echo '<tr><td colspan="6"><hr></td></tr>';
                                }
                ?>
                <tr>
                    <?php
                            echo $this->Html->tag('td', $customCreated);
                            echo $this->Html->tag('td', $customType, array(
                                'style' => 'text-align:center;'
                            ));
                            echo $this->Html->tag('td', $coa);
                            echo $this->Html->tag('td', $customDebit, array(
                                'style' => 'text-align:right;'
                            ));
                            echo $this->Html->tag('td', $customCredit, array(
                                'style' => 'text-align:right;'
                            ));
                            echo $this->Html->tag('td', $document_no);
                    ?>
                </tr>
                <?php

                                $old = $new;
                            }
                        }
                ?>
            </tbody>
        </table>
        <?php 
                if( $data_action != 'excel' ) {
                    if(empty($values)){
                        echo $this->Html->tag('p', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                        ));
                    }
        ?>
    </div><!-- /.box-body -->
</section>
<?php
            }
        } else{
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
            
            $each_loop_message = '';
            $no = 1;

            if(!empty($customers)){
                foreach ($customers as $key => $value) {
                    $total_pituang = !empty($value['piutang'][0][0]['total_pituang'])?$value['piutang'][0][0]['total_pituang']:0;
                    $current_rev1to15 = !empty($value['current_rev1to15'][0][0]['current_rev1to15'])?$value['current_rev1to15'][0][0]['current_rev1to15']:0;
                    $current_rev16to30 = !empty($value['current_rev16to30'][0][0]['current_rev16to30'])?$value['current_rev16to30'][0][0]['current_rev16to30']:0;
                    $current_rev30 = !empty($value['current_rev30'][0][0]['current_rev30'])?$value['current_rev30'][0][0]['current_rev30']:0;
                    $current = $total_pituang - $current_rev1to15 - $current_rev16to30 - $current_rev30;

                    $content = $this->Html->tag('td', $value['Customer']['customer_name_code'], array(
                        'style' => 'text-align: left; width: 120px;'
                    ));
                    $content .= $this->Html->tag('td', $this->Number->currency($total_pituang, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                        'style' => 'text-align: right;'
                    ));
                    $content .= $this->Html->tag('td', $this->Number->currency($current, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                        'style' => 'text-align: right;'
                    ));
                    $content .= $this->Html->tag('td', $this->Number->currency($current_rev1to15, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                        'style' => 'text-align: right;'
                    ));
                    $content .= $this->Html->tag('td', $this->Number->currency($current_rev16to30, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                        'style' => 'text-align: right;'
                    ));
                    $content .= $this->Html->tag('td', $this->Number->currency($current_rev30, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                        'style' => 'text-align: right;'
                    ));

                    $each_loop_message .= $this->Html->tag('tr', $content);
                    $no++;
                }
            }else{
                $each_loop_message .= '<tr>
                    <td colspan="7">data tidak tersedia</td>
                </tr>';
            }   

            $date_title = __('Account Receivable Aging Report');
            $total_data = count($customers);
$tbl = <<<EOD

      <div class="clearfix container_16" id="content">

        <h2 class="grid_8" style="text-align: center;">$date_title</h2>
        <h4>Total Customer : $total_data</h4>
        <table cellpadding="2" cellspacing="2" nobr="true" style="$table">
            <thead>
                <tr style="$table_tr_head">
                    <th style="text-align: center; width: 120px;">Customer</th>
                    <th style="text-align: center;">Saldo Piutang</th>
                    <th style="text-align: center;">Current</th>
                    <th style="text-align: center;">Over Due</th>
                </tr>
                <tr style="$table_tr_head">
                    <th style="text-align: center; color: #FFFFFF;">1- 15</th>
                    <th style="text-align: center;">16 - 30</th>
                    <th style="text-align: center;">> 30</th>
                </tr>
            </thead>
            <tbody>
                $each_loop_message
            </tbody>
        </table> 
      </div>
EOD;

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