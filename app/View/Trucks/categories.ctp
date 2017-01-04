<?php 
        $this->Html->addCrumb(__('Truk'), array(
            'action' => 'index',
        ));
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/trucks/search_categories');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Jenis Truk', array(
                    'controller' => 'trucks',
                    'action' => 'category_add'
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
                <th>Jenis Truk</th>
                <th>Dibuat</th>
                <th>Action</th>
            </tr>
            <?php
                $i = 1;
                if(!empty($truck_categories)){
                    foreach ($truck_categories as $key => $value) {
                        $value_data = $value['TruckCategory'];
                        $id = $value_data['id'];
            ?>
            <tr>
                <th><?php echo $value_data['name'];?></td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                        echo $this->Html->link('Edit', array(
                            'controller' => 'trucks',
                            'action' => 'category_edit',
                            $id
                        ), array(
                            'class' => 'btn btn-primary btn-xs'
                        ));

                        echo $this->Html->link('Hapus', array(
                            'controller' => 'trucks',
                            'action' => 'category_toggle',
                            $id
                        ), array(
                            'class' => 'btn btn-danger btn-xs trigger-disabled',
                            'title' => 'disable status brand',
                            'data-alert' => __('Anda yakin ingin menghapus perlengkapan ini?'),
                        ));
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