<?php 
        $text = __('Semua Truk');
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
        }

        if( empty($data_action) || ( !empty($data_action) && $data_action == 'excel' ) ){
            $tdStyle = '';
            $border = 0;

            if( $data_action == 'excel' ) {
                header('Content-type: application/ms-excel');
                header('Content-Disposition: attachment; filename='.$sub_module_title.'.xls');
                $border = 1;
                $tdStyle = 'text-align: center;';
            } else {
                $this->Html->addCrumb($sub_module_title);
                echo $this->element('blocks/trucks/search_report_truck');

                $dataColumns = array(
                    'nomor_id' => array(
                        'name' => __('No. ID'),
                        'field_model' => 'Truck.id',
                        'display' => false,
                    ),
                    'nopol' => array(
                        'name' => __('Nopol'),
                        'field_model' => 'Truck.nopol',
                        'display' => true,
                    ),
                    'brand_name' => array(
                        'name' => __('Merek'),
                        'field_model' => 'TruckBrand.name',
                        'display' => true,
                    ),
                    'atas_nama' => array(
                        'name' => __('Pemilik'),
                        'field_model' => 'Truck.atas_nama',
                        'display' => true,
                    ),
                    'category_name' => array(
                        'name' => __('Jenis'),
                        'field_model' => 'TruckCategory.name',
                        'display' => true,
                    ),
                    'driver_name' => array(
                        'name' => __('Supir'),
                        'field_model' => 'Driver.driver_name',
                        'display' => true,
                    ),
                    'customer_code' => array(
                        'name' => __('Alokasi'),
                        'field_model' => 'CustomerNoType.code',
                        'display' => true,
                    ),
                    'capacity' => array(
                        'name' => __('Kapasitas'),
                        'field_model' => 'Truck.capacity',
                        'display' => true,
                    ),
                    'tahun' => array(
                        'name' => __('Tahun'),
                        'field_model' => 'Truck.tahun',
                        'display' => true,
                    ),
                    'truck_facility' => array(
                        'name' => __('Truk Fasilitas'),
                        'field_model' => 'TruckFacility.name',
                        'display' => false,
                    ),
                    'no_rangka' => array(
                        'name' => __('No Rangka'),
                        'field_model' => 'Truck.no_rangka',
                        'display' => false,
                    ),
                );
                $showHideColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'show-hide' );
                $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
?>
<section class="content invoice">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo __('Laporan Truk');?>
        <small class="pull-right">
            <?php
                    echo $text;
            ?>
        </small>
    </h2>
    <div class="no-print print-action">
        <?php 
                if( !empty($showHideColumn) ) {
        ?>
        <div class="list-field pull-left">
            <div class="btn-group" id="columnDropdown">
                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Show/Hide Kolom <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li>Kolom Table</li>
                    <li class="divider"></li>
                    <?php 
                            echo $showHideColumn;
                    ?>
                    <li>
                        <div class="checkbox">
                            <label>
                                <?php
                                        echo $this->Form->checkbox('nomor_id', array(
                                            'data-field' => 'nomor_id',
                                        ));
                                        echo __('No. ID');
                                ?>
                            </label>
                        </div>
                    </li>
                    <li>
                        <div class="checkbox">
                            <label>
                                <?php
                                        echo $this->Form->checkbox('truck_facility', array(
                                            'data-field' => 'truck_facility',
                                        ));
                                        echo __('Fasilitas Truk');
                                ?>
                            </label>
                        </div>
                    </li>
                    <li>
                        <div class="checkbox">
                            <label>
                                <?php
                                        echo $this->Form->checkbox('no_rangka', array(
                                            'data-field' => 'no_rangka',
                                        ));
                                        echo __('No Rangka');
                                ?>
                            </label>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <?php 
                }
        ?>
        <div class="action pull-right">
            <?php
                    echo $this->Html->link('<i class="fa fa-download"></i> Download Excel', $this->here.'/excel', array(
                        'escape' => false,
                        'class' => 'btn btn-success pull-right'
                    ));
                    echo $this->Html->link('<i class="fa fa-download"></i> Download PDF', $this->here.'/pdf', array(
                        'escape' => false,
                        'class' => 'btn btn-primary pull-right'
                    ));
            ?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="table-responsive center-table">
        <?php 
                }
        ?>
        <table class="table table-bordered sorting" border="<?php echo $border; ?>">
            <thead>
                <tr>
                    <?php
                            if( !empty($fieldColumn) ) {
                                echo $fieldColumn;
                            }
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    if(!empty($trucks)){
                        foreach ($trucks as $key => $truck) {
                            $content = $this->Html->tag('td', str_pad($truck['Truck']['id'], 4, '0', STR_PAD_LEFT), array(
                                'class' => 'hide nomor_id',
                                'style' => 'text-align: left;'
                            ));
                            $content .= $this->Html->tag('td', $truck['Truck']['nopol'], array(
                                'style' => 'text-align: left;',
                                'class' => 'nopol',
                            ));
                            $content .= $this->Html->tag('td', $truck['TruckBrand']['name'], array(
                                'class' => 'brand_name',
                            ));
                            $content .= $this->Html->tag('td', $truck['Truck']['atas_nama'], array(
                                'class' => 'atas_nama',
                            ));
                            $content .= $this->Html->tag('td', $truck['TruckCategory']['name'], array(
                                'class' => 'category_name',
                            ));
                            $content .= $this->Html->tag('td', $truck['Driver']['driver_name'], array(
                                'class' => 'driver_name',
                            ));
                            $content .= $this->Html->tag('td', $truck['CustomerNoType']['code'], array(
                                'class' => 'customer_code',
                            ));
                            $content .= $this->Html->tag('td', $truck['Truck']['capacity'], array(
                                'class' => 'capacity',
                                'style' => 'text-align: center;'
                            ));
                            $content .= $this->Html->tag('td', $truck['Truck']['tahun'], array(
                                'class' => 'tahun',
                                'style' => 'text-align: center;'
                            ));
                            $content .= $this->Html->tag('td', $truck['TruckFacility']['name'], array(
                                'class' => 'hide truck_facility',
                            ));
                            $content .= $this->Html->tag('td', $truck['Truck']['no_rangka'], array(
                                'class' => 'hide no_rangka',
                            ));

                            echo $this->Html->tag('tr', $content);
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
    </div>
    <?php 
            }

            echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $User['full_name'])), array(
                'style' => 'font-size: 14px;font-style: italic;margin-top: 10px;'
            ));
            
            if( $data_action != 'excel' ) {
                echo $this->Html->tag('div', $this->element('pagination'), array(
                    'class' => 'pagination-report'
                ));
    ?>
</section>
<?php
        }
    } else{
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
                $content .= $this->Html->tag('td', !empty($truck['Driver']['driver_name'])?$truck['Driver']['driver_name']:'-');
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
$print_label = $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $User['full_name'])), array(
    'style' => 'font-size: 24px;font-style: italic;margin-top: 10px;'
));
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