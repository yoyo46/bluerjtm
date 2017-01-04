<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/settings/search_banks');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Bank', array(
                    'controller' => 'settings',
                    'action' => 'bank_add'
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
                        echo $this->Html->tag('th', $this->Paginator->sort('Coa.coa_name', $this->Common->getSorting('Coa.coa_name', __('No. Akun')), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Bank.name', $this->Common->getSorting('Bank.name', __('Bank')), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Bank.branch', $this->Common->getSorting('Bank.branch', __('Cabang')), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Bank.account_number', $this->Common->getSorting('Bank.account_number', __('No. Rek')), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Bank.account_name', $this->Common->getSorting('Bank.account_name', __('Atas Nama')), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('Bank.created', $this->Common->getSorting('Bank.created', __('Dibuat')), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    $i = 1;

                    if(!empty($banks)){
                        foreach ($banks as $key => $value) {
                            $value_data = $value['Bank'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $value['Coa']['coa_name'];?></td>
                <td><?php echo $value_data['name'];?></td>
                <td><?php echo $value_data['branch'];?></td>
                <td><?php echo $value_data['account_number'];?></td>
                <td><?php echo $value_data['account_name'];?></td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'settings',
                                'action' => 'bank_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link(__('Hapus'), array(
                                'controller' => 'settings',
                                'action' => 'bank_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs trigger-disabled',
                                'title' => 'disable status brand',
                                'data-alert' => __('Anda yakin ingin menghapus data bank ini?'),
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
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>