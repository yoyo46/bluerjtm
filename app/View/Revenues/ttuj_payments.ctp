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
        <?php 
                // if( in_array('insert_invoice_payments', $allowModule) ) {
        ?>
        <div class="box-tools">
            <?php
                echo $this->Html->link(sprintf('<i class="fa fa-plus"></i> Tambah Pembayaran %s', $labelAdd), array(
                    'controller' => 'revenues',
                    'action' => 'ttuj_payment_add',
                    $action_type,
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
                        echo $this->Html->tag('th', $this->Paginator->sort('TtujPayment.nodoc', __('No. Dokumen'), array(
                            'escape' => false
                        )));
                        // echo $this->Html->tag('th', $this->Paginator->sort('TtujPayment.receiver_name', __('Dibayar Kepada'), array(
                        //     'escape' => false
                        // )));
                        // echo $this->Html->tag('th', $this->Paginator->sort('Ttuj.no_ttuj', __('No TTUJ'), array(
                        //     'escape' => false
                        // )));
                        echo $this->Html->tag('th', $this->Paginator->sort('TtujPayment.total_payment', __('Total Dibayar'), array(
                            'escape' => false,
                            'class' => 'text-center'
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('TtujPayment.date_payment', __('Tgl pembayaran'), array(
                            'escape' => false
                        )), array(
                            'class' => 'text-center'
                        ));
                        echo $this->Html->tag('th', $this->Paginator->sort('TtujPayment.status', __('Status'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($invoices)){
                        foreach ($invoices as $key => $value) {
                            $id = $value['TtujPayment']['id'];
            ?>
            <tr>
                <td><?php echo $value['TtujPayment']['nodoc'];?></td>
                <!-- <td><?php echo $value['TtujPayment']['receiver_name'];?></td>
                <td><?php echo !empty($value['Ttuj']['no_ttuj'])?$value['Ttuj']['no_ttuj']:false;?></td> -->
                <td align="right"><?php echo $this->Number->currency($value['TtujPayment']['total_payment'], Configure::read('__Site.config_currency_code'), array('places' => 0));?></td>
                <td class="text-center"><?php echo $this->Common->customDate($value['TtujPayment']['date_payment']);?></td>
                <td>
                    <?php 
                        if(empty($value['TtujPayment']['is_canceled'])){
                            if($value['TtujPayment']['status']){
                                echo $this->Html->tag('span', 'aktif', array(
                                    'class' => 'label label-success'
                                ));
                            }else{
                                echo $this->Html->tag('span', 'non-aktif', array(
                                    'class' => 'label label-danger'
                                ));
                            }
                        }else{
                            echo $this->Html->tag('span', __('Void'), array(
                                'class' => 'label label-danger'
                            ));
                            if(!empty($value['TtujPayment']['canceled_date'])){
                                echo '<br>'.$this->Common->customDate($value['TtujPayment']['canceled_date'], 'd/m/Y');
                            }
                        }
                    ?>
                </td>
                <td class="action">
                    <?php 
                            echo $this->Html->link('Detail', array(
                                'controller' => 'revenues',
                                'action' => 'detail_ttuj_payment',
                                $id,
                                $action_type,
                            ), array(
                                'class' => 'btn btn-info btn-xs'
                            ));
                            
                            if(empty($value['TtujPayment']['is_canceled'])){
                                echo $this->Html->link(__('Void'), array(
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