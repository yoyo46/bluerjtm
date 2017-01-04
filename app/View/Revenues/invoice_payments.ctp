<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/revenues/search_invoice_payments');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                    'controller' => 'revenues',
                    'action' => 'invoice_payment_add'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app btn-success pull-right'
                ));
            ?>
        </div>
    </div><!-- /.box-header -->
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <?php
                        echo $this->Html->tag('th', $this->Paginator->sort('InvoicePayment.id', __('No. Referensi'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('InvoicePayment.nodoc', __('No. Dokumen'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('InvoicePayment.date_payment', __('Tgl pembayaran'), array(
                            'escape' => false
                        )), array(
                            'class' => 'text-center'
                        ));
                        echo $this->Html->tag('th', __('Customer'));
                        // echo $this->Html->tag('th', $this->Paginator->sort('InvoicePayment.coa_id', __('COA'), array(
                        //     'escape' => false
                        // )));
                        echo $this->Html->tag('th', $this->Paginator->sort('InvoicePayment.total_payment', __('Total Dibayar'), array(
                            'escape' => false,
                        )), array(
                            'class' => 'text-right',
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
                            $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                            $transaction_status = $this->Common->filterEmptyField($value, 'InvoicePayment', 'transaction_status');
                            $customStatus = $this->Revenue->_callStatusCustom($value, 'InvoicePayment');
                            $periode = $this->Common->filterEmptyField($value, 'InvoicePayment', 'date_payment', false, false, array(
                                'date' => 'Y-m',
                            ));
            ?>
            <tr>
                <td><?php echo $noref;?></td>
                <td><?php echo $value['InvoicePayment']['nodoc'];?></td>
                <td class="text-center"><?php echo $this->Common->customDate($value['InvoicePayment']['date_payment'], 'd M Y');?></td>
                <td><?php echo !empty($value['Customer']['customer_name'])?$value['Customer']['customer_name']:false;?></td>
                <!-- <td><?php // echo !empty($value['Coa']['name'])?$value['Coa']['name']:false;?></td> -->
                <td align="right"><?php echo $this->Common->getFormatPrice($value['InvoicePayment']['grand_total_payment']);?></td>
                <td>
                    <?php 
                            echo $customStatus;
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

                            if(empty($value['InvoicePayment']['is_canceled'])){
                                // echo $this->Html->link('Edit', array(
                                //     'controller' => 'revenues',
                                //     'action' => 'detail_invoice_payment',
                                //     $id
                                // ), array(
                                //     'class' => 'btn btn-primary btn-xs'
                                // ));
                                if( $transaction_status != 'posting' ) {
                                    echo $this->Html->link(__('Edit'), array(
                                        'controller' => 'revenues',
                                        'action' => 'invoice_payment_edit',
                                        $id
                                    ), array(
                                        'class' => 'btn btn-primary btn-xs',
                                        'closing' => true,
                                        'periode' => $periode,
                                    ));
                                }
                                
                                echo $this->Html->link('Void', array(
                                    'controller' => 'revenues',
                                    'action' => 'invoice_payment_delete',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs trigger-disabled',
                                    'closing' => true,
                                    'periode' => $periode,
                                    'data-alert' => __('Apakah Anda yakin ingin mengbatalkan pembayaran invoice ini?'),
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
                            'colspan' => '7'
                        )));
                    }
            ?>
        </table>
    </div>
    <?php echo $this->element('pagination');?>
</div>