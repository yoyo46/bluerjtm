<?php 
        $this->Html->addCrumb(__('Truk'), array(
            'action' => 'index',
        ));
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/trucks/search_supir');

        $dataColumns = array(
            'cabang' => array(
                'name' => __('Cabang'),
                'field_model' => 'Branch.name',
                'display' => true,
            ),
            'no_id' => array(
                'name' => __('No. ID'),
                'field_model' => 'Driver.no_id',
                'display' => true,
            ),
            'name' => array(
                'name' => __('Nama'),
                'field_model' => 'Driver.name',
                'display' => true,
            ),
            'alias' => array(
                'name' => __('alias'),
                'field_model' => 'Driver.alias',
                'display' => true,
            ),
            'identity_number' => array(
                'name' => __('No. Identitas'),
                'field_model' => 'Driver.identity_number',
                'display' => true,
            ),
            'Address' => array(
                'name' => __('Alamat'),
                'field_model' => 'Driver.Address',
                'display' => true,
            ),
            'phone' => array(
                'name' => __('Telepon'),
                'field_model' => 'Driver.phone',
                'display' => true,
            ),
            'created' => array(
                'name' => __('Dibuat'),
                'field_model' => 'Driver.created',
                'display' => true,
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => 'Driver.status',
                'display' => true,
            ),
            'action' => array(
                'name' => __('Action'),
                'field_model' => false,
                'display' => true,
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <div class="btn-group pull-right">
                <?php 
                        echo $this->Html->tag('button', '<i class="fa fa-plus"></i> Tambah', array(
                            'data-toggle' => 'dropdown',
                            'class' => 'btn btn-app btn-success dropdown-toggle'
                        ));
                ?>
                <ul class="dropdown-menu" role="menu">
                    <?php 
                            echo $this->Html->tag('li', $this->Html->link(__('Supir'), array(
                                'controller' => 'trucks',
                                'action' => 'driver_add'
                            ), array(
                                'escape' => false,
                            )));
                            echo $this->Html->tag('li', '', array(
                                'class' => 'divider',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('Import Excel'), array(
                                'controller' => 'trucks',
                                'action' => 'add_driver_import',
                            ), array(
                                'escape' => false,
                            )));
                    ?>
                </ul>
            </div>
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
            <?php
                    if( !empty($truck_drivers) ){
                        foreach ($truck_drivers as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'Driver', 'id');
                            $no_id = $this->Common->filterEmptyField($value, 'Driver', 'no_id');
                            $name = $this->Common->filterEmptyField($value, 'Driver', 'name');
                            $alias = $this->Common->filterEmptyField($value, 'Driver', 'alias', '-');
                            $identity_number = $this->Common->filterEmptyField($value, 'Driver', 'identity_number');
                            $address = $this->Common->filterEmptyField($value, 'Driver', 'address');
                            $phone = $this->Common->filterEmptyField($value, 'Driver', 'phone');
                            $status = $this->Common->filterEmptyField($value, 'Driver', 'status');
                            $created = $this->Common->filterEmptyField($value, 'Driver', 'created');
                            $is_resign = $this->Common->filterEmptyField($value, 'Driver', 'is_resign');
                            $branch = $this->Common->filterEmptyField($value, 'Branch', 'code');
                            $branch_id = $this->Common->filterEmptyField($value, 'Driver', 'branch_id');
                            $activate = array(
                                'controller' => 'trucks',
                                'action' => 'driver_toggle',
                                $id
                            );
            ?>
            <tr>
                <td><?php echo $branch;?></td>
                <td><?php echo $no_id;?></td>
                <td><?php echo $name;?></td>
                <td><?php echo $alias;?></td>
                <td><?php echo $identity_number;?></td>
                <td><?php echo $address;?></td>
                <td><?php echo $phone;?></td>
                <td><?php echo $this->Time->niceShort($created);?></td>
                <td class="text-center">
                    <?php 
                            if(!empty($status)){
                                echo $this->Html->tag('div', $this->Common->icon('check'), array(
                                    'class' => 'btn btn-success btn-xs',
                                ));
                            }else{
                                if(!empty($is_resign)){
                                    echo $this->Html->tag('span', __('Resign'), array(
                                        'class' => 'label label-danger',
                                    ));;
                                }else{
                                    echo $this->Html->tag('div', $this->Common->icon('times'), array(
                                        'class' => 'btn btn-danger btn-xs',
                                    )); 
                                }
                            }
                    ?>
                </td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'trucks',
                                'action' => 'driver_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs',
                                // 'branch_id' => $branch_id,
                            ));

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
                            'colspan' => '10'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>