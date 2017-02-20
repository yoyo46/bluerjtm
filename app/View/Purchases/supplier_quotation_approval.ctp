<?php
        $this->Html->addCrumb(__('Penawaran Supplier'), array(
            'action' => 'supplier_quotations',
        ));
        $this->Html->addCrumb($sub_module_title);

        $id = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'id');
        $nodoc = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'nodoc');
        $tgl = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'transaction_date');
        $description = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'note', '-');
        $status = $this->Common->filterEmptyField($value, 'SupplierQuotation', 'approval');

        $vendor = $this->Common->filterEmptyField($value, 'Vendor', 'name');
        $products = $this->Common->filterEmptyField($value, 'SupplierQuotationDetail');

        $customDate = $this->Common->formatDate($tgl, 'd/m/Y');
        $customStatus = $this->Common->_callStatusApproval($status);
        $noref = str_pad($id, 6, '0', STR_PAD_LEFT);

        $user_approvals = $this->Common->filterEmptyField($result_approval, 'user_otorisasi_approvals');
        $show_approval = $this->Common->filterEmptyField($result_approval, 'show_approval');
        $dataColumns = array(
            'product' => array(
                'name' => __('Barang'),
            ),
            'unit' => array(
                'name' => __('Satuan'),
                'class' => 'text-center',
            ),
            'price' => array(
                'name' => __('Harga'),
                'class' => 'text-center',
            ),
            'disc' => array(
                'name' => __('Potongan'),
                'class' => 'text-center',
            ),
            'ppn' => array(
                'name' => __('Pajak'),
                'class' => 'text-center',
            ),
            'total' => array(
                'name' => __('Total'),
                'class' => 'text-center',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        if( $status == 'pending' ) {
            $completed = false;
        } else {
            $completed = true;
        }
?>
<div class="row">
    <div class="col-sm-5">
        <div class="box">
            <?php 
                    echo $this->element('blocks/common/box_header', array(
                        'title' => __('Informasi SQ'),
                    ));
            ?>
            <div class="box-body">
                <dl class="dl-horizontal">
                    <?php 
                            echo $this->Html->tag('dt', __('No. Referensi'));
                            echo $this->Html->tag('dd', $noref);

                            echo $this->Html->tag('dt', __('No. Dokumen'));
                            echo $this->Html->tag('dd', $nodoc);

                            echo $this->Html->tag('dt', __('Vendor'));
                            echo $this->Html->tag('dd', $vendor);

                            echo $this->Html->tag('dt', __('Tgl SQ'));
                            echo $this->Html->tag('dd', $customDate);

                            echo $this->Html->tag('dt', __('Keterangan'));
                            echo $this->Html->tag('dd', $description);

                            echo $this->Html->tag('dt', __('Status'));
                            echo $this->Html->tag('dd', $customStatus);
                    ?>
            </div>
        </div>
    </div>
    <div class="col-sm-7">
        <div class="box box-success">
            <?php 
                    echo $this->element('blocks/common/box_header', array(
                        'title' => __('Informasi Barang'),
                    ));
            ?>
            <div class="box-body table-responsive">
                <table class="table table-hover">
                    <?php
                            if( !empty($fieldColumn) ) {
                                echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                            }
                    ?>
                    <tbody>
                        <?php
                                if( !empty($products) ){
                                    $grandTotal = 0;

                                    foreach ($products as $key => $product) {
                                        $name = $this->Common->filterEmptyField($product, 'Product', 'full_name');
                                        $satuan = $this->Common->filterEmptyField($product, 'ProductUnit', 'name');

                                        $price = $this->Common->filterEmptyField($product, 'SupplierQuotationDetail', 'price', 0);
                                        $disc = $this->Common->filterEmptyField($product, 'SupplierQuotationDetail', 'disc', 0);
                                        $ppn = $this->Common->filterEmptyField($product, 'SupplierQuotationDetail', 'ppn', 0);
                                        $total = $price - $disc + $ppn;

                                        $customPrice = $this->Common->getFormatPrice($price);
                                        $customDisc = $this->Common->getFormatPrice($disc);
                                        $customPpn = $this->Common->getFormatPrice($ppn);
                                        $customTotal = $this->Common->getFormatPrice($total);
                                        $grandTotal += $total;
                        ?>
                        <tr>
                            <?php
                                    echo $this->Html->tag('td', $name);
                                    echo $this->Html->tag('td', $satuan, array(
                                        'class' => 'text-center',
                                    ));
                                    echo $this->Html->tag('td', $customPrice, array(
                                        'align' => 'right'
                                    ));
                                    echo $this->Html->tag('td', $customDisc, array(
                                        'align' => 'right'
                                    ));
                                    echo $this->Html->tag('td', $customPpn, array(
                                        'align' => 'right'
                                    ));
                                    echo $this->Html->tag('td', $customTotal, array(
                                        'align' => 'right'
                                    ));
                            ?>
                        </tr>
                        <?php
                                    }
                                }

                                $contentTr = $this->Html->tag('td', __('Total'), array(
                                    'class' => 'text-right bold',
                                    'colspan' => 5,
                                ));
                                $contentTr .= $this->Html->tag('td', $this->Common->getFormatPrice($grandTotal), array(
                                    'class' => 'text-right bold',
                                ));
                                echo $this->Html->tag('tr', $contentTr);
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
        echo $this->element('blocks/cashbanks/tables/list_approvals', array(
            'title' => __('Penawaran Supplier Approval'),
            'user_otorisasi_approvals' => $user_approvals,
            'show_approval' => $show_approval,
            'completed' => $completed,
            'urlBack' => array(
                'controller' => 'purchases',
                'action' => 'supplier_quotations', 
                'admin' => false,
            ),
        ));
?>