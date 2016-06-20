<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/lakas/searchs/payments');
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                    'controller' => 'lakas',
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
                        echo $this->Html->tag('th', $this->Paginator->sort('LakaPayment.id', __('No. Referensi'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('LakaPayment.nodoc', __('No. Dokumen'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('LakaPayment.date_payment', __('Tgl pembayaran'), array(
                            'escape' => false
                        )), array(
                            'class' => 'text-center'
                        ));

                        echo $this->Html->tag('th', $this->Paginator->sort('LakaPayment.total_payment', __('Total Dibayar'), array(
                            'escape' => false,
                        )), array(
                            'class' => 'text-center'
                        ));
                        echo $this->Html->tag('th', $this->Paginator->sort('LakaPayment.status', __('Status'), array(
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
                            $id = $this->Common->filterEmptyField($value, 'LakaPayment', 'id');
                            $nodoc = $this->Common->filterEmptyField($value, 'LakaPayment', 'nodoc');
                            $total_payment = $this->Common->filterEmptyField($value, 'LakaPayment', 'total_payment');
                            $date_payment = $this->Common->filterEmptyField($value, 'LakaPayment', 'date_payment');
                            $status = $this->Common->filterEmptyField($value, 'LakaPayment', 'status');
                            $is_canceled = $this->Common->filterEmptyField($value, 'LakaPayment', 'is_canceled');
                            $canceled_date = $this->Common->filterEmptyField($value, 'LakaPayment', 'canceled_date');

                            $periode = $this->Common->filterEmptyField($value, 'LakaPayment', 'date_payment', false, false, array(
                                'date' => 'Y-m',
                            ));

                            $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                            $total_payment = $this->Common->getFormatPrice($total_payment);
                            $date_payment = $this->Common->formatDate($date_payment, 'd/m/Y');
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
                                $canceled_date = $this->Common->formatDate($canceled_date, 'd/m/Y');
                                $statusDoc .= '<br>'.$canceled_date;
                            }
                        }
                        echo $this->Html->tag('td', $statusDoc, array(
                            'class' => 'text-center'
                        ));

                        $actionDoc = $this->Html->link('Detail', array(
                            'controller' => 'lakas',
                            'action' => 'payment_detail',
                            $id,
                        ), array(
                            'class' => 'btn btn-info btn-xs'
                        ));
                        
                        if(empty($is_canceled)){
                            $actionDoc .= $this->Html->link(__('Edit'), array(
                                'controller' => 'lakas',
                                'action' => 'payment_edit',
                                $id,
                            ), array(
                                'class' => 'btn btn-primary btn-xs',
                                'closing' => true,
                                'periode' => $periode,
                            ));
                            
                            $actionDoc .= $this->Html->link(__('Void'), array(
                                'controller' => 'lakas',
                                'action' => 'payment_delete',
                                $id,
                            ), array(
                                'class' => 'btn btn-danger btn-xs ajaxModal',
                                'data-action' => 'cancel_invoice',
                                'title' => __('Void Pembayaran LAKA'),
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