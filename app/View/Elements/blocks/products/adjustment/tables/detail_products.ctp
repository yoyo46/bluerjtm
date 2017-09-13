<?php 
        $view = !empty($view)?$view:false;
        $value = !empty($value)?$value:false;
        $data = $this->request->data;
        $dataDetail = $this->Common->filterEmptyField($data, 'ProductAdjustmentDetail');
        $id = $this->Common->filterEmptyField($value, 'ProductAdjustment', 'id', 0);

        $dataColumns = array(
            'code' => array(
                'name' => __('Kode'),
                'class' => 'text-center',
                'style' => 'width:5%;',
            ),
            'name' => array(
                'name' => __('Nama'),
                'style' => 'width:10%;',
            ),
            'unit' => array(
                'name' => __('Satuan'),
                'class' => 'text-center',
                'style' => 'width:5%;',
            ),
            'note' => array(
                'name' => __('Keterangan'),
                'style' => 'width:10%;',
            ),
        );

        if( empty($view) ) {
            $dataColumns = array_merge($dataColumns, array(
                'qty' => array(
                    'name' => __('Qty'),
                    'class' => 'text-center',
                    'style' => 'width:5%;',
                ),
                'qty_adjust' => array(
                    'name' => __('Adjustment'),
                    'class' => 'text-center',
                    'style' => 'width:8%;',
                ),
                'qty_difference' => array(
                    'name' => __('Difference'),
                    'class' => 'text-center',
                    'style' => 'width:5%;',
                ),
            ));
        } else {
            $dataColumns = array_merge($dataColumns, array(
                'type' => array(
                    'name' => __('Last Stok'),
                    'class' => 'text-center',
                    'style' => 'width:8%;',
                ),
                'qty' => array(
                    'name' => __('Adjustment'),
                    'class' => 'text-center',
                    'style' => 'width:8%;',
                ),
                'qty_difference' => array(
                    'name' => __('Difference'),
                    'class' => 'text-center',
                    'style' => 'width:5%;',
                ),
            ));
        }

        $dataColumns = array_merge($dataColumns, array(
            'price' => array(
                'name' => __('Harga'),
                'class' => 'text-right',
                'style' => 'width:8%;',
            ),
            'serial_number' => array(
                'name' => __('No Seri'),
                'class' => 'text-center',
                'style' => 'width:12%;',
            ),
        ));

        if( empty($view) ) {
            $dataColumns = array_merge($dataColumns, array(
                'action' => array(
                    'name' => __('Action'),
                    'class' => 'text-center',
                    'style' => 'width:5%;',
                ),
            ));
        }

        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        if( empty($view) ) {
            echo $this->Html->tag('div', $this->Html->link($this->Common->icon('plus-square').__(' Ambil Barang'), $this->Html->url( array(
                    'controller'=> 'products', 
                    'action' => 'adjustment_products',
                    'admin' => false,
                )), array(
                    'escape' => false,
                    'allow' => true,
                    'title' => __('Daftar Barang'),
                    'class' => 'btn bg-maroon ajaxCustomModal',
                    'data-form' => '.adjust-form',
                )), array(
                'class' => "form-group",
            ));
        }
?>
<div class="temp-document-picker document-calc">
    <div class="box box-success">
        <?php 
                echo $this->element('blocks/common/box_header', array(
                    'title' => __('Informasi Barang'),
                ));
        ?>
        <div class="box-body table-responsive">
            <table class="table table-hover" id="wrapper-write">
                <?php
                        if( !empty($fieldColumn) ) {
                            echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                        }
                ?>
                <tbody class="wrapper-table-documents" data-remove="true">
                    <?php
                            $grandtotal = 0;
                            $total_qty_adjustment = 0;
                            $total_in_qty = 0;

                            if(!empty($dataDetail)){
                                foreach ($dataDetail as $key => $value) {
                                    $id = $this->Common->filterEmptyField($value, 'ProductAdjustmentDetail', 'product_id');
                                    $qty = $this->Common->filterEmptyField($value, 'ProductAdjustmentDetail', 'qty', 0);
                                    $price = $this->Common->filterEmptyField($value, 'ProductAdjustmentDetail', 'price', 0);
                                    $code = $this->Common->filterEmptyField($value, 'ProductAdjustmentDetail', 'code');
                                    $serial_number = $this->Common->filterEmptyField($value, 'ProductAdjustmentDetailSerialNumber');

                                    $grandtotal += $price;
                                    $total_qty_adjustment += $qty;

                                    if( !empty($code) ) {
                                        $modelName = 'ProductAdjustmentDetail';
                                    } else {
                                        $modelName = 'Product';
                                    }
                                    if( empty($serial_number) ) {
                                        $serial_number = $this->Common->filterEmptyField($value, 'ProductAdjustmentDetail', 'serial_number');
                                    }

                                    echo $this->element('blocks/products/adjustment/tables/detail_items', array(
                                        'id' => $id,
                                        'modelName' => $modelName,
                                        'value' => $value,
                                        'qty' => $qty,
                                        'price' => $price,
                                        'key' => $key,
                                        'serial_number' => $serial_number,
                                    ));
                                }
                            }
                    ?>
                </tbody>
                <tfoot>
                    <tr class="grandtotal">
                        <?php
                                echo $this->Html->tag('td', __('Total'), array(
                                    'colspan' => 5,
                                    'class' => 'text-right',
                                ));
                                echo $this->Html->tag('td', $total_qty_adjustment, array(
                                    'class' => 'text-center total_custom',
                                    'rel' => 'qty',
                                ));
                                echo $this->Html->tag('td', '&nbsp;', array(
                                    'colspan' => 6,
                                ));
                        ?>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>