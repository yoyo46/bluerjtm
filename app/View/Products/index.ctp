<?php 
        $dataColumns = array(
            'code' => array(
                'name' => __('Kode'),
                'field_model' => 'Product.code',
                'display' => true,
            ),
            'name' => array(
                'name' => __('Nama'),
                'field_model' => 'Product.name',
                'display' => true,
            ),
            'unit' => array(
                'name' => __('Satuan'),
                'field_model' => false,
                'display' => true,
            ),
            'group' => array(
                'name' => __('Grup'),
                'field_model' => false,
                'display' => true,
            ),
            'type' => array(
                'name' => __('Tipe'),
                'field_model' => 'Product.type',
                'display' => true,
            ),
            'status' => array(
                'name' => __('Status'),
                'field_model' => 'Product.status',
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
        echo $this->element('blocks/products/search');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                    'controller' => 'products',
                    'action' => 'add'
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
                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'Product', 'id');
                            $code = $this->Common->filterEmptyField($value, 'Product', 'code');
                            $name = $this->Common->filterEmptyField($value, 'Product', 'name');
                            $type = $this->Common->filterEmptyField($value, 'Product', 'type');
                            $created = $this->Common->filterEmptyField($value, 'Product', 'created');
                            $status = $this->Common->filterEmptyField($value, 'Product', 'status');

                            $unit = $this->Common->filterEmptyField($value, 'ProductUnit', 'name');
                            $group = $this->Common->filterEmptyField($value, 'ProductCategory', 'name');

                            $customCreated = $this->Common->formatDate($created, 'd/m/Y');
                            $customType = $this->Common->unSlug($type);
                            $customStatus = $this->Common->getCheckStatus($status);
                            $customAction = $this->Html->link('Edit', array(
                                'controller' => 'products',
                                'action' => 'edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));
                            $customAction .= $this->Html->link(__('Hapus'), array(
                                'controller' => 'products',
                                'action' => 'toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs',
                            ), __('Anda yakin ingin menghapus barang ini?'));
            ?>
            <tr>
                <?php 
                        echo $this->Html->tag('td', $code);
                        echo $this->Html->tag('td', $name);
                        echo $this->Html->tag('td', $unit);
                        echo $this->Html->tag('td', $group);
                        echo $this->Html->tag('td', $customType);
                        echo $this->Html->tag('td', $customStatus);
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
                            'colspan' => '7'
                        )));
                    }
            ?>
            </tbody>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>