<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/trucks/search_stnk_payments');
?>
<div class="box">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', $sub_module_title, array(
                    'class' => 'box-title'
                ));
        ?>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Tambah', array(
                    'controller' => 'trucks',
                    'action' => 'stnk_payment_add'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app pull-right'
                ));
            ?>
        </div>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover red sorting">
            <thead>
                <tr>
                    <?php
                            echo $this->Html->tag('th', $this->Paginator->sort('Stnk.id', __('No. Referensi'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', $this->Paginator->sort('Stnk.no_pol', __('No. Pol'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', $this->Paginator->sort('Stnk.tgl_bayar', __('Tgl Perpanjang'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', $this->Paginator->sort('Stnk.to_date', __('Berlaku Hingga'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', $this->Paginator->sort('Stnk.plat_to_date', __('Perpanjang Plat Hingga'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', $this->Paginator->sort('StnkPayment.total_pembayaran', __('Biaya Perpanjang'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', $this->Paginator->sort('Stnk.paid', __('Status'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', $this->Paginator->sort('StnkPayment.created', __('Dibuat'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', __('Action'));
                    ?>
                </tr>
            </thead>
            <?php
                    $i = 1;
                    if(!empty($stnkPayments)){
                        foreach ($stnkPayments as $key => $value) {
                            $id = $value['StnkPayment']['id'];
                            $noref = str_pad($id, 6, '0', STR_PAD_LEFT);
            ?>
            <tr>
                <td><?php echo $noref;?></td>
                <td><?php echo $value['Stnk']['no_pol'];?></td>
                <td><?php echo $this->Common->customDate($value['Stnk']['tgl_bayar'], 'd M Y');?></td>
                <td><?php echo $this->Common->customDate($value['Stnk']['to_date'], 'd M Y');?></td>
                <td>
                    <?php
                            echo $this->Common->customDate($value['Stnk']['plat_to_date'], 'd M Y', '-');
                    ?>
                </td>
                <td>
                    <?php echo $this->Number->currency($value['StnkPayment']['total_pembayaran'], 'Rp. ', array('places' => 0));?>
                </td>
                <td>
                    <?php 
                        if(empty($value['StnkPayment']['is_void'])){
                            if( !empty($value['Stnk']['paid']) ) {
                                echo '<span class="label label-success">Sudah Bayar</span>'; 
                            } else if( !empty($value['Stnk']['rejected']) ) {
                                echo '<span class="label label-danger">Ditolak</span>'; 
                            } else {
                                echo '<span class="label label-default">Belum Bayar</span>';  
                            }
                        }else{
                            echo '<span class="label label-danger">Non-Aktif</span>'; 
                        }
                    ?>
                </td>
                <td><?php echo $this->Time->niceShort($value['StnkPayment']['created']);?></td>
                <td class="action">
                    <?php
                                echo $this->Html->link(__('Detail'), array(
                                    'controller' => 'trucks',
                                    'action' => 'stnk_detail',
                                    $id
                                ), array(
                                    'class' => 'btn btn-primary btn-xs'
                                ));

                            if( empty($value['StnkPayment']['is_void']) && empty($value['StnkPayment']['rejected']) ){
                                echo $this->Html->link(__('Void'), array(
                                    'controller' => 'trucks',
                                    'action' => 'stnk_payment_delete',
                                    $id
                                ), array(
                                    'class' => 'btn btn-danger btn-xs'
                                ), __('Anda yakin ingin membatalkan data pembayaran STNK ini?'));
                            }
                    ?>
                </td>
            </tr>
            <?php
                        }
                    } else {
                         echo $this->Html->tag('tr', $this->Html->tag('td', __('Data belum tersedia.'), array(
                            'class' => 'alert alert-warning text-center',
                            'colspan' => '10'
                        )));
                    }
            ?>
        </table>
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>