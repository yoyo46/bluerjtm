<?php 
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $header_module_title = !empty($header_module_title)?$header_module_title:false;
        $dataColumns = array(
            'date' => array(
                'name' => __('Tanggal'),
                'field_model' => 'Ttuj.ttuj_date',
                'style' => 'text-align: center',
                'display' => true,
            ),
            'no_ttuj' => array(
                'name' => __('No TTUJ'),
                'field_model' => 'Ttuj.no_ttuj',
                'style' => 'text-align: center',
                'display' => true,
            ),
            'nopol' => array(
                'name' => __('Nopol'),
                'field_model' => 'Ttuj.nopol',
                'style' => 'text-align: center',
                'display' => true,
            ),
            'driver' => array(
                'name' => __('Supir'),
                'field_model' => false,
                'style' => 'text-align: center',
                'display' => true,
            ),
            'from_city' => array(
                'name' => __('Dari'),
                'field_model' => 'Ttuj.from_city_name',
                'style' => 'text-align: center',
                'display' => true,
            ),
            'to_city' => array(
                'name' => __('Tujuan'),
                'field_model' => 'Ttuj.to_city_name',
                'style' => 'text-align: center',
                'display' => true,
            ),
            'note' => array(
                'name' => __('Keterangan Muat'),
                'field_model' => false,
                'style' => 'text-align: center',
                'display' => true,
            ),
            'unit' => array(
                'name' => __('Unit'),
                'field_model' => false,
                'style' => 'text-align: center',
                'display' => true,
            ),
            'uang_jalan' => array(
                'name' => __('Uang Jalan 1'),
                'field_model' => false,
                'style' => 'text-align: center',
                'display' => true,
            ),
            'uang_jalan_2' => array(
                'name' => __('Uang Jalan 2'),
                'field_model' => false,
                'style' => 'text-align: center',
                'display' => true,
            ),
            'uang_jalan_extra' => array(
                'name' => __('Uang Jalan Extra'),
                'field_model' => false,
                'style' => 'text-align: center',
                'display' => true,
            ),
            'commission' => array(
                'name' => __('Komisi'),
                'field_model' => false,
                'style' => 'text-align: center',
                'display' => true,
            ),
            'commission_extra' => array(
                'name' => __('Komisi Extra'),
                'field_model' => false,
                'style' => 'text-align: center',
                'display' => true,
            ),
            'uang_kuli_muat' => array(
                'name' => __('Uang Kuli Muat'),
                'field_model' => false,
                'style' => 'text-align: center',
                'display' => true,
            ),
            'uang_kuli_bongkar' => array(
                'name' => __('Uang Kuli Bongkar'),
                'field_model' => false,
                'style' => 'text-align: center',
                'display' => true,
            ),
            'uang_penyebrangan' => array(
                'name' => __('Uang Penyebrangan'),
                'field_model' => false,
                'style' => 'text-align: center',
                'display' => true,
            ),
            'uang_kawal' => array(
                'name' => __('Uang Kawal'),
                'field_model' => false,
                'style' => 'text-align: center',
                'display' => true,
            ),
            'uang_keamanan' => array(
                'name' => __('Uang Keamanan'),
                'field_model' => false,
                'style' => 'text-align: center',
                'display' => true,
            ),
            'total' => array(
                'name' => __('Total'),
                'field_model' => false,
                'style' => 'text-align: center',
                'display' => true,
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table', $data_action );
        $allow_branch = !empty($allow_branch)?$allow_branch:array();

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
                echo $this->element('blocks/trucks/search_ttuj_report');
        }
?>
<section class="content invoice">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
    </h2>
    <dl class="dl-horizontal">
        <dt style="text-align: left;"><?php echo __('Cabang:')?></dt>
        <dd><?php echo implode(', ', $allow_branch);?></dd>
        <dt style="text-align: left;"><?php echo __('Periode:')?></dt>
        <dd><?php echo $periode;?></dd>
    </dl>
    <?php 
            if( $data_action != 'excel' ) {
                echo $this->Common->_getPrint(array(
                    '_attr' => array(
                        'class' => 'ajaxLink',
                        'data-request' => '#form-report',
                    ),
                ));
            }
    ?>
    <div class="table-responsive center-table">
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
                        echo $this->element('blocks/trucks/tables/ttuj_report');
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
        
        $each_loop_message = $this->element('blocks/trucks/tables/ttuj_report');

$print_label = $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
    'style' => 'font-size: 24px;font-style: italic;margin-top: 10px;'
));
$listCabang = implode(', ', $allow_branch);
$tbl = <<<EOD

      <div class="clearfix container_16" id="content">
        <h2 class="grid_8" style="text-align: center;">$header_module_title</h2>
        <h2 style="text-align: center;">$sub_module_title</h2>
        <p style="text-align: left;font-size: 24px;">
        Cabang: $listCabang<br>
        Periode: $periode</p>
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