<?php 
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $cities = !empty($cities)?array_filter($cities):false;
        
        if( empty($data_action) || ( !empty($data_action) && $data_action == 'excel' ) ){
            $this->Html->addCrumb($sub_module_title);
            $addStyle = '';
            $tdStyle = '';
            $border = 0;
            $headerRowspan = false;
            $headerRowspanSub = false;
            $addClass = '';

            if( $data_action == 'excel' ) {
                header('Content-type: application/ms-excel');
                header('Content-Disposition: attachment; filename='.$sub_module_title.'.xls');
                $border = 1;
                $tdStyle = 'text-align: center;';
                $addClass = '';
                $headerRowspan = 2;
            }

            if( !empty($cities) ) {
                $headerRowspanSub = 2;
                $addStyle = 'width: 100%;height: 550px;';
                $addClass = 'easyui-datagrid';
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
                    'ritase_report',
                    0,
                    $data_type,
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
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('Ttuj.customer',array(
                                'label'=> __('Alokasi'),
                                'class'=>'form-control',
                                'required' => false,
                                'placeholder' => __('Alokasi')
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
                                'action' => 'ritase_report', 
                                $data_type,
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
                            echo $this->Form->label('Ttuj.type', __('Truk'));
                    ?>
                    <div class="row">
                        <div class="col-sm-4">
                            <?php 
                                    echo $this->Form->input('Ttuj.type',array(
                                        'label'=> false,
                                        'class'=>'form-control',
                                        'required' => false,
                                        'empty' => false,
                                        'options' => array(
                                            '1' => __('Nopol'),
                                            '2' => __('ID Truk'),
                                        ),
                                    ));
                            ?>
                        </div>
                        <div class="col-sm-8">
                            <?php 
                                    echo $this->Form->input('Ttuj.nopol',array(
                                        'label'=> false,
                                        'class'=>'form-control',
                                        'required' => false,
                                    ));
                            ?>
                        </div>
                    </div>
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
                    
                    $urlExcel = $this->passedArgs;
                    $urlExcel['data_action'] = 'excel';
                    echo $this->Html->link('<i class="fa fa-download"></i> Download Excel', $urlExcel, array(
                        'escape' => false,
                        'class' => 'btn btn-success pull-right'
                    ));
                    $urlPdf = $this->passedArgs;
                    $urlPdf['data_action'] = 'pdf';
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
                            echo $this->Html->tag('th', $this->Common->getSorting('Truck.nopol', __('NO. POL')), array(
                                'style' => 'text-align: center;width: 120px;vertical-align: middle;',
                                'data-options' => 'field:\'nopol\',width:120',
                                'rowspan' => $headerRowspan,
                            ));
                            echo $this->Html->tag('th', __('Supir'), array(
                                'style' => 'text-align: center;width: 120px;vertical-align: middle;',
                                'data-options' => 'field:\'driver\',width:120',
                                'rowspan' => $headerRowspan,
                            ));
                            echo $this->Html->tag('th', $this->Common->getSorting('Truck.capacity', __('Kapasitas')), array(
                                'style' => 'text-align: center;width: 100px;vertical-align: middle;',
                                'data-options' => 'field:\'capacity\',width:100',
                                'rowspan' => $headerRowspan,
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', $this->Common->getSorting('CustomerNoType.code', __('Alokasi')), array(
                                'style' => 'text-align: center;width: 150px;vertical-align: middle;',
                                'data-options' => 'field:\'alokasi\',width:100',
                                'rowspan' => $headerRowspan,
                            ));
                    ?>
                    <?php 
                            if( $data_action != 'excel' && !empty($cities) ) {
                    ?>
                </tr>
            </thead>
            <thead>
                <tr>
                    <?php 
                            }

                            if( !empty($cities) ) {
                                echo $this->Html->tag('th', __('Tujuan'), array(
                                    'data-options' => 'field:\'tujuan\'',
                                    'style' => 'text-align: center;vertical-align: middle;',
                                    'class' => 'col-alokasi',
                                    'colspan' => count($cities),
                                    'align' => 'center',
                                ));
                            }

                            echo $this->Html->tag('th', $this->Common->getSorting('CustomerNoType.target_rit', __('Target RIT')), array(
                                'style' => 'text-align: center;width: 120px;vertical-align: middle;',
                                'data-options' => 'field:\'date_receipt\',width:120',
                                'rowspan' => $headerRowspanSub,
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', __('Total RIT'), array(
                                'style' => 'text-align: center;width: 120px;vertical-align: middle;',
                                'data-options' => 'field:\'date_receipt\',width:80',
                                'rowspan' => $headerRowspanSub,
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', __('Pencapaian<br>(%)'), array(
                                'data-options' => 'field:\'pencapaian\',width:120',
                                'style' => 'text-align: center;width: 120px;vertical-align: middle;',
                                'rowspan' => $headerRowspanSub,
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', __('OVER LT'), array(
                                'data-options' => 'field:\'over_lt\',width:80',
                                'style' => 'text-align: center;width: 80px;vertical-align: middle;',
                                'rowspan' => $headerRowspanSub,
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', __('Q LT<br>(%)'), array(
                                'data-options' => 'field:\'q_lt\',width:80',
                                'style' => 'text-align: center;width: 80px;vertical-align: middle;',
                                'rowspan' => $headerRowspanSub,
                                'align' => 'center',
                            ));
                    ?>
                </tr>
                <?php 
                        if( !empty($cities) ) {
                            echo '<tr>';

                            foreach ($cities as $key => $value) {
                                $slug = $this->Common->toSlug($value);
                                echo $this->Html->tag('th', $value, array(
                                    'data-options' => 'field:\''.$slug.'\',width:120',
                                    'class' => sprintf('%s-%s', $slug, $key),
                                    'style' => 'width: 120px;text-align:center:vertical-align:middle;',
                                    'align' => 'center',
                                ));
                            }

                            echo '</tr>';
                        }
                ?>
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

                                if( !empty($value['CustomerNoType']['target_rit']) ) {
                                    $target_rit = $value['CustomerNoType']['target_rit'];
                                    $pencapaian = ( $total / $target_rit ) * 100;
                                } else {
                                    $pencapaian = 0;
                                    $target_rit = 0;
                                }
                ?>
                <tr>
                    <?php 
                            $link_truck = $this->Html->link($value['Truck']['nopol'], array(
                                'controller' => 'revenues',
                                'action' => 'detail_ritase',
                                $id
                            ));
                            echo $this->Html->tag('td', $link_truck);
                            echo $this->Html->tag('td', !empty($value['Driver']['driver_name'])?$value['Driver']['driver_name']:false);
                            echo $this->Html->tag('td', $value['Truck']['capacity'], array(
                                'style' => 'text-align:center;',
                            ));
                            echo $this->Html->tag('td', !empty($value['CustomerNoType']['code'])?$value['CustomerNoType']['code']:'-', array(
                                'class' => 'text-left',
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

            if(!empty($trucks)){
                foreach ($trucks as $key => $value) {
                    $cityArr = Set::extract('/City/Ttuj/to_city_id', $value);
                    $total = !empty($value['Total'])?$value['Total']:0;

                    if( !empty($value['CustomerNoType']['target_rit']) ) {
                        $target_rit = $value['CustomerNoType']['target_rit'];
                        $pencapaian = ( $total / $target_rit ) * 100;
                    } else {
                        $pencapaian = 0;
                        $target_rit = 0;
                    }

                    $content = $this->Html->tag('td', $no);
                    $content .= $this->Html->tag('td', $value['Truck']['nopol']);
                    $content .= $this->Html->tag('td', !empty($value['Driver']['driver_name'])?$value['Driver']['driver_name']:false);
                    $content .= $this->Html->tag('td', $value['Truck']['capacity']);
                    $content .= $this->Html->tag('td', !empty($value['CustomerNoType']['code'])?$value['CustomerNoType']['code']:'-');
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