<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/cashbanks/searchs/budgets');
?>
<div class="box">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => $sub_module_title,
                '_label_multiple' => __('Tambah'),
                '_add_multiple' => array(
                    array(
                        'label' => __('Tambah'),
                        'url' => array(
                            'controller' => 'cashbanks',
                            'action' => 'budget_add',
                            'admin' => false,
                        ),
                    ),
                    array(
                        'label' => __('Import'),
                        'url' => array(
                            'controller' => 'cashbanks',
                            'action' => 'budget_import',
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
                        echo $this->Html->tag('th', __('COA'));
                        echo $this->Html->tag('th', __('Tahun'));
                        echo $this->Html->tag('th', __('Dibuat'));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $value_data = $value['Budget'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $value['Coa']['coa_name'];?></td>
                <td><?php echo date('Y', mktime(0, 0, 0, 1, 1, $value_data['year']));?></td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'cashbanks',
                                'action' => 'budget_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link(__('Hapus'), array(
                                'controller' => 'cashbanks',
                                'action' => 'budget_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs trigger-disabled',
                                'title' => 'disable status brand',
                                'data-alert' => __('Anda yakin ingin menghapus data Budget ini?'),
                            ));
                    ?>
                </td>
            </tr>
            <?php
                        }
                    } else {
                         echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '6'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>