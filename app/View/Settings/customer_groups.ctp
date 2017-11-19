<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/settings/search_customer_groups');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Grup', array(
                        'controller' => 'settings',
                        'action' => 'customer_group_add'
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
                <?php
                        echo $this->Html->tag('th', $this->Paginator->sort('CustomerGroup.name', __('Grup Customer'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Grup.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($customerGroups)){
                        foreach ($customerGroups as $key => $value) {
                            $id = $value['CustomerGroup']['id'];
            ?>
            <tr>
                <td><?php echo $value['CustomerGroup']['name'];?></td>
                <td><?php echo $this->Common->customDate($value['CustomerGroup']['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'settings',
                                'action' => 'customer_group_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link(__('Hapus'), array(
                                'controller' => 'settings',
                                'action' => 'customer_group_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs trigger-disabled',
                                'title' => 'Hapus Grup Customer',
                                'data-alert' => __('Anda yakin ingin menghapus data Grup Customer ini?'),
                            ));
                    ?>
                </td>
            </tr>
            <?php
                        }
                    } else {
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '4'
                        )));
                    }
            ?>
        </table>
    </div>
    <?php echo $this->element('pagination');?>
</div>