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
                    echo $this->Common->rule_link('<i class="fa fa-plus"></i> Tambah Grup Motor', array(
                        'controller' => 'settings',
                        'action' => 'group_motor_add'
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
                <th>Grup Motor</th>
                <th>Dibuat</th>
                <th>Action</th>
            </tr>
            <?php
                    if(!empty($group_motors)){
                        foreach ($group_motors as $key => $value) {
                            $value_data = $value['GroupMotor'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $value_data['name'];?></td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Common->rule_link('Edit', array(
                                'controller' => 'settings',
                                'action' => 'group_motor_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Common->rule_link('Hapus', array(
                                'controller' => 'settings',
                                'action' => 'group_motor_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs',
                                'title' => 'disable status brand'
                            ), sprintf(__('Apakah Anda yakin akan menghapus data Grup Motor %s?'), $value_data['name']));
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