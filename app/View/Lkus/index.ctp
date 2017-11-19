<?php 
        $dataColumns = array(
            'no_doc' => array(
                'name' => __('No. LKU'),
                'field_model' => 'Lku.no_doc',
                'display' => true,
            ),
            'nopol' => array(
                'name' => __('Nopol'),
                'field_model' => false,
                'display' => true,
            ),
            'no_ttuj' => array(
                'name' => __('No. TTUJ'),
                'field_model' => false,
                'display' => true,
            ),
            'customer' => array(
                'name' => __('Customer'),
                'field_model' => false,
                'display' => true,
                'style' => 'width:20%;'
            ),
            'tgl_lku' => array(
                'name' => __('Tgl LKU'),
                'field_model' => 'Lku.tgl_lku',
                'display' => true,
            ),
            'total_klaim' => array(
                'name' => __('Total Klaim'),
                'field_model' => 'Lku.total_klaim',
                'display' => true,
            ),
            'total_price' => array(
                'name' => __('Pembayaran'),
                'field_model' => 'Lku.total_price',
                'display' => true,
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => false,
                'display' => true,
            ),
            'created' => array(
                'name' => __('Dibuat'),
                'field_model' => 'Lku.created',
                'display' => true,
            ),
            'action' => array(
                'name' => __('Action'),
                'field_model' => false,
                'display' => true,
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/lkus/search_index');
?>
<div class="box box-success">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', $sub_module_title, array(
                    'class' => 'box-title'
                ));
        ?>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                        'controller' => 'lkus',
                        'action' => 'add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app btn-success pull-right'
                    ));
            ?>
            <div class="clear"></div>
        </div>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover sorting red">
            <thead>
                <tr>
                    <?php 
                            echo $fieldColumn;
                    ?>
                </tr>
            </thead>
            <tbody>
                <?php
                        if(!empty($Lkus)){
                            foreach ($Lkus as $key => $value) {
                                $id = $this->Common->filterEmptyField($value, 'Lku', 'id');
                                $no_doc = $this->Common->filterEmptyField($value, 'Lku', 'no_doc');
                                $tgl_lku = $this->Common->filterEmptyField($value, 'Lku', 'tgl_lku');
                                $total_klaim = $this->Common->filterEmptyField($value, 'Lku', 'total_klaim');
                                $total_price = $this->Common->filterEmptyField($value, 'Lku', 'total_price');
                                $status = $this->Common->filterEmptyField($value, 'Lku', 'status');
                                $paid = $this->Common->filterEmptyField($value, 'Lku', 'paid');
                                $complete_paid = $this->Common->filterEmptyField($value, 'Lku', 'complete_paid');
                                $created = $this->Common->filterEmptyField($value, 'Lku', 'created');
                                $completed = $this->Common->filterEmptyField($value, 'Lku', 'completed');

                                $nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol');
                                $no_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'no_ttuj');
                                $customer_name_code = $this->Common->filterEmptyField($value, 'Customer', 'customer_name_code');

                                $customTglLku = $this->Common->customDate($tgl_lku, 'd/m/Y');
                                $customCreated = $this->Common->customDate($created, 'd/m/Y');
                                $customTotalPrice = $this->Common->getCurrencyPrice($total_price);
                                $customStatus = $this->Lku->getCheckStatus($value, 'Lku');
                ?>
                <tr>
                    <?php 
                            echo $this->Html->tag('td', $no_doc);
                            echo $this->Html->tag('td', $nopol);
                            echo $this->Html->tag('td', $no_ttuj);
                            echo $this->Html->tag('td', $customer_name_code);
                            echo $this->Html->tag('td', $customTglLku);
                            echo $this->Html->tag('td', $total_klaim, array(
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('td', $customTotalPrice, array(
                                'class' => 'text-right',
                            ));
                            echo $this->Html->tag('td', $customStatus);
                            echo $this->Html->tag('td', $customCreated);
                    ?>
                    <td class="action">
                        <?php
                                echo $this->Html->link('Info', array(
                                    'controller' => 'lkus',
                                    'action' => 'detail',
                                    $id
                                ), array(
                                    'class' => 'btn btn-info btn-xs'
                                ));

                                if( !empty($status) && empty($paid) && empty($completed) ){
                                    echo $this->Html->link('Edit', array(
                                        'controller' => 'lkus',
                                        'action' => 'edit',
                                        $id
                                    ), array(
                                        'class' => 'btn btn-primary btn-xs'
                                    ));

                                    echo $this->Html->link(__('Void'), array(
                                        'controller' => 'lkus',
                                        'action' => 'toggle',
                                        $id
                                    ), array(
                                        'class' => 'btn btn-danger btn-xs trigger-disabled',
                                        'title' => 'disable status brand',
                                        'data-alert' => __('Apakah Anda yakin akan mengbatalkan data ini?'),
                                    ));
                                }
                        ?>
                    </td>
                </tr>
                <?php
                            }
                        }else{
                            echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                                'class' => 'alert alert-warning text-center',
                                'colspan' => '10'
                            )));
                        }
                ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>