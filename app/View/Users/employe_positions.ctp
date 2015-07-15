<?php 
        $this->Html->addCrumb(__('Karyawan'), array(
            'action' => 'employes',
        ));
        $this->Html->addCrumb($sub_module_title);
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Posisi Karyawan', array(
                        'controller' => 'users',
                        'action' => 'employe_position_add'
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
                <th>Posisi Karyawan</th>
                <th>Kode</th>
                <th>Dibuat</th>
                <th>Action</th>
            </tr>
            <?php
                    if(!empty($employe_positions)){
                        foreach ($employe_positions as $key => $value) {
                            $value_data = $value['EmployePosition'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $value_data['name'];?></td>
                <td><?php echo $value_data['code'];?></td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'users',
                                'action' => 'employe_position_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link('Hapus', array(
                                'controller' => 'users',
                                'action' => 'employe_position_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs',
                                'title' => 'disable status brand'
                            ), sprintf(__('Apakah Anda yakin akan menon-aktifkan %s?'), $value_data['name']));
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