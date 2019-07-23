<?php 
        $this->Html->addCrumb($sub_module_title);

        echo $this->element('blocks/debt/search/payments');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                    'controller' => 'debt',
                    'action' => 'payment_add',
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
                        echo $this->Html->tag('th', $this->Paginator->sort('DebtPayment.id', __('No. Referensi'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('DebtPayment.nodoc', __('No. Dokumen'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('DebtPayment.date_payment', __('Tgl pembayaran'), array(
                            'escape' => false
                        )), array(
                            'class' => 'text-center'
                        ));

                        echo $this->Html->tag('th', $this->Paginator->sort('DebtPayment.total_payment', __('Total Dibayar'), array(
                            'escape' => false,
                        )), array(
                            'class' => 'text-center'
                        ));
                        echo $this->Html->tag('th', $this->Paginator->sort('DebtPayment.status', __('Status'), array(
                            'escape' => false
                        )), array(
                            'class' => 'text-center'
                        ));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    if(!empty($values)){
                        foreach ($values as $key => $value) {
                            $id = Common::hashEmptyField($value, 'DebtPayment.id');
                            $nodoc = Common::hashEmptyField($value, 'DebtPayment.nodoc');
                            $total_payment = Common::hashEmptyField($value, 'DebtPayment.total_payment');
                            $date_payment = Common::hashEmptyField($value, 'DebtPayment.date_payment');
                            $status = Common::hashEmptyField($value, 'DebtPayment.status');
                            $is_canceled = Common::hashEmptyField($value, 'DebtPayment.is_canceled');
                            $canceled_date = Common::hashEmptyField($value, 'DebtPayment.canceled_date');
                            $ttuj_payment_id = Common::hashEmptyField($value, 'DebtPayment.ttuj_payment_id');

                            $periode = Common::hashEmptyField($value, 'DebtPayment.date_payment', false, array(
                                'date' => 'Y-m',
                            ));

                            $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                            $total_payment = $this->Common->getFormatPrice($total_payment);
                            $date_payment = $this->Common->formatDate($date_payment, 'd M Y');
            ?>
            <tr>
                <?php 
                        echo $this->Html->tag('td', $noref);
                        echo $this->Html->tag('td', $nodoc);
                        echo $this->Html->tag('td', $date_payment, array(
                            'class' => 'text-center'
                        ));
                        echo $this->Html->tag('td', $total_payment, array(
                            'class' => 'text-right'
                        ));

                        if(empty($is_canceled)){
                            if(!empty($status)){
                                $statusDoc = $this->Html->tag('span', 'aktif', array(
                                    'class' => 'label label-success'
                                ));
                            }else{
                                $statusDoc = $this->Html->tag('span', 'non-aktif', array(
                                    'class' => 'label label-danger'
                                ));
                            }
                        }else{
                            $statusDoc = $this->Html->tag('span', __('Void'), array(
                                'class' => 'label label-danger'
                            ));
                            if(!empty($canceled_date)){
                                $canceled_date = $this->Common->formatDate($canceled_date, 'd M Y');
                                $statusDoc .= '<br>'.$canceled_date;
                            }
                        }
                        echo $this->Html->tag('td', $statusDoc, array(
                            'class' => 'text-center'
                        ));

                        $actionDoc = $this->Html->link('Detail', array(
                            'controller' => 'debt',
                            'action' => 'payment_detail',
                            $id,
                        ), array(
                            'class' => 'btn btn-info btn-xs'
                        ));
                        
                        if(empty($is_canceled) && empty($ttuj_payment_id)){
                            // $actionDoc = $this->Html->link(__('Edit'), array(
                            //     'controller' => 'debt',
                            //     'action' => 'payment_edit',
                            //     $id,
                            // ), array(
                            //     'class' => 'btn btn-primary btn-xs',
                            //     'closing' => true,
                            //     'periode' => $periode,
                            // ));
                            
                            $actionDoc .= $this->Html->link(__('Void'), array(
                                'controller' => 'debt',
                                'action' => 'payment_delete',
                                $id,
                            ), array(
                                'class' => 'btn btn-danger btn-xs ajaxModal',
                                'data-action' => 'cancel_invoice',
                                'title' => __('Void Pembayaran Hutang'),
                                'closing' => true,
                                'periode' => $periode,
                            ));
                        }

                        echo $this->Html->tag('td', $actionDoc, array(
                            'class' => 'action',
                        ));
                ?>
            </tr>
            <?php
                        }
                    } else {
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '8'
                        )));
                    }
            ?>
        </table>
    </div>
    <?php echo $this->element('pagination');?>
</div>