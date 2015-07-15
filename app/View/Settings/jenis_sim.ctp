<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/settings/search_jenis_sim');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <?php 
                if( in_array('insert_jenis_sim', $allowModule) ) {
        ?>
        <div class="box-tools">
            <?php
                echo $this->Common->rule_link('<i class="fa fa-plus"></i> Tambah Jenis SIM', array(
                    'controller' => 'settings',
                    'action' => 'sim_add'
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
                <th>id</th>
                <th>Jenis SIM</th>
                <th>Dibuat</th>
                <th>Action</th>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($jenis_sim)){
                        foreach ($jenis_sim as $key => $value) {
                            $value_data = $value['JenisSim'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $id;?></td>
                <td><?php echo $value_data['name'];?></td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            if( in_array('update_jenis_sim', $allowModule) ) {
                                echo $this->Common->rule_link('Edit', array(
                                    'controller' => 'settings',
                                    'action' => 'sim_edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));
                            }

                            if( in_array('delete_jenis_sim', $allowModule) ) {
                                echo $this->Common->rule_link(__('Hapus'), array(
                                    'controller' => 'settings',
                                    'action' => 'sim_toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status brand'
                                ), __('Anda yakin ingin menghapus data Kota ini?'));
                            }
                    ?>
                </td>
            </tr>
            <?php
                        }
                    } else {
                         echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '3'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>