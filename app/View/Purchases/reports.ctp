<?php 

        $full_name = $this->Common->filterEmptyField($User, 'Employe', 'full_name');
        $element = 'blocks/purchases/purchase_orders/tables/reports';
        $dataColumns = array(
            'nodoc' => array(
                'name' => __('No Dokumen'),
                'field_model' => 'PurchaseOrder.nodoc',
                'align' => 'center',
                'style' => 'text-align: center;',
                'data-options' => 'field:\'nodoc\',width:120',
                'mainalign' => 'center',
                'align' => 'left',
            ),
            'transaction_date' => array(
                'name' => __('Tgl PO'),
                'field_model' => 'PurchaseOrder.transaction_date',
                'style' => 'text-align: center;',
                'data-options' => 'field:\'transaction_date\',width:100',
                'mainalign' => 'center',
                'align' => 'left',
            ),
            'supplier' => array(
                'name' => __('Supplier'),
                'field_model' => 'Vendor.name',
                'style' => 'text-align: center;',
                'data-options' => 'field:\'supplier\',width:120',
                'mainalign' => 'center',
                'align' => 'left',
                'fix_column' => true,
            ),
            'no_sq' => array(
                'name' => __('No. SQ'),
                'field_model' => 'PurchaseOrder.no_sq',
                'style' => 'text-align: center;',
                'data-options' => 'field:\'no_sq\',width:100',
                'mainalign' => 'center',
                'align' => 'left',
            ),
            'ppn_type' => array(
                'name' => __('Jenis Pajak'),
                'field_model' => 'PurchaseOrder.ppn_type',
                'style' => 'text-align: center;',
                'data-options' => 'field:\'ppn_type\',width:100',
                'mainalign' => 'center',
                'align' => 'center',
            ),
            'etd' => array(
                'name' => __('E.T.D'),
                'field_model' => 'PurchaseOrder.etd',
                'align' => 'center',
                'style' => 'text-align: center;',
                'data-options' => 'field:\'etd\',width:80',
                'align' => 'center',
            ),
            'top' => array(
                'name' => __('T.O.P'),
                'field_model' => 'PurchaseOrder.top',
                'style' => 'text-align: center;',
                'data-options' => 'field:\'top\',width:80',
                'align' => 'center',
            ),
            'note' => array(
                'name' => __('Keterangan'),
                'field_model' => 'PurchaseOrder.note',
                'style' => 'text-align: center;',
                'data-options' => 'field:\'note\',width:150',
                'mainalign' => 'center',
                'align' => 'left',
            ),
            'qty' => array(
                'name' => __('Qty'),
                'style' => 'text-align: center;',
                'data-options' => 'field:\'qty\',width:80',
                'align' => 'center',
            ),
            'qty_retur' => array(
                'name' => __('Qty Retur'),
                'style' => 'text-align: center;',
                'data-options' => 'field:\'qty_retur\',width:80',
                'align' => 'center',
            ),
            'total_qty' => array(
                'name' => __('Total Qty'),
                'style' => 'text-align: center;',
                'data-options' => 'field:\'total_qty\',width:80',
                'align' => 'center',
            ),
            'price' => array(
                'name' => __('Total Harga'),
                'style' => 'text-align: center;',
                'data-options' => 'field:\'price\',width:100',
                'mainalign' => 'center',
                'align' => 'right',
            ),
            'disc' => array(
                'name' => __('Potongan'),
                'style' => 'text-align: center;',
                'data-options' => 'field:\'disc\',width:100',
                'mainalign' => 'center',
                'align' => 'right',
            ),
            'ppn' => array(
                'name' => __('Pajak'),
                'style' => 'text-align: center;',
                'data-options' => 'field:\'ppn\',width:100',
                'mainalign' => 'center',
                'align' => 'right',
            ),
            'grandtotal' => array(
                'name' => __('Grandtotal'),
                'style' => 'text-align: center;',
                'data-options' => 'field:\'grandtotal\',width:100',
                'mainalign' => 'center',
                'align' => 'right',
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => 'PurchaseOrder.transaction_status',
                'style' => 'text-align: center;',
                'data-options' => 'field:\'status\',width:100',
                'align' => 'center',
            ),
            'receipt_status' => array(
                'name' => __('Penerimaan'),
                'field_model' => 'PurchaseOrder.draft_receipt_status',
                'style' => 'text-align: center;',
                'data-options' => 'field:\'receipt_status\',width:100',
                'align' => 'center',
            ),
            'retur_status' => array(
                'name' => __('Retur'),
                'field_model' => 'PurchaseOrder.draft_retur_status',
                'style' => 'text-align: center;',
                'data-options' => 'field:\'retur_status\',width:100',
                'align' => 'center',
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

            echo $this->element('blocks/purchases/purchase_orders/forms/search_reports');
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
        <table id="tt" class="table table-bordered easyui-datagrid" style="width: 100%;height: 550px;" singleSelect="true">
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