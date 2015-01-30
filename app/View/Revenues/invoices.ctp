<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/revenues/search_invoices');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Invoice', array(
                    'controller' => 'revenues',
                    'action' => 'invoice_add'
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
                        echo $this->Html->tag('th', $this->Paginator->sort('Invoice.no_invoice', __('Kode Invoice'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Customer'));
                        echo $this->Html->tag('th', $this->Paginator->sort('Invoice.period_from', __('Periode Invoice'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($truck_customers)){
                        foreach ($truck_customers as $key => $value) {
                            $id = $value['Invoice']['id'];
            ?>
            <tr>
                <td><?php echo $value['Invoice']['no_invoice'];?></td>
                <td><?php echo $value['Customer']['name'];?></td>
                <td>
                    <?php 
                        echo $this->Common->customDate($value['Invoice']['period_from']).' sampai '.$this->Common->customDate($value['Invoice']['period_to']);
                    ?>
                </td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Edit', array(
                                'controller' => 'revenues',
                                'action' => 'invoice_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            // echo $this->Html->link(__('Hapus'), array(
                            //     'controller' => 'revenues',
                            //     'action' => 'invoice_toggle',
                            //     $id
                            // ), array(
                            //     'class' => 'btn btn-danger btn-xs',
                            //     'title' => 'Hapus Data Invoice'
                            // ), __('Anda yakin ingin menghapus data Invoice ini?'));
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