<?php 
        $dataColumns = array(
            'branch' => array(
                'name' => __('Cabang'),
                'field_model' => false,
                'display' => true,
            ),
            'code' => array(
                'name' => __('Kode'),
                'field_model' => 'Customer.code',
                'display' => true,
            ),
            'type' => array(
                'name' => __('Tipe'),
                'field_model' => 'CustomerType.name',
                'display' => true,
            ),
            'group' => array(
                'name' => __('Grup'),
                'field_model' => 'CustomerGroup.name',
                'display' => true,
            ),
            'name' => array(
                'name' => __('Nama'),
                'field_model' => 'Customer.name',
                'display' => true,
                'style' => 'width: 15%;'
            ),
            'address' => array(
                'name' => __('Alamat'),
                'field_model' => 'Customer.address',
                'display' => true,
                'style' => 'width: 20%;'
            ),
            'phone_number' => array(
                'name' => __('Telepon'),
                'field_model' => 'Customer.phone_number',
                'display' => true,
            ),
            'created' => array(
                'name' => __('Dibuat'),
                'field_model' => 'Customer.created',
                'display' => true,
            ),
            'order' => array(
                'name' => __('Order'),
                'field_model' => 'Customer.order',
                'display' => true,
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => 'Customer.status',
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
        echo $this->element('blocks/settings/search_customers');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                        'controller' => 'settings',
                        'action' => 'customer_add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app pull-right'
                    ));
            ?>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-hover sorting">
            <thead>
                <tr>
                    <?php
                            if( !empty($fieldColumn) ) {
                                echo $fieldColumn;
                            }
                    ?>
                </tr>
            </thead>
            <tbody>
            <?php
                    if(!empty($truck_customers)){
                        foreach ($truck_customers as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'Customer', 'id');
                            $code = $this->Common->filterEmptyField($value, 'Customer', 'code');
                            $type = $this->Common->filterEmptyField($value, 'CustomerType', 'name', '-');
                            $group = $this->Common->filterEmptyField($value, 'CustomerGroup', 'name');
                            $name = $this->Common->filterEmptyField($value, 'Customer', 'name');
                            $address = $this->Common->filterEmptyField($value, 'Customer', 'address');
                            $phone_number = $this->Common->filterEmptyField($value, 'Customer', 'phone_number');
                            $order = $this->Common->filterEmptyField($value, 'Customer', 'order');
                            $created = $this->Common->filterEmptyField($value, 'Customer', 'created');
                            $branch_id = $this->Common->filterEmptyField($value, 'Customer', 'branch_id');
                            $status = $this->Common->filterEmptyField($value, 'Customer', 'status');
                            $branch = $this->Common->filterEmptyField($value, 'Branch', 'code');
                            $activate = array(
                                'controller' => 'settings',
                                'action' => 'customer_toggle',
                                $id
                            );
            ?>
                <tr>
                    <td><?php echo $branch;?></td>
                    <td><?php echo $code;?></td>
                    <td><?php echo $type;?></td>
                    <td><?php echo $group;?></td>
                    <td><?php echo $name;?></td>
                    <td><?php echo $address;?></td>
                    <td>
                        <?php 
                            echo $phone_number;
                        ?>
                    </td>
                    <td><?php echo $this->Common->customDate($created, 'd M Y');?></td>
                    <td><?php echo $order;?></td>
                    <td class="text-center">
                        <?php 
                                if(!empty($status)){
                                    echo $this->Html->tag('div', $this->Common->icon('check'), array(
                                        'class' => 'btn btn-success btn-xs',
                                    ));
                                }else{
                                    echo $this->Html->tag('div', $this->Common->icon('times'), array(
                                        'class' => 'btn btn-danger btn-xs',
                                    ));
                                }
                        ?>
                    </td>
                    <td class="action">
                        <?php 
                                echo $this->Html->link('Edit', array(
                                    'controller' => 'settings',
                                    'action' => 'customer_edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs',
                                    // 'branch_id' => $branch_id,
                                ));

                                // Custom Otorisasi
                                if(!empty($status)){
                                    echo $this->Html->link(__('Non-Aktif'), $activate, array(
                                        'class' => 'btn btn-danger btn-xs',
                                        // 'branch_id' => $branch_id,
                                    ), __('Apakah Anda yakin akan non-aktifkan data ini?'));
                                }else{
                                    echo $this->Html->link(__('Aktifkan'), $activate, array(
                                        'class' => 'btn btn-success btn-xs',
                                        // 'branch_id' => $branch_id,
                                    ), __('Apakah Anda yakin akan aktifkan data ini?'));
                                }
                        ?>
                    </td>
                </tr>
            <?php
                        }
                    } else {
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '11'
                        )));
                    }
            ?>
            </tbody>
        </table>
    </div>
    <?php echo $this->element('pagination');?>
</div>