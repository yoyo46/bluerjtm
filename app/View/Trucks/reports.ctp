<?php 
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $text = __('Semua Truk');
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
            'company' => array(
                'name' => __('Pemilik'),
                'field_model' => 'Truck.company',
                'sorting' => false,
                'display' => true,
            ),
            'category_name' => array(
                'name' => __('Jenis'),
                'field_model' => 'TruckCategory.name',
                'display' => true,
            ),
            'no_id' => array(
                'name' => __('ID Supir'),
                'field_model' => 'Driver.no_id',
                'sorting' => false,
                'display' => true,
            ),
            'driver_name' => array(
                'name' => __('Supir'),
                'field_model' => 'Driver.driver_name',
                'sorting' => false,
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
            'no_machine' => array(
                'name' => __('No Mesin'),
                'field_model' => 'Truck.no_machine',
                'display' => false,
            ),
            'bpkb' => array(
                'name' => __('No BPKB'),
                'field_model' => 'Truck.bpkb',
                'display' => false,
            ),
            'no_stnk' => array(
                'name' => __('No STNK'),
                'field_model' => 'Truck.no_stnk',
                'display' => false,
            ),
            'is_gps' => array(
                'name' => __('GPS?'),
                'field_model' => 'Truck.is_gps',
                'display' => false,
            ),
            'emergency_name' => array(
                'name' => __('Nama Panggilan'),
                'field_model' => 'Truck.emergency_name',
                'display' => false,
            ),
            'emergency_call' => array(
                'name' => __('No Tlp'),
                'field_model' => 'Truck.emergency_call',
                'display' => false,
            ),
            'branch' => array(
                'name' => __('Cabang'),
                'field_model' => 'Branch.name',
                'sorting' => false,
                'display' => true,
            ),
            'nopolis' => array(
                'name' => __('No. Polis'),
                'field_model' => 'Insurance.nodoc',
                'sorting' => false,
                'display' => false,
            ),
            'insurance_name' => array(
                'name' => __('Nama Asuransi'),
                'field_model' => 'Insurance.name',
                'sorting' => false,
                'display' => false,
            ),
            'insurance_to_name' => array(
                'name' => __('Nama Tertanggung'),
                'field_model' => 'Insurance.to_name',
                'sorting' => false,
                'display' => false,
            ),
            'insurance_date' => array(
                'name' => __('Periode Pertanggungan'),
                'field_model' => 'Insurance.date',
                'sorting' => false,
                'display' => false,
            ),
            'insurance_status' => array(
                'name' => __('Status Asuransi'),
                'field_model' => 'Insurance.status',
                'sorting' => false,
                'display' => false,
            ),
        );
        $showHideColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'show-hide' );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table', $data_action );

        if(!empty($from_date) || !empty($to_date)){
            $text = 'periode ';
            if(!empty($from_date)){
                $text .= date('d M Y', strtotime($from_date));
            }

            if(!empty($to_date)){
                if(!empty($from_date)){
                    $text .= ' - ';
                }
                $text .= date('d M Y', strtotime($to_date));
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
?>
<style>
    .string{ mso-number-format:\@; }
</style>
<section class="content invoice">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo __('Laporan Truk');?>
        <small class="pull-right">
            <?php
                    echo $text;
            ?>
        </small>
    </h2>
    <?php 
            echo $this->Common->_getPrint(array(
                '_attr' => array(
                    'class' => 'ajaxLink',
                    'data-request' => '#form-report',
                ),
            ), $showHideColumn);
    ?>
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
                            $id = $this->Common->filterEmptyField($truck, 'Truck', 'id');

                            $company = $this->Common->filterEmptyField($truck, 'Company', 'name');
                            $branch = $this->Common->filterEmptyField($truck, 'Branch', 'name');

                            $driver_name = $this->Common->filterEmptyField($truck, 'Driver', 'driver_name');
                            $no_id = $this->Common->filterEmptyField($truck, 'Driver', 'no_id');

                            $brand_name = $this->Common->filterEmptyField($truck, 'TruckBrand', 'name');
                            $facility_name = $this->Common->filterEmptyField($truck, 'TruckFacility', 'name');


                            $nopolis = Common::hashEmptyField($truck, 'Insurance.nodoc');
                            $insurance_name = Common::hashEmptyField($truck, 'Insurance.name');
                            $insurance_to_name = Common::hashEmptyField($truck, 'Insurance.to_name');
                            $start_date = Common::hashEmptyField($truck, 'Insurance.start_date');
                            $end_date = Common::hashEmptyField($truck, 'Insurance.end_date');
                            $insurance_status = Common::hashEmptyField($truck, 'Insurance.status');

                            $insurance_date = Common::getCombineDate($start_date, $end_date);

                            if( !empty($nopolis) ) {
                                $statusArr = Common::_callInsuranceStatus($truck);
                                $status = Common::hashEmptyField($statusArr, 'status');
                                $status_color = Common::hashEmptyField($statusArr, 'color');

                                if( $data_action != 'excel' ) {
                                    $insurance_status = $this->Html->tag('span', $status, array(
                                        'class' => 'label label-'.$status_color,
                                    ));
                                } else {
                                    $insurance_status = $status;
                                }
                            } else {
                                $insurance_status = false;
                            }
                            
                            $content = $this->Common->_getDataColumn(str_pad($id, 4, '0', STR_PAD_LEFT), 'Truck', 'id', array(
                                'class' => 'hide nomor_id',
                                'style' => 'text-align: left;',
                            ));
                            $content .= $this->Common->_getDataColumn($truck['Truck']['nopol'], 'Truck', 'nopol', array(
                                'style' => 'text-align: left;',
                                'class' => 'nopol',
                            ));
                            $content .= $this->Common->_getDataColumn($brand_name, 'TruckBrand', 'name', array(
                                'class' => 'brand_name',
                            ));
                            $content .= $this->Common->_getDataColumn($company, 'Truck', 'company', array(
                                'class' => 'company',
                            ));
                            $content .= $this->Common->_getDataColumn(Common::hashEmptyField($truck, 'TruckCategory.name'), 'TruckCategory', 'name', array(
                                'class' => 'category_name',
                            ));
                            $content .= $this->Common->_getDataColumn($no_id, 'Driver', 'no_id', array(
                                'class' => 'no_id',
                            ));
                            $content .= $this->Common->_getDataColumn($driver_name, 'Driver', 'driver_name', array(
                                'class' => 'driver_name',
                            ));
                            $content .= $this->Common->_getDataColumn(Common::hashEmptyField($truck, 'CustomerNoType.code'), 'CustomerNoType', 'code', array(
                                'class' => 'customer_code',
                            ));
                            $content .= $this->Common->_getDataColumn($truck['Truck']['capacity'], 'Truck', 'capacity', array(
                                'class' => 'capacity',
                                'style' => 'text-align: center;'
                            ));
                            $content .= $this->Common->_getDataColumn($truck['Truck']['tahun'], 'Truck', 'tahun', array(
                                'class' => 'tahun',
                                'style' => 'text-align: center;'
                            ));
                            $content .= $this->Common->_getDataColumn($facility_name, 'TruckFacility', 'name', array(
                                'class' => 'hide truck_facility',
                            ));
                            $content .= $this->Common->_getDataColumn($truck['Truck']['no_rangka'], 'Truck', 'no_rangka', array(
                                'class' => 'hide no_rangka',
                            ));
                            $content .= $this->Common->_getDataColumn($truck['Truck']['no_machine'], 'Truck', 'no_machine', array(
                                'class' => 'hide no_machine',
                            ));
                            $content .= $this->Common->_getDataColumn($truck['Truck']['bpkb'], 'Truck', 'bpkb', array(
                                'class' => 'hide bpkb',
                            ));
                            $content .= $this->Common->_getDataColumn($truck['Truck']['no_stnk'], 'Truck', 'no_stnk', array(
                                'class' => 'hide no_stnk',
                            ));
                            $content .= $this->Common->_getDataColumn($truck['Truck']['is_gps'], 'Truck', 'is_gps', array(
                                'class' => 'hide is_gps',
                                'options' => array(
                                    0 => __('Tidak'),
                                    1 => __('Ya'),
                                ),
                            ));
                            $content .= $this->Common->_getDataColumn($truck['Truck']['emergency_name'], 'Truck', 'emergency_name', array(
                                'class' => 'hide emergency_name',
                            ));
                            $content .= $this->Common->_getDataColumn($truck['Truck']['emergency_call'], 'Truck', 'emergency_call', array(
                                'class' => 'hide emergency_call',
                            ));
                            $content .= $this->Common->_getDataColumn($branch, 'Branch', 'name', array(
                                'style' => 'text-align: left;',
                                'class' => 'branch',
                            ));
                            $content .= $this->Common->_getDataColumn($nopolis, 'Insurance', 'nodoc', array(
                                'style' => 'text-align: left;',
                                'class' => 'hide nopolis string',
                            ));
                            $content .= $this->Common->_getDataColumn($insurance_name, 'Insurance', 'name', array(
                                'style' => 'text-align: left;',
                                'class' => 'hide insurance_name',
                            ));
                            $content .= $this->Common->_getDataColumn($insurance_to_name, 'Insurance', 'to_name', array(
                                'style' => 'text-align: left;',
                                'class' => 'hide insurance_to_name',
                            ));
                            $content .= $this->Common->_getDataColumn($insurance_date, 'Insurance', 'date', array(
                                'style' => 'text-align: center;',
                                'class' => 'hide insurance_date string',
                            ));
                            $content .= $this->Common->_getDataColumn($insurance_status, 'Insurance', 'status', array(
                                'style' => 'text-align: center;',
                                'class' => 'hide insurance_status',
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

            echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
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

        if(!empty($trucks)){
            foreach ($trucks as $truck):
                $id = $this->Common->filterEmptyField($truck, 'Truck', 'id');
                $branch = !empty($truck['Branch']['name'])?$truck['Branch']['name']:false;
                $driver_name = !empty($truck['Driver']['driver_name'])?$truck['Driver']['driver_name']:false;
                $customer_code = Common::hashEmptyField($truck, 'CustomerNoType.code');
                $truck_facility = !empty($truck['TruckFacility']['name'])?$truck['TruckFacility']['name']:false;
                $brand_name = !empty($truck['TruckBrand']['name'])?$truck['TruckBrand']['name']:false;
                $company = $this->Common->filterEmptyField($truck, 'Company', 'name');

                $content = $this->Common->_getDataColumn(str_pad($id, 4, '0', STR_PAD_LEFT), 'Truck', 'id', array(
                    'style' => 'text-align: left;',
                ));
                $content .= $this->Common->_getDataColumn($truck['Truck']['nopol'], 'Truck', 'nopol', array(
                    'style' => 'text-align: left;',
                ));
                $content .= $this->Common->_getDataColumn($brand_name, 'TruckBrand', 'name');
                $content .= $this->Common->_getDataColumn($company, 'Truck', 'company');
                $content .= $this->Common->_getDataColumn(Common::hashEmptyField($truck, 'TruckCategory.name'), 'TruckCategory', 'name');
                $content .= $this->Common->_getDataColumn($driver_name, 'Driver', 'driver_name');
                $content .= $this->Common->_getDataColumn($customer_code, 'CustomerNoType', 'code');
                $content .= $this->Common->_getDataColumn($truck['Truck']['capacity'], 'Truck', 'capacity', array(
                    'style' => 'text-align: center;'
                ));
                $content .= $this->Common->_getDataColumn($truck['Truck']['tahun'], 'Truck', 'tahun', array(
                    'style' => 'text-align: center;'
                ));
                $content .= $this->Common->_getDataColumn($truck_facility, 'TruckFacility', 'name');
                $content .= $this->Common->_getDataColumn($truck['Truck']['no_rangka'], 'Truck', 'no_rangka');
                $content .= $this->Common->_getDataColumn($truck['Truck']['no_machine'], 'Truck', 'no_machine');
                $content .= $this->Common->_getDataColumn($truck['Truck']['bpkb'], 'Truck', 'bpkb');
                $content .= $this->Common->_getDataColumn($truck['Truck']['no_stnk'], 'Truck', 'no_stnk');
                $content .= $this->Common->_getDataColumn($truck['Truck']['is_gps'], 'Truck', 'is_gps', array(
                    'options' => array(
                        0 => __('Tidak'),
                        1 => __('Ya'),
                    ),
                ));
                $content .= $this->Common->_getDataColumn($truck['Truck']['emergency_name'], 'Truck', 'emergency_name');
                $content .= $this->Common->_getDataColumn($truck['Truck']['emergency_call'], 'Truck', 'emergency_call');
                $content .= $this->Common->_getDataColumn($branch, 'Branch', 'name', array(
                    'style' => 'text-align: left;',
                ));

                $each_loop_message .= $this->Html->tag('tr', $content);
            endforeach;
        }else{
            $each_loop_message .= '<tr>
                <td colspan="7">data tidak tersedia</td>
            </tr>';
        }   

$total_trucks = count($trucks);
$print_label = $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
    'style' => 'font-size: 24px;font-style: italic;margin-top: 10px;'
));
$tbl = <<<EOD

      <div class="clearfix container_16" id="content">
        <h2 class="grid_8" style="text-align: center;">Laporan Truk</h2>
        <h4>Total Truk : $total_trucks</h4>
        <table cellpadding="2" cellspacing="2" nobr="true" style="$table">
            <tbody>
                <tr style="$table_tr_head">
                    $fieldColumn
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
        $filename = $sub_module_title.'.pdf';
        $tcpdf->Output($path.'/'.$filename, 'F'); 

        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        readfile($path.'/'.$filename);
    }
?>