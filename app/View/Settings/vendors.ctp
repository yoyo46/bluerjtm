<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/settings/search_vendors');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_label_multiple' => __('Tambah'),
                '_add_multiple' => array(
                    array(
                        'label' => __('Supplier'),
                        'url' => array(
                            'controller' => 'settings',
                            'action' => 'vendor_add'
                        ),
                    ),
                    array(
                        'label' => __('Import'),
                        'url' => array(
                            'controller' => 'settings',
                            'action' => 'vendor_import',
                            'admin' => false,
                        ),
                    ),
                ),
            ));
    ?>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <?php
                        echo $this->Html->tag('th', $this->Paginator->sort('Vendor.name', __('Nama Supplier'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Vendor.address', __('Alamat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Vendor.phone_number', __('Telepon'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Vendor.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    if(!empty($vendors)){
                        foreach ($vendors as $key => $value) {
                            $value_data = $value['Vendor'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $value_data['name'];?></td>
                <td><?php echo $value_data['address'];?></td>
                <td>
                    <?php 
                            echo $value_data['phone_number'];
                    ?>
                </td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'settings',
                                'action' => 'vendor_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link(__('Hapus'), array(
                                'controller' => 'settings',
                                'action' => 'vendor_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs trigger-disabled',
                                'title' => 'disable status brand',
                                'data-alert' => __('Anda yakin ingin menghapus data supplier ini?'),
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
    </div>
    <?php echo $this->element('pagination');?>
</div>