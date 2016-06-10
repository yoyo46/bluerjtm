<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/trucks/search_siup');
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
                echo $this->Html->link('<i class="fa fa-plus"></i> Perpanjang Ijin Usaha', array(
                    'controller' => 'trucks',
                    'action' => 'siup_add'
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
                            echo $this->Html->tag('th', $this->Paginator->sort('Siup.id', __('No. Ref'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', $this->Paginator->sort('Siup.no_pol', __('No. Pol'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', $this->Paginator->sort('Siup.tgl_siup', __('Tgl Perpanjang'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', $this->Paginator->sort('Siup.to_date', __('Berlaku Hingga'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', $this->Paginator->sort('Siup.price', __('Biaya Perpanjang'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', $this->Paginator->sort('Siup.paid', __('Status'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', $this->Paginator->sort('Siup.created', __('Dibuat'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', __('Action'));
                    ?>
                </tr>
            </thead>
            <?php
                    $i = 1;
                    if(!empty($siup)){
                        foreach ($siup as $key => $value) {
                            $id = $this->Common->filterEmptyField($value, 'Siup', 'id');
                            $paid = $this->Common->filterEmptyField($value, 'Siup', 'paid', 'none');
                            $noref = $this->Common->_callGenerateNoRef($id);
            ?>
            <tr>
                <td><?php echo $noref;?></td>
                <td><?php echo $value['Truck']['nopol'];?></td>
                <td><?php echo $this->Common->customDate($value['Siup']['tgl_siup']);?></td>
                <td><?php echo $this->Common->customDate($value['Siup']['to_date']);?></td>
                <td>
                    <?php echo $this->Number->currency($value['Siup']['price'], 'Rp. ', array('places' => 0));?>
                </td>
                <td>
                    <?php 
                            if( empty($value['Siup']['status']) ) {
                                echo '<span class="label label-danger">Non-Aktif</span>'; 
                            } else if( $paid == 'full' ) {
                                echo '<span class="label label-success">Sudah Bayar</span>'; 
                            } else if( $paid == 'half' ) {
                                echo '<span class="label label-success">Dibayar Sebagian</span>'; 
                            } else if( !empty($value['Siup']['rejected']) ) {
                                echo '<span class="label label-danger">Ditolak</span>'; 
                            } else {
                                echo '<span class="label label-default">Belum Bayar</span>';  
                            }
                    ?>
                </td>
                <td><?php echo $this->Time->niceShort($value['Siup']['created']);?></td>
                <td class="action">
                    <?php
                            if(!empty($value['Siup']['status']) && $paid == 'none') {
                                $label = __('Ubah');
                            } else {
                                $label = __('Detail');
                            }

                            echo $this->Html->link($label, array(
                                'controller' => 'trucks',
                                'action' => 'siup_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));

                            if( !empty($value['Siup']['status']) ) {
                                if( $paid == 'none' && empty($value['Siup']['rejected']) ){
                                    echo $this->Html->link(__('Void'), array(
                                        'controller' => 'trucks',
                                        'action' => 'siup_delete',
                                        $id
                                    ), array(
                                        'class' => 'btn btn-danger btn-xs'
                                    ), __('Anda yakin ingin void data Perpanjang Ijin Usaha ini?'));
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