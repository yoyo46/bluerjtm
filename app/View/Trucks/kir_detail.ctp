<?php
        $this->Html->addCrumb('Pembayaran KIR', array(
            'controller' => 'trucks',
            'action' => 'kir_payments'
        ));
        $this->Html->addCrumb($sub_module_title);
?>
<div class="row">
    <div class="col-sm-6">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php echo __('Informasi KIR')?></h3>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt><?php echo __('No. Pol')?></dt>
                    <dd><?php echo $kir['Kir']['no_pol'];?></dd>
                    <dt><?php echo __('Tanggal Perpanjang')?></dt>
                    <dd><?php echo $this->Common->customDate($kir['Kir']['tgl_kir']);?></dd>
                    <dt><?php echo __('Tgl Berakhir KIR')?></dt>
                    <dd><?php echo $this->Common->customDate($kir['Kir']['from_date']);?></dd>
                    <dt><?php echo __('Berlaku Sampai')?></dt>
                    <dd><?php echo $this->Common->customDate($kir['Kir']['to_date']);?></dd>
                    <dt><?php echo __('Estimasi Biaya')?></dt>
                    <dd><?php echo $this->Number->currency($kir['Kir']['price_estimate'], Configure::read('__Site.config_currency_code').' ', array('places' => 0));?></dd>
                    <dt><?php echo __('Biaya Perpanjang')?></dt>
                    <dd><?php echo $this->Number->currency($kir['Kir']['price'], Configure::read('__Site.config_currency_code').' ', array('places' => 0));?></dd>
                    <dt><?php echo __('Denda')?></dt>
                    <dd>
                        <?php 
                            $denda = 0;
                            if(!empty($kir['Kir']['denda'])){
                                $denda = $kir['Kir']['denda'];
                            }
                            echo $this->Number->currency($denda, Configure::read('__Site.config_currency_code').' ', array('places' => 0));
                        ?>
                    </dd>
                    <dt><?php echo __('Total Pembayaran')?></dt>
                    <dd>
                        <?php 
                            $total = $kir['Kir']['price'] + $denda;
                            echo $this->Number->currency($total, Configure::read('__Site.config_currency_code').' ', array('places' => 0));
                        ?>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><?php echo __('Informasi Pembayaran')?></h3>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                    <dt><?php echo __('Tgl Dibayar')?></dt>
                    <dd><?php echo $this->Common->customDate($kir['KirPayment']['kir_payment_date']);?></dd>
                    <dt><?php echo __('Tgl Dokumen Dibuat')?></dt>
                    <dd><?php echo $this->Common->customDate($kir['KirPayment']['created']);?></dd>
                    <dt><?php echo __('Keterangan')?></dt>
                    <dd><?php echo !empty($kir['KirPayment']['note'])?str_replace(PHP_EOL, '<br>', $kir['KirPayment']['note']):'-';?></dd>
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
        </div>
    </div>
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