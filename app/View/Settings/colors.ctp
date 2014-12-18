<?php 
        $this->Html->addCrumb(__('Tipe Motor'), array(
            'action' => 'type_motors',
        ));
        $this->Html->addCrumb($sub_module_title);
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Warna Motor', array(
                        'controller' => 'settings',
                        'action' => 'color_motor_add'
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
                <th>Warna Motor</th>
                <th>Dibuat</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($colors)){
                        foreach ($colors as $key => $value) {
                            $value_data = $value['ColorMotor'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $i++;?></td>
                <td><?php echo $value_data['name'];?></td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td>
                    <?php 
                        if(!empty($value_data['status'])){
                            echo '<span class="label label-success">Active</span>'; 
                        }else{
                            echo '<span class="label label-danger">Non Active</span>';  
                        }
                        
                    ?>
                </td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'settings',
                                'action' => 'color_motor_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            if(!empty($value_data['status'])){
                                echo $this->Html->link('Disable', array(
                                    'controller' => 'settings',
                                    'action' => 'color_motor_toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status brand'
                                ), sprintf(__('Apakah Anda yakin akan menon-aktifkan %s?'), $value_data['name']));
                            }else{
                                echo $this->Html->link('Enable', array(
                                    'controller' => 'settings',
                                    'action' => 'color_motor_toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-success btn-xs',
                                    'title' => 'enable status brand'
                                ), sprintf(__('Apakah Anda yakin akan mengaktifkan %s?'), $value_data['name']));
                            }
                    ?>
                </td>
            </tr>
            <?php
                        }
                    } else {
                         echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '5'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>