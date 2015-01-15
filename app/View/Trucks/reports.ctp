<?php 
    if($action != 'pdf'){
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/trucks/search_report_truck');

        echo $this->Form->create('Truck', array(
            'url'=> $this->Html->url( array(
                'controller' => 'trucks',
                'action' => 'search',
                'reports'
            )), 
            'class' => 'form-inline text-right hidden-print',
            'inputDefaults' => array('div' => false),
        ));

        $find_sort = array(
            '' => __('Urutkan Berdasarkan'),
            '1' => __('No. Pol A - Z'),
            '2' => __('No. Pol Z - A'),
            '3' => __('Nama Supir Z - A'),
            '4' => __('Nama Supir Z - A'),
        );

        echo $this->Html->tag('div', $this->Form->input('Truck.sortby', array(
            'label'=> false,
            'options'=> $find_sort,
            'div' => false,
            'data-placeholder' => 'Order By',
            'autocomplete'=> false,
            'empty'=> false,
            'error' => false,
            'onChange' => 'submit()',
            'class' => 'form-control order-by-list'
        )), array(
            'class' => 'form-group'
        ));

        echo $this->Form->end();
?>
<section class="content invoice">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo __('Laporan Truk');?>
        <small class="pull-right">
            <?php
                if(!empty($from_date) || !empty($to_date)){
                    $text = 'periode ';
                    if(!empty($from_date)){
                        $text .= date('d/m/Y', strtotime($from_date));
                    }

                    if(!empty($to_date)){
                        if(!empty($from_date)){
                            $text .= ' - ';
                        }
                        $text .= date('d/m/Y', strtotime($to_date));
                    }

                    echo $text;
                }else{
                    echo __('Semua Truk');
                }
            ?>
        </small>
    </h2>
    <div class="row no-print">
        <div class="col-xs-12">
            <button class="btn btn-default" onclick="window.print();"><i class="fa fa-print"></i> Print</button>
            <button class="btn btn-success pull-right"><i class="fa fa-table"></i> Download Excel</button>
            <?php
                echo $this->Html->link('<i class="fa fa-download"></i> Download PDF', $this->here.'/pdf', array(
                    'escape' => false,
                    'class' => 'btn btn-primary pull-right'
                ));
            ?>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo __('Nopol');?></th>
                    <th><?php echo __('Merek');?></th>
                    <th><?php echo __('Pemilik');?></th>
                    <th><?php echo __('Jenis');?></th>
                    <th><?php echo __('Supir');?></th>
                    <th><?php echo __('Kapasitas');?></th>
                    <th><?php echo __('Tahun');?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if(!empty($trucks)){
                        foreach ($trucks as $key => $truck) {
                            $content = $this->Html->tag('td', $truck['Truck']['nopol']);
                            $content .= $this->Html->tag('td', $truck['TruckBrand']['name']);
                            $content .= $this->Html->tag('td', $truck['Truck']['atas_nama']);
                            $content .= $this->Html->tag('td', $truck['TruckCategory']['name']);
                            $content .= $this->Html->tag('td', $truck['Driver']['name']);
                            $content .= $this->Html->tag('td', $truck['Truck']['capacity']);
                            $content .= $this->Html->tag('td', $truck['Truck']['tahun']);

                            echo $this->Html->tag('tr', $content);
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data tidak ditemukan'), array(
                            'colspan' => '7'
                        )));
                    }
                ?>
            </tbody>
        </table>
    </div>
</section>
<?php
    }else{
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

        if(!empty($trucks)){
            foreach ($trucks as $truck):

                $content = $this->Html->tag('td', $no);
                $content .= $this->Html->tag('td', $truck['Truck']['nopol']);
                $content .= $this->Html->tag('td', $truck['TruckBrand']['name']);
                $content .= $this->Html->tag('td', $truck['Truck']['atas_nama']);
                $content .= $this->Html->tag('td', $truck['TruckCategory']['name']);
                $content .= $this->Html->tag('td', $truck['Driver']['name']);
                $content .= $this->Html->tag('td', $truck['Truck']['capacity']);
                $content .= $this->Html->tag('td', $truck['Truck']['tahun']);

                $each_loop_message .= $this->Html->tag('tr', $content);
                $no++;
            endforeach;
        }else{
            $each_loop_message .= '<tr>
                <td colspan="7">data tidak tersedia</td>
            </tr>';
        }   

$date_title = sprintf('Tanggal %s sampai %s', $this->Common->customDate($from_date), $this->Common->customDate($to_date));
$total_trucks = count($trucks);
$tbl = <<<EOD

      <div class="clearfix container_16" id="content">

        <h2 class="grid_8" style="text-align: center;">Laporan Truk</h2>
        <h3 style="text-align: center;">$date_title</h3><br>
        <h4>Total Truk : $total_trucks</h4>
        <table cellpadding="2" cellspacing="2" nobr="true" style="$table">
            <tbody>
            <tr style="$table_tr_head">
                <th>No. </th>
                <th>Nopol</th>
                <th>Merek</th>
                <th>Pemilik</th>
                <th>Jenis</th>
                <th>Supir</th>
                <th>Kapasitas</th>
                <th>Tahun</th>
            </tr>
          
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