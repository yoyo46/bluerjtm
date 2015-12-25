<?php 
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $dataColumns = array(
            'no_invoice' => array(
                'name' => __('No. Invoice'),
                'field_model' => 'Invoice.no_invoice',
                'style' => 'text-align: center',
                'display' => true,
            ),
            'tarif_type' => array(
                'name' => __('Jenis Tarif'),
                'field_model' => 'Invoice.tarif_type',
                'style' => 'text-align: center',
                'display' => true,
            ),
            'invoice_date' => array(
                'name' => __('Tgl Invoice'),
                'field_model' => 'Invoice.invoice_date',
                'style' => 'text-align: center',
                'display' => true,
            ),
            'period_from' => array(
                'name' => __('Periode'),
                'field_model' => 'Invoice.period_from',
                'style' => 'text-align: center',
                'display' => true,
            ),
            'expired_date' => array(
                'name' => __('Tatuh Tempo'),
                'style' => 'text-align: center',
                'display' => true,
            ),
            'total' => array(
                'name' => __('Total'),
                'field_model' => 'Invoice.total',
                'style' => 'text-align: center',
                'display' => true,
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table', $data_action );

        if( empty($data_action) || ( !empty($data_action) && $data_action == 'excel' ) ){
            $tdStyle = '';
            $tableStyle = '';
            $border = 0;

            if( $data_action == 'excel' ) {
                header('Content-type: application/ms-excel');
                header('Content-Disposition: attachment; filename='.$sub_module_title.'.xls');
                $border = 1;
                $tdStyle = 'text-align: center;';
                $tableStyle = 'width: 100%;';
            } else {
                $this->Html->addCrumb($sub_module_title);
                echo $this->element('blocks/revenues/search_invoice_report_detail');
        }
?>
<section class="content invoice">
    <h2 class="page-header" style="<?php echo $tdStyle; ?>">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
        <?php
                echo $this->Html->tag('div', $periode, array(
                    'class' => 'pull-right',
                ));
        ?>
        <div class="clear"></div>
    </h2>
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
        <table class="table table-bordered sorting" border="<?php echo $border; ?>" style="<?php echo $tableStyle; ?>">
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
                        echo $this->element('blocks/revenues/tables/invoice_report_detail');
                ?>
            </tbody>
        </table>
        <?php 
                if( $data_action != 'excel' ) {
                    if(empty($values)){
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
        
        $each_loop_message = $this->element('blocks/revenues/tables/invoice_report_detail');

$print_label = $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
    'style' => 'font-size: 24px;font-style: italic;margin-top: 10px;'
));
$tbl = <<<EOD

      <div class="clearfix container_16" id="content">
        <h2 style="text-align: center;">
            $sub_module_title
            <br>
            $periode
        </h2>
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