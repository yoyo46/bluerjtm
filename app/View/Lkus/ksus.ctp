<?php 
        $dataColumns = array(
            'no_doc' => array(
                'name' => __('No. KSU'),
                'field_model' => 'Ksu.no_doc',
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
            'tgl_ksu' => array(
                'name' => __('Tgl KSU'),
                'field_model' => 'Ksu.tgl_ksu',
                'display' => true,
            ),
            'total_klaim' => array(
                'name' => __('Total Klaim'),
                'field_model' => 'Ksu.total_klaim',
                'display' => true,
            ),
            'total_price' => array(
                'name' => __('Pembayaran'),
                'field_model' => 'Ksu.total_price',
                'display' => true,
            ),
            'kekurangan_atpm' => array(
                'name' => __('Kekurangan MD?'),
                'field_model' => 'Ksu.kekurangan_atpm',
                'display' => true,
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => false,
                'display' => true,
            ),
            'created' => array(
                'name' => __('Dibuat'),
                'field_model' => 'Ksu.created',
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
        echo $this->element('blocks/lkus/search_ksu');
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
                        'action' => 'ksu_add'
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
                        if(!empty($Ksus)){
                            foreach ($Ksus as $key => $value) {
                                $id = $this->Common->filterEmptyField($value, 'Ksu', 'id');
                                $no_doc = $this->Common->filterEmptyField($value, 'Ksu', 'no_doc');
                                $tgl_ksu = $this->Common->filterEmptyField($value, 'Ksu', 'tgl_ksu');
                                $total_klaim = $this->Common->filterEmptyField($value, 'Ksu', 'total_klaim', '-');
                                $total_price = $this->Common->filterEmptyField($value, 'Ksu', 'total_price');
                                $status = $this->Common->filterEmptyField($value, 'Ksu', 'status');
                                $paid = $this->Common->filterEmptyField($value, 'Ksu', 'paid');
                                $complete_paid = $this->Common->filterEmptyField($value, 'Ksu', 'complete_paid');
                                $kekurangan_atpm = $this->Common->filterEmptyField($value, 'Ksu', 'kekurangan_atpm');
                                $created = $this->Common->filterEmptyField($value, 'Ksu', 'created');
                                $completed = $this->Common->filterEmptyField($value, 'Ksu', 'completed');

                                $no_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'no_ttuj');
                                $customer_name_code = $this->Common->filterEmptyField($value, 'Customer', 'customer_name_code');

                                $customTglKsu = $this->Common->customDate($tgl_ksu, 'd/m/Y');
                                $customCreated = $this->Common->customDate($created, 'd/m/Y');
                                $customTotalPrice = $this->Common->getCurrencyPrice($total_price);
                                $customAtpm = $this->Common->getCheckStatus($kekurangan_atpm);
                                $customStatus = $this->Lku->getCheckStatus($value, 'Ksu');
                ?>
                <tr>
                    <?php 
                            echo $this->Html->tag('td', $no_doc);
                            echo $this->Html->tag('td', $no_ttuj);
                            echo $this->Html->tag('td', $customer_name_code);
                            echo $this->Html->tag('td', $customTglKsu);
                            echo $this->Html->tag('td', $total_klaim, array(
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('td', $customTotalPrice, array(
                                'class' => 'text-right',
                            ));
                            echo $this->Html->tag('td', $customAtpm, array(
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('td', $customStatus);
                            echo $this->Html->tag('td', $customCreated);
                    ?>
                    <td class="action">
                        <?php
                            echo $this->Html->link('Info', array(
                                'controller' => 'lkus',
                                'action' => 'detail_ksu',
                                $id
                            ), array(
                                'class' => 'btn btn-info btn-xs'
                            ));

                            if( !empty($status) && empty($paid) && empty($completed) ){
                                echo $this->Html->link('Edit', array(
                                    'controller' => 'lkus',
                                    'action' => 'ksu_edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));

                                echo $this->Html->link(__('Void'), array(
                                    'controller' => 'lkus',
                                    'action' => 'ksu_toggle',
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