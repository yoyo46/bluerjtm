<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/products/search_units');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                    'controller' => 'products',
                    'action' => 'unit_add'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app pull-right'
                ));
            ?>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-hover sorting">
            <thead>
                <tr>
                    <?php 
                            echo $this->Html->tag('th', $this->Paginator->sort('ProductUnit.name', $this->Common->getSorting('ProductUnit.name', __('Satuan Barang')), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', $this->Paginator->sort('ProductUnit.created', $this->Common->getSorting('ProductUnit.created', __('Dibuat')), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', __('Action'));
                    ?>
                </tr>
            </thead>
            <?php
                    $i = 1;

                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $value_data = $value['ProductUnit'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $value_data['name'];?></td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Ubah', array(
                                'controller' => 'products',
                                'action' => 'unit_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link(__('Hapus'), array(
                                'controller' => 'products',
                                'action' => 'unit_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs trigger-disabled',
                                'data-alert' => __('Anda yakin ingin menghapus data satuan barang ini?'),
                            ));
                    ?>
                </td>
            </tr>
            <?php
                        }
                    } else {
                         echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '7'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>