<?php 
        $this->Html->addCrumb($sub_module_title);
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
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <tr>
                <th>No.</th>
                <th>Nama</th>
                <th>Group</th>
                <th>Email</th>
                <th>Telepon</th>
                <th>Jenis Kelamin</th>
                <th>Tgl Lahir</th>
                <th>Status</th>
                <th>terakhir dirubah</th>
                <th>Action</th>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($list_user)){
                        foreach ($list_user as $key => $value) {
                            $value_data = $value['User'];
                            $id = $value_data['id'];

                            $name = $value_data['first_name'].(!empty($value_data['last_name'])) ? ' '.$value_data['last_name'] : '';
            ?>
            <tr>
                <td><?php echo $i++;?></td>
                <td><?php echo $name;?></td>
                <td><?php echo $value['Group']['name'];?></td>
                <td><?php echo $value_data['email'];?></td>
                <td><?php echo $value_data['phone'];;?></td>
                <td>
                    <?php 
                        $gend = 'Pria';
                        if($value_data['gender'] == 'female'){
                            $gend = 'Wanita';
                        }
                        echo $gend;
                    ?>
                </td>
                <td><?php echo $this->Common->customDate($value_data['birthdate']);?></td>
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