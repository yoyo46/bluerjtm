<?php 
        $dataColumns = array(
            'parent' => array(
                'name' => __('Parent Grup'),
                'field_model' => false,
                'display' => true,
            ),
            'name' => array(
                'name' => __('Nama Grup'),
                'field_model' => 'ProductCategory.name',
                'display' => true,
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => 'ProductCategory.status',
                'display' => true,
            ),
            'action' => array(
                'name' => __('Action'),
                'field_model' => false,
                'display' => true,
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/products/search_categories');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                    'controller' => 'products',
                    'action' => 'category_add'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app pull-right'
                ));
            ?>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-hover sorting">
            <?php
                    if( !empty($fieldColumn) ) {
                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                    }
            ?>
            <tbody>
            <?php
                    if(!empty($productCategories)){
                        foreach ($productCategories as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'ProductCategory', 'id');
                            $parent_name = $this->Common->filterEmptyField($value, 'Parent', 'name', '-');
                            $name = $this->Common->filterEmptyField($value, 'ProductCategory', 'name');
                            $created = $this->Common->filterEmptyField($value, 'ProductCategory', 'created');

                            $customCreated = $this->Common->formatDate($created, 'd/m/Y');
            ?>
            <tr>
                <td><?php echo $parent_name;?></td>
                <td><?php echo $name;?></td>
                <td><?php echo $customCreated;?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'products',
                                'action' => 'category_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link(__('Hapus'), array(
                                'controller' => 'products',
                                'action' => 'category_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs',
                            ), __('Anda yakin ingin menghapus data grup barang ini?'));
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
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>