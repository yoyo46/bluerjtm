<?php 
        if( !empty($data_action) ){
            $headerRowspan = 2;
        } else {
            $headerRowspan = false;
        }

        $full_name = $this->Common->filterEmptyField($User, 'Employe', 'full_name');
        $element = 'blocks/revenues/tables/report_monitoring_sj_revenue';
        $dataColumns = array(
            'no_ttuj' => array(
                'name' => __('NO TTUJ'),
                'field_model' => 'Ttuj.no_ttuj',
                'style' => 'text-align: center;width: 150px;',
                'data-options' => 'field:\'no_ttuj\',width:120',
                'rowspan' => $headerRowspan,
                'mainalign' => 'center',
                'align' => 'left',
            ),
            'date' => array(
                'name' => __('TANGGAL'),
                'field_model' => 'Ttuj.ttuj_date',
                'align' => 'center',
                'style' => 'text-align: center;width: 100px;',
                'data-options' => 'field:\'ttuj_date\',width:100',
                'rowspan' => $headerRowspan,
            ),
            'to_city_name' => array(
                'name' => __('TUJUAN'),
                'field_model' => 'Ttuj.to_city_name',
                'style' => 'text-align: center;width: 120px;',
                'data-options' => 'field:\'to_city_name\',width:120',
                'rowspan' => $headerRowspan,
                'mainalign' => 'center',
                'align' => 'left',
            ),
            'nopol' => array(
                'name' => __('NOPOL'),
                'field_model' => 'Ttuj.nopol',
                'style' => 'text-align: center;width: 120px;',
                'data-options' => 'field:\'nopol\',width:100',
                'rowspan' => $headerRowspan,
                'mainalign' => 'center',
                'align' => 'left',
            ),
            'unit' => array(
                'name' => __('UNIT'),
                'align' => 'center',
                'style' => 'text-align: center;width: 80px;',
                'data-options' => 'field:\'unit\',width:80',
                'rowspan' => $headerRowspan,
                'fix_column' => true,
            ),
            'sj' => array(
                'name' => __('SURAT JALAN (unit)'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'sj\'',
                'align' => 'center',
                'child' => array(
                    'sj_receipt' => array(
                        'name' => __('KEMBALI'),
                        'style' => 'text-align: center;',
                        'data-options' => 'field:\'sj_receipt\',width:80',
                        'align' => 'center',
                    ),
                    'sj_invoice' => array(
                        'name' => __('TERTAGIH'),
                        'style' => 'text-align: center;',
                        'data-options' => 'field:\'sj_invoice\',width:80',
                        'align' => 'center',
                    ),
                ),
            ),
            'selisih' => array(
                'name' => __('SELISIH'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'selisih\'',
                'align' => 'center',
                'child' => array(
                    'sj_not_receipt' => array(
                        'name' => __('SJ BELUM KEMBALI'),
                        'style' => 'text-align: center;',
                        'data-options' => 'field:\'sj_not_receipt\',width:120',
                        'align' => 'center',
                    ),
                    'sj_not_receipt_not_invoice' => array(
                        'name' => __('SJ KEMBALI BELUM TERTAGIH'),
                        'style' => 'text-align: center;',
                        'data-options' => 'field:\'sj_not_receipt_not_invoice\',width:120,styler:cellStyler',
                        'align' => 'center',
                    ),
                    'sj_not_invoice' => array(
                        'name' => __('SJ BELUM TERTAGIH'),
                        'style' => 'text-align: center;',
                        'data-options' => 'field:\'sj_not_invoice\',width:120',
                        'align' => 'center',
                    ),
                ),
            ),
            'date_receipt' => array(
                'name' => __('TGL SJ KEMBALI'),
                'style' => 'text-align: center;',
                'data-options' => 'field:\'date_receipt\',width:100',
                'rowspan' => 2,
                'align' => 'center',
            ),
            'date_invoice' => array(
                'name' => __('TGL INVOICE'),
                'style' => 'text-align: center;',
                'data-options' => 'field:\'date_invoice\',width:100',
                'rowspan' => 2,
                'align' => 'center',
            ),
            'leads' => array(
                'name' => __('LEAD TIME (DAY)'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'leads\'',
                'align' => 'center',
                'child' => array(
                    'sj_lead_not_invoice' => array(
                        'name' => __('SJ KEMBALI'),
                        'style' => 'text-align: center;',
                        'data-options' => 'field:\'sj_lead_not_invoice\',width:100',
                        'align' => 'center',
                    ),
                    'sj_lead_process_billing' => array(
                        'name' => __('SJ PROSES BILLING'),
                        'style' => 'text-align: center;',
                        'data-options' => 'field:\'sj_lead_process_billing\',width:100',
                        'align' => 'center',
                    ),
                    'sj_lead_invoice' => array(
                        'name' => __('SJ TERTAGIH'),
                        'style' => 'text-align: center;',
                        'data-options' => 'field:\'sj_lead_invoice\',width:100',
                        'align' => 'center',
                    ),
                ),
            ),
        );

        if( !empty($data_action) ){
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table', true );

            echo $this->element(sprintf('blocks/common/tables/export_%s', $data_action), array(
                'tableHead' => $fieldColumn,
                'tableBody' => $this->element($element),
                'sub_module_title' => sprintf('%s - %s', $sub_module_title, $period_text),
                'contentTr' => false,
            ));
        } else {
            $this->Html->addCrumb($sub_module_title);

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
            $addStyle = 'width: 100%;height: 550px;';
            $addClass = 'easyui-datagrid';

            echo $this->element('blocks/revenues/searchs/report_monitoring_sj_revenue');
?>
<section class="content invoice">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => sprintf('%s - %s', $sub_module_title, $period_text),
            ));
            echo $this->Common->_getPrint(array(
                '_attr' => array(
                    'escape' => false,
                ),
            ));
    ?>
    <div class="table-responsive">
        <?php 
                if(!empty($values)){
        ?>
        <table id="tt" class="table table-bordered <?php echo $addClass; ?>" style="<?php echo $addStyle; ?>" singleSelect="true">
            <thead frozen="true">
                <tr>
                    <?php
                            if( !empty($fieldColumn) ) {
                                echo $fieldColumn;
                            }
                    ?>
                </tr>
            </thead>
            <?php 
                    echo $this->Html->tag('tbody', $this->element($element));
            ?>
        </table>
        <?php 
                } else {
                    echo $this->Html->tag('p', __('Data belum tersedia.'), array(
                        'class' => 'alert alert-warning text-center',
                    ));
                }
        ?>
    </div><!-- /.box-body -->
    <?php 
            echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 14px;font-style: italic;margin-top: 10px;'
            ));
            echo $this->Html->tag('div', $this->element('pagination'), array(
                'class' => 'pagination-report'
            ));
    ?>
</div>
<script type="text/javascript">
    function cellStyler(value,row,index, rel){
        if( isNaN(value) ) {
            value = parseInt($(value).filter('strong').html());
        }

        if( value < 0 ) {
            return 'background-color:#f2dede;color:#a94442;';
        } else {
            return false;
        }
    }
</script>
<?php 
        }
?>