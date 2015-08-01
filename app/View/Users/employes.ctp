<?php 
        $this->Html->addCrumb(__('Karyawan'));
        echo $this->element('blocks/users/search_employe');

        $dataColumns = array(
            'full_name' => array(
                'name' => __('Nama'),
                'field_model' => 'Employe.full_name',
                'display' => true,
            ),
            'group' => array(
                'name' => __('Posisi'),
                'field_model' => 'Group.name',
                'display' => true,
            ),
            'address' => array(
                'name' => __('Alamat'),
                'field_model' => 'Employe.address',
                'display' => true,
            ),
            'phone' => array(
                'name' => __('Telepon'),
                'field_model' => 'Employe.phone',
                'display' => true,
            ),
            'created' => array(
                'name' => __('Dibuat'),
                'field_model' => 'Employe.created',
                'display' => true,
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => 'Employe.status',
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
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Karyawan', array(
                        'controller' => 'users',
                        'action' => 'employe_add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app pull-right'
                    ));

                    // echo $this->Html->link('<i class="fa fa-user"></i> Posisi Karyawan', array(
                    //     'controller' => 'users',
                    //     'action' => 'employe_positions'
                    // ), array(
                    //     'escape' => false,
                    //     'class' => 'btn btn-app pull-right'
                    // ));
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
            <?php
                    if(!empty($employes)){
                        foreach ($employes as $key => $value) {
                            $value_data = $value['Employe'];
                            $id = $value_data['id'];
                            $group_name = !empty($value['Group']['name'])?$value['Group']['name']:false;
                            $full_name = !empty($value_data['full_name'])?$value_data['full_name']:false;
                            $activate = array(
                                'controller' => 'users',
                                'action' => 'employe_toggle',
                                $id
                            );
            ?>
            <tr>
                <td><?php echo $full_name;?></td>
                <td><?php echo $group_name;?></td>
                <td><?php echo $value_data['address'];?></td>
                <td><?php echo $value_data['phone'];?></td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td>
                    <?php 
                            if(!empty($value_data['status'])){
                                echo $this->Html->link($this->Common->icon('check'), $activate, array(
                                    'escape' => false,
                                    'class' => 'btn btn-success btn-xs',
                                    'title' => 'enable status user'
                                ), sprintf(__('Apakah Anda yakin akan mengaktifkan %s?'), $full_name));
                            }else{
                                echo $this->Html->link($this->Common->icon('times'), $activate, array(
                                    'escape' => false,
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status user'
                                ), sprintf(__('Apakah Anda yakin akan menon-aktifkan %s?'), $full_name));
                            }
                    ?>
                </td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'users',
                                'action' => 'employe_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            if(!empty($value_data['status'])){
                                echo $this->Html->link(__('Non-Aktif'), $activate, array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status user'
                                ), sprintf(__('Apakah Anda yakin akan menon-aktifkan %s?'), $full_name));
                            }else{
                                echo $this->Html->link('Aktifkan', $activate, array(
                                    'class' => 'btn btn-success btn-xs',
                                    'title' => 'enable status user'
                                ), sprintf(__('Apakah Anda yakin akan mengaktifkan %s?'), $full_name));
                            }
                    ?>
                </td>
            </tr>
            <?php
                        }
                    } else {
                         echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '8'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>