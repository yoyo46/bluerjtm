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
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Karyawan', array(
                        'controller' => 'users',
                        'action' => 'employe_add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app pull-right'
                    ));

                    echo $this->Html->link('<i class="fa fa-user"></i> Posisi Karyawan', array(
                        'controller' => 'users',
                        'action' => 'employe_positions'
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
                <th>No.</th>
                <?php
                    echo $this->Html->tag('th', $this->Paginator->sort('Employe.name', __('Karyawan'), array(
                        'escape' => false
                    )));
                    echo $this->Html->tag('th', $this->Paginator->sort('EmployePosition.name', __('Posisi'), array(
                        'escape' => false
                    )));
                    echo $this->Html->tag('th', $this->Paginator->sort('EmployePosition.code', __('Kode'), array(
                        'escape' => false
                    )));
                    echo $this->Html->tag('th', $this->Paginator->sort('Employe.address', __('Alamat'), array(
                        'escape' => false
                    )));
                    echo $this->Html->tag('th', $this->Paginator->sort('Employe.phone', __('No. Telp'), array(
                        'escape' => false
                    )));
                    echo $this->Html->tag('th', $this->Paginator->sort('Employe.created', __('Dibuat'), array(
                        'escape' => false
                    )));
                ?>
                <th>Action</th>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($employes)){
                        foreach ($employes as $key => $value) {
                            $value_data = $value['Employe'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $i++;?></td>
                <td><?php echo $value_data['name'];?></td>
                <td><?php echo $value['EmployePosition']['name'];?></td>
                <td><?php echo $value['EmployePosition']['code'];?></td>
                <td><?php echo $value_data['address'];?></td>
                <td><?php echo $value_data['phone'];?></td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'users',
                                'action' => 'employe_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link('Hapus', array(
                                'controller' => 'users',
                                'action' => 'employe_toggle',
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
                            'colspan' => '8'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>