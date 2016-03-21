<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/revenues/search_ttuj_payments');

        switch ($action_type) {
            case 'biaya_ttuj':
                $labelAdd = __('Biaya TTUJ');
                break;
            
            default:
                $labelAdd = __('Uang Jalan/Komisi');
                break;
        }
?>
<div class="box">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title;?></h3>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                    'controller' => 'revenues',
                    'action' => 'ttuj_payment_add',
                    $action_type,
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
                        echo $this->Html->tag('th', $this->Paginator->sort('TtujPayment.id', __('No. Referensi'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('TtujPayment.nodoc', __('No. Dokumen'), array(
                            'escape' => false
                        )));

                        if( $action_type == 'biaya_ttuj' ) {
                            echo $this->Html->tag('th', $this->Paginator->sort('TtujPayment.receiver_name', __('Dibayar Kepada'), array(
                                'escape' => false
                            )));
                        }

                        echo $this->Html->tag('th', $this->Paginator->sort('TtujPayment.total_payment', __('Total Dibayar'), array(
                            'escape' => false,
                        )), array(
                            'class' => 'text-center'
                        ));
                        echo $this->Html->tag('th', $this->Paginator->sort('TtujPayment.date_payment', __('Tgl pembayaran'), array(
                            'escape' => false
                        )), array(
                            'class' => 'text-center'
                        ));
                        echo $this->Html->tag('th', $this->Paginator->sort('TtujPayment.status', __('Status'), array(
                            'escape' => false
                        )), array(
                            'class' => 'text-center'
                        ));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($invoices)){
                        foreach ($invoices as $key => $value) {
                            $id = $value['TtujPayment']['id'];
                            $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
                            $transaction_status = $this->Common->filterEmptyField($value, 'TtujPayment', 'transaction_status');
                            $customStatus = $this->Revenue->_callStatusCustom($value, 'TtujPayment');
            ?>
            <tr>
                <?php 
                        echo $this->Html->tag('td', $noref);
                        echo $this->Html->tag('td', $value['TtujPayment']['nodoc']);

                        if( $action_type == 'biaya_ttuj' ) {
                            echo $this->Html->tag('td', $value['TtujPayment']['receiver_name']);
                        }

                        echo $this->Html->tag('td', $this->Number->currency($value['TtujPayment']['total_payment'], Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
                            'class' => 'text-right'
                        ));

                        echo $this->Html->tag('td', $this->Common->customDate($value['TtujPayment']['date_payment'], 'd M Y'), array(
                            'class' => 'text-center'
                        ));

                        echo $this->Html->tag('td', $customStatus, array(
                            'class' => 'text-center'
                        ));

                        $actionDoc = $this->Html->link('Detail', array(
                            'controller' => 'revenues',
                            'action' => 'detail_ttuj_payment',
                            $id,
                            $action_type,
                        ), array(
                            'class' => 'btn btn-info btn-xs'
                        ));
                        
                        if(empty($value['TtujPayment']['is_canceled'])){
                            if( $transaction_status != 'posting' ) {
                                $actionDoc .= $this->Html->link(__('Edit'), array(
                                    'controller' => 'revenues',
                                    'action' => 'edit_ttuj_payment',
                                    $action_type,
                                    $id,
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));
                            }
                            
                            $actionDoc .= $this->Html->link(__('Void'), array(
                                'controller' => 'revenues',
                                'action' => 'ttuj_payment_delete',
                                $id,
                                $action_type,
                            ), array(
                                'class' => 'btn btn-danger btn-xs ajaxModal',
                                'data-action' => 'cancel_invoice',
                                'title' => __('Void Pembayaran Uang Jalan')
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