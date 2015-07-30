<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/users/search_list_groups');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Posisi Karyawan', array(
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
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <th>Posisi</th>
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
                            echo $this->Html->link('Edit', array(
                                'controller' => 'users',
                                'action' => 'group_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link(__('Hapus'), array(
                                'controller' => 'users',
                                'action' => 'group_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs'
                            ), __('Anda yakin ingin menghapus posisi karyawan ini?'));

                            if( $id != 1 ) {
                                echo $this->Html->link('Otorisasi', array(
                                    'controller' => 'users',
                                    'action' => 'authorization_privilage',
                                    $id
                                ), array(
                                    'class' => 'btn btn-success btn-xs'
                                ));
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