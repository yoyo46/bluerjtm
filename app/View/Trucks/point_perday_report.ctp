<?php 
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        if( empty($data_action) || ( !empty($data_action) && $data_action == 'excel' ) ){
            $this->Html->addCrumb($sub_module_title);
            $tdStyle = '';
            $border = 0;
            $headerRowspan = false;
            $addClass = 'easyui-datagrid';

            if( $data_action == 'excel' ) {
                header('Content-type: application/ms-excel');
                header('Content-Disposition: attachment; filename='.$sub_module_title.'.xls');
                $border = 1;
                $tdStyle = 'text-align: center;';
                $addClass = '';
                $headerRowspan = 2;
            } else {
                echo $this->element('blocks/trucks/search_report_point_perday');
            }

            if( !empty($customers) ) {
                $addStyle = 'width: 100%;height: 550px;';
            }
?>
<section class="content invoice">
    <?php 
            if( $data_action != 'excel' ) {
    ?>
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
    </h2>
    <div class="row no-print print-action">
        <div class="col-xs-12 action">
            <?php
                    $urlDefault = $this->passedArgs;
                    $urlDefault['controller'] = 'trucks';
                    $urlDefault['action'] = 'point_perday_report';

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
        <table id="tt" class="table table-bordered <?php echo $addClass; ?>" style="<?php echo $addStyle; ?>" singleSelect="true" border="<?php echo $border; ?>" data-options="rowStyler: rowColored">
            <thead frozen="true">
                <tr>
                    <?php 
                            echo $this->Html->tag('th', $this->Common->getSorting('Customer.customer_code', __('Customer')), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'customer_code\',width:120',
                            ));

                            echo $this->Html->tag('th', __('Target'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'target\',width:100,sortable:true',
                                'align' => 'center',
                            ));

                            echo $this->Html->tag('th', __('Total'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'total\',width:100,sortable:true',
                                'align' => 'center',
                            ));

                            if( $data_action != 'excel' ) {
                    ?>
                </tr>
            </thead>
            <thead>
                <tr>
                    <?php 
                            }

                            for ($i=1; $i <= $lastDay; $i++) {
                                echo $this->Html->tag('th', $i, array(
                                    'style' => 'text-align: center;',
                                    'data-options' => 'field:\'pencapaian_'.$i.'\',width:100,sortable:true',
                                    'align' => 'center',
                                    'title' => date('l', mktime(0, 0, 0, date("m", strtotime($currentMonth)) , $i, date("Y", strtotime($currentMonth)))),
                                ));
                            }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php 
                        echo $this->element('blocks/trucks/tables/point_perday_report_items');
                ?>
            </tbody>
        </table>
    <?php
            if( $data_action != 'excel' ) {
    ?>
    </div>
    <?php 
            }

            echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 14px;font-style: italic;margin-top: 10px;'
            ));
    ?>
</section>
<?php
    } else {
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
        $each_loop_message = $this->element('blocks/trucks/tables/point_perday_report_items');
        $header = $this->Html->tag('th', __('Customer'), array(
            'rowspan' => 2,
            'style' => 'text-align: center;',
        ));

        for ($i=1; $i <= $lastDay; $i++) {
            $header .= $this->Html->tag('th', $i, array(
                'style' => 'text-align: center;',
            ));
        }

        $header .= $this->Html->tag('th', __('Total'), array(
            'style' => 'text-align: center;',
        ));

        $header .= $this->Html->tag('th', __('Target'), array(
            'style' => 'text-align: center;',
        ));

        $date_title = $sub_module_title;
        $print_label = $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
            'style' => 'font-size: 24px;font-style: italic;margin-top: 10px;'
        ));

$tbl = <<<EOD

      <div class="clearfix container_16" id="content">

        <h3 style="text-align: center;">$date_title</h3><br>
        <table cellpadding="2" cellspacing="2" nobr="true" style="$table">
            <thead>
                <tr style="$table_tr_head">
                    $header
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
    function rowColored (index,row) {
        var value = row.customer_code.substr(0, 5).toLowerCase();
        var temp_value = row.customer_code;

        if( $.inArray( temp_value, [ 'MSK.SRG', 'KAISAR', 'SSP.JMB', 'HYT.PDG', 'SMG.JY', 'YIMM', 'YMMWJ' ] ) != '-1' ) {
            return 'background: #f4f4f4;';
        } else if( value == 'total' ) {
            return 'background: rgba(221, 221, 221,0.5);font-weight:bold;';
        }
    }
</script>