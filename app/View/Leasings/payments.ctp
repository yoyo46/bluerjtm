<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/lkus/search_index_payment');
?>
<div class="box box-success">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', $sub_module_title, array(
                    'class' => 'box-title'
                ));
        ?>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                        'controller' => 'leasings',
                        'action' => 'payment_add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app btn-success pull-right'
                    ));
            ?>
            <div class="clear"></div>
        </div>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover">
            <tr>
                <?php 
                        echo $this->Html->tag('th', $this->Paginator->sort('LeasingPayment.no_doc', __('No Dokumen'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('LeasingPayment.payment_date', __('Tgl Pembayaran'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', __('Vendor'));

                        echo $this->Html->tag('th', $this->Paginator->sort('LeasingPayment.denda', __('Denda'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('LeasingPayment.grandtotal', __('Total Pembayaran'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', __('Status'));

                        echo $this->Html->tag('th', $this->Paginator->sort('LeasingPayment.created', __('Dibuat'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', __('Action'), array(
                            'escape' => false
                        ));
                ?>
            </tr>
            <?php
                    if(!empty($payments)){
                        foreach ($payments as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'LeasingPayment', 'id');
                            $no_doc = $this->Common->filterEmptyField($value, 'LeasingPayment', 'no_doc');
                            $payment_date = $this->Common->filterEmptyField($value, 'LeasingPayment', 'payment_date');
                            $grandtotal = $this->Common->filterEmptyField($value, 'LeasingPayment', 'grandtotal');
                            $denda = $this->Common->filterEmptyField($value, 'LeasingPayment', 'denda');
                            $status = $this->Common->filterEmptyField($value, 'LeasingPayment', 'status');
                            $rejected = $this->Common->filterEmptyField($value, 'LeasingPayment', 'rejected');
                            $created = $this->Common->filterEmptyField($value, 'LeasingPayment', 'created');

                            $vendor = $this->Common->filterEmptyField($value, 'Vendor', 'name');

                            $customCreated = $this->Time->niceShort($created);
                            $customDate = $this->Common->customDate($payment_date, 'd/m/Y');
                            $customDenda = $this->Common->getFormatPrice($denda);
                            $customGrandtotal = $this->Common->getFormatPrice($grandtotal);
            ?>
            <tr>
                <?php 
                        echo $this->Html->tag('td', $no_doc);
                        echo $this->Html->tag('td', $customDate);
                        echo $this->Html->tag('td', $vendor);
                        echo $this->Html->tag('td', $customDenda);
                        echo $this->Html->tag('td', $customGrandtotal);

                        if( !empty($rejected) ) {
                            $lblStatus = $this->Html->tag('span', __('Void'), array(
                                'class' => 'label label-danger',
                            ));
                        } else {
                            $lblStatus = $this->Html->tag('span', __('Dibayar'), array(
                                'class' => 'label label-success',
                            ));
                        }

                        echo $this->Html->tag('td', $lblStatus);
                        echo $this->Html->tag('td', $customCreated);
                ?>
                <td class="action">
                    <?php
                            echo $this->Html->link('Info', array(
                                'controller' => 'leasings',
                                'action' => 'detail_payment',
                                $id
                            ), array(
                                'class' => 'btn btn-info btn-xs'
                            ));

                            if( empty($rejected) ){
                                echo $this->Html->link('Void', array(
                                    'controller' => 'leasings',
                                    'action' => 'payment_delete',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs'
                                ), __('Apakah Anda yakin ingin membatalkan pembayaran ini?'));
                            }
                    ?>
                </td>
            </tr>
            <?php
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '8'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>