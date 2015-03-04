<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/revenues/search_invoice_payments');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <?php 
                // if( in_array('insert_invoice_payments', $allowModule) ) {
        ?>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Pembayaran Invoice', array(
                    'controller' => 'revenues',
                    'action' => 'invoice_payment_add'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app btn-success pull-right'
                ));
            ?>
        </div>
        <?php 
                // }
        ?>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <?php
                        echo $this->Html->tag('th', __('Customer'));
                        echo $this->Html->tag('th', $this->Paginator->sort('InvoicePayment.nodoc', __('No. Dokumen'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('InvoicePayment.total_payment', __('Total Dibayar'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('InvoicePayment.date_payment', __('Tgl pembayaran'), array(
                            'escape' => false
                        )), array(
                            'class' => 'text-center'
                        ));
                        echo $this->Html->tag('th', $this->Paginator->sort('InvoicePayment.status', __('Status'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($invoices)){
                        foreach ($invoices as $key => $value) {
                            $id = $value['InvoicePayment']['id'];
            ?>
            <tr>
                <td><?php echo !empty($value['Customer']['customer_name'])?$value['Customer']['customer_name']:false;?></td>
                <td><?php echo $value['InvoicePayment']['nodoc'];?></td>
                <td align="right"><?php echo $this->Number->currency($value['InvoicePayment']['total_payment'], Configure::read('__Site.config_currency_code'), array('places' => 0));?></td>
                <td class="text-center"><?php echo $this->Common->customDate($value['InvoicePayment']['date_payment']);?></td>
                <td>
                    <?php 
                        if($value['InvoicePayment']['status']){
                            echo $this->Html->tag('span', 'aktif', array(
                                'class' => 'label label-success'
                            ));
                        }else{
                            echo $this->Html->tag('span', 'non-aktif', array(
                                'class' => 'label label-danger'
                            ));
                        }
                    ?>
                </td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Info', array(
                                'controller' => 'revenues',
                                'action' => 'detail_invoice_payment',
                                $id
                            ), array(
                                'class' => 'btn btn-info btn-xs'
                            ));
                            // echo $this->Html->link('Edit', array(
                            //     'controller' => 'revenues',
                            //     'action' => 'detail_invoice_payment',
                            //     $id
                            // ), array(
                            //     'class' => 'btn btn-primary btn-xs'
                            // ));
                            
                            echo $this->Html->link('Hapus', array(
                                'controller' => 'revenues',
                                'action' => 'invoice_payment_delete',
                                $id
                            ), array(
                                'class' => 'btn btn-danger btn-xs'
                            ), __('Apakah Anda yakin ingin menghapus pembayaran invoice ini?'));
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
    </div>
    <?php echo $this->element('pagination');?>
</div>