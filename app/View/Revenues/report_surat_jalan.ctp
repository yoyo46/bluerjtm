<?php 
        $element = 'blocks/revenues/tables/report_surat_jalan';
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $dataColumns = array(
            'noref' => array(
                'name' => __('No. Ref'),
                'field_model' => 'SuratJalan.id',
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'noref\',width:80',
                'align' => 'left',
                'mainalign' => 'center',
            ),
            'nodoc' => array(
                'name' => __('No. Doc'),
                'field_model' => 'SuratJalan.nodoc',
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'nodoc\',width:120',
                'align' => 'left',
                'mainalign' => 'center',
            ),
            'date' => array(
                'name' => __('Tgl Diterima'),
                'field_model' => 'SuratJalan.tgl_surat_jalan',
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'date\',width:80',
                'align' => 'center',
            ),
            'note' => array(
                'name' => __('Keterangan'),
                'field_model' => 'SuratJalan.note',
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'note\',width:200',
                'fix_column' => true,
                'align' => 'left',
                'mainalign' => 'center',
            ),
            'nottuj' => array(
                'name' => __('No. TTUJ'),
                'field_model' => 'Ttuj.no_ttuj',
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'nottuj\',width:120',
                'align' => 'left',
                'mainalign' => 'center',
            ),
            'date_ttuj' => array(
                'name' => __('Tgl TTUJ'),
                'field_model' => 'Ttuj.ttuj_date',
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'date_ttuj\',width:80',
                'align' => 'center',
            ),
            'nopol' => array(
                'name' => __('Nopol'),
                'field_model' => 'Ttuj.nopol',
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'nopol\',width:80',
                'align' => 'center',
            ),
            'driver' => array(
                'name' => __('Supir'),
                'field_model' => 'Ttuj.driver_name',
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'driver\',width:120',
                'align' => 'left',
                'mainalign' => 'center',
            ),
            'to' => array(
                'name' => __('Tujuan'),
                'field_model' => 'Ttuj.to_city_name',
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'to\',width:200',
                'align' => 'left',
                'mainalign' => 'center',
            ),
            'customer' => array(
                'name' => __('Customer'),
                'field_model' => 'Ttuj.customer_name',
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'customer\',width:150',
                'align' => 'left',
                'mainalign' => 'center',
            ),
            'note_ttuj' => array(
                'name' => __('Keterangan TTUJ'),
                'field_model' => 'Ttuj.note',
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'note_ttuj\',width:200',
                'align' => 'left',
                'mainalign' => 'center',
            ),
            'qty_ttuj' => array(
                'name' => __('Qty TTUJ'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'qty_ttuj\',width:80',
                'align' => 'center',
            ),
            'qty_sj' => array(
                'name' => __('Qty Diterima'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'qty_sj\',width:80',
                'align' => 'center',
            ),
        );

        if( !empty($data_action) ){
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table', true );

            echo $this->element(sprintf('blocks/common/tables/export_%s', $data_action), array(
                'tableHead' => $fieldColumn,
                'tableBody' => $this->element($element),
                'sub_module_title' => $module_title,
                'contentTr' => false,
            ));
        } else {
            $this->Html->addCrumb($sub_module_title);

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

            echo $this->element('blocks/revenues/searchs/report_surat_jalan');
?>
<section class="content invoice">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
    </h2>
    <?php 
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
        <table id="tt" class="table table-bordered easyui-datagrid sorting" singleSelect="true" data-options="
            singleSelect: true,
            rowStyler: function(index,row){
                if ( index%2 == 0 ){
                    return 'background-color:#f5f5f5';
                } else {
                    return 'background-color:#d9edf7';
                }
            }
        ">
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
<?php 
        }
?>