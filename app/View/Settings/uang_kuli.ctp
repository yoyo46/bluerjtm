<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/settings/search_uang_kulis');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <?php 
                // if( in_array('insert_uang_kuli', $allowModule) ) {
        ?>
        <div class="box-tools">
            <?php
                    echo $this->Common->rule_link('<i class="fa fa-plus"></i> Tambah Uang Kuli', array(
                        'controller' => 'settings',
                        'action' => 'uang_kuli_add',
                        $data_action,
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app pull-right'
                    ));
            ?>
        </div>
        <?php 
                // }
        ?>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <?php
                        echo $this->Html->tag('th', $this->Paginator->sort('UangKuli.title', __('Nama'), array(
                            'escape' => false
                        )));

                        if( $data_action == 'bongkar' ) {
                            echo $this->Html->tag('th', $this->Paginator->sort('Customer.name', __('Customer'), array(
                                'escape' => false
                            )));
                        }

                        echo $this->Html->tag('th', $this->Paginator->sort('City.name', __('Kota Asal'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('UangKuli.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($uangKulis)){
                        foreach ($uangKulis as $key => $value) {
                            $id = $value['UangKuli']['id'];
            ?>
            <tr>
                <td><?php echo $value['UangKuli']['title'];?></td>
                <?php 
                        if( $data_action == 'bongkar' ) {
                            echo $this->Html->tag('td', !empty($value['Customer']['customer_name'])?$value['Customer']['customer_name']:false);
                        }
                ?>
                <td><?php echo $value['City']['name'];?></td>
                <td><?php echo $this->Common->customDate($value['UangKuli']['created']);?></td>
                <td class="action">
                    <?php 
                            // if( in_array('update_uang_kuli', $allowModule) ) {
                                echo $this->Common->rule_link('Edit', array(
                                    'controller' => 'settings',
                                    'action' => 'uang_kuli_edit',
                                    $data_action,
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));
                            // }

                            // if( in_array('delete_uang_kuli', $allowModule) ) {
                                echo $this->Common->rule_link(__('Hapus'), array(
                                    'controller' => 'settings',
                                    'action' => 'uang_kuli_toggle',
                                    $data_action,
                                    $id,
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status brand'
                                ), __('Anda yakin ingin menghapus data Uang Kuli ini?'));
                            // }
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
    </div>
    <?php echo $this->element('pagination');?>
</div>