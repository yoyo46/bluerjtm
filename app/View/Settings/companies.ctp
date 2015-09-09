<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/settings/search_companies');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Company', array(
                    'controller' => 'settings',
                    'action' => 'company_add'
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
                        echo $this->Html->tag('th', $this->Paginator->sort('Company.name', __('Company'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Company.address', __('Alamat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Company.phone_number', __('Telepon'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Company.is_rjtm', __('via RJTM'), array(
                            'escape' => false
                        )), array(
                            'class' => 'text-center',
                        ));
                        echo $this->Html->tag('th', $this->Paginator->sort('Company.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($companies)){
                        foreach ($companies as $key => $value) {
                            $value_data = $value['Company'];
                            $id = $value_data['id'];
                            $is_rjtm = $this->Common->filterEmptyField($value, 'Company', 'is_rjtm');
            ?>
            <tr>
                <td><?php echo $value_data['name'];?></td>
                <td><?php echo $value_data['address'];?></td>
                <td>
                    <?php 
                        echo $value_data['phone_number'];
                    ?>
                </td>
                <td class="text-center">
                    <?php 
                            echo $this->Common->getCheckStatus($is_rjtm);
                    ?>
                </td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'settings',
                                'action' => 'company_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link(__('Hapus'), array(
                                'controller' => 'settings',
                                'action' => 'company_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs',
                                'title' => 'disable status brand'
                            ), __('Anda yakin ingin menghapus data Company ini?'));
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