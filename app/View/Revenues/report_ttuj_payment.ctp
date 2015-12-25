<?php 
        $element = 'blocks/revenues/tables/report_ttuj_payment';
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $dataColumns = array(
            'data' => array(
                'name' => __('Tanggal'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'date\',width:100',
            ),
            'nodoc' => array(
                'name' => __('No Dok'),
                'style' => 'text-align: center;left: 120px;vertical-align: middle;',
                'data-options' => 'field:\'nodoc\',width:120',
            ),
            'branch' => array(
                'name' => __('Cabang'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'branch\',width:100',
            ),
            'nottuj' => array(
                'name' => __('No TTUJ'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'nottuj\',width:120',
            ),
            'ttujdate' => array(
                'name' => __('Tgl TTUJ'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'ttujdate\',width:100',
                'fix_column' => true
            ),
            'nopol' => array(
                'name' => __('Nopol'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'nopol\',width:100',
            ),
            'customer' => array(
                'name' => __('Customer'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'customer\',width:120',
            ),
            'from' => array(
                'name' => __('Asal'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'from\',width:100',
            ),
            'to' => array(
                'name' => __('Tujuan'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'to\',width:100',
            ),
            'driver' => array(
                'name' => __('Supir'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'driver\',width:100',
            ),
            'note' => array(
                'name' => __('Keterangan'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'note\',width:120',
            ),
            'type' => array(
                'name' => __('Jenis'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'type\',width:100',
            ),
            'total' => array(
                'name' => __('Total'),
                'style' => 'text-align: right;vertical-align: middle;',
                'align' => 'right',
                'data-options' => 'field:\'total\',width:100',
            ),
            'paid' => array(
                'name' => __('Total Pembayaran'),
                'style' => 'text-align: right;vertical-align: middle;',
                'align' => 'right',
                'data-options' => 'field:\'paid\',width:100',
            ),
            'saldo' => array(
                'name' => __('Saldo'),
                'style' => 'text-align: right;vertical-align: middle;',
                'align' => 'right',
                'data-options' => 'field:\'saldo\',width:100',
            ),
        );

        if( !empty($data_action) ){
            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table', true );

            echo $this->element(sprintf('blocks/common/tables/export_%s', $data_action), array(
                'tableHead' => $fieldColumn,
                'tableBody' => $this->element($element),
                'sub_module_title' => $module_title,
            ));
        } else {
            $this->Html->addCrumb($sub_module_title);

            $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
            $addStyle = 'width: 100%;height: 550px;';
            $addClass = 'easyui-datagrid';

            echo $this->element('blocks/revenues/search_report_ttuj_payment');
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