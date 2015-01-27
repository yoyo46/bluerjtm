<?php 
        if( empty($data_action) || ( !empty($data_action) && $data_action == 'excel' ) ){
            $this->Html->addCrumb($sub_module_title);
            $tdStyle = '';
            $border = 0;

            if( $data_action == 'excel' ) {
                header('Content-type: application/ms-excel');
                header('Content-Disposition: attachment; filename='.$sub_module_title.'.xls');
                $border = 1;
                $tdStyle = 'text-align: center;';
            } else {
                echo $this->element('blocks/trucks/search_report_point_perday');
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
    <?php 
            }
    ?>
        <table class="table table-bordered report" border="<?php echo $border; ?>">
            <thead>
                <tr>
                    <?php 
                            echo $this->Html->tag('th', __('Alokasi'), array(
                                'class' => 'text-middle text-center',
                                'style' => $tdStyle,
                            ));

                            for ($i=1; $i <= $lastDay; $i++) {
                                echo $this->Html->tag('th', $i, array(
                                    'class' => 'text-center',
                                    'style' => 'width: 100px;',
                                    'title' => date('l', mktime(0, 0, 0, date("m", strtotime($currentMonth)) , $i, date("Y", strtotime($currentMonth)))),
                                ));
                            }

                            echo $this->Html->tag('th', __('Total'), array(
                                'class' => 'text-middle text-center',
                                'style' => $tdStyle,
                            ));

                            echo $this->Html->tag('th', __('Target'), array(
                                'class' => 'text-middle text-center',
                                'style' => $tdStyle,
                            ));
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                        if(!empty($customers)){
                            $total = array();

                            foreach ($customers as $key => $customer) {
                ?>
                <tr>
                    <?php
                            $customer_id = $customer['Customer']['id'];
                            $totalMuatan = 0;

                            echo $this->Html->tag('td', $this->Html->tag('div', $customer['Customer']['code'], array(
                                'style' => 'width: 80px;',
                            )));

                            for ($i=1; $i <= $lastDay; $i++) {
                                $day = date('d', mktime(0, 0, 0, date("m", strtotime($currentMonth)) , $i, date("Y", strtotime($currentMonth))));
                                $muatan = !empty($dataTtuj[$customer_id][$day])?$dataTtuj[$customer_id][$day]:'-';
                                $totalMuatan += $muatan;

                                echo $this->Html->tag('td', $muatan, array(
                                    'class' => 'text-center',
                                    'style' => 'width: 100px;text-align: center;',
                                ));
                            }

                            echo $this->Html->tag('td', $this->Html->tag('div', $totalMuatan, array(
                                'style' => 'width: 60px;text-align: center;',
                            )), array(
                                'style' => $tdStyle,
                            ));

                            $target_rit = !empty($targetUnit[$customer['Customer']['id']][$currentMonth])?$targetUnit[$customer['Customer']['id']][$currentMonth]:'-';
                            echo $this->Html->tag('td', $target_rit, array(
                                'style' => 'width: 60px;text-align: center;',
                            ));
                    ?>
                </tr>
                <?php 

                            }
                        }else{
                            echo $this->Html->tag('tr', $this->Html->tag('td', __('Data tidak ditemukan'), array(
                                'colspan' => '7'
                            )));
                        }
                ?>
            </tbody>
        </table>
    <?php
            if( $data_action != 'excel' ) {
                echo $this->element('pagination');
    ?>
    </div>
    <?php 
            }
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
        
        $each_loop_message = '';
        $no = 1;

        if(!empty($customers)){
            foreach ($customers as $customer):
                $content = $this->Html->tag('td', $no, array(
                    'style' => 'text-align: center;',
                ));
                
                $customer_id = $customer['Customer']['id'];
                $totalMuatan = 0;

                $content .= $this->Html->tag('td', $customer['Customer']['code']);

                for ($i=1; $i <= $lastDay; $i++) {
                    $muatan = !empty($dataTtuj[$customer_id][date('d', mktime(0, 0, 0, date("m", strtotime($currentMonth)) , $i, date("Y", strtotime($currentMonth))))])?$dataTtuj[$customer_id][date('d', mktime(0, 0, 0, date("m", strtotime($currentMonth)) , $i, date("Y", strtotime($currentMonth))))]:'-';
                    $totalMuatan += $muatan;
                    $content .= $this->Html->tag('td', $muatan, array(
                        'style' => 'text-align: center;',
                    ));
                }

                $content .= $this->Html->tag('td', $totalMuatan, array(
                    'style' => 'text-align: center;',
                ));

                $target_rit = !empty($targetUnit[$customer['Customer']['id']][$currentMonth])?$targetUnit[$customer['Customer']['id']][$currentMonth]:'-';
                $content .= $this->Html->tag('td', $target_rit, array(
                    'style' => 'text-align: center;',
                ));

                $each_loop_message .= $this->Html->tag('tr', $content);
                $no++;
            endforeach;
        }else{
            $each_loop_message .= '<tr>
                <td colspan="7">data tidak tersedia</td>
            </tr>';
        }

        $header = $this->Html->tag('th', __('Alokasi'), array(
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

$tbl = <<<EOD

      <div class="clearfix container_16" id="content">

        <h3 style="text-align: center;">$date_title</h3><br>
        <table cellpadding="2" cellspacing="2" nobr="true" style="$table">
            <thead>
                <tr style="$table_tr_head">
                    <th rowspan="2" style="text-align: center;">No. </th>
                    $header
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