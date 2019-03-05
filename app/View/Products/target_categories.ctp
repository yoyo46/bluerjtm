<?php 
        $dataColumns = array(
            'group' => array(
                'name' => __('Grup'),
                'field_model' => 'ProductCategory.name',
            ),
            'target' => array(
                'name' => __('Target'),
                'field_model' => 'ProductCategoryTarget.target',
            ),
            'created' => array(
                'name' => __('Tgl Dibuat'),
                'field_model' => 'ProductCategoryTarget.created',
                'class' => 'text-center',
            ),
            // 'status' => array(
            //     'name' => __('Status'),
            //     'field_model' => 'ProductCategoryTarget.status',
            //     'class' => 'text-center',
            // ),
            'action' => array(
                'name' => __('Action'),
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/products/product_category_targtet_search');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_label_multiple' => __('Tambah'),
                '_add' => array(
                    'controller' => 'products',
                    'action' => 'target_category_add',
                    'admin' => false,
                ),
            ));
    ?>
    <div class="box-body table-responsive">
        <table class="table table-hover sorting">
            <?php
                    if( !empty($fieldColumn) ) {
                        echo $this->Html->tag('thead', $this->Html->tag('tr', $fieldColumn));
                    }
            ?>
            <tbody>
            <?php
                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $id = Common::hashEmptyField($value, 'ProductCategoryTarget.id');
                            $target = Common::hashEmptyField($value, 'ProductCategoryTarget.target', 0, array(
                                'type' => 'currency',
                            ));
                            $created = Common::hashEmptyField($value, 'ProductCategoryTarget.created', '-', array(
                                'date' => 'd M Y',
                            ));
                            $status = Common::hashEmptyField($value, 'ProductCategoryTarget.status');
                            $group = $this->Common->filterEmptyField($value, 'ProductCategory', 'name');
                            $customStatus = $this->Common->getCheckStatus($status);

                            $customAction = $this->Html->link('Edit', array(
                                'controller' => 'products',
                                'action' => 'target_category_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));
                            $customAction .= $this->Html->link(__('Hapus'), array(
                                'controller' => 'products',
                                'action' => 'target_category_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs trigger-disabled',
                                'data-alert' => __('Anda yakin ingin menghapus target ini?'),
                            ));
            ?>
            <tr>
                <?php 
                        echo $this->Html->tag('td', $group);
                        echo $this->Html->tag('td', __('%s KM', $target));
                        // echo $this->Html->tag('td', $customStatus, array(
                        //     'class' => 'text-center',
                        // ));
                        echo $this->Html->tag('td', $created, array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('td', $customAction, array(
                            'class' => 'action',
                        ));
                ?>
            </tr>
            <?php
                        }
                    } else {
                         echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '11'
                        )));
                    }
            ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>