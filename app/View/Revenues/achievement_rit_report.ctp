<style type="text/css">
    .datagrid-header-row > td > div.datagrid-cell {
        margin: 0 auto;
    }
</style>
<?php 
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;

        if( empty($data_action) || ( !empty($data_action) && $data_action == 'excel' ) ){
            $this->Html->addCrumb($sub_module_title);
            $addStyle = '';
            $border = 0;
            $headerRowspan = false;
            $addClass = 'easyui-datagrid';

            if( $data_action == 'excel' ) {
                header('Content-type: application/ms-excel');
                header('Content-Disposition: attachment; filename='.$sub_module_title.'.xls');
                $border = 1;
                $addClass = '';
                $headerRowspan = 3;
            }

            if( !empty($cities) ) {
                $addStyle = 'width: 100%;height: 550px;';
            }

            if( $data_action != 'excel' ) {
                echo $this->element('blocks/revenues/searchs/achievement_rit_report');
?>
<section class="content invoice">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
    </h2>
    <div class="row no-print print-action">
        <div class="col-xs-12 action">
            <?php
                    $urlDefault = $this->passedArgs;
                    $urlDefault['controller'] = 'revenues';
                    $urlDefault['action'] = 'achievement_rit_report';

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
        <table id="tt" class="table table-bordered <?php echo $addClass; ?>" style="<?php echo $addStyle; ?>" singleSelect="true" border="<?php echo $border; ?>">
            <thead frozen="true">
                <tr>
                    <?php 
                            echo $this->Html->tag('th', $this->Common->getSorting('Truck.nopol', __('NoPol')), array(
                                'style' => 'text-align: center;width: 120px;',
                                'data-options' => 'field:\'nopol\',width:150,',
                                'rowspan' => $headerRowspan,
                            ));
                            echo $this->Html->tag('th', __('Alokasi'), array(
                                'style' => 'text-align: center;width: 120px;',
                                'data-options' => 'field:\'alocation\',width:150,',
                                'rowspan' => $headerRowspan,
                            ));
                            echo $this->Html->tag('th', __('Kapasitas'), array(
                                'style' => 'text-align: center;width: 80px;',
                                'data-options' => 'field:\'capacity\',width:80,',
                                'rowspan' => $headerRowspan,
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

                            if( isset($totalCnt) ) {
                                for ($i=0; $i <= $totalCnt; $i++) {
                                    $formatDate = 'F';

                                    if( $fromYear != $toYear ) {
                                        $formatDate = 'F Y';
                                    }

                                    echo $this->Html->tag('th', date($formatDate, mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear)), array(
                                        'style' => 'text-align: center;',
                                        'colspan' => 4,
                                        'align' => 'center',
                                    ));
                                }
                            }

                            echo $this->Html->tag('th', __('Grandtotal'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'grandtotal_side\',width:100',
                                'align' => 'center',
                                'colspan' => 4,
                            ));
                    ?>
                </tr>
                <tr>
                    <?php 
                            if( isset($totalCnt) ) {
                                for ($i=0; $i <= $totalCnt; $i++) {
                                    echo $this->Html->tag('th', __('RIT'), array(
                                        'style' => 'text-align: center;',
                                        'data-options' => 'field:\'rit_'.$i.'\',width:100',
                                        'align' => 'center',
                                        'colspan' => 2,
                                    ));
                                    echo $this->Html->tag('th', __('UNIT'), array(
                                        'style' => 'text-align: center;',
                                        'data-options' => 'field:\'unit_'.$i.'\',width:100',
                                        'align' => 'center',
                                        'rel' => $i,
                                        'colspan' => 2,
                                    ));
                                }
                            }

                            echo $this->Html->tag('th', __('RIT'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'side_grandtotal_rit\',width:100',
                                'align' => 'center',
                                'colspan' => 2,
                            ));

                            echo $this->Html->tag('th', __('UNIT'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'side_grandtotal_unit\',width:100',
                                'align' => 'center',
                                'colspan' => 2,
                            ));
                    ?>
                </tr>
                <tr>
                    <?php 
                            if( isset($totalCnt) ) {
                                for ($i=0; $i <= $totalCnt; $i++) {
                                    echo $this->Html->tag('th', __('TARGET'), array(
                                        'style' => 'text-align: center;',
                                        'data-options' => 'field:\'target_rit_'.$i.'\',width:100',
                                        'align' => 'center',
                                    ));
                                    echo $this->Html->tag('th', __('PENCAPAIAN'), array(
                                        'style' => 'text-align: center;',
                                        'data-options' => 'field:\'total_rit_'.$i.'\',width:100,styler:targetRit',
                                        'align' => 'center',
                                        'rel' => $i,
                                    ));
                                    echo $this->Html->tag('th', __('TARGET'), array(
                                        'style' => 'text-align: center;',
                                        'data-options' => 'field:\'target_unit_'.$i.'\',width:100',
                                        'align' => 'center',
                                    ));
                                    echo $this->Html->tag('th', __('PENCAPAIAN'), array(
                                        'style' => 'text-align: center;',
                                        'data-options' => 'field:\'total_unit_'.$i.'\',width:100,styler:targetUnit',
                                        'align' => 'center',
                                        'rel' => $i,
                                    ));
                                }
                            }

                            echo $this->Html->tag('th', __('TARGET'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'grandtotal_target_rit\',width:100',
                                'align' => 'center',
                            ));

                            echo $this->Html->tag('th', __('PENCAPAIAN'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'grandtotal_rit\',width:100,styler:calcGrandtotalTargetRit',
                                'align' => 'center',
                            ));

                            echo $this->Html->tag('th', __('TARGET'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'grandtotal_target_unit\',width:100',
                                'align' => 'center',
                            ));

                            echo $this->Html->tag('th', __('PENCAPAIAN'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'grandtotal_unit\',width:100,styler:calcGrandtotalTargetUnit',
                                'align' => 'center',
                            ));
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php 
                        echo $this->element('blocks/revenues/tables/achievement_rit_report_items');
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
    <?php 
            }
            
            echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 14px;font-style: italic;margin-top: 10px;'
            ));

            if( $data_action != 'excel' ) {
                echo $this->element('pagination');
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
        
            $each_loop_message = $this->element('blocks/revenues/tables/achievement_rit_report_items', array(
                'numbering' => true,
            ));

            $topHeader = '';
            $secondHeader = '';
            $thirdHeader = '';

            if( isset($totalCnt) ) {
                for ($i=0; $i <= $totalCnt; $i++) {
                    $formatDate = 'F';

                    if( $fromYear != $toYear ) {
                        $formatDate = 'F Y';
                    }

                    $topHeader .= $this->Html->tag('th', date($formatDate, mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear)), array(
                        'style' => 'text-align: center;',
                        'colspan' => 4,
                        'align' => 'center',
                    ));
                }
            }

            $topHeader .= $this->Html->tag('th', __('Grandtotal'), array(
                'style' => 'text-align: center;',
                'align' => 'center',
                'colspan' => 4,
            ));

            if( isset($totalCnt) ) {
                for ($i=0; $i <= $totalCnt; $i++) {
                    $secondHeader .= $this->Html->tag('th', __('RIT'), array(
                        'style' => 'text-align: center;',
                        'align' => 'center',
                        'colspan' => 2,
                    ));
                    $secondHeader .= $this->Html->tag('th', __('UNIT'), array(
                        'style' => 'text-align: center;',
                        'align' => 'center',
                        'rel' => $i,
                        'colspan' => 2,
                    ));
                }
            }

            $secondHeader .= $this->Html->tag('th', __('RIT'), array(
                'style' => 'text-align: center;',
                'align' => 'center',
                'colspan' => 2,
            ));

            $secondHeader .= $this->Html->tag('th', __('UNIT'), array(
                'style' => 'text-align: center;',
                'align' => 'center',
                'colspan' => 2,
            ));

            if( isset($totalCnt) ) {
                for ($i=0; $i <= $totalCnt; $i++) {
                    $thirdHeader .= $this->Html->tag('th', __('TARGET'), array(
                        'style' => 'text-align: center;',
                        'align' => 'center',
                    ));
                    $thirdHeader .= $this->Html->tag('th', __('PENCAPAIAN'), array(
                        'style' => 'text-align: center;',
                        'align' => 'center',
                        'rel' => $i,
                    ));
                    $thirdHeader .= $this->Html->tag('th', __('TARGET'), array(
                        'style' => 'text-align: center;',
                        'align' => 'center',
                    ));
                    $thirdHeader .= $this->Html->tag('th', __('PENCAPAIAN'), array(
                        'style' => 'text-align: center;',
                        'align' => 'center',
                        'rel' => $i,
                    ));
                }
            }

            $thirdHeader .= $this->Html->tag('th', __('TARGET'), array(
                'style' => 'text-align: center;',
                'align' => 'center',
            ));

            $thirdHeader .= $this->Html->tag('th', __('PENCAPAIAN'), array(
                'style' => 'text-align: center;',
                'align' => 'center',
            ));

            $thirdHeader .= $this->Html->tag('th', __('TARGET'), array(
                'style' => 'text-align: center;',
                'align' => 'center',
            ));

            $thirdHeader .= $this->Html->tag('th', __('PENCAPAIAN'), array(
                'style' => 'text-align: center;',
                'align' => 'center',
            ));

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
                    <th rowspan="3" style="text-align: center;">No. </th>
                    <th rowspan="3">NoPol</th>
                    <th rowspan="3">Alokasi</th>
                    <th rowspan="3">Kapasitas</th>
                    $topHeader
                </tr>
                <tr style="$table_tr_head">
                    $secondHeader
                </tr>
                <tr style="$table_tr_head">
                    $thirdHeader
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
    function targetRit(value,row,index, rel){
        var tmp = 'row.target_rit_'+rel;
        var target = eval(tmp);
        target = parseInt(target.replace(/,/gi, ""));

        if( !isNaN(value) ) {
            value = parseInt(value.replace(/,/gi, ""));
        }

        if (!isNaN(value) && !isNaN(target) && target != 0){
            if( value >= target ) {
                return 'background-color:#dff0d8;color:#3c763d;';
            } else {
                return 'background-color:#f2dede;color:#a94442;';
            }
        } else {
            return false;
        }
    }
    function targetUnit(value,row,index, rel){
        var tmp = 'row.target_unit_'+rel;
        var target = eval(tmp);
        target = parseInt(target.replace(/,/gi, ""));
        
        if( !isNaN(value) ) {
            value = parseInt(value.replace(/,/gi, ""));
        }

        if (!isNaN(value) && !isNaN(target) && target != 0){
            if( value >= target ) {
                return 'background-color:#dff0d8;color:#3c763d;';
            } else {
                return 'background-color:#f2dede;color:#a94442;';
            }
        } else {
            return false;
        }
    }

    function calcGrandtotalTargetRit (value,row,index) {
        var target = 0;

        if(typeof row.grandtotal_target_rit != 'undefined' ){
            target = parseInt(row.grandtotal_target_rit.replace(/,/gi, ""));
        }

        if(typeof value != 'undefined' ){
            value = parseInt(value.toString().replace(/,/gi, ""));
        } else {
            value = 0;
        }

        if( !isNaN(value) && !isNaN(target) && value >= target ) {
            return 'background-color:#dff0d8;color:#3c763d;';
        } else {
            return 'background-color:#f2dede;color:#a94442;';
        }
    }
    function calcGrandtotalTargetUnit (value,row,index) {
        var target = 0;

        if(typeof row.grandtotal_target_unit != 'undefined' ){
            target = parseInt(row.grandtotal_target_unit.replace(/,/gi, ""));
        }

        if(typeof value != 'undefined' ){
            value = parseInt(value.toString().replace(/,/gi, ""));
        } else {
            value = 0;
        }

        if( !isNaN(value) && !isNaN(target) && value >= target ) {
            return 'background-color:#dff0d8;color:#3c763d;';
        } else {
            return 'background-color:#f2dede;color:#a94442;';
        }
    }
</script>