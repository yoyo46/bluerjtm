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
                    if( !empty($cities) ) {
            ?>
            <div class="list-field">
                <?php 
                        echo $this->Html->link('<i class="fa fa-th-large"></i> Kolom Laporan', 'javascript:', array(
                            'escape' => false,
                            'class' => 'show',
                        ));                        
                ?>
                <ul>
                    <?php 
                            echo $this->Html->link('<i class="fa fa-times"></i>', 'javascript:', array(
                                'escape' => false,
                                'class' => 'close'
                            ));

                            foreach ($cities as $key => $value) {
                                $slug = $this->Common->toSlug($value);
                    ?>
                    <li>
                        <div class="checkbox">
                            <label>
                                <?php
                                        echo $this->Form->checkbox($this->Common->toSlug($value), array(
                                            'data-field' => sprintf('%s-%s', $slug, $key),
                                            'data-parent' => 'col-alokasi',
                                            'checked' => true,
                                        ));
                                        echo $value;
                                ?>
                            </label>
                        </div>
                    </li>
                    <?php
                            }
                    ?>
                </ul>
            </div>
            <?php
                    }
                    
                    echo $this->Html->link('<i class="fa fa-download"></i> Download Excel', array(
                        $data_type,
                        'excel'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-success pull-right'
                    ));
                    echo $this->Html->link('<i class="fa fa-download"></i> Download PDF', array(
                        $data_type,
                        'pdf'
                    ), array(
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
                            echo $this->Html->tag('th', __('NO. POL'), array(
                                'class' => 'text-center text-middle',
                                'rowspan' => 2,
                                'style' => $tdStyle,
                            ));
                            echo $this->Html->tag('th', __('Supir'), array(
                                'class' => 'text-center text-middle',
                                'rowspan' => 2,
                                'style' => $tdStyle,
                            ));
                            echo $this->Html->tag('th', __('Kapasitas'), array(
                                'style' => 'width: 100px;'.$tdStyle,
                                'class' => 'text-center text-middle',
                                'rowspan' => 2,
                            ));
                            echo $this->Html->tag('th', __('Alokasi'), array(
                                'style' => 'width: 100px;'.$tdStyle,
                                'class' => 'text-center text-middle',
                                'rowspan' => 2,
                            ));

                            if( !empty($cities) ) {
                                echo $this->Html->tag('th', __('Tujuan'), array(
                                    'colspan' => count($cities),
                                    'class' => 'text-center col-alokasi',
                                    'style' => $tdStyle,
                                ));
                            }

                            echo $this->Html->tag('th', __('Target RIT'), array(
                                'style' => 'width: 120px;'.$tdStyle,
                                'class' => 'text-center text-middle',
                                'rowspan' => 2,
                            ));
                            echo $this->Html->tag('th', __('Total RIT'), array(
                                'style' => 'width: 80px;'.$tdStyle,
                                'class' => 'text-center text-middle',
                                'rowspan' => 2,
                            ));
                            echo $this->Html->tag('th', __('Pencapaian<br>(%)'), array(
                                'style' => 'width: 120px;'.$tdStyle,
                                'class' => 'text-center text-middle',
                                'rowspan' => 2,
                            ));
                            echo $this->Html->tag('th', __('OVER LT'), array(
                                'style' => 'width: 80px;'.$tdStyle,
                                'class' => 'text-center text-middle',
                                'rowspan' => 2,
                            ));
                            echo $this->Html->tag('th', __('Q LT<br>(%)'), array(
                                'style' => 'width: 80px;'.$tdStyle,
                                'class' => 'text-center text-middle',
                                'rowspan' => 2,
                            ));
                    ?>
                </tr>
                <tr>
                    <?php 
                            if( !empty($cities) ) {
                                foreach ($cities as $key => $value) {
                                    $slug = $this->Common->toSlug($value);
                                    echo $this->Html->tag('th', $value, array(
                                        'class' => 'text-center '.sprintf('%s-%s', $slug, $key),
                                        'style' => 'width: 120px;'.$tdStyle,
                                    ));
                                }
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
                                $overTime = !empty($value['OverTime'])?$value['OverTime']:0;
                                $qLt = 0;

                                if( !empty($overTime) ) {
                                    $qLt = round(($overTime/$total)*100, 2);
                                }

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
                            echo $this->Html->tag('td', $this->Html->tag('div', $value['Truck']['nopol'], array(
                                'style' => 'width: 80px;',
                            )));
                            echo $this->Html->tag('td', $this->Html->tag('div', $value['Driver']['driver_name'], array(
                                'style' => 'width: 120px;',
                            )));
                            echo $this->Html->tag('td', $this->Html->tag('div', $value['Truck']['capacity'], array(
                                'style' => 'width: 80px;'.$tdStyle,
                            )), array(
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('td', $this->Html->tag('div', !empty($value['Customer']['customer_name'])?$value['Customer']['customer_name']:'-', array(
                                'style' => 'width: 150px;',
                            )), array(
                                'class' => 'text-center',
                            ));

                            if( !empty($cities) ) {
                                foreach ($cities as $key => $city) {
                                    $slug = $this->Common->toSlug($city);
                                    $keyCity = array_search($key, $cityArr);
                                    $cnt = 0;

                                    if( is_numeric($keyCity) && !empty($value['City'][$keyCity][0]['cnt']) ) {
                                        $cnt = $value['City'][$keyCity][0]['cnt'];
                                    }

                                    echo $this->Html->tag('td', $cnt, array(
                                        'class' => 'text-center '.sprintf('%s-%s', $slug, $key),
                                        'style' => $tdStyle,
                                    ));
                                }
                            }
                            echo $this->Html->tag('td', $target_rit, array(
                                'class' => 'text-center',
                                'style' => $tdStyle,
                            ));
                            echo $this->Html->tag('td', $total, array(
                                'class' => 'text-center',
                                'style' => $tdStyle,
                            ));
                            echo $this->Html->tag('td', number_format($pencapaian, 2).' %', array(
                                'class' => 'text-center',
                                'style' => $tdStyle,
                            ));
                            echo $this->Html->tag('td', $overTime, array(
                                'class' => 'text-center',
                                'style' => $tdStyle,
                            ));
                            echo $this->Html->tag('td', $qLt, array(
                                'class' => 'text-center',
                                'style' => $tdStyle,
                            ));
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
                    if(empty($trucks)){
                        echo $this->Html->tag('p', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                        ));
                    }
        ?>
    </div><!-- /.box-body -->
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
                    $content .= $this->Html->tag('td', $value['Driver']['driver_name']);
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