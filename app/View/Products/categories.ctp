<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/products/search_categories');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <?php 
                // if( in_array('insert_banks', $allowModule) ) {
        ?>
        <div class="box-tools">
            <?php
                echo $this->Common->rule_link('<i class="fa fa-plus"></i> Tambah', array(
                    'controller' => 'products',
                    'action' => 'category_add'
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
                        echo $this->Html->tag('th', $this->Paginator->sort('ProductCategory.name', $this->Common->getSorting('ProductCategory.name', __('Kategori Barang')), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('ProductCategory.created', $this->Common->getSorting('ProductCategory.created', __('Dibuat')), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    $i = 1;

                    if(!empty($productCategories)){
                        foreach ($productCategories as $key => $productCategory) {
                            $value_data = $productCategory['ProductCategory'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $value_data['name'];?></td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            // if( in_array('update_banks', $allowModule) ) {
                                echo $this->Common->rule_link('Ubah', array(
                                    'controller' => 'products',
                                    'action' => 'category_edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));
                            // }

                            // if( in_array('delete_banks', $allowModule) ) {
                                echo $this->Common->rule_link(__('Hapus'), array(
                                    'controller' => 'products',
                                    'action' => 'category_toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status brand'
                                ), __('Anda yakin ingin menghapus data Kategori barang ini?'));
                            // }
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