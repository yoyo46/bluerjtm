<?php 
        $this->Html->addCrumb(__('Group'), array(
            'controller' => 'users',
            'action' => 'groups'
        ));
        
        $this->Html->addCrumb($sub_module_title);
        
        echo $this->element('blocks/users/search_action_module');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Module', array(
                        'controller' => 'users',
                        'action' => 'action_module_add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app pull-right'
                    ));
            ?>
        </div>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <?php 
                        echo $this->Html->tag('th', $this->Paginator->sort('BranchModule.id', __('ID'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('BranchModule.name', __('Nama Module'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('BranchModule.type', __('Tipe Module'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('BranchModule.controller', __('Controller'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('BranchModule.action', __('Action'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('BranchModule.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    if(!empty($action_modules)){
                        foreach ($action_modules as $key => $value) {
                            $id = $this->Common->safeTagPrint($value['BranchModule']['id']);
                            $name = $this->Common->safeTagPrint($value['BranchModule']['name']);
                            $type = !empty($value['BranchModule']['type']) ? ucwords($this->Common->safeTagPrint($value['BranchModule']['type'])) : ' - ';
                            $controller = !empty($value['BranchModule']['controller']) ? $this->Common->safeTagPrint($value['BranchModule']['controller']) : '-';
                            $action = !empty($value['BranchModule']['action']) ? $this->Common->safeTagPrint($value['BranchModule']['action']) : '-';
                            $created = $this->Common->safeTagPrint($value['BranchModule']['created']);
            ?>
            <tr>
                <td><?php echo $id;?></td>
                <td><?php echo $name;?></td>
                <td><?php echo $type;?></td>
                <td><?php echo $controller;?></td>
                <td><?php echo $action;?></td>
                <td><?php echo $this->Common->customDate($created);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link(__('Edit'), array(
                                'controller' => 'users',
                                'action' => 'action_module_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link(__('Hapus'), array(
                                'controller' => 'users',
                                'action' => 'action_module_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs',
                                'title' => 'disable status brand'
                            ), __('Anda yakin ingin menghapus data module ini ?'));
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