<?php 
        $element = 'blocks/revenues/tables/report_revenue';
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $dataColumns = array(
            'date' => array(
                'name' => __('Tanggal'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'date\',width:100',
            ),
            'branch' => array(
                'name' => __('Cabang'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'branch\',width:50',
            ),
            'customer' => array(
                'name' => __('Customer'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'customer\',width:100',
            ),
            'nodoc' => array(
                'name' => __('No TTUJ'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'nodoc\',width:100',
            ),
            'nopol' => array(
                'name' => __('Nopol'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'nopol\',width:100',
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
            'unit' => array(
                'name' => __('Jumlah Unit'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'unit\',width:100',
            ),
            'price' => array(
                'name' => __('Harga Unit'),
                'style' => 'text-align: center;vertical-align: middle;',
            ),
            'total' => array(
                'name' => __('Total'),
                'style' => 'text-align: center;vertical-align: middle;',
            ),
            'inv' => array(
                'name' => __('No Invoice'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'inv\',width:100',
            ),
            'status' => array(
                'name' => __('Status'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'status\',width:100',
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

            echo $this->element('blocks/revenues/search_report_revenue');
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
        <table id="tt" class="table table-bordered" singleSelect="true">
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