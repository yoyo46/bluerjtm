<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/settings/search_company_types');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Tipe Customer', array(
                    'controller' => 'settings',
                    'action' => 'company_type_add'
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
                        echo $this->Html->tag('th', $this->Paginator->sort('CompanyType.name', __('Tipe Customer'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('CompanyType.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('CompanyType.status', __('Status'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($companyTypes)){
                        foreach ($companyTypes as $key => $value) {
                            $value_data = $value['CompanyType'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $value_data['name'];?></td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td>
                    <?php 
                            if(!empty($value_data['status'])){
                                echo $this->Html->tag('span', __('Active'), array(
                                    'class' => 'label label-success'
                                ));
                            }else{
                                echo $this->Html->tag('span', __('Non Active'), array(
                                    'class' => 'label label-danger'
                                ));
                            }
                    ?>
                </td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'settings',
                                'action' => 'company_type_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            if(!empty($value_data['status'])){
                                echo $this->Html->link('Disable', array(
                                    'controller' => 'settings',
                                    'action' => 'company_type_toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status brand'
                                ));
                            }else{
                                echo $this->Html->link('Enable', array(
                                    'controller' => 'settings',
                                    'action' => 'company_type_toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-success btn-xs',
                                    'title' => 'enable status brand'
                                ));
                            }
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