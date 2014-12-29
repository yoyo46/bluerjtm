<?php
        $this->Html->addCrumb('Pembayaran KIR', array(
            'controller' => 'trucks',
            'action' => 'kir_payments'
        ));
        $this->Html->addCrumb($sub_module_title);
?>
<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title"><?php echo $sub_module_title?></h3>
    </div>
    <div class="box-body">
        <dl class="dl-horizontal">
            <dt><?php echo __('No. Pol')?></dt>
            <dd><?php echo $kir['Kir']['no_pol'];?></dd>
            <dt><?php echo __('Tanggal Perpanjang')?></dt>
            <dd><?php echo $this->Common->customDate($kir['Kir']['tgl_kir']);?></dd>
            <dt><?php echo __('Tgl KIR ditetapkan')?></dt>
            <dd><?php echo $this->Common->customDate($kir['Kir']['from_date']);?></dd>
            <dt><?php echo __('Berlaku Hingga')?></dt>
            <dd><?php echo $this->Common->customDate($kir['Kir']['to_date']);?></dd>
            <dt><?php echo __('Biaya Perpanjang KIR')?></dt>
            <dd><?php echo $this->Number->currency($kir['Kir']['price'], Configure::read('__Site.config_currency_code').' ', array('places' => 0));?></dd>
            <dt><?php echo __('Keterangan')?></dt>
            <dd><?php echo str_replace(PHP_EOL, '<br>', $kir['KirPayment']['note']);?></dd>
            <dt><?php echo __('Dibuat Tgl')?></dt>
            <dd><?php echo $this->Common->customDate($kir['KirPayment']['created']);?></dd>
            <dt><?php echo __('Status')?></dt>
            <dd>
                <?php 
                        if( !empty($kir['KirPayment']['rejected']) ) {
                            echo '<span class="label label-danger">Ditolak</span>'; 
                        } else {
                            echo '<span class="label label-success">Sudah Bayar</span>'; 
                        }
                ?>
            </dd>
        </dl>
    </div>

    <div class="box-footer text-center action">
        <?php
                echo $this->Html->link(__('Kembali'), array(
                    'controller' => 'trucks',
                    'action' => 'kir_payments'
                ), array(
                    'class'=> 'btn btn-default',
                ));
        ?>
    </div>
</div>