<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/settings/search_branches');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <?php 
                if( in_array('insert_branches', $allowModule) ) {
        ?>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Cabang', array(
                        'controller' => 'settings',
                        'action' => 'branch_add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app pull-right'
                    ));
            ?>
        </div>
        <?php 
                }
        ?>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <?php 
                        echo $this->Html->tag('th', $this->Paginator->sort('Branch.name', __('Cabang'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Branch.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    if(!empty($branches)){
                        foreach ($branches as $key => $value) {
                            $id = $this->Common->safeTagPrint($value['Branch']['id']);
                            $name = $this->Common->safeTagPrint($value['Branch']['name']);
                            $created = $this->Common->safeTagPrint($value['Branch']['created']);
            ?>
            <tr>
                <td><?php echo $name;?></td>
                <td><?php echo $this->Common->customDate($created);?></td>
                <td class="action">
                    <?php 
                            if( in_array('update_branches', $allowModule) ) {
                                echo $this->Html->link(__('Edit'), array(
                                    'controller' => 'settings',
                                    'action' => 'branch_edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));
                            }

                            if( in_array('delete_branches', $allowModule) ) {
                                echo $this->Html->link(__('Hapus'), array(
                                    'controller' => 'settings',
                                    'action' => 'branch_toggle',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs',
                                    'title' => 'disable status brand'
                                ), __('Anda yakin ingin menghapus data cabang ini ?'));
                            }
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