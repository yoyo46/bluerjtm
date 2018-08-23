<?php 
        if( !empty($data_action) ){
            $headerRowspan = 2;
        } else {
            $headerRowspan = false;
        }

        $element = 'blocks/leasings/tables/leasing_report';
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $dataColumns = array(
            'no_contract' => array(
                'name' => __('No. Kontrak'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'no_contract\',width:100',
                'align' => 'center',
                'rowspan' => $headerRowspan,
            ),
            'vendor' => array(
                'name' => __('Supplier'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'vendor\',width:120',
                'align' => 'center',
                'rowspan' => $headerRowspan,
            ),
            'start_date' => array(
                'name' => __('Tgl Mulai'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'start_date\',width:100',
                'align' => 'center',
                'rowspan' => $headerRowspan,
            ),
            'end_date' => array(
                'name' => __('Tgl Berakhir'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'end_date\',width:100',
                'rowspan' => $headerRowspan,
                'align' => 'center',
                'fix_column' => true,
            ),
            'price' => array(
                'name' => __('Harga Truk'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'price\',width:120',
                'align' => 'center',
                'rowspan' => 2,
            ),
            'dp' => array(
                'name' => __('DP'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'dp\'',
                'align' => 'center',
                'child' => array(
                    'total_dp' => array(
                        'name' => __('DP'),
                        'style' => 'text-align: center;vertical-align: middle;',
                        'data-options' => 'field:\'total_dp\',width:120',
                        'mainalign' => 'center',
                        'align' => 'right',
                    ),
                    'total_dp_paid' => array(
                        'name' => __('Pembayaran'),
                        'style' => 'text-align: center;vertical-align: middle;',
                        'data-options' => 'field:\'total_dp_paid\',width:120',
                        'mainalign' => 'center',
                        'align' => 'right',
                    ),
                    'total_sisa_dp' => array(
                        'name' => __('Sisa'),
                        'style' => 'text-align: center;vertical-align: middle;',
                        'data-options' => 'field:\'total_sisa_dp\',width:120',
                        'mainalign' => 'center',
                        'align' => 'right',
                    ),
                ),
            ),
            'facility' => array(
                'name' => __('Fasilitas Kredit'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'facility\'',
                'align' => 'center',
                'child' => array(
                    'bunga_facility' => array(
                        'name' => __('Bunga (%)'),
                        'style' => 'text-align: center;vertical-align: middle;',
                        'data-options' => 'field:\'bunga_facility\',width:100',
                        'align' => 'center',
                    ),
                    'uang_jalan' => array(
                        'name' => __('Bulan'),
                        'style' => 'text-align: center;vertical-align: middle;',
                        'data-options' => 'field:\'uang_jalan\',width:100',
                        'align' => 'center',
                    ),
                    'biaya_maintenance' => array(
                        'name' => __('Pokok'),
                        'style' => 'text-align: center;vertical-align: middle;',
                        'data-options' => 'field:\'biaya_maintenance\',width:120',
                        'mainalign' => 'center',
                        'align' => 'right',
                    ),
                    'biaya_lainnya' => array(
                        'name' => __('Bunga'),
                        'style' => 'text-align: center;vertical-align: middle;',
                        'data-options' => 'field:\'biaya_lainnya\',width:120',
                        'mainalign' => 'center',
                        'align' => 'right',
                    ),
                    'total' => array(
                        'name' => __('Total'),
                        'style' => 'text-align: center;vertical-align: middle;',
                        'data-options' => 'field:\'total\',width:120',
                        'mainalign' => 'center',
                        'align' => 'right',
                    ),
                ),
            ),
            'installment' => array(
                'name' => __('Angsuran / Bulan'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'installment\'',
                'align' => 'center',
                'child' => array(
                    'pokok' => array(
                        'name' => __('Pokok'),
                        'style' => 'text-align: center;vertical-align: middle;',
                        'data-options' => 'field:\'pokok\',width:120',
                        'mainalign' => 'center',
                        'align' => 'right',
                    ),
                    'bunga' => array(
                        'name' => __('Bunga'),
                        'style' => 'text-align: center;vertical-align: middle;',
                        'data-options' => 'field:\'bunga\',width:120',
                        'mainalign' => 'center',
                        'align' => 'right',
                    ),
                    'installment_total' => array(
                        'name' => __('Total'),
                        'style' => 'text-align: center;vertical-align: middle;',
                        'data-options' => 'field:\'installment_total\',width:120',
                        'mainalign' => 'center',
                        'align' => 'right',
                    ),
                ),
            ),
            'payment' => array(
                'name' => __('Total Pembayaran'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'payment\'',
                'align' => 'center',
                'child' => array(
                    'x' => array(
                        'name' => __('X'),
                        'style' => 'text-align: center;vertical-align: middle;',
                        'data-options' => 'field:\'x\',width:100',
                        'align' => 'center',
                    ),
                    'pokok_paid' => array(
                        'name' => __('Pokok'),
                        'style' => 'text-align: center;vertical-align: middle;',
                        'data-options' => 'field:\'pokok_paid\',width:120',
                        'mainalign' => 'center',
                        'align' => 'right',
                    ),
                    'bunga_paid' => array(
                        'name' => __('Bunga'),
                        'style' => 'text-align: center;vertical-align: middle;',
                        'data-options' => 'field:\'bunga_paid\',width:120',
                        'mainalign' => 'center',
                        'align' => 'right',
                    ),
                    'total_paid' => array(
                        'name' => __('Total'),
                        'style' => 'text-align: center;vertical-align: middle;',
                        'data-options' => 'field:\'total_paid\',width:120',
                        'mainalign' => 'center',
                        'align' => 'right',
                    ),
                ),
            ),
            'sisa' => array(
                'name' => __('Sisa Angsuran'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'payment\'',
                'align' => 'center',
                'child' => array(
                    'sisa_x' => array(
                        'name' => __('X'),
                        'style' => 'text-align: center;vertical-align: middle;',
                        'data-options' => 'field:\'sisa_x\',width:100',
                        'align' => 'center',
                    ),
                    'pokok_sisa' => array(
                        'name' => __('Pokok'),
                        'style' => 'text-align: center;vertical-align: middle;',
                        'data-options' => 'field:\'pokok_sisa\',width:120',
                        'mainalign' => 'center',
                        'align' => 'right',
                    ),
                    'bunga_sisa' => array(
                        'name' => __('Bunga'),
                        'style' => 'text-align: center;vertical-align: middle;',
                        'data-options' => 'field:\'bunga_sisa\',width:120',
                        'mainalign' => 'center',
                        'align' => 'right',
                    ),
                    'total_sisa' => array(
                        'name' => __('Total'),
                        'style' => 'text-align: center;vertical-align: middle;',
                        'data-options' => 'field:\'total_sisa\',width:120',
                        'mainalign' => 'center',
                        'align' => 'right',
                    ),
                ),
            ),
        );

        if( !empty($data_action) ){
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table', $data_action );

            echo $this->element(sprintf('blocks/common/tables/export_%s', $data_action), array(
                'tableHead' => $fieldColumn,
                'tableBody' => $this->element($element),
                'sub_module_title' => $module_title,
                'contentTr' => false,
            ));
        } else {
            $this->Html->addCrumb($sub_module_title);

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
            $addStyle = 'width: 100%;height: 550px;';
            $addClass = 'easyui-datagrid';
            // $addClass = '';

            echo $this->element('blocks/leasings/searchs/leasing_report');
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
        <table id="tt" class="table table-bordered sorting <?php echo $addClass; ?>" style="<?php echo $addStyle; ?>" singleSelect="true" data-options="rowStyler: rowColored">
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