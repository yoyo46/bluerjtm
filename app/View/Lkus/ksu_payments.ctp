<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/lkus/search_index_payment', array(
            'model' => 'KsuPayment',
            'action' => 'ksu_payments'
        ));
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
                        'action' => 'ksu_payment_add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app btn-success pull-right'
                    ));
            ?>
            <div class="clear"></div>
        </div>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover red">
            <thead>
                <tr>
                    <?php 
                            echo $this->Html->tag('th', $this->Paginator->sort('KsuPayment.id', __('No. Referensi'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', $this->Paginator->sort('KsuPayment.no_doc', __('No. Dokumen'), array(
                                'escape' => false
                            )));

                            echo $this->Html->tag('th', $this->Paginator->sort('KsuPayment.tgl_bayar', __('Tgl Pembayaran'), array(
                                'escape' => false
                            )));

                            echo $this->Html->tag('th', $this->Paginator->sort('Customer.name', __('Customer'), array(
                                'escape' => false
                            )));

                            echo $this->Html->tag('th', $this->Paginator->sort('LkuPayment.grandtotal', __('Total Pembayaran'), array(
                                'escape' => false
                            )));

                            echo $this->Html->tag('th', $this->Paginator->sort('KsuPayment.paid', __('Status'), array(
                                'escape' => false
                            )));

                            echo $this->Html->tag('th', $this->Paginator->sort('KsuPayment.created', __('Dibuat'), array(
                                'escape' => false
                            )));

                            echo $this->Html->tag('th', __('Action'), array(
                                'escape' => false
                            ));
                    ?>
                </tr>
            </thead>
            <?php
                    if(!empty($payments)){
                        foreach ($payments as $key => $value) {
                            $id = $value['KsuPayment']['id'];
                            $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
            ?>
            <tr>
                <td><?php echo $noref;?></td>
                <td><?php echo $value['KsuPayment']['no_doc'];?></td>
                <?php echo $this->Html->tag('td', date('d M Y', strtotime($value['KsuPayment']['tgl_bayar'])));?>
                <td><?php echo $value['Customer']['customer_name_code'];?></td>
                <td><?php echo $this->Number->currency($value['KsuPayment']['grandtotal'], Configure::read('__Site.config_currency_code'), array('places' => 0));?></td>
                <?php 
                        if(!empty($value['KsuPayment']['is_void'])){
                            echo $this->Html->tag('td', '<span class="label label-danger">Void</span>');
                        }else{
                            if(!empty($value['KsuPayment']['paid'])){
                                echo $this->Html->tag('td', '<span class="label label-success">Telah di bayar</span>');
                            } else{
                                echo $this->Html->tag('td', '<span class="label label-warning">Belum di bayar</span>');
                            }
                        }
                ?>
                <td><?php echo $this->Common->customDate($value['KsuPayment']['created']);?></td>
                <td class="action">
                    <?php
                            echo $this->Html->link('Info', array(
                                'controller' => 'lkus',
                                'action' => 'detail_ksu_payment',
                                $id
                            ), array(
                                'class' => 'btn btn-info btn-xs'
                            ));

                            if(empty($value['KsuPayment']['is_void'])){
                                echo $this->Html->link('void', array(
                                    'controller' => 'lkus',
                                    'action' => 'ksu_payment_delete',
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
                            'colspan' => '8'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>