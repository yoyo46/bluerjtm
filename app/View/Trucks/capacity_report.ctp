<?php 
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $currentUrl = array(
            'controller' => 'trucks', 
            'action' => 'capacity_report', 
        );

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
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Pencarian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php 
            echo $this->Form->create('Truck', array(
                'url'=> $this->Html->url( array(
                    'controller' => 'trucks',
                    'action' => 'search',
                    'capacity_report'
                )), 
                'role' => 'form',
                'inputDefaults' => array('div' => false),
            ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('customer_code',array(
                                'label'=> __('Alokasi'),
                                'class'=>'form-control',
                                'required' => false,
                            ));
                    ?>
                </div>
                <div class="form-group action">
                    <?php
                            echo $this->Form->button('<i class="fa fa-search"></i> '.__('Cari'), array(
                                'div' => false, 
                                'class'=> 'btn btn-success btn-sm',
                                'type' => 'submit',
                            ));
                            echo $this->Html->link('<i class="fa fa-refresh"></i> '.__('Reset'), $currentUrl, array(
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
                    $urlDefault['action'] = 'capacity_report';

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
        <table class="table table-bordered report" border="<?php echo $border; ?>">
            <thead>
                <tr>
                    <?php 
                            echo $this->Html->tag('th', $this->Common->getSorting('Customer.code', __('ALOKASI')), array(
                                'rowspan' => 2,
                                'class' => 'text-middle text-center',
                                'style' => $tdStyle,
                            ));

                            echo $this->Html->tag('th', __('JUMLAH TRUK PER KAPASITAS'), array(
                                'colspan' => count($capacities),
                                'class' => 'text-center',
                                'style' => $tdStyle,
                            ));

                            echo $this->Html->tag('th', __('Total'), array(
                                'class' => 'text-center',
                                'style' => $tdStyle,
                                'rowspan' => 2,
                            ));
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
                                $customer_id = $customer['Customer']['id'];
                ?>
                <tr>
                    <?php
                            if( !empty($capacities) ) {
                                $totalRit = 0;

                                echo $this->Html->tag('td', $customer['Customer']['code'], array(
                                    'style' => $tdStyle,
                                ));

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
                        }

                        if( !empty($capacities) && !empty($truckWithoutAlocations) ) {
                ?>
                <tr>
                    <?php
                            $customer_id = 0;
                            echo $this->Html->tag('td', __('-'), array(
                                'style' => $tdStyle.'font-weight:bold;text-align:right;',
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

                        if( empty($capacities) || empty($truckWithoutAlocations) ) {
                            echo $this->Html->tag('tr', $this->Html->tag('td', __('Data tidak ditemukan'), array(
                                'colspan' => '7'
                            )));
                        } else {
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
                <?php 
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
        $tcpdf->AddPage();
        // Table with rowspans and THEAD
        $table_tr_head = 'background-color: #3C8DBC; border-right: 1px solid #FFFFFF; color: #FFFFFF; font-weight: bold; padding: 0 10px; text-align: left;';
        $table_th = 'padding-top: 3px';
        $table = 'width:100%;font-size: 24px; border: 1px solid #CCC; border-collapse: collapse; padding: 0; margin: 0;';
        
        $each_loop_message = '';
        $no = 1;

        if( !empty($capacities) ) {
            if(!empty($customers)){
                foreach ($customers as $customer):
                    $content = $this->Html->tag('td', $no, array(
                        'style' => 'text-align: center;',
                    ));
                    
                    $customer_id = $customer['Customer']['id'];
                    $content .= $this->Html->tag('td', $customer['Customer']['code'], array(
                        'style' => 'text-align: center;',
                    ));

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
                        $content .= $this->Html->tag('th', $kapasitas, array(
                            'style' => 'text-align: center;',
                        ));
                    }

                    $content .= $this->Html->tag('th', $totalRit, array(
                        'style' => 'text-align: center;',
                    ));

                    $each_loop_message .= $this->Html->tag('tr', $content);
                    $no++;
                endforeach;
            }

            if(!empty($truckWithoutAlocations)){
                if( !empty($capacities) ) {
                    $content = $this->Html->tag('td', $no, array(
                        'style' => 'text-align: center;',
                    ));
                    
                    $customer_id = 0;
                    $content .= $this->Html->tag('td', '-', array(
                        'style' => 'text-align: center;',
                    ));

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
                        $content .= $this->Html->tag('th', $kapasitas, array(
                            'style' => 'text-align: center;',
                        ));
                    }

                    $content .= $this->Html->tag('th', $totalRit, array(
                        'style' => 'text-align: center;',
                    ));
                    $each_loop_message .= $this->Html->tag('tr', $content);
                }
            }
        }

        if( empty($capacities) || empty($truckWithoutAlocations) ) {
            $each_loop_message .= '<tr><td colspan="4">data tidak tersedia</td></tr>';
        } else {
            $content = $this->Html->tag('th', __('Jumlah'), array(
                'style' => 'text-align: center;font-weight:bold;',
                'colspan' => 2,
            ));

            if( !empty($total) ) {
                $totalRit = 0;

                foreach ($total as $key => $value) {
                    $totalRit += $value;
                    $content .= $this->Html->tag('th', $value, array(
                        'style' => 'text-align: center;font-weight:bold;',
                    ));
                }

                $content .= $this->Html->tag('td', $totalRit, array(
                    'style' => 'text-align: center;font-weight:bold;',
                ));
            }

            $each_loop_message .= $this->Html->tag('tr', $content);
        }

        $header = $this->Html->tag('th', __('Alokasi'), array(
            'rowspan' => 2,
            'style' => 'text-align: center;',
        ));

        $header .= $this->Html->tag('th', __('JUMLAH TRUK PER KAPASITAS'), array(
            'colspan' => count($capacities),
            'style' => 'text-align: center;',
        ));

        $header .= $this->Html->tag('th', __('Total'), array(
            'style' => 'text-align: center;',
            'rowspan' => 2,
        ));

        $date_title = $sub_module_title;
        $subHeader = '';
        $totalCustomer = 0;
        $print_label = $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
            'style' => 'font-size: 24px;font-style: italic;margin-top: 10px;'
        ));

        if( !empty($capacities) ) {
            $totalCustomer = count($customers);
            $totalRit = 0;

            foreach ($capacities as $key => $capacity) {
                $totalRit += $capacity;
                $subHeader .= $this->Html->tag('th', $capacity, array(
                    'style' => 'text-align: center;',
                ));
            }

            $subHeader = $this->Html->tag('tr', $subHeader, array(
                'style' => $table_tr_head,
            ));
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
                $subHeader
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