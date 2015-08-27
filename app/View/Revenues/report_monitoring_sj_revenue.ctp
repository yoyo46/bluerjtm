    <?php 
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        if( empty($data_action) || ( !empty($data_action) && $data_action == 'excel' ) ){
            $this->Html->addCrumb($sub_module_title);
            $addStyle = '';
            $tdStyle = '';
            $border = 0;
            $addClass = 'easyui-datagrid';
            $headerRowspan = false;

            if( $data_action == 'excel' ) {
                header('Content-type: application/ms-excel');
                header('Content-Disposition: attachment; filename='.$sub_module_title.'.xls');
                $border = 1;
                $tdStyle = 'text-align: center;';
                $addClass = '';
                $headerRowspan = 2;
            }

            if( !empty($ttujs) ) {
                $addStyle = 'width: 100%;height: 550px;';
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
            echo $this->Form->create('Ttuj', array(
                'url'=> $this->Html->url( array(
                    'controller' => 'revenues',
                    'action' => 'search',
                    'report_monitoring_sj_revenue'
                )), 
                'role' => 'form',
                'inputDefaults' => array('div' => false),
            ));
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('date',array(
                                'label'=> __('Tgl Laporan'),
                                'class'=>'form-control date-range',
                                'required' => false,
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            echo $this->Form->input('status',array(
                                'label'=> __('Status Surat Jalan'),
                                'class'=>'form-control',
                                'required' => false,
                                'empty' => __('Pilih Status'),
                                'options' => array(
                                    'pending' => __('Surat jalan Belum Diterima'),
                                    'hal_receipt' => __('Surat jalan Sebagian Diterima'),
                                    'receipt' => __('Surat jalan Diterima'),
                                    'receipt_unpaid' => __('Surat jalan diterima belum ditagih'),
                                ),
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
                                'action' => 'report_monitoring_sj_revenue', 
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
                            echo $this->Form->input('customer', array(
                                'label'=> __('Customer'), 
                                'class'=>'form-control',
                                'empty' => __('Pilih Customer'),
                                'options' => $customerList,
                            ));
                    ?>
                </div>
                <div class="form-group">
                    <?php 
                            // Custom Otorisasi
                            echo $this->Common->getCheckboxBranch();
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
        <small class="pull-right">
            <?php
                    echo $period_text;
            ?>
        </small>
    </h2>
    <div class="row no-print print-action">
        <div class="col-xs-12 action">
            <?php
                    $urlDefault = $this->passedArgs;
                    $urlDefault['controller'] = 'revenues';
                    $urlDefault['action'] = 'report_monitoring_sj_revenue';

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
        <table id="tt" class="table table-bordered <?php echo $addClass; ?>" style="<?php echo $addStyle; ?>" singleSelect="true" border="<?php echo $border; ?>">
            <thead frozen="true">
                <tr>
                    <?php 
                            echo $this->Html->tag('th', $this->Common->getSorting('Ttuj.no_ttuj', __('NO TTUJ')), array(
                                'style' => 'text-align: center;width: 150px;',
                                'data-options' => 'field:\'no_ttuj\',width:120',
                                'rowspan' => $headerRowspan,
                                'mainalign' => 'center',
                                'align' => 'left',
                            ));
                            echo $this->Html->tag('th', $this->Common->getSorting('Ttuj.ttuj_date', __('TANGGAL')), array(
                                'align' => 'center',
                                'style' => 'text-align: center;width: 100px;',
                                'data-options' => 'field:\'ttuj_date\',width:100',
                                'rowspan' => $headerRowspan,
                            ));
                            echo $this->Html->tag('th', $this->Common->getSorting('Ttuj.to_city_name', __('TUJUAN')), array(
                                'style' => 'text-align: center;width: 120px;',
                                'data-options' => 'field:\'to_city_name\',width:120',
                                'rowspan' => $headerRowspan,
                                'mainalign' => 'center',
                                'align' => 'left',
                            ));
                            echo $this->Html->tag('th', $this->Common->getSorting('Ttuj.nopol', __('NOPOL')), array(
                                'style' => 'text-align: center;width: 120px;',
                                'data-options' => 'field:\'nopol\',width:100',
                                'rowspan' => $headerRowspan,
                                'mainalign' => 'center',
                                'align' => 'left',
                            ));
                            echo $this->Html->tag('th', __('UNIT'), array(
                                'align' => 'center',
                                'style' => 'text-align: center;width: 80px;',
                                'data-options' => 'field:\'unit\',width:80',
                                'rowspan' => $headerRowspan,
                            ));
                    ?>
            <?php 
                    if( $data_action != 'excel' ) {
            ?>
                </tr>
            </thead>
            <thead>
                <tr>
            <?php 
                    }
            ?>
                    <?php 
                            echo $this->Html->tag('th', __('SURAT JALAN (unit)'), array(
                                'style' => 'text-align: center;',
                                'colspan' => 2,
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', __('SELISIH'), array(
                                'style' => 'text-align: center;',
                                'colspan' => 3,
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', __('TGL SJ KEMBALI'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'date_receipt\',width:100',
                                'rowspan' => 2,
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', __('TGL INVOICE'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'date_invoice\',width:100',
                                'rowspan' => 2,
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', __('LEAD TIME (DAY)'), array(
                                'style' => 'text-align: center;',
                                'colspan' => 3,
                                'align' => 'center',
                            ));
                    ?>
                </tr>
                <tr>
                    <?php 
                            echo $this->Html->tag('th', __('KEMBALI'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'sj_receipt\',width:80',
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', __('TERTAGIH'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'sj_invoice\',width:80',
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', __('SJ BELUM KEMBALI'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'sj_not_receipt\',width:120',
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', __('SJ KEMBALI BELUM TERTAGIH'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'sj_not_receipt_not_invoice\',width:120',
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', __('SJ BELUM TERTAGIH'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'sj_not_invoice\',width:120',
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', __('SJ KEMBALI'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'sj_lead_not_invoice\',width:100',
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', __('SJ PROSES BILLING'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'sj_lead_process_billing\',width:100',
                                'align' => 'center',
                            ));
                            echo $this->Html->tag('th', __('SJ TERTAGIH'), array(
                                'style' => 'text-align: center;',
                                'data-options' => 'field:\'sj_lead_invoice\',width:100',
                                'align' => 'center',
                            ));
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                        if(!empty($ttujs)){
                            $totalUnit = 0;
                            $totalUnitSj = 0;
                            $totalUnitInvoiced = 0;
                            $totalUnitSjNotRecipt = 0;
                            $totalUnitJSUnInvoiced = 0;
                            $totalUnitUnInvoiced = 0;
                            $avgLeadSj = 0;
                            $avgLeadSjBilling = 0;
                            $avgLeadSjInvoiced = 0;
                            $cntAvgLeadSj = 0;
                            $cntAvgLeadSjBilling = 0;
                            $cntAvgLeadSjInvoiced = 0;

                            foreach ($ttujs as $key => $ttuj) {
                                $city_name = !empty($ttuj['city_name'])?$ttuj['city_name']:'-';
                                $unit = !empty($ttuj['Qty'])?$ttuj['Qty']:0;
                                $unitSj = !empty($ttuj['QtySJ'])?$ttuj['QtySJ']:0;
                                $unitSjNotRecipt = $unit - $unitSj;
                                $unitInvoiced = !empty($ttuj['unitInvoiced'])?$ttuj['unitInvoiced']:0;
                                $unitJSUnInvoiced = $unitSj - $unitInvoiced;
                                $unitUnInvoiced = $unit - $unitInvoiced;
                                $tglInvoiced = !empty($ttuj['Invoice']['invoice_date'])?$this->Common->customDate($ttuj['Invoice']['invoice_date'], 'd/m/Y'):'-';

                                if( !empty($ttuj['SuratJalan']['tgl_surat_jalan']) ) {
                                    $dtSj = $ttuj['SuratJalan']['tgl_surat_jalan'];
                                } else {
                                    $dtSj = date('Y-m-d');
                                }

                                if( !empty($ttuj['Invoice']['invoice_date']) ) {
                                    $dtInvoice = $ttuj['Invoice']['invoice_date'];
                                } else {
                                    $dtInvoice = date('Y-m-d');
                                }

                                $str = strtotime($dtSj) - (strtotime($ttuj['Ttuj']['ttuj_date']));

                                if( !empty($ttuj['SuratJalan']['tgl_surat_jalan']) ) {
                                    $tglSJ = $this->Common->customDate($ttuj['SuratJalan']['tgl_surat_jalan'], 'd/m/Y');
                                    $leadSj = floor($str/3600/24);

                                    if( $leadSj < 0 ) {
                                        $leadSj -= 1;
                                    } else {
                                        $leadSj += 1;
                                    }

                                    $avgLeadSj += $leadSj;
                                    $cntAvgLeadSj++;
                                } else {
                                    $leadSj = 0;
                                    $tglSJ = '-';
                                }

                                if( !empty($ttuj['SuratJalan']['tgl_surat_jalan']) && !empty($ttuj['Invoice']['invoice_date']) ) {
                                    $str = strtotime($dtInvoice) - (strtotime($dtSj));
                                    $leadSjBilling = floor($str/3600/24);

                                    if( $leadSjBilling < 0 ) {
                                        $leadSjBilling -= 1;
                                    } else {
                                        $leadSjBilling += 1;
                                    }

                                    $avgLeadSjBilling += $leadSjBilling;
                                    $cntAvgLeadSjBilling++;
                                } else {
                                    $leadSjBilling = 0;
                                }

                                $str = strtotime($dtInvoice) - (strtotime($ttuj['Ttuj']['ttuj_date']));

                                if( !empty($ttuj['Invoice']['invoice_date']) ) {
                                    $leadSjInvoiced = floor($str/3600/24);

                                    if( $leadSjInvoiced < 0 ) {
                                        $leadSjInvoiced -= 1;
                                    } else {
                                        $leadSjInvoiced += 1;
                                    }

                                    $avgLeadSjInvoiced += $leadSjInvoiced;
                                    $cntAvgLeadSjInvoiced++;
                                } else {
                                    $leadSjInvoiced = 0;
                                }

                                $totalUnit += $unit;
                                $totalUnitSj += $unitSj;
                                $totalUnitInvoiced += $unitInvoiced;
                                $totalUnitSjNotRecipt += $unitSjNotRecipt;
                                $totalUnitJSUnInvoiced += $unitJSUnInvoiced;
                                $totalUnitUnInvoiced += $unitUnInvoiced;
                ?>
                <tr>
                    <?php 
                            echo $this->Html->tag('td', $this->Html->link($ttuj['Ttuj']['no_ttuj'], array(
                                'controller' => 'revenues',
                                'action' => 'ttuj_edit',
                                $ttuj['Ttuj']['id']
                            ), array(
                                'target' => '_blank',
                            )));
                            echo $this->Html->tag('td', $this->Common->customDate($ttuj['Ttuj']['ttuj_date'], 'd/m/Y'), array(
                                'style' => 'text-align: center;'
                            ));
                            echo $this->Html->tag('td', $city_name);
                            echo $this->Html->tag('td', $ttuj['Ttuj']['nopol']);
                            echo $this->Html->tag('td', $unit, array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', $unitSj, array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', $unitInvoiced, array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', $unitSjNotRecipt, array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', $unitJSUnInvoiced, array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', $unitUnInvoiced, array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', $tglSJ, array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', $tglInvoiced, array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', $leadSj, array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', $leadSjBilling, array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', $leadSjInvoiced, array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                    ?>
                </tr>
                <?php
                            }
                ?>
                <tr>
                    <?php 
                            echo $this->Html->tag('td', '', array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', '', array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', '', array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', $this->Html->tag('strong', __('Total'), array(
                                'style' => 'text-align: right;display: block;'
                            )), array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', $this->Html->tag('strong', $totalUnit), array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', $this->Html->tag('strong', $totalUnitSj), array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', $this->Html->tag('strong', $totalUnitInvoiced), array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', $this->Html->tag('strong', $totalUnitSjNotRecipt), array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', $this->Html->tag('strong', $totalUnitJSUnInvoiced), array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', $this->Html->tag('strong', $totalUnitUnInvoiced), array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', '', array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', $this->Html->tag('strong', __('AVG')), array(
                                'align' => 'right',
                                'style' => 'text-align: right;',
                            ));

                            if( !empty($cntAvgLeadSj) ) {
                                $avgLeadSj = $avgLeadSj/$cntAvgLeadSj;
                            } else {
                                $avgLeadSj = 0;
                            }

                            if( !empty($cntAvgLeadSjBilling) ) {
                                $avgLeadSjBilling = $avgLeadSjBilling/$cntAvgLeadSjBilling;
                            } else {
                                $avgLeadSjBilling = 0;
                            }

                            if( !empty($cntAvgLeadSjInvoiced) ) {
                                $avgLeadSjInvoiced = $avgLeadSjInvoiced/$cntAvgLeadSjInvoiced;
                            } else {
                                $avgLeadSjInvoiced = 0;
                            }

                            echo $this->Html->tag('td', $this->Html->tag('strong', round($avgLeadSj, 2)), array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', $this->Html->tag('strong', round($avgLeadSjBilling, 2)), array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                            echo $this->Html->tag('td', $this->Html->tag('strong', round($avgLeadSjInvoiced, 2)), array(
                                'align' => 'center',
                                'style' => 'text-align: center;',
                            ));
                    ?>
                </tr>
                <?php
                        }
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
    </div><!-- /.box-body -->
    <?php 
            }

            echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 14px;font-style: italic;margin-top: 10px;'
            ));
            
            if( $data_action != 'excel' ) {
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
            $tdStyle = 'text-align: center;';
            $each_loop_message = '';
            $contentHeader = '<tr style="'.$table_tr_head.'">';
            $contentHeader .= $this->Html->tag('th', __('NO TTUJ'), array(
                'style' => 'text-align: center;',
                'rowspan' => 2,
            ));
            $contentHeader .= $this->Html->tag('th', __('TANGGAL'), array(
                'style' => 'text-align: center;',
                'rowspan' => 2,
            ));
            $contentHeader .= $this->Html->tag('th', __('TUJUAN'), array(
                'style' => 'text-align: center;',
                'rowspan' => 2,
            ));
            $contentHeader .= $this->Html->tag('th', __('NOPOL'), array(
                'style' => 'text-align: center;',
                'rowspan' => 2,
            ));
            $contentHeader .= $this->Html->tag('th', __('UNIT'), array(
                'style' => 'text-align: center;',
                'rowspan' => 2,
            ));
            $contentHeader .= $this->Html->tag('th', __('SURAT JALAN (unit)'), array(
                'style' => 'text-align: center;',
                'colspan' => 2,
                'align' => 'center',
            ));
            $contentHeader .= $this->Html->tag('th', __('SELISIH'), array(
                'style' => 'text-align: center;',
                'colspan' => 3,
                'align' => 'center',
            ));
            $contentHeader .= $this->Html->tag('th', __('TGL SJ KEMBALI'), array(
                'style' => 'text-align: center;',
                'rowspan' => 2,
            ));
            $contentHeader .= $this->Html->tag('th', __('TGL INVOICE'), array(
                'style' => 'text-align: center;',
                'rowspan' => 2,
            ));
            $contentHeader .= $this->Html->tag('th', __('LEAD TIME (DAY)'), array(
                'style' => 'text-align: center;',
                'colspan' => 3,
            ));
            $contentHeader .= '</tr><tr style="'.$table_tr_head.'">';

            $contentHeader .= $this->Html->tag('th', __('KEMBALI'), array(
                'style' => 'text-align: center;',
            ));
            $contentHeader .= $this->Html->tag('th', __('TERTAGIH'), array(
                'style' => 'text-align: center;',
            ));
            $contentHeader .= $this->Html->tag('th', __('SJ BELUM KEMBALI'), array(
                'style' => 'text-align: center;',
            ));
            $contentHeader .= $this->Html->tag('th', __('SJ KEMBALI BELUM TERTAGIH'), array(
                'style' => 'text-align: center;',
            ));
            $contentHeader .= $this->Html->tag('th', __('SJ BELUM TERTAGIH'), array(
                'style' => 'text-align: center;',
            ));
            $contentHeader .= $this->Html->tag('th', __('SJ KEMBALI'), array(
                'style' => 'text-align: center;',
            ));
            $contentHeader .= $this->Html->tag('th', __('SJ PROSES BILLING'), array(
                'style' => 'text-align: center;',
            ));
            $contentHeader .= $this->Html->tag('th', __('SJ TERTAGIH'), array(
                'style' => 'text-align: center;',
            ));
            $contentHeader .= '</tr>';

            if(!empty($ttujs)){
                $totalUnit = 0;
                $totalUnitSj = 0;
                $totalUnitInvoiced = 0;
                $totalUnitSjNotRecipt = 0;
                $totalUnitJSUnInvoiced = 0;
                $totalUnitUnInvoiced = 0;
                $avgLeadSj = 0;
                $avgLeadSjBilling = 0;
                $avgLeadSjInvoiced = 0;
                $cntAvgLeadSj = 0;
                $cntAvgLeadSjBilling = 0;
                $cntAvgLeadSjInvoiced = 0;

                foreach ($ttujs as $key => $ttuj) {
                    $city_name = !empty($ttuj['city_name'])?$ttuj['city_name']:'-';
                    $unit = !empty($ttuj['Qty'])?$ttuj['Qty']:0;
                    $unitSj = !empty($ttuj['QtySJ'])?$ttuj['QtySJ']:0;
                    $unitSjNotRecipt = $unit - $unitSj;
                    $unitInvoiced = !empty($ttuj['unitInvoiced'])?$ttuj['unitInvoiced']:0;
                    $unitJSUnInvoiced = $unitSj - $unitInvoiced;
                    $unitUnInvoiced = $unit - $unitInvoiced;
                    $tglInvoiced = !empty($ttuj['Invoice']['invoice_date'])?$this->Common->customDate($ttuj['Invoice']['invoice_date'], 'd/m/Y'):'-';

                    if( !empty($ttuj['SuratJalan']['tgl_surat_jalan']) ) {
                        $dtSj = $ttuj['SuratJalan']['tgl_surat_jalan'];
                    } else {
                        $dtSj = date('Y-m-d');
                    }

                    if( !empty($ttuj['Invoice']['invoice_date']) ) {
                        $dtInvoice = $ttuj['Invoice']['invoice_date'];
                    } else {
                        $dtInvoice = date('Y-m-d');
                    }

                    $str = strtotime($dtSj) - (strtotime($ttuj['Ttuj']['ttuj_date']));

                    if( !empty($ttuj['SuratJalan']['tgl_surat_jalan']) ) {
                        $tglSJ = $this->Common->customDate($ttuj['SuratJalan']['tgl_surat_jalan'], 'd/m/Y');
                        $leadSj = floor($str/3600/24);

                        if( $leadSj < 0 ) {
                            $leadSj -= 1;
                        } else {
                            $leadSj += 1;
                        }

                        $avgLeadSj += $leadSj;
                        $cntAvgLeadSj++;
                    } else {
                        $leadSj = 0;
                        $tglSJ = '-';
                    }

                    if( !empty($ttuj['SuratJalan']['tgl_surat_jalan']) && !empty($ttuj['Invoice']['invoice_date']) ) {
                        $str = strtotime($dtInvoice) - (strtotime($dtSj));
                        $leadSjBilling = floor($str/3600/24);

                        if( $leadSjBilling < 0 ) {
                            $leadSjBilling -= 1;
                        } else {
                            $leadSjBilling += 1;
                        }

                        $avgLeadSjBilling += $leadSjBilling;
                        $cntAvgLeadSjBilling++;
                    } else {
                        $leadSjBilling = 0;
                    }

                    $str = strtotime($dtInvoice) - (strtotime($ttuj['Ttuj']['ttuj_date']));

                    if( !empty($ttuj['Invoice']['invoice_date']) ) {
                        $leadSjInvoiced = floor($str/3600/24);

                        if( $leadSjInvoiced < 0 ) {
                            $leadSjInvoiced -= 1;
                        } else {
                            $leadSjInvoiced += 1;
                        }
                        
                        $avgLeadSjInvoiced += $leadSjInvoiced;
                        $cntAvgLeadSjInvoiced++;
                    } else {
                        $leadSjInvoiced = 0;
                    }

                    $totalUnit += $unit;
                    $totalUnitSj += $unitSj;
                    $totalUnitInvoiced += $unitInvoiced;
                    $totalUnitSjNotRecipt += $unitSjNotRecipt;
                    $totalUnitJSUnInvoiced += $unitJSUnInvoiced;
                    $totalUnitUnInvoiced += $unitUnInvoiced;

                    $each_loop_message .= '<tr>';
                    $each_loop_message .= $this->Html->tag('td', $this->Html->link($ttuj['Ttuj']['no_ttuj'], array(
                        'controller' => 'revenues',
                        'action' => 'ttuj_edit',
                        $ttuj['Ttuj']['id']
                    )), array(
                        'style' => 'text-align: left;',
                    ));
                    $each_loop_message .= $this->Html->tag('td', $this->Common->customDate($ttuj['Ttuj']['ttuj_date'], 'd/m/Y'), array(
                        'style' => 'text-align: left;',
                    ));
                    $each_loop_message .= $this->Html->tag('td', $city_name, array(
                        'style' => 'text-align: left;',
                    ));
                    $each_loop_message .= $this->Html->tag('td', $ttuj['Ttuj']['nopol'], array(
                        'style' => 'text-align: left;',
                    ));
                    $each_loop_message .= $this->Html->tag('td', $unit, array(
                        'style' => 'text-align: center;',
                    ));
                    $each_loop_message .= $this->Html->tag('td', $unitSj, array(
                        'style' => 'text-align: center;',
                    ));
                    $each_loop_message .= $this->Html->tag('td', $unitInvoiced, array(
                        'style' => 'text-align: center;',
                    ));
                    $each_loop_message .= $this->Html->tag('td', $unitSjNotRecipt, array(
                        'style' => 'text-align: center;',
                    ));
                    $each_loop_message .= $this->Html->tag('td', $unitJSUnInvoiced, array(
                        'style' => 'text-align: center;',
                    ));
                    $each_loop_message .= $this->Html->tag('td', $unitUnInvoiced, array(
                        'style' => 'text-align: center;',
                    ));
                    $each_loop_message .= $this->Html->tag('td', $tglSJ, array(
                        'style' => 'text-align: center;',
                    ));
                    $each_loop_message .= $this->Html->tag('td', $tglInvoiced, array(
                        'style' => 'text-align: center;',
                    ));
                    $each_loop_message .= $this->Html->tag('td', $leadSj, array(
                        'style' => 'text-align: center;',
                    ));
                    $each_loop_message .= $this->Html->tag('td', $leadSjBilling, array(
                        'style' => 'text-align: center;',
                    ));
                    $each_loop_message .= $this->Html->tag('td', $leadSjInvoiced, array(
                        'style' => 'text-align: center;',
                    ));
                    $each_loop_message .= '</tr>';
                }

                $each_loop_message .= '<tr>';
                $each_loop_message .= $this->Html->tag('td', '', array(
                    'style' => 'text-align: center;',
                ));
                $each_loop_message .= $this->Html->tag('td', '', array(
                    'style' => 'text-align: center;',
                ));
                $each_loop_message .= $this->Html->tag('td', '', array(
                    'style' => 'text-align: center;',
                ));
                $each_loop_message .= $this->Html->tag('td', $this->Html->tag('strong', __('Total'), array(
                    'style' => 'text-align: right;display: block;'
                )), array(
                    'style' => 'text-align: center;',
                ));
                $each_loop_message .= $this->Html->tag('td', $this->Html->tag('strong', $totalUnit), array(
                    'style' => 'text-align: center;',
                ));
                $each_loop_message .= $this->Html->tag('td', $this->Html->tag('strong', $totalUnitSj), array(
                    'style' => 'text-align: center;',
                ));
                $each_loop_message .= $this->Html->tag('td', $this->Html->tag('strong', $totalUnitInvoiced), array(
                    'style' => 'text-align: center;',
                ));
                $each_loop_message .= $this->Html->tag('td', $this->Html->tag('strong', $totalUnitSjNotRecipt), array(
                    'style' => 'text-align: center;',
                ));
                $each_loop_message .= $this->Html->tag('td', $this->Html->tag('strong', $totalUnitJSUnInvoiced), array(
                    'style' => 'text-align: center;',
                ));
                $each_loop_message .= $this->Html->tag('td', $this->Html->tag('strong', $totalUnitUnInvoiced), array(
                    'style' => 'text-align: center;',
                ));
                $each_loop_message .= $this->Html->tag('td', '', array(
                    'style' => 'text-align: center;',
                ));
                $each_loop_message .= $this->Html->tag('td', $this->Html->tag('strong', __('AVG')), array(
                    'style' => 'text-align: right;',
                ));

                if( !empty($cntAvgLeadSj) ) {
                    $avgLeadSj = $avgLeadSj/$cntAvgLeadSj;
                } else {
                    $avgLeadSj = 0;
                }

                if( !empty($cntAvgLeadSjBilling) ) {
                    $avgLeadSjBilling = $avgLeadSjBilling/$cntAvgLeadSjBilling;
                } else {
                    $avgLeadSjBilling = 0;
                }

                if( !empty($cntAvgLeadSjInvoiced) ) {
                    $avgLeadSjInvoiced = $avgLeadSjInvoiced/$cntAvgLeadSjInvoiced;
                } else {
                    $avgLeadSjInvoiced = 0;
                }

                $each_loop_message .= $this->Html->tag('td', $this->Html->tag('strong', round($avgLeadSj, 2)), array(
                    'align' => 'center',
                    'style' => 'text-align: center;',
                ));
                $each_loop_message .= $this->Html->tag('td', $this->Html->tag('strong', round($avgLeadSjBilling, 2)), array(
                    'align' => 'center',
                    'style' => 'text-align: center;',
                ));
                $each_loop_message .= $this->Html->tag('td', $this->Html->tag('strong', round($avgLeadSjInvoiced, 2)), array(
                    'align' => 'center',
                    'style' => 'text-align: center;',
                ));
                $each_loop_message .= '</tr>';
            }

            $date_title = $sub_module_title;
            $print_label = $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 24px;font-style: italic;margin-top: 10px;'
            ));
$tbl = <<<EOD

    <div class="clearfix container_16" id="content">
        <h2 class="grid_8" style="text-align: center;">$date_title</h2>
        <div style="text-align: right;display: block;">
            $period_text
        </div>
        <br>
        <table cellpadding="2" cellspacing="2" nobr="true" style="$table" border="1">
            <thead>
                $contentHeader
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