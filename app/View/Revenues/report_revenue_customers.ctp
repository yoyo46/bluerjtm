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
            echo $this->Form->create('Ttuj', array(
                'url'=> $this->Html->url( array(
                    'controller' => 'revenues',
                    'action' => 'search',
                    'report_revenue_customers'
                )), 
                'role' => 'form',
                'inputDefaults' => array('div' => false),
            ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <?php 
                        echo $this->Form->label('fromMonth', __('Dari Bulan'));
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
                                    ));
                            ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php 
                                    echo $this->Form->year('from', 1949, date('Y'), array(
                                        'label'=> false, 
                                        'class'=>'form-control',
                                        'required' => false,
                                        'empty' => false,
                                    ));
                            ?>
                        </div>
                    </div>
                </div>
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
            </div>
            <div class="col-sm-6">
                <?php 
                        echo $this->Form->label('fromMonth', __('Sampai Bulan'));
                ?>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php 
                                    echo $this->Form->month('to', array(
                                        'label'=> false, 
                                        'class'=>'form-control',
                                        'required' => false,
                                        'empty' => false,
                                    ));
                            ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?php 
                                    echo $this->Form->year('to', 1949, date('Y'), array(
                                        'label'=> false, 
                                        'class'=>'form-control',
                                        'empty' => false,
                                    ));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="form-group action">
                    <?php
                            echo $this->Form->button('<i class="fa fa-search"></i> '.__('Submit'), array(
                                'div' => false, 
                                'class'=> 'btn btn-success btn-sm',
                                'type' => 'submit',
                            ));
                            echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                                'action' => 'report_revenue_customers', 
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
    <?php 
            }
    ?>
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
        <small class="pull-right">
            <?php
                    echo $period_text;
            ?>
        </small>
    </h2>
    <?php 
            if( $data_action != 'excel' ) {
    ?>
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
                            echo $this->Html->tag('th', $this->Common->getSorting('Customer.code', __('Customer')), array(
                                'style' => 'width: 150px;'.$tdStyle,
                                'class' => 'text-center text-middle',
                            ));

                            if( !empty($totalCnt) ) {
                                for ($i=0; $i <= $totalCnt; $i++) {
                                    $formatDate = 'F';

                                    if( $fromYear != $toYear ) {
                                        $formatDate = 'F Y';
                                    }
                                    echo $this->Html->tag('th', date($formatDate, mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear)), array(
                                        'class' => 'text-center',
                                        'style' => $tdStyle,
                                    ));
                                }
                            }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                        if(!empty($customers)){
                            foreach ($customers as $key => $customer) {
                ?>
                <tr>
                    <?php 
                            echo $this->Html->tag('td', $customer['Customer']['name']);

                            if( !empty($totalCnt) ) {
                                for ($i=0; $i <= $totalCnt; $i++) {
                                    $currDate = date('Y-m', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear));
                                    $pencapaian = !empty($customer['Customer'][$currDate]['total_revenue'])?$this->Number->format($customer['Customer'][$currDate]['total_revenue'], Configure::read('__Site.config_currency_code'), array('places' => 0)):'0';
                                    echo $this->Html->tag('td', $pencapaian, array(
                                        'style' => 'text-align: right;',
                                    ));
                                }
                            }
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
            $tdStyle = 'text-align: center;';
            $each_loop_message = '';

            $contentHeader = $this->Html->tag('th', $this->Common->getSorting('Customer.code', __('Customer')), array(
                'style' => $tdStyle,
            ));

            if( !empty($totalCnt) ) {
                for ($i=0; $i <= $totalCnt; $i++) {
                    $formatDate = 'F';

                    if( $fromYear != $toYear ) {
                        $formatDate = 'F Y';
                    }
                    $contentHeader .= $this->Html->tag('th', date($formatDate, mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear)), array(
                        'style' => $tdStyle,
                    ));
                }
            }

            if(!empty($customers)){
                foreach ($customers as $key => $customer) {
                    $each_loop_message .= '<tr>';
                    $each_loop_message .= $this->Html->tag('td', $customer['Customer']['name']);

                    if( !empty($totalCnt) ) {
                        for ($i=0; $i <= $totalCnt; $i++) {
                            $currDate = date('Y-m', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear));
                            $pencapaian = !empty($customer['Customer'][$currDate]['total_revenue'])?$this->Number->format($customer['Customer'][$currDate]['total_revenue'], Configure::read('__Site.config_currency_code'), array('places' => 0)):'0';
                            $each_loop_message .= $this->Html->tag('td', $pencapaian, array(
                                'style' => 'text-align: right;',
                            ));
                        }
                    }
                    $each_loop_message .= '</tr>';
                }
            }

            $date_title = $sub_module_title;
            $print_label = $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $User['full_name'])), array(
                'style' => 'font-size: 24px;font-style: italic;margin-top: 10px;'
            ));
$tbl = <<<EOD

      <div class="clearfix container_16" id="content">
        <h2 class="grid_8" style="text-align: center;">$date_title</h2>
        <div style="text-align: right;display: block;">
            $period_text
        </div>
        <table cellpadding="2" cellspacing="2" nobr="true" style="$table;display: block;">
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