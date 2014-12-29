<?php 
        $this->Html->addCrumb(__('Truk'), array(
            'action' => 'index',
        ));
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/trucks/search_supir');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Supir', array(
                        'controller' => 'trucks',
                        'action' => 'driver_add'
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
                <th>Nama Supir</th>
                <th>Panggilan</th>
                <th>No. Identitas</th>
                <th>Alamat</th>
                <th>Telepon</th>
                <th>Dibuat</th>
                <th>Action</th>
            </tr>
            <?php
                $i = 1;
                if(!empty($truck_drivers)){
                    foreach ($truck_drivers as $key => $value) {
                        $value_data = $value['Driver'];
                        $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $value_data['name'];?></td>
                <td><?php echo !empty($value_data['alias'])?$value_data['alias']:'-';?></td>
                <td><?php echo $value_data['identity_number'];?></td>
                <td><?php echo $value_data['address'];?></td>
                <td>
                    <?php 
                        echo $value_data['phone'];
                        if(!empty($value_data['phone_2'])){
                            echo ' / '.$value_data['phone_2'];
                        }
                    ?>
                </td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'trucks',
                                'action' => 'driver_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link(__('Hapus'), array(
                                'controller' => 'trucks',
                                'action' => 'driver_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs',
                                'title' => 'disable status brand'
                            ), sprintf(__('Anda yakin ingin menghapus data supir %s?'), $value_data['name']));
                    ?>
                </td>
            </tr>
            <?php
                        }
                    } else {
                         echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '9'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>