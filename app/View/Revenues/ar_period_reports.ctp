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
                    'ar_period_reports'
                )), 
                'role' => 'form',
                'inputDefaults' => array('div' => false),
            ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->label('fromMonth', __('Pilih Tahun'));
                            echo $this->Form->year('from', 1949, date('Y'), array(
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => false,
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
                                'action' => 'ar_period_reports', 
                            ), array(
                                'escape' => false, 
                                'class'=> 'btn btn-default btn-sm',
                            ));
                    ?>
                </div>
            </div>
            <div class="col-sm-6">
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
<section class="content invoice">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
    </h2>
    <div class="row no-print print-action">
        <div class="col-xs-12 action">
            <?php
                    $urlDefault = $this->passedArgs;
                    $urlDefault['controller'] = 'revenues';
                    $urlDefault['action'] = 'ar_period_reports';

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
                            echo $this->Html->tag('th', __('A/R'), array(
                                'style' => 'width: 120px;'.$tdStyle,
                                'class' => 'text-center text-middle',
                            ));

                            for ($i=1; $i <= $toMonth; $i++) {
                                $formatDate = 'F';

                                echo $this->Html->tag('th', date($formatDate, mktime(0, 0, 0, $i, 1, $fromYear)), array(
                                    'class' => 'text-center',
                                    'style' => $tdStyle,
                                ));
                            }
                    ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php 
                            echo $this->Html->tag('td', __('A/R (PERIOD)'));

                            for ($i=1; $i <= $toMonth; $i++) {
                                $month = date('Y-m', mktime(0, 0, 0, $i, 1, $fromYear));
                                $ar = !empty($totalAr['AR'][$month])?$totalAr['AR'][$month]:0;

                                echo $this->Html->tag('td', $this->Number->currency($ar, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                                    'class' => 'text-right'
                                ));
                            }
                    ?>
                </tr>
                <tr>
                    <?php 
                            echo $this->Html->tag('td', __('TOTAL INVOICE'));
                            
                            for ($i=1; $i <= $toMonth; $i++) {
                                $month = date('Y-m', mktime(0, 0, 0, $i, 1, $fromYear));
                                $invoice = !empty($totalAr['Invoice'][$month])?$totalAr['Invoice'][$month]:0;

                                echo $this->Html->tag('td', $this->Number->currency($invoice, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                                    'class' => 'text-right'
                                ));
                            }
                    ?>
                </tr>
                <tr>
                    <?php 
                            echo $this->Html->tag('td', __('LAST PERIOD'));
                            
                            for ($i=1; $i <= $toMonth; $i++) {
                                $month = date('Y-m', mktime(0, 0, 0, $i, 1, $fromYear));
                                $lastInvoice = !empty($totalAr['LastInvoice'][$month])?$totalAr['LastInvoice'][$month]:0;

                                echo $this->Html->tag('td', $this->Number->currency($lastInvoice, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                                    'class' => 'text-right'
                                ));
                            }
                    ?>
                </tr>
                <tr>
                    <?php 
                            echo $this->Html->tag('td', __('CURRENT PERIOD'));
                            
                            for ($i=1; $i <= $toMonth; $i++) {
                                $month = date('Y-m', mktime(0, 0, 0, $i, 1, $fromYear));
                                $invoice = !empty($totalAr['Invoice'][$month])?$totalAr['Invoice'][$month]:0;
                                $lastInvoice = !empty($totalAr['LastInvoice'][$month])?$totalAr['LastInvoice'][$month]:0;

                                echo $this->Html->tag('td', $this->Number->currency($invoice - $lastInvoice, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                                    'class' => 'text-right'
                                ));
                            }
                    ?>
                </tr>
            </tbody>
        </table>
        <?php 
                if( $data_action != 'excel' ) {
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
        
            $no = 1;
            $each_loop_message = '';
            $content = $this->Html->tag('td', __('A/R (PERIOD)'), array(
                'style' => 'text-align: left;'
            ));

            for ($i=1; $i <= $toMonth; $i++) {
                $month = date('Y-m', mktime(0, 0, 0, $i, 1, $fromYear));
                $ar = !empty($totalAr['AR'][$month])?$totalAr['AR'][$month]:0;

                $content .= $this->Html->tag('td', $this->Number->currency($ar, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                    'style' => 'text-align: right;'
                ));
            }

            $each_loop_message .= $this->Html->tag('tr', $content);
            $content = $this->Html->tag('td', __('TOTAL INVOICE'), array(
                'style' => 'text-align: left;'
            ));

            for ($i=1; $i <= $toMonth; $i++) {
                $month = date('Y-m', mktime(0, 0, 0, $i, 1, $fromYear));
                $invoice = !empty($totalAr['Invoice'][$month])?$totalAr['Invoice'][$month]:0;

                $content .= $this->Html->tag('td', $this->Number->currency($invoice, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                    'style' => 'text-align: right;'
                ));
            }

            $each_loop_message .= $this->Html->tag('tr', $content);
            $content = $this->Html->tag('td', __('LAST PERIOD'), array(
                'style' => 'text-align: left;'
            ));

            for ($i=1; $i <= $toMonth; $i++) {
                $month = date('Y-m', mktime(0, 0, 0, $i, 1, $fromYear));
                $lastInvoice = !empty($totalAr['LastInvoice'][$month])?$totalAr['LastInvoice'][$month]:0;

                $content .= $this->Html->tag('td', $this->Number->currency($lastInvoice, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                    'style' => 'text-align: right;'
                ));
            }

            $each_loop_message .= $this->Html->tag('tr', $content);
            $content = $this->Html->tag('td', __('CURRENT PERIOD'), array(
                'style' => 'text-align: left;'
            ));

            for ($i=1; $i <= $toMonth; $i++) {
                $month = date('Y-m', mktime(0, 0, 0, $i, 1, $fromYear));
                $invoice = !empty($totalAr['Invoice'][$month])?$totalAr['Invoice'][$month]:0;
                $lastInvoice = !empty($totalAr['LastInvoice'][$month])?$totalAr['LastInvoice'][$month]:0;

                $content .= $this->Html->tag('td', $this->Number->currency($invoice - $lastInvoice, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                    'style' => 'text-align: right;'
                ));
            }

            $each_loop_message .= $this->Html->tag('tr', $content);

            if(!empty($ttujs)){
                foreach ($ttujs as $key => $value) {
                    $id = $value['Customer']['id'];
                    $customer_name = !empty($value['Customer']['customer_name'])?$value['Customer']['customer_name']:'-';
                    $target_rit = !empty($value['Customer']['target_rit'])?$value['Customer']['target_rit']:'-';

                    $content = $this->Html->tag('td', $no);
                    $content .= $this->Html->tag('td', $customer_name);

                    if( isset($totalCnt) ) {
                        for ($i=0; $i <= $totalCnt; $i++) {
                            $pencapaian = !empty($cntPencapaian[$value['Customer']['id']][date('Y-m', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear))])?$cntPencapaian[$value['Customer']['id']][date('Y-m', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear))]:'-';
                            $content .= $this->Html->tag('td', $target_rit, array(
                                'style' => 'text-align: center;'
                            ));
                            $content .= $this->Html->tag('td', $pencapaian, array(
                                'style' => 'text-align: center;'
                            ));
                        }
                    }

                    $each_loop_message .= $this->Html->tag('tr', $content);
                    $no++;
                }
            }

            $topHeader = $this->Html->tag('th', __('A/R'), array(
                'style' => 'text-align: center;',
            ));

            for ($i=1; $i <= $toMonth; $i++) {
                $formatDate = 'F';

                $topHeader .= $this->Html->tag('th', date($formatDate, mktime(0, 0, 0, $i, 1, $fromYear)), array(
                    'style' => 'text-align: center;',
                ));
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
                    $topHeader
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