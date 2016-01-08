<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/trucks/search_kir');
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
                echo $this->Html->link('<i class="fa fa-plus"></i> Perpanjang KIR', array(
                    'controller' => 'trucks',
                    'action' => 'kir_add'
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
                            echo $this->Html->tag('th', $this->Paginator->sort('Kir.no_pol', __('No. Pol'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', $this->Paginator->sort('Kir.tgl_kir', __('Tgl Perpanjang'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', $this->Paginator->sort('Kir.to_date', __('Berlaku Hingga'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', $this->Paginator->sort('Kir.price', __('Biaya Perpanjang'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', __('Status'));
                            echo $this->Html->tag('th', $this->Paginator->sort('Kir.created', __('Dibuat'), array(
                                'escape' => false
                            )));
                            echo $this->Html->tag('th', __('Action'));
                    ?>
                </tr>
            </thead>
            <?php
                    $i = 1;
                    if(!empty($kir)){
                        foreach ($kir as $key => $value) {
                            $id = $value['Kir']['id'];
                            $branch_id = $this->Common->filterEmptyField($value, 'Kir', 'branch_id');
                            $paid = $this->Common->filterEmptyField($value, 'Kir', 'paid');
            ?>
            <tr>
                <td><?php echo $value['Truck']['nopol'];?></td>
                <td><?php echo $this->Common->customDate($value['Kir']['tgl_kir']);?></td>
                <td><?php echo $this->Common->customDate($value['Kir']['to_date']);?></td>
                <td>
                    <?php echo $this->Number->currency($value['Kir']['price'], 'Rp. ', array('places' => 0));?>
                </td>
                <td>
                    <?php 
                            if( empty($value['Kir']['status']) ) {
                                echo '<span class="label label-danger">Non-Aktif</span>'; 
                            } else if( $paid == 'full' ) {
                                echo '<span class="label label-success">Sudah Bayar</span>'; 
                            } else if( $paid == 'half' ) {
                                echo '<span class="label label-success">Dibayar Sebagian</span>'; 
                            } else if( !empty($value['Kir']['rejected']) ) {
                                echo '<span class="label label-danger">Ditolak</span>'; 
                            } else {
                                echo '<span class="label label-default">Belum Bayar</span>';  
                            }
                    ?>
                </td>
                <td><?php echo $this->Time->niceShort($value['Kir']['created']);?></td>
                <td class="action">
                    <?php
                            if(!empty($value['Kir']['status']) && $paid == 'none') {
                                $label = __('Edit');
                            } else {
                                $label = __('Detail');
                            }

                            echo $this->Html->link($label, array(
                                'controller' => 'trucks',
                                'action' => 'kir_edit',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs',
                                'branch_id' => $branch_id,
                            ));

                            if( !empty($value['Kir']['status']) ) {
                                if( $paid == 'none' && empty($value['Kir']['rejected']) ){
                                    echo $this->Html->link(__('Void'), array(
                                        'controller' => 'trucks',
                                        'action' => 'kir_delete',
                                        $id
                                    ), array(
                                        'class' => 'btn btn-danger btn-xs',
                                        'branch_id' => $branch_id,
                                    ), __('Anda yakin ingin menon-aktifkan data Perpanjang KIR ini?'));
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