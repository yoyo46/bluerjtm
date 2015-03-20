<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/settings/search_tipe_motor');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Tipe Motor', array(
                        'controller' => 'settings',
                        'action' => 'type_motor_add'
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
                <th>Kode Motor</th>
                <th>Tipe Motor</th>
                <th>Group Motor</th>
                <th>Dibuat</th>
                <th>Action</th>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($type_motors)){
                        foreach ($type_motors as $key => $value) {
                            $value_data = $value['TipeMotor'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $value_data['code_motor'];?></td>
                <td><?php echo $value_data['name'];?></td>
                <td><?php echo $value['GroupMotor']['name'];?></td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'settings',
                                'action' => 'type_motor_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link('Hapus', array(
                                'controller' => 'settings',
                                'action' => 'type_motor_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs',
                                'title' => 'disable status brand'
                            ), sprintf(__('Apakah Anda yakin akan menghapus data Tipe Motor %s?'), $value_data['name']));
                    ?>
                </td>
            </tr>
            <?php
                        }
                    } else {
                         echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '6'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>