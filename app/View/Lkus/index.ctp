<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/lkus/search_index');
?>
<div class="box box-success">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', $sub_module_title, array(
                    'class' => 'box-title'
                ));

                if( in_array('insert_lkus', $allowModule) ) {
        ?>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah LKU', array(
                        'controller' => 'lkus',
                        'action' => 'add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app btn-success pull-right'
                    ));
            ?>
            <div class="clear"></div>
        </div>
        <?php 
                }
        ?>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <?php 
                        echo $this->Html->tag('th', $this->Paginator->sort('Lku.no_doc', __('No Lku'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('Lku.tgl_lku', __('Tgl Lku'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('Lku.status', __('Status'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('Lku.paid', __('Status Pembayaran'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('Lku.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'), array(
                            'escape' => false
                        ));
                ?>
            </tr>
            <?php
                    if(!empty($Lkus)){
                        foreach ($Lkus as $key => $value) {
                            $id = $value['Lku']['id'];
            ?>
            <tr>
                <td><?php echo $value['Lku']['no_doc'];?></td>
                <?php echo $this->Html->tag('td', date('d M Y', strtotime($value['Lku']['tgl_lku'])));?>
                <?php 
                        if(!empty($value['Lku']['status'])){
                            echo $this->Html->tag('td', '<span class="label label-success">Aktif</span>');
                        } else{
                            echo $this->Html->tag('td', '<span class="label label-danger">Non-aktif</span>');
                        }

                        if(!empty($value['Lku']['paid'])){
                            echo $this->Html->tag('td', '<span class="label label-success">Sudah di bayar</span>');
                        } else{
                            echo $this->Html->tag('td', '<span class="label label-danger">Belum di bayar</span>');
                        }
                ?>
                <td><?php echo $this->Common->customDate($value['Lku']['created']);?></td>
                <td class="action">
                    <?php
                        if( empty($value['Lku']['paid']) && !empty($value['Lku']['status']) ){
                            if( in_array('update_lkus', $allowModule) ) {
                                echo $this->Html->link('Rubah', array(
                                    'controller' => 'lkus',
                                    'action' => 'edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));
                            }

                            if( in_array('delete_lkus', $allowModule) ) {
                                echo $this->Html->link(__('Void'), array(
                                    'controller' => 'lkus',
                                    'action' => 'toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status brand'
                                ), __('Apakah Anda yakin akan mengbatalkan data ini?'));
                            }
                        }
                    ?>
                </td>
            </tr>
            <?php
                        }
                    }else{
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