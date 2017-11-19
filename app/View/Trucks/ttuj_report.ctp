<?php 
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $header_module_title = !empty($header_module_title)?$header_module_title:false;
        $dataColumns = array(
            'branch' => array(
                'name' => __('Cabang'),
                'style' => 'text-align: center',
                'data-options' => 'field:\'branch\',width:80',
            ),
            'date' => array(
                'name' => __('Tanggal'),
                'style' => 'text-align: center',
                'data-options' => 'field:\'date\',width:100',
            ),
            'no_ttuj' => array(
                'name' => __('No TTUJ'),
                'style' => 'text-align: center',
                'data-options' => 'field:\'no_ttuj\',width:100',
            ),
            'customer' => array(
                'name' => __('Customer'),
                'style' => 'text-align: center',
                'data-options' => 'field:\'customer\',width:100',
            ),
            'nopol' => array(
                'name' => __('Nopol'),
                'style' => 'text-align: center',
                'data-options' => 'field:\'nopol\',width:100',
            ),
            'capacity' => array(
                'name' => __('Kapasitas'),
                'style' => 'text-align: center',
                'align' => 'center',
                'data-options' => 'field:\'capacity\',width:80',
            ),
            'driver' => array(
                'name' => __('Supir'),
                'style' => 'text-align: center',
                'data-options' => 'field:\'driver\',width:100',
                'fix_column' => true,
            ),
            'from_city' => array(
                'name' => __('Dari'),
                'style' => 'text-align: center',
                'data-options' => 'field:\'from_city\',width:100',
            ),
            'to_city' => array(
                'name' => __('Tujuan'),
                'style' => 'text-align: center',
                'data-options' => 'field:\'to_city\',width:100',
            ),
            'note' => array(
                'name' => __('Keterangan Muat'),
                'style' => 'text-align: center',
                'data-options' => 'field:\'note\',width:120',
            ),
            'unit' => array(
                'name' => __('Unit'),
                'style' => 'text-align: center',
                'align' => 'center',
                'data-options' => 'field:\'unit\',width:80',
            ),
            'id_uang_jalan' => array(
                'name' => __('Id Uang Jalan'),
                'style' => 'text-align: center',
                'align' => 'center',
                'data-options' => 'field:\'id_uang_jalan\',width:100',
            ),
            'uang_jalan' => array(
                'name' => __('Uang Jalan 1'),
                'align' => 'right',
                'style' => 'text-align: center',
                'data-options' => 'field:\'uang_jalan\',width:100',
            ),
            'uang_jalan_2' => array(
                'name' => __('Uang Jalan 2'),
                'style' => 'text-align: center',
                'align' => 'right',
                'data-options' => 'field:\'uang_jalan_2\',width:120',
            ),
            'uang_jalan_extra' => array(
                'name' => __('Uang Jalan Extra'),
                'style' => 'text-align: center',
                'align' => 'right',
                'data-options' => 'field:\'uang_jalan_extra\',width:120',
            ),
            'commission' => array(
                'name' => __('Komisi'),
                'style' => 'text-align: center',
                'align' => 'right',
                'data-options' => 'field:\'commission\',width:100',
            ),
            'commission_extra' => array(
                'name' => __('Komisi Extra'),
                'style' => 'text-align: center',
                'align' => 'right',
                'data-options' => 'field:\'commission_extra\',width:120',
            ),
            'uang_kuli_muat' => array(
                'name' => __('Uang Kuli Muat'),
                'style' => 'text-align: center',
                'align' => 'right',
                'data-options' => 'field:\'uang_kuli_muat\',width:120',
            ),
            'uang_kuli_bongkar' => array(
                'name' => __('Uang Kuli Bongkar'),
                'style' => 'text-align: center',
                'align' => 'right',
                'data-options' => 'field:\'uang_kuli_bongkar\',width:130',
            ),
            'uang_penyebrangan' => array(
                'name' => __('Uang Penyebrangan'),
                'style' => 'text-align: center',
                'align' => 'right',
                'data-options' => 'field:\'uang_penyebrangan\',width:150',
            ),
            'uang_kawal' => array(
                'name' => __('Uang Kawal'),
                'style' => 'text-align: center',
                'align' => 'right',
                'data-options' => 'field:\'uang_kawal\',width:100',
            ),
            'uang_keamanan' => array(
                'name' => __('Uang Keamanan'),
                'style' => 'text-align: center',
                'align' => 'right',
                'data-options' => 'field:\'uang_keamanan\',width:120',
            ),
            'total' => array(
                'name' => __('Total'),
                'style' => 'text-align: center',
                'align' => 'right',
                'data-options' => 'field:\'total\',width:100',
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
        <table class="table table-bordered sorting easyui-datagrid" border="<?php echo $border; ?>">
            <thead frozen="true">
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