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
        <table class="table table-bordered report" border="<?php echo $border; ?>">
            <thead>
                <tr>
                    <?php 
                            echo $this->Html->tag('th', $this->Common->getSorting('Customer.customer_name', __('ALOKASI')), array(
                                'rowspan' => 2,
                                'class' => 'text-middle text-center',
                                'style' => $tdStyle,
                            ));

                            echo $this->Html->tag('th', __('JUMLAH TRUK PER KAPASITAS'), array(
                                'colspan' => count($capacities),
                                'class' => 'text-center',
                                'style' => $tdStyle,
                            ));

                            if( !empty($capacities) ) {
                                echo $this->Html->tag('th', __('Total'), array(
                                    'class' => 'text-center',
                                    'style' => $tdStyle,
                                    'rowspan' => 2,
                                ));
                            }
                    ?>
                </tr>
                <tr>
                    <?php 
                            if( !empty($capacities) ) {
                                foreach ($capacities as $key => $capacity) {
                                    echo $this->Html->tag('th', $capacity, array(
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
                            $total = array();

                            foreach ($customers as $key => $customer) {
                ?>
                <tr>
                    <?php
                            $customer_id = $customer['Customer']['id'];
                            echo $this->Html->tag('td', $customer['Customer']['customer_name'], array(
                                'style' => $tdStyle,
                            ));

                            if( !empty($capacities) ) {
                                $totalRit = 0;

                                foreach ($capacities as $key => $capacity) {
                                    $kapasitas = 0;

                                    if( !empty($truckArr[$customer_id][$capacity]) ) {
                                        $kapasitas = $truckArr[$customer_id][$capacity];
                                    }

                                    if( !empty($total[$capacity]) ) {
                                        $total[$capacity] += $kapasitas;
                                    } else {
                                        $total[$capacity] = $kapasitas;
                                    }

                                    $totalRit += $kapasitas;
                                    echo $this->Html->tag('td', $kapasitas, array(
                                        'class' => 'text-center',
                                        'style' => $tdStyle,
                                    ));
                                }

                                echo $this->Html->tag('td', $totalRit, array(
                                    'class' => 'text-center',
                                    'style' => $tdStyle,
                                ));
                            }
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
                <tr>
                    <?php 
                            echo $this->Html->tag('th', __('Jumlah'), array(
                                'class' => 'text-center',
                                'style' => $tdStyle,
                            ));

                            if( !empty($total) ) {
                                $totalRit = 0;

                                foreach ($total as $key => $value) {
                                    $totalRit += $value;
                                    echo $this->Html->tag('th', $value, array(
                                        'class' => 'text-center',
                                        'style' => $tdStyle,
                                    ));
                                }

                                echo $this->Html->tag('td', $totalRit, array(
                                    'class' => 'text-center',
                                    'style' => $tdStyle,
                                ));
                            }
                    ?>
                </tr>
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
        $tcpdf->AddPage();
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
                $content .= $this->Html->tag('td', $customer['Customer']['customer_name'], array(
                    'style' => 'text-align: center;',
                ));

                if( !empty($capacities) ) {
                    $totalRit = 0;

                    foreach ($capacities as $key => $capacity) {
                        $kapasitas = 0;

                        if( !empty($truckArr[$customer_id][$capacity]) ) {
                            $kapasitas = $truckArr[$customer_id][$capacity];
                        }

                        if( !empty($total[$capacity]) ) {
                            $total[$capacity] += $kapasitas;
                        } else {
                            $total[$capacity] = $kapasitas;
                        }

                        $totalRit = $kapasitas;
                        $content .= $this->Html->tag('th', $kapasitas, array(
                            'style' => 'text-align: center;',
                        ));
                    }

                    $content .= $this->Html->tag('th', $totalRit, array(
                        'style' => 'text-align: center;',
                    ));
                }

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

        $header .= $this->Html->tag('th', __('JUMLAH TRUK PER KAPASITAS'), array(
            'colspan' => count($capacities),
            'style' => 'text-align: center;',
        ));

        if( !empty($capacities) ) {
            $header .= $this->Html->tag('th', __('Total'), array(
                'style' => 'text-align: center;',
                'rowspan' => 2,
            ));
        }

        $date_title = $sub_module_title;
        $totalCustomer = count($customers);
        $subHeader = '';

        if( !empty($capacities) ) {
            $totalRit = 0;

            foreach ($capacities as $key => $capacity) {
                $totalRit += $capacity;
                $subHeader .= $this->Html->tag('th', $capacity, array(
                    'style' => 'text-align: center;',
                ));
            }
        }

$tbl = <<<EOD

      <div class="clearfix container_16" id="content">

        <h3 style="text-align: center;">$date_title</h3><br>
        <h4>Total Alokasi : $totalCustomer</h4>
        <table cellpadding="2" cellspacing="2" nobr="true" style="$table">
            <thead>
                <tr style="$table_tr_head">
                    <th rowspan="2" style="text-align: center;">No. </th>
                    $header
                </tr>
                <tr style="$table_tr_head">
                    $subHeader
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