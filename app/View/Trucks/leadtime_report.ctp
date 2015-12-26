<?php 
        $element = 'blocks/trucks/tables/leadtime_report';
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $dataColumns = array(
            'nopol' => array(
                'name' => __('Nopol'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'nopol\',width:100',
                'align' => 'center',
            ),
            'capacity' => array(
                'name' => __('Kapasitas'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'capacity\',width:100',
                'align' => 'center',
            ),
            'driver' => array(
                'name' => __('Supir'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'driver\',width:100',
            ),
            'nottuj' => array(
                'name' => __('No TTUJ'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'nottuj\',width:120',
                'fix_column' => true
            ),
            'from' => array(
                'name' => __('Dari'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'from\',width:100',
            ),
            'to' => array(
                'name' => __('Tujuan'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'to\',width:100',
            ),
            'qty' => array(
                'name' => __('Qty'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'qty\',width:80',
                'align' => 'center',
            ),
            'status' => array(
                'name' => __('Status'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'status\',width:100',
                'align' => 'center',
            ),
            'ttujdate' => array(
                'name' => __('Tgl Berangkat'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'ttujdate\',width:100',
                'align' => 'center',
            ),
            'ttujdatetiba' => array(
                'name' => __('Tgl Tiba'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'ttujdatetiba\',width:100',
                'align' => 'center',
            ),
            'lt' => array(
                'name' => __('LT Pergi (Jam)'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'lt\',width:80',
                'align' => 'center',
            ),
            'targetlt' => array(
                'name' => __('Target LT (Jam)'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'targetlt\',width:80',
                'align' => 'center',
            ),
            'ttujdatebalik' => array(
                'name' => __('Tgl Balik'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'ttujdatebalik\',width:100',
                'align' => 'center',
            ),
            'ttujdatepool' => array(
                'name' => __('Tgl Sampai Pool'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'ttujdatepool\',width:100',
                'align' => 'center',
            ),
            'ltback' => array(
                'name' => __('LT Pulang (Jam)'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'ltback\',width:80',
                'align' => 'center',
            ),
            'totallt' => array(
                'name' => __('Total LT (Jam)'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'totallt\',width:80',
                'align' => 'center',
            ),
            'targetltpool' => array(
                'name' => __('Target LT (Jam)'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'targetltpool\',width:80',
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
            $addStyle = 'width: 100%;height: 550px;';
            $addClass = 'easyui-datagrid';

            echo $this->element('blocks/trucks/searchs/leadtime_report');
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
<?php 
        }
?>