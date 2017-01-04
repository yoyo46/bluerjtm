<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/settings/search_target_units');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Target Unit', array(
                    'controller' => 'settings',
                    'action' => 'customer_target_unit_add'
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
                        echo $this->Html->tag('th', __('Customer'));
                        echo $this->Html->tag('th', __('Tahun'));
                        echo $this->Html->tag('th', __('Dibuat'));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($customerTargetUnits)){
                        foreach ($customerTargetUnits as $key => $customerTargetUnit) {
                            $value_data = $customerTargetUnit['CustomerTargetUnit'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $customerTargetUnit['Customer']['customer_name_code'];?></td>
                <td><?php echo date('Y', mktime(0, 0, 0, 1, 1, $value_data['year']));?></td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'settings',
                                'action' => 'customer_target_unit_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link(__('Hapus'), array(
                                'controller' => 'settings',
                                'action' => 'customer_target_unit_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs trigger-disabled',
                                'title' => 'disable status brand',
                                'data-alert' => __('Anda yakin ingin menghapus data Target Unit ini?'),
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