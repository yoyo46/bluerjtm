<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/settings/search_parts_motor');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Part Motor', array(
                    'controller' => 'settings',
                    'action' => 'parts_motor_add'
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
                <th>Parts</th>
                <th>Code</th>
                <th>Biaya Klaim</th>
                <th>Dibuat</th>
                <th>Action</th>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($parts_motor)){
                        foreach ($parts_motor as $key => $value) {
                            $value_data = $value['PartsMotor'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $value_data['name'];?></td>
                <td><?php echo $value_data['code'];?></td>
                <td>
                    <?php 
                        if(!empty($value_data['biaya_claim_unit'])){
                            echo $this->Number->currency($value_data['biaya_claim_unit'], Configure::read('__Site.config_currency_code'), array('places' => 0));
                        }else{
                            echo '-';
                        }
                    ?>
                </td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'settings',
                                'action' => 'parts_motor_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link(__('Hapus'), array(
                                'controller' => 'settings',
                                'action' => 'parts_motor_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs trigger-disabled',
                                'title' => 'disable status brand',
                                'data-alert' => __('Anda yakin ingin menghapus data part motor ini?'),
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