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
                        'controller' => 'lkus',
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
                        echo $this->Html->tag('th', $this->Paginator->sort('LkuPayment.no_doc', __('No Dokumen'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('LkuPayment.tgl_bayar', __('Tgl Pembayaran'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('Customer.name', __('Customer'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('LkuPayment.grandtotal', __('Total Pembayaran'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('LkuPayment.paid', __('Status'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('LkuPayment.created', __('Dibuat'), array(
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
                            $id = $value['LkuPayment']['id'];
            ?>
            <tr>
                <td><?php echo $value['LkuPayment']['no_doc'];?></td>
                <?php echo $this->Html->tag('td', date('d M Y', strtotime($value['LkuPayment']['tgl_bayar'])));?>
                <td><?php echo !empty($value['Customer']['customer_name_code']) ? $value['Customer']['customer_name_code'] : ' - ';?></td>
                <td><?php echo $this->Number->currency($value['LkuPayment']['grandtotal'], Configure::read('__Site.config_currency_code'), array('places' => 0));?></td>
                <?php 
                        if(!empty($value['LkuPayment']['is_void'])){
                            echo $this->Html->tag('td', '<span class="label label-danger">Void</span>');
                        }else{
                            if(!empty($value['LkuPayment']['paid'])){
                                echo $this->Html->tag('td', '<span class="label label-success">Telah di bayar</span>');
                            } else{
                                echo $this->Html->tag('td', '<span class="label label-warning">Belum di bayar</span>');
                            }
                        }
                ?>
                <td><?php echo $this->Common->customDate($value['LkuPayment']['created']);?></td>
                <td class="action">
                    <?php
                            echo $this->Html->link('Info', array(
                                'controller' => 'lkus',
                                'action' => 'detail_payment',
                                $id
                            ), array(
                                'class' => 'btn btn-info btn-xs'
                            ));

                            if(empty($value['LkuPayment']['is_void'])){
                                echo $this->Html->link('Void', array(
                                    'controller' => 'lkus',
                                    'action' => 'payment_delete',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs'
                                ), __('Apakah Anda yakin ingin pembayaran ini?'));
                            }
                    ?>
                </td>
            </tr>
            <?php
                        }
                    }else{
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