<?php 
        $this->Html->addCrumb($sub_module_title);
        echo $this->element('blocks/trucks/search_kir_payments');
?>
<div class="box">
    <div class="box-header">
        <?php 
                echo $this->Html->tag('h3', $sub_module_title, array(
                    'class' => 'box-title'
                ));
                
                if( in_array('insert_kir_payments', $allowModule) ) {
        ?>
        <div class="box-tools">
            <?php
                echo $this->Html->link('<i class="fa fa-plus"></i> Bayar KIR', array(
                    'controller' => 'trucks',
                    'action' => 'kir_payment_add'
                ), array(
                    'escape' => false,
                    'class' => 'btn btn-app pull-right'
                ));
            ?>
        </div>
        <?php 
                }
        ?>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-hover">
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
                        echo $this->Html->tag('th', $this->Paginator->sort('Kir.paid', __('Status'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', $this->Paginator->sort('KirPayment.created', __('Dibuat'), array(
                            'escape' => false
                        )));
                        echo $this->Html->tag('th', __('Action'));
                ?>
            </tr>
            <?php
                    $i = 1;
                    if(!empty($kirPayments)){
                        foreach ($kirPayments as $key => $value) {
                            $id = $value['KirPayment']['id'];
            ?>
            <tr>
                <td><?php echo $value['Kir']['no_pol'];?></td>
                <td><?php echo $this->Common->customDate($value['Kir']['tgl_kir']);?></td>
                <td><?php echo $this->Common->customDate($value['Kir']['to_date']);?></td>
                <td>
                    <?php echo $this->Number->currency($value['Kir']['price'], 'Rp. ', array('places' => 0));?>
                </td>
                <td>
                    <?php 
                            if( !empty($value['Kir']['paid']) ) {
                                echo '<span class="label label-success">Sudah Bayar</span>'; 
                            } else if( !empty($value['Kir']['rejected']) ) {
                                echo '<span class="label label-danger">Ditolak</span>'; 
                            } else {
                                echo '<span class="label label-default">Belum Bayar</span>';  
                            }
                    ?>
                </td>
                <td><?php echo $this->Time->niceShort($value['KirPayment']['created']);?></td>
                <td class="action">
                    <?php
                            echo $this->Html->link(__('Detail'), array(
                                'controller' => 'trucks',
                                'action' => 'kir_detail',
                                $id
                            ), array(
                                'class' => 'btn btn-primary btn-xs'
                            ));
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
    </div><!-- /.box-body -->
    <?php echo $this->element('pagination');?>
</div>