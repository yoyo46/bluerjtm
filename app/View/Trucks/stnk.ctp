<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/trucks/search_stnk');
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
                echo $this->Html->link('<i class="fa fa-plus"></i> Perpanjang STNK', array(
                    'controller' => 'trucks',
                    'action' => 'stnk_add'
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
                            echo $this->Html->tag('th', $this->Paginator->sort('Stnk.price', __('Biaya Perpanjang'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', __('Status'));
                            echo $this->Html->tag('th', $this->Paginator->sort('Stnk.created', __('Dibuat'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', __('Action'));
                    ?>
                </tr>
            </thead>
            <?php
                $i = 1;
                if(!empty($stnks)){
                    foreach ($stnks as $key => $value) {
                        $id = $value['Stnk']['id'];
                        $paid = $this->Common->filterEmptyField($value, 'Stnk', 'paid');
            ?>
            <tr>
                <td><?php echo $value['Stnk']['no_pol'];?></td>
                <td><?php echo $this->Common->customDate($value['Stnk']['tgl_bayar']);?></td>
                <td><?php echo $this->Common->customDate($value['Stnk']['to_date']);?></td>
                <td>
                    <?php
                            echo $this->Common->customDate($value['Stnk']['plat_to_date'], 'd M Y', '-');
                    ?>
                </td>
                <td><?php echo $this->Number->currency($value['Stnk']['price'], 'Rp. ');?></td>
                <td>
                    <?php 
                            if( empty($value['Stnk']['status']) ) {
                                echo '<span class="label label-danger">Non-Aktif</span>'; 
                            } else if( $paid == 'full' ) {
                                echo '<span class="label label-success">Sudah Bayar</span>'; 
                            } else if( $paid == 'half' ) {
                                echo '<span class="label label-success">Dibayar Sebagian</span>'; 
                            } else if( !empty($value['Stnk']['rejected']) ) {
                                echo '<span class="label label-danger">Ditolak</span>'; 
                            } else {
                                echo '<span class="label label-default">Belum Bayar</span>';  
                            }
                    ?>
                </td>
                <td><?php echo $this->Time->niceShort($value['Stnk']['created']);?></td>
                <td class="action">
                    <?php
                            if(!empty($value['Stnk']['status']) && $paid == 'none') {
                                $label = __('Ubah');
                            } else {
                                $label = __('Detail');
                            }

                            echo $this->Html->link($label, array(
                                'controller' => 'trucks',
                                'action' => 'stnk_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            if( !empty($value['Stnk']['status']) ) {
                                if( $paid == 'none' && empty($value['Stnk']['rejected']) ){
                                    echo $this->Html->link(__('Void'), array(
                                        'controller' => 'trucks',
                                        'action' => 'stnk_delete',
                                        $id
                                    ), array(
                                        'class' => 'btn btn-danger btn-xs'
                                    ), __('Anda yakin ingin menon-aktifkan data Perpanjang STNK ini?'));
                                }
                            }
                    ?>
                </td>
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
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>