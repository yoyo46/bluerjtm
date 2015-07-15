<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/users/search_list_groups');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <?php 
                if( in_array('insert_group_user', $allowModule) ) {
        ?>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Group', array(
                        'controller' => 'users',
                        'action' => 'group_add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app pull-right'
                    ));

                    echo $this->Html->link('<i class="fa fa-archive"></i> Action Modules', array(
                        'controller' => 'users',
                        'action' => 'action_modules'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app pull-right'
                    ));
            ?>
        </div>
        <?php 
                }
        ?>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <th>Group</th>
                <th>Dibuat</th>
                <th>Action</th>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($groups)){
                        foreach ($groups as $key => $value) {
                            $value_data = $value['Group'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $value_data['name'];?></td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            if( in_array('update_group_user', $allowModule) ) {
                                echo $this->Html->link('Edit', array(
                                    'controller' => 'users',
                                    'action' => 'group_edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));

                                echo $this->Html->link('Otorisasi', array(
                                    'controller' => 'users',
                                    'action' => 'authorization_privilage',
                                    $id
                                ), array(
                                    'class' => 'btn btn-warning btn-xs'
                                ));
                            }

                            if( in_array('delete_group_user', $allowModule) ) {
                                if(!empty($value_data['status'])){
                                    echo $this->Html->link('Disable', array(
                                        'controller' => 'users',
                                        'action' => 'group_toggle',
                                        $id
                                    ), array(
                                        'class' => 'btn btn-danger btn-xs',
                                    ), sprintf(__('Apakah Anda yakin akan menon-aktifkan %s?'), $value_data['name']));
                                }else{
                                    echo $this->Html->link('Enable', array(
                                        'controller' => 'users',
                                        'action' => 'group_toggle',
                                        $id
                                    ), array(
                                        'class' => 'btn btn-success btn-xs',
                                    ), sprintf(__('Apakah Anda yakin akan mengaktifkan %s?'), $value_data['name']));
                                }
                            }
                    ?>
                </td>
            </tr>
            <?php
                        }
                    } else {
                         echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '4'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
</div>