<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/users/search_list_users');

        $dataColumns = array(
            'full_name' => array(
                'name' => __('Nama'),
                'field_model' => 'Employe.full_name',
            ),
            'branch' => array(
                'name' => __('Cabang'),
            ),
            'group' => array(
                'name' => __('Grup User'),
                'field_model' => 'Group.name',
            ),
            'email' => array(
                'name' => __('Email'),
                'field_model' => 'User.email',
            ),
            'phone' => array(
                'name' => __('Telepon'),
                'field_model' => 'Employe.phone',
            ),
            'last_login' => array(
                'name' => __('Terakhir Login'),
                'field_model' => 'User.last_login',
            ),
            'modified' => array(
                'name' => __('Dirubah'),
                'field_model' => 'User.modified',
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => 'User.status',
            ),
            'action' => array(
                'name' => __('Action'),
                'width' => '15%',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah User', array(
                        'controller' => 'users',
                        'action' => 'add'
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
            <?php
                    if(!empty($list_user)){
                        foreach ($list_user as $key => $value) {
                            $value_data = $value['User'];
                            $id = $value_data['id'];

                            $name = $this->Common->filterEmptyField($value, 'Employe', 'full_name');
                            $phone = $this->Common->filterEmptyField($value, 'Employe', 'phone');
                            $group_name = $this->Common->filterEmptyField($value, 'Group', 'name');
                            $last_login = $this->Common->filterEmptyField($value, 'User', 'last_login');

                            $activate = array(
                                'controller' => 'users',
                                'action' => 'toggle',
                                $id
                            );

                            $last_login = $this->Common->formatDate($last_login);
            ?>
            <tr>
                <td><?php echo $name;?></td>
                <td><?php echo !empty($value['Branch']['name'])?$value['Branch']['name']:false;?></td>
                <td><?php echo $group_name;?></td>
                <td><?php echo $value_data['email'];?></td>
                <td><?php echo $phone;?></td>
                <td class="text-center"><?php echo $last_login;?></td>
                <td><?php echo $this->Common->customDate($value_data['modified'], 'd M Y');?></td>
                <td>
                    <?php 
                            if(!empty($value_data['status'])){
                                echo $this->Html->link($this->Common->icon('check'), $activate, array(
                                    'escape' => false,
                                    'class' => 'btn btn-success btn-xs',
                                    'title' => 'enable status user'
                                ), sprintf(__('Apakah Anda yakin akan mengaktifkan %s?'), $name));
                            }else{
                                echo $this->Html->link($this->Common->icon('times'), $activate, array(
                                    'escape' => false,
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status user'
                                ), sprintf(__('Apakah Anda yakin akan menon-aktifkan %s?'), $name));
                            }
                    ?>
                </td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'users',
                                'action' => 'edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            if(!empty($value_data['status'])){
                                echo $this->Html->link(__('Non-Aktif'), $activate, array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status user'
                                ), sprintf(__('Apakah Anda yakin akan menon-aktifkan %s?'), $name));
                            }else{
                                echo $this->Html->link('Aktifkan', $activate, array(
                                    'class' => 'btn btn-success btn-xs',
                                    'title' => 'enable status user'
                                ), sprintf(__('Apakah Anda yakin akan mengaktifkan %s?'), $name));
                            }
                            
                            echo $this->Html->link(__('Ganti Password'), array(
                                'controller' => 'users',
                                'action' => 'password',
                                $id
                            ), array(
                                'class' => 'btn btn-warning btn-xs'
                            ));
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