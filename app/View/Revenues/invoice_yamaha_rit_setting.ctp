<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/revenues/searchs/invoice_yamaha_rit_setting');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                    'controller' => 'revenues',
                    'action' => 'invoice_yamaha_rit_setting_add'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app pull-right'
                ));
            ?>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-hover sorting">
            <thead>
                <tr>
                    <?php 
                            echo $this->Html->tag('th', $this->Paginator->sort('SettingInvoiceYamahaRit.name', $this->Common->getSorting('SettingInvoiceYamahaRit.name', __('Judul')), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', $this->Paginator->sort('SettingInvoiceYamahaRit.percent', $this->Common->getSorting('SettingInvoiceYamahaRit.percent', __('Tarif dlm Persen')), array(
                                'escape' => false
                            )), array(
                                'class' => 'text-center',
                            ));
                            echo $this->Html->tag('th', $this->Paginator->sort('SettingInvoiceYamahaRit.created', $this->Common->getSorting('SettingInvoiceYamahaRit.created', __('Dibuat')), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', __('Action'));
                    ?>
                </tr>
            </thead>
            <?php
                    $i = 1;

                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $value_data = $value['SettingInvoiceYamahaRit'];
                            $id = $value_data['id'];
            ?>
            <tr>
                <td><?php echo $value_data['name'];?></td>
                <td class="text-center"><?php echo $value_data['percent'].'%';?></td>
                <td><?php echo $this->Common->customDate($value_data['created']);?></td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Ubah', array(
                                'controller' => 'revenues',
                                'action' => 'invoice_yamaha_rit_setting_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            echo $this->Html->link(__('Hapus'), array(
                                'controller' => 'revenues',
                                'action' => 'invoice_yamaha_rit_setting_toggle',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs trigger-disabled',
                                'data-alert' => __('Anda yakin ingin menghapus data satuan barang ini?'),
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