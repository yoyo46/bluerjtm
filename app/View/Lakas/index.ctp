<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/lakas/search_index');
?>
<div class="box box-success">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', $sub_module_title, array(
                    'class' => 'box-title'
                ));

                if( in_array('insert_lakas', $allowModule) ) {
        ?>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah LAKA', array(
                        'controller' => 'lakas',
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
                        echo $this->Html->tag('th', $this->Paginator->sort('Laka.no_doc', __('No LAKA'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('Laka.tgl_laka', __('Tgl LAKA'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('Laka.status', __('Status'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('Laka.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'), array(
                            'escape' => false
                        ));
                ?>
            </tr>
            <?php
                    if(!empty($Lakas)){
                        foreach ($Lakas as $key => $value) {
                            $id = $value['Laka']['id'];
            ?>
            <tr>
                <td><?php echo $value['Laka']['nopol'];?></td>
                <?php echo $this->Html->tag('td', date('d M Y', strtotime($value['Laka']['tgl_laka'])));?>
                <?php 
                        if(!empty($value['Laka']['status'])){
                            echo $this->Html->tag('td', '<span class="label label-success">Aktif</span>');
                        } else{
                            echo $this->Html->tag('td', '<span class="label label-danger">Non-aktif</span>');
                        }
                ?>
                <td><?php echo $this->Common->customDate($value['Laka']['created']);?></td>
                <td class="action">
                    <?php
                            if( in_array('update_lakas', $allowModule) ) {
                                echo $this->Html->link('Rubah', array(
                                    'controller' => 'lakas',
                                    'action' => 'edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));
                            }

                            if( in_array('delete_lakas', $allowModule) ) {
                                echo $this->Html->link(__('Hapus'), array(
                                    'controller' => 'lakas',
                                    'action' => 'toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status brand'
                                ), __('Apakah Anda yakin akan menghapus data ini?'));
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