<?php 
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        if( empty($data_action) || ( !empty($data_action) && $data_action == 'excel' ) ){
            $this->Html->addCrumb($sub_module_title);
            $addStyle = '';
            $tdStyle = '';
            $border = 0;
            $headerRowspan = false;
            $addClass = 'easyui-datagrid';
            // $addClass = '';

            if( $data_action == 'excel' ) {
                header('Content-type: application/ms-excel');
                header('Content-Disposition: attachment; filename='.$sub_module_title.'.xls');
                $border = 1;
                $tdStyle = 'text-align: center;';
                $addClass = '';
                $headerRowspan = 2;
            }

            if( !empty($cities) ) {
                $addStyle = 'width: 100%;height: 550px;';
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
                    'achievement_report'
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
                        <div class="form-group">
                            <?php 
                                    echo $this->Form->input('customer',array(
                                        'label'=> __('Customer'),
                                        'class'=>'form-control',
                                        'required' => false,
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
                <div class="form-group action">
                    <?php
                            echo $this->Form->button('<i class="fa fa-search"></i> '.__('Submit'), array(
                                'div' => false, 
                                'class'=> 'btn btn-success btn-sm',
                                'type' => 'submit',
                            ));
                            echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), array(
                                'action' => 'achievement_report', 
                            ), array(
                                'escape' => false, 
                                'class'=> 'btn btn-default btn-sm',
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
                    <div class="col-sm-12">
                        <?php 
                                // Custom Otorisasi
                                echo $this->Common->getCheckboxBranch();
                        ?>
                    </div>
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
                    $urlDefault['action'] = 'achievement_report';

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
                            // echo $this->Html->tag('th', $this->Common->getSorting('Customer.customer_group_id', __('Group')), array(
                            //     'style' => 'text-align: center;width: 150px;',
                            //     'data-options' => 'field:\'group\',width:150,',
                            //     'rowspan' => $headerRowspan,
                            // ));
                            echo $this->Html->tag('th', $this->Common->getSorting('Customer.code', __('Customer')), array(
                                'style' => 'text-align: center;width: 150px;',
                                'data-options' => 'field:\'customer_code\',width:150,',
                                'rowspan' => $headerRowspan,
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
                                        'colspan' => 2,
                                        'align' => 'center',
                                    ));
                                }
                            }

                            echo $this->Html->tag('th', __('Total Target'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'total_target_side\',width:100',
                                'align' => 'center',
                                'rowspan' => 2,
                            ));

                            echo $this->Html->tag('th', __('Total Pencapaian'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'total_pencapaian_side\',width:100,styler:calcPencapaian',
                                'align' => 'center',
                                'rowspan' => 2,
                            ));
                    ?>
                </tr>
                <tr>
                    <?php 
                            if( isset($totalCnt) ) {
                                for ($i=0; $i <= $totalCnt; $i++) {
                                    echo $this->Html->tag('th', __('TARGET UNIT'), array(
                                        'style' => 'text-align: center;',
                                        'data-options' => 'field:\'target_unit_'.$i.'\',width:100',
                                        'align' => 'center',
                                    ));
                                    echo $this->Html->tag('th', __('PENCAPAIAN'), array(
                                        'style' => 'text-align: center;',
                                        'data-options' => 'field:\'pencapaian_'.$i.'\',width:100,styler:cellStyler',
                                        'align' => 'center',
                                        'rel' => $i,
                                    ));
                                }
                            }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                        if(!empty($ttujs)){
                            $grandTotalPencapaian = 0;
                            $grandTotalTarget = 0;
                            $grandTotalPencapaianGroup = array();
                            $grandTotalTargetGroup = array();
                            $groupName = array();
                            $totalPencapaianGroup = array();
                            $totalTargetGroup = array();
                            $temp_tye_id = false;
                            $temp_group_id = false;
                            $finishTotal = array();

                            foreach ($ttujs as $key => $value) {
                                $id = $value['Customer']['id'];
                                $customer_name = $this->Common->filterEmptyField($value, 'Customer', 'code', '-');
                                $customer_group_id = $this->Common->filterEmptyField($value, 'Customer', 'customer_group_id', '-');
                                $type = $this->Common->filterEmptyField($value, 'CustomerType', 'name');
                                $type_id = $this->Common->filterEmptyField($value, 'CustomerType', 'id');
                                $group_name = $this->Common->filterEmptyField($value, 'CustomerGroup', 'name');

                                $totalSidePencapaian = 0;
                                $totalSideTarget = 0;

                                // if( !empty($temp_group_id) && !empty($temp_tye_id) && ( $temp_group_id != $customer_group_id || $temp_tye_id != $type_id ) ) {
                                //     echo $this->element('blocks/revenues/tables/achievement_report', array(
                                //         'type_id' => $temp_tye_id,
                                //         'customer_group_id' => $temp_group_id,
                                //         'groupName' => $groupName,
                                //         'totalCnt' => $totalCnt,
                                //         'totalTargetGroup' => $totalTargetGroup,
                                //         'totalPencapaianGroup' => $totalPencapaianGroup,
                                //         'grandTotalTargetGroup' => $grandTotalTargetGroup,
                                //         'grandTotalPencapaianGroup' => $grandTotalPencapaianGroup,
                                //     ));

                                //     $finishTotal[$temp_tye_id][$temp_group_id] = true;
                                // }

                                $temp_group_id = $customer_group_id;
                                $temp_tye_id = $type_id;
                ?>
                <tr>
                    <?php 
                            // echo $this->Html->tag('td', $customer_group_id);
                            // echo $this->Html->tag('td', $customer_name.' - '.$type);
                            echo $this->Html->tag('td', $customer_name);

                            if( isset($totalCnt) ) {
                                for ($i=0; $i <= $totalCnt; $i++) {
                                    $pencapaian = !empty($cntPencapaian[$value['Customer']['id']][date('Y-m', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear))])?$cntPencapaian[$value['Customer']['id']][date('Y-m', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear))]:'-';
                                    $target_rit = !empty($targetUnit[$value['Customer']['id']][date('Y-m', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear))])?$targetUnit[$value['Customer']['id']][date('Y-m', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear))]:'-';
                                    $nameVal = 'totalPencapaian'.$i;
                                    $nameValTarget = 'totalTarget'.$i;

                                    if( empty($$nameVal) ) {
                                        $$nameVal = 0;
                                    }
                                    if( empty($$nameValTarget) ) {
                                        $$nameValTarget = 0;
                                    }

                                    if( is_numeric($pencapaian) ) {
                                        $$nameVal += $pencapaian;
                                        $totalSidePencapaian += $pencapaian;
                                    }

                                    if( is_numeric($target_rit) ) {
                                        $$nameValTarget += $target_rit;
                                        $totalSideTarget += $target_rit;
                                    }

                                    echo $this->Html->tag('td', $target_rit, array(
                                        'class' => 'text-center',
                                        'style' => $tdStyle,
                                    ));
                                    echo $this->Html->tag('td', $pencapaian, array(
                                        'class' => 'text-center',
                                        'style' => $tdStyle,
                                    ));

                                    if( !empty($totalPencapaianGroup[$i][$type_id][$customer_group_id]) ) {
                                        $totalPencapaianGroup[$i][$type_id][$customer_group_id] += $pencapaian;
                                    } else {
                                        $totalPencapaianGroup[$i][$type_id][$customer_group_id] = $pencapaian;
                                    }
                                    if( !empty($totalTargetGroup[$i][$type_id][$customer_group_id]) ) {
                                        $totalTargetGroup[$i][$type_id][$customer_group_id] += $target_rit;
                                    } else {
                                        $totalTargetGroup[$i][$type_id][$customer_group_id] = $target_rit;
                                    }
                                }
                            }

                            echo $this->Html->tag('td', $this->Number->format($totalSideTarget, false, array('places' => 0)), array(
                                'class' => 'text-center',
                                'style' => $tdStyle,
                            ));
                            echo $this->Html->tag('td', $this->Number->format($totalSidePencapaian, false, array('places' => 0)), array(
                                'class' => 'text-center',
                                'style' => $tdStyle,
                            ));
                            $grandTotalPencapaian += $totalSidePencapaian;
                            $grandTotalTarget += $totalSideTarget;
                    ?>
                </tr>
                <?php

                                if( !empty($grandTotalPencapaianGroup[$type_id][$customer_group_id]) ) {
                                    $grandTotalPencapaianGroup[$type_id][$customer_group_id] += $totalSidePencapaian;
                                } else {
                                    $grandTotalPencapaianGroup[$type_id][$customer_group_id] = $totalSidePencapaian;
                                }
                                if( !empty($grandTotalTargetGroup[$type_id][$customer_group_id]) ) {
                                    $grandTotalTargetGroup[$type_id][$customer_group_id] += $totalSideTarget;
                                } else {
                                    $grandTotalTargetGroup[$type_id][$customer_group_id] = $totalSideTarget;
                                }

                                $groupName[$type_id][$customer_group_id] = $group_name;
                            }

                            // if( empty($finishTotal[$temp_tye_id][$temp_group_id]) ) {
                            //     echo $this->element('blocks/revenues/tables/achievement_report', array(
                            //         'type_id' => $temp_tye_id,
                            //         'customer_group_id' => $temp_group_id,
                            //         'groupName' => $groupName,
                            //         'totalCnt' => $totalCnt,
                            //         'totalTargetGroup' => $totalTargetGroup,
                            //         'totalPencapaianGroup' => $totalPencapaianGroup,
                            //         'grandTotalTargetGroup' => $grandTotalTargetGroup,
                            //         'grandTotalPencapaianGroup' => $grandTotalPencapaianGroup,
                            //     ));
                            // }
                ?>
                <tr>
                    <?php 
                            echo $this->Html->tag('td', __('Total'), array(
                                'style' => 'font-weight: bold;'
                            ));

                            if( isset($totalCnt) ) {
                                for ($i=0; $i <= $totalCnt; $i++) {
                                    $totalPencapaian = 0;
                                    $totalTarget = 0;
                                    $nameVal = 'totalPencapaian'.$i;
                                    $nameValTarget = 'totalTarget'.$i;

                                    if( !empty($$nameVal) ) {
                                        $totalPencapaian = $$nameVal;
                                    }
                                    if( !empty($$nameValTarget) ) {
                                        $totalTarget = $$nameValTarget;
                                    }
                                    echo $this->Html->tag('td', $this->Number->format($totalTarget, false, array('places' => 0)));
                                    echo $this->Html->tag('td', $this->Number->format($totalPencapaian, false, array('places' => 0)));
                                }
                            }

                            echo $this->Html->tag('td', $this->Number->format($grandTotalTarget, false, array('places' => 0)));
                            echo $this->Html->tag('td', $this->Number->format($grandTotalPencapaian, false, array('places' => 0)));
                    ?>
                </tr>
                <?php
                        }
                ?>
            </tbody>
        </table>
        <?php 
                if( $data_action != 'excel' ) {
                    if(empty($ttujs)){
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

            if(!empty($ttujs)){
                $grandTotalPencapaian = 0;
                $grandTotalTarget = 0;

                foreach ($ttujs as $key => $value) {
                    $id = $value['Customer']['id'];
                    $customer_name = !empty($value['Customer']['code'])?$value['Customer']['code']:'-';
                    $totalSidePencapaian = 0;
                    $totalSideTarget = 0;

                    $content = $this->Html->tag('td', $no);
                    $content .= $this->Html->tag('td', $customer_name);

                    if( ($totalCnt) ) {
                        for ($i=0; $i <= $totalCnt; $i++) {
                            $pencapaian = !empty($cntPencapaian[$value['Customer']['id']][date('Y-m', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear))])?$cntPencapaian[$value['Customer']['id']][date('Y-m', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear))]:'-';
                            $target_rit = !empty($targetUnit[$value['Customer']['id']][date('Y-m', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear))])?$targetUnit[$value['Customer']['id']][date('Y-m', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear))]:'-';
                            $bgStyle = '';

                            if( is_numeric($pencapaian) && is_numeric($target_rit) ) {
                                if( !empty($pencapaian) && $pencapaian >= $target_rit ) {
                                    $bgStyle = 'background-color:#dff0d8;color:#3c763d;';
                                } else {
                                    $bgStyle = 'background-color:#f2dede;color:#a94442;';
                                }
                            }

                            $content .= $this->Html->tag('td', $target_rit, array(
                                'style' => 'text-align: center;'
                            ));
                            $content .= $this->Html->tag('td', $pencapaian, array(
                                'style' => 'text-align: center;'.$bgStyle
                            ));

                            $nameVal = 'totalPencapaian'.$i;
                            $nameValTarget = 'totalTarget'.$i;

                            if( empty($$nameVal) ) {
                                $$nameVal = 0;
                            }
                            if( empty($$nameValTarget) ) {
                                $$nameValTarget = 0;
                            }

                            if( is_numeric($pencapaian) ) {
                                $$nameVal += $pencapaian;
                                $totalSidePencapaian += $pencapaian;
                            }

                            if( is_numeric($target_rit) ) {
                                $$nameValTarget += $target_rit;
                                $totalSideTarget += $target_rit;
                            }
                        }
                        $bgStyle = '';

                        if( is_numeric($totalSidePencapaian) && is_numeric($totalSideTarget) && !empty($totalSideTarget) ) {
                            if( !empty($totalSidePencapaian) && $totalSidePencapaian >= $totalSideTarget ) {
                                $bgStyle = 'background-color:#dff0d8;color:#3c763d;';
                            } else {
                                $bgStyle = 'background-color:#f2dede;color:#a94442;';
                            }
                        }

                        $content .= $this->Html->tag('td', $this->Number->format($totalSideTarget, false, array('places' => 0)), array(
                            'style' => 'text-align: right;'
                        ));
                        $content .= $this->Html->tag('td', $this->Number->format($totalSidePencapaian, false, array('places' => 0)), array(
                            'style' => 'text-align: right;'.$bgStyle
                        ));
                        $grandTotalPencapaian += $totalSidePencapaian;
                        $grandTotalTarget += $totalSideTarget;
                    }

                    $each_loop_message .= $this->Html->tag('tr', $content);
                    $no++;
                }

                $content = $this->Html->tag('td', __('Total'), array(
                    'colspan' => 2,
                    'style' => 'text-align: right;font-weight: bold;'
                ));

                if( isset($totalCnt) ) {
                    for ($i=0; $i <= $totalCnt; $i++) {
                        $totalPencapaian = 0;
                        $totalTarget = 0;
                        $nameVal = 'totalPencapaian'.$i;
                        $nameValTarget = 'totalTarget'.$i;
                        $bgStyle = '';

                        if( !empty($$nameVal) ) {
                            $totalPencapaian = $$nameVal;
                        }
                        if( !empty($$nameValTarget) ) {
                            $totalTarget = $$nameValTarget;
                        }

                        if( is_numeric($totalPencapaian) && is_numeric($totalTarget) ) {
                            if( !empty($totalPencapaian) && $totalPencapaian >= $totalTarget ) {
                                $bgStyle = 'background-color:#dff0d8;color:#3c763d;';
                            } else {
                                $bgStyle = 'background-color:#f2dede;color:#a94442;';
                            }
                        }

                        $content .= $this->Html->tag('td', $this->Number->format($totalTarget, false, array('places' => 0)), array(
                            'style' => 'text-align: center;font-weight: bold;'
                        ));
                        $content .= $this->Html->tag('td', $this->Number->format($totalPencapaian, false, array('places' => 0)), array(
                            'style' => 'text-align: center;font-weight: bold;'.$bgStyle
                        ));
                    }
                }
                $bgStyle = '';

                if( is_numeric($grandTotalPencapaian) && is_numeric($grandTotalTarget) ) {
                    if( !empty($grandTotalPencapaian) && $grandTotalPencapaian >= $grandTotalTarget ) {
                        $bgStyle = 'background-color:#dff0d8;color:#3c763d;';
                    } else {
                        $bgStyle = 'background-color:#f2dede;color:#a94442;';
                    }
                }

                $content .= $this->Html->tag('td', $this->Number->format($grandTotalTarget, false, array('places' => 0)), array(
                    'style' => 'text-align: center;font-weight: bold;'
                ));
                $content .= $this->Html->tag('td', $this->Number->format($grandTotalPencapaian, false, array('places' => 0)), array(
                    'style' => 'text-align: center;font-weight: bold;'.$bgStyle
                ));
                $each_loop_message .= $this->Html->tag('tr', $content);
            }

            $topHeader = '';
            $cityHeader = '';

            if( isset($totalCnt) ) {
                for ($i=0; $i <= $totalCnt; $i++) {
                    $formatDate = 'F';

                    if( $fromYear != $toYear ) {
                        $formatDate = 'F Y';
                    }
                    $topHeader .= $this->Html->tag('th', date($formatDate, mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear)), array(
                        'colspan' => 2,
                        'style' => 'text-align: center;'
                    ));
                }

                $topHeader .= $this->Html->tag('th', __('Total Target'), array(
                    'style' => 'text-align: center;',
                    'rowspan' => 2,
                ));

                $topHeader .= $this->Html->tag('th', __('Total Pencapaian'), array(
                    'style' => 'text-align: center;',
                    'rowspan' => 2,
                ));
            }

            if( isset($totalCnt) ) {
                for ($i=0; $i <= $totalCnt; $i++) {
                    $cityHeader .= $this->Html->tag('th', __('TARGET UNIT'), array(
                        'style' => 'text-align: center;'
                    ));
                    $cityHeader .= $this->Html->tag('th', __('PENCAPAIAN'), array(
                        'style' => 'text-align: center;'
                    ));
                }
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
                    <th rowspan="2">No. </th>
                    <th rowspan="2">Customer</th>
                    $topHeader
                </tr>
                <tr style="$table_tr_head">
                    $cityHeader
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
    function cellStyler(value,row,index, rel){
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

    function calcPencapaian (value,row,index) {
        var target = parseInt(row.total_target_side.replace(/,/gi, ""));
        var value = parseInt(value.replace(/,/gi, ""));

        if( !isNaN(value) && !isNaN(target) && value != 0 && value >= target ) {
            return 'background-color:#dff0d8;color:#3c763d;';
        } else {
            return 'background-color:#f2dede;color:#a94442;';
        }
    }
</script>