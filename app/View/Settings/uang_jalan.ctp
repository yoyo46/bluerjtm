<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/settings/search_uang_jalans');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <div class="btn-group pull-right">
                <?php 
                        echo $this->Html->tag('button', '<i class="fa fa-plus"></i> Tambah', array(
                            'data-toggle' => 'dropdown',
                            'class' => 'btn btn-app btn-success dropdown-toggle'
                        ));
                        echo $this->Html->link($this->Common->icon('file-o').__(' Download Excel'), array(
                            'controller' => 'settings',
                            'action' => 'pick_uang_jalan',
                            'admin' => false,
                        ), array(
                            'escape' => false,
                            'class' => 'btn btn-app ajaxCustomModal',
                            'title' => __('Download Uang Jalan'),
                            'data-size' => 'modal-sm',
                        ));
                ?>
                <ul class="dropdown-menu" role="menu">
                    <?php 
                            echo $this->Html->tag('li', $this->Html->link(__('Uang Jalan'), array(
                                'controller' => 'settings',
                                'action' => 'uang_jalan_add'
                            ), array(
                                'escape' => false,
                            )));
                            echo $this->Html->tag('li', '', array(
                                'class' => 'divider',
                            ));
                            echo $this->Html->tag('li', $this->Html->link(__('Import Excel'), array(
                                'controller' => 'settings',
                                'action' => 'uang_jalan_import'
                            ), array(
                                'escape' => false,
                            )));
                    ?>
                </ul>
            </div>
            <div class="clear"></div>
        </div>
    </div><!-- /.box-header -->
    <div class="wrapper-download"></div>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <?php
                        echo $this->Html->tag('th', $this->Paginator->sort('UangJalan.id', __('ID/No. Ref'), array(
                            'escape' => false
                        )), array(
                            'width' => '10%',
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('th', $this->Paginator->sort('UangJalan.title', __('Nama'), array(
                            'escape' => false
                        )));
                        // echo $this->Html->tag('th', $this->Paginator->sort('FromCity.name', __('Kota Asal'), array(
                        //     'escape' => false
                        // )));
                        // echo $this->Html->tag('th', $this->Paginator->sort('ToCity.name', __('Kota Tujuan'), array(
                        //     'escape' => false
                        // )));
                        echo $this->Html->tag('th', __('Kota Asal'));
                        echo $this->Html->tag('th', __('Kota Tujuan'));
                        echo $this->Html->tag('th', $this->Paginator->sort('UangJalan.capacity', __('Kapasitas'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('UangJalan.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($uangJalans)){
                        foreach ($uangJalans as $key => $value) {
                            $id = $value['UangJalan']['id'];
                            $from_city_name = !empty($value['FromCity']['name'])?$value['FromCity']['name']:false;
                            $to_city_name = !empty($value['ToCity']['name'])?$value['ToCity']['name']:false;
            ?>
            <tr>
                <td class="text-center"><?php echo __('#%s', $id);?></td>
                <td><?php echo $value['UangJalan']['title'];?></td>
                <td><?php echo $from_city_name;?></td>
                <td><?php echo $to_city_name;?></td>
                <td><?php echo $value['UangJalan']['capacity'];?></td>
                <td><?php echo $this->Common->customDate($value['UangJalan']['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Info', array(
                                'controller' => 'settings',
                                'action' => 'uang_jalan_detail',
                                $id
                            ), array(
                                'class' => 'btn btn-info btn-xs'
                            ));
                            echo $this->Html->link('Edit', array(
                                'controller' => 'settings',
                                'action' => 'uang_jalan_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));
                            echo $this->Html->link(__('Hapus'), array(
                                'controller' => 'settings',
                                'action' => 'uang_jalan_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs trigger-disabled',
                                'title' => 'disable status brand',
                                'data-alert' => __('Anda yakin ingin menghapus data Uang Jalan ini?'),
                            ));
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