<?php 
        $element = 'blocks/revenues/tables/report_expense_per_truck';
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $dataColumns = array(
            'nopol' => array(
                'name' => __('Nopol'),
                'field_model' => 'Truck.nopol',
                'style' => 'text-align: center;vertical-align: middle;',
                'align' => 'center',
                'rowspan' => 2,
            ),
            'year' => array(
                'name' => __('Tahun'),
                'field_model' => 'Truck.tahun',
                'style' => 'text-align: center;vertical-align: middle;',
                'align' => 'center',
                'rowspan' => 2,
            ),
            'brand' => array(
                'name' => __('Merk'),
                'field_model' => 'TruckBrand.name',
                'style' => 'text-align: left;vertical-align: middle;',
                'rowspan' => 2,
            ),
            'category' => array(
                'name' => __('Jenis'),
                'field_model' => 'TruckCategory.name',
                'style' => 'text-align: left;vertical-align: middle;',
                'rowspan' => 2,
            ),
            'capacity' => array(
                'name' => __('Kapasitas'),
                'field_model' => 'Truck.capacity',
                'style' => 'text-align: left;vertical-align: middle;',
                'rowspan' => 2,
            ),
            'alocation' => array(
                'name' => __('Alokasi'),
                'field_model' => 'CustomerNoType.code',
                'style' => 'text-align: left;vertical-align: middle;',
                'rowspan' => 2,
            ),
            'total' => array(
                'name' => __('Total Revenue'),
                'style' => 'text-align: center;vertical-align: middle;',
                'align' => 'center',
                'rowspan' => 2,
            ),
            'expense' => array(
                'name' => __('Expense'),
                'style' => 'text-align: center;vertical-align: middle;',
                'child' => array(
                    'uang_jalan' => array(
                        'name' => __('Biaya uang jalan'),
                        'style' => 'text-align: center;vertical-align: middle;',
                    ),
                    'biaya_maintenance' => array(
                        'name' => __('Biaya Maintenance'),
                        'style' => 'text-align: center;vertical-align: middle;',
                    ),
                    'biaya_lainnya' => array(
                        'name' => __('Biaya lain2'),
                        'style' => 'text-align: center;vertical-align: middle;',
                    ),
                    'total' => array(
                        'name' => __('Total'),
                        'style' => 'text-align: center;vertical-align: middle;',
                    ),
                ),
            ),
            'gross_profit' => array(
                'name' => __('Gross Profit'),
                'style' => 'text-align: center;vertical-align: middle;',
                'align' => 'center',
                'rowspan' => 2,
            ),
            'er' => array(
                'name' => __('e/r (%)'),
                'style' => 'text-align: center;vertical-align: middle;',
                'align' => 'center',
                'rowspan' => 2,
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

            echo $this->element('blocks/revenues/searchs/report_expense_per_truck');
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
        <table id="tt" class="table table-bordered sorting">
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