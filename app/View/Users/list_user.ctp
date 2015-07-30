<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/users/search_list_users');
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
        <table class="table table-hover">
            <tr>
                <th>Nama</th>
                <th>Cabang</th>
                <th>Group</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Status</th>
                <th>Terakhir dirubah</th>
                <th>Action</th>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($list_user)){
                        foreach ($list_user as $key => $value) {
                            $value_data = $value['User'];
                            $id = $value_data['id'];

                            $name = !empty($value['Employe']['full_name'])?$value['Employe']['full_name']:false;
                            $phone = !empty($value['Employe']['phone'])?$value['Employe']['phone']:false;
                            $group_name = !empty($value['Group']['name'])?$value['Group']['name']:false;
            ?>
            <tr>
                <td><?php echo $name;?></td>
                <td><?php echo !empty($value['City']['name'])?$value['City']['name']:false;?></td>
                <td><?php echo $group_name;?></td>
                <td><?php echo $value_data['email'];?></td>
                <td><?php echo $phone;?></td>
                <td>
                    <?php 
                        if(!empty($value_data['status'])){
                            echo '<span class="label label-success">Active</span>'; 
                        }else{
                            echo '<span class="label label-danger">Non Active</span>';  
                        }
                        
                    ?>
                </td>
                <td><?php echo $this->Common->customDate($value_data['modified']);?></td>
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
                                echo $this->Html->link('Disable', array(
                                    'controller' => 'users',
                                    'action' => 'toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status user'
                                ), sprintf(__('Apakah Anda yakin akan menon-aktifkan %s?'), $name));
                            }else{
                                echo $this->Html->link('Enable', array(
                                    'controller' => 'users',
                                    'action' => 'toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-success btn-xs',
                                    'title' => 'enable status user'
                                ), sprintf(__('Apakah Anda yakin akan mengaktifkan %s?'), $name));
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
</div>