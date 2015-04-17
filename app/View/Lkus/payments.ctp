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

                if( in_array('insert_lku_payments', $allowModule) ) {
        ?>
        <div class="box-tools">
            <?php
                    echo $this->Html->link('<i class="fa fa-plus"></i> Tambah Pembayaran LKU/KSU', array(
                        'controller' => 'lkus',
                        'action' => 'payment_add'
                    ), array(
                        'escape' => false,
                        'class' => 'btn btn-app btn-success pull-right'
                    ));
            ?>
            <div class="clear"></div>
        </div>
        <?php 
                }
        ?>
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

                        echo $this->Html->tag('th', $this->Paginator->sort('Customer.name', __('Nama Customer'), array(
                            'escape' => false
                        )));

                        echo $this->Html->tag('th', $this->Paginator->sort('LkuPayment.paid', __('Status Pembayaran'), array(
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
                <td><?php echo $value['Customer']['name'];?></td>
                <?php 
                        if(!empty($value['LkuPayment']['paid'])){
                            echo $this->Html->tag('td', '<span class="label label-success">Telah di bayar</span>');
                        } else{
                            echo $this->Html->tag('td', '<span class="label label-danger">Belum di bayar</span>');
                        }
                ?>
                <td><?php echo $this->Common->customDate($value['LkuPayment']['created']);?></td>
                <td class="action">
                    <?php
                            if( in_array('update_lku_payments', $allowModule) ) {
                                echo $this->Html->link('Rubah', array(
                                    'controller' => 'lkus',
                                    'action' => 'payment_edit',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));
                            }
                    ?>
                </td>
            </tr>
            <?php
                        }
                    }else{
                        echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '6'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>