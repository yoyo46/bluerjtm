<?php 
        if( empty($data_action) ){
            $this->Html->addCrumb($sub_module_title);
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
            echo $this->Form->create('Revenue', array(
                'url'=> $this->Html->url( array(
                    'controller' => 'revenues',
                    'action' => 'search',
                    'ritase_report'
                )), 
                'role' => 'form',
                'inputDefaults' => array('div' => false),
            ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->label('date', __('Tanggal'));
                    ?>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php 
                                echo $this->Form->input('Ttuj.date',array(
                                    'label'=> false,
                                    'class'=>'form-control pull-right date-range',
                                    'required' => false,
                                ));
                        ?>
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
                                'action' => 'ritase_report', 
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
                            echo $this->Form->input('Ttuj.nopol',array(
                                'label'=> __('Nopol'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('Nopol')
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('Ttuj.driver_name',array(
                                'label'=> __('Nama Supir'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('Nama Supir')
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
                    echo $this->Html->link('<i class="fa fa-download"></i> Download Excel', array(
                        'excel'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-success pull-right'
                    ));
                    echo $this->Html->link('<i class="fa fa-download"></i> Download PDF', array(
                        'pdf'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-primary pull-right'
                    ));
            ?>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered" style="min-width: 1500px;">
            <thead>
                <tr>
                    <?php 
                            echo $this->Html->tag('th', __('NO. POL'), array(
                                'style' => 'width: 100px;',
                                'class' => 'text-center text-middle',
                                'rowspan' => 2,
                            ));
                            echo $this->Html->tag('th', __('Supir'), array(
                                'style' => 'width: 120px;',
                                'class' => 'text-center text-middle',
                                'rowspan' => 2,
                            ));
                            echo $this->Html->tag('th', __('Kapasitas'), array(
                                'style' => 'width: 100px;',
                                'class' => 'text-center text-middle',
                                'rowspan' => 2,
                            ));
                            echo $this->Html->tag('th', __('Alokasi'), array(
                                'style' => 'width: 100px;',
                                'class' => 'text-center text-middle',
                                'rowspan' => 2,
                            ));
                            echo $this->Html->tag('th', __('Total'), array(
                                'style' => 'width: 80px;',
                                'class' => 'text-center text-middle',
                                'rowspan' => 2,
                            ));
                            echo $this->Html->tag('th', __('Target RIT'), array(
                                'style' => 'width: 120px;',
                                'class' => 'text-center text-middle',
                                'rowspan' => 2,
                            ));
                            echo $this->Html->tag('th', __('Tujuan'), array(
                                'colspan' => count($cities),
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('th', __('Pencapaian (%)'), array(
                                'style' => 'width: 120px;',
                                'class' => 'text-center text-middle',
                                'rowspan' => 2,
                            ));
                    ?>
                </tr>
                <tr>
                    <?php 
                            if( !empty($cities) ) {
                                foreach ($cities as $key => $value) {
                                    echo $this->Html->tag('th', $value);
                                }
                            } else {
                                echo '<th>&nbsp;</th>';
                            }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                        if(!empty($trucks)){
                            foreach ($trucks as $key => $value) {
                                $id = $value['Truck']['id'];
                                $cityArr = Set::extract('/City/Ttuj/to_city_id', $value);
                                $total = !empty($value['Total'])?$value['Total']:0;

                                if( !empty($value['Customer']['target_rit']) ) {
                                    $target_rit = $value['Customer']['target_rit'];
                                    $pencapaian = ( $total / $target_rit ) * 100;
                                } else {
                                    $pencapaian = 0;
                                    $target_rit = 0;
                                }
                ?>
                <tr>
                    <?php 
                            echo $this->Html->tag('td', $value['Truck']['nopol']);
                            echo $this->Html->tag('td', $value['Driver']['name']);
                            echo $this->Html->tag('td', $value['Truck']['capacity'], array(
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('td', !empty($value['Customer']['name'])?$value['Customer']['name']:'-', array(
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('td', $total, array(
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('td', $target_rit, array(
                                'class' => 'text-center',
                            ));

                            if( !empty($cities) ) {
                                foreach ($cities as $key => $city) {
                                    $keyCity = array_search($key, $cityArr);
                                    $cnt = 0;

                                    if( is_numeric($keyCity) && !empty($value['City'][$keyCity][0]['cnt']) ) {
                                        $cnt = $value['City'][$keyCity][0]['cnt'];
                                    }

                                    echo $this->Html->tag('td', $cnt, array(
                                        'class' => 'text-center',
                                    ));
                                }
                            }
                            echo $this->Html->tag('td', number_format($pencapaian, 2).' %', array(
                                'class' => 'text-center',
                            ));
                    ?>
                </tr>
                <?php
                            }
                        }else{
                            echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                                'class' => 'alert alert-warning text-center',
                                'colspan' => '10'
                            )));
                        }
                ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
</div>
<?php 
        } else if($data_action == 'excel') {
            $this->PhpExcel->createWorksheet()->setDefaultFont('Calibri', 12);
            $cntCol = !empty($cities)?count($cities):1;

            $table = array(
                array('label' => __('NO. POL'), 'filter' => true, 'width' => 10),
                array('label' => __('Supir'), 'filter' => true, 'width' => 12),
                array('label' => __('Kapasitas'), 'filter' => true, 'width' => 10),
                array('label' => __('Alokasi'), 'filter' => true, 'width' => 15),
                array('label' => __('Total'), 'filter' => true, 'width' => 10),
                array('label' => __('Target RIT'), 'filter' => true, 'width' => 12),
                array('label' => __('Tujuan'), 'filter' => false),
            );

            $this->PhpExcel->setActiveSheetIndex(0)->mergeCells('A1:A2');
            $this->PhpExcel->setActiveSheetIndex(0)->mergeCells('B1:B2');
            $this->PhpExcel->setActiveSheetIndex(0)->mergeCells('C1:C2');
            $this->PhpExcel->setActiveSheetIndex(0)->mergeCells('D1:D2');
            $this->PhpExcel->setActiveSheetIndex(0)->mergeCells('E1:E2');
            $this->PhpExcel->setActiveSheetIndex(0)->mergeCells('F1:F2');

            if( !empty($cities) ) {
                $colSpan = sprintf('G1:%s1', chr(71 + count($cities)-1));
                $this->PhpExcel->setActiveSheetIndex(0)->mergeCells($colSpan);
            }

            $this->PhpExcel->getActiveSheet(0)->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->PhpExcel->getActiveSheet(0)->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->PhpExcel->getActiveSheet(0)->getStyle('B1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->PhpExcel->getActiveSheet(0)->getStyle('B1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->PhpExcel->getActiveSheet(0)->getStyle('C1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->PhpExcel->getActiveSheet(0)->getStyle('C1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->PhpExcel->getActiveSheet(0)->getStyle('D1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->PhpExcel->getActiveSheet(0)->getStyle('D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->PhpExcel->getActiveSheet(0)->getStyle('E1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->PhpExcel->getActiveSheet(0)->getStyle('E1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->PhpExcel->getActiveSheet(0)->getStyle('F1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->PhpExcel->getActiveSheet(0)->getStyle('F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->PhpExcel->getActiveSheet(0)->getStyle('G1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $this->PhpExcel->getActiveSheet(0)->getStyle('G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            // add heading with different font and bold text
            $this->PhpExcel->addTableHeader($table, array(
                'name' => 'Cambria',
                'bold' => true
            ));

            $bodyTable = array(
                array('label' => '', 'filter' => false, 'width' => 10),
                array('label' => '', 'filter' => false, 'width' => 12),
                array('label' => '', 'filter' => false, 'width' => 10),
                array('label' => '', 'filter' => false, 'width' => 15),
                array('label' => '', 'filter' => false, 'width' => 10),
                array('label' => '', 'filter' => false, 'width' => 12),
            );

            if( !empty($cities) ) {
                foreach ($cities as $key => $value) {
                    $bodyTable[] = array('label' => $value, 'filter' => false, 'width' => 15);
                }
            }

            $this->PhpExcel->addTableHeader($bodyTable, array(
                'name' => 'Cambria',
                'bold' => false
            ));

            // add data
            if(!empty($trucks)){
                foreach ($trucks as $key => $value) {
                    $cityArr = Set::extract('/City/Ttuj/to_city_id', $value);
                    $total = !empty($value['Total'])?$value['Total']:0;

                    if( !empty($value['Customer']['target_rit']) ) {
                        $target_rit = $value['Customer']['target_rit'];
                        $pencapaian = ( $total / $target_rit ) * 100;
                    } else {
                        $pencapaian = 0;
                        $target_rit = 0;
                    }

                    $bodyTable = array(
                        $value['Truck']['nopol'],
                        $value['Driver']['name'],
                        $value['Truck']['capacity'],
                        !empty($value['Customer']['name'])?$value['Customer']['name']:'-',
                        $total,
                        $target_rit,
                    );

                    if( !empty($cities) ) {
                        foreach ($cities as $key => $city) {
                            $keyCity = array_search($key, $cityArr);
                            $cnt = 0;

                            if( is_numeric($keyCity) && !empty($value['City'][$keyCity][0]['cnt']) ) {
                                $cnt = $value['City'][$keyCity][0]['cnt'];
                            }

                            $bodyTable[] = $cnt;
                        }
                    }

                    $this->PhpExcel->addTableRow($bodyTable);
                }
            }

            // close table and output
            $this->PhpExcel->addTableFooter()->output($sub_module_title);
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

            if(!empty($trucks)){
                foreach ($trucks as $key => $value) {
                    $cityArr = Set::extract('/City/Ttuj/to_city_id', $value);
                    $total = !empty($value['Total'])?$value['Total']:0;

                    if( !empty($value['Customer']['target_rit']) ) {
                        $target_rit = $value['Customer']['target_rit'];
                        $pencapaian = ( $total / $target_rit ) * 100;
                    } else {
                        $pencapaian = 0;
                        $target_rit = 0;
                    }

                    $content = $this->Html->tag('td', $no);
                    $content .= $this->Html->tag('td', $value['Truck']['nopol']);
                    $content .= $this->Html->tag('td', $value['Driver']['name']);
                    $content .= $this->Html->tag('td', $value['Truck']['capacity']);
                    $content .= $this->Html->tag('td', !empty($value['Customer']['name'])?$value['Customer']['name']:'-');
                    $content .= $this->Html->tag('td', $total);
                    $content .= $this->Html->tag('td', $target_rit);

                    if( !empty($cities) ) {
                        foreach ($cities as $key => $city) {
                            $keyCity = array_search($key, $cityArr);
                            $cnt = 0;

                            if( is_numeric($keyCity) && !empty($value['City'][$keyCity][0]['cnt']) ) {
                                $cnt = $value['City'][$keyCity][0]['cnt'];
                            }

                            $content .= $this->Html->tag('td', $cnt);
                        }
                    }

                    $each_loop_message .= $this->Html->tag('tr', $content);
                    $no++;
                }
            }

            $cityHeader = '';
            $cityCnt = 0;

            if( !empty($cities) ) {
                $cityCnt = count($cities);

                foreach ($cities as $key => $value) {
                    $cityHeader .= $this->Html->tag('th', $value);
                }
            }

            $date_title = $sub_module_title;
$tbl = <<<EOD

      <div class="clearfix container_16" id="content">

        <h2 class="grid_8" style="text-align: center;">$date_title</h2>
        <table cellpadding="2" cellspacing="2" nobr="true" style="$table">
            <thead>
                <tr style="$table_tr_head">
                    <th rowspan="2">No. </th>
                    <th rowspan="2">NO. POL</th>
                    <th rowspan="2">Supir</th>
                    <th rowspan="2">Kapasitas</th>
                    <th rowspan="2">Alokasi</th>
                    <th rowspan="2">Total</th>
                    <th rowspan="2">Target RIT</th>
                    <th colspan="$cityCnt" style="text-align:center;">Tujuan</th>
                </tr>
                <tr style="$table_tr_head">
                    $cityHeader
                </tr>
            </thead>
            <tbody>          
                $each_loop_message
            </tbody>
        </table> 
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